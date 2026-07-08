<?php
/**
 * Git Fix - Ubah remote ke HTTPS lalu fetch + reset
 * Upload ke public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/git_fix.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Git Fix HTTPS</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:12px;border-radius:8px;overflow-x:auto;font-size:13px}</style></head><body>";
echo "<h1>🔧 Git Fix: Remote HTTPS + Force Reset</h1>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
$httpsRemote = 'https://github.com/YulianusZega/pembdahub.git';

echo "<p class='info'>ℹ️ Laravel root: <b>$laravelRoot</b></p>";

// Fungsi bantu jalankan command
function runCmd(string $cmd, string $label): bool {
    echo "<h2>$label</h2>";
    $output = [];
    $retCode = 0;
    exec($cmd . ' 2>&1', $output, $retCode);
    $out = implode("\n", $output);
    $class = $retCode === 0 ? 'ok' : 'err';
    echo "<pre class='$class'>" . htmlspecialchars($out ?: '(kosong)') . "</pre>";
    if ($retCode !== 0) echo "<p class='err'>⚠️ Exit code: $retCode</p>";
    return $retCode === 0;
}

// 1. Cek remote URL saat ini
runCmd("git -C $laravelRoot remote get-url origin", "1️⃣ Remote URL Saat Ini");

// 2. Ubah remote ke HTTPS
runCmd("git -C $laravelRoot remote set-url origin $httpsRemote", "2️⃣ Ubah Remote → HTTPS");

// 3. Konfirmasi perubahan
runCmd("git -C $laravelRoot remote get-url origin", "3️⃣ Konfirmasi Remote Baru");

// 4. Git fetch via HTTPS
$fetchOk = runCmd("git -C $laravelRoot fetch origin main --depth=1", "4️⃣ Git Fetch (HTTPS)");

if (!$fetchOk) {
    echo "<p class='warn'>⚠️ Fetch gagal. Mencoba tanpa --depth...</p>";
    $fetchOk = runCmd("git -C $laravelRoot fetch origin main", "4️⃣ Git Fetch (retry)");
}

// 5. Reset hard ke origin/main
if ($fetchOk) {
    runCmd("git -C $laravelRoot reset --hard origin/main", "5️⃣ Force Reset ke origin/main");
} else {
    echo "<h2>5️⃣ Force Reset</h2>";
    echo "<p class='err'>❌ Fetch gagal, reset tidak dijalankan. Lihat alternatif di bawah.</p>";
}

// 6. Cek commit terbaru
runCmd("git -C $laravelRoot log --oneline -5", "6️⃣ Git Log Terbaru");

// 7. Verifikasi file-file kritis
echo "<h2>7️⃣ Verifikasi File Kritis</h2>";

$checks = [
    ['path' => "$laravelRoot/app/Http/Controllers/Admin/TeachingAssignmentController.php",
     'marker' => 'Prioritas: (1) teacher',
     'label' => 'TeachingAssignmentController'],
    ['path' => "$laravelRoot/resources/views/admin/assignments/teaching/create.blade.php",
     'marker' => 'Informasi Pilihan Mata Pelajaran',
     'label' => 'create.blade.php (info banner)'],
    ['path' => "$laravelRoot/resources/views/admin/assignments/teaching/create.blade.php",
     'marker' => 'Mapel tidak muncul',
     'label' => 'create.blade.php (mapel hint)'],
    ['path' => "$laravelRoot/database/seeders/TimeSlotsSeeder.php",
     'marker' => 'getSlotsForSchool',
     'label' => 'TimeSlotsSeeder (semua sekolah)'],
];

foreach ($checks as $check) {
    if (!file_exists($check['path'])) {
        echo "<p class='err'>❌ FILE TIDAK DITEMUKAN: {$check['label']}</p>";
        continue;
    }
    $content = file_get_contents($check['path']);
    $ok = strpos($content, $check['marker']) !== false;
    echo "<p class='" . ($ok ? 'ok' : 'err') . "'>" . ($ok ? '✅' : '❌') . " {$check['label']}: " . ($ok ? 'VERSI TERBARU ✓' : 'MASIH LAMA ✗') . "</p>";
}

// 8. Clear cache
echo "<h2>8️⃣ Bersihkan Cache</h2>";
$viewCacheDir = "$laravelRoot/storage/framework/views/";
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ $cleared view cache dihapus.</p>";
}
foreach (['config.php','routes-v7.php','packages.php','services.php'] as $cf) {
    $fp = "$laravelRoot/bootstrap/cache/$cf";
    if (file_exists($fp) && @unlink($fp)) echo "<p class='ok'>✅ Deleted: bootstrap/cache/$cf</p>";
}

echo "<hr>";
echo "<p class='warn'><b>⚠️ HAPUS file git_fix.php setelah selesai!</b></p>";
echo "<p><a href='https://perguruanpembda.com/clear-cache.php?secret=pembda99' style='color:#03dac6'>→ Jalankan clear-cache.php</a></p>";
echo "<p><a href='https://perguruanpembda.com/admin/assignments/teaching/create?teacher_id=295' target='_blank' style='color:#03dac6'>→ Test halaman penugasan mengajar</a></p>";
echo "</body></html>";
