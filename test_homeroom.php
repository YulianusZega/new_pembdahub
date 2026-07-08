<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$teacher = App\Models\Teacher::whereRaw('LOWER(full_name) LIKE ?', ['%yonata%'])->first();
if (!$teacher) {
    echo "Teacher not found\n";
} else {
    $classrooms = App\Models\Classroom::where('homeroom_teacher_id', $teacher->id)->get(['id', 'class_name', 'is_active', 'academic_year_id'])->toArray();
    $activeYear = App\Models\AcademicYear::where('is_active', true)->first();
    echo json_encode(['teacher_id' => $teacher->id, 'name' => $teacher->full_name, 'classrooms' => $classrooms, 'activeYear_id' => $activeYear->id ?? null], JSON_PRETTY_PRINT);
}

// Find all teachers who are homeroom teachers
$homerooms = App\Models\Classroom::where('is_active', true)->with('homeroomTeacher')->get();
echo "\nActive Classrooms with Homeroom Teachers:\n";
foreach($homerooms as $c) {
    echo $c->class_name . " => " . ($c->homeroomTeacher ? $c->homeroomTeacher->full_name : 'None') . " (Academic Year: " . $c->academic_year_id . ")\n";
}
