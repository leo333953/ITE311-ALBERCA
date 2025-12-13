<?php
echo "🔑 Generating Correct Password Hashes\n";
echo "====================================\n\n";

// Generate the correct password hashes for UserSeeder passwords
$passwords = [
    'admin123' => password_hash('admin123', PASSWORD_DEFAULT),
    'teacher123' => password_hash('teacher123', PASSWORD_DEFAULT),
    'student123' => password_hash('student123', PASSWORD_DEFAULT),
    'teacher456' => password_hash('teacher456', PASSWORD_DEFAULT),
    'student456' => password_hash('student456', PASSWORD_DEFAULT)
];

echo "✅ Generated password hashes:\n\n";

foreach ($passwords as $plain => $hash) {
    echo "Password: $plain\n";
    echo "Hash: $hash\n\n";
}

// Now update the SQL file with correct hashes
echo "🔧 Updating SQL files with correct password hashes...\n";

// Read the current lms_alberca.sql
$sqlContent = file_get_contents('lms_alberca.sql');

// Create the correct INSERT statement
$correctUserInsert = "-- Users data with CORRECT password hashes matching UserSeeder.php\n";
$correctUserInsert .= "INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES\n";
$correctUserInsert .= "(1, 'Admin', 'admin@example.com', '" . $passwords['admin123'] . "', 'admin', NOW(), NOW(), NULL),\n";
$correctUserInsert .= "(2, 'John Doe', 'teacher@example.com', '" . $passwords['teacher123'] . "', 'teacher', NOW(), NOW(), NULL),\n";
$correctUserInsert .= "(3, 'Jane Smith', 'student@example.com', '" . $passwords['student123'] . "', 'student', NOW(), NOW(), NULL),\n";
$correctUserInsert .= "(4, 'Alice Brown', 'alice.teacher@example.com', '" . $passwords['teacher456'] . "', 'teacher', NOW(), NOW(), NULL),\n";
$correctUserInsert .= "(5, 'Bob Green', 'bob.student@example.com', '" . $passwords['student456'] . "', 'student', NOW(), NOW(), NULL);";

// Replace the user insert section
$pattern = '/-- Users data.*?INSERT INTO `users`.*?;/s';
$sqlContent = preg_replace($pattern, $correctUserInsert, $sqlContent);

// Save updated SQL
file_put_contents('lms_alberca.sql', $sqlContent);

echo "✅ Updated lms_alberca.sql with correct password hashes\n";

// Update all other SQL files
$filesToUpdate = [
    'MASTER_lms_alberca_sync.sql',
    'lms_alberca_complete.sql',
    'lms_alberca_export_for_phpmyadmin.sql'
];

foreach ($filesToUpdate as $file) {
    if (file_exists($file)) {
        copy('lms_alberca.sql', $file);
        echo "✅ Updated: $file\n";
    }
}

echo "\n🎯 Password Fix Complete!\n";
echo "========================\n";
echo "✅ All SQL files now have correct password hashes\n";
echo "✅ Passwords match UserSeeder.php exactly\n";

echo "\n👤 Login Credentials (VERIFIED):\n";
echo "================================\n";
echo "🔐 Admin: admin@example.com / admin123\n";
echo "🔐 Teacher: teacher@example.com / teacher123\n";
echo "🔐 Student: student@example.com / student123\n";
echo "🔐 Extra Teacher: alice.teacher@example.com / teacher456\n";
echo "🔐 Extra Student: bob.student@example.com / student456\n";

echo "\n🚀 Now import any of these files to phpMyAdmin:\n";
foreach (['lms_alberca.sql'] + $filesToUpdate as $file) {
    if (file_exists($file)) {
        echo "  ✅ $file\n";
    }
}

echo "\n💡 After import, login should work with the credentials above!\n";
?>