<?php
// Fix ASSET_URL dan cek .env production
// Akses: https://perguruanpembda.com/fix_asset_url.php?secret=pembda99

if (($_GET['secret'] ?? '') !== 'pembda99') {
    http_response_code(403);
    die('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$envPath = __DIR__ . '/../.env';

echo "=== FIX ASSET_URL / APP_URL DI .env ===\n";
echo "Waktu: " . date('Y-m-d H:i:s') . "\n\n";

if (!file_exists($envPath)) {
    echo "ERROR: .env tidak ditemukan di: " . $envPath . "\n";
    die();
}

$envContent = file_get_contents($envPath);

// Tampilkan nilai APP_URL dan ASSET_URL saat ini
echo "=== NILAI SAAT INI ===\n";
preg_match('/^APP_URL=.*/m', $envContent, $matchApp);
preg_match('/^ASSET_URL=.*/m', $envContent, $matchAsset);
echo "APP_URL  : " . ($matchApp[0] ?? 'tidak ada') . "\n";
echo "ASSET_URL: " . ($matchAsset[0] ?? 'tidak ada') . "\n\n";

if (isset($_GET['fix']) && $_GET['fix'] === 'yes') {
    echo "=== MELAKUKAN FIX ===\n";
    
    // Hapus atau perbaiki ASSET_URL jika ada www
    if (!empty($matchAsset[0]) && str_contains($matchAsset[0], 'www.')) {
        $newLine = 'ASSET_URL=https://perguruanpembda.com';
        $envContent = preg_replace('/^ASSET_URL=.*/m', $newLine, $envContent);
        echo "ASSET_URL diubah ke: " . $newLine . "\n";
    } elseif (empty($matchAsset)) {
        echo "Tidak ada ASSET_URL di .env (ini normal, tidak perlu diubah)\n";
    } else {
        echo "ASSET_URL sudah benar: " . ($matchAsset[0] ?? '-') . "\n";
    }

    // Perbaiki APP_URL jika ada www
    if (!empty($matchApp[0]) && str_contains($matchApp[0], 'www.')) {
        $newLine = 'APP_URL=https://perguruanpembda.com';
        $envContent = preg_replace('/^APP_URL=.*/m', $newLine, $envContent);
        echo "APP_URL diubah ke: " . $newLine . "\n";
    } else {
        echo "APP_URL sudah benar: " . ($matchApp[0] ?? '-') . "\n";
    }

    // Simpan .env
    if (file_put_contents($envPath, $envContent) !== false) {
        echo "\n✓ .env berhasil disimpan\n";
    } else {
        echo "\n✗ GAGAL menyimpan .env!\n";
    }

    // Bootstrap Laravel dan clear config cache
    try {
        require __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();
        
        $out = new \Symfony\Component\Console\Output\BufferedOutput();
        \Illuminate\Support\Facades\Artisan::call('config:clear', [], $out);
        echo "config:clear: " . trim($out->fetch()) . "\n";

        $out2 = new \Symfony\Component\Console\Output\BufferedOutput();
        \Illuminate\Support\Facades\Artisan::call('cache:clear', [], $out2);
        echo "cache:clear: " . trim($out2->fetch()) . "\n";

        // Tampilkan nilai baru
        echo "\nAPP_URL baru (dari config): " . config('app.url') . "\n";
    } catch (\Exception $e) {
        echo "Error saat clear cache: " . $e->getMessage() . "\n";
    }
} else {
    echo "=== UNTUK MELAKUKAN FIX, AKSES: ===\n";
    echo "https://perguruanpembda.com/fix_asset_url.php?secret=pembda99&fix=yes\n\n";
    echo "Script ini akan:\n";
    echo "1. Hapus/perbaiki ASSET_URL yang menggunakan www.\n";
    echo "2. Clear config cache\n";
    echo "3. Tampilkan nilai APP_URL baru\n";
}

echo "\n=== SELESAI ===\n";
