<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\CourseModel;
use App\Models\UserModel;
use App\Helpers\NotificationHelper;

class Enrollments extends BaseController
{
    protected $enrollmentModel;
    protected $courseModel;
    protected $userModel;
    protected $notificationHelper;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        $this->userModel = new UserModel();
        $this->notificationHelper = new NotificationHelper();
    }

    // Student enrollment form
    public function index()
    {
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to('/login');
        }

        $role = session()->get('role');
        $userEmail = session()->get('email') ?? session()->get('user_email');

        if ($role === 'student') {
            // Show enrollment form and student's enrollments
            $enrollments = [];
            $courses = [];
            
            if ($userEmail) {
                try {
                    $enrollments = $this->enrollmentModel->getStudentEnrollments($userEmail);
                } catch (Exception $e) {
                    log_message('error', 'Error getting student enrollments: ' . $e->getMessage());
                }
            }
            
            // Get available courses
            try {
                $courses = $this->courseModel->findAll();
            } catch (Exception $e) {
                log_message('error', 'Error getting courses: ' . $e->getMessage());
            }
            
            $data = [
                'role' => $role,
                'enrollments' => $enrollments ?? [],
                'courses' => $courses ?? []
            ];
            return view('templates/header', ['role' => $role]) 
                 . view('student/enrollments', $data);
        } elseif ($role === 'teacher') {
            // Show all enrollments for teacher management
            try {
                $allEnrollments = $this->enrollmentModel->getEnrollmentsWithDetails();
                $stats = $this->enrollmentModel->getEnrollmentStats();
            } catch (Exception $e) {
                log_message('error', 'Error getting teacher enrollment data: ' . $e->getMessage());
                $allEnrollments = [];
                $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
            }
            
            $data = [
                'role' => $role,
                'pendingEnrollments' => $allEnrollments, // Using same variable name for compatibility
                'stats' => $stats
            ];
            return view('templates/header', ['role' => $role]) 
                 . view('teacher/enrollments', $data);
        } elseif ($role === 'admin') {
            // Show all enrollments for admin
            try {
                $allEnrollments = $this->enrollmentModel->getEnrollmentsWithDetails();
                $stats = $this->enrollmentModel->getEnrollmentStats();
                $users = $this->userModel->findAll();
            } catch (Exception $e) {
                log_message('error', 'Error getting admin enrollment data: ' . $e->getMessage());
                $allEnrollments = [];
                $stats = ['total' => 0, 'pending' => 0, 'approved' => 0, 'rejected' => 0];
                $users = [];
            }
            
            $data = [
                'role' => $role,
                'allEnrollments' => $allEnrollments,
                'stats' => $stats,
                'users' => $users
            ];
            return view('templates/header', ['role' => $role]) 
                 . view('admin/enrollments', $data);
        }

        return redirect()->to('/dashboard');
    }

    // Submit enrollment (Student)
    public function submit()
    {
        // Add debugging
        log_message('info', 'Enrollment submit - Session: ' . json_encode([
            'isLoggedIn' => session()->get('isLoggedIn'),
            'role' => session()->get('role'),
            'user_id' => session()->get('user_id'),
            'email' => session()->get('email')
        ]));
        log_message('info', 'Enrollment submit - POST data: ' . json_encode($this->request->getPost()));
        
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'student') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized. Please login as a student.']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'student_id' => 'required|min_length[3]',
            'full_name' => 'required|min_length[3]',
            'course_program' => 'required',
            'year_level' => 'required',
            'email' => 'required|valid_email',
            'phone_number' => 'required',
            'address' => 'required',
            'date_of_birth' => 'required|valid_date'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $email = $this->request->getPost('email');
        
        $studentId = $this->request->getPost('student_id');
        
        // Check if student ID is already used
        if ($this->enrollmentModel->where('student_number', $studentId)->first()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'This Student ID is already registered. Please use a different Student ID.'
            ]);
        }
        
        // Check if email is already enrolled
        if ($this->enrollmentModel->isStudentEnrolled($email)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You have already submitted an enrollment application with this email.'
            ]);
        }

        $data = [
            // New enrollment system fields
            'student_number' => $studentId,
            'full_name' => $this->request->getPost('full_name'),
            'course_year' => $this->request->getPost('course_program') . ' - ' . $this->request->getPost('year_level'),
            'email' => $this->request->getPost('email'),
            'phone_number' => $this->request->getPost('phone_number'),
            'address' => $this->request->getPost('address'),
            'date_of_birth' => $this->request->getPost('date_of_birth'),
            'guardian_name' => $this->request->getPost('guardian_name'),
            'guardian_phone' => $this->request->getPost('guardian_phone'),
            'guardian_email' => $this->request->getPost('guardian_email'),
            'status' => 'pending',
            'submitted_at' => date('Y-m-d H:i:s'),
            
            // Required legacy fields
            'user_id' => session()->get('user_id') ?? 0,
            'course_id' => 1, // Default course ID since we're using course_program now
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        try {
            if ($this->enrollmentModel->insert($data)) {
                // Send notification about new enrollment
                $this->notificationHelper->notifyEnrollmentSubmitted(
                    $this->request->getPost('full_name'),
                    $this->request->getPost('course_program'),
                    $studentId
                );
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Enrollment submitted successfully. Your Student ID is: ' . $studentId . '. Status: Pending approval.'
                ]);
            } else {
                // Get database errors
                $errors = $this->enrollmentModel->errors();
                log_message('error', 'Enrollment insert failed: ' . json_encode($errors));
                
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to submit enrollment: ' . (empty($errors) ? 'Unknown database error' : implode(', ', $errors))
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Enrollment submission exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    // Approve enrollment (Teacher/Admin)
    public function approve($id)
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true || !in_array($role, ['teacher', 'admin'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $enrollment = $this->enrollmentModel->find($id);
        if (!$enrollment) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enrollment not found'
            ]);
        }

        if ($enrollment['status'] !== 'pending') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enrollment is not pending'
            ]);
        }

        $approvedBy = session()->get('user_id');
        $remarks = $this->request->getPost('remarks') ?? 'Enrollment approved';
        
        if ($this->enrollmentModel->approveEnrollment($id, $approvedBy, $remarks)) {
            // Send notification about approval
            $approverName = $this->notificationHelper->getUserName($approvedBy);
            $this->notificationHelper->notifyEnrollmentApproved(
                $enrollment['student_number'],
                $approverName,
                $enrollment['course_year']
            );
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Enrollment approved successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to approve enrollment'
        ]);
    }

    // Reject enrollment (Teacher/Admin)
    public function reject($id)
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true || !in_array($role, ['teacher', 'admin'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $enrollment = $this->enrollmentModel->find($id);
        if (!$enrollment) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enrollment not found'
            ]);
        }

        if ($enrollment['status'] !== 'pending') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enrollment is not pending'
            ]);
        }

        $approvedBy = session()->get('user_id');
        $reason = $this->request->getPost('reason') ?? 'Enrollment rejected';
        
        if ($this->enrollmentModel->rejectEnrollment($id, $approvedBy, $reason)) {
            // Send notification about rejection
            $rejecterName = $this->notificationHelper->getUserName($approvedBy);
            $this->notificationHelper->notifyEnrollmentRejected(
                $enrollment['student_number'],
                $rejecterName,
                $enrollment['course_year'],
                $reason
            );
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Enrollment rejected successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to reject enrollment'
        ]);
    }

    // Get enrollment details
    public function get($id)
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $enrollment = $this->enrollmentModel->getEnrollmentsWithDetails();
        $enrollment = array_filter($enrollment, function($e) use ($id) {
            return $e['id'] == $id;
        });

        if (empty($enrollment)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enrollment not found'
            ]);
        }

        $enrollment = array_values($enrollment)[0];

        // Students can only see their own enrollments
        if ($role === 'student' && $enrollment['email'] != session()->get('email')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'enrollment' => $enrollment
        ]);
    }
    
    // Search enrollments (Admin/Teacher)
    public function search()
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true || !in_array($role, ['admin', 'teacher'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $searchTerm = $this->request->getGet('search');
        $statusFilter = $this->request->getGet('status');
        $courseFilter = $this->request->getGet('course');
        $sortBy = $this->request->getGet('sort_by') ?: 'submitted_at';
        
        try {
            $builder = $this->enrollmentModel->builder();
            $builder->select('enrollments.*, approver.name as approved_by_name')
                   ->join('users approver', 'enrollments.approved_by = approver.id', 'left')
                   ->where('enrollments.deleted_at IS NULL'); // Exclude soft deleted records
            
            // Apply search filter
            if (!empty($searchTerm)) {
                $builder->groupStart()
                    ->like('enrollments.full_name', $searchTerm)
                    ->orLike('enrollments.student_number', $searchTerm)
                    ->orLike('enrollments.email', $searchTerm)
                    ->orLike('enrollments.course_year', $searchTerm)
                    ->groupEnd();
            }
            
            // Apply status filter
            if (!empty($statusFilter)) {
                $builder->where('enrollments.status', $statusFilter);
            }
            
            // Apply course filter
            if (!empty($courseFilter)) {
                $builder->like('enrollments.course_year', $courseFilter);
            }
            
            // Apply sorting
            switch ($sortBy) {
                case 'name':
                    $builder->orderBy('enrollments.full_name', 'ASC');
                    break;
                case 'status':
                    $builder->orderBy('enrollments.status', 'ASC');
                    break;
                case 'course':
                    $builder->orderBy('enrollments.course_year', 'ASC');
                    break;
                case 'submitted_at':
                default:
                    $builder->orderBy('enrollments.submitted_at', 'DESC');
                    break;
            }
            
            $enrollments = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'enrollments' => $enrollments,
                'count' => count($enrollments),
                'search_term' => $searchTerm,
                'filters' => [
                    'status' => $statusFilter,
                    'course' => $courseFilter,
                    'sort_by' => $sortBy
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Enrollment search error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }

    // Delete enrollment (Teacher/Admin) - Soft Delete
    public function delete($id)
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true || !in_array($role, ['teacher', 'admin'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $enrollment = $this->enrollmentModel->find($id);
        if (!$enrollment) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Enrollment not found'
            ]);
        }

        // Soft delete the enrollment
        if ($this->enrollmentModel->delete($id)) {
            // Send notification about deletion
            $deleterName = $this->notificationHelper->getUserName(session()->get('user_id'));
            $this->notificationHelper->notifyEnrollmentDeleted(
                $enrollment['full_name'],
                $deleterName,
                $enrollment['course_year']
            );
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Enrollment deleted successfully (can be recovered by admin)'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete enrollment'
        ]);
    }

    // Debug method to test enrollment submission
    public function debug()
    {
        if (session()->get('isLoggedIn') !== true) {
            return "Please login first. <a href='" . base_url('login') . "'>Login</a>";
        }
        
        $output = "<h1>Enrollment Debug</h1>";
        $output .= "<h2>Session Data:</h2>";
        $output .= "<pre>" . json_encode(session()->get(), JSON_PRETTY_PRINT) . "</pre>";
        
        $output .= "<h2>Test Enrollment Submission:</h2>";
        $output .= "<form method='post' action='" . base_url('enrollments/submit') . "'>";
        $output .= "<input type='hidden' name='student_id' value='2024-TEST-001'>";
        $output .= "<input type='hidden' name='full_name' value='Test Student'>";
        $output .= "<input type='hidden' name='course_program' value='Bachelor of Science in Information Technology'>";
        $output .= "<input type='hidden' name='year_level' value='1st Year'>";
        $output .= "<input type='hidden' name='email' value='test@student.com'>";
        $output .= "<input type='hidden' name='phone_number' value='1234567890'>";
        $output .= "<input type='hidden' name='address' value='Test Address'>";
        $output .= "<input type='hidden' name='date_of_birth' value='2000-01-01'>";
        $output .= "<button type='submit'>Test Submit</button>";
        $output .= "</form>";
        
        return $output;
    }
}