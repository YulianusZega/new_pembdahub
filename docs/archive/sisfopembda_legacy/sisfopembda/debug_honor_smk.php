<?php
require_once 'config.php';

echo "<h3>Debugging Honor per Jam SMK</h3>";

// Cek data jam_honor untuk SMK
$stmt = $pdo->query("
    SELECT 
        jh.unit_id,
        u.nama as unit_nama,
        jh.status_kepegawaian,
        jh.honor_per_jam
    FROM jam_honor jh
    JOIN unit u ON jh.unit_id = u.id
    WHERE u.nama LIKE '%SMK%'
    ORDER BY jh.status_kepegawaian
");

echo "<table border='1'>";
echo "<tr><th>Unit</th><th>Status</th><th>Honor per Jam</th></tr>";
while ($row = $stmt->fetch()) {
    echo "<tr>";
    echo "<td>" . $row['unit_nama'] . "</td>";
    echo "<td>" . $row['status_kepegawaian'] . "</td>";
    echo "<td>Rp " . number_format($row['honor_per_jam'], 0, ',', '.') . "</td>";
    echo "</tr>";
}
echo "</table>";

// Cek contoh data penugasan untuk SMK
echo "<h4>Sample Data Penugasan SMK:</h4>";
$stmt2 = $pdo->query("
    SELECT 
        p.nama,
        p.status_kepegawaian,
        u.nama as unit_nama,
        pen.jam_mengajar,
        pen.jam_wajib,
        pen.jam_honor,
        pen.honor,
        ROUND(pen.honor / NULLIF(pen.jam_honor, 0), 0) as calculated_honor_per_jam
    FROM penugasan pen
    JOIN pegawai p ON pen.pegawai_id = p.id
    JOIN unit u ON pen.unit_id = u.id
    WHERE u.nama LIKE '%SMK%' AND pen.jam_honor > 0
    LIMIT 5
");

echo "<table border='1'>";
echo "<tr><th>Nama</th><th>Status</th><th>Unit</th><th>Jam Mengajar</th><th>Jam Wajib</th><th>Jam Honor</th><th>Total Honor</th><th>Honor per Jam (Calculated)</th></tr>";
while ($row = $stmt2->fetch()) {
    echo "<tr>";
    echo "<td>" . $row['nama'] . "</td>";
    echo "<td>" . $row['status_kepegawaian'] . "</td>";
    echo "<td>" . $row['unit_nama'] . "</td>";
    echo "<td>" . $row['jam_mengajar'] . "</td>";
    echo "<td>" . $row['jam_wajib'] . "</td>";
    echo "<td>" . $row['jam_honor'] . "</td>";
    echo "<td>Rp " . number_format($row['honor'], 0, ',', '.') . "</td>";
    echo "<td>Rp " . number_format($row['calculated_honor_per_jam'], 0, ',', '.') . "</td>";
    echo "</tr>";
}
echo "</table>";
?>
