<?php
/**
 * CEK & SYNC POIN PRESTASI - PembdaHUB
 * 
 * Akses: perguruanpembda.com/sync_prestasi_points.php?secret=pembda99
 */

$secret = $_GET['secret'] ?? '';
if ($secret !== 'pembda99') {
    http_response_code(403);
    die('<h2 style="color:red">403 Forbidden</h2>');
}

define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cek & Sync Poin Prestasi - PembdaHUB</title>
<style>
    body { background:#0f172a; color:#e2e8f0; font-family:'Segoe UI',sans-serif; padding:40px; }
    .card { background:#1e293b; border-radius:16px; padding:32px; max-width:800px; margin:0 auto; }
    table { width:100%; border-collapse:collapse; margin-top:20px; font-size:14px; }
    th { background:#0f172a; padding:12px; text-align:left; color:#94a3b8; }
    td { padding:12px; border-bottom:1px solid #334155; }
    .btn { background:#10b981; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:bold; display:inline-block; margin-top:20px; }
</style>
</head>
<body>
<div class="card">
    <h2>🏆 Cek Poin Prestasi (Mulai 10 Juli 2026)</h2>

<?php
try {
    $records = DB::table('student_counseling_records')
        ->join('students', 'students.id', '=', 'student_counseling_records.student_id')
        ->where('student_counseling_records.incident_date', '>=', '2026-07-10')
        ->where('student_counseling_records.record_type', 'penghargaan')
        ->select('student_counseling_records.*', 'students.full_name', 'students.user_id')
        ->get();

    echo "<table>";
    echo "<tr><th>Nama Siswa</th><th>Judul Prestasi</th><th>Tingkat</th><th>Status Log Poin</th><th>Tindakan Sistem</th></tr>";

    $levels = ['sekolah' => 50, 'kabupaten' => 100, 'propinsi' => 150, 'nasional' => 200, 'internasional' => 250];

    foreach ($records as $r) {
        $expectedPoints = $levels[$r->achievement_level] ?? 50;
        
        $hasLog = false;
        if ($r->user_id) {
            $hasLog = DB::table('reputation_logs')
                ->where('user_id', $r->user_id)
                ->where('reference_type', 'App\\Models\\StudentCounselingRecord')
                ->where('reference_id', $r->id)
                ->exists();
        }

        $status = $hasLog 
            ? "<span style='color:#34d399'>✅ Sudah Tercatat (+{$expectedPoints} Poin)</span>" 
            : "<span style='color:#f87171'>❌ Poin Belum Masuk</span>";

        $action = "-";
        
        // FORCE SYNC (Hapus log lama dan ulangi proses transfer poin)
        if ($r->user_id && isset($_GET['force']) && $_GET['force'] == '1') {
            DB::table('reputation_logs')
                ->where('reference_type', 'App\Models\StudentCounselingRecord')
                ->where('reference_id', $r->id)
                ->delete();
            $hasLog = false;
        }

        // SYNC OTOMATIS JIKA BELUM ADA POIN
        if (!$hasLog && $r->user_id && isset($_GET['sync']) && $_GET['sync'] == '1') {
            \App\Models\ReputationLog::log($r->user_id, $expectedPoints, 'achievement', $r->title, \App\Models\StudentCounselingRecord::find($r->id));
            $status = "<span style='color:#60a5fa'>🔄 Baru Saja Disinkronisasi (+{$expectedPoints} Poin)</span>";
            $action = "Synced";
        } elseif (!$hasLog && $r->user_id) {
            $action = "Butuh Sync";
        } elseif (!$r->user_id) {
            $action = "Siswa belum punya akun login!";
        }

        echo "<tr>";
        echo "<td><strong>{$r->full_name}</strong></td>";
        echo "<td>{$r->title}</td>";
        echo "<td><span style='text-transform:uppercase;font-size:12px;background:#3b82f633;color:#93c5fd;padding:2px 8px;border-radius:4px'>{$r->achievement_level}</span></td>";
        echo "<td>{$status}</td>";
        echo "<td>{$action}</td>";
        echo "</tr>";
    }
    echo "</table>";

    if (!isset($_GET['sync'])) {
        echo "<br><a href='?secret=pembda99&sync=1&force=1' class='btn'>⚡ Masukkan Poin yang Tertinggal Sekarang (Force Sync)</a>";
    } else {
        echo "<br><a href='https://perguruanpembda.com/admin/counseling' class='btn'>✅ Selesai - Buka Konseling</a>";
    }

} catch (\Exception $e) {
    echo "<div style='color:red'>Error: " . $e->getMessage() . "</div>";
}
?>
</div>
</body>
</html>
