<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TeachingAssignment;

$teacher = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Martperan%')->first();
$classroom = Classroom::where('school_id', 3)->where('class_name', 'LIKE', '%X DPIB%')->first();
$subject = Subject::where('school_id', 3)->where(function($q) {
    $q->where('subject_name', 'LIKE', '%DDPK%')
      ->orWhere('subject_name', 'LIKE', '%Dasar-dasar Program Keahlian%');
})->first();

if (!$teacher) echo "Teacher not found.\n";
if (!$classroom) echo "Classroom not found.\n";
if (!$subject) {
    echo "Subject not found by DDPK. Trying broader search:\n";
    $subs = Subject::where('school_id', 3)->get();
    foreach($subs as $s) {
        if(strpos(strtolower($s->subject_name), 'dasar') !== false) {
            echo "Possible subject: " . $s->subject_name . " (ID: " . $s->id . ")\n";
        }
    }
}

if ($teacher && $classroom && $subject) {
    $ta = TeachingAssignment::updateOrCreate([
        'academic_year_id' => 5, // Currently active AY
        'semester_id' => 7, // Currently active Sem
        'classroom_id' => $classroom->id,
        'subject_id' => $subject->id,
        'teacher_id' => $teacher->id,
    ], [
        'hours_per_week' => 6,
        'is_active' => 1,
        'block_type' => 'all'
    ]);
    
    echo "SUCCESS: Created TA ID " . $ta->id . " for " . $teacher->full_name . " | Subj: " . $subject->subject_name . " | Class: " . $classroom->class_name . " | 6 JP\n";
}
