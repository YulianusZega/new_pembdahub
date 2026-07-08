<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

$schoolId = 3;
$ayId = 1;
$semester = 'ganjil';

$schedules = Schedule::where('school_id', $schoolId)
    ->where('academic_year_id', $ayId)
    ->where('semester', $semester)
    ->with(['timeSlot', 'subject'])
    ->get();

$data = [];
foreach ($schedules as $s) {
    if (!$s->timeSlot) continue;
    
    // Group by Day and Class to calculate local "Jam Ke-N"
    $key = $s->day_of_week . '_' . $s->classroom_id;
    if (!isset($data[$key])) {
        $data[$key] = [];
    }
    $data[$key][] = $s;
}

$updateCount = 0;
foreach ($data as $key => $roomSchedules) {
    // Sort schedules in that room by slot order
    usort($roomSchedules, function($a, $b) {
        return $a->timeSlot->slot_order <=> $b->timeSlot->slot_order;
    });
    
    $subjectCounts = [];
    foreach ($roomSchedules as $s) {
        $sid = $s->subject_id;
        if (!isset($subjectCounts[$sid])) {
            $subjectCounts[$sid] = 0;
        }
        $subjectCounts[$sid]++;
        
        // We will store this in a temporary file for the View to read
        // Mapping: ScheduleID -> HourNumberInSequence
        $results[$s->id] = $subjectCounts[$sid];
    }
}

file_put_contents('schedule_hour_sequence.json', json_encode($results));
echo "Calculated sequences for " . count($results) . " schedules.\n";
