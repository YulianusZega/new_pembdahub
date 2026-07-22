<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Schedule;

$total = Schedule::count();
$nullSchool = Schedule::whereNull('school_id')->count();
echo "Total Schedules: " . $total . "\n";
echo "Null school_id: " . $nullSchool . "\n";

if (request('confirm') === 'yes') {
    $count = Schedule::whereHas('classroom', function($q) {
        $q->where('school_id', 3);
    })->delete();
    echo "Deleted schedules via classroom school_id 3: " . $count . "\n";
}
