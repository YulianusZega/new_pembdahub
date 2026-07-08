<?php
/**
 * Script untuk mengecek keberadaan data simulasi Demo XII
 */
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$demoClass = \App\Models\Classroom::where('class_name', 'Demo XII')->first();
if ($demoClass) {
    echo "Class Demo XII exists. ID: " . $demoClass->id . "\n";
    $students = \App\Models\StudentClass::where('classroom_id', $demoClass->id)->count();
    echo "Students in class: " . $students . "\n";
    
    $tas = \App\Models\TeachingAssignment::where('classroom_id', $demoClass->id)->count();
    echo "Teaching Assignments: " . $tas . "\n";
    
    $schedules = \App\Models\Schedule::where('classroom_id', $demoClass->id)->count();
    echo "Schedules: " . $schedules . "\n";
} else {
    echo "Class Demo XII DOES NOT EXIST!\n";
}
