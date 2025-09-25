<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueAppointments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:check-overdue';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for pending appointments that are past their date and time and mark them as failed.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $now = Carbon::now();

        // Find pending appointments where the appointment datetime is in the past
        $overdueAppointments = Appointment::where('status', 'pending')
            ->whereRaw("CONCAT(appointment_date, ' ', start_time) < ?", [$now->toDateTimeString()])
            ->get();

        if ($overdueAppointments->isEmpty()) {
            $this->info('No overdue pending appointments found.');
            return;
        }

        $this->info("Found {$overdueAppointments->count()} overdue pending appointment(s). Processing...");

        foreach ($overdueAppointments as $appointment) {
            $appointment->update([
                'status' => 'failed',
                'counselor_notes' => ($appointment->counselor_notes ? $appointment->counselor_notes . "\n\n" : '') .
                    "[Failed on " . now()->format('M d, Y g:i A') . "] - Appointment was not approved by counselor before the scheduled time."
            ]);

            // Notify the student
            $appointment->user->notifications()->create([
                'appointment_id' => $appointment->id,
                'title' => 'Appointment Failed',
                'message' => "Your pending appointment on {$appointment->getFormattedDateTime()} has been marked as failed because it was not approved by the counselor before the scheduled time.",
                'type' => 'appointment_failed',
                'is_read' => false,
                'read_at' => null,
            ]);

            // Notify the counselor
            if ($appointment->counselor) {
                $appointment->counselor->notifications()->create([
                    'appointment_id' => $appointment->id,
                    'title' => 'Appointment Automatically Failed',
                    'message' => "The pending appointment with {$appointment->user->full_name} on {$appointment->getFormattedDateTime()} has been automatically marked as failed due to no approval before the scheduled time.",
                    'type' => 'appointment_failed',
                    'is_read' => false,
                    'read_at' => null,
                ]);
            }

            $this->line("Marked appointment ID {$appointment->id} as failed.");
        }

        $this->info('Overdue appointments processing completed.');
    }
}
