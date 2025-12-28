<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\CounselorUnavailableDate;

$id = 4; // test record created earlier
$row = CounselorUnavailableDate::find($id);
if (!$row) {
    echo "No record with id={$id}\n";
    exit(0);
}

echo "Deleting record id={$id} date={$row->date} expires_at={$row->expires_at}\n";
$row->delete();
echo "Deleted.\n";
