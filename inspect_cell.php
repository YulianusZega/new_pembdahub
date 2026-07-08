<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\Schedule;

$s = Schedule::where('day_of_week', 'monday')->where('classroom_id', 189)->where('time_slot_id', 171)->first();
if ($s) {
    echo "ID: {$s->id}\n";
    echo "Teacher: {$s->teacher_id} (" . ($s->teacher->full_name ?? 'N/A') . ")\n";
    echo "Subject: {$s->subject_id} (" . ($s->subject->name ?? 'N/A') . ")\n";
} else {
    echo "Not found\n";
}
