<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return redirect()->to('/login');
        }

        $data = [
            'role' => session()->get('role'),
            'users' => $this->userModel->findAll(),
            'totalUsers' => $this->userModel->countAll(),
            'adminCount' => $this->userModel->where('role', 'admin')->countAllResults(false),
            'teacherCount' => $this->userModel->where('role', 'teacher')->countAllResults(false),
            'studentCount' => $this->userModel->where('role', 'student')->countAllResults()
        ];

        return view('templates/header', ['role' => $data['role']]) 
             . view('admin/users', $data);
    }

    public function create()
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
            'role' => 'required|in_list[admin,teacher,student]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => $this->request->getPost('password'),
            'role' => $this->request->getPost('role'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->insert($data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User created successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to create user'
        ]);
    }

    public function update($id)
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $validation = \Config\Services::validation();
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
            'role' => 'required|in_list[admin,teacher,student]'
        ];

        // Only validate password if it's being changed
        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
        }

        $validation->setRules($rules);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'role' => $this->request->getPost('role')
        ];

        // Only update password if provided
        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        if ($this->userModel->update($id, $data)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User updated successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to update user'
        ]);
    }

    public function delete($id)
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        // Prevent deleting yourself
        if ($id == session()->get('user_id')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'You cannot delete your own account'
            ]);
        }

        if ($this->userModel->delete($id)) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Failed to delete user'
        ]);
    }

    public function get($id)
    {
        if(session()->get('isLoggedIn') !== true || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $user = $this->userModel->find($id);
        
        if ($user) {
            return $this->response->setJSON([
                'status' => 'success',
                'user' => $user
            ]);
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'User not found'
        ]);
    }
}

