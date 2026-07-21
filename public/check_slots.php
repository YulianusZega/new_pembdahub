<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TimeSlot;

echo "<pre>";
echo "All Time Slots:\n";
$slots = TimeSlot::where('school_id', 3)->orderBy('day_of_week')->orderBy('start_time')->get();
foreach($slots as $s) {
    if (strtolower($s->day_of_week) === 'senin') {
        echo "ID: {$s->id} | {$s->slot_name} | {$s->start_time} - {$s->end_time} | Teaching: {$s->is_teaching_slot}\n";
    }
}
echo "</pre>";
