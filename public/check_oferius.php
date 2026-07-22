<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TimeSlot;
use App\Models\Schedule;

$schoolId = 3; // SMK
echo "<pre>";
$ts = TimeSlot::where('school_id', $schoolId)
    ->where('day_of_week', 'tuesday')
    ->where('is_teaching_slot', 1)
    ->orderBy('period_number')
    ->get();
    
echo "TimeSlots for Tuesday School 3:\n";
foreach ($ts as $t) {
    echo "ID: {$t->id}, Period: {$t->period_number}, Time: {$t->start_time} - {$t->end_time}\n";
}

echo "\nSchedules for TA 6177 (AGM X TSM 2):\n";
$schedules = Schedule::where('teaching_assignment_id', 6177)->get();
foreach ($schedules as $s) {
    echo "Schedule ID: {$s->id}, TS_ID: {$s->time_slot_id}\n";
}
echo "</pre>";
