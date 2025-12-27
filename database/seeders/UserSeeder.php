<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first course for student assignment
        $course = Course::first();

        // Student User
        User::firstOrCreate(
            ['email' => 'student@gmail.com'],
            [
                'first_name' => 'John',
                'middle_name' => 'Doe',
                'last_name' => 'Student',
                'email' => 'student@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'student',
                'student_id' => '20250001',
                'course_id' => $course ? $course->id : null,
                'year_level' => '1st Year',
                'email_verified_at' => now(),
            ]
        );

        // Faculty User
        User::firstOrCreate(
            ['email' => 'faculty@gmail.com'],
            [
                'first_name' => 'Jane',
                'middle_name' => 'Smith',
                'last_name' => 'Faculty',
                'email' => 'faculty@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'faculty',
                'faculty_id' => 'FAC001',
                'email_verified_at' => now(),
            ]
        );

        // Counselor User
        User::firstOrCreate(
            ['email' => 'counselor@gmail.com'],
            [
                'first_name' => 'Alice',
                'middle_name' => 'Johnson',
                'last_name' => 'Counselor',
                'email' => 'counselor@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'counselor',
                'faculty_id' => 'FAC002',
                'availability_status' => 'AVAILABLE',
                'email_verified_at' => now(),
            ]
        );

        // Assistant User
        User::firstOrCreate(
            ['email' => 'assistant@gmail.com'],
            [
                'first_name' => 'Bob',
                'middle_name' => 'Wilson',
                'last_name' => 'Assistant',
                'email' => 'assistant@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'assistant',
                'faculty_id' => 'FAC003',
                'email_verified_at' => now(),
            ]
        );

        // Staff User
        User::firstOrCreate(
            ['email' => 'staff@gmail.com'],
            [
                'first_name' => 'Charlie',
                'middle_name' => 'Brown',
                'last_name' => 'Staff',
                'email' => 'staff@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'staff_id' => 'STAFF001',
                'email_verified_at' => now(),
            ]
        );
    }
}
