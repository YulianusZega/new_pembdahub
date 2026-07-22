<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;
use App\Models\TeachingAssignment;

$teacher = Teacher::where('full_name', 'LIKE', '%Yelfi%')->first();
if ($teacher) {
    echo "Found Teacher: " . $teacher->full_name . " (ID: " . $teacher->id . ")\n";
    
    $tas = TeachingAssignment::with(['classroom', 'subject'])
        ->where('teacher_id', $teacher->id)
        ->get();
        
    foreach($tas as $ta) {
        $plotted = \App\Models\Schedule::where('teaching_assignment_id', $ta->id)->sum('duration_slots');
        echo "TA ID: " . $ta->id . 
             " | Class: " . ($ta->classroom->class_name ?? 'N/A') . 
             " | Subj: " . ($ta->subject->subject_name ?? 'N/A') . 
             " | Hours: " . $ta->hours_per_week . 
             " | Plotted: " . $plotted . 
             " | Active: " . $ta->is_active . 
             " | Sem: " . $ta->semester_id . 
             " | AY: " . $ta->academic_year_id . "\n";
    }
} else {
    echo "Teacher Yelfi not found.\n";
}
