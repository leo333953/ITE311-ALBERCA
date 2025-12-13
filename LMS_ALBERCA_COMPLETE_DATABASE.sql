-- =====================================================
-- LMS ALBERCA - COMPLETE DATABASE FILE
-- =====================================================
-- File: LMS_ALBERCA_COMPLETE_DATABASE.sql
-- Generated: 2025-12-13
-- Purpose: Single file containing entire database
-- Easy navigation with clear sections
-- Ready for phpMyAdmin import
-- =====================================================

-- =====================================================
-- SECTION 1: DATABASE SETUP
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";
SET FOREIGN_KEY_CHECKS = 0;

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Create and use database
CREATE DATABASE IF NOT EXISTS `lms_alberca` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lms_alberca`;

-- =====================================================
-- SECTION 2: TABLE STRUCTURES
-- =====================================================

-- -----------------------------------------------------
-- Table: users (User Management)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL DEFAULT 'student',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: courses (Course Management)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `instructor_id` (`instructor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: enrollments (Student Enrollment System)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `enrollments`;
CREATE TABLE `enrollments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
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
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `course_id` (`course_id`),
  KEY `approved_by` (`approved_by`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: lessons (Course Lessons)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `lessons`;
CREATE TABLE `lessons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: materials (Course Materials)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `materials`;
CREATE TABLE `materials` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) unsigned NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: notifications (System Notifications)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: quizzes (Quiz System)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `quizzes`;
CREATE TABLE `quizzes` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_id` (`course_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: submissions (Quiz Submissions)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `submissions`;
CREATE TABLE `submissions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `quiz_id` int(11) unsigned NOT NULL,
  `answers` text DEFAULT NULL,
  `score` decimal(5,2) DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `quiz_id` (`quiz_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- -----------------------------------------------------
-- Table: migrations (Database Version History)
-- -----------------------------------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- SECTION 3: SAMPLE DATA
-- =====================================================

-- -----------------------------------------------------
-- Data: users (5 users matching UserSeeder.php)
-- -----------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Admin', 'admin@example.com', '$2y$10$5O6yl2LVhWfXepd6oujZDeQ8s38kTbcqT86uWOWXZtIP8sPj7SfNC', 'admin', NOW(), NOW(), NULL),
(2, 'John Doe', 'teacher@example.com', '$2y$10$WZJwtViULbPeIHYvUzQs8OA2q/rYERgqvnuvEQ0mTft/EzSbXJHTy', 'teacher', NOW(), NOW(), NULL),
(3, 'Jane Smith', 'student@example.com', '$2y$10$ZvbSYXxUDIGDDFAFPKu5VeWcEBI35whcCGqFzVSKj4lDU03juXDNC', 'student', NOW(), NOW(), NULL),
(4, 'Alice Brown', 'alice.teacher@example.com', '$2y$10$ctMfzM7v1ec/ABV79wHiceff7AqcxMSQIz/sKljs6NhAuRnJqMTtW', 'teacher', NOW(), NOW(), NULL),
(5, 'Bob Green', 'bob.student@example.com', '$2y$10$uFhHpiIZu03pesdfkFx7qO7x1YPTokcw5LDuKgSva3KAjgEFfQNGu', 'student', NOW(), NOW(), NULL);

-- -----------------------------------------------------
-- Data: courses (Sample courses with new structure)
-- -----------------------------------------------------
INSERT INTO `courses` (`id`, `course_code`, `course_name`, `title`, `description`, `year_level`, `semester`, `academic_year`, `instructor_id`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CS101', 'Introduction to Programming', 'Introduction to Programming', 'Learn the basics of programming with hands-on exercises and projects.', '1st Year', '1st Semester', '2025-2026', 2, 'Active', NOW(), NOW(), NULL),
(2, 'WEB201', 'Web Development Fundamentals', 'Web Development Fundamentals', 'Master HTML, CSS, and JavaScript to build modern web applications.', '2nd Year', '1st Semester', '2025-2026', 2, 'Active', NOW(), NOW(), NULL),
(3, 'DB301', 'Database Management Systems', 'Database Management Systems', 'Comprehensive course on database design, SQL, and database administration.', '3rd Year', '2nd Semester', '2025-2026', 4, 'Active', NOW(), NOW(), NULL);

-- -----------------------------------------------------
-- Data: enrollments (Sample enrollment records)
-- -----------------------------------------------------
INSERT INTO `enrollments` (`id`, `user_id`, `course_id`, `enrollment_date`, `status`, `student_number`, `full_name`, `course_year`, `email`, `phone_number`, `address`, `date_of_birth`, `submitted_at`, `deleted_at`) VALUES
(1, 3, 1, NOW(), 'approved', 'STU-2025-001', 'Jane Smith', 'Bachelor of Science in Information Technology - 1st Year', 'student@example.com', '09123456789', '123 Student Street, City', '2000-01-15', NOW(), NULL),
(2, 5, 2, NOW(), 'pending', 'STU-2025-002', 'Bob Green', 'Bachelor of Science in Computer Science - 2nd Year', 'bob.student@example.com', '09987654321', '456 Learning Ave, Town', '1999-05-20', NOW(), NULL);

-- -----------------------------------------------------
-- Data: lessons (Sample lessons)
-- -----------------------------------------------------
INSERT INTO `lessons` (`id`, `course_id`, `title`, `content`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Variables and Data Types', 'Introduction to programming variables and basic data types.', NOW(), NOW(), NULL),
(2, 1, 'Control Structures', 'Learn about if statements, loops, and conditional logic.', NOW(), NOW(), NULL),
(3, 2, 'HTML Basics', 'Understanding HTML structure and semantic elements.', NOW(), NOW(), NULL);

-- -----------------------------------------------------
-- Data: notifications (Sample notifications)
-- -----------------------------------------------------
INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 'Welcome to LMS Alberca! Your admin account has been set up successfully.', 0, NOW()),
(2, 2, 'Welcome to LMS Alberca! Your teacher account is ready to use.', 0, NOW()),
(3, 1, 'New enrollment application submitted by Jane Smith for Introduction to Programming', 0, NOW());

-- -----------------------------------------------------
-- Data: quizzes (Sample quizzes)
-- -----------------------------------------------------
INSERT INTO `quizzes` (`id`, `course_id`, `title`, `description`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Programming Basics Quiz', 'Test your knowledge of basic programming concepts.', NOW(), NOW(), NULL),
(2, 2, 'HTML & CSS Assessment', 'Evaluate your understanding of web development fundamentals.', NOW(), NOW(), NULL);

-- -----------------------------------------------------
-- Data: migrations (Database version history)
-- -----------------------------------------------------
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

-- =====================================================
-- SECTION 4: AUTO_INCREMENT SETTINGS
-- =====================================================

ALTER TABLE `users` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `courses` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `enrollments` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `lessons` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `materials` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `notifications` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `quizzes` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `submissions` MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
ALTER TABLE `migrations` MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

-- =====================================================
-- SECTION 5: FOREIGN KEY CONSTRAINTS
-- =====================================================

ALTER TABLE `courses` ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`instructor_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
ALTER TABLE `enrollments` ADD CONSTRAINT `enrollments_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
ALTER TABLE `lessons` ADD CONSTRAINT `lessons_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
ALTER TABLE `materials` ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
ALTER TABLE `notifications` ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `quizzes` ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE;
ALTER TABLE `submissions` ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
ALTER TABLE `submissions` ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

-- =====================================================
-- SECTION 6: FINALIZATION
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- =====================================================
-- SECTION 7: SYSTEM INFORMATION
-- =====================================================

-- DATABASE SUMMARY:
-- - Database: lms_alberca
-- - Tables: 9 (users, courses, enrollments, lessons, materials, notifications, quizzes, submissions, migrations)
-- - Users: 5 (1 admin, 2 teachers, 2 students)
-- - Courses: 3 sample courses with new structure
-- - Enrollments: 2 sample enrollment records
-- - Features: Complete LMS with enrollment, notifications, soft delete

-- LOGIN CREDENTIALS (UserSeeder.php):
-- Admin: admin@example.com / admin123
-- Teacher: teacher@example.com / teacher123
-- Student: student@example.com / student123
-- Extra Teacher: alice.teacher@example.com / teacher456
-- Extra Student: bob.student@example.com / student456

-- IMPORT INSTRUCTIONS:
-- 1. Open phpMyAdmin (http://localhost/phpmyadmin)
-- 2. Click 'Import' tab
-- 3. Choose this file: LMS_ALBERCA_COMPLETE_DATABASE.sql
-- 4. Click 'Go' button
-- 5. Test login: http://localhost/ITE311-ALBERCA/login

-- =====================================================
-- END OF FILE
-- =====================================================