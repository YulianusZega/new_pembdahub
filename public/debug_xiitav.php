<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
if (php_sapi_name() !== 'cli' && request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Classroom;
use App\Models\BlockSchedule;
use App\Models\TimeSlot;

$class = Classroom::where('school_id', 3)->where('name', 'XII TAV')->first();
if (!$class) die("Class not found");

$slots = BlockSchedule::whereHas('teachingAssignment', function($q) use ($class) {
    $q->where('classroom_id', $class->id);
})->with('timeSlot')->get();

echo "XII TAV Schedules:\n";
foreach($slots as $s) {
    if (strtolower($s->timeSlot->day_of_week) == 'senin') {
        echo "Monday: {$s->timeSlot->slot_name} - {$s->teachingAssignment->subject->name}\n";
    }
}
