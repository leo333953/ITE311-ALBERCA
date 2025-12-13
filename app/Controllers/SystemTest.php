<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\UserModel;

class SystemTest extends BaseController
{
    public function index()
    {
        $output = "<h1>System Status Test</h1>";
        $output .= "<style>body { font-family: Arial, sans-serif; margin: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";
        
        // Test database connection
        try {
            $db = \Config\Database::connect();
            $output .= "<p class='success'>✅ Database connection: OK</p>";
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ Database connection failed: " . $e->getMessage() . "</p>";
            return $output;
        }
        
        // Test CourseModel
        try {
            $courseModel = new CourseModel();
            $courses = $courseModel->findAll();
            $output .= "<p class='success'>✅ CourseModel: OK (" . count($courses) . " courses found)</p>";
            
            if (empty($courses)) {
                $output .= "<p class='info'>ℹ️ No courses found. <a href='" . base_url('test-courses') . "'>Create sample courses</a></p>";
            }
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ CourseModel failed: " . $e->getMessage() . "</p>";
        }
        
        // Test EnrollmentModel
        try {
            $enrollmentModel = new EnrollmentModel();
            $enrollments = $enrollmentModel->findAll();
            $output .= "<p class='success'>✅ EnrollmentModel: OK (" . count($enrollments) . " enrollments found)</p>";
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ EnrollmentModel failed: " . $e->getMessage() . "</p>";
        }
        
        // Test UserModel
        try {
            $userModel = new UserModel();
            $users = $userModel->findAll();
            $output .= "<p class='success'>✅ UserModel: OK (" . count($users) . " users found)</p>";
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ UserModel failed: " . $e->getMessage() . "</p>";
        }
        
        // Test session
        $output .= "<h2>Session Information</h2>";
        $output .= "<pre>" . json_encode(session()->get(), JSON_PRETTY_PRINT) . "</pre>";
        
        // Test routes
        $output .= "<h2>Quick Links</h2>";
        $output .= "<ul>";
        $output .= "<li><a href='" . base_url('login') . "'>Login</a></li>";
        $output .= "<li><a href='" . base_url('dashboard') . "'>Dashboard</a></li>";
        $output .= "<li><a href='" . base_url('courses') . "'>Course Catalog (Admin)</a></li>";
        $output .= "<li><a href='" . base_url('enrollments') . "'>Enrollment System</a></li>";
        $output .= "<li><a href='" . base_url('test-courses') . "'>Create Sample Courses</a></li>";
        $output .= "</ul>";
        
        return $output;
    }
}