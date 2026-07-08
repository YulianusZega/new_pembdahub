<?php
// Simple script to check jam_honor table
$host = 'localhost';
$dbname = 'sisfopembda';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "<h2>Data Tabel jam_honor</h2>";
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Unit</th><th>Status</th><th>Jam Wajib</th><th>Honor per Jam</th></tr>";
    
    $stmt = $pdo->query("
        SELECT jh.id, u.nama as unit_nama, jh.status_kepegawaian, jh.jam_wajib, jh.honor_per_jam 
        FROM jam_honor jh 
        JOIN unit u ON jh.unit_id = u.id 
        ORDER BY u.nama, jh.status_kepegawaian
    ");
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['unit_nama'] . "</td>";
        echo "<td>" . $row['status_kepegawaian'] . "</td>";
        echo "<td>" . $row['jam_wajib'] . "</td>";
        echo "<td>Rp " . number_format($row['honor_per_jam'], 0, ',', '.') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h3>Khusus SMK:</h3>";
    $stmt_smk = $pdo->query("
        SELECT jh.id, u.nama as unit_nama, jh.status_kepegawaian, jh.jam_wajib, jh.honor_per_jam 
        FROM jam_honor jh 
        JOIN unit u ON jh.unit_id = u.id 
        WHERE u.nama LIKE '%SMK%'
        ORDER BY jh.status_kepegawaian
    ");
    
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>Unit</th><th>Status</th><th>Jam Wajib</th><th>Honor per Jam</th></tr>";
    
    while ($row = $stmt_smk->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>";
        echo "<td>" . $row['unit_nama'] . "</td>";
        echo "<td>" . $row['status_kepegawaian'] . "</td>";
        echo "<td>" . $row['jam_wajib'] . "</td>";
        echo "<td><strong>Rp " . number_format($row['honor_per_jam'], 0, ',', '.') . "</strong></td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
