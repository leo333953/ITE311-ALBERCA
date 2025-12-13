<?php
echo "🔄 Database Transfer Script\n";
echo "==========================\n\n";

// Database connection settings
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect to MySQL
    $mysqli = new mysqli($host, $username, $password);
    
    if ($mysqli->connect_error) {
        die("❌ Connection failed: " . $mysqli->connect_error . "\n");
    }
    
    echo "✅ Connected to MySQL server\n";
    
    // Check if lms_alberca database exists
    $result = $mysqli->query("SHOW DATABASES LIKE 'lms_alberca'");
    
    if ($result->num_rows > 0) {
        echo "✅ lms_alberca database found\n";
        
        // Select the database
        $mysqli->select_db('lms_alberca');
        
        // Check tables
        $tablesResult = $mysqli->query("SHOW TABLES");
        $tables = [];
        
        echo "📊 Tables in lms_alberca:\n";
        while ($row = $tablesResult->fetch_array()) {
            $tables[] = $row[0];
            echo "  - " . $row[0] . "\n";
        }
        
        // Check if tables have the new course structure
        if (in_array('courses', $tables)) {
            $courseStructure = $mysqli->query("DESCRIBE courses");
            $courseFields = [];
            
            echo "\n📋 Course table structure:\n";
            while ($field = $courseStructure->fetch_assoc()) {
                $courseFields[] = $field['Field'];
                echo "  - " . $field['Field'] . " (" . $field['Type'] . ")\n";
            }
            
            // Check if new fields exist
            $newFields = ['course_code', 'course_name', 'year_level', 'semester', 'academic_year', 'status'];
            $missingFields = array_diff($newFields, $courseFields);
            
            if (empty($missingFields)) {
                echo "✅ Course table has all new fields\n";
            } else {
                echo "⚠️  Missing fields in courses table: " . implode(', ', $missingFields) . "\n";
                echo "🔧 Need to run migration to add missing fields\n";
            }
        }
        
        // Check sample data
        if (in_array('users', $tables)) {
            $userCount = $mysqli->query("SELECT COUNT(*) as count FROM users")->fetch_assoc();
            echo "\n👥 Users in database: " . $userCount['count'] . "\n";
            
            // Show sample users
            $users = $mysqli->query("SELECT name, email, role FROM users LIMIT 5");
            echo "📋 Sample users:\n";
            while ($user = $users->fetch_assoc()) {
                echo "  - " . $user['name'] . " (" . $user['email'] . ") - " . $user['role'] . "\n";
            }
        }
        
        if (in_array('courses', $tables)) {
            $courseCount = $mysqli->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc();
            echo "\n📚 Courses in database: " . $courseCount['count'] . "\n";
            
            // Show sample courses
            $courses = $mysqli->query("SELECT title, course_code, course_name FROM courses LIMIT 3");
            echo "📋 Sample courses:\n";
            while ($course = $courses->fetch_assoc()) {
                $code = $course['course_code'] ?? 'N/A';
                $name = $course['course_name'] ?? $course['title'];
                echo "  - [$code] $name\n";
            }
        }
        
    } else {
        echo "❌ lms_alberca database not found\n";
        echo "🔧 Creating lms_alberca database...\n";
        
        if ($mysqli->query("CREATE DATABASE lms_alberca")) {
            echo "✅ lms_alberca database created\n";
        } else {
            echo "❌ Failed to create database: " . $mysqli->error . "\n";
        }
    }
    
    $mysqli->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Next Steps:\n";
echo "1. Open phpMyAdmin: http://localhost/phpmyadmin\n";
echo "2. Click on 'lms_alberca' database\n";
echo "3. If tables are missing, import lms_alberca.sql\n";
echo "4. Test login: http://localhost/ITE311-ALBERCA/login\n";
echo "   - Admin: admin@lms.com / password\n";
echo "   - Teacher: teacher@lms.com / password\n";
echo "   - Student: student@lms.com / password\n";
?>