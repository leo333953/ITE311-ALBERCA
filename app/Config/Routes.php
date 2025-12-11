<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/about', 'Home::about');
$routes->get('/contact', 'Home::contact');

$routes->get('/register', 'Auth::register');
$routes->post('/register', 'Auth::register');

$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::login');

$routes->get('auth/logout', 'Auth::logout'); 
$routes->get('/dashboard', 'Auth::dashboard');

// Temporary routes for Step 2 testing - Add these lines
$routes->get('/admin/dashboard', function() {
    return "Step 2 Working! Admin Dashboard - Role: " . session()->get('role');
});

$routes->get('/teacher/dashboard', function() {
    return "Step 2 Working! Teacher Dashboard - Role: " . session()->get('role');
});

$routes->get('/student/dashboard', function() {
    return "Step 2 Working! Student Dashboard - Role: " . session()->get('role');
});

$routes->get('dashboard', 'Auth::dashboard');
// For enrolling via AJAX
$routes->post('course/enroll', 'Course::enroll');

// For displaying enrolled courses / success message
$routes->get('course/enroll', 'Course::enrollPage');
// Keep your existing routes below these

//Love 7

$routes->get('/materials/delete/(:num)', 'Materials::delete/$1');
$routes->get('/materials/download/(:num)', 'Materials::download/$1');
$routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');
$routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');


// Admin Routes
$routes->get('/users', 'Users::index');
$routes->post('/users/create', 'Users::create');
$routes->post('/users/update/(:num)', 'Users::update/$1');
$routes->post('/users/delete/(:num)', 'Users::delete/$1');
$routes->get('/users/get/(:num)', 'Users::get/$1');
$routes->get('/reports', 'Reports::index');
$routes->get('/courses', 'Course::index');
$routes->get('/settings', 'Settings::index');

// Teacher Routes
$routes->get('/courses/manage', 'Course::manage');
$routes->get('/students', 'Students::index');
$routes->get('/lessons', 'Lessons::index');
$routes->get('/announcements', 'Announcements::index');

// Student Routes
$routes->get('/assignments', 'Assignments::index');
$routes->get('/groups', 'Groups::index');
$routes->get('/progress', 'Progress::index');

//Notification
$routes->get('/notifications', 'Notifications::get');
$routes->post('/notifications/mark-as-read/(:num)', 'Notifications::markAsRead/$1');

//Lab Nien
$routes->get('/courses/search', 'Course::search');
$routes->post('/courses/search', 'Course::search');