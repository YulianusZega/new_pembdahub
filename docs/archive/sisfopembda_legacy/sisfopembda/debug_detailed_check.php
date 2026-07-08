<?php
require_once 'config.php';

echo "<h2>Debug: Detailed Pegawai Check</h2>";

// Check database directly
$sql = "SELECT id, nomor_induk, nama FROM pegawai WHERE unit_id = 1 ORDER BY id";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$db_pegawai = $stmt->fetchAll();

echo "<h3>Database pegawai (unit_id=1): " . count($db_pegawai) . " records</h3>";

$yarisman_found = false;
$dewi_found = false;

foreach ($db_pegawai as $p) {
    if (strpos($p['nama'], 'Yarisman') !== false) {
        echo "<p style='color: blue;'>DB: Yarisman - ID: {$p['id']}, Nomor: {$p['nomor_induk']}, Nama: {$p['nama']}</p>";
        $yarisman_found = true;
    }
    if (strpos($p['nama'], 'Dewi Juli') !== false) {
        echo "<p style='color: green;'>DB: Dewi Juli - ID: {$p['id']}, Nomor: {$p['nomor_induk']}, Nama: {$p['nama']}</p>";
        $dewi_found = true;
    }
}

if (!$yarisman_found) echo "<p style='color: red;'>Yarisman NOT FOUND in database!</p>";
if (!$dewi_found) echo "<p style='color: red;'>Dewi Juli NOT FOUND in database!</p>";

// Now simulate exact export logic
echo "<hr><h3>Simulate Export Logic</h3>";

$unit_id = 1;
$tahun_pelajaran = '2025/2026';
$status_kepegawaian = '';
$jabatan_ids_filter = [];

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

$stmt = $pdo->prepare($sql_pegawai);
$stmt->execute($params);
$pegawais = $stmt->fetchAll();

echo "<p>Export query result: " . count($pegawais) . " records</p>";

// Check for duplicates in export query
$export_ids = [];
$export_duplicates = [];

foreach ($pegawais as $i => $p) {
    if (in_array($p['pegawai_id'], $export_ids)) {
        $export_duplicates[] = "Index $i: {$p['pegawai_nama']} (ID: {$p['pegawai_id']})";
    } else {
        $export_ids[] = $p['pegawai_id'];
    }
    
    if (strpos($p['pegawai_nama'], 'Yarisman') !== false) {
        echo "<p style='color: blue;'>EXPORT: Yarisman at index $i - ID: {$p['pegawai_id']}, Nama: {$p['pegawai_nama']}</p>";
    }
    if (strpos($p['pegawai_nama'], 'Dewi Juli') !== false) {
        echo "<p style='color: green;'>EXPORT: Dewi Juli at index $i - ID: {$p['pegawai_id']}, Nama: {$p['pegawai_nama']}</p>";
    }
}

if (!empty($export_duplicates)) {
    echo "<h4 style='color: red;'>DUPLICATES in export query:</h4>";
    foreach ($export_duplicates as $dup) {
        echo "<p style='color: red;'>- $dup</p>";
    }
}

// Check missing from export
$db_ids = array_column($db_pegawai, 'id');
$missing_from_export = array_diff($db_ids, $export_ids);

if (!empty($missing_from_export)) {
    echo "<h4 style='color: red;'>Missing from export:</h4>";
    foreach ($missing_from_export as $missing_id) {
        foreach ($db_pegawai as $p) {
            if ($p['id'] == $missing_id) {
                echo "<p style='color: red;'>- ID: {$p['id']}, Nama: {$p['nama']}</p>";
                break;
            }
        }
    }
}

// Show all export results
echo "<h4>All Export Results:</h4>";
echo "<table border='1'>";
echo "<tr><th>Index</th><th>ID</th><th>Nomor</th><th>Nama</th><th>Status</th></tr>";
foreach ($pegawais as $i => $p) {
    $highlight = '';
    if (strpos($p['pegawai_nama'], 'Yarisman') !== false) $highlight = 'background: yellow;';
    if (strpos($p['pegawai_nama'], 'Dewi Juli') !== false) $highlight = 'background: lightgreen;';
    
    echo "<tr style='$highlight'>";
    echo "<td>$i</td>";
    echo "<td>{$p['pegawai_id']}</td>";
    echo "<td>{$p['nomor_induk']}</td>";
    echo "<td>{$p['pegawai_nama']}</td>";
    echo "<td>{$p['status_kepegawaian']}</td>";
    echo "</tr>";
}
echo "</table>";
?>
