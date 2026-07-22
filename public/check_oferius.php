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

$out = [];
foreach ($schedules as $s) {
    $ts = DB::table('time_slots')->where('id', $s->time_slot_id)->first();
    $sub = DB::table('subjects')->where('id', $s->subject_id)->first();
    $out[] = [
        'id' => $s->id,
        'ts_id' => $s->time_slot_id,
        'period' => $ts ? $ts->period_number : 'NULL',
        'is_teaching' => $ts ? $ts->is_teaching_slot : 'NULL',
        'sub' => $sub ? ($sub->subject_code ?? $sub->name) : 'NULL',
    ];
}

header('Content-Type: application/json');
echo json_encode($out, JSON_PRETTY_PRINT);
