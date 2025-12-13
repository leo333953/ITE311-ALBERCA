<?php

namespace App\Controllers;

class SystemStatus extends BaseController
{
    public function index()
    {
        $output = "<!DOCTYPE html><html><head><title>System Status</title>";
        $output .= "<style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { color: #28a745; } .error { color: #dc3545; } .info { color: #17a2b8; } .warning { color: #ffc107; }
            .card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; }
            .btn { display: inline-block; padding: 8px 16px; margin: 5px; text-decoration: none; border-radius: 4px; color: white; }
            .btn-primary { background: #007bff; } .btn-success { background: #28a745; } .btn-warning { background: #ffc107; color: black; }
            .btn-danger { background: #dc3545; } .btn-info { background: #17a2b8; }
            h1, h2, h3 { color: #333; } .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        </style></head><body>";
        
        $output .= "<div class='container'>";
        $output .= "<h1>🚀 LMS System Status Dashboard</h1>";
        
        // System Status
        $output .= "<div class='card'>";
        $output .= "<h2>📊 System Health</h2>";
        
        // Database connection test
        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
            $output .= "<p class='success'>✅ Database: Connected</p>";
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ Database: Failed - " . $e->getMessage() . "</p>";
        }
        
        // Models test
        try {
            $courseModel = new \App\Models\CourseModel();
            $courses = $courseModel->findAll();
            $output .= "<p class='success'>✅ CourseModel: Working (" . count($courses) . " courses)</p>";
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ CourseModel: Failed - " . $e->getMessage() . "</p>";
        }
        
        try {
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $enrollments = $enrollmentModel->findAll();
            $output .= "<p class='success'>✅ EnrollmentModel: Working (" . count($enrollments) . " enrollments)</p>";
        } catch (\Exception $e) {
            $output .= "<p class='error'>❌ EnrollmentModel: Failed - " . $e->getMessage() . "</p>";
        }
        
        $output .= "</div>";
        
        // Session Status
        $output .= "<div class='card'>";
        $output .= "<h2>👤 Session Status</h2>";
        $isLoggedIn = session()->get('isLoggedIn');
        $role = session()->get('role');
        $userId = session()->get('user_id');
        
        if ($isLoggedIn) {
            $output .= "<p class='success'>✅ Logged in as: <strong>$role</strong> (ID: $userId)</p>";
        } else {
            $output .= "<p class='warning'>⚠️ Not logged in</p>";
        }
        $output .= "</div>";
        
        // Quick Actions
        $output .= "<div class='grid'>";
        
        // Authentication Tests
        $output .= "<div class='card'>";
        $output .= "<h3>🔐 Authentication Tests</h3>";
        $output .= "<a href='" . base_url('test-admin') . "' class='btn btn-primary'>Login as Admin</a>";
        $output .= "<a href='" . base_url('test-student') . "' class='btn btn-info'>Login as Student</a>";
        $output .= "<a href='" . base_url('test-teacher') . "' class='btn btn-warning'>Login as Teacher</a>";
        $output .= "<a href='" . base_url('auth/logout') . "' class='btn btn-warning'>Logout</a>";
        $output .= "</div>";
        
        // System Tests
        $output .= "<div class='card'>";
        $output .= "<h3>🧪 System Tests</h3>";
        $output .= "<a href='" . base_url('test-course-create') . "' class='btn btn-success'>Test Course Creation</a>";
        $output .= "<a href='" . base_url('test-course-ajax') . "' class='btn btn-info'>Test AJAX Course</a>";
        $output .= "<a href='" . base_url('test-courses') . "' class='btn btn-warning'>Create Sample Courses</a>";
        $output .= "</div>";
        
        $output .= "</div>";
        
        // Main System Links
        $output .= "<div class='card'>";
        $output .= "<h2>🎯 Main System Access</h2>";
        $output .= "<div class='grid'>";
        
        $output .= "<div>";
        $output .= "<h3>Admin Functions</h3>";
        $output .= "<a href='" . base_url('courses') . "' class='btn btn-primary'>📚 Course Catalog Management</a><br>";
        $output .= "<a href='" . base_url('users') . "' class='btn btn-primary'>👥 User Management</a><br>";
        $output .= "<a href='" . base_url('enrollments') . "' class='btn btn-primary'>📝 All Enrollments</a><br>";
        $output .= "<a href='" . base_url('soft-deletes') . "' class='btn btn-primary'>🗑️ Soft Delete Management</a>";
        $output .= "</div>";
        
        $output .= "<div>";
        $output .= "<h3>Teacher Functions</h3>";
        $output .= "<a href='" . base_url('courses/manage') . "' class='btn btn-warning'>📚 Course Catalog Management</a><br>";
        $output .= "<a href='" . base_url('enrollments') . "' class='btn btn-warning'>📝 Manage Enrollments</a><br>";
        $output .= "<a href='" . base_url('dashboard') . "' class='btn btn-warning'>🏠 Teacher Dashboard</a>";
        $output .= "</div>";
        
        $output .= "<div>";
        $output .= "<h3>Student Functions</h3>";
        $output .= "<a href='" . base_url('enrollments') . "' class='btn btn-success'>📋 Submit Enrollment</a><br>";
        $output .= "<a href='" . base_url('courses') . "' class='btn btn-success'>📖 My Courses</a><br>";
        $output .= "<a href='" . base_url('dashboard') . "' class='btn btn-success'>🏠 Dashboard</a>";
        $output .= "</div>";
        
        $output .= "</div>";
        $output .= "</div>";
        
        // Recent Activity
        $output .= "<div class='card'>";
        $output .= "<h2>📈 Recent Activity</h2>";
        
        try {
            $db = \Config\Database::connect();
            
            // Recent courses
            $recentCourses = $db->query("SELECT title, created_at FROM courses ORDER BY created_at DESC LIMIT 3")->getResultArray();
            $output .= "<h4>Recent Courses:</h4><ul>";
            foreach ($recentCourses as $course) {
                $output .= "<li>{$course['title']} - " . date('M d, Y', strtotime($course['created_at'] ?? 'now')) . "</li>";
            }
            $output .= "</ul>";
            
            // Recent enrollments
            $recentEnrollments = $db->query("SELECT full_name, course_year, status, submitted_at FROM enrollments ORDER BY submitted_at DESC LIMIT 3")->getResultArray();
            $output .= "<h4>Recent Enrollments:</h4><ul>";
            foreach ($recentEnrollments as $enrollment) {
                $status = ucfirst($enrollment['status'] ?? 'pending');
                $name = $enrollment['full_name'] ?? 'Unknown';
                $course = $enrollment['course_year'] ?? 'Unknown Course';
                $date = date('M d, Y', strtotime($enrollment['submitted_at'] ?? 'now'));
                $output .= "<li>$name - $course ($status) - $date</li>";
            }
            $output .= "</ul>";
            
        } catch (\Exception $e) {
            $output .= "<p class='error'>Could not load recent activity: " . $e->getMessage() . "</p>";
        }
        
        $output .= "</div>";
        
        $output .= "<div class='card'>";
        $output .= "<h2>🔧 Troubleshooting</h2>";
        $output .= "<p><strong>Course Save Error?</strong> Try these steps:</p>";
        $output .= "<ol>";
        $output .= "<li>Make sure you're logged in as admin: <a href='" . base_url('test-admin') . "'>Login as Admin</a></li>";
        $output .= "<li>Test course creation: <a href='" . base_url('test-course-create') . "'>Test Course Creation</a></li>";
        $output .= "<li>Check the admin course catalog: <a href='" . base_url('courses') . "'>Course Catalog</a></li>";
        $output .= "<li>If still having issues, check browser console for JavaScript errors</li>";
        $output .= "</ol>";
        $output .= "</div>";
        
        $output .= "</div></body></html>";
        
        return $output;
    }
}