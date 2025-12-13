<?php

namespace App\Controllers;

use App\Models\CourseModel;

class TestCourses extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();
        
        // Check if courses exist
        $courses = $courseModel->findAll();
        
        $output = "<h1>Course System Test</h1>";
        $output .= "<p>Total courses: " . count($courses) . "</p>";
        
        if (empty($courses)) {
            $output .= "<h2>Creating sample courses...</h2>";
            
            // Create sample courses
            $sampleCourses = [
                [
                    'title' => 'Bachelor of Science in Information Technology',
                    'description' => 'A comprehensive 4-year program focusing on information systems, database management, web development, and software engineering. Students will learn programming languages, system analysis, and project management.',
                    'instructor_id' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Bachelor of Science in Computer Science',
                    'description' => 'A rigorous 4-year program covering algorithms, data structures, software engineering, artificial intelligence, and computer systems. Prepares students for careers in software development and research.',
                    'instructor_id' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Web Development Fundamentals',
                    'description' => 'Learn the basics of web development including HTML5, CSS3, JavaScript, PHP, and MySQL. Build responsive websites and dynamic web applications.',
                    'instructor_id' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Database Management Systems',
                    'description' => 'Comprehensive course on database design, SQL, normalization, and database administration. Covers MySQL, PostgreSQL, and NoSQL databases.',
                    'instructor_id' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ],
                [
                    'title' => 'Mobile App Development',
                    'description' => 'Learn to develop mobile applications for Android and iOS platforms using modern frameworks and tools.',
                    'instructor_id' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]
            ];
            
            foreach ($sampleCourses as $course) {
                try {
                    if ($courseModel->insert($course)) {
                        $output .= "<p>✅ Created: " . $course['title'] . "</p>";
                    } else {
                        $output .= "<p>❌ Failed to create: " . $course['title'] . "</p>";
                        $output .= "<p>Errors: " . json_encode($courseModel->errors()) . "</p>";
                    }
                } catch (Exception $e) {
                    $output .= "<p>❌ Exception creating " . $course['title'] . ": " . $e->getMessage() . "</p>";
                }
            }
            
            $output .= "<p><a href='" . base_url('test-courses') . "'>Refresh to see courses</a></p>";
        } else {
            $output .= "<h2>Existing Courses:</h2><ul>";
            foreach ($courses as $course) {
                $output .= "<li>ID: {$course['id']} - {$course['title']}</li>";
            }
            $output .= "</ul>";
        }
        
        $output .= "<p><a href='" . base_url('enrollments') . "'>Go to Enrollment System</a></p>";
        $output .= "<p><a href='" . base_url('dashboard') . "'>Back to Dashboard</a></p>";
        
        return $output;
    }
}