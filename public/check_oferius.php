<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;
use App\Models\Schedule;

$ta = TeachingAssignment::find(6177);
echo "<pre>";
echo "TA ID: 6177\n";
echo "Hours per week: {$ta->hours_per_week}\n";
$plottedJP = Schedule::where('teaching_assignment_id', $ta->id)->sum('duration_slots');
echo "Plotted JP: {$plottedJP}\n";

$schedules = Schedule::where('teaching_assignment_id', $ta->id)->get();
foreach ($schedules as $s) {
    echo "- Schedule ID: {$s->id}, Day: {$s->day_of_week}, Start: {$s->time_slot_id}, Duration: {$s->duration_slots}\n";
}
echo "</pre>";
