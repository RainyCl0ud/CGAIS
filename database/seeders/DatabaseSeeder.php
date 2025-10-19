<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Schedule;
use App\Models\ValidId;
use App\Models\Appointment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test users
        $student = User::create([
            'first_name' => 'John',
            'middle_name' => 'Patambag',
            'last_name' => 'Algabre',
            'name_extension' => '',
            'email' => 'algabrejohn097@gmail.com',
            'role' => 'student',
            'student_id' => '2022308446',
            'faculty_id' => null,
            'password' => bcrypt('admin'),
        ]);

        $counselor = User::create([
            'first_name' => 'Karren',
            'middle_name' => 'Linaac',
            'last_name' => 'Belarmino',
            'name_extension' => null,
            'email' => 'algabrejohn02@gmail.com',
            'role' => 'counselor',
            'student_id' => null,
            'faculty_id' => '10101010',
            'password' => bcrypt('admin'),
        ]);

        $assistant = User::create([
            'first_name' => 'John',
            'middle_name' => 'Clinton',
            'last_name' => 'Labadan',
            'name_extension' => null,
            'email' => 'assistant@gmail.com',
            'role' => 'assistant',
            'student_id' => null,
            'faculty_id' => '10101020',
            'password' => bcrypt('admin'),
        ]);

        $faculty = User::create([
            'first_name' => 'Jasmin',
            'middle_name' => 'Torres',
            'last_name' => 'Caipang',
            'name_extension' => null,
            'email' => 'faculty@gmail.com',
            'role' => 'faculty',
            'student_id' => null,
            'faculty_id' => '3245058',
            'password' => bcrypt('admin'),
        ]);

        $staff = User::create([
            'first_name' => 'Staff',
            'middle_name' => 'Member',
            'last_name' => 'One',
            'name_extension' => null,
            'email' => 'staff@gmail.com',
            'role' => 'staff',
            'student_id' => null,
            'faculty_id' => null,
            'staff_id' => 'STAFF001',
            'password' => bcrypt('admin'),
        ]);

        // Create valid IDs for testing - Updated to match new user data
        ValidId::create([
            'id_code' => '2022308446',
            'type' => 'student',
            'is_used' => true,
            'email' => 'student@gmail.com',
        ]);

        ValidId::create([
            'id_code' => '10101010',
            'type' => 'faculty',
            'is_used' => true,
            'email' => 'counselor@gmail.com',
        ]);

        ValidId::create([
            'id_code' => '10101020',
            'type' => 'faculty',
            'is_used' => true,
            'email' => 'assistant@gmail.com',
        ]);

        ValidId::create([
            'id_code' => '3245058',
            'type' => 'faculty',
            'is_used' => true,
            'email' => 'faculty@gmail.com',
        ]);

        ValidId::create([
            'id_code' => 'STAFF001',
            'type' => 'faculty',
            'is_used' => true,
            'email' => 'staff@gmail.com',
        ]);

        // Create additional valid IDs for new registrations
        for ($i = 1; $i <= 10; $i++) {
            $studentId = '2022' . str_pad($i, 7, '0', STR_PAD_LEFT);
            ValidId::create([
                'id_code' => $studentId,
                'type' => 'student',
                'is_used' => false,
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            $facultyId = '10' . str_pad($i, 6, '0', STR_PAD_LEFT);
            ValidId::create([
                'id_code' => $facultyId,
                'type' => 'faculty',
                'is_used' => false,
            ]);
        }

        // Create counselor schedule
        Schedule::create([
            'counselor_id' => $counselor->id,
            'day_of_week' => 'monday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'max_appointments' => 3,
            'is_available' => true,
        ]);

        Schedule::create([
            'counselor_id' => $counselor->id,
            'day_of_week' => 'tuesday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'max_appointments' => 3,
            'is_available' => true,
        ]);

        Schedule::create([
            'counselor_id' => $counselor->id,
            'day_of_week' => 'wednesday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'max_appointments' => 3,
            'is_available' => true,
        ]);

        Schedule::create([
            'counselor_id' => $counselor->id,
            'day_of_week' => 'thursday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'max_appointments' => 3,
            'is_available' => true,
        ]);

        Schedule::create([
            'counselor_id' => $counselor->id,
            'day_of_week' => 'friday',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'max_appointments' => 3,
            'is_available' => true,
        ]);

        // Create sample appointments
        $this->call([
            AppointmentSeeder::class,
            PersonalDataSheetSeeder::class,
            FeedbackFormSeeder::class,
        ]);
    }
}
