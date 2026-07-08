<?php
/**
 * Diagnostic script for all grades of teacher 124
 */
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Semester;
use App\Models\Subject;

$teacher = Teacher::where('full_name', 'like', '%YONATA TELAUMBANUA%')->first();
if (!$teacher) {
    die("Teacher not found.");
}

echo "TEACHER: {$teacher->full_name} (ID: {$teacher->id})\n\n";

$grades = Grade::where('teacher_id', $teacher->id)->get();
echo "TOTAL GRADES IN DATABASE: " . $grades->count() . "\n\n";

$grouped = $grades->groupBy('semester_id');

foreach ($grouped as $semId => $semGrades) {
    $sem = Semester::find($semId);
    $semName = $sem ? "{$sem->name} (Year ID: {$sem->academic_year_id})" : 'Unknown';
    echo "SEMESTER ID: {$semId} ({$semName})\n";
    
    $subGrouped = $semGrades->groupBy('subject_id');
    foreach ($subGrouped as $subId => $subGrades) {
        $sub = Subject::find($subId);
        $subName = $sub ? ($sub->subject_name ?: $sub->name) : 'Unknown';
        echo "  - Subject ID: {$subId} ({$subName}) -> " . $subGrades->count() . " grades\n";
        
        // Count unique students
        $uniqStudents = $subGrades->pluck('student_id')->unique()->toArray();
        echo "    Students count: " . count($uniqStudents) . "\n";
        
        // Show first grade details
        $fg = $subGrades->first();
        echo "    First grade: ID {$fg->id}, Score {$fg->score}, Notes '{$fg->notes}', Created {$fg->created_at}\n";
    }
    echo "----------------------------------------\n";
}
