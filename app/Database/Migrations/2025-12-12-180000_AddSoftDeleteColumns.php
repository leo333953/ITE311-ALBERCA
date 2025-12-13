<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteColumns extends Migration
{
    public function up()
    {
        // Add deleted_at column to courses table
        if (!$this->db->fieldExists('deleted_at', 'courses')) {
            $this->forge->addColumn('courses', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'updated_at'
                ]
            ]);
        }

        // Add deleted_at column to enrollments table
        if (!$this->db->fieldExists('deleted_at', 'enrollments')) {
            $this->forge->addColumn('enrollments', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'remarks'
                ]
            ]);
        }

        // Add deleted_at column to materials table
        if (!$this->db->fieldExists('deleted_at', 'materials')) {
            $this->forge->addColumn('materials', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at'
                ]
            ]);
        }

        // Add updated_at column to materials table if it doesn't exist
        if (!$this->db->fieldExists('updated_at', 'materials')) {
            $this->forge->addColumn('materials', [
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'deleted_at'
                ]
            ]);
        }

        // Ensure users table has deleted_at (should already exist)
        if (!$this->db->fieldExists('deleted_at', 'users')) {
            $this->forge->addColumn('users', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                    'after' => 'created_at'
                ]
            ]);
        }
    }

    public function down()
    {
        // Remove deleted_at columns
        $tables = ['courses', 'enrollments', 'materials', 'users'];
        
        foreach ($tables as $table) {
            if ($this->db->fieldExists('deleted_at', $table)) {
                $this->forge->dropColumn($table, 'deleted_at');
            }
        }
        
        // Remove updated_at from materials if we added it
        if ($this->db->fieldExists('updated_at', 'materials')) {
            $this->forge->dropColumn('materials', 'updated_at');
        }
    }
}