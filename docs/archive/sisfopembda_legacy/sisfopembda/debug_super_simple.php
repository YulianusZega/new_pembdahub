<?php
require_once 'config.php';

echo "<h1>Super Simple Debug - Check 19 Pegawai</h1>";

// Simple query - get all pegawai for unit 2
$sql = "SELECT id, nama FROM pegawai WHERE unit_id = 2 ORDER BY id";
$result = mysqli_query($conn, $sql);

echo "<table border='1'>";
echo "<tr><th>Row</th><th>ID</th><th>Nama</th><th>Note</th></tr>";

$row = 1;
while ($pegawai = mysqli_fetch_assoc($result)) {
    $note = '';
    if ($pegawai['nama'] == 'Yarisman Waruwu') {
        $note = 'TARGET YARISMAN';
    } elseif ($pegawai['nama'] == 'Dewi Juli Sulastri Zega') {
        $note = 'TARGET DEWI JULI';
    }
    
    echo "<tr>";
    echo "<td>$row</td>";
    echo "<td>{$pegawai['id']}</td>";
    echo "<td>{$pegawai['nama']}</td>";
    echo "<td style='font-weight: bold; color: red;'>$note</td>";
    echo "</tr>";
    $row++;
}
echo "</table>";

echo "<p>Total: " . ($row - 1) . " pegawai</p>";

// Check if both target names exist
$yarisman_sql = "SELECT id, nama FROM pegawai WHERE nama = 'Yarisman Waruwu' AND unit_id = 2";
$yarisman_result = mysqli_query($conn, $yarisman_sql);
$yarisman_count = mysqli_num_rows($yarisman_result);

$dewi_sql = "SELECT id, nama FROM pegawai WHERE nama = 'Dewi Juli Sulastri Zega' AND unit_id = 2";
$dewi_result = mysqli_query($conn, $dewi_sql);
$dewi_count = mysqli_num_rows($dewi_result);

echo "<h2>Target Check:</h2>";
echo "<p>Yarisman Waruwu found: $yarisman_count times</p>";
echo "<p>Dewi Juli Sulastri Zega found: $dewi_count times</p>";

if ($yarisman_count == 1 && $dewi_count == 1) {
    echo "<p style='color: green; font-weight: bold;'>✓ BOTH TARGETS FOUND EXACTLY ONCE - DATABASE IS CORRECT!</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Problem with target names in database</p>";
}

mysqli_close($conn);
?>
