<?php
require_once 'config.php';
require_once 'override_functions.php';

// Usage (CLI or web): debug_compare_totals.php?tahun=2025/2026 OR php debug_compare_totals.php 2025/2026

$tahun = '2025/2026';
if (PHP_SAPI === 'cli') {
    if (!empty($argv[1])) $tahun = $argv[1];
} else {
    if (!empty($_GET['tahun'])) $tahun = $_GET['tahun'];
}

echo "Debug Compare Totals - Tahun Pelajaran: $tahun\n\n";

try {
    // Fetch penugasan rows for the year
    $sql = "SELECT pen.*, p.status_kepegawaian, p.gaji_pokok, p.status_perkawinan, p.jumlah_anak
            FROM penugasan pen
            JOIN pegawai p ON pen.pegawai_id = p.id
            WHERE pen.tahun_pelajaran = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$tahun]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo "Tidak ada data penugasan untuk tahun $tahun\n";
        exit;
    }

    $sum_db = 0.0;
    $sum_calc = 0.0;
    $diff_count = 0;
    $details = [];

    foreach ($rows as $r) {
        $db_total = (float)($r['total'] ?? 0);
        $sum_db += $db_total;

        // get jam_honor data for unit + status_kepegawaian
        $jamHonorStmt = $pdo->prepare("SELECT jam_wajib, honor_per_jam FROM jam_honor WHERE unit_id = ? AND status_kepegawaian = ?");
        $jamHonorStmt->execute([$r['unit_id'], $r['status_kepegawaian']]);
        $jh = $jamHonorStmt->fetch(PDO::FETCH_ASSOC);

        $jam_wajib = $r['jam_wajib'];
        $honor_per_jam = 0;
        $jam_honor_calc = (float)$r['jam_honor'];
        $honor_calc = (float)$r['honor'];

        if ($jh) {
            $honor_per_jam = (float)$jh['honor_per_jam'];
            // apply override rules (same helpers used by fix_honor_smk)
            $overrideRules = getOverrideRules($pdo, $r['pegawai_id']);
            $jam_wajib = getCustomJamWajib($overrideRules, (int)$jh['jam_wajib']);

            // check skip honor rule
            $skip_honor = false;
            foreach ($overrideRules as $rule) {
                if (!empty($rule['is_active']) && ($rule['rule_type'] ?? '') === 'no_honor_calculation') {
                    $skip_honor = true;
                    break;
                }
            }

            if (!$skip_honor) {
                $jam_honor_calc = max(0, (float)$r['jam_mengajar'] - (float)$jam_wajib);
                $honor_calc = $jam_honor_calc * $honor_per_jam;
            } else {
                $jam_honor_calc = 0;
                $honor_calc = 0;
            }
        }

        // tunjangan via existing helper
        $tunj = calculateTunjanganWithOverride(
            $pdo,
            $r['pegawai_id'],
            $r['gaji_pokok'],
            $r['status_perkawinan'],
            $r['jumlah_anak'],
            $r['status_kepegawaian']
        );

        // tunjangan_jabatan (sum of related jabatan tunjangan)
        $tjStmt = $pdo->prepare("SELECT COALESCE(SUM(j.tunjangan_jabatan),0) as tj FROM penugasan_jabatan pj JOIN jabatan j ON pj.jabatan_id=j.id WHERE pj.penugasan_id = ?");
        $tjStmt->execute([$r['id']]);
        $tjRow = $tjStmt->fetch(PDO::FETCH_ASSOC);
        $tunj_jabatan = (float)($tjRow['tj'] ?? 0);

        $calc_total = (float)$r['gaji_pokok'] + (float)$tunj['tunjangan_keluarga'] + (float)$tunj['tunjangan_anak'] + (float)$tunj['tunjangan_beras'] + $tunj_jabatan + $honor_calc;

        $sum_calc += $calc_total;

        $diff = $calc_total - $db_total;
        if (abs($diff) > 0.5) { // non-zero (tolerance for floats)
            $diff_count++;
            $details[] = [
                'penugasan_id' => $r['id'],
                'pegawai_id' => $r['pegawai_id'],
                'unit_id' => $r['unit_id'],
                'pegawai_nama' => $r['pegawai_id'],
                'db_total' => $db_total,
                'calc_total' => $calc_total,
                'diff' => $diff,
                'jam_mengajar' => $r['jam_mengajar'],
                'jam_wajib_used' => $jam_wajib,
                'jam_honor_calc' => $jam_honor_calc,
                'honor_per_jam' => $honor_per_jam,
            ];
        }
    }

    echo "Summary:\n";
    echo "  SUM(penugasan.total) in DB  = " . number_format($sum_db,0,',','.') . "\n";
    echo "  SUM(recalculated total)    = " . number_format($sum_calc,0,',','.') . "\n";
    echo "  DIFF (calc - db)           = " . number_format($sum_calc - $sum_db,0,',','.') . "\n";
    echo "  Rows with difference       = $diff_count\n\n";

    if ($diff_count > 0) {
        // sort details by absolute diff desc
        usort($details, function($a,$b){ return abs($b['diff']) <=> abs($a['diff']); });
        echo "Top differences (up to 20):\n";
        $limit = min(20, count($details));
        for ($i=0;$i<$limit;$i++) {
            $d = $details[$i];
            echo sprintf("%2d) penugasan_id=%s pegawai_id=%s unit_id=%s DB=%s  CALC=%s  DIFF=%s  (jam_meng=%s, jam_wajib=%s, jam_honor_calc=%s, hpj=%s)\n",
                $i+1,
                $d['penugasan_id'], $d['pegawai_id'], $d['unit_id'],
                number_format($d['db_total'],0,',','.'),
                number_format($d['calc_total'],0,',','.'),
                number_format($d['diff'],0,',','.'),
                $d['jam_mengajar'], $d['jam_wajib_used'], $d['jam_honor_calc'], number_format($d['honor_per_jam'],0,',','.')
            );
        }
    } else {
        echo "No differences found between DB totals and recalculated totals.\n";
    }

} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\nDone.\n";

?>
