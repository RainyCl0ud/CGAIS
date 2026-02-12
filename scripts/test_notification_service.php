<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use App\Notifications\AppointmentStatusNotification;

try {
    // Get or create a test service
    $service = Service::firstOrCreate(
        ['slug' => 'test-custom-service'],
        ['name' => 'Custom Counseling Service', 'created_by' => 1]
    );

    // Get users
    $user = User::first();
    if (!$user) {
        echo "No users found. Please seed the database.\n";
        exit(1);
    }
    $counselor = User::where('role', 'counselor')->first() ?? $user;

    // Create a test appointment
    $appointment = Appointment::create([
        'user_id' => $user->id,
        'counselor_id' => $counselor->id,
        'appointment_date' => Carbon::now()->addDays(1)->toDateString(),
        'start_time' => '10:00',
        'end_time' => '10:30',
        'status' => 'pending',
        'type' => 'regular',
        'counseling_category' => $service->slug,
    ]);

    echo "Created appointment with service: {$service->name}\n";
    echo "getCounselingCategoryLabel(): {$appointment->getCounselingCategoryLabel()}\n";

    // Test the notification
    $notification = new AppointmentStatusNotification($appointment, 'approved');
    $mailMessage = $notification->toMail($user);

    // Find the type line in the message
    $lines = $mailMessage->introLines;
    $typeLine = '';
    foreach ($lines as $line) {
        if (strpos($line, '🏥 **Type:**') !== false) {
            $typeLine = $line;
            break;
        }
    }

    echo "Notification Type Line: {$typeLine}\n";

    // Test with default service
    $appointment2 = Appointment::create([
        'user_id' => $user->id,
        'counselor_id' => $counselor->id,
        'appointment_date' => Carbon::now()->addDays(2)->toDateString(),
        'start_time' => '11:00',
        'end_time' => '11:30',
        'status' => 'pending',
        'type' => 'regular',
        'counseling_category' => 'counseling_services',
    ]);

    echo "\nTesting with default service 'counseling_services'\n";
    echo "getCounselingCategoryLabel(): {$appointment2->getCounselingCategoryLabel()}\n";

    $notification2 = new AppointmentStatusNotification($appointment2, 'approved');
    $mailMessage2 = $notification2->toMail($user);
    $lines2 = $mailMessage2->introLines;
    $typeLine2 = '';
    foreach ($lines2 as $line) {
        if (strpos($line, '🏥 **Type:**') !== false) {
            $typeLine2 = $line;
            break;
        }
    }
    echo "Notification Type Line: {$typeLine2}\n";

    // Clean up
    $appointment->delete();
    $appointment2->delete();

} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
    exit(1);
}
