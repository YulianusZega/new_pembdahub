<?php
require_once 'config.php';
require_once 'override_functions.php';

// Get unit filter dari parameter
$unit_filter = isset($_GET['unit']) ? $_GET['unit'] : '';

echo "<h3>Perbaikan Honor per Jam - Recalculate Data Penugasan</h3>";

if ($unit_filter) {
    echo "<p><strong>Filter Unit:</strong> $unit_filter</p>";
} else {
    echo "<p><strong>Mode:</strong> Semua Unit</p>";
}

try {
    // 1. Tampilkan data jam_honor saat ini
    echo "<h4>1. Data jam_honor saat ini:</h4>";
    
    $where_clause = "";
    $params = [];
    if ($unit_filter) {
        $where_clause = "WHERE u.nama = ?";
        $params[] = $unit_filter;
    }
    
    $stmt = $pdo->prepare("
        SELECT jh.id, u.nama as unit_nama, jh.status_kepegawaian, jh.jam_wajib, jh.honor_per_jam
        FROM jam_honor jh
        JOIN unit u ON jh.unit_id = u.id
        $where_clause
        ORDER BY u.nama, jh.status_kepegawaian
    ");
    $stmt->execute($params);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Unit</th><th>Status</th><th>Jam Wajib</th><th>Honor per Jam</th></tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['unit_nama'] . "</td>";
        echo "<td>" . $row['status_kepegawaian'] . "</td>";
        echo "<td>" . $row['jam_wajib'] . "</td>";
        echo "<td>Rp " . number_format($row['honor_per_jam'], 0, ',', '.') . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // 2. Recalculate semua penugasan sesuai filter
    echo "<h4>2. Recalculate data penugasan:</h4>";
    
    $where_penugasan = "";
    $params_penugasan = [];
    if ($unit_filter) {
        $where_penugasan = "AND u.nama = ?";
        $params_penugasan[] = $unit_filter;
    }
    
    // Ambil semua penugasan sesuai filter
    $stmt_penugasan = $pdo->prepare("
        SELECT pen.id as penugasan_id, pen.pegawai_id, p.status_kepegawaian, p.gaji_pokok,
               p.status_perkawinan, p.jumlah_anak, u.nama as unit_nama,
               pen.jam_mengajar, pen.jam_wajib, pen.jam_honor, pen.honor, 
               pen.tunjangan_keluarga, pen.tunjangan_anak, pen.tunjangan_beras,
               pen.tunjangan_jabatan, pen.total, u.id as unit_id
        FROM penugasan pen
        JOIN pegawai p ON pen.pegawai_id = p.id
        JOIN unit u ON pen.unit_id = u.id
        WHERE pen.tahun_pelajaran = '2025/2026' $where_penugasan
    ");
    $stmt_penugasan->execute($params_penugasan);
    
    $updated_count = 0;
    $updated_details = [];
    
    while ($penugasan = $stmt_penugasan->fetch()) {
        // Ambil honor_per_jam dari tabel jam_honor
        $stmt_honor = $pdo->prepare("
            SELECT jam_wajib, honor_per_jam FROM jam_honor 
            WHERE unit_id = ? AND status_kepegawaian = ?
        ");
        $stmt_honor->execute([$penugasan['unit_id'], $penugasan['status_kepegawaian']]);
        $honor_data = $stmt_honor->fetch();
        
        if ($honor_data) {
            // Ambil override rules untuk pegawai ini
            $overrideRules = getOverrideRules($pdo, $penugasan['pegawai_id']);
            
            // Hitung jam wajib (dengan override jika ada)
            $default_jam_wajib = $honor_data['jam_wajib'];
            $jam_wajib = getCustomJamWajib($overrideRules, $default_jam_wajib);
            
            // Hitung honor (jika tidak di-override)
            $honor = 0;
            $jam_honor = 0;
            $honor_per_jam = $honor_data['honor_per_jam'];
            
            // Cek apakah perhitungan honor ditiadakan
            $skip_honor = false;
            foreach ($overrideRules as $rule) {
                if ($rule['is_active'] && $rule['rule_type'] === 'no_honor_calculation') {
                    $skip_honor = true;
                    break;
                }
            }
            
            if (!$skip_honor) {
                $jam_honor = max(0, $penugasan['jam_mengajar'] - $jam_wajib);
                $honor = $jam_honor * $honor_per_jam;
            }
            
            // Hitung tunjangan dengan override
            $tunjanganResult = calculateTunjanganWithOverride(
                $pdo, 
                $penugasan['pegawai_id'], 
                $penugasan['gaji_pokok'], 
                $penugasan['status_perkawinan'], 
                $penugasan['jumlah_anak'],
                $penugasan['status_kepegawaian']
            );
            
            // Hitung tunjangan jabatan
            $tunjangan_jabatan = 0;
            $stmt_jabatan = $pdo->prepare("
                SELECT SUM(j.tunjangan_jabatan) as total_tunjangan_jabatan
                FROM penugasan_jabatan pj
                JOIN jabatan j ON pj.jabatan_id = j.id
                WHERE pj.penugasan_id = ?
            ");
            $stmt_jabatan->execute([$penugasan['penugasan_id']]);
            $jabatan_data = $stmt_jabatan->fetch();
            if ($jabatan_data) {
                $tunjangan_jabatan = $jabatan_data['total_tunjangan_jabatan'] ?: 0;
            }
            
            // Hitung total
            $total = $penugasan['gaji_pokok'] + $tunjanganResult['tunjangan_keluarga'] + 
                    $tunjanganResult['tunjangan_anak'] + $tunjanganResult['tunjangan_beras'] + 
                    $tunjangan_jabatan + $honor;
            
            // Update penugasan dengan nilai yang baru
            $stmt_update_penugasan = $pdo->prepare("
                UPDATE penugasan 
                SET jam_wajib = ?, jam_honor = ?, honor = ?, 
                    tunjangan_keluarga = ?, tunjangan_anak = ?, tunjangan_beras = ?,
                    tunjangan_jabatan = ?, total = ?
                WHERE id = ?
            ");
            $stmt_update_penugasan->execute([
                $jam_wajib, $jam_honor, $honor,
                $tunjanganResult['tunjangan_keluarga'],
                $tunjanganResult['tunjangan_anak'], 
                $tunjanganResult['tunjangan_beras'],
                $tunjangan_jabatan, $total,
                $penugasan['penugasan_id']
            ]);
            
            $updated_count++;
            $updated_details[] = [
                'unit' => $penugasan['unit_nama'],
                'status' => $penugasan['status_kepegawaian'],
                'jam_honor_old' => $penugasan['jam_honor'],
                'jam_honor_new' => $jam_honor,
                'honor_old' => $penugasan['honor'],
                'honor_new' => $honor,
                'honor_per_jam' => $honor_per_jam
            ];
        }
    }
    
    echo "✓ Berhasil recalculate " . $updated_count . " data penugasan<br>";

    // 3. Tampilkan sample perubahan
    if (!empty($updated_details)) {
        echo "<h4>3. Sample perubahan yang dilakukan:</h4>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Unit</th><th>Status</th><th>Jam Honor</th><th>Honor (Lama)</th><th>Honor (Baru)</th><th>Honor/Jam</th></tr>";
        
        $sample_count = 0;
        foreach ($updated_details as $detail) {
            if ($sample_count >= 10) break; // Tampilkan max 10 sample
            
            $color = ($detail['honor_old'] != $detail['honor_new']) ? 'background-color: #fff3cd;' : '';
            
            echo "<tr style='$color'>";
            echo "<td>" . $detail['unit'] . "</td>";
            echo "<td>" . $detail['status'] . "</td>";
            echo "<td>" . $detail['jam_honor_new'] . "</td>";
            echo "<td>Rp " . number_format($detail['honor_old'], 0, ',', '.') . "</td>";
            echo "<td><strong>Rp " . number_format($detail['honor_new'], 0, ',', '.') . "</strong></td>";
            echo "<td>Rp " . number_format($detail['honor_per_jam'], 0, ',', '.') . "</td>";
            echo "</tr>";
            $sample_count++;
        }
        echo "</table>";
        echo "<p><em>Baris dengan background kuning menunjukkan data yang berubah.</em></p>";
    }

    // 4. Tampilkan data setelah recalculate
    echo "<h4>4. Data penugasan setelah recalculate:</h4>";
    $stmt_final = $pdo->prepare("
        SELECT p.nama, p.status_kepegawaian, u.nama as unit_nama,
               pen.jam_mengajar, pen.jam_wajib, pen.jam_honor, pen.honor,
               CASE WHEN pen.jam_honor > 0 THEN ROUND(pen.honor / pen.jam_honor, 0) ELSE 0 END as honor_per_jam_calculated
        FROM penugasan pen
        JOIN pegawai p ON pen.pegawai_id = p.id
        JOIN unit u ON pen.unit_id = u.id
        WHERE pen.tahun_pelajaran = '2025/2026' $where_penugasan
        ORDER BY u.nama, p.status_kepegawaian, p.nama
        LIMIT 10
    ");
    $stmt_final->execute($params_penugasan);
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Nama</th><th>Status</th><th>Unit</th><th>Jam Mengajar</th><th>Jam Wajib</th><th>Jam Honor</th><th>Total Honor</th><th>Honor/Jam</th></tr>";
    while ($row = $stmt_final->fetch()) {
        echo "<tr>";
        echo "<td>" . $row['nama'] . "</td>";
        echo "<td>" . $row['status_kepegawaian'] . "</td>";
        echo "<td>" . $row['unit_nama'] . "</td>";
        echo "<td>" . $row['jam_mengajar'] . "</td>";
        echo "<td>" . $row['jam_wajib'] . "</td>";
        echo "<td>" . $row['jam_honor'] . "</td>";
        echo "<td>Rp " . number_format($row['honor'], 0, ',', '.') . "</td>";
        echo "<td><strong>Rp " . number_format($row['honor_per_jam_calculated'], 0, ',', '.') . "</strong></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<div style='background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h4 style='color: #155724; margin: 0;'>✅ RECALCULATE SELESAI!</h4>";
    echo "<p style='margin: 10px 0 0 0; color: #155724;'>Semua data penugasan telah di-recalculate menggunakan honor_per_jam yang benar dari tabel jam_honor.</p>";
    echo "</div>";

    // 5. Link untuk unit lainnya
    echo "<h4>5. Recalculate Unit Lainnya:</h4>";
    $stmt_units = $pdo->query("SELECT DISTINCT nama FROM unit ORDER BY nama");
    echo "<p>";
    while ($unit = $stmt_units->fetch()) {
        $unit_name = $unit['nama'];
        $current_class = ($unit_filter == $unit_name) ? "style='font-weight: bold; color: #007bff;'" : "";
        echo "<a href='?unit=" . urlencode($unit_name) . "' $current_class>$unit_name</a> | ";
    }
    echo "<a href='?' " . (empty($unit_filter) ? "style='font-weight: bold; color: #007bff;'" : "") . ">Semua Unit</a>";
    echo "</p>";

} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h4 style='color: #721c24; margin: 0;'>❌ ERROR!</h4>";
    echo "<p style='margin: 10px 0 0 0; color: #721c24;'>Error: " . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
