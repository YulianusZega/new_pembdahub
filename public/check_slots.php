<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');

$timeSlots = App\Models\TimeSlot::where('school_id', 3)->where('academic_year_id', 5)->get();
$daysMap = ['Senin'=>'monday', 'Selasa'=>'tuesday', 'Rabu'=>'wednesday', 'Kamis'=>'thursday', 'Jumat'=>'friday', 'Sabtu'=>'saturday'];
$timeSlotMap = [];
foreach ($timeSlots as $slot) {
    $dayEn = $daysMap[$slot->day_of_week] ?? null;
    if ($dayEn) {
        $num = (int) str_replace('Les ', '', $slot->slot_name);
        $timeSlotMap[$dayEn][$num] = $slot->id;
    }
}
echo json_encode($timeSlotMap, JSON_PRETTY_PRINT);
