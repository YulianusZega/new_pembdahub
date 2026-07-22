<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$results = \Illuminate\Support\Facades\DB::table('schedules as s')
    ->join('time_slots as ts', 's.time_slot_id', '=', 'ts.id')
    ->leftJoin('teaching_assignments as ta', 's.teaching_assignment_id', '=', 'ta.id')
    ->leftJoin('subjects as sub', 'ta.subject_id', '=', 'sub.id')
    ->leftJoin('teachers as t', 'ta.teacher_id', '=', 't.id')
    ->whereIn('s.classroom_id', \Illuminate\Support\Facades\DB::table('classrooms')->where('class_name', 'like', '%X DPIB%')->pluck('id'))
    ->select('s.id', 's.day_of_week', 'ts.slot_order', 'sub.name as mapel_name', 't.full_name as guru_name')
    ->orderBy('s.day_of_week')
    ->orderBy('ts.slot_order')
    ->get();

$out = "Jadwal X DPIB di DB:\n";
foreach ($results as $r) {
    $out .= "ID: {$r->id} | Hari: {$r->day_of_week} | Slot: {$r->slot_order} | Mapel: {$r->mapel_name} | Guru: {$r->guru_name}\n";
}
file_put_contents('xdpib_dump.txt', $out);
echo "DONE! https://perguruanpembda.com/xdpib_dump.txt";

