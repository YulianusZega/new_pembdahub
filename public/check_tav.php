<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

$ts = App\Models\TimeSlot::where('school_id', 3)->where('day_of_week', 'monday')->get();
echo "Monday TimeSlots for school 3:\n";
foreach ($ts as $t) {
    echo "ID: {$t->id}, Name: {$t->slot_name}, Is Teaching: {$t->is_teaching_slot}\n";
}
