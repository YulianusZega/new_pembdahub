<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== TEACHER 295 ===\n";
$teacher = App\Models\Teacher::with(['school', 'competentSubjects'])->find(295);
if ($teacher) {
    echo "Name: " . $teacher->full_name . "\n";
    echo "School ID: " . $teacher->school_id . "\n";
    echo "School Name: " . ($teacher->school->name ?? 'N/A') . "\n";
    echo "Competent Subjects: " . $teacher->competentSubjects->count() . "\n";
    foreach ($teacher->competentSubjects as $s) {
        echo "  - [{$s->id}] {$s->subject_name}\n";
    }
} else {
    echo "NOT FOUND\n";
}

echo "\n=== ACADEMIC YEARS ===\n";
$years = App\Models\AcademicYear::all(['id','year','is_active']);
foreach ($years as $y) {
    echo "ID {$y->id}: {$y->year} " . ($y->is_active ? "(AKTIF)" : "") . "\n";
}

echo "\n=== SCHOOLS ===\n";
$schools = App\Models\School::where('is_active',1)->schoolsOnly()->get(['id','name']);
foreach ($schools as $s) {
    echo "ID {$s->id}: {$s->name}\n";
}

echo "\n=== CLASSROOMS (school_id=3, all years) ===\n";
$classrooms = App\Models\Classroom::where('school_id', 3)->where('is_active', 1)
    ->with('academicYear')
    ->get(['id','class_name','grade_level','academic_year_id','is_active']);
echo "Total: " . $classrooms->count() . "\n";
foreach ($classrooms as $c) {
    echo "  [{$c->id}] {$c->class_name} - TP: " . ($c->academicYear->year ?? $c->academic_year_id) . "\n";
}

echo "\n=== SUBJECTS (school_id=3) ===\n";
$subjects = App\Models\Subject::where('school_id', 3)->where('is_active', 1)->get(['id','subject_name']);
echo "Total: " . $subjects->count() . "\n";
foreach ($subjects as $s) {
    echo "  [{$s->id}] {$s->subject_name}\n";
}
