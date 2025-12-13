<?php
echo "📤 Export XAMPP Data to phpMyAdmin\n";
echo "==================================\n\n";

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
    
    echo "✅ Connected to XAMPP database: $database\n\n";
    
    // Start building the export file
    $exportFile = "lms_alberca_export_for_phpmyadmin.sql";
    $sql = "";
    
    // Add header
    $sql .= "-- phpMyAdmin SQL Export\n";
    $sql .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
    $sql .= "-- Source: XAMPP Database\n";
    $sql .= "-- Target: phpMyAdmin\n\n";
    
    $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sql .= "START TRANSACTION;\n";
    $sql .= "SET time_zone = \"+00:00\";\n\n";
    
    $sql .= "-- Create database\n";
    $sql .= "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
    $sql .= "USE `$database`;\n\n";
    
    // Get all tables
    $tablesResult = $mysqli->query("SHOW TABLES");
    $tables = [];
    
    while ($row = $tablesResult->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "📋 Exporting " . count($tables) . " tables...\n";
    
    foreach ($tables as $table) {
        echo "  📄 Processing: $table\n";
        
        // Get table structure
        $createResult = $mysqli->query("SHOW CREATE TABLE `$table`");
        $createRow = $createResult->fetch_array();
        
        $sql .= "-- --------------------------------------------------------\n";
        $sql .= "-- Table structure for table `$table`\n";
        $sql .= "-- --------------------------------------------------------\n\n";
        $sql .= "DROP TABLE IF EXISTS `$table`;\n";
        $sql .= $createRow[1] . ";\n\n";
        
        // Get data
        $dataResult = $mysqli->query("SELECT * FROM `$table`");
        $rowCount = $dataResult->num_rows;
        
        if ($rowCount > 0) {
            $sql .= "-- Dumping data for table `$table`\n";
            $sql .= "-- Records: $rowCount\n\n";
            
            // Get column info
            $columnsResult = $mysqli->query("SHOW COLUMNS FROM `$table`");
            $columns = [];
            while ($col = $columnsResult->fetch_assoc()) {
                $columns[] = "`" . $col['Field'] . "`";
            }
            
            $sql .= "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES\n";
            
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
            
            $sql .= implode(",\n", $values) . ";\n\n";
            echo "    ✅ Exported $rowCount records\n";
        } else {
            echo "    ⚪ No data\n";
        }
    }
    
    // Add AUTO_INCREMENT settings
    $sql .= "-- --------------------------------------------------------\n";
    $sql .= "-- AUTO_INCREMENT settings\n";
    $sql .= "-- --------------------------------------------------------\n\n";
    
    foreach ($tables as $table) {
        $statusResult = $mysqli->query("SHOW TABLE STATUS LIKE '$table'");
        $status = $statusResult->fetch_assoc();
        
        if ($status && $status['Auto_increment']) {
            $idType = ($table === 'migrations') ? 'bigint(20)' : 'int(11)';
            $sql .= "ALTER TABLE `$table` MODIFY `id` $idType unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=" . $status['Auto_increment'] . ";\n";
        }
    }
    
    // Add foreign key constraints
    $sql .= "\n-- --------------------------------------------------------\n";
    $sql .= "-- Foreign key constraints\n";
    $sql .= "-- --------------------------------------------------------\n\n";
    
    $constraints = [
        "ALTER TABLE `courses` ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;",
        "ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;",
        "ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;",
        "ALTER TABLE `materials` ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;",
        "ALTER TABLE `notifications` ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;"
    ];
    
    foreach ($constraints as $constraint) {
        $sql .= $constraint . "\n";
    }
    
    $sql .= "\nCOMMIT;\n";
    
    // Save to file
    file_put_contents($exportFile, $sql);
    
    $fileSize = filesize($exportFile);
    
    echo "\n✅ Export completed successfully!\n";
    echo "📁 File: $exportFile\n";
    echo "📊 Size: " . number_format($fileSize / 1024, 2) . " KB\n";
    
    // Count total records exported
    $totalRecords = 0;
    foreach ($tables as $table) {
        $countResult = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countResult->fetch_assoc()['count'];
        $totalRecords += $count;
    }
    
    echo "📋 Total records exported: $totalRecords\n";
    
    $mysqli->close();
    
    echo "\n🎯 How to import into phpMyAdmin:\n";
    echo "================================\n";
    echo "1. Open phpMyAdmin in your browser\n";
    echo "2. Click 'Import' tab\n";
    echo "3. Choose file: $exportFile\n";
    echo "4. Click 'Go' button\n";
    echo "5. Wait for import to complete\n";
    
    echo "\n✅ Your XAMPP data is ready for phpMyAdmin!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>