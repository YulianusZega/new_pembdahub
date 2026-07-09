<?php

echo "=== PembdaHUB Local Diagnostic ===\n\n";

$issues = 0;

// 1. Check Directories
$requiredDirs = [
    'storage/app',
    'storage/app/public',
    'storage/framework/cache/data',
    'storage/framework/sessions',
    'storage/framework/testing',
    'storage/framework/views',
    'storage/logs',
    'bootstrap/cache',
];

echo "1. Checking required directories...\n";
foreach ($requiredDirs as $dir) {
    if (!is_dir(__DIR__ . '/' . $dir)) {
        echo "[WARN] Directory missing: $dir (Creating...)\n";
        mkdir(__DIR__ . '/' . $dir, 0755, true);
        $issues++;
    } else {
        echo "[OK] Directory exists: $dir\n";
    }
}
echo "\n";

// 2. Check vendor
echo "2. Checking Composer dependencies (vendor)...\n";
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "[ERROR] vendor/autoload.php is missing. Composer install is required!\n";
    $issues++;
} else {
    echo "[OK] Vendor folder seems intact.\n";
}
echo "\n";

// 3. Check .env
echo "3. Checking .env file...\n";
if (!file_exists(__DIR__ . '/.env')) {
    echo "[ERROR] .env file is missing!\n";
    $issues++;
} else {
    echo "[OK] .env file exists.\n";
}
echo "\n";

// 4. Check DB Connection
echo "4. Checking Database Connection...\n";
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "[OK] Database connected successfully.\n";
} catch (\Exception $e) {
    echo "[ERROR] Database connection failed: " . $e->getMessage() . "\n";
    $issues++;
}
echo "\n";

// 5. Storage Link
echo "5. Checking Storage Link...\n";
if (!is_dir(__DIR__ . '/public/storage') && !is_link(__DIR__ . '/public/storage')) {
    echo "[WARN] public/storage link is missing! (You might need to run php artisan storage:link)\n";
    $issues++;
} else {
    echo "[OK] Storage link exists.\n";
}
echo "\n";

if ($issues === 0) {
    echo "=== ALL CHECKS PASSED. SYSTEM IS HEALTHY. ===\n";
} else {
    echo "=== FOUND $issues ISSUE(S) THAT NEED ATTENTION. ===\n";
}
