<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Appointment;
use App\Notifications\AssistantAppointmentNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestAssistantNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:assistant-notifications {action=booked}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test assistant email notifications for appointments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        if (!in_array($action, ['booked', 'rescheduled', 'cancelled'])) {
            $this->error('Invalid action. Use: booked, rescheduled, or cancelled');
            return 1;
        }

        $this->info("Testing assistant notification for action: {$action}");

        // Get or create a test student
        $student = User::where('role', 'student')->first();
        if (!$student) {
            $student = User::factory()->create([
                'role' => 'student',
                'first_name' => 'Test',
                'last_name' => 'Student',
                'email' => 'test.student@example.com',
            ]);
            $this->info('Created test student: ' . $student->full_name);
        }

        // Get or create a test counselor
        $counselor = User::where('role', 'counselor')->first();
        if (!$counselor) {
            $counselor = User::factory()->create([
                'role' => 'counselor',
                'first_name' => 'Test',
                'last_name' => 'Counselor',
                'email' => 'test.counselor@example.com',
            ]);
            $this->info('Created test counselor: ' . $counselor->full_name);
        }

        // Get assistants
        $assistants = User::where('role', 'assistant')->get();
        if ($assistants->isEmpty()) {
            $this->error('No assistant users found in the system.');
            return 1;
        }

        $this->info('Found ' . $assistants->count() . ' assistant(s) to notify:');
        foreach ($assistants as $assistant) {
            $this->line("- {$assistant->full_name} ({$assistant->email})");
        }

        // Create a test appointment
        $appointment = Appointment::create([
            'user_id' => $student->id,
            'counselor_id' => $counselor->id,
            'appointment_date' => now()->addDays(1)->format('Y-m-d'),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'type' => 'regular',
            'counseling_category' => 'consultation',
            'reason' => 'Test appointment for assistant notification',
            'status' => 'pending',
        ]);

        $this->info("Created test appointment: ID {$appointment->id}");
        $this->line("Date: {$appointment->appointment_date}");
        $this->line("Time: {$appointment->start_time} - {$appointment->end_time}");
        $this->line("Client: {$student->full_name}");
        $this->line("Counselor: {$counselor->full_name}");

        // Test the notification for each assistant
        foreach ($assistants as $assistant) {
            $this->line('');
            $this->info("Sending '{$action}' notification to: {$assistant->full_name} ({$assistant->email})");
            
            try {
                $assistant->notify(new AssistantAppointmentNotification($appointment, $action, "Test reason for {$action}"));
                $this->line("✅ Notification sent successfully to {$assistant->full_name}");
            } catch (\Exception $e) {
                $this->error("❌ Failed to send notification to {$assistant->full_name}: " . $e->getMessage());
                Log::error("Failed to send assistant notification", [
                    'assistant_id' => $assistant->id,
                    'appointment_id' => $appointment->id,
                    'action' => $action,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->line('');
        $this->info('Test completed! Check your email for notifications.');
        
        // Clean up test data
        $appointment->delete();
        $this->line('Cleaned up test appointment.');

        return 0;
    }
}