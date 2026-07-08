<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'override_functions.php';

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

try {
    // Check authentication
    $auth = new Auth($pdo);
    $auth->requireLogin();
    
    if ($_GET['action'] !== 'calculate_preview') {
        throw new Exception('Invalid action');
    }
    
    $pegawai_id = (int)$_GET['pegawai_id'];
    $gaji_pokok = (int)$_GET['gaji_pokok'];
    $jumlah_anak = (int)$_GET['jumlah_anak'];
    $jam_mengajar = (int)$_GET['jam_mengajar'];
    $jabatan_ids_str = $_GET['jabatan_ids'] ?? '';
    
    // Get pegawai data
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
    
    // Function to check if pegawai has no gaji pokok
    function isNoGajiPokok($statusKepegawaian, $kelompokPekerjaan) {
        // PNS tidak ada gaji pokok (sudah ditentukan pemerintah)
        if ($statusKepegawaian === 'PNS') {
            return true;
        }
        // Kontrak tidak ada gaji pokok (sama seperti PNS)
        if ($statusKepegawaian === 'Kontrak') {
            return true;
        }
        // Honorer Pendidik tidak ada gaji pokok (hanya honor mengajar)
        if ($statusKepegawaian === 'Honorer' && $kelompokPekerjaan === 'Pendidik') {
            return true;
        }
        return false;
    }
    
    // Get override rules for this pegawai
    $overrideRules = getOverrideRules($pdo, $pegawai_id);
    
    // For PNS, Kontrak, and Honorer Pendidik, override gaji pokok to 0
    if (isNoGajiPokok($pegawai['status_kepegawaian'], $pegawai['kelompok_pekerjaan'])) {
        $gaji_pokok = 0;
    }
    
    // Get tunjangan formulas
    $stmt = $pdo->prepare("SELECT * FROM tunjangan_formula WHERE status_kepegawaian = ?");
    $stmt->execute([$pegawai['status_kepegawaian']]);
    $formula = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$formula) {
        throw new Exception('Formula tunjangan tidak ditemukan untuk status kepegawaian: ' . $pegawai['status_kepegawaian']);
    }
    
    // Calculate tunjangan jabatan
    $tunjangan_jabatan = 0;
    if (!empty($jabatan_ids_str)) {
        $jabatan_ids = explode(',', $jabatan_ids_str);
        $jabatan_ids = array_filter(array_map('intval', $jabatan_ids));
        
        if (!empty($jabatan_ids)) {
            $placeholders = str_repeat('?,', count($jabatan_ids) - 1) . '?';
            $stmt = $pdo->prepare("SELECT SUM(tunjangan_jabatan) as total FROM jabatan WHERE id IN ($placeholders)");
            $stmt->execute($jabatan_ids);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $tunjangan_jabatan = (int)($result['total'] ?? 0);
        }
    }
    
    // Calculate tunjangan using override rules
    $tunjanganData = calculateTunjanganWithOverride(
        $pdo, 
        $pegawai_id, 
        $gaji_pokok, 
        $pegawai['status_perkawinan'], // Kirim langsung status dari database
        $jumlah_anak,
        $pegawai['status_kepegawaian']
    );
    
    $tunjangan_keluarga = $tunjanganData['tunjangan_keluarga'];
    $tunjangan_anak = $tunjanganData['tunjangan_anak'];
    $tunjangan_beras = $tunjanganData['tunjangan_beras'];
    
    // Calculate honor mengajar if not overridden
    $honor = 0;
    $jam_wajib = 0;
    $jam_honor = 0;
    $honor_per_jam = 0;
    
    if (shouldCalculateHonor($overrideRules)) {
        // Get jam wajib and honor per jam for this unit and status
        $stmt = $pdo->prepare("
            SELECT jam_wajib, honor_per_jam 
            FROM jam_honor 
            WHERE unit_id = ? AND status_kepegawaian = ?
        ");
        $stmt->execute([$pegawai['unit_id'], $pegawai['status_kepegawaian']]);
        $jam_honor_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($jam_honor_data) {
            $jam_wajib = getCustomJamWajib($overrideRules, (int)$jam_honor_data['jam_wajib']);
            $honor_per_jam = (int)$jam_honor_data['honor_per_jam'];
            
            // Calculate honor only if jam mengajar exceeds jam wajib
            if ($jam_mengajar > $jam_wajib) {
                $jam_honor = $jam_mengajar - $jam_wajib;
                $honor = $jam_honor * $honor_per_jam;
            }
        }
    }
    
    // Calculate total
    $total = $gaji_pokok + $tunjangan_jabatan + $tunjangan_keluarga + $tunjangan_anak + $tunjangan_beras + $honor;
    
    // Get override info
    $overrideInfo = getActiveOverrideInfo($pdo, $pegawai_id);
    
    echo json_encode([
        'success' => true,
        'data' => [
            'gaji_pokok' => $gaji_pokok,
            'tunjangan_jabatan' => $tunjangan_jabatan,
            'tunjangan_keluarga' => $tunjangan_keluarga,
            'tunjangan_anak' => $tunjangan_anak,
            'tunjangan_beras' => $tunjangan_beras,
            'jam_mengajar' => $jam_mengajar,
            'jam_wajib' => $jam_wajib,
            'jam_honor' => $jam_honor,
            'honor_per_jam' => $honor_per_jam,
            'honor' => $honor,
            'total' => $total,
            'override_rules' => $overrideInfo,
            'has_override' => !empty($overrideInfo)
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
