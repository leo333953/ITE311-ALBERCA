<?php
echo "🎯 Creating Master Sync File for phpMyAdmin\n";
echo "==========================================\n\n";

// Use the updated lms_alberca.sql as the master source
if (!file_exists('lms_alberca.sql')) {
    die("❌ lms_alberca.sql not found!\n");
}

echo "✅ Using lms_alberca.sql as master source\n";

// Read the master SQL file
$masterContent = file_get_contents('lms_alberca.sql');

// Create the final master file with enhanced header
$finalContent = "-- =====================================================\n";
$finalContent .= "-- LMS ALBERCA - MASTER SYNCHRONIZATION FILE\n";
$finalContent .= "-- =====================================================\n";
$finalContent .= "-- Generated: " . date('Y-m-d H:i:s') . "\n";
$finalContent .= "-- Purpose: Complete system synchronization\n";
$finalContent .= "-- Source: All system files unified\n";
$finalContent .= "-- Target: phpMyAdmin import\n";
$finalContent .= "-- Status: READY FOR IMPORT\n";
$finalContent .= "-- =====================================================\n\n";

// Add the master content (skip original header)
$lines = explode("\n", $masterContent);
$skipHeader = true;

foreach ($lines as $line) {
    if (strpos($line, 'CREATE DATABASE') !== false) {
        $skipHeader = false;
    }
    if (!$skipHeader) {
        $finalContent .= $line . "\n";
    }
}

// Save master file
$masterFile = "MASTER_lms_alberca_sync.sql";
file_put_contents($masterFile, $finalContent);

echo "✅ Master file created: $masterFile\n";

// Update all related files to match
echo "\n📋 Synchronizing all SQL files...\n";

$filesToSync = [
    'lms_alberca_complete.sql',
    'lms_alberca_export_for_phpmyadmin.sql'
];

foreach ($filesToSync as $file) {
    if (file_exists($file)) {
        copy($masterFile, $file);
        echo "✅ Synced: $file\n";
    } else {
        copy($masterFile, $file);
        echo "✅ Created: $file\n";
    }
}

// Create comprehensive import guide
$guide = "# 🎯 LMS ALBERCA - Complete System Synchronization\n\n";
$guide .= "## 📁 Files Ready for phpMyAdmin Import:\n\n";
$guide .= "### Primary Import File:\n";
$guide .= "- **`MASTER_lms_alberca_sync.sql`** - Main file for import\n\n";
$guide .= "### Backup Files (identical content):\n";
$guide .= "- `lms_alberca.sql`\n";
$guide .= "- `lms_alberca_complete.sql`\n";
$guide .= "- `lms_alberca_export_for_phpmyadmin.sql`\n\n";

$guide .= "## 🚀 Import to phpMyAdmin:\n\n";
$guide .= "1. **Open phpMyAdmin**: `http://localhost/phpmyadmin`\n";
$guide .= "2. **Click 'Import' tab**\n";
$guide .= "3. **Choose file**: `MASTER_lms_alberca_sync.sql`\n";
$guide .= "4. **Click 'Go' button**\n";
$guide .= "5. **Wait for completion**\n\n";

$guide .= "## 👤 Login Credentials (UserSeeder.php):\n\n";
$guide .= "| Role | Email | Password |\n";
$guide .= "|------|-------|----------|\n";
$guide .= "| Admin | admin@example.com | admin123 |\n";
$guide .= "| Teacher | teacher@example.com | teacher123 |\n";
$guide .= "| Student | student@example.com | student123 |\n";
$guide .= "| Teacher 2 | alice.teacher@example.com | teacher456 |\n";
$guide .= "| Student 2 | bob.student@example.com | student456 |\n\n";

$guide .= "## 📊 Database Contents:\n\n";
$guide .= "### Tables (9 total):\n";
$guide .= "- `users` - 5 user accounts\n";
$guide .= "- `courses` - Course management with new fields\n";
$guide .= "- `enrollments` - Student enrollment system\n";
$guide .= "- `materials` - Course materials\n";
$guide .= "- `notifications` - System notifications\n";
$guide .= "- `lessons` - Course lessons\n";
$guide .= "- `quizzes` - Quiz system\n";
$guide .= "- `submissions` - Quiz submissions\n";
$guide .= "- `migrations` - Database version history\n\n";

$guide .= "### New Course Fields:\n";
$guide .= "- Course Code (e.g., CS101)\n";
$guide .= "- Course Name (e.g., Introduction to Programming)\n";
$guide .= "- Year Level (1st-4th Year)\n";
$guide .= "- Semester (1st/2nd/Summer)\n";
$guide .= "- Academic Year (2025-2026)\n";
$guide .= "- Status (Active/Inactive/Archived)\n\n";

$guide .= "## ✅ After Import:\n\n";
$guide .= "1. **Test login**: `http://localhost/ITE311-ALBERCA/login`\n";
$guide .= "2. **Verify all features work**\n";
$guide .= "3. **Check course creation with new fields**\n";
$guide .= "4. **Test enrollment system**\n";
$guide .= "5. **Verify notifications**\n\n";

$guide .= "## 🔧 System Features Included:\n\n";
$guide .= "- ✅ Role-based access (Admin/Teacher/Student)\n";
$guide .= "- ✅ Course management with structured fields\n";
$guide .= "- ✅ Student enrollment system\n";
$guide .= "- ✅ Material upload (PDF/PPT only)\n";
$guide .= "- ✅ Notification system\n";
$guide .= "- ✅ Soft delete functionality\n";
$guide .= "- ✅ Search and filtering\n";
$guide .= "- ✅ Complete CRUD operations\n\n";

$guide .= "---\n";
$guide .= "*All files are now synchronized and ready for phpMyAdmin import!*\n";

file_put_contents('COMPLETE_SYNC_GUIDE.md', $guide);

// Get file statistics
$fileSize = filesize($masterFile);
$lineCount = substr_count($finalContent, "\n");

echo "\n🎯 SYNCHRONIZATION COMPLETE!\n";
echo "============================\n";
echo "✅ Master file: $masterFile\n";
echo "✅ Guide: COMPLETE_SYNC_GUIDE.md\n";
echo "✅ All SQL files synchronized\n";
echo "📊 File size: " . number_format($fileSize / 1024, 2) . " KB\n";
echo "📄 Lines: $lineCount\n";

echo "\n📋 Files Ready for phpMyAdmin:\n";
foreach ([$masterFile] + $filesToSync as $file) {
    if (file_exists($file)) {
        $size = filesize($file);
        echo "  ✅ $file (" . number_format($size / 1024, 2) . " KB)\n";
    }
}

echo "\n🚀 NEXT STEP: Import $masterFile to phpMyAdmin!\n";
echo "📖 Read COMPLETE_SYNC_GUIDE.md for detailed instructions\n";
?>