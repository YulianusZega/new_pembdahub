<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$classes = App\Models\Classroom::where('school_id', 3)->where('class_name', 'XII TAV')->get();
if ($classes->isEmpty()) {
    $classes = App\Models\Classroom::where('school_id', 3)->where('name', 'XII TAV')->get();
}

echo "<pre>";
foreach ($classes as $c) {
    echo "Class ID: {$c->id}, Name: {$c->class_name} (Active: {$c->is_active})\n";
    $ta = App\Models\TeachingAssignment::where('classroom_id', $c->id)
        ->where('academic_year_id', 5)
        ->where('semester_id', 7)
        ->with('subject', 'teacher')->get();
    echo "  Assignments: " . $ta->count() . "\n";
    foreach ($ta as $t) {
        echo "    " . $t->subject->name . " (".$t->hours_per_week." jam) - tipe: " . $t->block_type . "\n";
    }
}
echo "</pre>";
