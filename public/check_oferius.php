<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Schedule;

$s = Schedule::all();
echo "Total Schedules exactly now: " . count($s) . "\n";
foreach($s as $sch) {
    echo "ID: " . $sch->id . " TA: " . $sch->teaching_assignment_id . "\n";
}
