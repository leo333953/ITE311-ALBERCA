<?php

namespace App\Controllers;

class Lessons extends BaseController
{
    public function index()
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        return view('templates/header', ['role' => $role]) . view('teacher/lessons');
    }
}

