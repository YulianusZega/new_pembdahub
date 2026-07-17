<?php
/**
 * CLEANUP SCRIPT - Reset Poin Reputasi & Hall of Fame Siswa
 * Hapus: reputations + reputation_logs untuk siswa
 *
 * Akses:   perguruanpembda.com/cleanup_reputation.php?secret=pembda99
 * Hapus:   perguruanpembda.com/cleanup_reputation.php?secret=pembda99&confirm=HAPUS
 */

$secret = $_GET['secret'] ?? '';
if ($secret !== 'pembda99') {
    http_response_code(403);
    die('<h2 style="color:red">403 Forbidden</h2>');
}

$isConfirm = isset($_GET['confirm']) && $_GET['confirm'] === 'HAPUS';

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
<title>Reset Poin Reputasi - PembdaHUB</title>
<style>
    * { box-sizing:border-box; margin:0; padding:0; font-family:'Segoe UI',sans-serif; }
    body { background:#0f172a; color:#e2e8f0; min-height:100vh; padding:40px 20px; }
    .card { background:#1e293b; border-radius:16px; padding:32px; max-width:760px; margin:0 auto; border:1px solid #334155; }
    .header { display:flex; align-items:center; gap:16px; margin-bottom:28px; padding-bottom:24px; border-bottom:1px solid #334155; }
    h1 { font-size:1.5rem; font-weight:800; color:#f1f5f9; }
    h1 span { color:#fbbf24; }
    .subtitle { color:#94a3b8; font-size:.85rem; margin-top:4px; }
    .info-box { background:#0f172a; border-radius:10px; padding:16px 20px; margin-bottom:20px; border-left:4px solid #6366f1; }
    .info-box p { font-size:.85rem; color:#94a3b8; line-height:1.8; }
    .info-box strong { color:#f1f5f9; }
    .warning-box { background:#1c1200; border-radius:10px; padding:16px 20px; margin-bottom:20px; border-left:4px solid #fbbf24; }
    .warning-box p { font-size:.85rem; color:#fde68a; line-height:1.8; }
    .stat-row { display:flex; gap:12px; margin-bottom:24px; flex-wrap:wrap; }
    .stat { flex:1; min-width:140px; background:#0f172a; border-radius:10px; padding:16px; text-align:center; border:1px solid #334155; }
    .stat .num { font-size:2rem; font-weight:900; }
    .stat .lbl { font-size:.72rem; font-weight:700; color:#64748b; text-transform:uppercase; letter-spacing:.06em; margin-top:4px; }
    .stat.amber .num { color:#fbbf24; }
    .stat.rose .num  { color:#f87171; }
    .stat.blue .num  { color:#60a5fa; }
    table { width:100%; border-collapse:collapse; font-size:.82rem; margin-bottom:24px; }
    th { background:#0f172a; padding:10px 14px; text-align:left; font-size:.7rem; text-transform:uppercase; letter-spacing:.08em; color:#64748b; border-bottom:1px solid #334155; }
    td { padding:10px 14px; border-bottom:1px solid #1e293b; color:#cbd5e1; }
    tr:hover td { background:#0f172a55; }
    .btn-group { display:flex; gap:12px; flex-wrap:wrap; }
    .btn { display:inline-block; padding:12px 24px; border-radius:10px; font-weight:700; font-size:.875rem; text-decoration:none; cursor:pointer; border:none; transition:all .2s; }
    .btn-danger { background:#b45309; color:#fff; }
    .btn-danger:hover { background:#d97706; }
    .btn-back  { background:#334155; color:#94a3b8; }
    .btn-back:hover { background:#475569; }
    .result-success { background:#052e16; border:1px solid #166534; border-radius:10px; padding:20px; margin-top:20px; color:#86efac; font-weight:600; text-align:center; font-size:1.1rem; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <div style="font-size:2.5rem">🏅</div>
        <div>
            <h1>Reset <span>Poin Reputasi</span> & Hall of Fame</h1>
            <p class="subtitle">Kosongkan poin seluruh siswa agar fair untuk penggunaan real · PembdaHUB</p>
        </div>
    </div>

<?php
try {
    // Ambil hanya reputasi milik Siswa (via tabel students → user_id)
    $studentUserIds = DB::table('students')
        ->whereNotNull('user_id')
        ->pluck('user_id')
        ->toArray();

    $totalRep  = DB::table('reputations')
        ->whereIn('user_id', $studentUserIds)
        ->count();

    $totalLogs = DB::table('reputation_logs')
        ->whereIn('user_id', $studentUserIds)
        ->count();

    $topStudents = DB::table('reputations')
        ->join('users', 'users.id', '=', 'reputations.user_id')
        ->join('students', 'students.user_id', '=', 'users.id')
        ->whereIn('reputations.user_id', $studentUserIds)
        ->orderByDesc('total_points')
        ->limit(10)
        ->select('students.full_name', 'reputations.total_points', 'reputations.level_name')
        ->get();

} catch (\Exception $e) {
    echo '<div style="color:#fca5a5;padding:20px">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '</div></body></html>';
    exit;
}

// EKSEKUSI HAPUS
if ($isConfirm) {
    try {
        DB::table('reputation_logs')->whereIn('user_id', $studentUserIds)->delete();
        DB::table('reputations')->whereIn('user_id', $studentUserIds)->delete();

        echo "<div class='result-success'>✅ Berhasil! Semua poin siswa telah direset ke nol.<br>
              <small style='font-weight:400;color:#6ee7b7'>Selesai: " . date('d/m/Y H:i:s') . "</small></div>";
        echo "<div style='margin-top:16px;text-align:center'>
              <a href='https://perguruanpembda.com/admin/counseling' class='btn btn-back' style='display:inline-block'>← Kembali ke Aplikasi</a>
              </div>";
        echo "</div></body></html>";
        exit;
    } catch (\Exception $e) {
        echo "<div style='color:#fca5a5;padding:20px'>❌ Gagal: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

    <div class="info-box">
        <p>
            🎯 <strong>Yang akan direset:</strong> Tabel <code>reputations</code> dan <code>reputation_logs</code> untuk seluruh akun siswa.<br>
            👨‍🏫 <strong>Aman:</strong> Poin reputasi guru/admin <strong>tidak tersentuh</strong>.<br>
            📋 <strong>Catatan konseling/prestasi:</strong> Tidak dihapus — hanya kolom poin yang dikosongkan.
        </p>
    </div>

    <div class="stat-row">
        <div class="stat amber">
            <div class="num"><?= $totalRep ?></div>
            <div class="lbl">Siswa Punya Poin</div>
        </div>
        <div class="stat rose">
            <div class="num"><?= $totalLogs ?></div>
            <div class="lbl">Log Poin</div>
        </div>
        <div class="stat blue">
            <div class="num"><?= count($studentUserIds) ?></div>
            <div class="lbl">Total Siswa</div>
        </div>
    </div>

    <?php if ($topStudents->count() > 0): ?>
    <p style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">
        Daftar Siswa yang Akan Direset
    </p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Nama Siswa</th>
                <th>Poin Saat Ini</th>
                <th>Level</th>
                <th>Setelah Reset</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($topStudents as $i => $s): ?>
            <tr>
                <td><?= $i+1 ?></td>
                <td><strong><?= htmlspecialchars($s->full_name) ?></strong></td>
                <td style="color:#fbbf24;font-weight:700"><?= number_format($s->total_points) ?> pts</td>
                <td><?= htmlspecialchars($s->level_name ?? '-') ?></td>
                <td style="color:#4ade80;font-weight:700">0 pts</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div style="text-align:center;padding:40px;color:#64748b">
        ✅ Tidak ada poin siswa yang perlu direset. Semua sudah bersih!
    </div>
    <?php endif; ?>

    <?php if ($totalRep > 0): ?>
    <div class="warning-box">
        <p>⚠️ <strong>PERHATIAN:</strong> Aksi ini <strong>TIDAK BISA DIBATALKAN!</strong> Semua poin dan log reputasi siswa akan dihapus permanen.</p>
    </div>
    <?php endif; ?>

    <div class="btn-group">
        <?php if ($totalRep > 0): ?>
        <a href="?secret=pembda99&confirm=HAPUS" class="btn btn-danger"
           onclick="return confirm('YAKIN? Semua poin <?= $totalRep ?> siswa akan direset ke NOL!')">
            🗑️ RESET SEMUA POIN SISWA
        </a>
        <?php endif; ?>
        <a href="https://perguruanpembda.com/admin/counseling" class="btn btn-back">← Kembali</a>
    </div>

</div>
</body>
</html>
