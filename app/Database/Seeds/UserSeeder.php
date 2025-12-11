<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'role'       => 'admin',
                'name'       => 'Admin',
                'email'      => 'admin@example.com',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'created_at' => Time::now()
            ],
            [
                'role'       => 'teacher',
                'name'       => 'John Doe',
                'email'      => 'teacher@example.com',
                'password'   => password_hash('teacher123', PASSWORD_DEFAULT),
                'created_at' => Time::now()
            ],
            [
                'role'       => 'student',
                'name'       => 'Jane Smith',
                'email'      => 'student@example.com',
                'password'   => password_hash('student123', PASSWORD_DEFAULT),
                'created_at' => Time::now()
            ],
            // Optional: additional sample users
            [
                'role'       => 'teacher',
                'name'       => 'Alice Brown',
                'email'      => 'alice.teacher@example.com',
                'password'   => password_hash('teacher456', PASSWORD_DEFAULT),
                'created_at' => Time::now()
            ],
            [
                'role'       => 'student',
                'name'       => 'Bob Green',
                'email'      => 'bob.student@example.com',
                'password'   => password_hash('student456', PASSWORD_DEFAULT),
                'created_at' => Time::now()
            ],
        ];

        // Insert all users
        $this->db->table('users')->insertBatch($data);
    }
}
