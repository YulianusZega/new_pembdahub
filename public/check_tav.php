<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

$schedules = DB::table('schedules')
    ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
    ->join('subjects', 'schedules.subject_id', '=', 'subjects.id')
    ->where('schedules.classroom_id', 281)
    ->where('schedules.academic_year_id', 5)
    ->where('schedules.semester_id', 7)
    ->select('time_slots.day_of_week')
    ->get();
    
$counts = $schedules->groupBy('day_of_week')->map(function($g) { return $g->count(); });
echo "Schedule counts per day for 281:\n";
foreach ($counts as $day => $count) {
    echo "{$day}: {$count}\n";
}
