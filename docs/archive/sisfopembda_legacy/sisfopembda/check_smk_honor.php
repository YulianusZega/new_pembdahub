<?php
require_once 'config.php';

try {
    $stmt = $pdo->query('
        SELECT 
            jh.id,
            u.nama as unit_nama,
            jh.status_kepegawaian,
            jh.jam_wajib,
            jh.honor_per_jam
        FROM jam_honor jh
        JOIN unit u ON jh.unit_id = u.id
        WHERE u.nama LIKE "%SMK%"
        ORDER BY u.nama, jh.status_kepegawaian
    ');
    
    echo "Data Honor/Jam untuk SMK:\n";
    echo "========================\n";
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("Unit: %s\n", $row['unit_nama']);
        echo sprintf("Status: %s\n", $row['status_kepegawaian']);
        echo sprintf("Jam Wajib: %d jam\n", $row['jam_wajib']);
        echo sprintf("Honor per Jam: Rp %s\n", number_format($row['honor_per_jam'], 0, ',', '.'));
        echo "------------------------\n";
    }
    
    // Cek juga unit yang ada
    echo "\nSemua Unit yang Ada:\n";
    echo "===================\n";
    $unitStmt = $pdo->query('SELECT id, nama FROM unit ORDER BY nama');
    while ($unit = $unitStmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("ID: %d - %s\n", $unit['id'], $unit['nama']);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
