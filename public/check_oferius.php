<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\DB;

$slots = DB::table('time_slots')
    ->where('school_id', 3)
    ->where('day_of_week', 'tuesday')
    ->orderBy('start_time')
    ->select('id', 'period_number', 'is_teaching_slot', 'start_time')
    ->get();

$out = [];
$c = 1;
foreach($slots as $s) {
    if ($s->is_teaching_slot) {
        $out["Jam_$c"] = $s->id . "_" . $s->start_time;
        $c++;
    }
}
echo json_encode($out);
