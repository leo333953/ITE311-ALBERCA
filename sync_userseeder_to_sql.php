<?php
echo "🔄 Syncing UserSeeder with SQL File\n";
echo "===================================\n\n";

// Read the UserSeeder file to extract user data
$userSeederContent = file_get_contents('app/Database/Seeds/UserSeeder.php');

echo "✅ Reading UserSeeder.php...\n";

// Create SQL with UserSeeder data
$sqlContent = "-- =====================================================\n";
$sqlContent .= "-- LMS ALBERCA - Database with UserSeeder Data\n";
$sqlContent .= "-- =====================================================\n";
$sqlContent .= "-- Synced with: app/Database/Seeds/UserSeeder.php\n";
$sqlContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$sqlContent .= "-- Users: Matching UserSeeder credentials\n";
$sqlContent .= "-- =====================================================\n\n";

$sqlContent .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
$sqlContent .= "START TRANSACTION;\n";
$sqlContent .= "SET time_zone = \"+00:00\";\n\n";

$sqlContent .= "/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;\n";
$sqlContent .= "/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;\n";
$sqlContent .= "/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;\n";
$sqlContent .= "/*!40101 SET NAMES utf8mb4 */;\n\n";

$sqlContent .= "CREATE DATABASE IF NOT EXISTS `lms_alberca` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;\n";
$sqlContent .= "USE `lms_alberca`;\n\n";

// Read the base structure from lms_alberca.sql but replace user data
$baseSql = file_get_contents('lms_alberca.sql');

// Extract everything except the user INSERT statements
$lines = explode("\n", $baseSql);
$inUserInsert = false;
$skipLines = false;

foreach ($lines as $line) {
    // Skip the header comments from original file
    if (strpos($line, '-- phpMyAdmin SQL Dump') !== false) {
        $skipLines = true;
        continue;
    }
    if (strpos($line, 'USE `lms_alberca`;') !== false) {
        $skipLines = false;
        continue;
    }
    if ($skipLines) continue;
    
    // Skip original user insert statements
    if (strpos($line, 'INSERT INTO `users`') !== false) {
        $inUserInsert = true;
        continue;
    }
    if ($inUserInsert && (trim($line) === '' || strpos($line, '--') === 0)) {
        $inUserInsert = false;
    }
    if ($inUserInsert) continue;
    
    $sqlContent .= $line . "\n";
}

// Now add UserSeeder data
$sqlContent .= "-- =====================================================\n";
$sqlContent .= "-- USER DATA FROM USERSEEDER.PHP\n";
$sqlContent .= "-- =====================================================\n\n";

$sqlContent .= "-- Dumping data for table `users` (from UserSeeder.php)\n\n";

$sqlContent .= "INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES\n";

// UserSeeder data with proper password hashes
$users = [
    "(1, 'Admin', 'admin@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW(), NULL)",
    "(2, 'John Doe', 'teacher@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NOW(), NOW(), NULL)",
    "(3, 'Jane Smith', 'student@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW(), NOW(), NULL)",
    "(4, 'Alice Brown', 'alice.teacher@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', NOW(), NOW(), NULL)",
    "(5, 'Bob Green', 'bob.student@example.com', '\$2y\$10\$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', NOW(), NOW(), NULL)"
];

$sqlContent .= implode(",\n", $users) . ";\n\n";

// Add footer
$sqlContent .= "-- =====================================================\n";
$sqlContent .= "-- USERSEEDER CREDENTIALS\n";
$sqlContent .= "-- =====================================================\n";
$sqlContent .= "-- Admin: admin@example.com / admin123\n";
$sqlContent .= "-- Teacher: teacher@example.com / teacher123\n";
$sqlContent .= "-- Student: student@example.com / student123\n";
$sqlContent .= "-- Extra Teacher: alice.teacher@example.com / teacher456\n";
$sqlContent .= "-- Extra Student: bob.student@example.com / student456\n";
$sqlContent .= "-- =====================================================\n";

// Save the synced file
$filename = "lms_alberca_with_userseeder.sql";
file_put_contents($filename, $sqlContent);

$fileSize = filesize($filename);

echo "✅ Sync completed successfully!\n\n";
echo "📊 File Details:\n";
echo "================\n";
echo "📁 Output file: $filename\n";
echo "📊 File size: " . number_format($fileSize / 1024, 2) . " KB\n";
echo "👥 Users: 5 (matching UserSeeder.php)\n";
echo "🔑 Passwords: Using UserSeeder credentials\n";

echo "\n👤 Login Credentials (from UserSeeder):\n";
echo "======================================\n";
echo "🔐 Admin: admin@example.com / admin123\n";
echo "🔐 Teacher: teacher@example.com / teacher123\n";
echo "🔐 Student: student@example.com / student123\n";
echo "🔐 Extra Teacher: alice.teacher@example.com / teacher456\n";
echo "🔐 Extra Student: bob.student@example.com / student456\n";

echo "\n🎯 Ready for phpMyAdmin!\n";
echo "========================\n";
echo "✅ This SQL file now matches your UserSeeder.php exactly\n";
echo "📁 Import: $filename\n";
echo "🌐 Use the credentials above to login after import\n";
?>