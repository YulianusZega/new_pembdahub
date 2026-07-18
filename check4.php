<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$t566 = \App\Models\TimeSlot::find(566);
echo json_encode($t566->toArray(), JSON_PRETTY_PRINT);
