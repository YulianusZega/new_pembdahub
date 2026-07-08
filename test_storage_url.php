<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Storage;

$photo = 'teachers/test.jpg';
echo "Asset: " . asset('storage/' . $photo) . "\n";
echo "Storage URL: " . Storage::url($photo) . "\n";
echo "App URL: " . config('app.url') . "\n";
echo "Public Folder Path: " . public_path('storage/' . $photo) . "\n";
