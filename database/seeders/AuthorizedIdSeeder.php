<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AuthorizedId;
use App\Models\User;

class AuthorizedIdSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get a counselor user to set as registered_by
        $counselor = User::where('role', 'counselor')->first();
        
        if (!$counselor) {
            // Create a default counselor if none exists
            $counselor = User::create([
                'first_name' => 'Default',
                'last_name' => 'Counselor',
                'email' => 'counselor@example.com',
                'password' => bcrypt('password'),
                'role' => 'counselor',
            ]);
        }

        // Sample Student IDs
        $studentIds = [
            '2021-0001',
            '2021-0002',
            '2021-0003',
            '2021-0004',
            '2021-0005',
            '2022-0001',
            '2022-0002',
            '2022-0003',
            '2023-0001',
            '2023-0002',
        ];

        foreach ($studentIds as $id) {
            AuthorizedId::create([
                'id_number' => $id,
                'type' => 'student',
                'is_used' => false,
                'registered_by' => $counselor->id,
                'notes' => 'Sample student ID for testing',
            ]);
        }

        // Sample Faculty IDs
        $facultyIds = [
            'FAC-001',
            'FAC-002',
            'FAC-003',
            'FAC-004',
            'FAC-005',
        ];

        foreach ($facultyIds as $id) {
            AuthorizedId::create([
                'id_number' => $id,
                'type' => 'faculty',
                'is_used' => false,
                'registered_by' => $counselor->id,
                'notes' => 'Sample faculty ID for testing',
            ]);
        }

        $this->command->info('Sample authorized IDs created successfully!');
    }
}
