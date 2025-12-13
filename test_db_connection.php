<?php
// Test database connection
$mysqli = new mysqli('localhost', 'root', '', 'lms_alberca');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "✅ Database connection successful!\n";

// Test if users table exists and has data
$result = $mysqli->query("SELECT COUNT(*) as count FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Users table exists with " . $row['count'] . " records\n";
} else {
    echo "❌ Error accessing users table: " . $mysqli->error . "\n";
}

// Test if migrations table exists
$result = $mysqli->query("SELECT COUNT(*) as count FROM migrations");
if ($result) {
    $row = $result->fetch_assoc();
    echo "✅ Migrations table exists with " . $row['count'] . " records\n";
} else {
    echo "❌ Error accessing migrations table: " . $mysqli->error . "\n";
}

// Show all tables
$result = $mysqli->query("SHOW TABLES");
echo "\n📋 Available tables:\n";
while ($row = $result->fetch_array()) {
    echo "  - " . $row[0] . "\n";
}

$mysqli->close();
?>