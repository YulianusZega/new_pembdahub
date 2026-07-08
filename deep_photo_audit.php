<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check DB photo values vs actual files
echo "=== COMPARING DB PHOTOS TO ACTUAL FILES ===\n\n";

$teachers = App\Models\Teacher::where('school_id', 1)->whereNotNull('photo')->get();
$publicStoragePath = public_path('storage');

foreach ($teachers as $t) {
    $fullPath = $publicStoragePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $t->photo);
    $exists = file_exists($fullPath);
    echo "Teacher: " . $t->full_name . "\n";
    echo "  DB Photo: '" . $t->photo . "'\n";
    echo "  Full Path: " . $fullPath . "\n";
    echo "  Exists: " . ($exists ? 'YES' : 'NO') . "\n";
    echo "  Asset URL: " . asset('storage/' . $t->photo) . "\n\n";
}
