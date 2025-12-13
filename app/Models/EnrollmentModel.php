<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table      = 'enrollments';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes = true;
    protected $deletedField = 'deleted_at';

    protected $allowedFields = [
        'user_id',
        'course_id',
        'enrollment_date',
        'status',
        'student_info',
        'student_details_id',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'student_number',
        'full_name',
        'course_year',
        'email',
        'phone_number',
        'address',
        'date_of_birth',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'submitted_at',
        'processed_at',
        'remarks',
        'deleted_at'
    ];

    protected $useTimestamps = false;

    // Get enrollments with user details (excludes soft deleted records)
    public function getEnrollmentsWithDetails($status = null)
    {
        $builder = $this->db->table('enrollments e')
            ->select('e.*, approver.name as approved_by_name')
            ->join('users approver', 'e.approved_by = approver.id', 'left')
            ->where('e.deleted_at IS NULL'); // Exclude soft deleted records
            
        // Order by submitted_at if it exists, otherwise by id
        if ($this->db->fieldExists('submitted_at', 'enrollments')) {
            $builder->orderBy('e.submitted_at', 'DESC');
        } else {
            $builder->orderBy('e.id', 'DESC');
        }

        if ($status) {
            $builder->where('e.status', $status);
        }

        return $builder->get()->getResultArray();
    }

    // Get enrollments for a specific student by email (excludes soft deleted records)
    public function getStudentEnrollments($email)
    {
        if (!$email) {
            return [];
        }
        
        $builder = $this->db->table('enrollments e')
            ->select('e.*, approver.name as approved_by_name')
            ->join('users approver', 'e.approved_by = approver.id', 'left')
            ->where('e.email', $email)
            ->where('e.deleted_at IS NULL'); // Exclude soft deleted records
            
        // Order by submitted_at if it exists, otherwise by id
        if ($this->db->fieldExists('submitted_at', 'enrollments')) {
            $builder->orderBy('e.submitted_at', 'DESC');
        } else {
            $builder->orderBy('e.id', 'DESC');
        }
        
        return $builder->get()->getResultArray();
    }

    // Check if student is already enrolled with same email
    public function isStudentEnrolled($email)
    {
        return $this->where('email', $email)
                   ->where('status !=', 'rejected')
                   ->first() !== null;
    }

    // Approve enrollment
    public function approveEnrollment($enrollmentId, $approvedBy, $remarks = null)
    {
        return $this->update($enrollmentId, [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'processed_at' => date('Y-m-d H:i:s'),
            'remarks' => $remarks ?? 'Enrollment approved',
            'rejection_reason' => null
        ]);
    }

    // Reject enrollment
    public function rejectEnrollment($enrollmentId, $approvedBy, $reason = null)
    {
        return $this->update($enrollmentId, [
            'status' => 'rejected',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'processed_at' => date('Y-m-d H:i:s'),
            'rejection_reason' => $reason ?? 'Enrollment rejected',
            'remarks' => $reason ?? 'Enrollment rejected'
        ]);
    }

    // Get enrollment statistics
    public function getEnrollmentStats()
    {
        return [
            'total' => $this->countAll(),
            'pending' => $this->where('status', 'pending')->countAllResults(false),
            'approved' => $this->where('status', 'approved')->countAllResults(false),
            'rejected' => $this->where('status', 'rejected')->countAllResults()
        ];
    }

    // Generate unique student number
    public function generateStudentNumber()
    {
        $year = date('Y');
        $lastEnrollment = $this->where('student_number IS NOT NULL')
                              ->orderBy('id', 'DESC')
                              ->first();
        
        if ($lastEnrollment && $lastEnrollment['student_number']) {
            $lastNumber = intval(substr($lastEnrollment['student_number'], -4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $year . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}