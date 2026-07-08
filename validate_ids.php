<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\TimeSlot;

$data = json_decode(file_get_contents('schedules_to_import.json'), true);
echo "Checking " . count($data) . " entries...\n";

$bad = 0;
foreach($data as $i) {
    if (!Teacher::find($i['teacher_id'])) { echo "Bad Teacher: {$i['teacher_id']}\n"; $bad++; }
    if (!Subject::find($i['subject_id'])) { echo "Bad Subject: {$i['subject_id']}\n"; $bad++; }
    if (!Classroom::find($i['classroom_id'])) { echo "Bad Classroom: {$i['classroom_id']}\n"; $bad++; }
    if (!TimeSlot::find($i['slot_id'])) { echo "Bad Slot: {$i['slot_id']}\n"; $bad++; }
}
echo "Total Bad: $bad\n";
