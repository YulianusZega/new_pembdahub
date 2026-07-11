<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

header('Content-Type: text/plain');

echo "=== DIAGNOSTIK FORUM & HTTPS ===\n";
echo "Waktu: " . now()->format('Y-m-d H:i:s') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "APP_URL: " . config('app.url') . "\n";
echo "Request Scheme: " . (request()->secure() ? 'HTTPS' : 'HTTP') . "\n";
echo "Request Root: " . request()->root() . "\n";
echo "Route forum.like: " . route('forum.like', ['thread' => 1]) . "\n";
echo "Route forum.react: " . route('forum.react', ['thread' => 1]) . "\n";
echo "url('/forum/poll/1/vote'): " . url('/forum/poll/1/vote') . "\n";
echo "\n=== COOKIE & SESSION ===\n";
echo "Session Driver: " . config('session.driver') . "\n";
echo "Session Secure Cookie: " . (config('session.secure') ? 'TRUE' : 'FALSE') . "\n";
echo "Logged In User: " . (auth()->check() ? auth()->user()->username . ' (ID: ' . auth()->id() . ')' : 'GUEST') . "\n";

echo "\n=== LAST 30 LINES OF LARAVEL LOG ===\n";
$logFile = storage_path('logs/laravel.log');
if (file_exists($logFile)) {
    $lines = file($logFile);
    $lastLines = array_slice($lines, -30);
    echo implode("", $lastLines);
} else {
    echo "Log file not found.\n";
}
