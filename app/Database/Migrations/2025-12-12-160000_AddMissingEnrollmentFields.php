<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMissingEnrollmentFields extends Migration
{
    public function up()
    {
        // Check if fields exist before adding them
        $db = \Config\Database::connect();
        
        $fields = [];
        
        // Add status field if it doesn't exist
        if (!$db->fieldExists('status', 'enrollments')) {
            $fields['status'] = [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
                'after'      => 'enrollment_date'
            ];
        }
        
        // Add approved_by field if it doesn't exist
        if (!$db->fieldExists('approved_by', 'enrollments')) {
            $fields['approved_by'] = [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status'
            ];
        }
        
        // Add approved_at field if it doesn't exist
        if (!$db->fieldExists('approved_at', 'enrollments')) {
            $fields['approved_at'] = [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_by'
            ];
        }
        
        // Add rejection_reason field if it doesn't exist
        if (!$db->fieldExists('rejection_reason', 'enrollments')) {
            $fields['rejection_reason'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'approved_at'
            ];
        }
        
        // Add student_info field if it doesn't exist
        if (!$db->fieldExists('student_info', 'enrollments')) {
            $fields['student_info'] = [
                'type' => 'JSON',
                'null' => true,
                'after' => 'rejection_reason'
            ];
        }
        
        // Add student_details_id field if it doesn't exist
        if (!$db->fieldExists('student_details_id', 'enrollments')) {
            $fields['student_details_id'] = [
                'type'       => 'INT',
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'student_info'
            ];
        }
        
        // Only add fields if there are any to add
        if (!empty($fields)) {
            $this->forge->addColumn('enrollments', $fields);
        }
    }

    public function down()
    {
        // Remove the added fields
        $fieldsToRemove = [
            'status', 'approved_by', 'approved_at', 'rejection_reason', 
            'student_info', 'student_details_id'
        ];
        
        $db = \Config\Database::connect();
        $existingFields = [];
        
        foreach ($fieldsToRemove as $field) {
            if ($db->fieldExists($field, 'enrollments')) {
                $existingFields[] = $field;
            }
        }
        
        if (!empty($existingFields)) {
            $this->forge->dropColumn('enrollments', $existingFields);
        }
    }
}