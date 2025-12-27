<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Course;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'name' => 'Bachelor of Science in Information Technology',
                'code' => 'BSIT',
                'description' => 'A comprehensive program covering software development, database management, networking, and IT systems.',
                'is_active' => true,
            ],
            [
                'name' => 'Bachelor of Technology and Livelihood Education',
                'code' => 'BTLED',
                'description' => 'Focuses on technology and livelihood education with emphasis on practical skills and entrepreneurship.',
                'is_active' => true,
            ],
            [
                'name' => 'Bachelor of Agricultural Technology',
                'code' => 'BAT',
                'description' => 'Covers agricultural sciences, technology, and modern farming practices.',
                'is_active' => true,
            ],
            [
                'name' => 'Bachelor of Science in Agriculture Major in Precision Agriculture',
                'code' => 'BSA-PA',
                'description' => 'Specialized program in precision agriculture using modern technology and data-driven farming methods.',
                'is_active' => true,
            ],
            [
                'name' => 'Bachelor of Science in Computer Science',
                'code' => 'BSCS',
                'description' => 'A program focused on computer science fundamentals, algorithms, and software engineering.',
                'is_active' => false,
            ],
            [
                'name' => 'Bachelor of Science in Mathematics',
                'code' => 'BSMATH',
                'description' => 'Covers advanced mathematics, statistics, and their applications in various fields.',
                'is_active' => false,
            ],
        ];

        foreach ($courses as $course) {
            Course::updateOrCreate(
                ['code' => $course['code']],
                $course
            );
        }
    }
}
