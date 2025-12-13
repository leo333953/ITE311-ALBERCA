-- Migration to add new course fields to existing lms_alberca database
USE lms_alberca;

-- Add new columns to courses table
ALTER TABLE courses 
ADD COLUMN course_code VARCHAR(20) NOT NULL DEFAULT '' AFTER id,
ADD COLUMN course_name VARCHAR(255) NOT NULL DEFAULT '' AFTER course_code,
ADD COLUMN year_level ENUM('1st Year','2nd Year','3rd Year','4th Year') NOT NULL DEFAULT '1st Year' AFTER description,
ADD COLUMN semester ENUM('1st Semester','2nd Semester','Summer') NOT NULL DEFAULT '1st Semester' AFTER year_level,
ADD COLUMN academic_year VARCHAR(20) NOT NULL DEFAULT '2025-2026' AFTER semester,
ADD COLUMN status ENUM('Active','Inactive','Archived') NOT NULL DEFAULT 'Active' AFTER instructor_id;

-- Update existing courses with sample data
UPDATE courses SET 
    course_code = CASE 
        WHEN id = 1 THEN 'CS101'
        WHEN id = 2 THEN 'WEB201' 
        WHEN id = 3 THEN 'DB301'
        ELSE CONCAT('COURSE', id)
    END,
    course_name = title,
    year_level = CASE 
        WHEN id = 1 THEN '1st Year'
        WHEN id = 2 THEN '2nd Year'
        WHEN id = 3 THEN '3rd Year'
        ELSE '1st Year'
    END,
    semester = CASE 
        WHEN id = 3 THEN '2nd Semester'
        ELSE '1st Semester'
    END,
    academic_year = '2025-2026',
    status = 'Active'
WHERE id IN (1, 2, 3);

-- Add unique constraint for course_code
ALTER TABLE courses ADD UNIQUE KEY unique_course_code (course_code);