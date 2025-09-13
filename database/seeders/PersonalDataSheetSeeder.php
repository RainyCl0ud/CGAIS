<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PersonalDataSheet;
use App\Models\User;

class PersonalDataSheetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all students
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            // Create PDS for each student with sample data
            PersonalDataSheet::create([
                'user_id' => $student->id,
                'birth_date' => fake()->date(),
                'birth_place' => fake()->city(),
                'sex' => fake()->randomElement(['male', 'female']),
                'civil_status' => fake()->randomElement(['single', 'married']),
                'citizenship' => 'Filipino',
                'height' => fake()->numberBetween(150, 180) . ' cm',
                'weight' => fake()->numberBetween(45, 80) . ' kg',
                'blood_type' => fake()->randomElement(['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']),
                'mobile_number' => fake()->phoneNumber(),
                'telephone_number' => fake()->phoneNumber(),
                'permanent_address' => fake()->address(),
                'present_address' => fake()->address(),
                'father_name' => fake()->name('male'),
                'father_occupation' => fake()->jobTitle(),
                'father_contact' => fake()->phoneNumber(),
                'mother_name' => fake()->name('female'),
                'mother_occupation' => fake()->jobTitle(),
                'mother_contact' => fake()->phoneNumber(),
                'guardian_name' => fake()->name(),
                'guardian_relationship' => fake()->randomElement(['Parent', 'Sibling', 'Relative']),
                'guardian_contact' => fake()->phoneNumber(),
                'elementary_school' => fake()->company() . ' Elementary School',
                'elementary_year_graduated' => fake()->year(),
                'high_school' => fake()->company() . ' High School',
                'high_school_year_graduated' => fake()->year(),
                'college' => fake()->company() . ' University',
                'college_year_graduated' => fake()->year(),
                'course' => fake()->randomElement(['BS Information Technology', 'BS Agriculture Technology', 'BS Teachnology in Livelyhood Education ', 'BS Psychology']),
                'year_level' => fake()->randomElement(['1st Year', '2nd Year', '3rd Year', '4th Year']),
                'student_id_number' => fake()->numerify('2023-####'),
                'emergency_contact_name' => fake()->name(),
                'emergency_contact_relationship' => fake()->randomElement(['Parent', 'Sibling', 'Relative', 'Friend']),
                'emergency_contact_number' => fake()->phoneNumber(),
                'emergency_contact_address' => fake()->address(),
                'medical_conditions' => fake()->optional(0.3)->sentence(),
                'allergies' => fake()->optional(0.2)->sentence(),
                'medications' => fake()->optional(0.1)->sentence(),
                'hobbies' => fake()->optional(0.8)->sentence(),
                'interests' => fake()->optional(0.8)->sentence(),
                'goals' => fake()->optional(0.9)->sentence(),
                'concerns' => fake()->optional(0.4)->sentence(),
            ]);
        }
    }
} 