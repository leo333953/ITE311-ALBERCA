<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;
use App\Helpers\NotificationHelper;

class Course extends BaseController
{
    public function enroll()
    {
        // Add debugging
        log_message('info', 'Enrollment attempt - Session: ' . json_encode([
            'isLoggedIn' => session()->get('isLoggedIn'),
            'user_id' => session()->get('user_id'),
            'role' => session()->get('role')
        ]));
        log_message('info', 'Enrollment attempt - POST data: ' . json_encode($this->request->getPost()));
        
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
            'enrollment_date' => date('Y-m-d H:i:s'),
            'status' => 'approved'  // Direct enrollment is immediately approved
        ];

        log_message('info', 'Enrollment data to insert: ' . json_encode($data));

        try {
            if ($enrollmentModel->insert($data)) {
                log_message('info', 'Enrollment successful with ID: ' . $enrollmentModel->getInsertID());
                
                // Get course details for notification
                $courseModel = new CourseModel();
                $course = $courseModel->find($course_id);
                $courseName = $course ? $course['title'] : 'a course';
                
                // Create notification (skip if NotificationModel doesn't exist)
                try {
                    $notificationModel = new NotificationModel();
                    $notificationModel->createNotification(
                        $user_id, 
                        "You have successfully enrolled in {$courseName}!"
                    );
                } catch (\Exception $notifError) {
                    log_message('warning', 'Notification creation failed: ' . $notifError->getMessage());
                }
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Enrollment successful!'
                ]);
            } else {
                $errors = $enrollmentModel->errors();
                log_message('error', 'Enrollment insert failed - Model errors: ' . json_encode($errors));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Enrollment failed: ' . (empty($errors) ? 'Unknown database error' : implode(', ', $errors))
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Enrollment exception: ' . $e->getMessage());
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

        $role = session()->get('role');
        
        if ($role === 'admin') {
            // Admin sees course catalog management at /courses
            $courseModel = new CourseModel();
            $courses = $courseModel->findAll();
            
            $data = [
                'role' => $role,
                'courses' => $courses
            ];
            
            return view('templates/header', ['role' => $role]) 
                 . view('admin/courses', $data);
                 
        } elseif ($role === 'teacher') {
            // Teachers should use /courses/manage instead
            return redirect()->to('/courses/manage');
                 
                 
        } else {
            // Students see their enrolled courses
            $userId = session()->get('user_id');
            $enrollmentModel = new EnrollmentModel();
            
            // Get enrolled courses - check for both 'enrolled' and 'approved' status
            $db = \Config\Database::connect();
            $courses = $db->table('enrollments')
                ->select('courses.id, courses.title, courses.description')
                ->join('courses', 'courses.id = enrollments.course_id', 'left')
                ->where('enrollments.user_id', $userId)
                ->whereIn('enrollments.status', ['enrolled', 'approved'])
                ->where('enrollments.deleted_at IS NULL')
                ->get()
                ->getResultArray();

            return view('templates/header', ['role' => $role]) 
                 . view('courses/index', ['courses' => $courses]);
        }
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

        // Teacher course catalog management - same functionality as admin
        $courseModel = new CourseModel();
        $courses = $courseModel->findAll();
        
        $data = [
            'role' => 'teacher',
            'courses' => $courses
        ];
        
        return view('templates/header', ['role' => 'teacher']) 
             . view('teacher/courses', $data);
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
        $builder->whereIn('enrollments.status', ['enrolled', 'approved']); // Show enrolled and approved enrollments
        
        if (!empty($searchTerm)) {
            $builder->groupStart();
            $builder->like('courses.title', $searchTerm);
            $builder->orLike('courses.description', $searchTerm);
            $builder->groupEnd();
        }
        
        $courses = $builder->get()->getResultArray();
        
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'courses' => $courses,
                'count' => count($courses),
                'search_term' => $searchTerm
            ]);
        }
        
        return view('courses/search_results', ['courses' => $courses, 'searchTerm' => $searchTerm]);
    }
    
    // Admin/Teacher: Create new course
    public function create()
    {
        // Add debugging
        log_message('info', 'Course create - Session: ' . json_encode([
            'isLoggedIn' => session()->get('isLoggedIn'),
            'role' => session()->get('role'),
            'user_id' => session()->get('user_id')
        ]));
        log_message('info', 'Course create - POST data: ' . json_encode($this->request->getPost()));
        
        if (session()->get('isLoggedIn') !== true || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'course_code' => 'required|min_length[3]|max_length[20]',
            'course_name' => 'required|min_length[3]',
            'title' => 'required|min_length[3]',
            'description' => 'required|min_length[10]',
            'year_level' => 'required|in_list[1st Year,2nd Year,3rd Year,4th Year]',
            'semester' => 'required|in_list[1st Semester,2nd Semester,Summer]',
            'academic_year' => 'required|min_length[9]',
            'instructor_id' => 'permit_empty|integer',
            'status' => 'required|in_list[Active,Inactive,Archived]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('error', 'Course create validation failed: ' . json_encode($validation->getErrors()));
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        try {
            $courseModel = new CourseModel();
            $data = [
                'course_code' => $this->request->getPost('course_code'),
                'course_name' => $this->request->getPost('course_name'),
                'title' => $this->request->getPost('title'),
                'description' => $this->request->getPost('description'),
                'year_level' => $this->request->getPost('year_level'),
                'semester' => $this->request->getPost('semester'),
                'academic_year' => $this->request->getPost('academic_year'),
                'instructor_id' => $this->request->getPost('instructor_id') ?: null,
                'status' => $this->request->getPost('status')
            ];
            
            log_message('info', 'Course create - Data to insert: ' . json_encode($data));

            if ($courseModel->insert($data)) {
                log_message('info', 'Course created successfully with ID: ' . $courseModel->getInsertID());
                
                // Send notification about new course
                $notificationHelper = new NotificationHelper();
                $notificationHelper->notifyCourseCreated(
                    $data['title'],
                    session()->get('user_id')
                );
                
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Course created successfully'
                ]);
            } else {
                $errors = $courseModel->errors();
                log_message('error', 'Course create failed - Model errors: ' . json_encode($errors));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Failed to create course: ' . (empty($errors) ? 'Unknown database error' : implode(', ', $errors))
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Course create exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
    
    // Admin/Teacher: Update course
    public function update($id)
    {
        if (session()->get('isLoggedIn') !== true || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($id);
        
        if (!$course) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course not found'
            ]);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'course_code' => 'required|min_length[3]|max_length[20]',
            'course_name' => 'required|min_length[3]',
            'title' => 'required|min_length[3]',
            'description' => 'required|min_length[10]',
            'year_level' => 'required|in_list[1st Year,2nd Year,3rd Year,4th Year]',
            'semester' => 'required|in_list[1st Semester,2nd Semester,Summer]',
            'academic_year' => 'required|min_length[9]',
            'instructor_id' => 'permit_empty|integer',
            'status' => 'required|in_list[Active,Inactive,Archived]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'course_code' => $this->request->getPost('course_code'),
            'course_name' => $this->request->getPost('course_name'),
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'year_level' => $this->request->getPost('year_level'),
            'semester' => $this->request->getPost('semester'),
            'academic_year' => $this->request->getPost('academic_year'),
            'instructor_id' => $this->request->getPost('instructor_id') ?: null,
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($courseModel->update($id, $data)) {
            // Send notification about course update
            $notificationHelper = new NotificationHelper();
            $notificationHelper->notifyCourseUpdated(
                $data['title'],
                session()->get('user_id')
            );
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Course updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update course'
        ]);
    }
    
    // Admin/Teacher: Delete course
    public function delete($id)
    {
        if (session()->get('isLoggedIn') !== true || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($id);
        
        if (!$course) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Course not found'
            ]);
        }

        if ($courseModel->delete($id)) {
            // Send notification about course deletion
            $notificationHelper = new NotificationHelper();
            $notificationHelper->notifyCourseDeleted(
                $course['title'],
                session()->get('user_id')
            );
            
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Course deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete course'
        ]);
    }
    
    // Admin/Teacher: Get course details
    public function get($id)
    {
        if (session()->get('isLoggedIn') !== true || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $courseModel = new CourseModel();
        $course = $courseModel->find($id);
        
        if ($course) {
            return $this->response->setJSON([
                'status' => 'success',
                'course' => $course
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Course not found'
        ]);
    }
    
    // Get all teachers for dropdown
    public function getTeachers()
    {
        if (session()->get('isLoggedIn') !== true || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        try {
            $db = \Config\Database::connect();
            $teachers = $db->table('users')
                ->select('id, name, email')
                ->where('role', 'teacher')
                ->where('deleted_at IS NULL')
                ->orderBy('name', 'ASC')
                ->get()
                ->getResultArray();

            return $this->response->setJSON([
                'status' => 'success',
                'teachers' => $teachers
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to load teachers: ' . $e->getMessage()
            ]);
        }
    }

    // Admin/Teacher: Advanced search for courses
    public function searchAdmin()
    {
        if (session()->get('isLoggedIn') !== true || !in_array(session()->get('role'), ['admin', 'teacher'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $searchTerm = $this->request->getGet('search');
        $instructorFilter = $this->request->getGet('instructor_filter');
        $sortBy = $this->request->getGet('sort_by') ?: 'title';
        
        try {
            $courseModel = new CourseModel();
            $builder = $courseModel->builder();
            
            // Apply search filter
            if (!empty($searchTerm)) {
                $builder->groupStart()
                    ->like('title', $searchTerm)
                    ->orLike('description', $searchTerm)
                    ->groupEnd();
            }
            
            // Apply instructor filter
            if ($instructorFilter === 'assigned') {
                $builder->where('instructor_id IS NOT NULL');
            } elseif ($instructorFilter === 'unassigned') {
                $builder->where('instructor_id IS NULL');
            }
            
            // Apply sorting
            switch ($sortBy) {
                case 'created_at':
                    $builder->orderBy('created_at', 'DESC');
                    break;
                case 'instructor_id':
                    $builder->orderBy('instructor_id', 'ASC');
                    break;
                case 'title':
                default:
                    $builder->orderBy('title', 'ASC');
                    break;
            }
            
            $courses = $builder->get()->getResultArray();
            
            return $this->response->setJSON([
                'status' => 'success',
                'courses' => $courses,
                'count' => count($courses),
                'search_term' => $searchTerm,
                'filters' => [
                    'instructor_filter' => $instructorFilter,
                    'sort_by' => $sortBy
                ]
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Course search error: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Search failed: ' . $e->getMessage()
            ]);
        }
    }
}
