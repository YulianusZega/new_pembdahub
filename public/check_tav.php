<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$c = App\Models\Classroom::where('school_id', 3)->where('classroom_name', 'XII TAV')->first();
$ta = App\Models\TeachingAssignment::where('classroom_id', $c->id)->with('subject')->get();
foreach ($ta as $t) {
    echo $t->subject->name . " (".$t->hours_per_week." jam) - tipe: " . $t->block_type . "\n";
}

$blocks = App\Models\BlockSchedule::whereIn('teaching_assignment_id', $ta->pluck('id'))->with('timeSlot')->get();
foreach ($blocks as $b) {
    if (strtolower($b->timeSlot->day_of_week) == 'senin') {
        echo "Senin: " . $b->timeSlot->slot_name . " -> " . $b->teachingAssignment->subject->name . "\n";
    }
}
