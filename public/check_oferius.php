<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\Classroom;

echo "<pre>";
$classroom = Classroom::where('class_name', 'X TSM 2')->where('academic_year_id', 5)->first();
$schedules = Schedule::with(['subject', 'teacher'])
    ->where('classroom_id', $classroom->id)
    ->where('day_of_week', 'tuesday')
    ->get();

foreach ($schedules as $s) {
    $ts = TimeSlot::find($s->time_slot_id);
    echo "ID: {$s->id}, TS Period: {$ts->period_number}, Mapel: " . ($s->subject->subject_name ?? 'N/A') . ", Guru: " . ($s->teacher->full_name ?? 'N/A') . "\n";
}
echo "</pre>";
