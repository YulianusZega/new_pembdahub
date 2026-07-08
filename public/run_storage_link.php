<?php
/**
 * Safe Storage Link Creator (Public Directory Version)
 * HAPUS FILE INI SETELAH SELESAI DIGUNAKAN!
 * 
 * Akses via browser: https://perguruanpembda.com/run_storage_link.php?token=pembda2026storage
 */

$SECRET_TOKEN = 'pembda2026storage';

if (!isset($_GET['token']) || $_GET['token'] !== $SECRET_TOKEN) {
    http_response_code(403);
    die('⛔ Akses ditolak. Gunakan ?token=YOUR_TOKEN');
}

echo '<html><head><title>Storage Link Creator</title>';
echo '<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}';
echo '.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}';
echo 'h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:15px;border-radius:8px;overflow-x:auto}</style></head><body>';
echo '<h1>🔗 Pembda Hub - Storage Link Creator</h1>';

// Path target symlink (karena file berada di public/, maka path target adalah di directory yang sama)
$publicStoragePath = __DIR__ . '/storage';

echo '<p class="info">Memeriksa folder: ' . $publicStoragePath . '</p>';

if (file_exists($publicStoragePath) || is_link($publicStoragePath)) {
    if (is_link($publicStoragePath)) {
        if (unlink($publicStoragePath)) {
            echo '<p class="ok">✅ Berhasil menghapus Symbolic Link lama yang rusak.</p>';
        } else {
            echo '<p class="err">❌ Gagal menghapus Symbolic Link lama.</p>';
        }
    } else {
        // Jika berupa direktori asli, kita backup/rename
        $backupName = $publicStoragePath . '_bak_' . time();
        if (rename($publicStoragePath, $backupName)) {
            echo '<p class="warn">⚠️ Folder public/storage berupa folder fisik. Berhasil di-backup menjadi: ' . basename($backupName) . '</p>';
        } else {
            echo '<p class="err">❌ Gagal me-rename folder public/storage fisik.</p>';
        }
    }
} else {
    echo '<p class="info">Folder public/storage tidak ditemukan (bersih). Siap membuat link baru.</p>';
}

// Bootstrap Laravel (karena berada di public/, vendor & bootstrap ada di folder parent /../)
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo '<h2>dYs? Membuat Symlink secara manual (Native PHP)...</h2>';
    $target = __DIR__.'/../storage/app/public';
    $link = $publicStoragePath;
    
    if (symlink($target, $link)) {
        echo '<p class="ok">o. Hubungan folder penyimpanan (Symlink) berhasil dibuat!</p>';
        echo '<p class="info">Target: ' . htmlspecialchars($target) . '<br>Link: ' . htmlspecialchars($link) . '</p>';
    } else {
        echo '<p class="err">?O Gagal membuat symlink. Pastikan fungsi symlink() tidak didisable oleh hosting.</p>';
    }
} catch (\Exception $e) {
    echo '<p class="err">?O Error: ' . $e->getMessage() . '</p>';
}

echo '<hr><p class="warn">🗑️ <b>PENTING:</b> Hapus file run_storage_link.php dari folder public/ setelah selesai demi keamanan!</p>';
echo '</body></html>';
