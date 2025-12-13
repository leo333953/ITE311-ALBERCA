-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 13, 2025 at 02:00 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms_alberca`
--
CREATE DATABASE IF NOT EXISTS `lms_alberca` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lms_alberca`;

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) unsigned NOT NULL,
  `course_code` varchar(20) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `year_level` enum('1st Year','2nd Year','3rd Year','4th Year') NOT NULL DEFAULT '1st Year',
  `semester` enum('1st Semester','2nd Semester','Summer') NOT NULL DEFAULT '1st Semester',
  `academic_year` varchar(20) NOT NULL DEFAULT '2025-2026',
  `instructor_id` int(11) unsigned DEFAULT NULL,
  `status` enum('Active','Inactive','Archived') NOT NULL DEFAULT 'Active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course_code`, `course_name`, `title`, `description`, `year_level`, `semester`, `academic_year`, `instructor_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CS101', 'Introduction to Programming', 'Introduction to Programming', 'Learn the basics of programming with hands-on exercises and projects.', '1st Year', '1st Semester', '2025-2026', 2, 'Active', NOW(), NOW(), NULL),
(2, 'WEB201', 'Web Development Fundamentals', 'Web Development Fundamentals', 'Master HTML, CSS, and JavaScript to build modern web applications.', '2nd Year', '1st Semester', '2025-2026', 2, 'Active', NOW(), NOW(), NULL),
(3, 'DB301', 'Database Management Systems', 'Database Management Systems', 'Comprehensive course on database design, SQL, and database administration.', '3rd Year', '2nd Semester', '2025-2026', 2, 'Active', NOW(), NOW(), NULL);

-- --------------------------------------------------------

--
-- Table structure for table `enrollments`
--

CREATE TABLE `enrollments` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `enrollment_date` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `student_info` text DEFAULT NULL,
  `student_details_id` int(11) DEFAULT NULL,
  `approved_by` int(11) unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `student_number` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `course_year` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `guardian_name` varchar(255) DEFAULT NULL,
  `guardian_phone` varchar(20) DEFAULT NULL,
  `guardian_email` varchar(255) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollments`
--

INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `enrollment_date`, `status`, `student_number`, `full_name`, `course_year`, `email`, `phone_number`, `address`, `date_of_birth`, `submitted_at`, `deleted_at`) VALUES
(1, 3, 1, NOW(), 'approved', 'STU-2025-001', 'Student Demo', 'Bachelor of Science in Information Technology - 1st Year', 'student@lms.com', '09123456789', '123 Student Street, City', '2000-01-15', NOW(), NULL),
(2, 3, 2, NOW(), 'pending', 'STU-2025-002', 'Jane Smith', 'Bachelor of Science in Computer Science - 2nd Year', 'jane.smith@email.com', '09987654321', '456 Learning Ave, Town', '1999-05-20', NOW(), NULL);

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lessons`
--

INSERT INTO `lessons` (`id`, `course_id`, `title`, `content`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Variables and Data Types', 'Introduction to programming variables and basic data types.', NOW(), NOW(), NULL),
(2, 1, 'Control Structures', 'Learn about if statements, loops, and conditional logic.', NOW(), NOW(), NULL),
(3, 2, 'HTML Basics', 'Understanding HTML structure and semantic elements.', NOW(), NOW(), NULL);

-- --------------------------------------------------------

--
-- Table structure for table `materials`
--

CREATE TABLE `materials` (
  `id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2025-09-05-201314', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(2, '2025-09-05-201712', 'App\\Database\\Migrations\\CreateCoursesTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(3, '2025-09-05-202240', 'App\\Database\\Migrations\\CreateEnrollmentsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(4, '2025-09-05-202335', 'App\\Database\\Migrations\\CreateLessonsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(5, '2025-09-05-202417', 'App\\Database\\Migrations\\CreateQuizzesTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(6, '2025-09-05-202453', 'App\\Database\\Migrations\\CreateSubmissionTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(7, '2025-10-27-051022', 'App\\Database\\Migrations\\CreateMaterialsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(8, '2025-12-03-055323', 'App\\Database\\Migrations\\CreateNotificationsTable', 'default', 'App', UNIX_TIMESTAMP(), 1),
(9, '2025-12-12-150000', 'App\\Database\\Migrations\\AddEnrollmentSystemFields', 'default', 'App', UNIX_TIMESTAMP(), 2),
(10, '2025-12-12-160000', 'App\\Database\\Migrations\\AddMissingEnrollmentFields', 'default', 'App', UNIX_TIMESTAMP(), 3),
(11, '2025-12-12-170000', 'App\\Database\\Migrations\\FixCoursesInstructorId', 'default', 'App', UNIX_TIMESTAMP(), 4),
(12, '2025-12-12-180000', 'App\\Database\\Migrations\\AddSoftDeleteColumns', 'default', 'App', UNIX_TIMESTAMP(), 5);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Welcome to LMS Alberca! Your admin account has been set up successfully.', 0, NOW()),
(2, 2, 'Welcome to LMS Alberca! Your teacher account is ready to use.', 0, NOW()),
(3, 1, 'New enrollment application submitted by Student Demo for Bachelor of Science in Information Technology', 0, NOW());

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) unsigned NOT NULL,
  `course_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `course_id`, `title`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Programming Basics Quiz', 'Test your knowledge of basic programming concepts.', NOW(), NOW(), NULL),
(2, 2, 'HTML & CSS Assessment', 'Evaluate your understanding of web development fundamentals.', NOW(), NOW(), NULL);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `quiz_id` int(11) unsigned NOT NULL,
  `answers` text DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL DEFAULT 'student',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

-- Users data with CORRECT password hashes matching UserSeeder.php
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin@example.com', 'yO6yl2LVhWfXepd6oujZDeQ8s38kTbcqT86uWOWXZtIP8sPj7SfNC', 'admin', NOW(), NOW(), NULL),
(2, 'John Doe', 'teacher@example.com', 'y$WZJwtViULbPeIHYvUzQs8OA2q/rYERgqvnuvEQ0mTft/EzSbXJHTy', 'teacher', NOW(), NOW(), NULL),
(3, 'Jane Smith', 'student@example.com', 'y$ZvbSYXxUDIGDDFAFPKu5VeWcEBI35whcCGqFzVSKj4lDU03juXDNC', 'student', NOW(), NOW(), NULL),
(4, 'Alice Brown', 'alice.teacher@example.com', 'y$ctMfzM7v1ec/ABV79wHiceff7AqcxMSQIz/sKljs6NhAuRnJqMTtW', 'teacher', NOW(), NOW(), NULL),
(5, 'Bob Green', 'bob.student@example.com', 'y$uFhHpiIZu03pesdfkFx7qO7x1YPTokcw5LDuKgSva3KAjgEFfQNGu', 'student', NOW(), NOW(), NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `instructor_id` (`instructor_id`);

--
-- Indexes for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `materials`
--
ALTER TABLE `materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollments`
--
ALTER TABLE `enrollments`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `materials`
--
ALTER TABLE `materials`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `enrollments`
--
ALTER TABLE `enrollments`
  ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `lessons`
--
ALTER TABLE `lessons`
  ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =====================================================
-- LOGIN CREDENTIALS (from UserSeeder.php)
-- =====================================================
-- Admin: admin@example.com / admin123
-- Teacher: teacher@example.com / teacher123  
-- Student: student@example.com / student123
-- Extra Teacher: alice.teacher@example.com / teacher456
-- Extra Student: bob.student@example.com / student456
-- =====================================================