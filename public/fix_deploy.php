<?php
/**
 * Fix Git Deploy - Kembalikan remote ke SSH + bersihkan working tree
 * Upload ke public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/fix_deploy.php?secret=pembda99
 * 
 * Setelah script ini berhasil, klik Deploy di hPanel → GIT
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Fix Git Deploy</title>";
echo "<style>
body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}
.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}
h1{color:#bb86fc}h2{color:#03dac6}
pre{background:#16213e;padding:12px;border-radius:8px;overflow-x:auto;font-size:12px}
.box{background:#16213e;border:1px solid #333;border-radius:12px;padding:20px;margin:20px 0}
.step{background:#0d1b2a;border-left:4px solid #03dac6;padding:12px 16px;margin:10px 0;border-radius:0 8px 8px 0}
a.btn{display:inline-block;padding:10px 20px;background:#03dac6;color:#000;text-decoration:none;border-radius:8px;font-weight:bold;margin:5px}
</style></head><body>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
$sshRemote = 'git@github.com:YulianusZega/pembdahub.git';

function runCmd(string $cmd, string $label): array {
    echo "<h2>{$label}</h2>";
    $output = [];
    $retCode = 0;
    exec($cmd . ' 2>&1', $output, $retCode);
    $out = implode("\n", $output);
    $class = $retCode === 0 ? 'ok' : 'err';
    echo "<pre class='{$class}'>" . htmlspecialchars($out ?: '(kosong)') . "</pre>";
    if ($retCode !== 0) echo "<p class='err'>⚠️ Exit code: {$retCode}</p>";
    return ['output' => $out, 'code' => $retCode];
}

echo "<h1>🔧 Fix Git Deploy untuk hPanel</h1>";
echo "<p class='info'>Script ini mempersiapkan repository agar hPanel Git Deploy bisa berfungsi kembali.</p>";

// Step 1: Cek remote saat ini
$result = runCmd("git -C {$laravelRoot} remote get-url origin", "1️⃣ Remote URL Saat Ini");
$currentRemote = trim($result['output']);

// Step 2: Kembalikan ke SSH jika masih HTTPS
if (strpos($currentRemote, 'https://') !== false) {
    echo "<p class='warn'>⚠️ Remote masih HTTPS. Mengubah kembali ke SSH...</p>";
    runCmd("git -C {$laravelRoot} remote set-url origin {$sshRemote}", "2️⃣ Ubah Remote → SSH");
    runCmd("git -C {$laravelRoot} remote get-url origin", "✅ Konfirmasi Remote");
} elseif (strpos($currentRemote, 'git@github.com') !== false) {
    echo "<p class='ok'>✅ Remote sudah SSH. Tidak perlu diubah.</p>";
} else {
    echo "<p class='warn'>⚠️ Remote URL tidak dikenali: {$currentRemote}</p>";
    runCmd("git -C {$laravelRoot} remote set-url origin {$sshRemote}", "2️⃣ Set Remote → SSH");
}

// Step 3: Cek status working tree
$result = runCmd("git -C {$laravelRoot} status --short", "3️⃣ Git Status (Working Tree)");
$statusOutput = trim($result['output']);

if (!empty($statusOutput)) {
    echo "<p class='warn'>⚠️ Ada file yang dimodifikasi/untracked. Membersihkan...</p>";
    
    // Reset tracked files
    runCmd("git -C {$laravelRoot} checkout -- .", "🧹 Reset tracked files");
    
    // List untracked files (tapi JANGAN hapus semua - hanya yang tidak penting)
    $untrackedResult = runCmd("git -C {$laravelRoot} ls-files --others --exclude-standard", "📋 Untracked files");
    
    // Hapus file patch/diagnostic yang kita buat
    $patchFiles = [
        'public/patch_controller.php',
        'public/patch_teaching.php', 
        'public/patch_mapel_info.php',
        'public/patch_schedules.php',
        'public/git_reset.php',
        'public/git_fix.php',
        'public/fix_view.php',
        'public/deploy_files.php',
        'public/verify_deploy.php',
        'public/populate_timeslots.php',
        'public/restore_tp.php',
        'public/restore_tp_2025.php',
    ];
    
    echo "<h2>🗑️ Hapus File Patch/Diagnostic</h2>";
    foreach ($patchFiles as $pf) {
        $fullPath = "{$laravelRoot}/{$pf}";
        if (file_exists($fullPath)) {
            if (@unlink($fullPath)) {
                echo "<p class='ok'>✅ Dihapus: {$pf}</p>";
            } else {
                echo "<p class='err'>❌ Gagal hapus: {$pf}</p>";
            }
        }
    }
    
    // Hapus folder temporary yang dibuat oleh git (*.XXXX pattern)
    $tempDirs = glob("{$laravelRoot}/*.*/");
    foreach ($tempDirs as $dir) {
        $dirName = basename($dir);
        if (preg_match('/\.\d{4}\/?$/', $dirName)) {
            exec("rm -rf " . escapeshellarg($dir));
            echo "<p class='ok'>✅ Dihapus temp dir: {$dirName}</p>";
        }
    }
} else {
    echo "<p class='ok'>✅ Working tree bersih!</p>";
}

// Step 4: Cek commit saat ini
runCmd("git -C {$laravelRoot} log --oneline -5", "4️⃣ Commit Saat Ini di Server");

// Step 5: Cek commit terbaru di GitHub (via git log saja, tidak perlu fetch)
echo "<h2>5️⃣ Info Sinkronisasi</h2>";
$localHead = trim(shell_exec("git -C {$laravelRoot} rev-parse --short HEAD 2>&1"));
echo "<p class='info'>Commit di server: <b>{$localHead}</b></p>";
echo "<p class='warn'>Commit terbaru di GitHub perlu dicek manual (server tidak bisa fetch via exec).</p>";

// Step 6: Clear cache
echo "<h2>6️⃣ Bersihkan Cache</h2>";
$viewCacheDir = "{$laravelRoot}/storage/framework/views/";
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ {$cleared} view cache dihapus.</p>";
}
foreach (['config.php','routes-v7.php','packages.php','services.php','events.php'] as $cf) {
    $fp = "{$laravelRoot}/bootstrap/cache/{$cf}";
    if (file_exists($fp) && @unlink($fp)) echo "<p class='ok'>✅ Deleted: bootstrap/cache/{$cf}</p>";
}

// Step 7: Instruksi selanjutnya
echo "<div class='box'>";
echo "<h2 style='margin-top:0'>📋 Langkah Selanjutnya</h2>";
echo "<div class='step'><b>Step 1:</b> Buka <b>hPanel → Tingkat Lanjut → GIT</b></div>";
echo "<div class='step'><b>Step 2:</b> Klik tombol <b>⋮ (titik tiga)</b> di sebelah repository</div>";
echo "<div class='step'><b>Step 3:</b> Klik <b>Deploy</b> atau <b>Force Deploy</b></div>";
echo "<div class='step'><b>Step 4:</b> Tunggu sampai selesai (biasanya 10-30 detik)</div>";
echo "<div class='step'><b>Step 5:</b> Jalankan clear-cache: <a href='https://perguruanpembda.com/clear-cache.php?secret=pembda99' class='btn' target='_blank'>Clear Cache</a></div>";
echo "<div class='step'><b>Step 6:</b> Test aplikasi: <a href='https://perguruanpembda.com/admin/dashboard' class='btn' target='_blank'>Dashboard</a></div>";
echo "</div>";

echo "<hr>";
echo "<p class='warn'><b>⚠️ HAPUS file fix_deploy.php setelah Deploy berhasil!</b></p>";
echo "<p class='info'>ℹ️ File ini sendiri (<code>fix_deploy.php</code>) juga akan otomatis terhapus saat Deploy berhasil, karena file ini tidak ada di GitHub.</p>";
echo "</body></html>";
