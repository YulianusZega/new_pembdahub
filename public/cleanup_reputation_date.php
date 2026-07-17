<?php
/**
 * CLEANUP SCRIPT - Hapus Log Reputasi & Sesuaikan Poin Sebelum Tanggal 10 Juli 2026
 * Berlaku untuk SELURUH USER (Siswa, Guru, Admin)
 *
 * Akses:   perguruanpembda.com/cleanup_reputation_date.php?secret=pembda99
 * Hapus:   perguruanpembda.com/cleanup_reputation_date.php?secret=pembda99&confirm=HAPUS
 */

$secret = $_GET['secret'] ?? '';
if ($secret !== 'pembda99') {
    http_response_code(403);
    die('<h2 style="color:red">403 Forbidden</h2>');
}

$isConfirm = isset($_GET['confirm']) && $_GET['confirm'] === 'HAPUS';
$cutoffDate = '2026-07-10 00:00:00';

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
<title>Cleanup Poin Reputasi Berdasarkan Tanggal - PembdaHUB</title>
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
    .empty { text-align: center; padding: 40px; color: #64748b; font-size: .9rem; }
</style>
</head>
<body>
<div class="card">
    <div class="header">
        <div style="font-size:2.5rem">⏳</div>
        <div>
            <h1>Cleanup <span>Poin Reputasi</span> (Semua User)</h1>
            <p class="subtitle">Hapus poin & log sebelum 10 Juli 2026 · PembdaHUB</p>
        </div>
    </div>

<?php
try {
    // 1. Cari semua log sebelum tanggal cutoff
    $logsToDelete = DB::table('reputation_logs')
        ->where('created_at', '<', $cutoffDate)
        ->get();

    $totalLogs = $logsToDelete->count();

    // 2. Hitung poin yang harus dikurangi per user
    $pointsToDeductPerUser = [];
    foreach ($logsToDelete as $log) {
        if (!isset($pointsToDeductPerUser[$log->user_id])) {
            $pointsToDeductPerUser[$log->user_id] = 0;
        }
        $pointsToDeductPerUser[$log->user_id] += $log->points;
    }

    $totalUsersAffected = count($pointsToDeductPerUser);
    
    // 3. Ambil data user yang terdampak untuk preview
    $previewUsers = [];
    if ($totalUsersAffected > 0) {
        $userIds = array_keys($pointsToDeductPerUser);
        $usersReputations = DB::table('reputations')
            ->join('users', 'users.id', '=', 'reputations.user_id')
            ->whereIn('reputations.user_id', $userIds)
            ->select('users.name', 'reputations.total_points', 'reputations.user_id', 'users.role')
            ->orderByDesc('reputations.total_points')
            ->limit(15) // Tampilkan max 15 untuk preview
            ->get();
            
        foreach ($usersReputations as $rep) {
            $deduct = $pointsToDeductPerUser[$rep->user_id];
            $newPoints = max(0, $rep->total_points - $deduct);
            $previewUsers[] = [
                'name' => $rep->name,
                'role' => $rep->role,
                'old_points' => $rep->total_points,
                'deduct' => $deduct,
                'new_points' => $newPoints
            ];
        }
    }

} catch (\Exception $e) {
    echo '<div style="color:#fca5a5;padding:20px">❌ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    echo '</div></body></html>';
    exit;
}

// EKSEKUSI HAPUS
if ($isConfirm && $totalLogs > 0) {
    try {
        DB::beginTransaction();

        // 1. Hapus logs
        DB::table('reputation_logs')
            ->where('created_at', '<', $cutoffDate)
            ->delete();

        // 2. Update poin masing-masing user yang terdampak
        foreach ($pointsToDeductPerUser as $userId => $deductAmount) {
            $rep = DB::table('reputations')->where('user_id', $userId)->first();
            if ($rep) {
                $newPoints = max(0, $rep->total_points - $deductAmount);
                
                // Logic update level
                $newLevel = 'Newbie';
                if ($newPoints >= 5000) {
                    $newLevel = 'Emerald Elite';
                } elseif ($newPoints >= 2000) {
                    $newLevel = 'Legendary Scholar';
                } elseif ($newPoints >= 1000) {
                    $newLevel = 'Ace Specialist';
                } elseif ($newPoints >= 500) {
                    $newLevel = 'Rising Star';
                }
                
                DB::table('reputations')
                    ->where('user_id', $userId)
                    ->update([
                        'total_points' => $newPoints,
                        'level_name' => $newLevel
                    ]);
            }
        }

        DB::commit();

        echo "<div class='result-success'>✅ Berhasil! {$totalLogs} log poin sebelum 10 Juli dihapus dan poin total {$totalUsersAffected} user telah disesuaikan.<br>
              <small style='font-weight:400;color:#6ee7b7'>Selesai: " . date('d/m/Y H:i:s') . "</small></div>";
        echo "<div style='margin-top:16px;text-align:center'>
              <a href='https://perguruanpembda.com/admin/counseling' class='btn btn-back' style='display:inline-block'>← Kembali ke Aplikasi</a>
              </div>";
        echo "</div></body></html>";
        exit;
    } catch (\Exception $e) {
        DB::rollBack();
        echo "<div style='color:#fca5a5;padding:20px'>❌ Gagal: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

    <div class="info-box">
        <p>
            🎯 <strong>Target:</strong> Poin yang didapat <strong>sebelum 10 Juli 2026</strong>.<br>
            👨‍👩‍👧‍👦 <strong>Cakupan:</strong> Berlaku untuk <strong>SEMUA USER</strong> (Siswa, Guru, Admin).<br>
            📉 <strong>Mekanisme:</strong> Log poin lama dihapus, lalu total poin user saat ini akan dikurangi sebanyak poin yang dihapus tersebut. Poin tidak akan kurang dari 0.
        </p>
    </div>

    <div class="stat-row">
        <div class="stat rose">
            <div class="num"><?= $totalLogs ?></div>
            <div class="lbl">Log Dihapus</div>
        </div>
        <div class="stat blue">
            <div class="num"><?= $totalUsersAffected ?></div>
            <div class="lbl">User Terdampak</div>
        </div>
    </div>

    <?php if ($totalLogs > 0): ?>
    <p style="font-size:.75rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin-bottom:10px">
        Preview User Terdampak (Sebagian)
    </p>
    <table>
        <thead>
            <tr>
                <th>Nama / Role</th>
                <th>Poin Asal</th>
                <th>Poin Dihapus</th>
                <th>Sisa Poin</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($previewUsers as $s): ?>
            <tr>
                <td>
                    <strong><?= htmlspecialchars($s['name']) ?></strong><br>
                    <small style="color:#94a3b8; text-transform:uppercase"><?= htmlspecialchars($s['role']) ?></small>
                </td>
                <td style="color:#94a3b8;"><?= number_format($s['old_points']) ?></td>
                <td style="color:#f87171;font-weight:700">-<?= number_format($s['deduct']) ?></td>
                <td style="color:#4ade80;font-weight:700"><?= number_format($s['new_points']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
    <div class="empty">
        ✅ Tidak ada poin yang didapat sebelum 10 Juli 2026. Semua poin saat ini didapat setelah tanggal tersebut.
    </div>
    <?php endif; ?>

    <?php if ($totalLogs > 0): ?>
    <div class="warning-box">
        <p>⚠️ <strong>PERHATIAN:</strong> Aksi ini <strong>TIDAK BISA DIBATALKAN!</strong> Log poin akan dihapus permanen dan total poin user akan dikurangi.</p>
    </div>
    <?php endif; ?>

    <div class="btn-group">
        <?php if ($totalLogs > 0): ?>
        <a href="?secret=pembda99&confirm=HAPUS" class="btn btn-danger"
           onclick="return confirm('YAKIN? <?= $totalLogs ?> log lama akan dihapus dan poin user disesuaikan!')">
            🗑️ PROSES SEKARANG (<?= $totalLogs ?> Log)
        </a>
        <?php endif; ?>
        <a href="https://perguruanpembda.com/admin/counseling" class="btn btn-back">← Kembali</a>
    </div>

</div>
</body>
</html>
