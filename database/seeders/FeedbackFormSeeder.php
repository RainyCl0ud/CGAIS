<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeedbackForm;
use App\Models\User;
use App\Models\Appointment;

class FeedbackFormSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get completed appointments
        $completedAppointments = Appointment::where('status', 'completed')->get();
        $counselors = User::where('role', 'counselor')->get();

        foreach ($completedAppointments as $appointment) {
            // Create feedback for some completed appointments
            if (fake()->boolean(70)) { // 70% chance of having feedback
                FeedbackForm::create([
                    'user_id' => $appointment->user_id,
                    'counselor_id' => $appointment->counselor_id,
                    'appointment_id' => $appointment->id,
                    'counselor_rating' => fake()->numberBetween(3, 5),
                    'service_rating' => fake()->numberBetween(3, 5),
                    'facility_rating' => fake()->numberBetween(3, 5),
                    'overall_satisfaction' => fake()->numberBetween(3, 5),
                    'counselor_feedback' => fake()->optional(0.8)->paragraph(),
                    'service_feedback' => fake()->optional(0.7)->paragraph(),
                    'suggestions' => fake()->optional(0.4)->paragraph(),
                    'concerns' => fake()->optional(0.2)->paragraph(),
                    'would_recommend' => fake()->boolean(85), // 85% would recommend
                    'recommendation_reason' => fake()->optional(0.6)->sentence(),
                    'additional_comments' => fake()->optional(0.3)->paragraph(),
                ]);
            }
        }

        // Create some standalone feedback forms
        $students = User::where('role', 'student')->get();
        foreach ($students as $student) {
            if (fake()->boolean(30)) { // 30% chance of standalone feedback
                FeedbackForm::create([
                    'user_id' => $student->id,
                    'counselor_id' => $counselors->random()->id,
                    'appointment_id' => null,
                    'counselor_rating' => fake()->numberBetween(3, 5),
                    'service_rating' => fake()->numberBetween(3, 5),
                    'facility_rating' => fake()->numberBetween(3, 5),
                    'overall_satisfaction' => fake()->numberBetween(3, 5),
                    'counselor_feedback' => fake()->optional(0.8)->paragraph(),
                    'service_feedback' => fake()->optional(0.7)->paragraph(),
                    'suggestions' => fake()->optional(0.4)->paragraph(),
                    'concerns' => fake()->optional(0.2)->paragraph(),
                    'would_recommend' => fake()->boolean(85),
                    'recommendation_reason' => fake()->optional(0.6)->sentence(),
                    'additional_comments' => fake()->optional(0.3)->paragraph(),
                ]);
            }
        }
    }
} 