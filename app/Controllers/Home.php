<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        return view('index');   // home.php
    }

    public function about()
    {
        return view('about');  // about.php
    }

    public function contact()
    {
        return view('contact'); // contact.php
    }
}
