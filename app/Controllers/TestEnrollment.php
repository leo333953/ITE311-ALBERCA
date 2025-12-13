<?php

namespace App\Controllers;

class TestEnrollment extends BaseController
{
    public function index()
    {
        return view('test/enrollment_test');
    }
    
    public function demo()
    {
        $data = [
            'session_data' => session()->get(),
            'is_logged_in' => session()->get('isLoggedIn'),
            'user_role' => session()->get('role'),
            'user_email' => session()->get('email')
        ];
        
        $output = "<h1>Enrollment System Demo</h1>";
        $output .= "<h2>Session Information:</h2>";
        $output .= "<pre>" . json_encode($data, JSON_PRETTY_PRINT) . "</pre>";
        
        if (!session()->get('isLoggedIn')) {
            $output .= "<div class='alert alert-warning'>You are not logged in. Please <a href='" . base_url('login') . "'>login</a> first.</div>";
        } else if (session()->get('role') !== 'student') {
            $output .= "<div class='alert alert-warning'>You are logged in as " . session()->get('role') . ". Please login as a student to test enrollment.</div>";
        } else {
            $output .= "<div class='alert alert-success'>You are logged in as a student. Ready to test enrollment!</div>";
            $output .= "<p><a href='" . base_url('test-enrollment') . "' class='btn btn-primary'>Go to Enrollment Test Page</a></p>";
            $output .= "<p><a href='" . base_url('courses') . "' class='btn btn-success'>Go to My Courses</a></p>";
        }
        
        return $output;
    }
}