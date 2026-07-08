<?php
header('Content-Type: application/json');

// Include configuration
require_once 'config.php';

try {
    
    $response = array();
    
    // Get total pegawai
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM pegawai");
    $response['totalPegawai'] = $stmt->fetch()['total'];
    
    // Get total unit
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM unit");
    $response['totalUnit'] = $stmt->fetch()['total'];
    
    // Get total penugasan
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM penugasan");
    $response['totalPenugasan'] = $stmt->fetch()['total'];
    
    // Get total honor
    $stmt = $pdo->query("SELECT SUM(total) as total_honor FROM penugasan");
    $result = $stmt->fetch();
    $response['totalHonor'] = $result['total_honor'] ? $result['total_honor'] : 0;
    
    // Get recent penugasan (last 5)
    $stmt = $pdo->query("
        SELECT 
            pg.nama as nama_pegawai,
            u.nama as nama_unit,
            pn.tahun_pelajaran,
            pn.jam_mengajar,
            pn.total
        FROM penugasan pn
        JOIN pegawai pg ON pn.pegawai_id = pg.id
        JOIN unit u ON pn.unit_id = u.id
        ORDER BY pn.id DESC
        LIMIT 5
    ");
    $response['recentPenugasan'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get pegawai per unit for chart
    $stmt = $pdo->query("
        SELECT 
            u.nama as unit_nama,
            COUNT(p.id) as jumlah
        FROM unit u
        LEFT JOIN pegawai p ON u.id = p.unit_id
        GROUP BY u.id, u.nama
        ORDER BY u.nama
    ");
    $pegawaiPerUnit = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response['pegawaiPerUnit'] = array(
        'labels' => array_column($pegawaiPerUnit, 'unit_nama'),
        'data' => array_column($pegawaiPerUnit, 'jumlah')
    );
    
    // Get status kepegawaian for chart
    $stmt = $pdo->query("
        SELECT 
            status_kepegawaian,
            COUNT(*) as jumlah
        FROM pegawai
        GROUP BY status_kepegawaian
        ORDER BY jumlah DESC
    ");
    $statusKepegawaian = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response['statusKepegawaian'] = array(
        'labels' => array_column($statusKepegawaian, 'status_kepegawaian'),
        'data' => array_column($statusKepegawaian, 'jumlah')
    );
    
    echo json_encode($response);
    
} catch(PDOException $e) {
    // Return empty data if there's an error
    echo json_encode(array(
        'totalPegawai' => 0,
        'totalUnit' => 0,
        'totalPenugasan' => 0,
        'totalHonor' => 0,
        'recentPenugasan' => array(),
        'pegawaiPerUnit' => array('labels' => array(), 'data' => array()),
        'statusKepegawaian' => array('labels' => array(), 'data' => array())
    ));
}
?>
