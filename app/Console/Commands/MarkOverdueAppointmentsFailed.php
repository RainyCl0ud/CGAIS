<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Appointment;
use Carbon\Carbon;

class MarkOverdueAppointmentsFailed extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:mark-overdue-failed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mark overdue pending appointments as failed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();

        $overdueAppointments = Appointment::where('status', 'pending')
            ->where(function ($query) use ($now) {
                $query->where('appointment_date', '<', $now->toDateString())
                      ->orWhere(function ($query) use ($now) {
                          $query->where('appointment_date', '=', $now->toDateString())
                                ->where('start_time', '<', $now->toTimeString());
                      });
            })
            ->get();

        $count = 0;
        foreach ($overdueAppointments as $appointment) {
            $appointment->status = 'failed';
            $appointment->save();
            $count++;
        }

        $this->info("Marked {$count} overdue pending appointments as failed.");

        return 0;
    }
}
