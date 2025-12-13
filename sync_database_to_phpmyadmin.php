<?php
echo "🔄 Database Synchronization Script\n";
echo "=================================\n\n";

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
    
    // Check if database exists and has tables
    $tablesResult = $mysqli->query("SHOW TABLES");
    $tableCount = $tablesResult->num_rows;
    
    if ($tableCount == 0) {
        echo "⚠️  Database '$database' exists but has no tables!\n";
        echo "🔧 Let's import the complete structure...\n\n";
        
        // Read and execute the lms_alberca.sql file
        if (file_exists('lms_alberca.sql')) {
            echo "📁 Found lms_alberca.sql file\n";
            echo "🚀 Importing database structure and data...\n";
            
            $sql = file_get_contents('lms_alberca.sql');
            
            // Split SQL into individual statements
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            $successCount = 0;
            $errorCount = 0;
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^(\/\*|--|SET|START|COMMIT)/', $statement)) {
                    if ($mysqli->query($statement)) {
                        $successCount++;
                    } else {
                        $errorCount++;
                        if (strpos($mysqli->error, 'already exists') === false && 
                            strpos($mysqli->error, 'Duplicate') === false) {
                            echo "⚠️  Warning: " . $mysqli->error . "\n";
                        }
                    }
                }
            }
            
            echo "✅ Import completed: $successCount statements executed\n";
            if ($errorCount > 0) {
                echo "⚠️  $errorCount statements had warnings (likely duplicates)\n";
            }
        } else {
            echo "❌ lms_alberca.sql file not found!\n";
            echo "🔧 Creating basic structure...\n";
            
            // Create basic tables if SQL file doesn't exist
            $basicTables = [
                "CREATE TABLE IF NOT EXISTS `users` (
                    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                    `name` varchar(255) NOT NULL,
                    `email` varchar(255) NOT NULL,
                    `password` varchar(255) NOT NULL,
                    `role` enum('admin','teacher','student') NOT NULL DEFAULT 'student',
                    `created_at` datetime DEFAULT NULL,
                    `updated_at` datetime DEFAULT NULL,
                    `deleted_at` datetime DEFAULT NULL,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `email` (`email`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
                
                "INSERT IGNORE INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
                (1, 'Admin Alberca', 'admin@lms.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW()),
                (2, 'Teacher Alberca', 'teacher@lms.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NOW(), NOW()),
                (3, 'Student Alberca', 'student@lms.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW(), NOW())"
            ];
            
            foreach ($basicTables as $sql) {
                if ($mysqli->query($sql)) {
                    echo "✅ Created basic structure\n";
                } else {
                    echo "❌ Error: " . $mysqli->error . "\n";
                }
            }
        }
    }
    
    // Now check the current state
    echo "\n📊 Current Database Status:\n";
    echo "==========================\n";
    
    $tablesResult = $mysqli->query("SHOW TABLES");
    $tables = [];
    
    while ($row = $tablesResult->fetch_array()) {
        $tables[] = $row[0];
    }
    
    echo "📋 Tables found: " . count($tables) . "\n";
    
    foreach ($tables as $table) {
        $countResult = $mysqli->query("SELECT COUNT(*) as count FROM `$table`");
        $count = $countResult->fetch_assoc()['count'];
        echo "  - $table: $count records\n";
        
        // Show sample data for important tables
        if (in_array($table, ['users', 'courses', 'enrollments']) && $count > 0) {
            echo "    📄 Sample data:\n";
            $sampleResult = $mysqli->query("SELECT * FROM `$table` LIMIT 2");
            while ($sample = $sampleResult->fetch_assoc()) {
                $displayData = [];
                foreach ($sample as $key => $value) {
                    if (in_array($key, ['id', 'name', 'email', 'title', 'course_code', 'course_name', 'status', 'role'])) {
                        $displayData[] = "$key: $value";
                    }
                }
                echo "      " . implode(", ", array_slice($displayData, 0, 3)) . "\n";
            }
        }
    }
    
    // Check if new course fields exist
    echo "\n🔧 Checking course table structure...\n";
    $courseStructure = $mysqli->query("DESCRIBE courses");
    $courseFields = [];
    
    if ($courseStructure) {
        while ($field = $courseStructure->fetch_assoc()) {
            $courseFields[] = $field['Field'];
        }
        
        $requiredFields = ['course_code', 'course_name', 'year_level', 'semester', 'academic_year', 'status'];
        $missingFields = array_diff($requiredFields, $courseFields);
        
        if (empty($missingFields)) {
            echo "✅ Course table has all required fields\n";
        } else {
            echo "⚠️  Missing fields in courses table: " . implode(', ', $missingFields) . "\n";
            echo "🔧 Run update_database_structure.php to add missing fields\n";
        }
    }
    
    $mysqli->close();
    
    echo "\n🎯 Database Synchronization Summary:\n";
    echo "===================================\n";
    echo "✅ Database '$database' is accessible\n";
    echo "📊 Total tables: " . count($tables) . "\n";
    echo "🌐 Ready for use with your LMS system\n";
    
    echo "\n🔗 Test your system:\n";
    echo "   URL: http://localhost/ITE311-ALBERCA/login\n";
    echo "   Admin: admin@lms.com / password\n";
    echo "   Teacher: teacher@lms.com / password\n";
    echo "   Student: student@lms.com / password\n";
    
    echo "\n📁 Files available for phpMyAdmin import:\n";
    echo "   - lms_alberca.sql (complete structure)\n";
    if (file_exists('export_complete_database.php')) {
        echo "   - Run export_complete_database.php to create current export\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>