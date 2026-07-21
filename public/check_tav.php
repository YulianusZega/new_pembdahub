<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $c = App\Models\Classroom::where('school_id', 3)->where('class_name', 'XII TAV')->first();
    if (!$c) die("Not found XII TAV");
    
    $ta = App\Models\TeachingAssignment::where('classroom_id', $c->id)
        ->where('academic_year_id', 5)
        ->where('semester_id', 7)
        ->with('subject', 'teacher')->get();
    echo "<pre>Assignments for TP 5 Sem 7:\n";
    foreach ($ta as $t) {
        echo $t->subject->name . " (".$t->hours_per_week." jam) - tipe: " . $t->block_type . " - Guru: " . $t->teacher->name . "\n";
    }

    $blocks = App\Models\BlockSchedule::whereIn('teaching_assignment_id', $ta->pluck('id'))->with('timeSlot')->get();
    echo "\nBlocks on Senin:\n";
    foreach ($blocks as $b) {
        if (strtolower($b->timeSlot->day_of_week) == 'senin') {
            echo "Senin: " . $b->timeSlot->slot_name . " -> " . $b->teachingAssignment->subject->name . "\n";
        }
    }
    echo "</pre>";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
