<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeachingAssignment;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

$schoolId = 3;
$ayId = 1; // Assuming 2025/2026
$semId = 1; // Assuming Ganjil

echo "Starting synchronization of Teaching Assignment hours...\n";

// 1. Get all teaching assignments for school 3
$assignments = TeachingAssignment::whereHas('teacher', function($query) use ($schoolId) {
    $query->where('school_id', $schoolId);
})
->where('academic_year_id', $ayId)
->where('semester_id', $semId)
->get();

$updatedCount = 0;

foreach ($assignments as $assignment) {
    // Count how many schedule slots this assignment has
    $scheduledCount = Schedule::where('teaching_assignment_id', $assignment->id)->count();
    
    // Update hours_per_week to match scheduled count
    if ($assignment->hours_per_week != $scheduledCount) {
        $assignment->hours_per_week = $scheduledCount;
        $assignment->save();
        $updatedCount++;
    }
}

echo "Synchronization complete.\n";
echo "Total assignments analyzed: " . $assignments->count() . "\n";
echo "Total assignments updated: " . $updatedCount . "\n";
