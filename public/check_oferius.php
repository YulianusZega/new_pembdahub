<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\DB;

echo "<pre>";
$count = DB::table('time_slots')->where('school_id', 3)->where('day_of_week', 'tuesday')->where('is_teaching_slot', 1)->count();
echo "Number of teaching slots on Tuesday for SMK: " . $count . "\n";
echo "</pre>";
