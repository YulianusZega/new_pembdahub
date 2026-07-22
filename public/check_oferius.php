<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Schedule;

$schedules = Schedule::with(['classroom', 'teachingAssignment.subject'])
    ->where('day_of_week', 'tuesday')
    ->whereHas('teachingAssignment', function($q) {
        $q->where('teacher_id', 204); // Adiyusu Zai
    })
    ->get();

echo "Adiyusu Zai Tuesday Schedules:\n";
foreach($schedules as $s) {
    echo "Class: " . $s->classroom->class_name . " | Time: " . $s->time_slot_id . " | Subj: " . $s->teachingAssignment->subject->name . "\n";
}
