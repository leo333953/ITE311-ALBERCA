<?php

namespace App\Controllers;

class Reports extends BaseController
{
    public function index()
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        return view('templates/header', ['role' => $role]) . view('admin/reports');
    }
}

