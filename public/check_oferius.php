<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Schedule;

$schedules = Schedule::with(['subject', 'teacher', 'timeSlot', 'classroom'])
    ->whereHas('classroom', function($q) {
        $q->where('class_name', 'X TSM 2');
    })
    ->where('day_of_week', 'tuesday')
    ->get();

$out = "TUESDAY SCHEDULES FOR X TSM 2\n=================================\n";
foreach ($schedules as $s) {
    $out .= "ID: {$s->id} | TS: {$s->time_slot_id} | TS Period: " . ($s->timeSlot->period_number ?? 'N/A') . " | TS Time: " . ($s->timeSlot->start_time ?? 'N/A') . " - " . ($s->timeSlot->end_time ?? 'N/A') . " | Mapel: " . ($s->subject->subject_name ?? $s->subject->name ?? 'N/A') . " | Guru: " . ($s->teacher->full_name ?? 'N/A') . "\n";
}

file_put_contents(__DIR__ . '/dump.txt', $out);
echo "Dumped to dump.txt";
