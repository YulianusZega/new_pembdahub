<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
header('Content-Type: text/plain');
$ts = App\Models\TimeSlot::where('school_id', 3)->where('academic_year_id', 5)->where('day_of_week', 'Senin')->pluck('slot_name');
echo json_encode($ts);
