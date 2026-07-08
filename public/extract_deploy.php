<?php
/**
 * Extract deploy_update.zip ke folder Laravel
 * Upload BERSAMA deploy_update.zip ke: public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/extract_deploy.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Forbidden'); }

echo "<html><head><title>Extract Deploy Update</title>";
echo "<style>
body{font-family:monospace;background:#0d1117;color:#c9d1d9;padding:20px;line-height:1.8}
.ok{color:#3fb950}.err{color:#f85149}.warn{color:#d29922}.info{color:#58a6ff}
h1{color:#bc8cff}h2{color:#39d353}
a.btn{display:inline-block;padding:10px 20px;background:#238636;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;margin:5px}
</style></head><body>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
$zipPath = __DIR__ . '/deploy_update.zip';

echo "<h1>📦 Extract Deploy Update</h1>";

if (!file_exists($zipPath)) {
    echo "<p class='err'>❌ File deploy_update.zip tidak ditemukan di folder public/</p>";
    echo "<p class='info'>Upload deploy_update.zip ke public_html/pembdahub/public/ terlebih dahulu.</p>";
    echo "</body></html>";
    exit;
}

echo "<p class='info'>ZIP size: " . number_format(filesize($zipPath)) . " bytes</p>";

$zip = new ZipArchive();
if ($zip->open($zipPath) !== TRUE) {
    echo "<p class='err'>❌ Gagal membuka ZIP file</p>";
    echo "</body></html>";
    exit;
}

echo "<h2>📂 Extracting " . $zip->numFiles . " files...</h2>";

$success = 0;
$failed = 0;

for ($i = 0; $i < $zip->numFiles; $i++) {
    $filename = $zip->getNameIndex($i);
    $targetPath = "{$laravelRoot}/{$filename}";
    
    // Buat directory jika belum ada
    $dir = dirname($targetPath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    // Extract file
    $content = $zip->getFromIndex($i);
    if ($content === false) {
        echo "<p class='err'>❌ Gagal baca dari ZIP: {$filename}</p>";
        $failed++;
        continue;
    }
    
    $written = @file_put_contents($targetPath, $content);
    if ($written !== false) {
        echo "<p class='ok'>✅ {$filename} (" . number_format(strlen($content)) . " bytes)</p>";
        $success++;
    } else {
        echo "<p class='err'>❌ Gagal tulis: {$filename}</p>";
        $failed++;
    }
}

$zip->close();

// Clear cache
echo "<h2>🧹 Clear Cache</h2>";
$viewCacheDir = "{$laravelRoot}/storage/framework/views/";
if (is_dir($viewCacheDir)) {
    $cacheFiles = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($cacheFiles as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ {$cleared} view cache dihapus.</p>";
}
foreach (['config.php','routes-v7.php','packages.php','services.php','events.php'] as $cf) {
    $fp = "{$laravelRoot}/bootstrap/cache/{$cf}";
    if (file_exists($fp) && @unlink($fp)) echo "<p class='ok'>✅ Deleted: bootstrap/cache/{$cf}</p>";
}

// Verifikasi
echo "<h2>✅ Verifikasi Fix</h2>";
$checks = [
    ['file' => 'app/Http/Controllers/Admin/ScheduleGridController.php',
     'marker' => '$schools->first() ? $schools->first()->id : null',
     'label' => 'ScheduleGridController (default school fix)'],
    ['file' => 'app/Http/Controllers/Admin/TeachingAssignmentController.php',
     'marker' => 'Prioritas: (1) teacher',
     'label' => 'TeachingAssignmentController (school auto-detect)'],
    ['file' => 'app/Http/Controllers/Admin/TimeSlotController.php',
     'marker' => '$schools->first() ? $schools->first()->id : null',
     'label' => 'TimeSlotController (default school fix)'],
    ['file' => 'resources/views/admin/assignments/teaching/create.blade.php',
     'marker' => 'school_filter', 'label' => 'create.blade.php (school_filter TIDAK ADA)', 'invert' => true],
    ['file' => 'resources/views/admin/assignments/teaching/create.blade.php',
     'marker' => 'Informasi Pilihan Mata Pelajaran',
     'label' => 'create.blade.php (info banner kompetensi)'],
];
foreach ($checks as $c) {
    $path = "{$laravelRoot}/{$c['file']}";
    if (!file_exists($path)) { echo "<p class='err'>❌ NOT FOUND: {$c['label']}</p>"; continue; }
    $content = file_get_contents($path);
    $found = strpos($content, $c['marker']) !== false;
    $invert = $c['invert'] ?? false;
    $ok = $invert ? !$found : $found;
    echo "<p class='" . ($ok ? 'ok' : 'err') . "'>" . ($ok ? '✅' : '❌') . " {$c['label']}</p>";
}

// Summary
echo "<h2>📊 Ringkasan</h2>";
echo "<p class='ok'>✅ Berhasil: <b>{$success}</b></p>";
echo "<p class='" . ($failed > 0 ? 'err' : 'ok') . "'>❌ Gagal: <b>{$failed}</b></p>";

// Hapus ZIP dan script
echo "<h2>🗑️ Cleanup</h2>";
if (@unlink($zipPath)) echo "<p class='ok'>✅ deploy_update.zip dihapus</p>";

echo "<h2>🔗 Test Sekarang</h2>";
echo "<a class='btn' href='https://perguruanpembda.com/admin/dashboard' target='_blank'>Dashboard</a> ";
echo "<a class='btn' href='https://perguruanpembda.com/admin/schedules/grid' target='_blank'>Jadwal Grid</a> ";
echo "<a class='btn' href='https://perguruanpembda.com/admin/assignments/teaching' target='_blank'>Penugasan Mengajar</a>";

echo "<hr><p class='warn'>⚠️ Hapus extract_deploy.php, deploy_latest.php, check_commit.php, fix_deploy.php setelah selesai!</p>";
echo "</body></html>";
