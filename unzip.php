<?php
$zip = new ZipArchive;
// Zip berada di folder induk (pembdahub)
$res = $zip->open(__DIR__ . '/../app_update.zip');
if ($res === TRUE) {
    // Ekstrak ke folder induk (pembdahub)
    $zip->extractTo(__DIR__ . '/../');
    $zip->close();
    echo "<h1>✅ UPDATE BERHASIL!</h1>";
    echo "<p>Seluruh file (app, routes, resources) berhasil diperbarui dan digabungkan dengan aman ke versi terbaru.</p>";
} else {
    echo "<h1>❌ GAGAL!</h1>";
    echo "<p>File app_update.zip tidak ditemukan di folder pembdahub. Pastikan sudah diupload di luar folder public.</p>";
}
