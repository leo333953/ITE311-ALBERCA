<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\UserModel;

class TestSoftDelete extends BaseController
{
    public function index()
    {
        // Simulate admin session
        session()->set([
            'isLoggedIn' => true,
            'role' => 'admin',
            'user_id' => 1
        ]);
        
        $output = "<!DOCTYPE html><html><head><title>Soft Delete Test</title>";
        $output .= "<style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 1000px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { color: #28a745; } .error { color: #dc3545; } .info { color: #17a2b8; } .warning { color: #ffc107; }
            .card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; }
            .btn { display: inline-block; padding: 8px 16px; margin: 5px; text-decoration: none; border-radius: 4px; color: white; }
            .btn-primary { background: #007bff; } .btn-success { background: #28a745; } .btn-danger { background: #dc3545; }
            h1, h2, h3 { color: #333; } table { width: 100%; border-collapse: collapse; margin: 10px 0; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background: #f8f9fa; }
        </style></head><body>";
        
        $output .= "<div class='container'>";
        $output .= "<h1>🗑️ Soft Delete System Test</h1>";
        
        // Soft Delete Overview
        $output .= "<div class='card'>";
        $output .= "<h2>📋 Soft Delete Implementation Status</h2>";
        $output .= "<h3 class='success'>✅ Models with Soft Delete Enabled:</h3>";
        $output .= "<ul>";
        $output .= "<li><strong>CourseModel</strong> - Courses are soft deleted</li>";
        $output .= "<li><strong>UserModel</strong> - Users are soft deleted</li>";
        $output .= "<li><strong>MaterialModel</strong> - Materials are soft deleted (files preserved)</li>";
        $output .= "<li><strong>EnrollmentModel</strong> - Enrollments are soft deleted</li>";
        $output .= "</ul>";
        
        $output .= "<h3 class='info'>ℹ️ How Soft Delete Works:</h3>";
        $output .= "<ul>";
        $output .= "<li>Records are marked with <code>deleted_at</code> timestamp instead of being removed</li>";
        $output .= "<li>Soft deleted records are hidden from normal queries</li>";
        $output .= "<li>Records can be restored by setting <code>deleted_at</code> to NULL</li>";
        $output .= "<li>Physical files (materials) are preserved for recovery</li>";
        $output .= "<li>Admin can permanently delete records if needed</li>";
        $output .= "</ul>";
        $output .= "</div>";
        
        // Current Statistics
        $output .= "<div class='card'>";
        $output .= "<h2>📊 Current Database Statistics</h2>";
        
        try {
            $courseModel = new CourseModel();
            $userModel = new UserModel();
            
            // Active records
            $activeCourses = $courseModel->findAll();
            $activeUsers = $userModel->findAll();
            
            // Deleted records
            $deletedCourses = $courseModel->onlyDeleted()->findAll();
            $deletedUsers = $userModel->onlyDeleted()->findAll();
            
            $output .= "<table>";
            $output .= "<tr><th>Type</th><th>Active Records</th><th>Soft Deleted</th><th>Total</th></tr>";
            $output .= "<tr><td>Courses</td><td>" . count($activeCourses) . "</td><td>" . count($deletedCourses) . "</td><td>" . (count($activeCourses) + count($deletedCourses)) . "</td></tr>";
            $output .= "<tr><td>Users</td><td>" . count($activeUsers) . "</td><td>" . count($deletedUsers) . "</td><td>" . (count($activeUsers) + count($deletedUsers)) . "</td></tr>";
            $output .= "</table>";
            
        } catch (\Exception $e) {
            $output .= "<p class='error'>Error loading statistics: " . $e->getMessage() . "</p>";
        }
        $output .= "</div>";
        
        // Test Actions
        $output .= "<div class='card'>";
        $output .= "<h2>🧪 Test Soft Delete Functionality</h2>";
        
        $output .= "<h3>Test Delete Operations:</h3>";
        $output .= "<a href='" . base_url('courses') . "' class='btn btn-primary'>Go to Course Catalog</a>";
        $output .= "<span class='info'>→ Try deleting a course (it will be soft deleted)</span><br>";
        
        $output .= "<a href='" . base_url('users') . "' class='btn btn-primary'>Go to User Management</a>";
        $output .= "<span class='info'>→ Try deleting a user (it will be soft deleted)</span><br>";
        
        $output .= "<h3>Manage Soft Deleted Records:</h3>";
        $output .= "<a href='" . base_url('soft-deletes') . "' class='btn btn-success'>Soft Delete Management</a>";
        $output .= "<span class='success'>→ View, restore, or permanently delete soft-deleted records</span>";
        $output .= "</div>";
        
        // Benefits
        $output .= "<div class='card'>";
        $output .= "<h2>🎯 Benefits of Soft Delete</h2>";
        $output .= "<ul>";
        $output .= "<li><strong>Data Recovery:</strong> Accidentally deleted records can be restored</li>";
        $output .= "<li><strong>Audit Trail:</strong> Keep track of when records were deleted</li>";
        $output .= "<li><strong>Data Integrity:</strong> Maintain referential integrity with related records</li>";
        $output .= "<li><strong>Compliance:</strong> Meet data retention requirements</li>";
        $output .= "<li><strong>User Safety:</strong> Protect against accidental data loss</li>";
        $output .= "<li><strong>Business Continuity:</strong> Restore operations if needed</li>";
        $output .= "</ul>";
        $output .= "</div>";
        
        // Implementation Details
        $output .= "<div class='card'>";
        $output .= "<h2>⚙️ Implementation Details</h2>";
        $output .= "<h3>Database Changes:</h3>";
        $output .= "<ul>";
        $output .= "<li>Added <code>deleted_at</code> column to all main tables</li>";
        $output .= "<li>Models configured with <code>useSoftDeletes = true</code></li>";
        $output .= "<li>Controllers use model's <code>delete()</code> method (automatic soft delete)</li>";
        $output .= "</ul>";
        
        $output .= "<h3>Admin Features:</h3>";
        $output .= "<ul>";
        $output .= "<li>View all soft-deleted records by type</li>";
        $output .= "<li>Restore individual records</li>";
        $output .= "<li>Bulk restore all records of a type</li>";
        $output .= "<li>Permanently delete records (with confirmation)</li>";
        $output .= "</ul>";
        $output .= "</div>";
        
        $output .= "<p><a href='" . base_url('status') . "' class='btn btn-primary'>Back to System Status</a></p>";
        
        $output .= "</div></body></html>";
        
        return $output;
    }
}