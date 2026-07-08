<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Pastikan hanya bisa diakses dengan secret token
if (!isset($_GET['secret']) || $_GET['secret'] !== 'pembda99') {
    die("Akses Ditolak.");
}

echo "<div style='font-family: sans-serif; padding: 20px; max-width: 800px; margin: auto;'>";
echo "<h2 style='color: #2563eb;'>🛠️ Debug Logika Notifikasi WA Survei</h2>";

// 1. Cek Survei Aktif
echo "<h3>1. Mengecek Survei Aktif & Syarat 10 Menit</h3>";
$surveys = \App\Models\Survey::where('status', 'active')->get();

if ($surveys->isEmpty()) {
    echo "<p style='color: red;'>Tidak ada survei yang berstatus 'active'. (Mungkin sudah tertutup?)</p>";
} else {
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background: #f1f5f9;'><th>ID</th><th>Judul</th><th>Waktu Tutup (end_date)</th><th>Status Cron</th></tr>";
    
    foreach ($surveys as $s) {
        echo "<tr>";
        echo "<td>{$s->id}</td>";
        echo "<td>{$s->title}</td>";
        echo "<td>" . ($s->end_date ? $s->end_date->format('Y-m-d H:i:s') : '<span style="color:red;">Kosong</span>') . "</td>";
        
        echo "<td>";
        if ($s->end_date) {
            $diff = now()->diffInMinutes($s->end_date, false);
            // $diff bernilai negatif jika end_date di masa lalu
            if ($diff <= -10) {
                echo "<span style='color: green; font-weight: bold;'>✅ SIAP DIPROSES (Sudah lewat " . abs($diff) . " menit)</span>";
            } else {
                echo "<span style='color: orange; font-weight: bold;'>⏳ BELUM SAATNYA (Baru lewat " . abs($diff) . " menit dari batas 10 mnt, atau belum lewat sama sekali)</span>";
            }
        } else {
            echo "-";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// 2. Cek Kepala Sekolah
echo "<h3>2. Mengecek Data Kepala Sekolah SMK</h3>";
// Cari SMK (asumsi nama sekolah ada kata SMK atau id tertentu)
$sekolahSMK = \App\Models\School::where('name', 'like', '%SMK%')->first();
if ($sekolahSMK) {
    echo "<p>Sekolah Ditemukan: <b>{$sekolahSMK->name}</b></p>";
    if ($sekolahSMK->principal_id) {
        $kepsek = \App\Models\Teacher::find($sekolahSMK->principal_id);
        if ($kepsek) {
            echo "<p>Kepala Sekolah: <b>{$kepsek->user->name}</b></p>";
            echo "<p>Nomor WA di Database: <b>" . ($kepsek->phone ?? '<span style="color:red;">KOSONG</span>') . "</b></p>";
        } else {
            echo "<p style='color: red;'>Data Guru dengan ID {$sekolahSMK->principal_id} tidak ditemukan di tabel teachers.</p>";
        }
    } else {
        echo "<p style='color: red;'>Sekolah ini belum memiliki Kepala Sekolah yang di-assign (principal_id kosong).</p>";
    }
} else {
    echo "<p>Data sekolah SMK tidak ditemukan secara otomatis.</p>";
}

// 3. Tes Eksekusi Command
echo "<h3>3. Menjalankan Command (surveys:close-and-notify) secara Manual</h3>";
echo "<p><i>Mengeksekusi artisan command sekarang...</i></p>";
try {
    \Illuminate\Support\Facades\Artisan::call('surveys:close-and-notify');
    $output = \Illuminate\Support\Facades\Artisan::output();
    echo "<div style='background: #1e293b; color: #a5b4fc; padding: 15px; border-radius: 8px; font-family: monospace; white-space: pre-wrap;'>";
    echo $output ?: "Command berjalan tanpa output (Mungkin tidak ada survei yang memenuhi syarat).";
    echo "</div>";
} catch (\Exception $e) {
    echo "<div style='background: #fef2f2; color: #991b1b; padding: 15px; border-radius: 8px; font-family: monospace;'>Error: " . $e->getMessage() . "</div>";
}

echo "</div>";
