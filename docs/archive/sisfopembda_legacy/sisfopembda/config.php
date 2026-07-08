<?php
// Database Configuration
$host = 'localhost';
$dbname = 'sisfopembda';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Utility functions
function formatRupiah($angka) {
    if ($angka === null || !is_numeric($angka)) return '';
    if ($angka == 0) return '<span style="float: left;">Rp</span><span style="float: right;">0</span>';
    // For preview (HTML), use span with proper alignment
    return '<span style="float: left;">Rp</span><span style="float: right;">' . number_format($angka, 0, ',', '.') . '</span>';
}

function formatTanggal($tanggal) {
    if (!$tanggal) return '-';
    return date('d/m/Y', strtotime($tanggal));
}

function getStatusBadge($status) {
    $badges = [
        'PNS' => 'badge bg-success',
        'GTY' => 'badge bg-primary',
        'PTY' => 'badge bg-info',
        'Kontrak' => 'badge bg-warning',
        'Honorer' => 'badge bg-secondary',
        'Percobaan' => 'badge bg-danger'
    ];
    
    $class = isset($badges[$status]) ? $badges[$status] : 'badge bg-secondary';
    return "<span class='$class'>$status</span>";
}
?>
