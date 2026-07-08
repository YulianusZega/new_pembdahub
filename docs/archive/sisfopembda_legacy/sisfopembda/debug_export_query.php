<?php
require_once 'config.php';

// Simulasi parameter yang sama dengan export
$unit_id = 1;
$tahun_pelajaran = '2025/2026';
$status_kepegawaian = '';
$jabatan_ids_filter = [];

echo "<h2>Debug Export Query (Fixed Approach)</h2>";

// Build query sama seperti di export_penugasan_excel.php yang baru
$where = ["1=1"];
$params = [];

if ($unit_id && $unit_id > 0) {
    $where[] = "p.unit_id = ?";
    $params[] = $unit_id;
}

if ($status_kepegawaian) {
    $where[] = "p.status_kepegawaian = ?";
    $params[] = $status_kepegawaian;
}

// Simple query to get all pegawai first
$sql_pegawai = "SELECT 
            p.id as pegawai_id,
            p.nomor_induk,
            p.nama as pegawai_nama,
            p.status_kepegawaian,
            p.status_perkawinan,
            p.kelompok_pekerjaan,
            p.jumlah_anak,
            p.gaji_pokok,
            u.nama as unit_nama,
            u.id as unit_id
        FROM pegawai p
        LEFT JOIN unit u ON p.unit_id = u.id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY
            u.nama,
            CASE p.status_kepegawaian
                WHEN 'PNS' THEN 1
                WHEN 'GTY' THEN 2
                WHEN 'Honorer' THEN 3
                WHEN 'PTY' THEN 4
                ELSE 5
            END,
            p.nama";

echo "<h3>Pegawai Query:</h3>";
echo "<pre>" . $sql_pegawai . "</pre>";
echo "<h3>Parameters:</h3>";
echo "<pre>" . print_r($params, true) . "</pre>";

try {
    $stmt = $pdo->prepare($sql_pegawai);
    $stmt->execute($params);
    $pegawais = $stmt->fetchAll();

    echo "<h3>Step 1 - All Pegawai Found: " . count($pegawais) . "</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nomor Induk</th><th>Nama</th><th>Status</th></tr>";
    
    foreach ($pegawais as $pegawai) {
        echo "<tr>";
        echo "<td>" . $pegawai['pegawai_id'] . "</td>";
        echo "<td>" . $pegawai['nomor_induk'] . "</td>";
        echo "<td>" . $pegawai['pegawai_nama'] . "</td>";
        echo "<td>" . $pegawai['status_kepegawaian'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Now get penugasan data for each pegawai
    foreach ($pegawais as &$pegawai) {
        // Get penugasan data
        $penugasan_sql = "SELECT 
                            id as penugasan_id,
                            jam_mengajar,
                            jam_wajib,
                            jam_honor,
                            honor,
                            tunjangan_keluarga,
                            tunjangan_anak,
                            tunjangan_beras,
                            tunjangan_jabatan,
                            total
                        FROM penugasan 
                        WHERE pegawai_id = ? AND unit_id = ? AND tahun_pelajaran = ?";
        
        $penugasan_stmt = $pdo->prepare($penugasan_sql);
        $penugasan_stmt->execute([$pegawai['pegawai_id'], $pegawai['unit_id'], $tahun_pelajaran]);
        $penugasan_data = $penugasan_stmt->fetch();
        
        if ($penugasan_data) {
            $pegawai['penugasan_id'] = $penugasan_data['penugasan_id'];
            $pegawai['jam_mengajar'] = $penugasan_data['jam_mengajar'];
            $pegawai['jam_wajib'] = $penugasan_data['jam_wajib'];
            $pegawai['jam_honor'] = $penugasan_data['jam_honor'];
            $pegawai['honor'] = $penugasan_data['honor'];
            $pegawai['tunjangan_keluarga'] = $penugasan_data['tunjangan_keluarga'];
            $pegawai['tunjangan_anak'] = $penugasan_data['tunjangan_anak'];
            $pegawai['tunjangan_beras'] = $penugasan_data['tunjangan_beras'];
            $pegawai['tunjangan_jabatan'] = $penugasan_data['tunjangan_jabatan'];
            $pegawai['total'] = $penugasan_data['total'];
        } else {
            $pegawai['penugasan_id'] = null;
            $pegawai['jam_mengajar'] = 0;
            $pegawai['jam_wajib'] = 0;
            $pegawai['jam_honor'] = 0;
            $pegawai['honor'] = 0;
            $pegawai['tunjangan_keluarga'] = 0;
            $pegawai['tunjangan_anak'] = 0;
            $pegawai['tunjangan_beras'] = 0;
            $pegawai['tunjangan_jabatan'] = 0;
            $pegawai['total'] = 0;
        }
    }

    // Apply jabatan filter if needed
    if (!empty($jabatan_ids_filter)) {
        $filtered_pegawais = [];
        foreach ($pegawais as $pegawai) {
            if ($pegawai['penugasan_id']) {
                // Check if this pegawai has any of the filtered jabatan
                $placeholders_filter = implode(',', array_fill(0, count($jabatan_ids_filter), '?'));
                $jabatan_check_sql = "SELECT COUNT(*) FROM penugasan_jabatan pj 
                                     JOIN jabatan j ON pj.jabatan_id = j.id 
                                     WHERE pj.penugasan_id = ? AND j.id IN ($placeholders_filter)";
                $jabatan_check_params = array_merge([$pegawai['penugasan_id']], $jabatan_ids_filter);
                $jabatan_check_stmt = $pdo->prepare($jabatan_check_sql);
                $jabatan_check_stmt->execute($jabatan_check_params);
                
                if ($jabatan_check_stmt->fetchColumn() > 0) {
                    $filtered_pegawais[] = $pegawai;
                }
            }
        }
        $pegawais = $filtered_pegawais;
        echo "<h3>Step 3 - After Jabatan filter: " . count($pegawais) . "</h3>";
    } else {
        echo "<h3>Step 3 - No jabatan filter applied</h3>";
    }

    echo "<h3>Final Result: " . count($pegawais) . " pegawai</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nomor Induk</th><th>Nama</th><th>Status</th><th>Penugasan ID</th></tr>";
    
    foreach ($pegawais as $pegawai) {
        echo "<tr>";
        echo "<td>" . $pegawai['pegawai_id'] . "</td>";
        echo "<td>" . $pegawai['nomor_induk'] . "</td>";
        echo "<td>" . $pegawai['pegawai_nama'] . "</td>";
        echo "<td>" . $pegawai['status_kepegawaian'] . "</td>";
        echo "<td>" . ($pegawai['penugasan_id'] ?: 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Bandingkan dengan data database
    echo "<h3>Bandingkan dengan semua pegawai unit_id=1:</h3>";
    $check_sql = "SELECT id, nomor_induk, nama FROM pegawai WHERE unit_id = ? ORDER BY nama";
    $check_stmt = $pdo->prepare($check_sql);
    $check_stmt->execute([$unit_id]);
    $all_pegawai = $check_stmt->fetchAll();
    
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Nomor Induk</th><th>Nama</th><th>Status di Export</th></tr>";
    
    $export_ids = array_column($pegawais, 'pegawai_id');
    
    foreach ($all_pegawai as $p) {
        $status = in_array($p['id'], $export_ids) ? "✓ ADA" : "✗ HILANG";
        $color = in_array($p['id'], $export_ids) ? "green" : "red";
        echo "<tr style='color: $color'>";
        echo "<td>" . $p['id'] . "</td>";
        echo "<td>" . $p['nomor_induk'] . "</td>";
        echo "<td>" . $p['nama'] . "</td>";
        echo "<td><strong>$status</strong></td>";
        echo "</tr>";
    }
    echo "</table>";

} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
