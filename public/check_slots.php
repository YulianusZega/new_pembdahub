<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');
$ts = App\Models\TimeSlot::where('school_id', 3)
    ->select('academic_year_id', 'day_of_week', 'slot_name')
    ->get()
    ->groupBy('day_of_week')
    ->map(function($g) { return $g->pluck('slot_name', 'academic_year_id'); });
echo json_encode($ts, JSON_PRETTY_PRINT);
