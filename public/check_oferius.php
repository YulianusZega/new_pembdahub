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
    ->orderBy('time_slot_id')
    ->get();

echo "<html><body><table border='1'><tr><th>ID</th><th>TS</th><th>Subj</th><th>TA</th></tr>";
foreach($schedules as $s) {
    echo "<tr><td>" . $s->id . "</td><td>" . $s->time_slot_id . "</td><td>" . $s->subject_id . "</td><td>" . $s->teaching_assignment_id . "</td></tr>";
}
echo "</table></body></html>";
