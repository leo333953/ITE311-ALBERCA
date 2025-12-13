<?php
// Quick database test
try {
    $pdo = new PDO('mysql:host=localhost;dbname=lms_system', 'root', '');
    echo "SUCCESS: Connected to lms_system database";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
    
    // Try to create database
    try {
        $pdo = new PDO('mysql:host=localhost', 'root', '');
        $pdo->exec("CREATE DATABASE IF NOT EXISTS lms_system");
        echo "\nDatabase lms_system created";
    } catch (PDOException $e2) {
        echo "\nFailed to create database: " . $e2->getMessage();
    }
}
?>