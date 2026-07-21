<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$classes = App\Models\Classroom::where('school_id', 3)->where('class_name', 'XII TAV')->get();

echo "<pre>";
foreach ($classes as $c) {
    echo "Class ID: {$c->id}, Name: {$c->class_name}, TP: {$c->academic_year_id}\n";
    $ta = App\Models\TeachingAssignment::where('classroom_id', $c->id)
        ->where('academic_year_id', 5)
        ->where('semester_id', 7)
        ->with('subject', 'teacher')->get();
    echo "  Assignments (TP5 Sem7): " . $ta->count() . "\n";
    $blocks = App\Models\BlockSchedule::whereIn('teaching_assignment_id', $ta->pluck('id'))->with('timeSlot')->get();
    echo "  Blocks (Senin): \n";
    foreach ($blocks as $b) {
        if (strtolower($b->timeSlot->day_of_week) == 'senin') {
            echo "    " . $b->timeSlot->slot_name . " -> " . $b->teachingAssignment->subject->name . "\n";
        }
    }
}
echo "</pre>";
