<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "APP_URL: " . config('app.url') . "\n";
echo "Asset storage/teachers/test.jpg: " . asset('storage/teachers/test.jpg') . "\n";
