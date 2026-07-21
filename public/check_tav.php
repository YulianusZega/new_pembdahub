<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

$schedules = DB::table('schedules')
    ->join('time_slots', 'schedules.time_slot_id', '=', 'time_slots.id')
    ->join('classrooms', 'schedules.classroom_id', '=', 'classrooms.id')
    ->where('classrooms.class_name', 'XII TKJ')
    ->where('schedules.academic_year_id', 5)
    ->where('schedules.semester_id', 7)
    ->where('time_slots.day_of_week', 'monday')
    ->select('time_slots.slot_name')
    ->get();
    
echo "XII TKJ Senin:\n";
foreach ($schedules as $s) echo "{$s->slot_name}\n";
