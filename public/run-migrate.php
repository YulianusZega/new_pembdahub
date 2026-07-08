<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Artisan;

define('LARAVEL_START', microtime(true));

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (($_GET['key'] ?? '') !== 'pembda2026') {
    die('Akses ditolak.');
}

try {
    echo "Memulai migrate...<br>";
    // Gunakan --force untuk melewati konfirmasi di production
    Artisan::call('migrate', [
        '--force' => true
    ]);
    echo "Selesai! Output:<br><pre>" . Artisan::output() . "</pre>";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
