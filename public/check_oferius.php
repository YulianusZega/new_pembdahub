<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\TeachingAssignment;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\Subject;

if (request('secret') !== 'pembda99') die('Unauthorized');

$teacher = Teacher::where('full_name', 'like', '%Oferius%')->first();
$classroom = Classroom::where('class_name', 'X TSM 2')->where('academic_year_id', 5)->first();

echo "<pre>";
echo "Teacher ID: " . $teacher->id . "\n";
echo "Classroom X TSM 2 (AY 5) ID: " . $classroom->id . "\n\n";

$tas = TeachingAssignment::where('teacher_id', $teacher->id)
    ->where('classroom_id', $classroom->id)
    ->with('subject')
    ->get();

echo "Teaching Assignments for Oferius in X TSM 2 (AY 5 classroom ID {$classroom->id}):\n";
foreach ($tas as $ta) {
    echo "- ID: {$ta->id}, Subject ID: {$ta->subject_id} (" . ($ta->subject->subject_name ?? 'N/A') . "), JP: {$ta->hours_per_week}, Semester ID: {$ta->semester_id}, Academic Year ID: {$ta->academic_year_id}\n";
}

echo "\nAll Teaching Assignments for Oferius in ANY Classroom named X TSM 2:\n";
$classrooms = Classroom::where('class_name', 'X TSM 2')->pluck('id')->toArray();
$tasAll = TeachingAssignment::where('teacher_id', $teacher->id)
    ->whereIn('classroom_id', $classrooms)
    ->with(['subject', 'classroom'])
    ->get();
foreach ($tasAll as $ta) {
    echo "- ID: {$ta->id}, Classroom ID: {$ta->classroom_id} (AY {$ta->classroom->academic_year_id}), Subject ID: {$ta->subject_id} (" . ($ta->subject->subject_name ?? 'N/A') . "), JP: {$ta->hours_per_week}, Semester ID: {$ta->semester_id}, TA_AY: {$ta->academic_year_id}\n";
}

echo "</pre>";
