<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixCoursesInstructorId extends Migration
{
    public function up()
    {
        // Modify instructor_id to allow NULL values
        $fields = [
            'instructor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,  // Allow NULL values
                'default'    => null
            ]
        ];

        $this->forge->modifyColumn('courses', $fields);
    }

    public function down()
    {
        // Revert instructor_id to NOT NULL
        $fields = [
            'instructor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false
            ]
        ];

        $this->forge->modifyColumn('courses', $fields);
    }
}