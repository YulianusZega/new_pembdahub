<?php
/**
 * Diagnostic script for student Nadin grades
 */
header('Content-Type: text/plain; charset=utf-8');

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Grade;
use App\Models\Teacher;
use App\Models\Subject;

$nadinId = 1568; // NADIN EL CHASTIN GULO

$grades = Grade::where('student_id', $nadinId)
    ->where('semester_id', 8)
    ->get();

echo "ALL GRADES FOR NADIN (ID: 1568) IN SEMESTER 8:\n";
echo "Total grades: " . $grades->count() . "\n\n";

foreach ($grades as $g) {
    $t = Teacher::find($g->teacher_id);
    $tName = $t ? $t->name : 'Unknown';
    $sub = Subject::find($g->subject_id);
    $subName = $sub ? ($sub->subject_name ?: $sub->name) : 'Unknown';
    
    echo "- Grade ID: {$g->id}\n";
    echo "  Subject ID: {$g->subject_id} ({$subName})\n";
    echo "  Teacher: {$tName} (ID: {$g->teacher_id})\n";
    echo "  Score: {$g->score}\n";
    echo "  Grade Type: {$g->grade_type}\n";
    echo "  Notes: " . var_export($g->notes, true) . "\n";
    echo "  Created At: {$g->created_at}\n";
    echo "----------------------------------------\n";
}
