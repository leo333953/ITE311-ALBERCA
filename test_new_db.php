<?php
// Test new database connection
$mysqli = new mysqli('localhost', 'root', '', 'lms_system');

if ($mysqli->connect_error) {
    echo "❌ Connection failed: " . $mysqli->connect_error . "\n";
    
    // Try to create the database
    $mysqli_create = new mysqli('localhost', 'root', '');
    if (!$mysqli_create->connect_error) {
        echo "✅ Connected to MySQL server\n";
        
        if ($mysqli_create->query("CREATE DATABASE IF NOT EXISTS lms_system")) {
            echo "✅ Database lms_system created successfully\n";
        } else {
            echo "❌ Error creating database: " . $mysqli_create->error . "\n";
        }
        $mysqli_create->close();
    }
} else {
    echo "✅ Database connection successful to lms_system!\n";
}

$mysqli->close();
?>