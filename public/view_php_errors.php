<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== STARTING BOOTSTRAP ===\n";
try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
    echo "Laravel Bootstrapped\n";
    
    echo "Testing auth check...\n";
    $auth = auth()->check();
    echo "Auth Check: " . ($auth ? 'Yes' : 'No') . "\n";
    
    echo "Testing route url...\n";
    echo "Url: " . url('/forum/poll/1/vote') . "\n";
    
} catch (\Throwable $e) {
    echo "CRASH DETECTED: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}
