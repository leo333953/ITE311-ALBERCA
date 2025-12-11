<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;

class Auth extends BaseController
{
    protected $session;
    protected $validation;
    protected $db;
    protected $enrollmentModel;

    public function __construct()
    {
        $this->session = \Config\Services::session();
        $this->validation = \Config\Services::validation();
        $this->db = \Config\Database::connect();
        $this->enrollmentModel = new EnrollmentModel();
    }

    // REGISTER
    public function register()
    {
        helper(['form']);

        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name'             => 'required|min_length[3]|max_length[100]',
                'email'            => 'required|valid_email|is_unique[users.email]',
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'required|matches[password]'
            ];

            if ($this->validate($rules)) {
                $hashedPassword = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

                $userData = [
                    'name'       => $this->request->getPost('name'),
                    'email'      => $this->request->getPost('email'),
                    'password'   => $hashedPassword,
                    'role'       => 'student',
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

                $builder = $this->db->table('users');
                if ($builder->insert($userData)) {
                    $this->session->setFlashdata('success', 'Registration successful! Please login.');
                    return redirect()->to(base_url('login'));
                } else {
                    $this->session->setFlashdata('error', 'Registration failed. Please try again.');
                }
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
        }

        return view('auth/register');
    }

    // LOGIN
    public function login()
    {
        helper(['form']);

        if ($this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('dashboard'));
        }

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required'
            ];

            if ($this->validate($rules)) {
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');

                $builder = $this->db->table('users');
                $user = $builder->where('email', $email)->get()->getRowArray();

                if ($user && password_verify($password, $user['password'])) {
                    $sessionData = [
                        'user_id'    => $user['id'],
                        'name'       => $user['name'],
                        'email'      => $user['email'],
                        'role'       => $user['role'] ?? 'student',
                        'isLoggedIn' => true
                    ];

                    $this->session->set($sessionData);
                    $this->session->setFlashdata('success', 'Welcome back, ' . $user['name'] . '!');

                    return redirect()->to(base_url('dashboard'));
                } else {
                    $this->session->setFlashdata('error', 'Invalid email or password.');
                    return redirect()->to(base_url('login'));
                }
            } else {
                $this->session->setFlashdata('errors', $this->validation->getErrors());
            }
        }

        return view('auth/login');
    }

    // LOGOUT
    public function logout()
    {
        $this->session->destroy();
        return redirect()->to(base_url('login'));
    }

    // DASHBOARD
    public function dashboard()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('error', 'Please log in first.');
        }

        $user_id = $this->session->get('user_id');
        $user_name = $this->session->get('name');
        $user_role = $this->session->get('role');

        // Fetch all courses
        $courses = $this->db->table('courses')
            ->select('id, title, description')
            ->orderBy('title', 'ASC')
            ->get()
            ->getResultArray();

        // Fetch student enrolled courses
        $enrolledCourses = [];
        if ($user_role === 'student') {
            $enrolledCourses = $this->db->table('enrollments')
                ->select('courses.id, courses.title, courses.description')
                ->join('courses', 'enrollments.course_id = courses.id', 'left')
                ->where('enrollments.user_id', $user_id)
                ->get()
                ->getResultArray();
        }

        $enrollmentModel = new EnrollmentModel();
        $enrolledCourses = $enrollmentModel->getEnrollmentsByUser($user_id);

        $materialsModel = new MaterialModel();

        foreach ($enrolledCourses as &$course) {
            $course['materials'] = $materialsModel->getMaterialsByCourse($course['id']);
        }


        $role = session()->get('role');
        $data = [
            'user_name'       => $user_name,
            'user_role'       => $user_role,
            'courses'         => $courses,
            'enrolledCourses' => $enrolledCourses,
            'role' => $role
            
        ];


        return view('auth/dashboard', $data);
    }
}
