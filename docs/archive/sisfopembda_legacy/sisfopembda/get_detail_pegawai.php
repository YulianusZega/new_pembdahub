<?php
header('Content-Type: application/json');
require_once 'config.php';

// Ambil penugasan_id dari parameter
$penugasan_id = isset($_GET['penugasan_id']) ? (int)$_GET['penugasan_id'] : 0;

if ($penugasan_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID penugasan tidak valid']);
    exit;
}

try {
    // Query sederhana untuk mengambil data pegawai dan penugasan
    $stmt = $pdo->prepare("
        SELECT 
            p.nomor_induk,
            p.nama as nama_pegawai,
            p.status_kepegawaian,
            p.status_perkawinan,
            p.jumlah_anak,
            p.gaji_pokok,
            u.nama as unit_nama,
            u.id as unit_id,
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
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Data tidak ditemukan']);
        exit;
    }
    
    // Ambil honor per jam dari tabel jam_honor
    $honorStmt = $pdo->prepare("
        SELECT honor_per_jam 
        FROM jam_honor 
        WHERE unit_id = ? AND status_kepegawaian = ?
    ");
    $honorStmt->execute([$data['unit_id'], $data['status_kepegawaian']]);
    $honorData = $honorStmt->fetch(PDO::FETCH_ASSOC);
    $honor_per_jam = $honorData ? $honorData['honor_per_jam'] : 0;
    
    // Ambil persentase tunjangan dari tabel tunjangan_formula
    $formulaStmt = $pdo->query("SELECT nama, nilai FROM tunjangan_formula WHERE tipe = 'persen'");
    $formulas = [];
    while ($row = $formulaStmt->fetch()) {
        $formulas[$row['nama']] = $row['nilai'];
    }
    
    $persen_keluarga = isset($formulas['keluarga']) ? $formulas['keluarga'] : 0;
    $persen_anak = isset($formulas['anak']) ? $formulas['anak'] : 0;
    
    // Ambil data jabatan
    $jabatanStmt = $pdo->prepare("
        SELECT j.nama as jabatan_nama, j.tunjangan_jabatan
        FROM penugasan_jabatan pj
        JOIN jabatan j ON pj.jabatan_id = j.id
        WHERE pj.penugasan_id = ?
        ORDER BY j.tunjangan_jabatan DESC
    ");
    $jabatanStmt->execute([$penugasan_id]);
    $jabatan_list = $jabatanStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    $response = [
        'success' => true,
        'data' => [
            'nomor_induk' => $data['nomor_induk'],
            'nama_pegawai' => $data['nama_pegawai'],
            'status_kepegawaian' => $data['status_kepegawaian'],
            'status_perkawinan' => $data['status_perkawinan'],
            'jumlah_anak' => (int)$data['jumlah_anak'],
            'unit_nama' => $data['unit_nama'],
            'gaji_pokok' => (float)$data['gaji_pokok'],
            'jam_mengajar' => (int)$data['jam_mengajar'],
            'jam_wajib' => (int)$data['jam_wajib'],
            'jam_honor' => (int)$data['jam_honor'],
            'honor_per_jam' => (float)$honor_per_jam,
            'honor' => (float)$data['honor'],
            'tunjangan_keluarga' => (float)$data['tunjangan_keluarga'],
            'tunjangan_anak' => (float)$data['tunjangan_anak'],
            'tunjangan_beras' => (float)$data['tunjangan_beras'],
            'tunjangan_jabatan' => (float)$data['tunjangan_jabatan'],
            'total' => (float)$data['total'],
            'jabatan_list' => $jabatan_list,
            'persen_keluarga' => (float)$persen_keluarga,
            'persen_anak' => (float)$persen_anak
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Terjadi kesalahan: ' . $e->getMessage()
    ]);
}
?>
