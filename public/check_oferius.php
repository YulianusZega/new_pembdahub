<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TimeSlot;

echo "<pre>";
$ts = TimeSlot::where('day_of_week', 'tuesday')->orderBy('start_time')->get();
foreach ($ts as $t) {
    echo "ID: {$t->id}, Name: {$t->name}, Period: {$t->period_number}, Teaching: {$t->is_teaching_slot}, Time: {$t->start_time} - {$t->end_time}\n";
}
echo "</pre>";
