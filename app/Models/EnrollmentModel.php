<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];
    public $useTimestamps = false;

    public function enrollUser($data)
    {
        return $this->insert($data);
    }

    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }

    public function getEnrollmentsByUser($user_id)
    {
        return $this->db->table('enrollments')
            ->select('courses.id, courses.title, courses.description')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->where('enrollments.user_id', $user_id)
            ->get()
            ->getResultArray();
    }
}
