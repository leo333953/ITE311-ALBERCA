<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\UserModel;

class TestEnrollment extends BaseController
{
    public function index()
    {
        $output = "<h1>Enrollment System Test</h1>";
        
        try {
            // Test database connection
            $db = \Config\Database::connect();
            $output .= "<p>✅ Database connection: OK</p>";
            
            // Test EnrollmentModel
            $enrollmentModel = new EnrollmentModel();
            $output .= "<p>✅ EnrollmentModel: OK</p>";
            
            // Test CourseModel
            $courseModel = new CourseModel();
            $output .= "<p>✅ CourseModel: OK</p>";
            
            // Test UserModel
            $userModel = new UserModel();
            $output .= "<p>✅ UserModel: OK</p>";
            
            // Test enrollment stats
            $stats = $enrollmentModel->getEnrollmentStats();
            $output .= "<p>✅ Enrollment Stats: " . json_encode($stats) . "</p>";
            
            // Test getting enrollments
            $enrollments = $enrollmentModel->getEnrollmentsWithDetails();
            $output .= "<p>✅ Total Enrollments: " . count($enrollments) . "</p>";
            
            // Test session
            if (session()->get('isLoggedIn')) {
                $output .= "<p>✅ User logged in as: " . session()->get('role') . " (" . session()->get('email') . ")</p>";
            } else {
                $output .= "<p>⚠️ No user logged in</p>";
            }
            
            $output .= "<hr><h2>Database Structure Check</h2>";
            
            // Check enrollments table structure
            $query = $db->query("DESCRIBE enrollments");
            $fields = $query->getResultArray();
            
            $output .= "<h3>Enrollments Table Fields:</h3><ul>";
            foreach ($fields as $field) {
                $output .= "<li>{$field['Field']} ({$field['Type']})</li>";
            }
            $output .= "</ul>";
            
            $output .= "<hr><p><a href='" . base_url('enrollments') . "'>Go to Enrollment System</a></p>";
            
        } catch (Exception $e) {
            $output .= "<p>❌ Error: " . $e->getMessage() . "</p>";
            $output .= "<pre>" . $e->getTraceAsString() . "</pre>";
        }
        
        return $output;
    }
    
    public function demo()
    {
        // Demo the enrollment system with fake session data
        session()->set([
            'user_id' => 1,
            'name' => 'Demo Student',
            'email' => 'demo@student.com',
            'role' => 'student',
            'isLoggedIn' => true
        ]);
        
        return redirect()->to(base_url('enrollments'));
    }
}