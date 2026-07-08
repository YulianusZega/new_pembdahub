<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Request Root: " . request()->root() . "\n";
echo "Asset: " . asset('test.jpg') . "\n";
