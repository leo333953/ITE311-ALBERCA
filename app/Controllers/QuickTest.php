<?php

namespace App\Controllers;

use App\Models\CourseModel;

class QuickTest extends BaseController
{
    public function index()
    {
        $output = "<h1>Quick System Test</h1>";
        $output .= "<style>body { font-family: Arial, sans-serif; margin: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";
        
        // Test Course Model
        try {
            $courseModel = new CourseModel();
            $courses = $courseModel->findAll();
            $output .= "<p class='success'>✅ CourseModel working: " . count($courses) . " courses found</p>";
            
            if (!empty($courses)) {
                $output .= "<h3>Available Courses:</h3><ul>";
                foreach ($courses as $course) {
                    $output .= "<li>ID: {$course['id']} - {$course['title']}</li>";
                }
                $output .= "</ul>";
            }
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ CourseModel error: " . $e->getMessage() . "</p>";
        }
        
        // Test session
        $output .= "<h3>Session Test:</h3>";
        $output .= "<p>Logged in: " . (session()->get('isLoggedIn') ? 'Yes' : 'No') . "</p>";
        $output .= "<p>Role: " . (session()->get('role') ?? 'Not set') . "</p>";
        $output .= "<p>User ID: " . (session()->get('user_id') ?? 'Not set') . "</p>";
        
        // Test admin course view
        $output .= "<h3>Admin Course View Test:</h3>";
        if (session()->get('role') === 'admin') {
            $output .= "<p class='success'>✅ Admin role detected</p>";
            $output .= "<p><a href='" . base_url('courses') . "'>Test Admin Course Catalog</a></p>";
        } else {
            $output .= "<p class='info'>ℹ️ Not logged in as admin. <a href='" . base_url('login') . "'>Login as admin</a> to test course catalog</p>";
        }
        
        // Test enrollment system
        $output .= "<p><a href='" . base_url('enrollments') . "'>Test Enrollment System</a></p>";
        
        return $output;
    }
    
    public function testAdmin()
    {
        // Simulate admin login for testing
        session()->set([
            'isLoggedIn' => true,
            'role' => 'admin',
            'user_id' => 1,
            'email' => 'admin@test.com'
        ]);
        
        return redirect()->to('/courses');
    }
    
    public function testStudent()
    {
        // Simulate student login for testing
        session()->set([
            'isLoggedIn' => true,
            'role' => 'student',
            'user_id' => 2,
            'email' => 'student@test.com'
        ]);
        
        return redirect()->to('/enrollments');
    }
    
    public function testTeacher()
    {
        // Simulate teacher login for testing
        session()->set([
            'isLoggedIn' => true,
            'role' => 'teacher',
            'user_id' => 3,
            'email' => 'teacher@test.com'
        ]);
        
        return redirect()->to('/courses/manage');
    }
}