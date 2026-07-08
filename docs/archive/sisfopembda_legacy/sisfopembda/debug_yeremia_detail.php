<?php
require_once 'config.php';

echo "<h3>Debug Detail - Yeremia Harefa (ID 79)</h3>";

// 1. Cek semua penugasan untuk pegawai ID 79
echo "<h4>1. Semua Penugasan Yeremia Harefa:</h4>";
$stmt = $pdo->prepare("
    SELECT pen.*, u.nama as unit_nama 
    FROM penugasan pen
    JOIN unit u ON pen.unit_id = u.id
    WHERE pen.pegawai_id = 79
    ORDER BY pen.tahun_pelajaran DESC
");
$stmt->execute();
$penugasan_all = $stmt->fetchAll();

if ($penugasan_all) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Unit</th><th>Tahun</th><th>Jam Mengajar</th><th>Jam Wajib</th><th>Jam Honor</th><th>Honor</th></tr>";
    foreach ($penugasan_all as $p) {
        $highlight = ($p['tahun_pelajaran'] == '2025/2026') ? 'background-color: yellow;' : '';
        echo "<tr style='$highlight'>";
        echo "<td>" . $p['id'] . "</td>";
        echo "<td>" . $p['unit_nama'] . "</td>";
        echo "<td>" . $p['tahun_pelajaran'] . "</td>";
        echo "<td>" . $p['jam_mengajar'] . "</td>";
        echo "<td>" . $p['jam_wajib'] . "</td>";
        echo "<td>" . $p['jam_honor'] . "</td>";
        echo "<td>" . $p['honor'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "<p><em>Baris kuning = tahun 2025/2026</em></p>";
} else {
    echo "<p><strong>TIDAK ADA PENUGASAN SAMA SEKALI!</strong></p>";
}

// 2. Test query persis seperti di export
echo "<h4>2. Test Query Export (persis sama dengan export_penugasan_excel.php):</h4>";

$tahun_pelajaran = '2025/2026';
$where = ["p.unit_id IS NOT NULL"];
$params = [$tahun_pelajaran];

// Filter untuk SMK (seperti yang dipilih user)
$where[] = "u.nama = 'SMK Swasta Pembda Nias'";

$sql = "SELECT 
            p.id as pegawai_id,
            p.nomor_induk,
            p.nama as pegawai_nama,
            p.status_kepegawaian,
            p.status_perkawinan,
            p.kelompok_pekerjaan,
            p.jumlah_anak,
            p.gaji_pokok,
            u.nama as unit_nama,
            u.id as unit_id,
            pen.id as penugasan_id,
            pen.jam_mengajar,
            pen.jam_wajib,
            pen.jam_honor,
            pen.honor,
            pen.tunjangan_keluarga,
            pen.tunjangan_anak,
            pen.tunjangan_beras,
            pen.tunjangan_jabatan,
            pen.total
        FROM pegawai p
        LEFT JOIN unit u ON p.unit_id = u.id
        LEFT JOIN penugasan pen ON p.id = pen.pegawai_id AND pen.unit_id = u.id AND pen.tahun_pelajaran = ?
        LEFT JOIN penugasan_jabatan pj ON pen.id = pj.penugasan_id
        LEFT JOIN jabatan j ON pj.jabatan_id = j.id
        WHERE " . implode(' AND ', $where) . "
        GROUP BY p.id, pen.id
        ORDER BY p.nama";

echo "<p><strong>Query yang digunakan:</strong></p>";
echo "<pre style='background: #f5f5f5; padding: 10px;'>" . $sql . "</pre>";
echo "<p><strong>Parameters:</strong> " . implode(', ', $params) . "</p>";

$stmt_export = $pdo->prepare($sql);
$stmt_export->execute($params);
$results_export = $stmt_export->fetchAll();

echo "<p><strong>Total hasil query export:</strong> " . count($results_export) . "</p>";

// 3. Cari Yeremia di hasil
$found_yeremia = false;
foreach ($results_export as $i => $result) {
    if ($result['pegawai_id'] == 79) {
        $found_yeremia = true;
        echo "<p><strong>✓ YEREMIA DITEMUKAN di posisi " . ($i+1) . ":</strong></p>";
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($result as $key => $value) {
            echo "<tr><td>$key</td><td>" . ($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";
        break;
    }
}

if (!$found_yeremia) {
    echo "<p><strong>❌ YEREMIA TIDAK DITEMUKAN di hasil query export!</strong></p>";
    
    // Debug lebih lanjut
    echo "<h4>3. Debug Kondisi WHERE:</h4>";
    
    // Cek data pegawai 79 dengan unit
    $stmt_debug = $pdo->prepare("
        SELECT p.id, p.nama, p.unit_id, u.nama as unit_nama
        FROM pegawai p
        LEFT JOIN unit u ON p.unit_id = u.id
        WHERE p.id = 79
    ");
    $stmt_debug->execute();
    $pegawai_debug = $stmt_debug->fetch();
    
    echo "<p><strong>Data Pegawai 79:</strong></p>";
    echo "<table border='1'>";
    foreach ($pegawai_debug as $key => $value) {
        echo "<tr><td>$key</td><td>" . ($value ?? 'NULL') . "</td></tr>";
    }
    echo "</table>";
    
    // Cek kondisi WHERE satu per satu
    echo "<h4>4. Test Kondisi WHERE:</h4>";
    echo "<ul>";
    
    // Test p.unit_id IS NOT NULL
    if ($pegawai_debug['unit_id'] !== null) {
        echo "<li>✓ p.unit_id IS NOT NULL: TRUE (" . $pegawai_debug['unit_id'] . ")</li>";
    } else {
        echo "<li>❌ p.unit_id IS NOT NULL: FALSE (unit_id = NULL)</li>";
    }
    
    // Test unit name
    if ($pegawai_debug['unit_nama'] == 'SMK Swasta Pembda Nias') {
        echo "<li>✓ u.nama = 'SMK Swasta Pembda Nias': TRUE</li>";
    } else {
        echo "<li>❌ u.nama = 'SMK Swasta Pembda Nias': FALSE (actual: '" . $pegawai_debug['unit_nama'] . "')</li>";
    }
    
    echo "</ul>";
}

// 4. Tampilkan semua pegawai SMK untuk perbandingan
echo "<h4>5. Semua Pegawai SMK (untuk perbandingan):</h4>";
$stmt_all_smk = $pdo->prepare("
    SELECT p.id, p.nama, pen.id as penugasan_id, pen.tahun_pelajaran
    FROM pegawai p
    LEFT JOIN unit u ON p.unit_id = u.id
    LEFT JOIN penugasan pen ON p.id = pen.pegawai_id AND pen.unit_id = u.id AND pen.tahun_pelajaran = '2025/2026'
    WHERE u.nama = 'SMK Swasta Pembda Nias'
    ORDER BY p.nama
");
$stmt_all_smk->execute();
$all_smk = $stmt_all_smk->fetchAll();

echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>ID</th><th>Nama</th><th>Penugasan ID</th><th>Tahun Pelajaran</th><th>Status</th></tr>";
foreach ($all_smk as $smk) {
    $status = $smk['penugasan_id'] ? 'Ada Penugasan' : 'Tidak Ada Penugasan';
    $highlight = ($smk['id'] == 79) ? 'background-color: yellow;' : '';
    echo "<tr style='$highlight'>";
    echo "<td>" . $smk['id'] . "</td>";
    echo "<td>" . $smk['nama'] . "</td>";
    echo "<td>" . ($smk['penugasan_id'] ?? 'NULL') . "</td>";
    echo "<td>" . ($smk['tahun_pelajaran'] ?? 'NULL') . "</td>";
    echo "<td>" . $status . "</td>";
    echo "</tr>";
}
echo "</table>";
echo "<p><em>Baris kuning = Yeremia Harefa</em></p>";
?>
