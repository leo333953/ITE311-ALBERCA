<?php
// Check courses table structure
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_alberca';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "📋 Courses table structure:\n";
    $stmt = $pdo->query("DESCRIBE courses");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "   - {$row['Field']} ({$row['Type']}) - Null: {$row['Null']} - Default: {$row['Default']}\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "\n";
}
?>