<?php

namespace App\Controllers;

use App\Models\CourseModel;

class TestCourseCreate extends BaseController
{
    public function index()
    {
        // Simulate admin session
        session()->set([
            'isLoggedIn' => true,
            'role' => 'admin',
            'user_id' => 1
        ]);
        
        $output = "<h1>Course Creation Test</h1>";
        $output .= "<style>body { font-family: Arial, sans-serif; margin: 20px; } .success { color: green; } .error { color: red; }</style>";
        
        // Test form
        $output .= "<h2>Test Course Creation</h2>";
        $output .= "<form method='post' action='" . base_url('test-course-create/submit') . "'>";
        $output .= "<p><label>Title: <input type='text' name='title' value='Test Course " . date('H:i:s') . "' required></label></p>";
        $output .= "<p><label>Description: <textarea name='description' required>This is a test course created at " . date('Y-m-d H:i:s') . "</textarea></label></p>";
        $output .= "<p><label>Instructor ID (optional): <input type='number' name='instructor_id' placeholder='Leave empty'></label></p>";
        $output .= "<p><button type='submit'>Create Test Course</button></p>";
        $output .= "</form>";
        
        // Show existing courses
        try {
            $courseModel = new CourseModel();
            $courses = $courseModel->findAll();
            $output .= "<h2>Existing Courses (" . count($courses) . ")</h2>";
            $output .= "<ul>";
            foreach ($courses as $course) {
                $output .= "<li>ID: {$course['id']} - {$course['title']} (Instructor: " . ($course['instructor_id'] ?? 'None') . ")</li>";
            }
            $output .= "</ul>";
        } catch (\Exception $e) {
            $output .= "<p class='error'>Error loading courses: " . $e->getMessage() . "</p>";
        }
        
        $output .= "<p><a href='" . base_url('courses') . "'>Go to Admin Course Catalog</a></p>";
        
        return $output;
    }
    
    public function submit()
    {
        // Simulate admin session
        session()->set([
            'isLoggedIn' => true,
            'role' => 'admin',
            'user_id' => 1
        ]);
        
        $output = "<h1>Course Creation Test Result</h1>";
        $output .= "<style>body { font-family: Arial, sans-serif; margin: 20px; } .success { color: green; } .error { color: red; }</style>";
        
        try {
            $courseModel = new CourseModel();
            $data = [
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'instructor_id' => $this->request->getPost('instructor_id') ?: null
            ];
            
            $output .= "<h2>Data to Insert:</h2>";
            $output .= "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
            
            if ($courseModel->insert($data)) {
                $insertId = $courseModel->getInsertID();
                $output .= "<p class='success'>✅ Course created successfully! ID: $insertId</p>";
                
                // Verify the course was created
                $newCourse = $courseModel->find($insertId);
                $output .= "<h3>Created Course:</h3>";
                $output .= "<pre>" . json_encode($newCourse, JSON_PRETTY_PRINT) . "</pre>";
            } else {
                $errors = $courseModel->errors();
                $output .= "<p class='error'>❌ Failed to create course</p>";
                $output .= "<p>Errors: " . json_encode($errors) . "</p>";
            }
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ Exception: " . $e->getMessage() . "</p>";
        }
        
        $output .= "<p><a href='" . base_url('test-course-create') . "'>Back to Test</a></p>";
        $output .= "<p><a href='" . base_url('courses') . "'>Go to Admin Course Catalog</a></p>";
        
        return $output;
    }
    
    // Test AJAX course creation like the admin interface
    public function testAjax()
    {
        // Simulate admin session
        session()->set([
            'isLoggedIn' => true,
            'role' => 'admin',
            'user_id' => 1
        ]);
        
        // Simulate AJAX POST data
        $_POST['title'] = 'AJAX Test Course ' . date('H:i:s');
        $_POST['description'] = 'This is an AJAX test course created at ' . date('Y-m-d H:i:s');
        $_POST['instructor_id'] = '';
        
        // Call the actual Course controller create method
        $courseController = new \App\Controllers\Course();
        $response = $courseController->create();
        
        // Return the response
        return $response;
    }
}