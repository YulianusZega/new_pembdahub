<?php
// Script cek URL routing dan htaccess untuk diagnosis 404 proposals
// Akses: https://perguruanpembda.com/diag_route.php?secret=pembda99

if (($_GET['secret'] ?? '') !== 'pembda99') {
    http_response_code(403);
    die('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

// Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== ROUTE URL & HTACCESS DIAGNOSTIC ===\n";
echo "Waktu: " . date('Y-m-d H:i:s') . "\n\n";

// ─── 1. Cek .htaccess di public_html (level atas) ─────────────
echo "=== [1] .htaccess DI LEVEL public_html (satu level di atas) ===\n";
$parentHtaccess = __DIR__ . '/../../.htaccess'; // public_html/.htaccess
$parentHtaccessAlt = __DIR__ . '/../../../.htaccess'; // domain root .htaccess
echo "Cek path: " . realpath(__DIR__ . '/../../') . "/.htaccess\n";
if (file_exists($parentHtaccess)) {
    echo "ISI .htaccess (satu level di atas public/):\n";
    echo file_get_contents($parentHtaccess) . "\n";
} else {
    echo "  TIDAK ADA .htaccess di level atas\n";
}

// ─── 2. Cek .htaccess di public_html (dua level di atas) ──────
echo "\nCek path: " . realpath(__DIR__ . '/../../../') . "/.htaccess\n";
if (file_exists($parentHtaccessAlt)) {
    echo "ISI .htaccess (dua level di atas public/):\n";
    echo file_get_contents($parentHtaccessAlt) . "\n";
} else {
    echo "  TIDAK ADA .htaccess dua level di atas\n";
}

// ─── 3. URL yang di-generate oleh route() ─────────────────────
echo "\n=== [2] URL YANG DI-GENERATE OLEH route() HELPER ===\n";
try {
    echo "APP_URL (config): " . config('app.url') . "\n";
    echo "APP_ENV: " . config('app.env') . "\n\n";

    $urlProposals  = route('admin.final-projects.proposals.index');
    $urlDashboard  = route('admin.dashboard');
    $urlFormats    = route('admin.final-projects.formats.index');

    echo "route('admin.dashboard'): " . $urlDashboard . "\n";
    echo "route('admin.final-projects.formats.index'): " . $urlFormats . "\n";
    echo "route('admin.final-projects.proposals.index'): " . $urlProposals . "\n";
    echo "\nURL YANG BENAR UNTUK PROPOSALS ADALAH:\n  " . $urlProposals . "\n";
} catch (\Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

// ─── 4. REQUEST info ──────────────────────────────────────────
echo "\n=== [3] REQUEST INFO ===\n";
echo "REQUEST_URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "\n";
echo "SCRIPT_NAME: " . ($_SERVER['SCRIPT_NAME'] ?? 'N/A') . "\n";
echo "HTTP_HOST: " . ($_SERVER['HTTP_HOST'] ?? 'N/A') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "\n";
echo "SERVER_NAME: " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "\n";

// ─── 5. Daftar file di public_html (satu level di atas) ───────
echo "\n=== [4] ISI FOLDER public_html (satu level di atas) ===\n";
$parentDir = realpath(__DIR__ . '/../../');
echo "Path: " . $parentDir . "\n";
if (is_dir($parentDir)) {
    $items = scandir($parentDir);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;
        $isDir = is_dir($parentDir . '/' . $item);
        echo "  " . ($isDir ? "[DIR]  " : "[FILE] ") . $item . "\n";
    }
}

// ─── 6. Clear cache lagi ──────────────────────────────────────
echo "\n=== [5] CLEAR CACHE ===\n";
$commands = ['route:clear', 'config:clear', 'cache:clear', 'view:clear'];
foreach ($commands as $cmd) {
    try {
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        \Illuminate\Support\Facades\Artisan::call($cmd, [], $output);
        echo "  ✓ {$cmd}: " . trim($output->fetch()) . "\n";
    } catch (\Exception $e) {
        echo "  ✗ {$cmd}: " . $e->getMessage() . "\n";
    }
}

echo "\n=== SELESAI ===\n";
