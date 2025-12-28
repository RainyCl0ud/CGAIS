<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Carbon\Carbon;
use App\Models\User;
use App\Models\CounselorUnavailableDate;

echo "Looking for a counselor...\n";
$c = User::where('role', 'counselor')->first();
if (!$c) {
    echo "NO_COUNSELOR_FOUND\n";
    exit(1);
}

$date = '2025-12-29';
echo "Creating unavailable date for counselor id={$c->id} date={$date}\n";
$d = CounselorUnavailableDate::create([
    'counselor_id' => $c->id,
    'date' => $date,
    'is_unavailable' => true,
    'expires_at' => Carbon::parse($date, 'Asia/Manila')->addDay()->startOfDay(),
]);

echo "CREATED id={$d->id} expires_at={$d->expires_at}\n";
