<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'course_code',
        'course_name', 
        'title',
        'description',
        'year_level',
        'semester',
        'academic_year',
        'instructor_id',
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Get all active courses
    public function getActiveCourses()
    {
        return $this->where('status', 'Active')
                   ->where('deleted_at IS NULL')
                   ->orderBy('title', 'ASC')
                   ->findAll();
    }

    // Get courses by instructor
    public function getCoursesByInstructor($instructor_id)
    {
        return $this->where('instructor_id', $instructor_id)
                   ->where('deleted_at IS NULL')
                   ->orderBy('title', 'ASC')
                   ->findAll();
    }

    // Get course with instructor details
    public function getCourseWithInstructor($course_id)
    {
        $db = \Config\Database::connect();
        return $db->table('courses c')
            ->select('c.*, u.name as instructor_name, u.email as instructor_email')
            ->join('users u', 'c.instructor_id = u.id', 'left')
            ->where('c.id', $course_id)
            ->where('c.deleted_at IS NULL')
            ->get()
            ->getRowArray();
    }

    // Search courses
    public function searchCourses($searchTerm)
    {
        return $this->groupStart()
                   ->like('title', $searchTerm)
                   ->orLike('description', $searchTerm)
                   ->orLike('course_code', $searchTerm)
                   ->orLike('course_name', $searchTerm)
                   ->groupEnd()
                   ->where('deleted_at IS NULL')
                   ->orderBy('title', 'ASC')
                   ->findAll();
    }
}