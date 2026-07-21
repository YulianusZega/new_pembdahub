<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$taIds = App\Models\TeachingAssignment::where('classroom_id', 281)
    ->where('academic_year_id', 5)
    ->where('semester_id', 7)
    ->pluck('id');

$blocks = App\Models\BlockSchedule::whereIn('teaching_assignment_id', $taIds)
    ->join('time_slots', 'block_schedules.time_slot_id', '=', 'time_slots.id')
    ->join('teaching_assignments', 'block_schedules.teaching_assignment_id', '=', 'teaching_assignments.id')
    ->join('subjects', 'teaching_assignments.subject_id', '=', 'subjects.id')
    ->select('time_slots.day_of_week', 'time_slots.slot_name', 'subjects.name as subject_name')
    ->orderBy('time_slots.day_of_week')
    ->orderBy('time_slots.start_time')
    ->get();

echo "<pre>";
foreach ($blocks as $b) {
    echo "{$b->day_of_week} {$b->slot_name}: {$b->subject_name}\n";
}
echo "</pre>";
