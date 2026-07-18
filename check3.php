<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t566 = \App\Models\TimeSlot::find(566);
$t492 = \App\Models\TimeSlot::find(492);

echo "566: school=" . $t566->school_id . ", year=" . $t566->academic_year_id . "\n";
echo "492: school=" . ($t492 ? $t492->school_id : 'null') . ", year=" . ($t492 ? $t492->academic_year_id : 'null') . "\n";
