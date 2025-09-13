<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users
        $student = User::where('email', 'student@gmail.com')->first();
        $counselor = User::where('email', 'counselor@gmail.com')->first();
        $assistant = User::where('email', 'assistant@gmail.com')->first();
        $faculty = User::where('email', 'faculty@gmail.com')->first();

        if (!$student || !$counselor) {
            $this->command->error('Required users not found. Please run DatabaseSeeder first.');
            return;
        }

        // Sample appointment reasons
        $reasons = [
            'Academic concerns and course planning',
            'Personal issues affecting studies',
            'Career guidance and planning',
            'Stress and anxiety management',
            'Time management difficulties',
            'Study skills improvement',
            'Family problems',
            'Peer relationship issues',
            'Financial concerns',
            'Mental health support',
            'Graduation requirements clarification',
            'Internship and job search guidance'
        ];

        // Sample counselor notes
        $counselorNotes = [
            'Student showed good progress in managing stress.',
            'Recommended study schedule adjustments.',
            'Provided resources for career exploration.',
            'Follow-up session scheduled for next month.',
            'Student needs additional support with time management.',
            'Referred to academic advisor for course planning.',
            'Discussed coping strategies for anxiety.',
            'Student demonstrated improved study habits.',
            'Provided guidance on internship applications.',
            'Scheduled regular check-ins for ongoing support.'
        ];

        // Create appointments for the past week
        for ($i = 7; $i >= 1; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Past appointments (completed)
            if ($date->isPast()) {
                $this->createAppointment(
                    $student->id,
                    $counselor->id,
                    $date,
                    '09:00',
                    '10:00',
                    'completed',
                    'regular',
                    $reasons[array_rand($reasons)],
                    'Student attended session as scheduled.',
                    $counselorNotes[array_rand($counselorNotes)]
                );

                $this->createAppointment(
                    $assistant->id,
                    $counselor->id,
                    $date,
                    '14:00',
                    '15:00',
                    'completed',
                    'follow_up',
                    $reasons[array_rand($reasons)],
                    'Follow-up session completed successfully.',
                    $counselorNotes[array_rand($counselorNotes)]
                );
            }
        }

        // Create appointments for today and upcoming days
        for ($i = 0; $i <= 14; $i++) {
            $date = Carbon::now()->addDays($i);
            
            // Skip weekends
            if ($date->isWeekend()) {
                continue;
            }

            // Today's appointments
            if ($i === 0) {
                $this->createAppointment(
                    $student->id,
                    $counselor->id,
                    $date,
                    '10:00',
                    '11:00',
                    'confirmed',
                    'regular',
                    $reasons[array_rand($reasons)],
                    'Appointment confirmed for today.',
                    null
                );

                $this->createAppointment(
                    $faculty->id,
                    $counselor->id,
                    $date,
                    '15:00',
                    '16:00',
                    'pending',
                    'urgent',
                    'Urgent consultation needed.',
                    'Urgent appointment request.',
                    null
                );
            }

            // Upcoming appointments
            if ($i > 0 && $i <= 7) {
                $this->createAppointment(
                    $student->id,
                    $counselor->id,
                    $date,
                    '09:00',
                    '10:00',
                    'confirmed',
                    'regular',
                    $reasons[array_rand($reasons)],
                    'Upcoming appointment scheduled.',
                    null
                );

                $this->createAppointment(
                    $assistant->id,
                    $counselor->id,
                    $date,
                    '14:00',
                    '15:00',
                    'pending',
                    'follow_up',
                    $reasons[array_rand($reasons)],
                    'Follow-up appointment requested.',
                    null
                );
            }

            // Future appointments (8-14 days)
            if ($i > 7) {
                $this->createAppointment(
                    $faculty->id,
                    $counselor->id,
                    $date,
                    '11:00',
                    '12:00',
                    'pending',
                    'regular',
                    $reasons[array_rand($reasons)],
                    'Future appointment scheduled.',
                    null
                );
            }
        }

        // Create some cancelled appointments
        $this->createAppointment(
            $student->id,
            $counselor->id,
            Carbon::now()->subDays(3),
            '13:00',
            '14:00',
            'cancelled',
            'regular',
            'Academic planning session',
            'Appointment cancelled by student.',
            null
        );

        $this->createAppointment(
            $assistant->id,
            $counselor->id,
            Carbon::now()->subDays(5),
            '16:00',
            '17:00',
            'cancelled',
            'follow_up',
            'Career guidance follow-up',
            'Appointment cancelled due to scheduling conflict.',
            null
        );

        // Create a no-show appointment
        $this->createAppointment(
            $faculty->id,
            $counselor->id,
            Carbon::now()->subDays(2),
            '10:00',
            '11:00',
            'no_show',
            'regular',
            'Stress management consultation',
            'Student did not show up for appointment.',
            null
        );

        $this->command->info('Sample appointment data created successfully!');
    }

    private function createAppointment($userId, $counselorId, $date, $startTime, $endTime, $status, $type, $reason, $notes, $counselorNotes)
    {
        return Appointment::create([
            'user_id' => $userId,
            'counselor_id' => $counselorId,
            'appointment_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => $status,
            'type' => $type,
            'reason' => $reason,
            'notes' => $notes,
            'counselor_notes' => $counselorNotes,
        ]);
    }
} 