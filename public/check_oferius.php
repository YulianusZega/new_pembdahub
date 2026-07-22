<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use Illuminate\Support\Facades\DB;

$classroom = DB::table('classrooms')->where('class_name', 'X TSM 2')->where('academic_year_id', 5)->first();

$schedules = DB::table('schedules')
    ->where('classroom_id', $classroom->id)
    ->where('day_of_week', 'tuesday')
    ->get();

file_put_contents(__DIR__ . '/dump.txt', json_encode($schedules, JSON_PRETTY_PRINT));
echo "Dumped to dump.txt using DB facade";
