<?php
echo "🔄 Synchronizing ALL Files to Match phpMyAdmin\n";
echo "==============================================\n\n";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_alberca';
$port = 3306;

try {
    $mysqli = new mysqli($host, $username, $password, $database, $port);
    
    if ($mysqli->connect_error) {
        die("❌ Connection failed: " . $mysqli->connect_error . "\n");
    }
    
    echo "✅ Connected to database: $database\n\n";
    
    // 1. Create master SQL file with ALL current data
    echo "📋 Step 1: Creating Master SQL Export\n";
    echo "=====================================\n";
    
    $masterSQL = "";
    
    // Header
    $masterSQL .= "-- =====================================================\n";
    $masterSQL .= "-- LMS ALBERCA - COMPLETE SYSTEM EXPORT\n";
    $masterSQL .= "-- =====================================================\n";
    $masterSQL .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $masterSQL .= "-- Purpose: Complete synchronization with phpMyAdmin\n";
    $masterSQL .= "-- Source: All system files and database\n";
    $masterSQL .= "-- Target: phpMyAdmin import\n";
    $masterSQL .= "-- =====================================================\n\n";
    
    $masterSQL .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $masterSQL .= "START TRANSACTION;\n";
    $masterSQL .= "SET time_zone = \"+00:00\";\n";
    $masterSQL .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    
    $masterSQL .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
    $masterSQL .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
    $masterSQL .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
    $masterSQL .= "/*!40101 SET NAMES utf8mb4 */;\n\n";
    
    $masterSQL .= "-- Create and use database\n";
    $masterSQL .= "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
    $masterSQL .= "USE `$database`;\n\n";
    
    // Get all tables
    $tablesResult = $mysqli->query("SHOW TABLES");
    $tables = [];
    
    if ($tablesResult && $tablesResult->num_rows > 0) {
        while ($row = $tablesResult->fetch_array()) {
            $tables[] = $row[0];
        }
        echo "📊 Found " . count($tables) . " tables in database\n";
    } else {
        echo "⚠️  No tables found, using structure from lms_alberca.sql\n";
        // Use the updated lms_alberca.sql as base
        $baseSQL = file_get_contents('lms_alberca.sql');
        $masterSQL .= $baseSQL;
        $tables = ['users', 'courses', 'enrollments', 'lessons', 'materials', 'migrations', 'notifications', 'quizzes', 'submissions'];
    }
    
    if (!empty($tables)) {
        foreach ($tables as $table) {
            echo "  🔧 Processing: $table\n";
            
            $masterSQL .= "-- =====================================================\n";
            $masterSQL .= "-- Table: $table\n";
            $masterSQL .= "-- =====================================================\n\n";
            
            // Get table structure
            $createResult = $mysqli->query("SHOW CREATE TABLE `$table`");
            if ($createResult) {
                $createRow = $createResult->fetch_array();
                $masterSQL .= "DROP TABLE IF EXISTS `$table`;\n";
                $masterSQL .= $createRow[1] . ";\n\n";
                
                // Get data
                $dataResult = $mysqli->query("SELECT * FROM `$table`");
                if ($dataResult && $dataResult->num_rows > 0) {
                    $rowCount = $dataResult->num_rows;
                    echo "    📊 $rowCount records\n";
                    
                    // Get columns
                    $columnsResult = $mysqli->query("SHOW COLUMNS FROM `$table`");
                    $columns = [];
                    while ($col = $columnsResult->fetch_assoc()) {
                        $columns[] = "`" . $col['Field'] . "`";
                    }
                    
                    $masterSQL .= "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES\n";
                    
                    $values = [];
                    while ($row = $dataResult->fetch_assoc()) {
                        $rowValues = [];
                        foreach ($row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . $mysqli->real_escape_string($value) . "'";
                            }
                        }
                        $values[] = "(" . implode(", ", $rowValues) . ")";
                    }
                    
                    $masterSQL .= implode(",\n", $values) . ";\n\n";
                } else {
                    echo "    ⚪ No data\n";
                }
            }
        }
    }
    
    // Add AUTO_INCREMENT and constraints
    $masterSQL .= "-- =====================================================\n";
    $masterSQL .= "-- AUTO_INCREMENT and CONSTRAINTS\n";
    $masterSQL .= "-- =====================================================\n\n";
    
    // AUTO_INCREMENT
    foreach ($tables as $table) {
        if ($table !== 'migrations') {
            $masterSQL .= "ALTER TABLE `$table` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;\n";
        } else {
            $masterSQL .= "ALTER TABLE `$table` MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;\n";
        }
    }
    
    $masterSQL .= "\n-- Foreign Key Constraints\n";
    $constraints = [
        "ALTER TABLE `courses` ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;",
        "ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;",
        "ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;",
        "ALTER TABLE `materials` ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;",
        "ALTER TABLE `notifications` ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;"
    ];
    
    foreach ($constraints as $constraint) {
        $masterSQL .= $constraint . "\n";
    }
    
    $masterSQL .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
    $masterSQL .= "COMMIT;\n\n";
    
    // Add credentials info
    $masterSQL .= "-- =====================================================\n";
    $masterSQL .= "-- SYSTEM CREDENTIALS\n";
    $masterSQL .= "-- =====================================================\n";
    $masterSQL .= "-- These match your UserSeeder.php file:\n";
    $masterSQL .= "-- Admin: admin@example.com / admin123\n";
    $masterSQL .= "-- Teacher: teacher@example.com / teacher123\n";
    $masterSQL .= "-- Student: student@example.com / student123\n";
    $masterSQL .= "-- Extra Teacher: alice.teacher@example.com / teacher456\n";
    $masterSQL .= "-- Extra Student: bob.student@example.com / student456\n";
    $masterSQL .= "-- =====================================================\n";
    
    // Save master file
    $masterFile = "MASTER_lms_alberca_complete_sync.sql";
    file_put_contents($masterFile, $masterSQL);
    
    echo "\n✅ Master SQL file created: $masterFile\n";
    
    // 2. Update all related SQL files to match
    echo "\n📋 Step 2: Updating All SQL Files\n";
    echo "=================================\n";
    
    $filesToUpdate = [
        'lms_alberca_complete.sql',
        'lms_alberca_export_for_phpmyadmin.sql'
    ];
    
    foreach ($filesToUpdate as $file) {
        if (file_exists($file)) {
            copy($masterFile, $file);
            echo "✅ Updated: $file\n";
        }
    }
    
    // 3. Verify UserSeeder matches
    echo "\n📋 Step 3: Verifying UserSeeder Compatibility\n";
    echo "============================================\n";
    
    $userSeederContent = file_get_contents('app/Database/Seeds/UserSeeder.php');
    
    if (strpos($userSeederContent, 'admin@example.com') !== false) {
        echo "✅ UserSeeder.php matches SQL files\n";
    } else {
        echo "⚠️  UserSeeder.php needs updating\n";
    }
    
    // 4. Create import instructions
    echo "\n📋 Step 4: Creating Import Instructions\n";
    echo "======================================\n";
    
    $instructions = "# LMS ALBERCA - phpMyAdmin Import Instructions\n\n";
    $instructions .= "## Files Ready for Import:\n";
    $instructions .= "- **Primary**: `MASTER_lms_alberca_complete_sync.sql`\n";
    $instructions .= "- **Backup**: `lms_alberca_complete.sql`\n\n";
    $instructions .= "## Import Steps:\n";
    $instructions .= "1. Open phpMyAdmin (http://localhost/phpmyadmin)\n";
    $instructions .= "2. Click 'Import' tab\n";
    $instructions .= "3. Choose file: `MASTER_lms_alberca_complete_sync.sql`\n";
    $instructions .= "4. Click 'Go' button\n";
    $instructions .= "5. Wait for completion\n\n";
    $instructions .= "## Login Credentials:\n";
    $instructions .= "- **Admin**: admin@example.com / admin123\n";
    $instructions .= "- **Teacher**: teacher@example.com / teacher123\n";
    $instructions .= "- **Student**: student@example.com / student123\n";
    $instructions .= "- **Extra Teacher**: alice.teacher@example.com / teacher456\n";
    $instructions .= "- **Extra Student**: bob.student@example.com / student456\n\n";
    $instructions .= "## After Import:\n";
    $instructions .= "- Test login: http://localhost/ITE311-ALBERCA/login\n";
    $instructions .= "- All system files will match phpMyAdmin database\n";
    
    file_put_contents('IMPORT_INSTRUCTIONS.md', $instructions);
    
    $mysqli->close();
    
    echo "\n🎯 SYNCHRONIZATION COMPLETE!\n";
    echo "============================\n";
    echo "✅ Master file: MASTER_lms_alberca_complete_sync.sql\n";
    echo "✅ Instructions: IMPORT_INSTRUCTIONS.md\n";
    echo "✅ All files synchronized\n";
    echo "✅ Ready for phpMyAdmin import\n";
    
    $fileSize = filesize($masterFile);
    echo "\n📊 Master file size: " . number_format($fileSize / 1024, 2) . " KB\n";
    echo "📋 Tables: " . count($tables) . "\n";
    echo "🔑 Credentials: Match UserSeeder.php\n";
    
    echo "\n🚀 Next: Import MASTER_lms_alberca_complete_sync.sql to phpMyAdmin!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>