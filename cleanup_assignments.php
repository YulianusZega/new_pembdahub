<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeachingAssignment;
use App\Models\Schedule;

$schoolId = 3;

echo "Cleaning up empty teaching assignments for School 3...\n";

// Find assignments for school 3 that have 0 hours and 0 schedules
$emptyAssignments = TeachingAssignment::whereHas('teacher', function($query) use ($schoolId) {
    $query->where('school_id', $schoolId);
})
->where('hours_per_week', 0)
->whereNotExists(function ($query) {
    $query->select(DB::raw(1))
          ->from('schedules')
          ->whereRaw('schedules.teaching_assignment_id = teaching_assignments.id');
})
->get();

$deletedCount = $emptyAssignments->count();
foreach ($emptyAssignments as $assignment) {
    $assignment->delete();
}

echo "Cleanup complete. Deleted $deletedCount empty assignments.\n";
