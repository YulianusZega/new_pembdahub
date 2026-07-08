<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TeachingAssignment;
use App\Models\Schedule;
use App\Models\Teacher;
use App\Models\Classroom;

$data = json_decode(file_get_contents('schedules_to_import.json'), true);
$ayId = 1; 
$semId = 1; 
$semesterName = 'ganjil';

// CLEAR ALL FIRST
$classIds = Classroom::where('school_id', 3)->pluck('id');
Schedule::whereIn('classroom_id', $classIds)->delete();

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
        }

        $teacher = Teacher::find($item['teacher_id']);
        Schedule::create([
            'school_id' => 3,
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
        
        if ($item['day'] == 'monday' && $item['slot_id'] == 171 && $item['classroom_id'] == 189) {
            echo "SUCCESSFULLY CREATED Monday Jam 2 for X TKR INDUSTRI\n";
        }
    } catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}

echo "Import complete.\n";
