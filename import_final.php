<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeachingAssignment;
use App\Models\Schedule;
use App\Models\Teacher;

$data = json_decode(file_get_contents('schedules_to_import.json'), true);
$ayId = 1; 
$semId = 1; 
$semesterName = 'ganjil';

echo "Importing " . count($data) . " schedules...\n";

foreach ($data as $item) {
    try {
        $assignment = TeachingAssignment::where([
            'teacher_id' => $item['teacher_id'],
            'subject_id' => $item['subject_id'],
            'classroom_id' => $item['classroom_id'],
            'academic_year_id' => $ayId,
            'semester_id' => $semId,
        ])->first();

        if (!$assignment) {
            $assignment = TeachingAssignment::create([
                'teacher_id' => $item['teacher_id'],
                'subject_id' => $item['subject_id'],
                'classroom_id' => $item['classroom_id'],
                'academic_year_id' => $ayId,
                'semester_id' => $semId,
                'hours_per_week' => 0,
                'teaching_load_type' => 'wajib',
                'is_active' => true,
                'group_code' => $item['group_code'],
            ]);
        } elseif ($item['group_code'] && !$assignment->group_code) {
            $assignment->update(['group_code' => $item['group_code']]);
        }

        $teacher = Teacher::find($item['teacher_id']);
        Schedule::create([
            'school_id' => $teacher->school_id,
            'teacher_id' => $item['teacher_id'],
            'subject_id' => $item['subject_id'],
            'classroom_id' => $item['classroom_id'],
            'time_slot_id' => $item['slot_id'],
            'duration_slots' => 1,
            'day_of_week' => $item['day'],
            'academic_year_id' => $ayId,
            'semester_id' => $semId,
            'semester' => $semesterName,
            'teaching_assignment_id' => $assignment->id,
            'group_code' => $item['group_code'],
        ]);
    } catch (\Exception $e) {
    }
}

// Recalculate
$allAssignments = TeachingAssignment::where('academic_year_id', $ayId)->where('semester_id', $semId)->get();
foreach ($allAssignments as $ta) {
    $ta->update(['hours_per_week' => Schedule::where('teaching_assignment_id', $ta->id)->count()]);
}

echo "Import complete.\n";
