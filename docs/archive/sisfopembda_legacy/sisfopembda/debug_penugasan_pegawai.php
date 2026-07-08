<?php
require_once 'config.php';
require_once 'override_functions.php';

$pegawai_id = isset($argv[1]) ? (int)$argv[1] : (isset($_GET['pegawai_id']) ? (int)$_GET['pegawai_id'] : 13);
$tahun = isset($argv[2]) ? $argv[2] : (isset($_GET['tahun']) ? $_GET['tahun'] : '2025/2026');

try {
    $stmt = $pdo->prepare("SELECT pen.*, p.nama, p.status_kepegawaian, p.gaji_pokok, p.status_perkawinan, p.jumlah_anak FROM penugasan pen JOIN pegawai p ON pen.pegawai_id = p.id WHERE pen.pegawai_id = ? AND pen.tahun_pelajaran = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$pegawai_id, $tahun]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) { echo "No penugasan found for pegawai_id=$pegawai_id tahun=$tahun\n"; exit; }

    echo "Pegawai: {$row['nama']} (ID: {$pegawai_id})\n";
    echo "Unit ID: {$row['unit_id']} Jam Mengajar: {$row['jam_mengajar']} Jam Wajib(stored): {$row['jam_wajib']} Jam Honor(stored): {$row['jam_honor']} Honor(stored): {$row['honor']}\n";

    $overrideRules = getOverrideRules($pdo, $pegawai_id);
    print_r(['overrideRules'=>$overrideRules]);

    $jamHonorStmt = $pdo->prepare("SELECT jam_wajib, honor_per_jam FROM jam_honor WHERE unit_id = ? AND status_kepegawaian = ?");
    $jamHonorStmt->execute([$row['unit_id'], $row['status_kepegawaian']]);
    $jh = $jamHonorStmt->fetch(PDO::FETCH_ASSOC);
    echo "jam_honor table: "; print_r($jh);

    $jam_wajib_used = $jh ? getCustomJamWajib($overrideRules, (int)$jh['jam_wajib']) : $row['jam_wajib'];

    $skip_honor = false;
    foreach ($overrideRules as $rule) {
        if (!empty($rule['is_active']) && ($rule['rule_type'] ?? '') === 'no_honor_calculation') { $skip_honor = true; break; }
    }

    if ($skip_honor) {
        $jam_honor_calc = 0; $honor_calc = 0;
    } else {
        $jam_honor_calc = max(0, $row['jam_mengajar'] - $jam_wajib_used);
        $honor_calc = $jam_honor_calc * ($jh['honor_per_jam'] ?? 0);
    }

    echo "Computed: jam_wajib_used={$jam_wajib_used} jam_honor_calc={$jam_honor_calc} honor_calc={$honor_calc}\n";

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

?>
