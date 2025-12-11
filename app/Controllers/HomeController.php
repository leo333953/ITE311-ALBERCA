<?php namespace App\Controllers;

class HomeController extends BaseController
{
    public function index()
    {
        // If user is already logged in, redirect to appropriate dashboard
        if (session()->get('isLoggedIn')) {
            switch (session()->get('role')) {
                case 'admin':
                    return redirect()->to('/admin/dashboard');
                case 'teacher':
                    return redirect()->to('/teacher/dashboard');
                case 'student':
                    return redirect()->to('/student/dashboard');
                default:
                    return redirect()->to('/dashboard');
            }
        }
        
        // If not logged in, show homepage or redirect to login
        return redirect()->to('/login');
        // OR show a welcome page: return view('welcome');
    }
}