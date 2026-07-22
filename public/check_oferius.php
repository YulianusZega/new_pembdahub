<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TimeSlot;

$timeSlots = TimeSlot::where('school_id', 3)
    ->where('day_of_week', 'tuesday')
    ->orderBy('start_time')
    ->get();

$out = "<pre>TimeSlots for Tuesday School 3:\n";
$teachingPeriod = 1;
foreach ($timeSlots as $ts) {
    if ($ts->is_teaching_slot) {
        $out .= "Period $teachingPeriod => ID: {$ts->id}, Time: {$ts->start_time} - {$ts->end_time}\n";
        $teachingPeriod++;
    }
}
$out .= "</pre>";

echo $out;
