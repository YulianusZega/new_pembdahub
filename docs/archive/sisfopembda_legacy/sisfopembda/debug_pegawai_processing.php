<?php
require_once 'config.php';

// Simulasi parameter yang sama dengan export
$unit_id = 1;
$tahun_pelajaran = '2025/2026';
$status_kepegawaian = '';
$jabatan_ids_filter = [];

echo "<h2>Debug Pegawai Processing</h2>";

// Copy exact code from export file
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

try {
    $stmt = $pdo->prepare($sql_pegawai);
    $stmt->execute($params);
    $pegawais = $stmt->fetchAll();

    echo "<h3>Step 1 - Pegawai dari database: " . count($pegawais) . "</h3>";
    
    // Check for duplicates in initial query
    $seen_ids = [];
    $duplicates = [];
    foreach ($pegawais as $p) {
        if (in_array($p['pegawai_id'], $seen_ids)) {
            $duplicates[] = $p['pegawai_nama'] . " (ID: " . $p['pegawai_id'] . ")";
        } else {
            $seen_ids[] = $p['pegawai_id'];
        }
    }
    
    if (!empty($duplicates)) {
        echo "<h3 style='color: red'>DUPLIKASI DITEMUKAN di query awal:</h3>";
        foreach ($duplicates as $dup) {
            echo "<p style='color: red'>- $dup</p>";
        }
    } else {
        echo "<h3 style='color: green'>✓ Tidak ada duplikasi di query awal</h3>";
    }

    echo "<table border='1'>";
    echo "<tr><th>No</th><th>ID</th><th>Nomor Induk</th><th>Nama</th><th>Status</th></tr>";
    
    $count = 1;
    foreach ($pegawais as $pegawai) {
        echo "<tr>";
        echo "<td>" . $count++ . "</td>";
        echo "<td>" . $pegawai['pegawai_id'] . "</td>";
        echo "<td>" . $pegawai['nomor_induk'] . "</td>";
        echo "<td>" . $pegawai['pegawai_nama'] . "</td>";
        echo "<td>" . $pegawai['status_kepegawaian'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // Now simulate the penugasan processing
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

    echo "<h3>Step 2 - Setelah add penugasan data: " . count($pegawais) . "</h3>";
    
    // Apply jabatan filter if needed (exactly like in export file)
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
        echo "<h3>Step 3 - After jabatan filter: " . count($pegawais) . "</h3>";
    } else {
        echo "<h3>Step 3 - No jabatan filter, final count: " . count($pegawais) . "</h3>";
    }

    // Check for missing pegawai
    echo "<h3>Checking specific pegawai:</h3>";
    $found_yarisman = false;
    $found_dewi = false;
    
    foreach ($pegawais as $p) {
        if (strpos($p['pegawai_nama'], 'Yarisman') !== false) {
            echo "<p style='color: green'>✓ Found Yarisman: " . $p['pegawai_nama'] . " (ID: " . $p['pegawai_id'] . ")</p>";
            $found_yarisman = true;
        }
        if (strpos($p['pegawai_nama'], 'Dewi Juli') !== false) {
            echo "<p style='color: green'>✓ Found Dewi Juli: " . $p['pegawai_nama'] . " (ID: " . $p['pegawai_id'] . ")</p>";
            $found_dewi = true;
        }
    }
    
    if (!$found_yarisman) {
        echo "<p style='color: red'>✗ Yarisman NOT FOUND</p>";
    }
    if (!$found_dewi) {
        echo "<p style='color: red'>✗ Dewi Juli NOT FOUND</p>";
    }

} catch (Exception $e) {
    echo "<h3>Error:</h3>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
?>
