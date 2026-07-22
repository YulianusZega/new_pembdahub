<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Schedule;

echo "<pre>";
$schedules = Schedule::where('teaching_assignment_id', 6177)->get();
foreach ($schedules as $s) {
    echo "Schedule ID: {$s->id}, Day: {$s->day_of_week}, TS_ID: {$s->time_slot_id}, Duration: {$s->duration_slots}\n";
}
echo "</pre>";
