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
$routes->post('/courses/create', 'Course::create');
$routes->post('/courses/update/(:num)', 'Course::update/$1');
$routes->post('/courses/delete/(:num)', 'Course::delete/$1');
$routes->get('/courses/get/(:num)', 'Course::get/$1');
$routes->get('/courses/teachers', 'Course::getTeachers');
$routes->get('/courses/search-admin', 'Course::searchAdmin');
$routes->get('/settings', 'Settings::index');
$routes->get('/soft-deletes', 'SoftDeletes::index');
$routes->post('/soft-deletes/restore', 'SoftDeletes::restore');
$routes->post('/soft-deletes/permanent-delete', 'SoftDeletes::permanentDelete');
$routes->post('/soft-deletes/bulk-restore', 'SoftDeletes::bulkRestore');

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

// Enrollment routes removed - using direct enrollment only

// Test routes
$routes->get('/test-enrollment', 'TestEnrollment::index');
$routes->get('/enrollment-demo', 'TestEnrollment::demo');
$routes->get('/test-courses', 'TestCourses::index');
$routes->get('/system-test', 'SystemTest::index');
$routes->get('/quick-test', 'QuickTest::index');
$routes->get('/test-admin', 'QuickTest::testAdmin');
$routes->get('/test-student', 'QuickTest::testStudent');
$routes->get('/test-teacher', 'QuickTest::testTeacher');
$routes->get('/test-course-create', 'TestCourseCreate::index');
$routes->post('/test-course-create/submit', 'TestCourseCreate::submit');
$routes->get('/test-course-ajax', 'TestCourseCreate::testAjax');
$routes->get('/status', 'SystemStatus::index');
$routes->get('/test-upload', 'TestUpload::index');
$routes->get('/test-routes', 'TestRoutes::index');
$routes->get('/test-soft-delete', 'TestSoftDelete::index');