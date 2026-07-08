<?php
require_once 'config.php';

echo "<h2>Debug Database Pegawai Unit 1</h2>";

// Check for duplicates in pegawai table
$sql = "SELECT id, nomor_induk, nama, COUNT(*) as count_occurrence 
        FROM pegawai 
        WHERE unit_id = 1 
        GROUP BY nama, nomor_induk 
        HAVING COUNT(*) > 1";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$duplicates = $stmt->fetchAll();

if ($duplicates) {
    echo "<h3 style='color: red'>DUPLIKASI DITEMUKAN di tabel pegawai:</h3>";
    foreach ($duplicates as $dup) {
        echo "<p style='color: red'>- " . $dup['nama'] . " (ID: " . $dup['id'] . ") - " . $dup['count_occurrence'] . " kali</p>";
    }
} else {
    echo "<h3 style='color: green'>✓ Tidak ada duplikasi nama di tabel pegawai</h3>";
}

// Check all pegawai in unit 1
$sql_all = "SELECT id, nomor_induk, nama, status_kepegawaian FROM pegawai WHERE unit_id = 1 ORDER BY id";
$stmt_all = $pdo->prepare($sql_all);
$stmt_all->execute();
$all_pegawai = $stmt_all->fetchAll();

echo "<h3>Semua pegawai di unit_id = 1 (Total: " . count($all_pegawai) . ")</h3>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nomor Induk</th><th>Nama</th><th>Status</th></tr>";

foreach ($all_pegawai as $p) {
    $highlight = '';
    if (strpos($p['nama'], 'Yarisman') !== false) {
        $highlight = 'style="background: yellow"';
    }
    if (strpos($p['nama'], 'Dewi Juli') !== false) {
        $highlight = 'style="background: lightgreen"';
    }
    
    echo "<tr $highlight>";
    echo "<td>" . $p['id'] . "</td>";
    echo "<td>" . $p['nomor_induk'] . "</td>";
    echo "<td>" . $p['nama'] . "</td>";
    echo "<td>" . $p['status_kepegawaian'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Check penugasan for these pegawai
echo "<h3>Check Penugasan untuk unit_id = 1, tahun 2025/2026</h3>";
$sql_penugasan = "SELECT pen.*, p.nama FROM penugasan pen 
                  JOIN pegawai p ON pen.pegawai_id = p.id 
                  WHERE pen.unit_id = 1 AND pen.tahun_pelajaran = '2025/2026' 
                  ORDER BY p.nama";
$stmt_penugasan = $pdo->prepare($sql_penugasan);
$stmt_penugasan->execute();
$penugasan_data = $stmt_penugasan->fetchAll();

echo "<p>Total penugasan records: " . count($penugasan_data) . "</p>";

// Check for multiple penugasan per pegawai
$penugasan_count = [];
foreach ($penugasan_data as $pen) {
    $pegawai_id = $pen['pegawai_id'];
    if (!isset($penugasan_count[$pegawai_id])) {
        $penugasan_count[$pegawai_id] = [];
    }
    $penugasan_count[$pegawai_id][] = $pen;
}

echo "<h4>Pegawai dengan multiple penugasan:</h4>";
foreach ($penugasan_count as $pegawai_id => $penugasans) {
    if (count($penugasans) > 1) {
        echo "<p style='color: red'>Pegawai ID $pegawai_id (" . $penugasans[0]['nama'] . ") memiliki " . count($penugasans) . " penugasan:</p>";
        foreach ($penugasans as $pen) {
            echo "<p style='margin-left: 20px'>- Penugasan ID: " . $pen['id'] . "</p>";
        }
    }
}
?>
