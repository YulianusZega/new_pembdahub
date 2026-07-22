<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Semester;
use App\Models\TeachingAssignment;

$sems = Semester::where('academic_year_id', 5)->get();
echo "Semesters for AY 5:\n";
foreach($sems as $sem) {
    echo "ID: " . $sem->id . " | Name: " . $sem->semester_name . "\n";
}

$ta = TeachingAssignment::where('teacher_id', 113) // Wait, I don't know Martperan's ID. Let's just find by his name.
    ->whereHas('teacher', function($q) {
        $q->where('full_name', 'LIKE', '%Martperan%');
    })
    ->whereHas('classroom', function($q) {
        $q->where('class_name', 'LIKE', '%X DPIB%');
    })
    ->first();
if ($ta) {
    echo "Martperan TA: SemID=" . $ta->semester_id . ", AYID=" . $ta->academic_year_id . ", Active=" . $ta->is_active . ", SubjID=" . $ta->subject_id . "\n";
}

$ta2 = TeachingAssignment::whereHas('teacher', function($q) {
        $q->where('full_name', 'LIKE', '%Herman%');
    })
    ->whereHas('classroom', function($q) {
        $q->where('class_name', 'LIKE', '%X DPIB%');
    })
    ->first();
if ($ta2) {
    echo "Herman TA: SemID=" . $ta2->semester_id . ", AYID=" . $ta2->academic_year_id . ", Active=" . $ta2->is_active . ", SubjID=" . $ta2->subject_id . "\n";
}
