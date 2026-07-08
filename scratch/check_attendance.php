<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$today = \Carbon\Carbon::today()->toDateString();
echo "Today: $today\n";

$attendance = \App\Models\Attendance::where('date', $today)
    ->with(['student'])
    ->get();

foreach ($attendance as $att) {
    echo "ID: {$att->id} | Student: {$att->student->full_name} | Status: {$att->status} | Time In: {$att->time_in} | Time Out: {$att->time_out}\n";
}
