<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "--- attendances ---\n";
print_r(DB::select('DESCRIBE attendances'));

echo "--- employee_attendances ---\n";
print_r(DB::select('DESCRIBE employee_attendances'));

