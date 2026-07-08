<?php
// Start output buffering to prevent any HTML contamination
ob_start();

// Set content type first and prevent caching
header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

require_once 'config.php';
require_once 'tunjangan_formula.php';
require_once 'override_functions.php';

// For testing purposes, bypass auth if running from command line
if (php_sapi_name() !== 'cli') {
    require_once 'auth.php';
    // Check authentication
    if (!$auth->isLoggedIn()) {
        // Clean any previous output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Not authenticated']);
        exit;
    }
}

if (!isset($_GET['penugasan_id']) || !is_numeric($_GET['penugasan_id'])) {
    // Clean any previous output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid penugasan ID',
        'debug' => [
            'received_id' => $_GET['penugasan_id'] ?? 'not set',
            'is_numeric' => isset($_GET['penugasan_id']) ? is_numeric($_GET['penugasan_id']) : false
        ]
    ]);
    exit;
}

$penugasan_id = (int)$_GET['penugasan_id'];

try {
    // Get penugasan detail with pegawai info
    $stmt = $pdo->prepare("
        SELECT 
            p.id as pegawai_id,
            p.nomor_induk,
            p.nama,
            p.status_kepegawaian,
            p.status_perkawinan,
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
        FROM penugasan pen
        JOIN pegawai p ON pen.pegawai_id = p.id
        JOIN unit u ON pen.unit_id = u.id
        WHERE pen.id = ?
    ");
    
    $stmt->execute([$penugasan_id]);
    $data = $stmt->fetch();
    
    if (!$data) {
        // Clean any previous output before sending JSON
        ob_end_clean();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false, 
            'message' => 'Penugasan not found',
            'debug' => [
                'penugasan_id' => $penugasan_id,
                'query_executed' => true
            ]
        ]);
        exit;
    }
    
    // Get jabatan details for this penugasan
    $jabatanStmt = $pdo->prepare("
        SELECT j.id, j.nama, j.tunjangan_jabatan
        FROM penugasan_jabatan pj
        JOIN jabatan j ON pj.jabatan_id = j.id
        WHERE pj.penugasan_id = ?
        ORDER BY j.tunjangan_jabatan DESC
    ");
    $jabatanStmt->execute([$penugasan_id]);
    $jabatan_details = $jabatanStmt->fetchAll();
    
    // Get honor per jam for calculation display
    $jamHonorStmt = $pdo->prepare("
        SELECT honor_per_jam 
        FROM jam_honor 
        WHERE unit_id = ? AND status_kepegawaian = ?
    ");
    $jamHonorStmt->execute([$data['unit_id'], $data['status_kepegawaian']]);
    $jamHonorData = $jamHonorStmt->fetch();
    $honor_per_jam = $jamHonorData ? $jamHonorData['honor_per_jam'] : 0;
    
    // Recalculate tunjangan to ensure accuracy (using the override-aware function)
    $tunjanganResult = calculateTunjanganWithOverride(
        $pdo, 
        $data['pegawai_id'], 
        $data['gaji_pokok'], 
        $data['status_perkawinan'], // Kirim langsung status dari database
        $data['jumlah_anak'],
        $data['status_kepegawaian']
    );
    
    // Get override info
    $overrideInfo = getActiveOverrideInfo($pdo, $data['pegawai_id']);
    
    // Prepare response data
    $response = [
        'success' => true,
        'data' => [
            'pegawai_id' => $data['pegawai_id'],
            'nomor_induk' => $data['nomor_induk'],
            'nama' => $data['nama'],
            'status_kepegawaian' => $data['status_kepegawaian'],
            'status_perkawinan' => $data['status_perkawinan'],
            'jumlah_anak' => $data['jumlah_anak'],
            'unit_nama' => $data['unit_nama'],
            'gaji_pokok' => (float)$data['gaji_pokok'],
            'jam_mengajar' => (int)$data['jam_mengajar'],
            'jam_wajib' => (int)$data['jam_wajib'],
            'jam_honor' => (int)$data['jam_honor'],
            'honor_per_jam' => (float)$honor_per_jam,
            'honor' => (float)$data['honor'],
            'tunjangan_keluarga' => (float)$tunjanganResult['tunjangan_keluarga'],
            'tunjangan_anak' => (float)$tunjanganResult['tunjangan_anak'],
            'tunjangan_beras' => (float)$tunjanganResult['tunjangan_beras'],
            'tunjangan_jabatan' => (float)$data['tunjangan_jabatan'],
            'total' => (float)$data['total'],
            'override_rules' => $overrideInfo,
            'has_override' => !empty($overrideInfo),
            'jabatan_details' => array_map(function($jabatan) {
                return [
                    'id' => (int)$jabatan['id'],
                    'nama' => $jabatan['nama'],
                    'tunjangan_jabatan' => (float)$jabatan['tunjangan_jabatan']
                ];
            }, $jabatan_details)
        ]
    ];
    
    // Clean any previous output and send JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode($response);
    
} catch (PDOException $e) {
    // Clean any previous output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage(),
        'debug' => [
            'error_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
} catch (Exception $e) {
    // Clean any previous output before sending JSON
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false, 
        'message' => 'Error: ' . $e->getMessage(),
        'debug' => [
            'error_code' => $e->getCode(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ]
    ]);
}
?>
