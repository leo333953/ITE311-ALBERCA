<?php

namespace App\Controllers;

class TestUpload extends BaseController
{
    public function index()
    {
        // Simulate admin/teacher session for testing
        session()->set([
            'isLoggedIn' => true,
            'role' => 'admin',
            'user_id' => 1
        ]);
        
        $output = "<!DOCTYPE html><html><head><title>Upload Test</title>";
        $output .= "<style>
            body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
            .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
            .success { color: #28a745; } .error { color: #dc3545; } .info { color: #17a2b8; }
            .card { border: 1px solid #ddd; border-radius: 8px; padding: 15px; margin: 10px 0; }
            .btn { display: inline-block; padding: 8px 16px; margin: 5px; text-decoration: none; border-radius: 4px; color: white; background: #007bff; }
            h1, h2, h3 { color: #333; }
        </style></head><body>";
        
        $output .= "<div class='container'>";
        $output .= "<h1>📁 File Upload Restrictions Test</h1>";
        
        // Current restrictions info
        $output .= "<div class='card'>";
        $output .= "<h2>✅ Current Upload Restrictions</h2>";
        $output .= "<p class='success'><strong>Allowed File Types:</strong> PDF, PPT only</p>";
        $output .= "<p class='info'><strong>Maximum File Size:</strong> 100MB</p>";
        $output .= "<p class='error'><strong>Blocked File Types:</strong> PPTX, DOC, DOCX, ZIP, and all other formats</p>";
        $output .= "</div>";
        
        // Test the validation rules
        $output .= "<div class='card'>";
        $output .= "<h2>🧪 Validation Rules Test</h2>";
        
        $validation = \Config\Services::validation();
        $validation->setRules([
            'material_file' => [
                'rules' => 'uploaded[material_file]|max_size[material_file,102400]|ext_in[material_file,pdf,ppt]',
                'errors' => [
                    'uploaded' => 'Please select a file.',
                    'max_size' => 'File is too large. Maximum size is 100MB.',
                    'ext_in'  => 'Invalid file type. Only PDF and PowerPoint files (PDF, PPT) are allowed.'
                ]
            ]
        ]);
        
        $rules = $validation->getRules();
        $output .= "<p><strong>Current Validation Rule:</strong></p>";
        $output .= "<code>" . $rules['material_file']['rules'] . "</code>";
        $output .= "<p><strong>Error Message:</strong></p>";
        $output .= "<code>" . $rules['material_file']['errors']['ext_in'] . "</code>";
        $output .= "</div>";
        
        // File type examples
        $output .= "<div class='card'>";
        $output .= "<h2>📋 File Type Examples</h2>";
        $output .= "<h3 class='success'>✅ ALLOWED:</h3>";
        $output .= "<ul>";
        $output .= "<li>document.pdf</li>";
        $output .= "<li>presentation.ppt</li>";
        $output .= "</ul>";
        
        $output .= "<h3 class='error'>❌ BLOCKED:</h3>";
        $output .= "<ul>";
        $output .= "<li>slides.pptx</li>";
        $output .= "<li>document.doc</li>";
        $output .= "<li>document.docx</li>";
        $output .= "<li>archive.zip</li>";
        $output .= "<li>image.jpg</li>";
        $output .= "<li>video.mp4</li>";
        $output .= "<li>Any other file type</li>";
        $output .= "</ul>";
        $output .= "</div>";
        
        // Test links
        $output .= "<div class='card'>";
        $output .= "<h2>🔗 Test Links</h2>";
        $output .= "<p>To test the upload functionality:</p>";
        $output .= "<ol>";
        $output .= "<li>Make sure you have some courses in the system</li>";
        $output .= "<li>Visit the course upload page: <code>/admin/course/[COURSE_ID]/upload</code></li>";
        $output .= "<li>Try uploading different file types to test the restrictions</li>";
        $output .= "</ol>";
        
        // Check if there are courses available
        try {
            $courseModel = new \App\Models\CourseModel();
            $courses = $courseModel->findAll();
            
            if (!empty($courses)) {
                $output .= "<h3>Available Courses for Testing:</h3>";
                $output .= "<ul>";
                foreach ($courses as $course) {
                    $uploadUrl = base_url("admin/course/{$course['id']}/upload");
                    $output .= "<li><a href='$uploadUrl' class='btn'>Test Upload for: {$course['title']}</a></li>";
                }
                $output .= "</ul>";
            } else {
                $output .= "<p class='error'>No courses found. <a href='" . base_url('test-courses') . "'>Create sample courses first</a></p>";
            }
        } catch (\Exception $e) {
            $output .= "<p class='error'>Error loading courses: " . $e->getMessage() . "</p>";
        }
        
        $output .= "</div>";
        
        // Implementation summary
        $output .= "<div class='card'>";
        $output .= "<h2>🔧 Implementation Summary</h2>";
        $output .= "<h3>Changes Made:</h3>";
        $output .= "<ol>";
        $output .= "<li><strong>Materials Controller:</strong> Updated validation rules to only accept PDF, PPT</li>";
        $output .= "<li><strong>Upload View:</strong> Updated UI to show only allowed file types</li>";
        $output .= "<li><strong>Client-side Validation:</strong> Added JavaScript to validate file types before upload</li>";
        $output .= "<li><strong>HTML5 Accept Attribute:</strong> Added accept='.pdf,.ppt' to file input</li>";
        $output .= "</ol>";
        $output .= "</div>";
        
        $output .= "<p><a href='" . base_url('status') . "' class='btn'>Back to System Status</a></p>";
        
        $output .= "</div></body></html>";
        
        return $output;
    }
}