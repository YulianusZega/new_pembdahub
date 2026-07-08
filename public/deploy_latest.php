<?php
/**
 * Deploy Manual: Download file terbaru dari GitHub (bypass git pull)
 * Upload ke: public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/deploy_latest.php?secret=pembda99
 * 
 * Latar belakang: hPanel deploy "berhasil" tapi kode tidak ter-update
 * karena git fetch gagal (SSH key issue). Script ini bypass git
 * dan download langsung dari GitHub raw URL.
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Forbidden'); }

set_time_limit(300);

echo "<html><head><title>Deploy Latest from GitHub</title>";
echo "<style>
body{font-family:monospace;background:#0d1117;color:#c9d1d9;padding:20px;line-height:1.8}
.ok{color:#3fb950}.err{color:#f85149}.warn{color:#d29922}.info{color:#58a6ff}
h1{color:#bc8cff}h2{color:#39d353;border-bottom:1px solid #30363d;padding-bottom:8px}
pre{background:#161b22;padding:12px;border-radius:8px;overflow-x:auto;font-size:12px;border:1px solid #30363d}
.summary{background:#161b22;border:1px solid #30363d;border-radius:12px;padding:20px;margin:20px 0}
a.btn{display:inline-block;padding:10px 20px;background:#238636;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;margin:5px}
</style></head><body>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
$branch = 'main';
$repo = 'YulianusZega/pembdahub';
$rawBase = "https://raw.githubusercontent.com/{$repo}/{$branch}";

// File-file yang perlu diperbarui (selisih commit ba29611 → 63a64c9)
$files = [
    // Controllers - FIX UTAMA
    'app/Http/Controllers/Admin/ScheduleGridController.php',
    'app/Http/Controllers/Admin/TeachingAssignmentController.php',
    'app/Http/Controllers/Admin/TimeSlotController.php',
    'app/Http/Controllers/Admin/AcademicYearController.php',
    
    // Views - FIX UTAMA
    'resources/views/admin/assignments/teaching/create.blade.php',
    'resources/views/admin/assignments/teaching/index.blade.php',
    
    // Routes
    'routes/admin.php',
    'routes/web.php',
    
    // Seeders
    'database/seeders/TimeSlotsSeeder.php',
    'database/seeders/DatabaseSeeder.php',
    
    // Clear cache tool (updated version)
    'public/clear-cache.php',
    
    // Layout
    'resources/views/layouts/admin.blade.php',
    
    // API Controllers (TEFA + Attendance)
    'app/Http/Controllers/Api/AttendanceController.php',
    'app/Http/Controllers/Api/EmployeeAttendanceController.php',
    
    // TEFA module (new)
    'app/Http/Controllers/Admin/TefaController.php',
    'app/Models/TefaAttendance.php',
    'app/Models/TefaEmployee.php',
    'resources/views/admin/tefa/index.blade.php',
    'resources/views/admin/tefa/attendances.blade.php',
    'database/migrations/2026_07_05_160000_create_tefa_attendance_tables.php',
    'database/seeders/TefaEmployeeSeeder.php',
    
    // Build assets
    'public/build/manifest.json',
    'public/build/assets/app-CJfoEom5.css',
    'public/build/assets/app-DYB60j8S.css',
    
    // Agents config
    '.agents/AGENTS.md',
    '.agents/workflows/deploy.md',
];

function downloadFile(string $url): ?string {
    $ctx = stream_context_create([
        'http' => ['timeout' => 30, 'ignore_errors' => true],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false]
    ]);
    $content = @file_get_contents($url, false, $ctx);
    if ($content === false) {
        // Fallback: cURL
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_USERAGENT => 'PembdaHub-Deploy/1.0'
            ]);
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpCode !== 200) return null;
        } else {
            return null;
        }
    }
    // Check for GitHub 404
    if (strpos($content, '404: Not Found') !== false) return null;
    return $content;
}

echo "<h1>🚀 Deploy Latest dari GitHub</h1>";
echo "<p class='info'>Commit server saat ini: <b>ba29611</b> | Target: <b>63a64c9</b> (latest)</p>";
echo "<p class='info'>Total file: <b>" . count($files) . "</b></p>";

$dryRun = isset($_GET['dry_run']);
if ($dryRun) {
    echo "<p class='warn'>⚠️ MODE DRY RUN - tidak ada file yang diubah</p>";
}

$success = 0;
$failed = 0;
$skipped = 0;

foreach ($files as $file) {
    $url = "{$rawBase}/{$file}";
    $targetPath = "{$laravelRoot}/{$file}";
    
    echo "<div style='margin:8px 0;padding:4px 0;border-bottom:1px solid #21262d'>";
    
    // Download dari GitHub
    $content = downloadFile($url);
    
    if ($content === null) {
        echo "<span class='warn'>⏭️ SKIP (tidak ada di GitHub): {$file}</span>";
        $skipped++;
        echo "</div>";
        continue;
    }
    
    if ($dryRun) {
        $exists = file_exists($targetPath);
        echo "<span class='info'>📋 Would update: {$file} (" . strlen($content) . " bytes)" . ($exists ? '' : ' [NEW]') . "</span>";
        $success++;
        echo "</div>";
        continue;
    }
    
    // Buat directory jika belum ada
    $dir = dirname($targetPath);
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    
    // Backup file lama
    if (file_exists($targetPath)) {
        $backup = $targetPath . '.bak';
        @copy($targetPath, $backup);
    }
    
    // Tulis file baru
    $written = @file_put_contents($targetPath, $content);
    if ($written !== false) {
        echo "<span class='ok'>✅ {$file} (" . number_format(strlen($content)) . " bytes)</span>";
        $success++;
        // Hapus backup setelah sukses
        if (isset($backup) && file_exists($backup)) @unlink($backup);
    } else {
        echo "<span class='err'>❌ GAGAL: {$file}</span>";
        $failed++;
        // Restore backup
        if (isset($backup) && file_exists($backup)) @rename($backup, $targetPath);
    }
    unset($backup);
    echo "</div>";
}

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
     'marker' => 'school_filter', 'label' => 'create.blade.php (school_filter HARUS TIDAK ADA)', 'invert' => true],
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
echo "<div class='summary'>";
echo "<h2 style='margin-top:0'>📊 Ringkasan</h2>";
echo "<p class='ok'>✅ Berhasil: <b>{$success}</b></p>";
echo "<p class='" . ($failed > 0 ? 'err' : 'ok') . "'>❌ Gagal: <b>{$failed}</b></p>";
echo "<p class='info'>⏭️ Dilewati: <b>{$skipped}</b></p>";

echo "<h2>🔗 Test Sekarang</h2>";
echo "<a class='btn' href='https://perguruanpembda.com/admin/dashboard' target='_blank'>Dashboard</a> ";
echo "<a class='btn' href='https://perguruanpembda.com/admin/schedules/grid' target='_blank'>Jadwal Grid</a> ";
echo "<a class='btn' href='https://perguruanpembda.com/admin/assignments/teaching' target='_blank'>Penugasan Mengajar</a>";
echo "</div>";

echo "<hr><p class='warn'>⚠️ Hapus <code>deploy_latest.php</code> dan <code>check_commit.php</code> setelah selesai!</p>";
echo "</body></html>";
