<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TimeSlot;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

echo "<pre>";
$schedules = DB::select("SELECT * FROM schedules WHERE teaching_assignment_id = 6177");
foreach ($schedules as $s) {
    echo "Schedule ID: {$s->id}, TS_ID: {$s->time_slot_id}\n";
    $ts = DB::select("SELECT * FROM time_slots WHERE id = ?", [$s->time_slot_id]);
    if (!empty($ts)) {
        echo "  -> Period: {$ts[0]->period_number}, Time: {$ts[0]->start_time} - {$ts[0]->end_time}\n";
    }
}
echo "</pre>";
