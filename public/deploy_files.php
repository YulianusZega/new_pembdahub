<?php
/**
 * Direct File Deploy - Download file langsung dari GitHub Raw URL
 * Upload ke public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/deploy_files.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Direct File Deploy</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:12px;border-radius:8px;overflow-x:auto;font-size:12px;max-height:200px;overflow-y:auto}</style></head><body>";
echo "<h1>🚀 Direct File Deploy dari GitHub</h1>";
echo "<p class='info'>Mengunduh file langsung dari GitHub Raw tanpa perlu git credentials.</p>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
$branch = 'main';
$repo = 'YulianusZega/pembdahub';
$rawBase = "https://raw.githubusercontent.com/{$repo}/{$branch}";

// Daftar file yang perlu diperbarui dari GitHub
$files = [
    // Controller - fix school filter & auto-select TP/Semester
    'app/Http/Controllers/Admin/TeachingAssignmentController.php',
    // View - hapus school filter + tambah info banner kompetensi
    'resources/views/admin/assignments/teaching/create.blade.php',
    // Seeder - support semua sekolah (SMP, SMA, SMK) + academic_year_id
    'database/seeders/TimeSlotsSeeder.php',
    // Index view - fix tombol tambah parameter
    'resources/views/admin/assignments/teaching/index.blade.php',
];

// Cek allow_url_fopen
if (!ini_get('allow_url_fopen')) {
    echo "<p class='warn'>⚠️ allow_url_fopen OFF. Mencoba dengan cURL...</p>";
}

function downloadFile(string $url): string|false {
    // Coba file_get_contents dulu
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: PHP-Deploy\r\n",
            'timeout' => 30,
        ],
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
        ]
    ]);
    
    $content = @file_get_contents($url, false, $context);
    if ($content !== false) return $content;
    
    // Fallback ke cURL
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'PHP-Deploy',
        ]);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($result !== false && $httpCode === 200) return $result;
    }
    
    return false;
}

echo "<h2>📥 Download & Deploy File</h2>";
$successCount = 0;
$failCount = 0;

foreach ($files as $filePath) {
    $rawUrl = "{$rawBase}/{$filePath}";
    $localPath = "{$laravelRoot}/{$filePath}";
    
    echo "<div style='margin:8px 0;padding:10px;background:#16213e;border-radius:8px;border-left:3px solid #444'>";
    echo "<p style='margin:0'><b style='color:#40c4ff'>{$filePath}</b></p>";
    
    // Download dari GitHub
    $content = downloadFile($rawUrl);
    
    if ($content === false) {
        echo "<p class='err'>❌ Gagal download dari GitHub: {$rawUrl}</p>";
        $failCount++;
        echo "</div>";
        continue;
    }
    
    // Pastikan direktori ada
    $dir = dirname($localPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    
    // Backup file lama
    $backupPath = $localPath . '.bak.' . date('YmdHis');
    if (file_exists($localPath)) {
        copy($localPath, $backupPath);
    }
    
    // Tulis file baru
    $bytes = file_put_contents($localPath, $content);
    
    if ($bytes !== false) {
        echo "<p class='ok'>✅ Berhasil! " . number_format(strlen($content)) . " bytes ditulis.</p>";
        $successCount++;
    } else {
        echo "<p class='err'>❌ Gagal menulis ke: {$localPath}</p>";
        // Restore backup jika ada
        if (file_exists($backupPath)) copy($backupPath, $localPath);
        $failCount++;
    }
    
    // Hapus backup jika berhasil
    if ($bytes !== false && file_exists($backupPath)) {
        @unlink($backupPath);
    }
    
    echo "</div>";
}

echo "<h2>📊 Ringkasan</h2>";
echo "<p class='" . ($failCount === 0 ? 'ok' : 'warn') . "'>✅ Berhasil: <b>{$successCount}</b> file | ❌ Gagal: <b>{$failCount}</b> file</p>";

// Verifikasi
echo "<h2>✅ Verifikasi Konten</h2>";
$checks = [
    ['path' => 'app/Http/Controllers/Admin/TeachingAssignmentController.php', 'marker' => 'Prioritas: (1) teacher', 'label' => 'Controller (fix school)'],
    ['path' => 'resources/views/admin/assignments/teaching/create.blade.php', 'marker' => 'Informasi Pilihan Mata Pelajaran', 'label' => 'View (info banner)'],
    ['path' => 'resources/views/admin/assignments/teaching/create.blade.php', 'marker' => 'Mapel tidak muncul', 'label' => 'View (mapel hint)'],
    ['path' => 'resources/views/admin/assignments/teaching/create.blade.php', 'marker' => 'school_filter', 'label' => 'View (school filter HARUS TIDAK ADA)', 'invert' => true],
    ['path' => 'database/seeders/TimeSlotsSeeder.php', 'marker' => 'getSlotsForSchool', 'label' => 'TimeSlotsSeeder (semua sekolah)'],
];

foreach ($checks as $check) {
    $fullPath = "{$laravelRoot}/{$check['path']}";
    if (!file_exists($fullPath)) {
        echo "<p class='err'>❌ FILE TIDAK ADA: {$check['label']}</p>";
        continue;
    }
    $content = file_get_contents($fullPath);
    $found = strpos($content, $check['marker']) !== false;
    $invert = $check['invert'] ?? false;
    $ok = $invert ? !$found : $found;
    $status = $ok ? '✅' : '❌';
    $class = $ok ? 'ok' : 'err';
    $detail = $ok ? 'BENAR ✓' : 'PERLU DICEK ✗';
    echo "<p class='$class'>$status {$check['label']}: $detail</p>";
}

// Clear cache
echo "<h2>🧹 Bersihkan Cache</h2>";
$viewCacheDir = "$laravelRoot/storage/framework/views/";
if (is_dir($viewCacheDir)) {
    $files2 = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files2 as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ $cleared view cache dihapus.</p>";
}
foreach (['config.php','routes-v7.php','packages.php','services.php'] as $cf) {
    $fp = "$laravelRoot/bootstrap/cache/$cf";
    if (file_exists($fp) && @unlink($fp)) echo "<p class='ok'>✅ Deleted: bootstrap/cache/$cf</p>";
}

echo "<hr>";
echo "<p class='warn'><b>⚠️ HAPUS deploy_files.php setelah selesai!</b></p>";
echo "<p><a href='https://perguruanpembda.com/admin/assignments/teaching/create?teacher_id=295' target='_blank' style='color:#03dac6'>→ Test halaman penugasan mengajar</a></p>";
echo "</body></html>";
