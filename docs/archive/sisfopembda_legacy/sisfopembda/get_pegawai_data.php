<?php
require_once 'config.php';
require_once 'auth.php';

header('Content-Type: application/json');

try {
    // Check authentication
    $auth = new Auth($pdo);
    $auth->requireLogin();
    
    if (!isset($_GET['pegawai_id'])) {
        throw new Exception('Pegawai ID tidak ditemukan');
    }
    
    $pegawai_id = (int)$_GET['pegawai_id'];
    
    // Get current pegawai data
    $stmt = $pdo->prepare("
        SELECT p.*, u.nama as unit_nama 
        FROM pegawai p 
        LEFT JOIN unit u ON p.unit_id = u.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$pegawai_id]);
    $pegawai = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pegawai) {
        throw new Exception('Data pegawai tidak ditemukan');
    }
    
    echo json_encode([
        'success' => true,
        'data' => [
            'id' => $pegawai['id'],
            'nama' => $pegawai['nama'],
            'nomor_induk' => $pegawai['nomor_induk'],
            'status_kepegawaian' => $pegawai['status_kepegawaian'],
            'kelompok_pekerjaan' => $pegawai['kelompok_pekerjaan'],
            'status_perkawinan' => $pegawai['status_perkawinan'],
            'jumlah_anak' => (int)$pegawai['jumlah_anak'],
            'gaji_pokok' => (int)$pegawai['gaji_pokok'],
            'unit_id' => $pegawai['unit_id'],
            'unit_nama' => $pegawai['unit_nama']
        ]
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
