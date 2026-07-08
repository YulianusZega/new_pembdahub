<?php
/**
 * Safe Migration Runner (Public Directory Version)
 * HAPUS FILE INI SETELAH SELESAI DIGUNAKAN!
 * 
 * Akses via browser: https://perguruanpembda.com/run_migrate.php?token=pembda2026migrate
 */

$SECRET_TOKEN = 'pembda2026migrate';

if (!isset($_GET['token']) || $_GET['token'] !== $SECRET_TOKEN) {
    http_response_code(403);
    die('⛔ Akses ditolak. Gunakan ?token=YOUR_TOKEN');
}

// Bootstrap Laravel (karena berada di public/, vendor & bootstrap ada di folder parent /../)
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo '<html><head><title>Migration Runner</title>';
echo '<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}';
echo '.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}';
echo 'h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:15px;border-radius:8px;overflow-x:auto}</style></head><body>';
echo '<h1>🔧 Pembda Hub - Migration Runner</h1>';
echo '<p class="info">Waktu: ' . now()->format('Y-m-d H:i:s') . '</p>';

// Cek koneksi database
try {
    \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo '<p class="ok">✅ Koneksi database berhasil: <b>' . config('database.connections.mysql.database') . '</b></p>';
} catch (\Exception $e) {
    echo '<p class="err">❌ Koneksi database gagal: ' . $e->getMessage() . '</p>';
    die('</body></html>');
}

// Jalankan migration
if (isset($_GET['run']) && $_GET['run'] === 'yes') {
    echo '<h2>🚀 Menjalankan Migration...</h2>';
    echo '<pre>';
    
    try {
        $output = new \Symfony\Component\Console\Output\BufferedOutput();
        \Illuminate\Support\Facades\Artisan::call('migrate', [
            '--force' => true,
        ], $output);
        
        $result = $output->fetch();
        echo htmlspecialchars($result);
        echo '</pre>';
        echo '<p class="ok">✅ Migration selesai!</p>';
    } catch (\Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
        echo '</pre>';
    }
} else {
    echo '<br><a href="?token=' . $SECRET_TOKEN . '&run=yes" style="background:#bb86fc;color:#000;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:bold;font-size:16px">▶️ Jalankan Migration Sekarang</a>';
    echo '<p class="warn">⚠️ Klik tombol di atas untuk menjalankan migration.</p>';
}

echo '<hr><p class="warn">🗑️ <b>PENTING:</b> Hapus file run_migrate.php dari folder public/ setelah selesai demi keamanan!</p>';
echo '</body></html>';
