<?php
/**
 * Diagnostic script for teacher grades and subjects
 */
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grade;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\StudentClass;

$teacher = Teacher::where('full_name', 'like', '%YONATA TELAUMBANUA%')->first();
if (!$teacher) {
    die("Teacher YONATA TELAUMBANUA not found.");
}

echo "TEACHER: {$teacher->name} (ID: {$teacher->id})\n\n";

$grades = Grade::where('teacher_id', $teacher->id)->orderBy('created_at', 'desc')->get();
echo "TOTAL GRADES BY TEACHER: " . $grades->count() . "\n\n";

echo "LATEST 15 GRADES INSERTED:\n";
foreach ($grades->take(15) as $g) {
    // Find student class details
    $sc = StudentClass::where('student_id', $g->student_id)->first();
    $className = $sc && $sc->classroom ? $sc->classroom->class_name : 'Unknown';
    $classroomId = $sc ? $sc->classroom_id : 'Unknown';
    
    echo "- Grade ID: {$g->id}\n";
    echo "  Student ID: {$g->student_id} (" . ($g->student?->full_name ?? 'Unknown') . ")\n";
    echo "  Classroom: {$className} (ID: {$classroomId})\n";
    echo "  Subject ID: {$g->subject_id} (" . ($g->subject?->subject_name ?? $g->subject?->name ?? 'Unknown') . ")\n";
    echo "  Semester ID: {$g->semester_id}\n";
    echo "  Grade Type: {$g->grade_type}\n";
    echo "  Score: {$g->score}\n";
    echo "  Notes: " . var_export($g->notes, true) . "\n";
    echo "  Created At: {$g->created_at}\n";
    echo "----------------------------------------\n";
}
