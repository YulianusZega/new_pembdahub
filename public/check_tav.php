<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');
use Illuminate\Support\Facades\DB;

$sched = DB::table('schedules')
    ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
    ->join('classrooms', 'schedules.classroom_id', '=', 'classrooms.id')
    ->where('classrooms.class_name', 'XII TAV')
    ->where('schedules.academic_year_id', 5)
    ->where('schedules.semester_id', 7)
    ->select('time_slots.day_of_week', 'time_slots.slot_name')
    ->orderBy('time_slots.day_of_week')
    ->orderBy('time_slots.start_time')
    ->get();
echo "XII TAV schedule (" . $sched->count() . " slots):\n";
foreach ($sched as $s) echo "  {$s->day_of_week}: {$s->slot_name}\n";
