<?php

namespace App\Controllers;

class TestRoutes extends BaseController
{
    public function index()
    {
        $output = "<!DOCTYPE html><html><head><title>Route Test</title>";
        $output .= "<style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { color: #28a745; } .error { color: #dc3545; } .info { color: #17a2b8; }
            .card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; }
            .btn { display: inline-block; padding: 8px 16px; margin: 5px; text-decoration: none; border-radius: 4px; color: white; background: #007bff; }
            h1, h2, h3 { color: #333; }
        </style></head><body>";
        
        $output .= "<div class='container'>";
        $output .= "<h1>🔗 Course Route Structure Test</h1>";
        
        // Route structure info
        $output .= "<div class='card'>";
        $output .= "<h2>📋 Current Route Structure</h2>";
        $output .= "<h3 class='success'>✅ Admin Routes:</h3>";
        $output .= "<ul>";
        $output .= "<li><strong>/courses</strong> - Admin course catalog management</li>";
        $output .= "<li><strong>/courses/create</strong> - Create new course (POST)</li>";
        $output .= "<li><strong>/courses/update/{id}</strong> - Update course (POST)</li>";
        $output .= "<li><strong>/courses/delete/{id}</strong> - Delete course (POST)</li>";
        $output .= "<li><strong>/courses/get/{id}</strong> - Get course details (GET)</li>";
        $output .= "<li><strong>/courses/search-admin</strong> - Admin search (GET)</li>";
        $output .= "</ul>";
        
        $output .= "<h3 class='info'>ℹ️ Teacher Routes:</h3>";
        $output .= "<ul>";
        $output .= "<li><strong>/courses/manage</strong> - Teacher course catalog management</li>";
        $output .= "<li><strong>/courses/create</strong> - Create new course (POST) - shared with admin</li>";
        $output .= "<li><strong>/courses/update/{id}</strong> - Update course (POST) - shared with admin</li>";
        $output .= "<li><strong>/courses/delete/{id}</strong> - Delete course (POST) - shared with admin</li>";
        $output .= "<li><strong>/courses/get/{id}</strong> - Get course details (GET) - shared with admin</li>";
        $output .= "<li><strong>/courses/search-admin</strong> - Teacher search (GET) - shared with admin</li>";
        $output .= "</ul>";
        
        $output .= "<h3 class='error'>📚 Student Routes:</h3>";
        $output .= "<ul>";
        $output .= "<li><strong>/courses</strong> - Student enrolled courses view</li>";
        $output .= "<li><strong>/courses/search</strong> - Student course search (GET)</li>";
        $output .= "</ul>";
        $output .= "</div>";
        
        // Test links
        $output .= "<div class='card'>";
        $output .= "<h2>🧪 Test Links</h2>";
        
        $output .= "<h3>Admin Testing:</h3>";
        $output .= "<a href='" . base_url('test-admin') . "' class='btn' style='background: #dc3545;'>1. Login as Admin</a>";
        $output .= "<a href='" . base_url('courses') . "' class='btn' style='background: #dc3545;'>2. Admin Course Catalog</a>";
        
        $output .= "<h3>Teacher Testing:</h3>";
        $output .= "<a href='" . base_url('test-teacher') . "' class='btn' style='background: #ffc107; color: black;'>1. Login as Teacher</a>";
        $output .= "<a href='" . base_url('courses/manage') . "' class='btn' style='background: #ffc107; color: black;'>2. Teacher Course Catalog</a>";
        
        $output .= "<h3>Student Testing:</h3>";
        $output .= "<a href='" . base_url('test-student') . "' class='btn' style='background: #28a745;'>1. Login as Student</a>";
        $output .= "<a href='" . base_url('courses') . "' class='btn' style='background: #28a745;'>2. Student Courses</a>";
        $output .= "</div>";
        
        // Route behavior explanation
        $output .= "<div class='card'>";
        $output .= "<h2>⚙️ Route Behavior</h2>";
        $output .= "<h3>When accessing <code>/courses</code>:</h3>";
        $output .= "<ul>";
        $output .= "<li><strong>Admin:</strong> Shows admin course catalog management</li>";
        $output .= "<li><strong>Teacher:</strong> Redirects to <code>/courses/manage</code></li>";
        $output .= "<li><strong>Student:</strong> Shows enrolled courses</li>";
        $output .= "<li><strong>Not logged in:</strong> Redirects to login</li>";
        $output .= "</ul>";
        
        $output .= "<h3>When accessing <code>/courses/manage</code>:</h3>";
        $output .= "<ul>";
        $output .= "<li><strong>Teacher:</strong> Shows teacher course catalog management</li>";
        $output .= "<li><strong>Non-teacher:</strong> Redirects to login</li>";
        $output .= "</ul>";
        $output .= "</div>";
        
        // Current session info
        $output .= "<div class='card'>";
        $output .= "<h2>👤 Current Session</h2>";
        $isLoggedIn = session()->get('isLoggedIn');
        $role = session()->get('role');
        
        if ($isLoggedIn) {
            $output .= "<p class='success'>✅ Logged in as: <strong>$role</strong></p>";
            
            if ($role === 'admin') {
                $output .= "<p>You should access: <a href='" . base_url('courses') . "'>/courses</a></p>";
            } elseif ($role === 'teacher') {
                $output .= "<p>You should access: <a href='" . base_url('courses/manage') . "'>/courses/manage</a></p>";
            } elseif ($role === 'student') {
                $output .= "<p>You should access: <a href='" . base_url('courses') . "'>/courses</a> (enrolled courses)</p>";
            }
        } else {
            $output .= "<p class='error'>❌ Not logged in</p>";
        }
        $output .= "</div>";
        
        $output .= "<p><a href='" . base_url('status') . "' class='btn'>Back to System Status</a></p>";
        
        $output .= "</div></body></html>";
        
        return $output;
    }
}