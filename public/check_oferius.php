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
$classroom = Classroom::where('class_name', 'X TSM 2')->first();

echo "<pre>";
echo "Teacher ID: " . $teacher->id . "\n";
echo "Classroom ID: " . $classroom->id . "\n\n";

$tas = TeachingAssignment::where('teacher_id', $teacher->id)
    ->where('classroom_id', $classroom->id)
    ->with('subject')
    ->get();

echo "Teaching Assignments for Oferius in X TSM 2:\n";
foreach ($tas as $ta) {
    echo "- ID: {$ta->id}, Subject ID: {$ta->subject_id} (" . ($ta->subject->subject_name ?? 'N/A') . "), JP: {$ta->hours_per_week}, Semester ID: {$ta->semester_id}, Academic Year ID: {$ta->academic_year_id}\n";
}

echo "\nAll Subjects matching AGM:\n";
foreach (Subject::where('subject_name', 'like', '%AGM%')->orWhere('code', 'like', '%AGM%')->get() as $s) {
    echo "- ID: {$s->id}, Code: {$s->code}, Name: {$s->subject_name}\n";
}

echo "</pre>";
