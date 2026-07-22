<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\DB;

$schedules = DB::table('schedules')
    ->where('classroom_id', 353)
    ->where('day_of_week', 'tuesday')
    ->select('id', 'time_slot_id', 'subject_id', 'teaching_assignment_id')
    ->orderBy('time_slot_id')
    ->get();

foreach($schedules as $s) {
    echo $s->id . "," . $s->time_slot_id . "," . $s->subject_id . "," . $s->teaching_assignment_id . "\n";
}
