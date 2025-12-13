<?php
echo "🔄 Converting MySQL Database Files to SQL\n";
echo "========================================\n\n";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_alberca';
$port = 3306;

try {
    // Connect to the database
    $mysqli = new mysqli($host, $username, $password, $database, $port);
    
    if ($mysqli->connect_error) {
        die("❌ Connection failed: " . $mysqli->connect_error . "\n");
    }
    
    echo "✅ Connected to database: $database\n";
    echo "📁 Found database files in lms_alberca folder\n\n";
    
    // Create comprehensive SQL export
    $sqlContent = "";
    
    // Add SQL header
    $sqlContent .= "-- =====================================================\n";
    $sqlContent .= "-- MySQL Database Export from Physical Files\n";
    $sqlContent .= "-- Database: lms_alberca\n";
    $sqlContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $sqlContent .= "-- Source: 19 MySQL database files (.frm + .ibd)\n";
    $sqlContent .= "-- =====================================================\n\n";
    
    $sqlContent .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sqlContent .= "START TRANSACTION;\n";
    $sqlContent .= "SET time_zone = \"+00:00\";\n";
    $sqlContent .= "SET FOREIGN_KEY_CHECKS = 0;\n\n";
    
    $sqlContent .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
    $sqlContent .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
    $sqlContent .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
    $sqlContent .= "/*!40101 SET NAMES utf8mb4 */;\n\n";
    
    // Create database
    $sqlContent .= "-- Create database\n";
    $sqlContent .= "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
    $sqlContent .= "USE `$database`;\n\n";
    
    // Get all tables from the database
    $tablesResult = $mysqli->query("SHOW TABLES");
    $tables = [];
    
    echo "📋 Processing tables from database files:\n";
    
    while ($row = $tablesResult->fetch_array()) {
        $tables[] = $row[0];
    }
    
    // Process each table
    foreach ($tables as $table) {
        echo "  🔧 Processing table: $table\n";
        
        $sqlContent .= "-- =====================================================\n";
        $sqlContent .= "-- Table: $table\n";
        $sqlContent .= "-- =====================================================\n\n";
        
        // Get table structure
        $createResult = $mysqli->query("SHOW CREATE TABLE `$table`");
        if ($createResult) {
            $createRow = $createResult->fetch_array();
            
            $sqlContent .= "-- Table structure for table `$table`\n";
            $sqlContent .= "DROP TABLE IF EXISTS `$table`;\n";
            $sqlContent .= $createRow[1] . ";\n\n";
        }
        
        // Get table data
        $dataResult = $mysqli->query("SELECT * FROM `$table`");
        if ($dataResult && $dataResult->num_rows > 0) {
            $rowCount = $dataResult->num_rows;
            echo "    📊 Found $rowCount records\n";
            
            $sqlContent .= "-- Data for table `$table`\n";
            $sqlContent .= "-- Records: $rowCount\n\n";
            
            // Get column names
            $columnsResult = $mysqli->query("SHOW COLUMNS FROM `$table`");
            $columns = [];
            while ($col = $columnsResult->fetch_assoc()) {
                $columns[] = "`" . $col['Field'] . "`";
            }
            
            $sqlContent .= "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES\n";
            
            // Get all data
            $values = [];
            $dataResult->data_seek(0); // Reset result pointer
            
            while ($row = $dataResult->fetch_assoc()) {
                $rowValues = [];
                foreach ($row as $value) {
                    if ($value === null) {
                        $rowValues[] = 'NULL';
                    } elseif (is_numeric($value)) {
                        $rowValues[] = $value;
                    } else {
                        $rowValues[] = "'" . $mysqli->real_escape_string($value) . "'";
                    }
                }
                $values[] = "(" . implode(", ", $rowValues) . ")";
            }
            
            $sqlContent .= implode(",\n", $values) . ";\n\n";
        } else {
            echo "    ⚪ No data found\n";
            $sqlContent .= "-- No data for table `$table`\n\n";
        }
    }
    
    // Add AUTO_INCREMENT settings
    $sqlContent .= "-- =====================================================\n";
    $sqlContent .= "-- AUTO_INCREMENT Settings\n";
    $sqlContent .= "-- =====================================================\n\n";
    
    foreach ($tables as $table) {
        $statusResult = $mysqli->query("SHOW TABLE STATUS LIKE '$table'");
        if ($statusResult) {
            $status = $statusResult->fetch_assoc();
            if ($status && $status['Auto_increment'] && $status['Auto_increment'] > 1) {
                $idType = ($table === 'migrations') ? 'bigint(20)' : 'int(11)';
                $sqlContent .= "ALTER TABLE `$table` MODIFY `id` $idType unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=" . $status['Auto_increment'] . ";\n";
            }
        }
    }
    
    // Add foreign key constraints
    $sqlContent .= "\n-- =====================================================\n";
    $sqlContent .= "-- Foreign Key Constraints\n";
    $sqlContent .= "-- =====================================================\n\n";
    
    $constraints = [
        "ALTER TABLE `courses` ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;",
        "ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
        "ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
        "ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;",
        "ALTER TABLE `lessons` ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
        "ALTER TABLE `materials` ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
        "ALTER TABLE `notifications` ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
        "ALTER TABLE `quizzes` ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
        "ALTER TABLE `submissions` ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;",
        "ALTER TABLE `submissions` ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;"
    ];
    
    foreach ($constraints as $constraint) {
        $sqlContent .= $constraint . "\n";
    }
    
    // Add footer
    $sqlContent .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";
    $sqlContent .= "COMMIT;\n\n";
    $sqlContent .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
    $sqlContent .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
    $sqlContent .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n\n";
    
    $sqlContent .= "-- =====================================================\n";
    $sqlContent .= "-- Export completed successfully!\n";
    $sqlContent .= "-- Total tables: " . count($tables) . "\n";
    $sqlContent .= "-- Source: 19 MySQL database files\n";
    $sqlContent .= "-- Ready for phpMyAdmin import\n";
    $sqlContent .= "-- =====================================================\n";
    
    // Save to file
    $filename = "lms_alberca_from_19_files.sql";
    file_put_contents($filename, $sqlContent);
    
    $fileSize = filesize($filename);
    
    echo "\n✅ Conversion completed successfully!\n";
    echo "📁 Output file: $filename\n";
    echo "📊 File size: " . number_format($fileSize / 1024, 2) . " KB\n";
    echo "📋 Tables processed: " . count($tables) . "\n";
    
    // Count total records
    $totalRecords = 0;
    foreach ($tables as $table) {
        $countResult = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        if ($countResult) {
            $count = $countResult->fetch_assoc()['count'];
            $totalRecords += $count;
            echo "  - $table: $count records\n";
        }
    }
    
    echo "📊 Total records: $totalRecords\n";
    
    $mysqli->close();
    
    echo "\n🎯 Ready for phpMyAdmin!\n";
    echo "========================\n";
    echo "1. Open phpMyAdmin\n";
    echo "2. Go to Import tab\n";
    echo "3. Choose file: $filename\n";
    echo "4. Click 'Go' to import\n";
    echo "\n✅ Your 19 database files are now in 1 SQL file!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>