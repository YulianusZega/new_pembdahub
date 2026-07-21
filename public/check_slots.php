<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TimeSlot;

echo "<pre>";
echo "Monday Time Slots:\n";
$slots = TimeSlot::where('school_id', 3)->where('day_of_week', 'Senin')->orderBy('start_time')->get();
foreach($slots as $s) {
    echo "ID: {$s->id} | {$s->slot_name} | {$s->start_time} - {$s->end_time} | Teaching: {$s->is_teaching_slot}\n";
}
echo "</pre>";
