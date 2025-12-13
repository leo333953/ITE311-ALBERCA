<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';
    
    protected $allowedFields = [
        'title', 
        'description', 
        'instructor_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Get all courses with instructor details
    public function getCoursesWithInstructor()
    {
        return $this->db->table('courses c')
            ->select('c.*, u.name as instructor_name')
            ->join('users u', 'c.instructor_id = u.id', 'left')
            ->get()
            ->getResultArray();
    }
}