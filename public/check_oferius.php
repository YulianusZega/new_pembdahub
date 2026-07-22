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
    $ts_per = optional($s->timeSlot)->period_number ?? 'N/A';
    $ts_start = optional($s->timeSlot)->start_time ?? 'N/A';
    $mapel = optional($s->subject)->subject_name ?? optional($s->subject)->name ?? 'N/A';
    $guru = optional($s->teacher)->full_name ?? 'N/A';
    $out .= "ID: {$s->id} | TS: {$s->time_slot_id} | TS Period: {$ts_per} | TS Time: {$ts_start} | Mapel: {$mapel} | Guru: {$guru}\n";
}

file_put_contents(__DIR__ . '/dump.txt', $out);
echo "Dumped to dump.txt";
