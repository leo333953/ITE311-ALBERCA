<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;

class Course extends BaseController
{
    public function enroll()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You must be logged in to enroll.'
            ]);
        }

        $user_id = session()->get('user_id');
        $course_id = $this->request->getPost('course_id');

        if (empty($course_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'No course selected.'
            ]);
        }

        $enrollmentModel = new EnrollmentModel();

        if ($enrollmentModel->isAlreadyEnrolled($user_id, $course_id)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You are already enrolled in this course.'
            ]);
        }

        $data = [
            'user_id' => $user_id,
            'course_id' => $course_id,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        try {
            if ($enrollmentModel->insert($data)) {
                // Get course details for notification
                $courseModel = new CourseModel();
                $course = $courseModel->find($course_id);
                $courseName = $course ? $course['title'] : 'a course';
                
                // Create notification
                $notificationModel = new NotificationModel();
                $notificationModel->createNotification(
                    $user_id, 
                    "You have successfully enrolled in {$courseName}!"
                );
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Enrollment successful!'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Enrollment failed. Please try again.'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function index()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $enrollmentModel = new EnrollmentModel();
        $courses = $enrollmentModel->getEnrollmentsByUser($userId);
        $role = session()->get('role');

        return view('templates/header', ['role' => $role]) 
             . view('courses/index', ['courses' => $courses]);
    }

    public function getAllCourses()
    {
        $courseModel = new \App\Models\CourseModel();
        $courses = $courseModel->findAll();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $courses
        ]);
    }

    public function manage()
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'teacher') {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        return view('templates/header', ['role' => $role]) . view('teacher/manage_courses');
    }

    public function search()
    {
        $userId = session()->get('user_id');
        $searchTerm = $this->request->getGet('search_term');
        
        $db = \Config\Database::connect();
        $builder = $db->table('enrollments');
        $builder->select('courses.id, courses.title, courses.description');
        $builder->join('courses', 'courses.id = enrollments.course_id', 'left');
        $builder->where('enrollments.user_id', $userId);
        
        if (!empty($searchTerm)) {
            $builder->groupStart();
            $builder->like('courses.title', $searchTerm);
            $builder->orLike('courses.description', $searchTerm);
            $builder->groupEnd();
        }
        
        $courses = $builder->get()->getResultArray();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON($courses);
        }
        
        return view('courses/search_results', ['courses' => $courses, 'searchTerm' => $searchTerm]);
    }
}
