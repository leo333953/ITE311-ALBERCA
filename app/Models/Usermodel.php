<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'name',
        'email',
        'password',
        'role',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Get users by role
    public function getUsersByRole($role)
    {
        return $this->where('role', $role)
                   ->where('deleted_at IS NULL')
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    // Get all students
    public function getStudents()
    {
        return $this->getUsersByRole('student');
    }

    // Get all teachers
    public function getTeachers()
    {
        return $this->getUsersByRole('teacher');
    }

    // Get all admins
    public function getAdmins()
    {
        return $this->getUsersByRole('admin');
    }

    // Search users
    public function searchUsers($searchTerm)
    {
        return $this->groupStart()
                   ->like('name', $searchTerm)
                   ->orLike('email', $searchTerm)
                   ->groupEnd()
                   ->where('deleted_at IS NULL')
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    // Check if email exists
    public function emailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email)
                       ->where('deleted_at IS NULL');
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->first() !== null;
    }
}