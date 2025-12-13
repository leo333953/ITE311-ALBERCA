<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnrollmentSystemFields extends Migration
{
    public function up()
    {
        // Add missing fields to existing enrollments table
        $fields = [
            'student_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'id'
            ],
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'student_number'
            ],
            'course_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'full_name'
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'course_year'
            ],
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'email'
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'phone_number'
            ],
            'date_of_birth' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'address'
            ],
            'guardian_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'date_of_birth'
            ],
            'guardian_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'guardian_name'
            ],
            'guardian_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'guardian_phone'
            ],
            'submitted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'guardian_email'
            ],
            'processed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'approved_at'
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'processed_at'
            ]
        ];

        // Add new columns to existing enrollments table
        $this->forge->addColumn('enrollments', $fields);

        // Create system settings table for enrollment configuration
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'unique'     => true,
            ],
            'setting_value' => [
                'type' => 'TEXT',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('system_settings');

        // Insert default settings
        $this->db->table('system_settings')->insertBatch([
            [
                'setting_key' => 'current_school_year',
                'setting_value' => '2024-2025',
                'description' => 'Current academic school year',
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'current_semester',
                'setting_value' => '1st Semester',
                'description' => 'Current semester',
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'enrollment_open',
                'setting_value' => '1',
                'description' => 'Is enrollment currently open (1=yes, 0=no)',
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }

    public function down()
    {
        // Remove added columns from enrollments table
        $this->forge->dropColumn('enrollments', [
            'student_number', 'full_name', 'course_year', 'email', 'phone_number',
            'address', 'date_of_birth', 'guardian_name', 'guardian_phone', 
            'guardian_email', 'submitted_at', 'processed_at', 'remarks'
        ]);
        
        $this->forge->dropTable('system_settings');
    }
}