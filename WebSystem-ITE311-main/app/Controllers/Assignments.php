<?php

namespace App\Controllers;

class Assignments extends BaseController
{
    public function index()
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'student') {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        return view('templates/header', ['role' => $role]) . view('student/assignments');
    }
}

