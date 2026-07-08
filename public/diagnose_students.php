<?php
/**
 * Diagnostic script for students, classrooms, and academic years
 */
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StudentClass;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Grade;
use App\Models\Teacher;

// 1. Teacher info
$teacher = Teacher::where('full_name', 'like', '%YONATA TELAUMBANUA%')->first();
echo "DIAGNOSING TEACHER: " . ($teacher ? "{$teacher->name} (ID: {$teacher->id}, User ID: {$teacher->user_id})" : "NOT FOUND") . "\n\n";

// 2. Student Classes for classroom 255
$scs = StudentClass::where('classroom_id', 255)->get();
echo "Total student class records for class 255: " . $scs->count() . "\n\n";

// 3. Grades in Semester 8, Classroom 255, Subject 7
echo "GRADES FOR SEMESTER 8, CLASSROOM 255, SUBJECT 7:\n";
$grades = Grade::where('semester_id', 8)
    ->where('subject_id', 7)
    ->whereIn('student_id', $scs->pluck('student_id'))
    ->get();
echo "Total grades matching: " . $grades->count() . "\n";

$teachersInfo = [];
foreach ($grades as $g) {
    $t = Teacher::find($g->teacher_id);
    $tName = $t ? $t->name : 'Unknown';
    echo "- Student: {$g->student_id} (" . ($g->student?->full_name) . ")\n";
    echo "  Teacher ID: {$g->teacher_id} ({$tName})\n";
    echo "  Score: {$g->score}\n";
    echo "  Notes: '{$g->notes}'\n";
    echo "  Created: {$g->created_at}\n";
    echo "----------------------------------------\n";
}
