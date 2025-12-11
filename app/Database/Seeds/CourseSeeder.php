<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Introduction to Fitness Training',
                'description' => 'Covers basic fitness principles, warm-up routines, and exercise safety.',
                'instructor_id' => 1, 
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Advanced Strength Conditioning',
                'description' => 'Focuses on strength, endurance, and resistance training techniques.',
                'instructor_id' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Yoga and Flexibility',
                'description' => 'Teaches yoga postures, breathing techniques, and flexibility improvement.',
                'instructor_id' => 2, 
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Nutrition and Wellness',
                'description' => 'Explores proper diet planning and nutrition for optimal health and performance.',
                'instructor_id' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
