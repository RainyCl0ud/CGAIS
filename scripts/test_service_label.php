<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Service;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

try {
    $user = User::first();
    if (!$user) {
        echo "No users found in DB. Please create at least one user and retry.\n";
        exit(1);
    }

    $counselor = User::where('id', '!=', $user->id)->first() ?? $user;

    $col = DB::select("SHOW COLUMNS FROM appointments LIKE 'counseling_category'");
    if (!empty($col)) {
        echo "appointments.counseling_category column type: " . $col[0]->Type . PHP_EOL;
        if (preg_match('/varchar\((\d+)\)/', $col[0]->Type, $m)) {
            $max = intval($m[1]);
        } else {
            $max = 50;
        }
    } else {
        echo "Could not determine column type for counseling_category. Using max=50\n";
        $max = 50;
    }

    $desiredSlug = 'counseling-test-unique';
    if (strlen($desiredSlug) > $max) {
        $slug = substr($desiredSlug, 0, $max);
        echo "Truncating slug to fit column: $slug\n";
    } else {
        $slug = $desiredSlug;
    }

    $service = Service::firstOrCreate(
        ['slug' => $slug],
        ['name' => 'Counseling']
    );

    $appointment = Appointment::create([
        'user_id' => $user->id,
        'counselor_id' => $counselor->id,
        'appointment_date' => Carbon::now()->toDateString(),
        'start_time' => Carbon::now()->format('H:i'),
        'end_time' => Carbon::now()->addMinutes(30)->format('H:i'),
        'status' => 'pending',
        'type' => 'regular',
        'counseling_category' => $service->slug,
    ]);

    $found = Appointment::find($appointment->id);
    echo "Service slug used: " . $service->slug . PHP_EOL;
    echo "Service name saved: " . $service->name . PHP_EOL;
    echo "getCounselingCategoryLabel(): " . $found->getCounselingCategoryLabel() . PHP_EOL;

} catch (Throwable $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString();
    exit(1);
}
