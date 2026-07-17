<?php
/**
 * CLEANUP SCRIPT - Hapus Data Pembinaan & Prestasi Siswa
 * Sebelum Tanggal: 10 Juli 2026
 * Tabel: student_counseling_records
 *
 * Akses: perguruanpembda.com/cleanup_counseling.php?secret=pembda99
 * Preview: perguruanpembda.com/cleanup_counseling.php?secret=pembda99&dry_run=1
 * Hapus: perguruanpembda.com/cleanup_counseling.php?secret=pembda99&confirm=HAPUS
 */

// ── Keamanan ──
$secret = $_GET['secret'] ?? '';
if ($secret !== 'pembda99') {
    http_response_code(403);
    die('<h2 style="color:red">403 Forbidden - Secret key salah!</h2>');
}

$isDryRun  = isset($_GET['dry_run']) && $_GET['dry_run'] == '1';
$isConfirm = isset($_GET['confirm']) && $_GET['confirm'] === 'HAPUS';
$cutoffDate = '2026-07-10'; // Hapus data SEBELUM tanggal ini

// ── Bootstrap Laravel ──
define('LARAVEL_START', microtime(true));
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Cleanup Data Konseling - PembdaHUB</title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
    body { background: #0f172a; color: #e2e8f0; min-height: 100vh; padding: 40px 20px; }
    .card { background: #1e293b; border-radius: 16px; padding: 32px; max-width: 800px; margin: 0 auto; border: 1px solid #334155; }
    .header { display: flex; align-items: center; gap: 16px; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #334155; }
    .icon { font-size: 2.5rem; }
    h1 { font-size: 1.5rem; font-weight: 800; color: #f1f5f9; }
    h1 span { color: #f43f5e; }
    .subtitle { color: #94a3b8; font-size: .85rem; margin-top: 4px; }
    .info-box { background: #0f172a; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; border-left: 4px solid #6366f1; }
    .info-box p { font-size: .85rem; color: #94a3b8; line-height: 1.8; }
    .info-box strong { color: #f1f5f9; }
    .warning-box { background: #1c0a0a; border-radius: 10px; padding: 16px 20px; margin-bottom: 20px; border-left: 4px solid #f43f5e; }
    .warning-box p { font-size: .85rem; color: #fca5a5; line-height: 1.8; }
    .stat-row { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; }
    .stat { flex: 1; min-width: 140px; background: #0f172a; border-radius: 10px; padding: 16px; text-align: center; border: 1px solid #334155; }
    .stat .num { font-size: 2rem; font-weight: 900; }
    .stat .lbl { font-size: .72rem; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .06em; margin-top: 4px; }
    .stat.blue .num { color: #60a5fa; }
    .stat.rose .num { color: #f87171; }
    .stat.amber .num { color: #fbbf24; }
    table { width: 100%; border-collapse: collapse; font-size: .82rem; margin-bottom: 24px; }
    th { background: #0f172a; padding: 10px 14px; text-align: left; font-size: .7rem; text-transform: uppercase; letter-spacing: .08em; color: #64748b; border-bottom: 1px solid #334155; }
    td { padding: 10px 14px; border-bottom: 1px solid #1e293b; color: #cbd5e1; }
    tr:hover td { background: #0f172a55; }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 6px; font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; }
    .badge.prestasi { background: #1e3a8a33; color: #93c5fd; border: 1px solid #1e3a8a55; }
    .badge.pembinaan { background: #4c051933; color: #fca5a5; border: 1px solid #4c051955; }
    .btn-group { display: flex; gap: 12px; flex-wrap: wrap; }
    .btn { display: inline-block; padding: 12px 24px; border-radius: 10px; font-weight: 700; font-size: .875rem; text-decoration: none; cursor: pointer; border: none; transition: all .2s; }
    .btn-preview { background: #1d4ed8; color: #fff; }
    .btn-preview:hover { background: #2563eb; }
    .btn-danger { background: #be123c; color: #fff; }
    .btn-danger:hover { background: #e11d48; }
    .btn-safe { background: #065f46; color: #fff; }
    .btn-safe:hover { background: #059669; }
    .btn-back { background: #334155; color: #94a3b8; }
    .btn-back:hover { background: #475569; }
    .result-success { background: #052e16; border: 1px solid #166534; border-radius: 10px; padding: 20px; margin-top: 20px; color: #86efac; font-weight: 600; text-align: center; font-size: 1.1rem; }
    .result-error { background: #1c0a0a; border: 1px solid #7f1d1d; border-radius: 10px; padding: 20px; margin-top: 20px; color: #fca5a5; text-align: center; }
    .empty { text-align: center; padding: 40px; color: #64748b; font-size: .9rem; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <div class="icon">🗑️</div>
        <div>
            <h1>Cleanup Data <span>Pembinaan & Prestasi</span></h1>
            <p class="subtitle">Hapus catatan siswa sebelum tanggal cutoff · PembdaHUB Admin Tool</p>
        </div>
    </div>

<?php
// ── Query data yang akan dihapus ──
try {
    $records = DB::table('student_counseling_records')
        ->select(
            'student_counseling_records.id',
            'student_counseling_records.record_type',
            'student_counseling_records.title',
            'student_counseling_records.category',
            'student_counseling_records.incident_date',
            'student_counseling_records.created_at',
            'students.full_name as student_name'
        )
        ->leftJoin('students', 'students.id', '=', 'student_counseling_records.student_id')
        ->where('student_counseling_records.incident_date', '<', $cutoffDate)
        ->orderBy('student_counseling_records.incident_date', 'asc')
        ->get();

    $totalAll       = DB::table('student_counseling_records')->count();
    $totalAffected  = $records->count();
    $totalPrestasi  = $records->where('record_type', 'penghargaan')->count();
    $totalPembinaan = $records->where('record_type', '!=', 'penghargaan')->count();

} catch (\Exception $e) {
    echo '<div class="result-error">❌ Error koneksi database: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '</div></body></html>';
    exit;
}
?>

    <div class="info-box">
        <p>
            📅 <strong>Cutoff Date:</strong> Semua data dengan <code>incident_date</code> sebelum <strong>10 Juli 2026</strong> akan dihapus.<br>
            📋 <strong>Tabel target:</strong> <code>student_counseling_records</code><br>
            🔒 <strong>Data aman:</strong> Data siswa, kelas, guru, tahun pelajaran <strong>TIDAK tersentuh</strong>.
        </p>
    </div>

    {{-- Stats --}}
    <div class="stat-row">
        <div class="stat">
            <div class="num">{{ $totalAll }}</div>
            <div class="lbl">Total di DB</div>
        </div>
        <div class="stat rose">
            <div class="num">{{ $totalAffected }}</div>
            <div class="lbl">Akan Dihapus</div>
        </div>
        <div class="stat blue">
            <div class="num">{{ $totalPrestasi }}</div>
            <div class="lbl">Prestasi</div>
        </div>
        <div class="stat amber">
            <div class="num">{{ $totalPembinaan }}</div>
            <div class="lbl">Pembinaan</div>
        </div>
    </div>

<?php
// ── EKSEKUSI HAPUS ──
if ($isConfirm && !$isDryRun) {
    try {
        $deleted = DB::table('student_counseling_records')
            ->where('incident_date', '<', $cutoffDate)
            ->delete();

        echo "<div class='result-success'>✅ Berhasil! <strong>{$deleted} catatan</strong> dihapus dari database.<br><small style='font-weight:400;color:#6ee7b7'>Selesai pada: " . date('d/m/Y H:i:s') . "</small></div>";
        echo "<div style='margin-top:16px;text-align:center'>";
        echo "<a href='https://perguruanpembda.com/admin/counseling' class='btn btn-safe' style='display:inline-block'>✅ Buka Halaman Konseling</a>";
        echo "</div>";
        echo "</div></body></html>";
        exit;
    } catch (\Exception $e) {
        echo "<div class='result-error'>❌ Gagal hapus: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}

// ── TAMPILKAN PREVIEW ──
if ($totalAffected === 0) {
    echo "<div class='empty'>✅ Tidak ada data yang perlu dihapus (tidak ada catatan sebelum 10 Juli 2026).</div>";
} else {
    echo "<table>";
    echo "<thead><tr><th>#</th><th>Nama Siswa</th><th>Tipe</th><th>Judul</th><th>Kategori</th><th>Tgl. Kejadian</th></tr></thead>";
    echo "<tbody>";
    $no = 1;
    foreach ($records as $r) {
        $type    = $r->record_type === 'penghargaan' ? '<span class="badge prestasi">Prestasi</span>' : '<span class="badge pembinaan">Pembinaan</span>';
        $judul   = htmlspecialchars(mb_substr($r->title ?? '-', 0, 40)) . (mb_strlen($r->title ?? '') > 40 ? '...' : '');
        $name    = htmlspecialchars($r->student_name ?? '?');
        $cat     = htmlspecialchars(str_replace('_', ' ', $r->category ?? '-'));
        $date    = $r->incident_date ?? '-';
        echo "<tr><td>{$no}</td><td><strong>{$name}</strong></td><td>{$type}</td><td>{$judul}</td><td>{$cat}</td><td>{$date}</td></tr>";
        $no++;
    }
    echo "</tbody></table>";
}
?>

    <?php if ($totalAffected > 0): ?>
    <div class="warning-box">
        <p>⚠️ <strong>PERHATIAN:</strong> Aksi ini <strong>TIDAK BISA DIBATALKAN!</strong> Pastikan Bapak sudah memeriksa daftar di atas sebelum melanjutkan.</p>
    </div>
    <?php endif; ?>

    <div class="btn-group">
        <?php if ($totalAffected > 0 && !$isConfirm): ?>
        <a href="?secret=pembda99&dry_run=1" class="btn btn-preview">👁️ Preview (Dry Run)</a>
        <a href="?secret=pembda99&confirm=HAPUS" class="btn btn-danger"
           onclick="return confirm('YAKIN? <?= $totalAffected ?> catatan akan dihapus permanen!')">
            🗑️ HAPUS SEKARANG (<?= $totalAffected ?> data)
        </a>
        <?php elseif ($totalAffected === 0): ?>
        <a href="https://perguruanpembda.com/admin/counseling" class="btn btn-safe">✅ Kembali ke Aplikasi</a>
        <?php endif; ?>
        <a href="https://perguruanpembda.com/admin/counseling" class="btn btn-back">← Kembali</a>
    </div>

</div>
</body>
</html>
