<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\TeachingAssignment;
use App\Models\Schedule;
use App\Models\TimeSlot;

$schoolId = 3;
$academicYearId = 5;

$timeSlotsDB = TimeSlot::where('school_id', $schoolId)->where('is_teaching_slot', true)->get();
$timeSlotMap = []; 
foreach ($timeSlotsDB as $ts) {
    if (preg_match('/(\d+)/', $ts->slot_name, $m)) {
        $jamKe = (int)$m[1];
        $timeSlotMap[strtolower($ts->day_of_week)][$jamKe] = $ts->id;
    }
}
echo "timeSlotMap['monday'][2] = " . ($timeSlotMap['monday'][2] ?? 'null') . "\n";
echo "timeSlotMap['monday'][5] = " . ($timeSlotMap['monday'][5] ?? 'null') . "\n";

$classroom = Classroom::where('school_id', 3)->where('class_name', 'XII TAV')->first();
echo "Classroom ID = {$classroom->id}\n";

$teacher = Teacher::where('school_id', 3)->where('full_name', 'like', '%Filiaro Hulu%')->first();
echo "Teacher ID = " . ($teacher->id ?? 'null') . "\n";

$subject = Subject::where('school_id', 3)->where('code', 'KK-TE')->first();
echo "Subject ID = " . ($subject->id ?? 'null') . "\n";

$ta = TeachingAssignment::where([
    'academic_year_id' => $academicYearId,
    'semester_id' => 7,
    'classroom_id' => $classroom->id,
    'subject_id' => $subject->id,
    'teacher_id' => $teacher->id,
])->first();
echo "TA ID = " . ($ta->id ?? 'null') . "\n";
