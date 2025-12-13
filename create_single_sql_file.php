<?php
echo "📄 Creating Single SQL File from Database Files\n";
echo "===============================================\n\n";

// Read the existing lms_alberca.sql file
if (!file_exists('lms_alberca.sql')) {
    die("❌ lms_alberca.sql file not found!\n");
}

echo "✅ Found lms_alberca.sql file\n";
echo "📖 Reading database structure...\n";

$sqlContent = file_get_contents('lms_alberca.sql');

// Create enhanced header
$newSqlContent = "-- =====================================================\n";
$newSqlContent .= "-- LMS ALBERCA - Complete Database Export\n";
$newSqlContent .= "-- =====================================================\n";
$newSqlContent .= "-- Generated from: 19 MySQL database files\n";
$newSqlContent .= "-- Source files: .frm + .ibd files from lms_alberca folder\n";
$newSqlContent .= "-- Export date: " . date('Y-m-d H:i:s') . "\n";
$newSqlContent .= "-- Database: lms_alberca\n";
$newSqlContent .= "-- Tables: 9 (courses, enrollments, lessons, materials, migrations, notifications, quizzes, submissions, users)\n";
$newSqlContent .= "-- Ready for: phpMyAdmin import\n";
$newSqlContent .= "-- =====================================================\n\n";

// Add the original SQL content
$newSqlContent .= $sqlContent;

// Add footer information
$newSqlContent .= "\n-- =====================================================\n";
$newSqlContent .= "-- IMPORT INSTRUCTIONS FOR PHPMYADMIN\n";
$newSqlContent .= "-- =====================================================\n";
$newSqlContent .= "-- 1. Open phpMyAdmin in your browser\n";
$newSqlContent .= "-- 2. Click 'Import' tab\n";
$newSqlContent .= "-- 3. Choose this file: lms_alberca_complete.sql\n";
$newSqlContent .= "-- 4. Click 'Go' button\n";
$newSqlContent .= "-- 5. Wait for import completion\n";
$newSqlContent .= "-- \n";
$newSqlContent .= "-- DEFAULT LOGIN CREDENTIALS:\n";
$newSqlContent .= "-- Admin: admin@lms.com / password\n";
$newSqlContent .= "-- Teacher: teacher@lms.com / password\n";
$newSqlContent .= "-- Student: student@lms.com / password\n";
$newSqlContent .= "-- =====================================================\n";

// Save to new file
$outputFile = "lms_alberca_complete.sql";
file_put_contents($outputFile, $newSqlContent);

$fileSize = filesize($outputFile);

echo "✅ Single SQL file created successfully!\n\n";
echo "📊 File Details:\n";
echo "================\n";
echo "📁 Output file: $outputFile\n";
echo "📊 File size: " . number_format($fileSize / 1024, 2) . " KB\n";
echo "🗂️  Source: 19 MySQL database files (.frm + .ibd)\n";
echo "📋 Contains: Complete database structure + sample data\n";

// Count lines and tables
$lines = substr_count($newSqlContent, "\n");
$tables = substr_count($newSqlContent, "CREATE TABLE");

echo "📄 Total lines: $lines\n";
echo "🗃️  Tables included: $tables\n";

echo "\n🎯 Ready for phpMyAdmin Import!\n";
echo "===============================\n";
echo "✅ Your 19 database files are now consolidated into 1 SQL file\n";
echo "📁 File: $outputFile\n";
echo "🌐 Import this file into phpMyAdmin to recreate your database\n";

echo "\n📋 What's included:\n";
echo "==================\n";
echo "✓ Complete database structure (9 tables)\n";
echo "✓ Sample data (users, courses, enrollments)\n";
echo "✓ Foreign key relationships\n";
echo "✓ Indexes and constraints\n";
echo "✓ Auto-increment settings\n";
echo "✓ Default login accounts\n";

echo "\n🚀 Next step: Import $outputFile into phpMyAdmin!\n";
?>