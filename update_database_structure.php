<?php
echo "🔧 Database Structure Update Script\n";
echo "===================================\n\n";

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'lms_alberca';

try {
    $mysqli = new mysqli($host, $username, $password, $database);
    
    if ($mysqli->connect_error) {
        die("❌ Connection failed: " . $mysqli->connect_error . "\n");
    }
    
    echo "✅ Connected to lms_alberca database\n\n";
    
    // Check current course table structure
    echo "📋 Current course table structure:\n";
    $result = $mysqli->query("DESCRIBE courses");
    $existingFields = [];
    while ($row = $result->fetch_assoc()) {
        $existingFields[] = $row['Field'];
        echo "  - " . $row['Field'] . "\n";
    }
    
    echo "\n🔧 Adding missing fields to courses table...\n";
    
    // Add new fields if they don't exist
    $newFields = [
        'course_code' => "VARCHAR(20) NOT NULL DEFAULT '' AFTER id",
        'course_name' => "VARCHAR(255) NOT NULL DEFAULT '' AFTER course_code", 
        'year_level' => "ENUM('1st Year','2nd Year','3rd Year','4th Year') NOT NULL DEFAULT '1st Year' AFTER description",
        'semester' => "ENUM('1st Semester','2nd Semester','Summer') NOT NULL DEFAULT '1st Semester' AFTER year_level",
        'academic_year' => "VARCHAR(20) NOT NULL DEFAULT '2025-2026' AFTER semester",
        'status' => "ENUM('Active','Inactive','Archived') NOT NULL DEFAULT 'Active' AFTER instructor_id"
    ];
    
    foreach ($newFields as $fieldName => $fieldDefinition) {
        if (!in_array($fieldName, $existingFields)) {
            $sql = "ALTER TABLE courses ADD COLUMN $fieldName $fieldDefinition";
            if ($mysqli->query($sql)) {
                echo "✅ Added field: $fieldName\n";
            } else {
                echo "❌ Failed to add field $fieldName: " . $mysqli->error . "\n";
            }
        } else {
            echo "⚠️  Field $fieldName already exists\n";
        }
    }
    
    echo "\n📊 Updating existing courses with sample data...\n";
    
    // Check if there are any courses
    $courseCount = $mysqli->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc();
    
    if ($courseCount['count'] == 0) {
        echo "📝 No existing courses found. Adding sample courses...\n";
        
        $sampleCourses = [
            [
                'course_code' => 'CS101',
                'course_name' => 'Introduction to Programming',
                'title' => 'Introduction to Programming',
                'description' => 'Learn the basics of programming with hands-on exercises and projects.',
                'year_level' => '1st Year',
                'semester' => '1st Semester',
                'academic_year' => '2025-2026',
                'instructor_id' => 2,
                'status' => 'Active'
            ],
            [
                'course_code' => 'WEB201',
                'course_name' => 'Web Development Fundamentals', 
                'title' => 'Web Development Fundamentals',
                'description' => 'Master HTML, CSS, and JavaScript to build modern web applications.',
                'year_level' => '2nd Year',
                'semester' => '1st Semester',
                'academic_year' => '2025-2026',
                'instructor_id' => 2,
                'status' => 'Active'
            ],
            [
                'course_code' => 'DB301',
                'course_name' => 'Database Management Systems',
                'title' => 'Database Management Systems', 
                'description' => 'Comprehensive course on database design, SQL, and database administration.',
                'year_level' => '3rd Year',
                'semester' => '2nd Semester',
                'academic_year' => '2025-2026',
                'instructor_id' => 2,
                'status' => 'Active'
            ]
        ];
        
        foreach ($sampleCourses as $course) {
            $sql = "INSERT INTO courses (course_code, course_name, title, description, year_level, semester, academic_year, instructor_id, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $stmt = $mysqli->prepare($sql);
            $stmt->bind_param('sssssssss', 
                $course['course_code'],
                $course['course_name'], 
                $course['title'],
                $course['description'],
                $course['year_level'],
                $course['semester'],
                $course['academic_year'],
                $course['instructor_id'],
                $course['status']
            );
            
            if ($stmt->execute()) {
                echo "✅ Added course: " . $course['course_code'] . " - " . $course['course_name'] . "\n";
            } else {
                echo "❌ Failed to add course: " . $mysqli->error . "\n";
            }
        }
    } else {
        echo "📝 Found {$courseCount['count']} existing courses. Updating them...\n";
        
        // Update existing courses with default values
        $updateSql = "UPDATE courses SET 
            course_code = CASE 
                WHEN course_code = '' OR course_code IS NULL THEN CONCAT('COURSE', id)
                ELSE course_code 
            END,
            course_name = CASE 
                WHEN course_name = '' OR course_name IS NULL THEN title
                ELSE course_name 
            END,
            year_level = CASE 
                WHEN year_level = '' OR year_level IS NULL THEN '1st Year'
                ELSE year_level 
            END,
            semester = CASE 
                WHEN semester = '' OR semester IS NULL THEN '1st Semester'
                ELSE semester 
            END,
            academic_year = CASE 
                WHEN academic_year = '' OR academic_year IS NULL THEN '2025-2026'
                ELSE academic_year 
            END,
            status = CASE 
                WHEN status = '' OR status IS NULL THEN 'Active'
                ELSE status 
            END";
            
        if ($mysqli->query($updateSql)) {
            echo "✅ Updated existing courses with default values\n";
        } else {
            echo "❌ Failed to update courses: " . $mysqli->error . "\n";
        }
    }
    
    // Add unique constraint for course_code
    echo "\n🔧 Adding unique constraint for course_code...\n";
    $constraintSql = "ALTER TABLE courses ADD UNIQUE KEY unique_course_code (course_code)";
    if ($mysqli->query($constraintSql)) {
        echo "✅ Added unique constraint for course_code\n";
    } else {
        if (strpos($mysqli->error, 'Duplicate key name') !== false) {
            echo "⚠️  Unique constraint already exists\n";
        } else {
            echo "❌ Failed to add constraint: " . $mysqli->error . "\n";
        }
    }
    
    echo "\n📋 Final course table structure:\n";
    $result = $mysqli->query("DESCRIBE courses");
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    echo "\n📊 Final course count:\n";
    $finalCount = $mysqli->query("SELECT COUNT(*) as count FROM courses")->fetch_assoc();
    echo "  Total courses: " . $finalCount['count'] . "\n";
    
    if ($finalCount['count'] > 0) {
        echo "\n📋 Sample courses:\n";
        $courses = $mysqli->query("SELECT course_code, course_name, year_level, semester FROM courses LIMIT 3");
        while ($course = $courses->fetch_assoc()) {
            echo "  - [" . $course['course_code'] . "] " . $course['course_name'] . " (" . $course['year_level'] . ", " . $course['semester'] . ")\n";
        }
    }
    
    $mysqli->close();
    
    echo "\n✅ Database structure update completed!\n";
    echo "\n🎯 Your database is now ready!\n";
    echo "🌐 Test your application: http://localhost/ITE311-ALBERCA/login\n";
    echo "👤 Login credentials:\n";
    echo "   - Admin: admin@lms.com / password\n";
    echo "   - Teacher: teacher@lms.com / password\n";
    echo "   - Student: student@lms.com / password\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>