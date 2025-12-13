<?php
echo "🚀 Complete Database Export Script\n";
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
    
    echo "✅ Connected to database: $database\n\n";
    
    // Start building the SQL export
    $sqlExport = "-- phpMyAdmin SQL Dump\n";
    $sqlExport .= "-- Generated on: " . date('Y-m-d H:i:s') . "\n";
    $sqlExport .= "-- Host: $host:$port\n";
    $sqlExport .= "-- Database: $database\n\n";
    
    $sqlExport .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
    $sqlExport .= "START TRANSACTION;\n";
    $sqlExport .= "SET time_zone = \"+00:00\";\n\n";
    
    $sqlExport .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
    $sqlExport .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
    $sqlExport .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
    $sqlExport .= "/*!40101 SET NAMES utf8mb4 */;\n\n";
    
    $sqlExport .= "--\n-- Database: `$database`\n--\n";
    $sqlExport .= "CREATE DATABASE IF NOT EXISTS `$database` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
    $sqlExport .= "USE `$database`;\n\n";
    
    // Get all tables
    $tablesResult = $mysqli->query("SHOW TABLES");
    $tables = [];
    
    echo "📋 Found tables:\n";
    while ($row = $tablesResult->fetch_array()) {
        $tableName = $row[0];
        $tables[] = $tableName;
        echo "  - $tableName\n";
    }
    
    echo "\n🔧 Exporting table structures and data...\n";
    
    foreach ($tables as $table) {
        echo "  📄 Processing table: $table\n";
        
        // Get table structure
        $createTableResult = $mysqli->query("SHOW CREATE TABLE `$table`");
        $createTableRow = $createTableResult->fetch_array();
        
        $sqlExport .= "-- --------------------------------------------------------\n\n";
        $sqlExport .= "--\n-- Table structure for table `$table`\n--\n\n";
        $sqlExport .= "DROP TABLE IF EXISTS `$table`;\n";
        $sqlExport .= $createTableRow[1] . ";\n\n";
        
        // Get table data
        $dataResult = $mysqli->query("SELECT * FROM `$table`");
        $rowCount = $dataResult->num_rows;
        
        if ($rowCount > 0) {
            $sqlExport .= "--\n-- Dumping data for table `$table`\n--\n\n";
            
            // Get column names
            $columnsResult = $mysqli->query("SHOW COLUMNS FROM `$table`");
            $columns = [];
            while ($col = $columnsResult->fetch_assoc()) {
                $columns[] = "`" . $col['Field'] . "`";
            }
            
            $sqlExport .= "INSERT INTO `$table` (" . implode(", ", $columns) . ") VALUES\n";
            
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
            
            $sqlExport .= implode(",\n", $values) . ";\n\n";
            echo "    ✅ Exported $rowCount rows\n";
        } else {
            echo "    ⚠️  No data found\n";
        }
    }
    
    // Add indexes and constraints
    echo "\n🔧 Adding indexes and constraints...\n";
    
    foreach ($tables as $table) {
        // Get indexes
        $indexResult = $mysqli->query("SHOW INDEX FROM `$table`");
        $indexes = [];
        
        while ($index = $indexResult->fetch_assoc()) {
            if ($index['Key_name'] !== 'PRIMARY') {
                $indexes[$index['Key_name']][] = $index;
            }
        }
        
        if (!empty($indexes)) {
            $sqlExport .= "--\n-- Indexes for table `$table`\n--\n";
            foreach ($indexes as $indexName => $indexData) {
                $columns = [];
                $isUnique = $indexData[0]['Non_unique'] == 0;
                
                foreach ($indexData as $col) {
                    $columns[] = "`" . $col['Column_name'] . "`";
                }
                
                if ($isUnique) {
                    $sqlExport .= "ALTER TABLE `$table` ADD UNIQUE KEY `$indexName` (" . implode(", ", $columns) . ");\n";
                } else {
                    $sqlExport .= "ALTER TABLE `$table` ADD KEY `$indexName` (" . implode(", ", $columns) . ");\n";
                }
            }
            $sqlExport .= "\n";
        }
    }
    
    // Add AUTO_INCREMENT settings
    echo "🔧 Adding AUTO_INCREMENT settings...\n";
    $sqlExport .= "--\n-- AUTO_INCREMENT for dumped tables\n--\n\n";
    
    foreach ($tables as $table) {
        $statusResult = $mysqli->query("SHOW TABLE STATUS LIKE '$table'");
        $status = $statusResult->fetch_assoc();
        
        if ($status['Auto_increment']) {
            $sqlExport .= "--\n-- AUTO_INCREMENT for table `$table`\n--\n";
            $sqlExport .= "ALTER TABLE `$table` MODIFY `id` " . 
                         (strpos($table, 'migrations') !== false ? 'bigint(20)' : 'int(11)') . 
                         " unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=" . $status['Auto_increment'] . ";\n\n";
        }
    }
    
    // Add foreign key constraints
    echo "🔧 Adding foreign key constraints...\n";
    $sqlExport .= "--\n-- Constraints for dumped tables\n--\n\n";
    
    $constraintsQuery = "
        SELECT 
            kcu.TABLE_NAME,
            kcu.COLUMN_NAME,
            kcu.CONSTRAINT_NAME,
            kcu.REFERENCED_TABLE_NAME,
            kcu.REFERENCED_COLUMN_NAME
        FROM 
            INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu
        WHERE 
            kcu.REFERENCED_TABLE_SCHEMA = '$database' 
            AND kcu.REFERENCED_TABLE_NAME IS NOT NULL
    ";
    
    $constraintsResult = $mysqli->query($constraintsQuery);
    $constraints = [];
    
    if ($constraintsResult) {
        while ($constraint = $constraintsResult->fetch_assoc()) {
            $constraints[$constraint['TABLE_NAME']][] = $constraint;
        }
        
        foreach ($constraints as $table => $tableConstraints) {
            $sqlExport .= "--\n-- Constraints for table `$table`\n--\n";
            foreach ($tableConstraints as $constraint) {
                $sqlExport .= "ALTER TABLE `$table` ADD CONSTRAINT `" . $constraint['CONSTRAINT_NAME'] . "` " .
                             "FOREIGN KEY (`" . $constraint['COLUMN_NAME'] . "`) " .
                             "REFERENCES `" . $constraint['REFERENCED_TABLE_NAME'] . "` (`" . $constraint['REFERENCED_COLUMN_NAME'] . "`) " .
                             "ON DELETE CASCADE ON UPDATE CASCADE;\n";
            }
            $sqlExport .= "\n";
        }
    }
    
    $sqlExport .= "COMMIT;\n\n";
    $sqlExport .= "/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n";
    $sqlExport .= "/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n";
    $sqlExport .= "/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n";
    
    // Save to file
    $filename = "lms_alberca_complete_export_" . date('Y-m-d_H-i-s') . ".sql";
    file_put_contents($filename, $sqlExport);
    
    echo "\n✅ Export completed successfully!\n";
    echo "📁 File saved as: $filename\n";
    echo "📊 File size: " . number_format(filesize($filename) / 1024, 2) . " KB\n";
    
    // Display summary
    echo "\n📋 Export Summary:\n";
    echo "  - Database: $database\n";
    echo "  - Tables exported: " . count($tables) . "\n";
    echo "  - Export file: $filename\n";
    
    // Count total records
    $totalRecords = 0;
    foreach ($tables as $table) {
        $countResult = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countResult->fetch_assoc()['count'];
        $totalRecords += $count;
        echo "    - $table: $count records\n";
    }
    echo "  - Total records: $totalRecords\n";
    
    $mysqli->close();
    
    echo "\n🎯 Next Steps:\n";
    echo "1. Open phpMyAdmin in your browser\n";
    echo "2. Create a new database or select existing 'lms_alberca'\n";
    echo "3. Go to Import tab\n";
    echo "4. Choose file: $filename\n";
    echo "5. Click 'Go' to import\n";
    echo "\n🌐 Your complete database is ready for phpMyAdmin!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>