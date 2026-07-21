<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');
$ts = App\Models\TimeSlot::where('school_id', 3)->get();
echo "Total TimeSlots for school 3: " . $ts->count() . "\n";
foreach ($ts as $t) {
    echo "ID: {$t->id}, Day: {$t->day_of_week}, Name: {$t->slot_name}, AY: {$t->academic_year_id}\n";
}
