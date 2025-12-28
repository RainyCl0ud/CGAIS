<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\User;
use App\Models\Appointment;
use App\Notifications\AppointmentReminder;

echo "Starting test script...\n";

$user = User::first();
if (!$user) {
    echo "No users found in DB. Aborting.\n";
    exit(1);
}

$counselor = User::where('role', 'counselor')->first() ?? $user;

$appointmentDate = Carbon::now()->addDay()->toDateString();
$startTime = Carbon::now()->addMinute()->format('H:i:s');
$endTime = Carbon::now()->addMinute()->addHour()->format('H:i:s');

$appointment = Appointment::create([
    'user_id' => $user->id,
    'counselor_id' => $counselor->id,
    'appointment_date' => $appointmentDate,
    'start_time' => $startTime,
    'end_time' => $endTime,
    'type' => 'regular',
    'counseling_category' => 'consultation',
    'reason' => 'Automated test reminder',
    'status' => 'confirmed',
]);

$sendAt = $appointment->getAppointmentDateTime()->subDay();

echo "Created appointment ID: {$appointment->id}\n";
echo "Appointment datetime: " . $appointment->getAppointmentDateTime() . "\n";
echo "Scheduled sendAt: " . $sendAt . "\n";

if ($sendAt->gt(Carbon::now())) {
    $appointment->user->notify((new AppointmentReminder($appointment, 'tomorrow'))->delay($sendAt));
    echo "Notification scheduled for user {$appointment->user->email} at {$sendAt}\n";
} else {
    echo "Computed sendAt is not in the future; no notification scheduled.\n";
}

echo "Test script complete.\n";
