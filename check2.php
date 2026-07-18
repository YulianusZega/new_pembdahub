<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$overlaps = \App\Models\TimeSlot::where('school_id', 3)
    ->where('academic_year_id', 5)
    ->where('day_of_week', 'monday')
    ->where('id', '!=', 566)
    ->where('start_time', '<', '07:15')
    ->where('end_time', '>', '07:00')
    ->get();

echo "Overlaps count: " . $overlaps->count() . "\n";
foreach ($overlaps as $o) {
    echo "- ID: " . $o->id . " | Name: " . $o->slot_name . " | Time: " . $o->start_time . " to " . $o->end_time . "\n";
}
