<?php
$baseDir = __DIR__ . '/../';
$dirs = ['app', 'routes', 'resources'];

echo "<h1>🛠️ Memulai Pemulihan Folder Otomatis...</h1>";

foreach ($dirs as $d) {
    // 1. Jika folder saat ini masih ada tapi isinya kosong/rusak, kita singkirkan dulu
    if (is_dir($baseDir . $d)) {
        rename($baseDir . $d, $baseDir . 'rusak_' . $d . '_' . time());
        echo "<p>⚠️ Menyingkirkan folder $d yang rusak...</p>";
    }

    // 2. Cari semua folder cadangan berangka buatan Hostinger (contoh: app.8305)
    $backups = glob($baseDir . $d . '.*', GLOB_ONLYDIR);
    
    if (!empty($backups)) {
        // Urutkan dari yang terbaru (angka terbesar)
        usort($backups, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        $latest = $backups[0];
        
        // 3. Ubah namanya kembali ke nama asli
        if (rename($latest, $baseDir . $d)) {
            echo "<p style='color:green'>✅ Berhasil memulihkan folder <b>" . basename($latest) . "</b> menjadi <b>$d</b></p>";
        } else {
            echo "<p style='color:red'>❌ Gagal mengubah nama " . basename($latest) . "</p>";
        }
    } else {
        echo "<p style='color:red'>❌ TIDAK DITEMUKAN folder cadangan untuk <b>$d</b>!</p>";
    }
}

echo "<h2>Penyelesaian selesai! Silakan buka kembali web Bapak/Ibu.</h2>";
?>
