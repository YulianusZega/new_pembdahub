<?php
/**
 * Verify Deploy - Cek apakah fresh clone berhasil dan semua file terbaru
 * Akses: https://perguruanpembda.com/verify_deploy.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Verify Deploy</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}table{border-collapse:collapse;width:100%}td,th{padding:6px 12px;border:1px solid #333;text-align:left}th{background:#16213e}</style></head><body>";
echo "<h1>🔍 Verify Deploy - PembdaHUB</h1>";

$base = __DIR__ . '/../';

// ========================
// 1. Cek Git commit terbaru di server
// ========================
echo "<h2>1. Git Status di Server</h2>";
$gitDir = $base . '.git';
if (is_dir($gitDir)) {
    $headFile = $gitDir . '/refs/heads/main';
    $headCommit = file_exists($headFile) ? trim(file_get_contents($headFile)) : 'unknown';
    $fetchHead = file_exists($gitDir . '/FETCH_HEAD') ? trim(file_get_contents($gitDir . '/FETCH_HEAD')) : 'N/A';
    echo "<p class='ok'>✅ .git folder ditemukan</p>";
    echo "<p class='info'>📌 Commit HEAD (main): <b>$headCommit</b></p>";
    echo "<p class='info'>📥 FETCH_HEAD: " . substr($fetchHead, 0, 80) . "</p>";
} else {
    echo "<p class='err'>❌ .git folder TIDAK ditemukan! Fresh clone mungkin ke folder lain.</p>";
}

// ========================
// 2. Cek isi controller (versi baru vs lama)
// ========================
echo "<h2>2. Verifikasi Controller</h2>";
$ctrlPath = $base . 'app/Http/Controllers/Admin/TeachingAssignmentController.php';
if (file_exists($ctrlPath)) {
    $content = file_get_contents($ctrlPath);
    $size = number_format(strlen($content));
    
    if (strpos($content, 'Prioritas: (1) teacher') !== false) {
        echo "<p class='ok'>✅ TeachingAssignmentController: <b>VERSI TERBARU</b> ($size bytes)</p>";
    } else {
        echo "<p class='err'>❌ TeachingAssignmentController: <b>MASIH VERSI LAMA</b> ($size bytes) - Fresh clone belum berhasil</p>";
    }
} else {
    echo "<p class='err'>❌ Controller tidak ditemukan!</p>";
}

// ========================
// 3. Cek view create.blade.php
// ========================
echo "<h2>3. Verifikasi View</h2>";
$viewPath = $base . 'resources/views/admin/assignments/teaching/create.blade.php';
if (file_exists($viewPath)) {
    $content = file_get_contents($viewPath);
    $size = number_format(strlen($content));
    
    $hasSchoolFilter = strpos($content, 'school_filter') !== false || strpos($content, 'Semua Sekolah') !== false;
    $hasBanner = strpos($content, 'Informasi Pilihan Mata Pelajaran') !== false;
    $hasHint = strpos($content, 'Mapel tidak muncul') !== false;
    
    echo "<p class='" . ($hasSchoolFilter ? 'err' : 'ok') . "'>" . ($hasSchoolFilter ? '❌' : '✅') . " Filter Sekolah: " . ($hasSchoolFilter ? 'MASIH ADA (lama)' : 'SUDAH DIHAPUS (terbaru)') . " ($size bytes)</p>";
    echo "<p class='" . ($hasBanner ? 'ok' : 'warn') . "'>" . ($hasBanner ? '✅' : '⚠️') . " Info Banner Kompetensi: " . ($hasBanner ? 'ADA' : 'BELUM ADA') . "</p>";
    echo "<p class='" . ($hasHint ? 'ok' : 'warn') . "'>" . ($hasHint ? '✅' : '⚠️') . " Hint Mapel tidak muncul: " . ($hasHint ? 'ADA' : 'BELUM ADA') . "</p>";
} else {
    echo "<p class='err'>❌ View tidak ditemukan!</p>";
}

// ========================
// 4. Cek TimeSlotsSeeder (versi baru support semua sekolah)
// ========================
echo "<h2>4. Verifikasi TimeSlotsSeeder</h2>";
$seederPath = $base . 'database/seeders/TimeSlotsSeeder.php';
if (file_exists($seederPath)) {
    $content = file_get_contents($seederPath);
    $isNew = strpos($content, 'getSlotsForSchool') !== false;
    echo "<p class='" . ($isNew ? 'ok' : 'warn') . "'>" . ($isNew ? '✅' : '⚠️') . " TimeSlotsSeeder: " . ($isNew ? 'VERSI TERBARU (support semua sekolah)' : 'VERSI LAMA (hanya SMK)') . "</p>";
} else {
    echo "<p class='err'>❌ TimeSlotsSeeder tidak ditemukan!</p>";
}

// ========================
// 5. Cek time_slots data di DB
// ========================
echo "<h2>5. Data Time Slots di Database</h2>";
try {
    require $base . 'vendor/autoload.php';
    $app = require_once $base . 'bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $total = DB::table('time_slots')->count();
    $bySchool = DB::table('time_slots')
        ->join('schools', 'time_slots.school_id', '=', 'schools.id')
        ->select('schools.name', DB::raw('count(*) as total'))
        ->groupBy('schools.id', 'schools.name')
        ->get();
    
    if ($total > 0) {
        echo "<p class='ok'>✅ Total time slots: <b>$total records</b></p>";
        echo "<table><tr><th>Sekolah</th><th>Jumlah Slot</th></tr>";
        foreach ($bySchool as $row) {
            echo "<tr><td>{$row->name}</td><td>{$row->total}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warn'>⚠️ Time slots KOSONG. Perlu diisi via seeder.</p>";
        echo "<p><a href='?secret=pembda99&seed=1' style='background:#03dac6;color:#000;padding:8px 16px;border-radius:6px;text-decoration:none;font-weight:bold'>▶ Jalankan TimeSlotsSeeder Sekarang</a></p>";
    }

    // Run seeder if requested
    if (isset($_GET['seed']) && $_GET['seed'] == '1') {
        echo "<h2>Menjalankan TimeSlotsSeeder...</h2>";
        $seeder = new Database\Seeders\TimeSlotsSeeder();
        $seeder->run();
        $newTotal = DB::table('time_slots')->count();
        echo "<p class='ok'>✅ Seeder selesai! Total slots sekarang: $newTotal</p>";
    }
} catch (Exception $e) {
    echo "<p class='err'>❌ Error DB: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// ========================
// 6. Ringkasan
// ========================
echo "<h2>6. Ringkasan & Tindakan</h2>";
echo "<p class='info'>Commit lokal terbaru yang harus ada di server: <b>855f2cc</b></p>";
echo "<p>Jika controller/view masih versi lama → Fresh clone belum berhasil atau Deploy ke path yang salah.</p>";
echo "<hr><p style='color:#ff5252;font-weight:bold;'>⚠️ Hapus verify_deploy.php setelah selesai!</p>";
echo "</body></html>";
