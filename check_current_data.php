<?php
echo "🔍 Checking Current Database Data\n";
echo "================================\n\n";

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
    
    // Get all tables
    $tablesResult = $mysqli->query("SHOW TABLES");
    
    if ($tablesResult->num_rows == 0) {
        echo "⚠️  No tables found in database '$database'\n";
        echo "🔧 Make sure your database has data to export\n";
        exit;
    }
    
    echo "📊 Current Database Contents:\n";
    echo "============================\n";
    
    $totalRecords = 0;
    
    while ($row = $tablesResult->fetch_array()) {
        $tableName = $row[0];
        
        // Get record count
        $countResult = $mysqli->query("SELECT COUNT(*) as count FROM `$tableName`");
        $count = $countResult->fetch_assoc()['count'];
        $totalRecords += $count;
        
        echo "📋 Table: $tableName ($count records)\n";
        
        if ($count > 0) {
            // Show sample data for important tables
            if (in_array($tableName, ['users', 'courses', 'enrollments', 'materials', 'notifications'])) {
                echo "   📄 Sample data:\n";
                $sampleResult = $mysqli->query("SELECT * FROM `$tableName` LIMIT 3");
                
                while ($sample = $sampleResult->fetch_assoc()) {
                    $displayData = [];
                    $counter = 0;
                    foreach ($sample as $key => $value) {
                        if ($counter < 4 && !in_array($key, ['password', 'created_at', 'updated_at'])) {
                            $displayValue = strlen($value) > 30 ? substr($value, 0, 30) . '...' : $value;
                            $displayData[] = "$key: $displayValue";
                            $counter++;
                        }
                    }
                    echo "      " . implode(" | ", $displayData) . "\n";
                }
                echo "\n";
            }
        }
    }
    
    echo "📊 Total Records: $totalRecords\n";
    
    if ($totalRecords > 0) {
        echo "\n✅ You have data to export!\n";
        echo "🚀 Ready to create phpMyAdmin import file\n";
    } else {
        echo "\n⚠️  No data found to export\n";
        echo "💡 Make sure you have added data through your application first\n";
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>