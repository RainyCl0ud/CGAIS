<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email reminders to users with appointments scheduled for tomorrow.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting appointment reminder process...');

        // Get tomorrow's date
        $tomorrow = Carbon::tomorrow()->toDateString();

        // Find confirmed appointments for tomorrow that haven't been reminded yet
        $appointments = Appointment::where('appointment_date', $tomorrow)
            ->where('status', 'confirmed')
            ->with(['user', 'counselor'])
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No appointments found for tomorrow.');
            return;
        }

        $this->info("Found {$appointments->count()} appointment(s) for tomorrow. Processing reminders...");

        $remindersSent = 0;
        $remindersSkipped = 0;

        foreach ($appointments as $appointment) {
            // Check if we already sent a reminder for this appointment
            $existingReminder = $appointment->user->notifications()
                ->where('type', 'appointment_reminder')
                ->where('appointment_id', $appointment->id)
                ->where('message', 'like', '%tomorrow%')
                ->first();

            if ($existingReminder) {
                $this->line("Skipping appointment ID {$appointment->id} - reminder already sent.");
                $remindersSkipped++;
                continue;
            }

            try {
                // Send email notification
                $appointment->user->notify(new AppointmentReminder($appointment, 'tomorrow'));

                // Create in-app notification
                $appointment->user->notifications()->create([
                    'appointment_id' => $appointment->id,
                    'title' => 'Appointment Reminder - Tomorrow',
                    'message' => "Your appointment with " . ($appointment->counselor->full_name ?? 'your counselor') . " is scheduled for tomorrow at {$appointment->start_time->format('g:i A')}. Please ensure you are available and prepared for the session.",
                    'type' => 'appointment_reminder',
                    'is_read' => false,
                    'read_at' => null,
                ]);

                $remindersSent++;
                $this->line("Sent reminder for appointment ID {$appointment->id} to {$appointment->user->email}");

            } catch (\Exception $e) {
                $this->error("Failed to send reminder for appointment ID {$appointment->id}: " . $e->getMessage());
            }
        }

        $this->info("Reminder process completed. Sent: {$remindersSent}, Skipped: {$remindersSkipped}");
    }
}