<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\DB;

$count = DB::table('schedules')->where('classroom_id', 353)->where('day_of_week', 'tuesday')->count();
echo "<h1>COUNT: $count</h1>";
