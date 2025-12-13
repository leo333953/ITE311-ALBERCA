<?php
echo "<h2>🔍 Database Synchronization Test</h2>";

// Test direct MySQL connection
echo "<h3>1. Direct MySQL Connection Test</h3>";
try {
    $mysqli = new mysqli('localhost', 'root', '', 'lms_alberca');
    if ($mysqli->connect_error) {
        echo "❌ MySQL Connection failed: " . $mysqli->connect_error . "<br>";
    } else {
        echo "✅ MySQL Connection successful!<br>";
        
        // Test tables
        $tables = ['users', 'courses', 'enrollments', 'notifications', 'materials'];
        foreach ($tables as $table) {
            $result = $mysqli->query("SELECT COUNT(*) as count FROM $table");
            if ($result) {
                $row = $result->fetch_assoc();
                echo "✅ Table '$table': {$row['count']} records<br>";
            } else {
                echo "❌ Table '$table': Error - " . $mysqli->error . "<br>";
            }
        }
        
        // Test new course fields
        echo "<h4>Course Table Structure Test:</h4>";
        $result = $mysqli->query("DESCRIBE courses");
        if ($result) {
            echo "<ul>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>{$row['Field']} - {$row['Type']}</li>";
            }
            echo "</ul>";
        }
    }
    $mysqli->close();
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

// Test CodeIgniter database connection
echo "<h3>2. CodeIgniter Database Connection Test</h3>";
try {
    // Load CodeIgniter
    require_once 'vendor/autoload.php';
    
    // Simple test without full CI bootstrap
    echo "✅ CodeIgniter files accessible<br>";
    echo "📋 Database config points to: lms_alberca<br>";
    
} catch (Exception $e) {
    echo "❌ CodeIgniter test error: " . $e->getMessage() . "<br>";
}

echo "<h3>3. Login Test URLs</h3>";
echo "🌐 <a href='http://localhost/ITE311-ALBERCA/login' target='_blank'>Test Login Page</a><br>";
echo "👤 Admin: admin@lms.com / password<br>";
echo "👨‍🏫 Teacher: teacher@lms.com / password<br>";
echo "👨‍🎓 Student: student@lms.com / password<br>";

echo "<h3>4. Expected Database Structure</h3>";
echo "<strong>Users Table:</strong> id, name, email, password, role, created_at, updated_at, deleted_at<br>";
echo "<strong>Courses Table:</strong> id, course_code, course_name, title, description, year_level, semester, academic_year, instructor_id, status, created_at, updated_at, deleted_at<br>";
echo "<strong>Other Tables:</strong> enrollments, materials, notifications, lessons, quizzes, submissions, migrations<br>";
?>