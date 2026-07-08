<?php
/**
 * Force Git Reset & Pull - PembdaHUB
 * Upload ke public_html/, akses sekali, lalu HAPUS.
 * Akses: https://perguruanpembda.com/git_reset.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Git Force Reset</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:12px;border-radius:8px;overflow-x:auto;font-size:13px}</style></head><body>";
echo "<h1>⚙️ Git Force Reset & Pull</h1>";

$laravelRoot = realpath(__DIR__ . '/../pembdahub');
// Fallback: mungkin script ini diakses dari public_html dan pembdahub ada di sana
if (!$laravelRoot || !is_dir($laravelRoot . '/.git')) {
    // Coba path lain
    $laravelRoot = realpath(__DIR__ . '/../../');
    if (!is_dir($laravelRoot . '/.git')) {
        // Coba satu level lagi
        $candidates = [
            realpath(__DIR__ . '/../pembdahub'),
            realpath(__DIR__ . '/..'),
            realpath(__DIR__ . '/../..'),
            '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub',
        ];
        foreach ($candidates as $c) {
            if ($c && is_dir($c . '/.git')) {
                $laravelRoot = $c;
                break;
            }
        }
    }
}

echo "<p class='info'>ℹ️ Script location (__DIR__): <b>" . __DIR__ . "</b></p>";
echo "<p class='info'>ℹ️ Laravel root ditemukan: <b>" . ($laravelRoot ?: 'TIDAK DITEMUKAN') . "</b></p>";

if (!$laravelRoot || !is_dir($laravelRoot . '/.git')) {
    echo "<p class='err'>❌ Folder .git tidak ditemukan. Tidak bisa menjalankan git commands.</p>";
    echo "<p class='warn'>⚠️ Path yang dicari: " . implode(', ', $candidates ?? []) . "</p>";
    die("</body></html>");
}

// Cek apakah exec() tersedia
if (!function_exists('exec')) {
    echo "<p class='err'>❌ Fungsi exec() dinonaktifkan di server ini. Tidak bisa menjalankan git via PHP.</p>";
    echo "<p class='warn'>Gunakan opsi manual: hPanel → Git → Deploy atau ZIP upload.</p>";
    die("</body></html>");
}

// Test exec
$testOutput = [];
exec('echo test', $testOutput, $retCode);
if (empty($testOutput)) {
    echo "<p class='err'>❌ exec() tersedia tapi tidak bisa menjalankan command shell.</p>";
    die("</body></html>");
}

echo "<p class='ok'>✅ exec() tersedia dan berfungsi!</p>";

// ============================
// Jalankan git commands
// ============================
$steps = [
    ['cmd' => "git -C {$laravelRoot} status --short", 'label' => '📋 Git Status (sebelum reset)'],
    ['cmd' => "git -C {$laravelRoot} fetch origin main 2>&1", 'label' => '📥 Git Fetch dari GitHub'],
    ['cmd' => "git -C {$laravelRoot} reset --hard origin/main 2>&1", 'label' => '🔄 Force Reset ke origin/main'],
    ['cmd' => "git -C {$laravelRoot} log --oneline -5 2>&1", 'label' => '📌 Git Log (5 commit terakhir)'],
];

foreach ($steps as $step) {
    echo "<h2>{$step['label']}</h2>";
    $output = [];
    $retCode = 0;
    exec($step['cmd'], $output, $retCode);
    $outputStr = implode("\n", $output);
    $class = $retCode === 0 ? 'ok' : 'err';
    echo "<pre class='{$class}'>" . htmlspecialchars($outputStr ?: '(kosong)') . "</pre>";
    if ($retCode !== 0) {
        echo "<p class='err'>⚠️ Exit code: $retCode</p>";
    }
}

// Clear view cache setelah reset
echo "<h2>🧹 Bersihkan Cache Views</h2>";
$viewCacheDir = $laravelRoot . '/storage/framework/views/';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ $cleared compiled view cache dihapus.</p>";
}

// Clear bootstrap cache
$bootstrapCacheDir = $laravelRoot . '/bootstrap/cache/';
$cacheFiles = ['config.php', 'routes-v7.php', 'packages.php', 'services.php'];
foreach ($cacheFiles as $cf) {
    $fp = $bootstrapCacheDir . $cf;
    if (file_exists($fp) && @unlink($fp)) {
        echo "<p class='ok'>✅ Deleted: bootstrap/cache/$cf</p>";
    }
}

// Verify controller version
echo "<h2>✅ Verifikasi Controller</h2>";
$ctrlPath = $laravelRoot . '/app/Http/Controllers/Admin/TeachingAssignmentController.php';
if (file_exists($ctrlPath)) {
    $content = file_get_contents($ctrlPath);
    if (strpos($content, 'Prioritas: (1) teacher') !== false) {
        echo "<p class='ok'>✅ TeachingAssignmentController: <b>VERSI TERBARU!</b></p>";
    } else {
        echo "<p class='err'>❌ TeachingAssignmentController masih versi lama.</p>";
    }
} else {
    echo "<p class='err'>❌ Controller tidak ditemukan!</p>";
}

echo "<hr>";
echo "<p class='warn' style='font-weight:bold'>⚠️ HAPUS file git_reset.php setelah selesai!</p>";
echo "<p><a href='https://perguruanpembda.com/clear-cache.php?secret=pembda99' style='color:#03dac6'>→ Jalankan clear-cache.php untuk refresh semua cache</a></p>";
echo "</body></html>";
