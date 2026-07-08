<?php
// Prevent caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

require_once 'config.php';
require_once 'auth.php';
require_once 'override_functions.php';

// Function to check if pegawai has no gaji pokok
function isNoGajiPokok($statusKepegawaian, $kelompokPekerjaan) {
    // Pegawai yang gaji pokoknya tidak diinput melalui form
    if ($statusKepegawaian === 'PNS') {
        return true;
    }
    if ($statusKepegawaian === 'Kontrak') {
        return true;
    }
    // Honorer Pendidik tidak memiliki gaji pokok, hanya honor
    if ($statusKepegawaian === 'Honorer' && $kelompokPekerjaan === 'Pendidik') {
        return true;
    }
    return false;
}

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('penugasan');

// Handle logout
if (isset($_GET['logout'])) {
    $auth->logout();
}

// Handle form submission for penugasan
if ($_POST && isset($_POST['action']) && $_POST['action'] == 'save_penugasan') {
    $pegawai_id = isset($_POST['pegawai_id']) ? (int)$_POST['pegawai_id'] : 0;
    $unit_id = isset($_POST['unit_id']) ? (int)$_POST['unit_id'] : 0;
    $tahun_pelajaran = isset($_POST['tahun_pelajaran']) ? $_POST['tahun_pelajaran'] : '';
    $jam_mengajar = isset($_POST['jam_mengajar']) ? (int)$_POST['jam_mengajar'] : 0;
    $jabatan_ids = isset($_POST['jabatan_ids']) ? $_POST['jabatan_ids'] : [];
    $gaji_pokok_input = isset($_POST['gaji_pokok']) ? $_POST['gaji_pokok'] : '';
    $jumlah_anak = isset($_POST['jumlah_anak']) ? (int)$_POST['jumlah_anak'] : 0;
    
    // Validate required fields
    if (empty($pegawai_id) || empty($unit_id) || empty($tahun_pelajaran)) {
        throw new Exception("Data tidak lengkap. Pastikan pegawai, unit, dan tahun pelajaran sudah dipilih.");
    }
    
    // Get pegawai data to check status kepegawaian
    $pegawaiStmt = $pdo->prepare("SELECT status_kepegawaian, kelompok_pekerjaan, status_perkawinan, jumlah_anak FROM pegawai WHERE id = ?");
    $pegawaiStmt->execute([$pegawai_id]);
    $pegawaiData = $pegawaiStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$pegawaiData) {
        throw new Exception("Data pegawai tidak ditemukan");
    }
    
    $skipValidation = isNoGajiPokok($pegawaiData['status_kepegawaian'], $pegawaiData['kelompok_pekerjaan']);
    
    // Perbaikan: Lakukan pembersihan dan validasi yang lebih ketat
    $gaji_pokok = 0;
    if (!$skipValidation) {
        // Hapus semua karakter selain digit dari input gaji pokok
        $gaji_pokok_bersih = preg_replace('/[^0-9]/', '', $gaji_pokok_input);
        $gaji_pokok = (int)$gaji_pokok_bersih; // Pastikan ini adalah integer
        
        if ($gaji_pokok > 0 && $gaji_pokok < 100000) {
            throw new Exception("Gaji pokok minimal Rp 100.000");
        }
    }
    
    try {
        $pdo->beginTransaction();
        
        // Update pegawai data first (gaji pokok and jumlah anak)
        // Perbaikan: Gunakan query terpisah untuk mengupdate gaji pokok
        // Ini memastikan nilai gaji pokok yang disimpan adalah yang bersih
        $updatePegawaiStmt = $pdo->prepare("UPDATE pegawai SET gaji_pokok = ?, jumlah_anak = ? WHERE id = ?");
        $updatePegawaiStmt->execute([$gaji_pokok, $jumlah_anak, $pegawai_id]);
        
        // Get pegawai data for calculations (with updated values)
        $pegawaiStmt = $pdo->prepare("SELECT p.*, u.nama as unit_nama FROM pegawai p LEFT JOIN unit u ON p.unit_id = u.id WHERE p.id = ?");
        $pegawaiStmt->execute([$pegawai_id]);
        $pegawai = $pegawaiStmt->fetch();
        
        // Get jam honor rules
        $jamHonorStmt = $pdo->prepare("SELECT jam_wajib, honor_per_jam FROM jam_honor WHERE unit_id = ? AND status_kepegawaian = ?");
        $jamHonorStmt->execute([$unit_id, $pegawai['status_kepegawaian']]);
        $jamHonor = $jamHonorStmt->fetch();
        
        // Calculate values
        $default_jam_wajib = $jamHonor ? $jamHonor['jam_wajib'] : 0;
        
        // Check for custom jam wajib override
        $overrideRules = getOverrideRules($pdo, $pegawai['id']);
        $jam_wajib = getCustomJamWajib($overrideRules, $default_jam_wajib);
        
        $jam_honor = max(0, $jam_mengajar - $jam_wajib);
        $honor_per_jam = $jamHonor ? $jamHonor['honor_per_jam'] : 0;
        $honor = $jam_honor * $honor_per_jam;
        
        // Calculate tunjangan using helper function with override support
        $tunjanganResult = calculateTunjanganWithOverride(
            $pdo, 
            $pegawai['id'], 
            $pegawai['gaji_pokok'], 
            $pegawai['status_perkawinan'], 
            $pegawai['jumlah_anak'],
            $pegawai['status_kepegawaian']
        );
        $tunjangan_keluarga = $tunjanganResult['tunjangan_keluarga'];
        $tunjangan_anak = $tunjanganResult['tunjangan_anak'];
        $tunjangan_beras = $tunjanganResult['tunjangan_beras'];
        
        // Calculate jabatan tunjangan
        $tunjangan_jabatan = 0;
        if (!empty($jabatan_ids)) {
            $placeholders = str_repeat('?,', count($jabatan_ids) - 1) . '?';
            $jabatanStmt = $pdo->prepare("SELECT SUM(tunjangan_jabatan) as total FROM jabatan WHERE id IN ($placeholders)");
            $jabatanStmt->execute($jabatan_ids);
            $result = $jabatanStmt->fetch();
            $tunjangan_jabatan = $result['total'] ?: 0;
        }
        
        // THP (Take Home Pay) = Gaji Pokok + Total Tunjangan Jabatan + Total Tunjangan Keluarga + Honor
        $total_tunjangan_keluarga = $tunjangan_keluarga + $tunjangan_anak + $tunjangan_beras;
        $total = $pegawai['gaji_pokok'] + $tunjangan_jabatan + $total_tunjangan_keluarga + $honor;
        
        // Check if penugasan exists
        $checkStmt = $pdo->prepare("SELECT id FROM penugasan WHERE pegawai_id = ? AND unit_id = ? AND tahun_pelajaran = ?");
        $checkStmt->execute([$pegawai_id, $unit_id, $tahun_pelajaran]);
        $existing = $checkStmt->fetch();
        
        if ($existing) {
            // Update existing penugasan
            $updateStmt = $pdo->prepare("UPDATE penugasan SET jam_mengajar = ?, jam_wajib = ?, jam_honor = ?, honor = ?, tunjangan_keluarga = ?, tunjangan_anak = ?, tunjangan_beras = ?, tunjangan_jabatan = ?, total = ? WHERE id = ?");
            $updateStmt->execute([$jam_mengajar, $jam_wajib, $jam_honor, $honor, $tunjangan_keluarga, $tunjangan_anak, $tunjangan_beras, $tunjangan_jabatan, $total, $existing['id']]);
            $penugasan_id = $existing['id'];
            
            // Delete existing jabatan assignments
            $pdo->prepare("DELETE FROM penugasan_jabatan WHERE penugasan_id = ?")->execute([$penugasan_id]);
        } else {
            // Insert new penugasan
            $insertStmt = $pdo->prepare("INSERT INTO penugasan (pegawai_id, unit_id, tahun_pelajaran, jam_mengajar, jam_wajib, jam_honor, honor, tunjangan_keluarga, tunjangan_anak, tunjangan_beras, tunjangan_jabatan, total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertStmt->execute([$pegawai_id, $unit_id, $tahun_pelajaran, $jam_mengajar, $jam_wajib, $jam_honor, $honor, $tunjangan_keluarga, $tunjangan_anak, $tunjangan_beras, $tunjangan_jabatan, $total]);
            $penugasan_id = $pdo->lastInsertId();
        }
        
        // Insert jabatan assignments
        if (!empty($jabatan_ids)) {
            $jabatanInsertStmt = $pdo->prepare("INSERT INTO penugasan_jabatan (penugasan_id, jabatan_id) VALUES (?, ?)");
            foreach ($jabatan_ids as $jabatan_id) {
                $jabatanInsertStmt->execute([$penugasan_id, $jabatan_id]);
            }
        }
        
        $pdo->commit();
        $success = "Penugasan berhasil disimpan!";
        
    } catch (Exception $e) {
        $pdo->rollback();
        $error = "Gagal menyimpan penugasan: " . $e->getMessage();
    }
}

// Handle delete penugasan
if (isset($_GET['delete_penugasan']) && is_numeric($_GET['delete_penugasan'])) {
    $penugasan_id = (int)$_GET['delete_penugasan'];
    try {
        $pdo->beginTransaction();
        $pdo->prepare("DELETE FROM penugasan_jabatan WHERE penugasan_id = ?")->execute([$penugasan_id]);
        $pdo->prepare("DELETE FROM penugasan WHERE id = ?")->execute([$penugasan_id]);
        $pdo->commit();
        $success = "Penugasan berhasil dihapus!";
    } catch (Exception $e) {
        $pdo->rollback();
        $error = "Gagal menghapus penugasan: " . $e->getMessage();
    }
}

// Get filter values
$unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
$tahun_pelajaran = isset($_GET['tahun_pelajaran']) ? $_GET['tahun_pelajaran'] : '2025/2026';
$status_kepegawaian = isset($_GET['status_kepegawaian']) ? $_GET['status_kepegawaian'] : '';
$jabatan_ids_filter = isset($_GET['jabatan_ids_filter']) ? $_GET['jabatan_ids_filter'] : [];

// Initialize pegawais array
$pegawais = [];

// Get pegawai with penugasan data
$where = ["1=1"];
$params = [];

if ($unit_id && $unit_id > 0) {
    $where[] = "p.unit_id = ?";
    $params[] = $unit_id;
}

if ($status_kepegawaian) {
    $where[] = "p.status_kepegawaian = ?";
    $params[] = $status_kepegawaian;
}

if (!empty($jabatan_ids_filter)) {
    $placeholders_filter = implode(',', array_fill(0, count($jabatan_ids_filter), '?'));
    $where[] = "j.id IN ($placeholders_filter)";
    $params = array_merge($params, $jabatan_ids_filter);
}

// Perbaikan: Ubah query SQL untuk mengurutkan berdasarkan prioritas jabatan dan status kepegawaian
$sql = "SELECT 
            p.id as pegawai_id,
            p.nomor_induk,
            p.nama as pegawai_nama,
            p.status_kepegawaian,
            p.status_perkawinan,
            p.kelompok_pekerjaan,
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
        FROM pegawai p
        LEFT JOIN unit u ON p.unit_id = u.id
        LEFT JOIN penugasan pen ON p.id = pen.pegawai_id AND pen.unit_id = u.id AND pen.tahun_pelajaran = ?
        LEFT JOIN penugasan_jabatan pj ON pen.id = pj.penugasan_id
        LEFT JOIN jabatan j ON pj.jabatan_id = j.id
        WHERE " . implode(' AND ', $where) . "
        GROUP BY p.id, pen.id
        ORDER BY
            u.nama,
            CASE p.status_kepegawaian
                WHEN 'PNS' THEN 1
                WHEN 'GTY' THEN 2
                WHEN 'Honorer' THEN 3
                WHEN 'PTY' THEN 4
                ELSE 5
            END,
            p.nama";$params = array_merge([$tahun_pelajaran], $params);

// Execute query with error handling
try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $pegawais = $stmt->fetchAll();
    
    // Ensure $pegawais is always an array
    if (!is_array($pegawais)) {
        $pegawais = [];
    }
    
    // If still empty, try a simple fallback query to get all pegawai
    if (empty($pegawais) && empty($jabatan_ids_filter) && !$status_kepegawaian) {
        $fallbackSql = "SELECT 
                p.id as pegawai_id,
                p.nomor_induk,
                p.nama as pegawai_nama,
                p.status_kepegawaian,
                p.status_perkawinan,
                p.kelompok_pekerjaan,
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
            FROM pegawai p
            LEFT JOIN unit u ON p.unit_id = u.id
            LEFT JOIN penugasan pen ON p.id = pen.pegawai_id AND pen.tahun_pelajaran = ?";
        
        $fallbackParams = [$tahun_pelajaran];
        if ($unit_id && $unit_id > 0) {
            $fallbackSql .= " WHERE p.unit_id = ?";
            $fallbackParams[] = $unit_id;
        }
        
        $fallbackSql .= " ORDER BY u.nama, p.nama";
        
        $fallbackStmt = $pdo->prepare($fallbackSql);
        $fallbackStmt->execute($fallbackParams);
        $pegawais = $fallbackStmt->fetchAll();
    }
    
} catch (Exception $e) {
    // If query fails, initialize empty array
    $pegawais = [];
    error_log("Query error in input_penugasan.php: " . $e->getMessage());
}

// Get jabatan data separately to avoid complex GROUP_CONCAT
foreach ($pegawais as &$pegawai) {
    if ($pegawai['penugasan_id']) {
        $jabatanStmt = $pdo->prepare("
            SELECT j.id, j.nama, j.tunjangan_jabatan
            FROM penugasan_jabatan pj
            JOIN jabatan j ON pj.jabatan_id = j.id
            WHERE pj.penugasan_id = ?
            ORDER BY 
                CASE 
                    WHEN j.nama LIKE 'Kasek%' OR j.nama LIKE 'Kepala Sekolah%' THEN 1
                    WHEN j.nama LIKE 'Wakasek%' OR j.nama LIKE 'Wakil Kepala Sekolah%' THEN 2
                    WHEN j.nama LIKE 'PKS%' THEN 3
                    WHEN j.nama LIKE 'KTU%' OR j.nama LIKE 'Kepala Tata Usaha%' THEN 4
                    WHEN j.nama LIKE 'Kapro%' OR j.nama LIKE 'Kepala Program%' THEN 5
                    WHEN j.nama LIKE 'Wali Kelas%' OR j.nama LIKE 'Walikelas%' THEN 6
                    ELSE 7
                END, j.nama
        ");
        $jabatanStmt->execute([$pegawai['penugasan_id']]);
        $jabatanList = $jabatanStmt->fetchAll();
        
        $pegawai['jabatan_names'] = implode(', ', array_column($jabatanList, 'nama'));
        $pegawai['jabatan_ids'] = implode(',', array_column($jabatanList, 'id'));
        
        // Calculate jabatan priority based on highest priority jabatan
        $jabatanPriority = 7; // Default lowest priority
        foreach ($jabatanList as $jabatan) {
            $currentPriority = 7;
            if (strpos($jabatan['nama'], 'Kasek') !== false || strpos($jabatan['nama'], 'Kepala Sekolah') !== false) {
                $currentPriority = 1;
            } elseif (strpos($jabatan['nama'], 'Wakasek') !== false || strpos($jabatan['nama'], 'Wakil Kepala Sekolah') !== false) {
                $currentPriority = 2;
            } elseif (strpos($jabatan['nama'], 'PKS') !== false) {
                $currentPriority = 3;
            } elseif (strpos($jabatan['nama'], 'KTU') !== false || strpos($jabatan['nama'], 'Kepala Tata Usaha') !== false) {
                $currentPriority = 4;
            } elseif (strpos($jabatan['nama'], 'Kapro') !== false || strpos($jabatan['nama'], 'Kepala Program') !== false) {
                $currentPriority = 5;
            } elseif (strpos($jabatan['nama'], 'Wali Kelas') !== false || strpos($jabatan['nama'], 'Walikelas') !== false) {
                $currentPriority = 6;
            }
            
            if ($currentPriority < $jabatanPriority) {
                $jabatanPriority = $currentPriority;
            }
        }
        
        $pegawai['jabatan_priority'] = $jabatanPriority;
        $pegawai['jabatan_list'] = $jabatanList;
    } else {
        $pegawai['jabatan_names'] = '';
        $pegawai['jabatan_ids'] = '';
        $pegawai['jabatan_priority'] = 7; // Lowest priority for those without jabatan
        $pegawai['jabatan_list'] = [];
    }
    
    // Perbaikan: Ambil data honor per jam di sini
    $jamHonorStmt = $pdo->prepare("SELECT honor_per_jam FROM jam_honor WHERE unit_id = ? AND status_kepegawaian = ?");
    $jamHonorStmt->execute([$pegawai['unit_id'], $pegawai['status_kepegawaian']]);
    $jamHonorData = $jamHonorStmt->fetch();
    $pegawai['honor_per_jam'] = $jamHonorData['honor_per_jam'] ?? 0;
    
    // Fetch override rules for display
    $overrideRules = getOverrideRules($pdo, $pegawai['pegawai_id']);
    $overrideRulesDisplay = [];
    $hasOverride = false;
    foreach ($overrideRules as $rule) {
        if ($rule['is_active']) {
            $hasOverride = true;
            $ruleText = '';
            switch ($rule['rule_type']) {
                case 'no_tunjangan_anak': $ruleText = 'Tunjangan anak ditiadakan'; break;
                case 'no_tunjangan_keluarga': $ruleText = 'Tunjangan keluarga ditiadakan'; break;
                case 'no_tunjangan_beras': $ruleText = 'Tunjangan beras ditiadakan'; break;
                case 'custom_jam_wajib': $ruleText = 'Jam wajib disesuaikan menjadi ' . $rule['rule_value'] . ' jam'; break;
                case 'custom_tunjangan_anak_persen': $ruleText = 'Tunjangan anak disesuaikan menjadi ' . $rule['rule_value'] . '%'; break;
                case 'custom_tunjangan_keluarga_persen': $ruleText = 'Tunjangan keluarga disesuaikan menjadi ' . $rule['rule_value'] . '%'; break;
                case 'custom_tunjangan_beras_amount': $ruleText = 'Tunjangan beras disesuaikan menjadi ' . formatRupiah($rule['rule_value']); break;
                case 'no_honor_calculation': $ruleText = 'Perhitungan honor ditiadakan'; break;
            }
            if ($ruleText) {
                $overrideRulesDisplay[] = $ruleText . " (Alasan: " . ($rule['reason'] ?: '-') . ")";
            }
        }
    }
    $pegawai['has_override'] = $hasOverride;
    $pegawai['override_rules'] = $overrideRulesDisplay;
}
unset($pegawai); // Clean up reference

// Recalculate THP for display
foreach ($pegawais as &$pegawai) {
    if ($pegawai['penugasan_id']) {
        // Recalculate jam honor
        $pegawai['jam_honor'] = max(0, $pegawai['jam_mengajar'] - $pegawai['jam_wajib']);
        
        // Recalculate honor
        $pegawai['honor'] = $pegawai['jam_honor'] * ($pegawai['honor_per_jam'] ?? 0);
        
        // Recalculate THP (Take Home Pay)
        $total_tunjangan = $pegawai['tunjangan_keluarga'] + $pegawai['tunjangan_anak'] + $pegawai['tunjangan_beras'] + $pegawai['tunjangan_jabatan'];
        $pegawai['total'] = $pegawai['gaji_pokok'] + $total_tunjangan + $pegawai['honor'];
    }
}
unset($pegawai);

// Final sorting to ensure correct order based on jabatan priority and status kepegawaian
usort($pegawais, function($a, $b) {
    // First compare by unit name
    $unitCompare = strcmp($a['unit_nama'] ?? '', $b['unit_nama'] ?? '');
    if ($unitCompare !== 0) {
        return $unitCompare;
    }
    
    // Then compare by jabatan priority (lower number = higher priority)
    $jabatanPriorityA = $a['jabatan_priority'] ?? 7;
    $jabatanPriorityB = $b['jabatan_priority'] ?? 7;
    if ($jabatanPriorityA !== $jabatanPriorityB) {
        return $jabatanPriorityA - $jabatanPriorityB;
    }
    
    // Then compare by status kepegawaian priority
    $statusPriority = ['PNS' => 1, 'GTY' => 2, 'Honorer' => 3, 'PTY' => 4];
    $statusA = $statusPriority[$a['status_kepegawaian']] ?? 5;
    $statusB = $statusPriority[$b['status_kepegawaian']] ?? 5;
    if ($statusA !== $statusB) {
        return $statusA - $statusB;
    }
    
    // Finally compare by name
    return strcmp($a['pegawai_nama'], $b['pegawai_nama']);
});

// Get units and jabatan for dropdowns
$unitsStmt = $pdo->query("SELECT * FROM unit ORDER BY nama");
$units = $unitsStmt->fetchAll();

$jabatanStmt = $pdo->query("
    SELECT *, 
        CASE 
            WHEN nama LIKE 'Kasek%' OR nama LIKE 'Kepala Sekolah%' THEN 1
            WHEN nama LIKE 'Wakasek%' OR nama LIKE 'Wakil Kepala Sekolah%' THEN 2
            WHEN nama LIKE 'PKS%' THEN 3
            WHEN nama LIKE 'KTU%' OR nama LIKE 'Kepala Tata Usaha%' THEN 4
            WHEN nama LIKE 'Kapro%' OR nama LIKE 'Kepala Program%' THEN 5
            WHEN nama LIKE 'Wali Kelas%' OR nama LIKE 'Walikelas%' THEN 6
            ELSE 7
        END as jabatan_priority
    FROM jabatan 
    ORDER BY jabatan_priority, nama
");
$jabatans = $jabatanStmt->fetchAll();

// Get unit name for label based on filter
$unit_label_text = "Semua Unit Sekolah";
if ($unit_id > 0) {
    $unitNameStmt = $pdo->prepare("SELECT nama FROM unit WHERE id = ?");
    $unitNameStmt->execute([$unit_id]);
    $unitData = $unitNameStmt->fetch();
    if ($unitData) {
        $unit_label_text = $unitData['nama'];
    }
}

// Get menu items and user info
$menuItems = getMenuItems();
$userRole = $auth->getRole();
$fullName = $auth->getFullName();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Penugasan - SISFOPEMBDA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/dashboard.css" rel="stylesheet">
    <link href="assets/css/theme.css" rel="stylesheet">
    <style>
        /* Base table styling */
        .table {
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            background: white;
        }
        
        .table th {
            background: linear-gradient(135deg, #495057, #6c757d);
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            padding: 8px 6px;
            text-align: center;
            vertical-align: middle;
            border: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .table td {
            padding: 6px 4px;
            font-size: 0.7rem;
            vertical-align: middle;
            text-align: center;
            border-color: #e9ecef;
            transition: all 0.2s ease;
        }
        
        .table-hover tbody tr:hover {
            background: linear-gradient(135deg, rgba(0,123,255,0.08), rgba(0,123,255,0.12));
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .table-row-clickable:hover {
            background: linear-gradient(135deg, rgba(0,123,255,0.15), rgba(0,123,255,0.2)) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }
        
        /* Button styling improvements */
        .btn-group .btn {
            margin-right: 2px;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
            transition: all 0.2s ease;
        }
        
        .btn-group .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Modal and form styling */
        .modal-content {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
            background: linear-gradient(135deg, #007bff, #0056b3);
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            transition: all 0.2s ease;
        }
        
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
            transform: translateY(-1px);
        }
        
        /* PNS gaji pokok field styling */
        #modalGajiPokok:disabled {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-color: #dee2e6;
            color: #6c757d;
            cursor: not-allowed;
        }
        
        #modalGajiPokok:disabled::placeholder {
            color: #6c757d;
            opacity: 0.8;
        }
        
        /* Badge improvements */
        .badge {
            font-size: 0.6rem;
            padding: 0.25em 0.5em;
            border-radius: 8px;
            font-weight: 500;
        }
        
        /* Responsive table container */
        .table-responsive {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        /* Column width optimization */
        th:nth-child(1), td:nth-child(1) { width: 3%; min-width: 30px; }
        th:nth-child(2), td:nth-child(2) { width: 16%; min-width: 120px; text-align: left; }
        th:nth-child(3), td:nth-child(3) { width: 6%; min-width: 60px; }
        th:nth-child(4), td:nth-child(4) { width: 12%; min-width: 120px; text-align: left; }
        th:nth-child(5), td:nth-child(5) { width: 10%; min-width: 90px; }
        th:nth-child(6), td:nth-child(6) { width: 12%; min-width: 90px; }
        th:nth-child(7), td:nth-child(7) { width: 10%; min-width: 80px; }
        th:nth-child(8), td:nth-child(8) { width: 12%; min-width: 120px; text-align: left; }
        th:nth-child(9), td:nth-child(9) { width: 10%; min-width: 90px; }
        th:nth-child(10), td:nth-child(10) { width: 9%; min-width: 80px; }
        
        /* Enhanced tunjangan styling */
        .tunjangan-container {
            min-width: 140px;
            padding: 4px;
            background: rgba(248, 249, 250, 0.3);
            border-radius: 6px;
            border: 1px solid rgba(0,0,0,0.03);
        }
        
        .tunjangan-item {
            padding: 3px 0;
            margin: 1px 0;
            border-radius: 3px;
            transition: all 0.2s ease;
        }
        
        .tunjangan-item:not(:last-child) {
            border-bottom: 1px solid rgba(0,0,0,0.06);
            margin-bottom: 3px;
            padding-bottom: 3px;
        }
        
        .tunjangan-item:hover {
            background: rgba(255,255,255,0.6);
            transform: translateX(1px);
        }
        
        .tunjangan-label {
            font-size: 0.65rem;
            font-weight: 600;
            min-width: 70px;
            color: #495057;
        }
        
        .tunjangan-value {
            font-size: 0.65rem;
            font-weight: 700;
            white-space: nowrap;
            padding: 1px 4px;
            border-radius: 3px;
            background: rgba(255,255,255,0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-width: 60px;
        }
        
        .tunjangan-currency-symbol {
            font-size: 0.55rem;
            font-weight: 500;
            margin-right: 2px;
        }
        
        .tunjangan-currency-value {
            font-size: 0.6rem;
            font-weight: 600;
            text-align: right;
            flex-grow: 1;
        }
        
        /* Jabatan container styling */
        .jabatan-container {
            min-width: 120px;
            padding: 4px;
            background: rgba(248, 249, 250, 0.2);
            border-radius: 4px;
        }
        
        .jabatan-item {
            padding: 2px 0;
            margin: 1px 0;
            border-radius: 3px;
            transition: all 0.2s ease;
        }
        
        .jabatan-item:hover {
            background: rgba(255,255,255,0.4);
            transform: translateX(1px);
        }
        
        .jabatan-label {
            font-size: 0.65rem;
            font-weight: 600;
            color: #495057;
        }
        
        .jabatan-value {
            font-size: 0.65rem;
            font-weight: 700;
            padding: 1px 4px;
            border-radius: 3px;
            background: rgba(255,255,255,0.6);
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-width: 60px;
        }
        
        .jabatan-currency-symbol {
            font-size: 0.55rem;
            font-weight: 500;
            margin-right: 2px;
        }
        
        .jabatan-currency-value {
            font-size: 0.6rem;
            font-weight: 600;
            text-align: right;
            flex-grow: 1;
        }
        
        /* Jam container styling */
        .jam-container {
            min-width: 100px;
            padding: 4px;
            background: rgba(248, 249, 250, 0.2);
            border-radius: 4px;
        }
        
        /* Avatar circle enhancements */
        .avatar-circle {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        /* Preview calculation enhanced styling */
        .preview-container {
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .preview-item {
            padding: 10px 0;
            border-bottom: 1px solid rgba(0,0,0,0.08);
            transition: all 0.2s ease;
        }
        
        .preview-item:last-child {
            border-bottom: none;
        }
        
        .preview-item:hover {
            background: rgba(255,255,255,0.6);
            transform: translateX(3px);
            border-radius: 6px;
            margin: 0 -8px;
            padding: 10px 8px;
        }
        
        .preview-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
        }
        
        .preview-value {
            font-size: 0.9rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            background: rgba(255,255,255,0.8);
        }
        
        /* Card enhancements */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .card-header {
            border-radius: 15px 15px 0 0;
            font-weight: 600;
            border-bottom: 1px solid rgba(0,0,0,0.1);
        }
        
        /* Alert styling */
        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        /* Spinner enhancement */
        .fa-spinner {
            color: #007bff;
        }
        
        /* Responsive improvements */
        @media (max-width: 768px) {
            .table th, .table td {
                padding: 4px 3px;
                font-size: 0.65rem;
            }
            
            .name-text {
                font-size: 0.7rem;
                line-height: 1.1;
            }
            
            .nip-text {
                font-size: 0.6rem;
            }
            
            .tunjangan-container {
                min-width: 110px;
                padding: 3px;
            }
            
            .tunjangan-label, .tunjangan-currency-value {
                font-size: 0.6rem;
            }
            
            .tunjangan-currency-symbol {
                font-size: 0.5rem;
            }
            
            .jabatan-label, .jabatan-currency-value {
                font-size: 0.6rem;
            }
            
            .jabatan-currency-symbol {
                font-size: 0.5rem;
            }
            
            .btn-group .btn {
                font-size: 0.6rem;
                padding: 0.15rem 0.3rem;
            }
            
            .badge {
                font-size: 0.55rem;
                padding: 0.2em 0.4em;
            }
            
            .currency-symbol {
                font-size: 0.55rem;
            }
            
            .currency-value {
                font-size: 0.6rem;
            }
            
            /* Ensure names don't break layout on mobile */
            th:nth-child(2), td:nth-child(2) { 
                min-width: 100px;
                max-width: 130px;
            }
        }
        
        /* Text utility - removed truncation for names */
        .name-container {
            line-height: 1.2;
            word-wrap: break-word;
            white-space: normal;
        }
        
        .name-text {
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        
        .nip-text {
            font-size: 0.65rem;
            color: #6c757d;
            line-height: 1.1;
        }
        
        /* Currency format styling */
        .currency-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 80px;
        }
        
        .currency-symbol {
            font-size: 0.6rem;
            font-weight: 500;
            color: #6c757d;
            margin-right: 4px;
        }
        
        .currency-value {
            font-size: 0.65rem;
            font-weight: 600;
            text-align: right;
            flex-grow: 1;
        }
        
        .currency-display {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }
        
        .bg-gradient-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
        }
        
        .bg-gradient-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
        }
        
        .bg-gradient-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
        }
        
        /* Additional gradient button classes */
        .btn-gradient-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            border: none;
            color: white;
        }
        
        .btn-gradient-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            color: white;
        }
        
        .btn-gradient-success {
            background: linear-gradient(135deg, #28a745, #1e7e34);
            border: none;
            color: white;
        }
        
        .btn-gradient-success:hover {
            background: linear-gradient(135deg, #1e7e34, #155724);
            color: white;
        }
        
        .btn-gradient-info {
            background: linear-gradient(135deg, #17a2b8, #138496);
            border: none;
            color: white;
        }
        
        .btn-gradient-info:hover {
            background: linear-gradient(135deg, #138496, #0f6674);
            color: white;
        }
        
        .btn-gradient-warning {
            background: linear-gradient(135deg, #ffc107, #e0a800);
            border: none;
            color: #212529;
        }
        
        .btn-gradient-warning:hover {
            background: linear-gradient(135deg, #e0a800, #c69500);
            color: #212529;
        }
        
        .btn-gradient-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
        }
        
        .btn-gradient-danger:hover {
            background: linear-gradient(135deg, #c82333, #bd2130);
            color: white;
        }
        
        .btn-gradient-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            border: none;
            color: white;
        }
        
        .btn-gradient-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #495057);
            color: white;
        }
        
        /* Table row status styling */
        .table-success-subtle {
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(40, 167, 69, 0.05));
        }
        
        .table-warning-subtle {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 193, 7, 0.05));
        }
        
        /* Enhanced loading animation */
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }
    </style>
</head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <div class="container-fluid">
        <div class="row">
    <style>
        :root {
            --brand-primary:#2F5597;
            --brand-primary-accent:#4472C4;
            --brand-gradient:linear-gradient(90deg,#2F5597,#4472C4);
            --brand-gradient-soft:linear-gradient(135deg,#f0f5ff,#ffffff);
            --brand-danger:#C0392B;
            --brand-success:#1E8449;
        }
        body {background:var(--brand-gradient-soft); font-family:"Segoe UI", Arial, sans-serif;}
        .sidebar {background:var(--brand-primary); background:linear-gradient(180deg,#2F5597 0%,#24436f 100%);}
        .sidebar a {color:#e2e8f4; transition:all .2s ease;}
        .sidebar a:hover {background:rgba(255,255,255,0.08); color:#fff;}
        .main-content h2 {font-weight:600;}
        .card {border:none; box-shadow:0 4px 14px rgba(0,0,0,0.06); border-radius:14px; overflow:hidden;}
        .card-header {border:none;}
        .bg-gradient-primary {background:var(--brand-gradient)!important;}
        .btn-gradient-primary {background:var(--brand-gradient); color:#fff; border:none; box-shadow:0 3px 8px rgba(47,85,151,.35);}    
        .btn-gradient-primary:hover {filter:brightness(1.05);}    
        .form-label {font-weight:600; font-size:.85rem; text-transform:uppercase; letter-spacing:.5px; color:#2F5597;}
        .badge.bg-white.text-primary {background:#fff!important; border:1px solid var(--brand-primary-accent); font-weight:600;}
        table.data-table thead th {background:var(--brand-primary-accent); color:#fff; font-weight:600; border:0;}
        table.data-table tbody tr:nth-child(even){background:#f5f9ff;}
        table.data-table tbody tr:hover {background:#e8f1ff;}
        .table-responsive {border-radius:12px;}
        .filter-section .card-body {background:linear-gradient(145deg,#ffffff,#f1f6ff);}    
        .form-select, .form-control {border-radius:10px; border:1px solid #c9d6ec;}
        .form-check-input:checked {background-color:var(--brand-primary); border-color:var(--brand-primary);}
        .alert-success {background:#e9f7ef; border:1px solid #a9e2c2; color:#1E8449;}
        .alert-danger {background:#fdecea; border:1px solid #f5b7b1; color:#C0392B;}
        .override-badge {background:var(--brand-danger);}
        .table-sticky thead th {position:sticky; top:0; z-index:2;}
        .section-title {font-size:1.05rem; font-weight:600; letter-spacing:.5px; color:var(--brand-primary); margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem;}
        .mini-stat {display:flex; gap:1rem; flex-wrap:wrap; margin:1rem 0;}
        .mini-stat .item {flex:1 1 120px; background:#fff; padding:.75rem 1rem; border-radius:12px; box-shadow:0 2px 6px rgba(0,0,0,0.05); font-size:.8rem;}
        .mini-stat .item span {display:block; font-weight:600; color:var(--brand-primary); font-size:.7rem; text-transform:uppercase; letter-spacing:.5px;}
        .sticky-action-bar {position:sticky; bottom:0; background:#fff; padding:1rem; border-top:1px solid #d9e3f2; display:flex; justify-content:flex-end; gap:.75rem;}
        .btn-outline-secondary {border-radius:10px;}
        .btn {border-radius:10px; font-weight:500;}
    </style>
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="d-flex flex-column">
                    <div class="p-3 text-center border-bottom border-light">
                        <h4 class="text-white mb-0">SISFOPEMBDA</h4>
                        <small class="text-light">Sistem Informasi Administrasi</small>
                    </div>
                    
                    <div class="p-3 border-bottom border-light">
                        <div class="d-flex align-items-center">
                            <div class="avatar-circle me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="text-white small fw-bold"><?php echo htmlspecialchars($fullName); ?></div>
                                <div class="text-light small">
                                    <?php 
                                    $roleLabels = [
                                        'admin' => 'Administrator',
                                        'operator_sekolah' => 'Operator Sekolah', 
                                        'kepala_sekolah' => 'Kepala Sekolah'
                                    ];
                                    echo $roleLabels[$userRole] ?? $userRole;
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <nav class="nav flex-column mt-3">
                        <?php foreach ($menuItems as $key => $menu): ?>
                            <a class="nav-link <?php echo $key === 'penugasan' ? 'active' : ''; ?>" href="<?php echo $menu['file']; ?>">
                                <i class="<?php echo $menu['icon']; ?> me-2"></i> <?php echo $menu['title']; ?>
                            </a>
                        <?php endforeach; ?>
                        
                        <hr class="border-light mx-3">
                        <a class="nav-link text-danger" href="?logout=1">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </nav>
                </div>
            </div>

            <div class="col-md-9 col-lg-10 main-content">
                <div class="container-fluid p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2><i class="fas fa-tasks me-3"></i>Input Penugasan Pegawai</h2>
                            <p class="text-muted mb-0">Kelola penugasan jabatan dan jam mengajar pegawai</p>
                        </div>
                    </div>
                    
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Unit Sekolah</label>
                                    <select name="unit_id" class="form-select">
                                        <option value="">Semua Unit</option>
                                        <?php foreach ($units as $unit): ?>
                                            <option value="<?php echo $unit['id']; ?>" <?php echo $unit_id == $unit['id'] ? 'selected' : ''; ?>>
                                                <?php echo $unit['nama']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Tahun Pelajaran</label>
                                    <select name="tahun_pelajaran" class="form-select">
                                        <option value="2025/2026" <?php echo $tahun_pelajaran == '2025/2026' ? 'selected' : ''; ?>>2025/2026</option>
                                        <option value="2026/2027" <?php echo $tahun_pelajaran == '2026/2027' ? 'selected' : ''; ?>>2026/2027</option>
                                        <option value="2027/2028" <?php echo $tahun_pelajaran == '2027/2028' ? 'selected' : ''; ?>>2027/2028</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Status</label>
                                    <select name="status_kepegawaian" class="form-select">
                                        <option value="">Semua Status</option>
                                        <option value="PNS" <?php echo $status_kepegawaian == 'PNS' ? 'selected' : ''; ?>>PNS</option>
                                        <option value="GTY" <?php echo $status_kepegawaian == 'GTY' ? 'selected' : ''; ?>>GTY</option>
                                        <option value="Honorer" <?php echo $status_kepegawaian == 'Honorer' ? 'selected' : ''; ?>>Honorer</option>
                                        <option value="PTY" <?php echo $status_kepegawaian == 'PTY' ? 'selected' : ''; ?>>PTY</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Jabatan</label>
                                    <div class="border rounded p-2 bg-light" style="max-height: 100px; overflow-y: auto;">
                                        <?php foreach ($jabatans as $jabatan): ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="jabatan_ids_filter[]" value="<?php echo $jabatan['id']; ?>" id="filterJabatan<?php echo $jabatan['id']; ?>"
                                                    <?php echo in_array($jabatan['id'], $jabatan_ids_filter) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="filterJabatan<?php echo $jabatan['id']; ?>">
                                                    <?php echo htmlspecialchars($jabatan['nama']); ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <div class="col-12 d-flex align-items-end">
                                    <button type="submit" class="btn btn-gradient-primary w-100 py-2 fw-bold">
                                        <i class="fas fa-filter me-2"></i>Terapkan Filter
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-gradient-primary text-white">
                            <h5 class="mb-0 d-flex align-items-center">
                                <i class="fas fa-table me-2"></i>Data Penugasan Pegawai
                                <span class="badge bg-white text-primary ms-2 px-3 py-2 rounded-pill"><?php echo htmlspecialchars($unit_label_text); ?></span>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($pegawais)): ?>
                                <div class="bg-light px-4 py-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-8">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                <small class="text-dark fw-bold">
                                                    Unit : <span class="text-primary"><?php echo htmlspecialchars($unit_label_text); ?></span>
                                                    <?php if (!empty($tahun_pelajaran)): ?>
                                                        <span class="mx-2">|</span>
                                                        Tahun Pelajaran: <span class="text-success"><?php echo htmlspecialchars($tahun_pelajaran); ?></span>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4 text-end">
                                            <div class="d-flex align-items-center justify-content-end gap-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-users text-secondary me-2"></i>
                                                    <small class="text-muted fw-bold">Total Pegawai :</small>
                                                    <span class="badge bg-gradient-secondary ms-2 px-3 py-2 rounded-pill"><?php echo is_array($pegawais) ? count($pegawais) : 0; ?></span>
                                                </div>
                                                <?php if (!empty($pegawais)): ?>
                                                <button class="btn btn-success btn-sm" onclick="exportToExcel()" title="Export ke Excel">
                                                    <i class="fas fa-file-excel me-1"></i>Export Excel
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="p-3">
                            <?php endif; ?>
                            
                            <?php if (empty($pegawais)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-users text-muted" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">Tidak ada data pegawai</h5>
                                    <?php if ($unit_id > 0): ?>
                                        <p class="text-muted">
                                            Tidak ada pegawai di unit <strong><?php echo htmlspecialchars($unit_label_text); ?></strong>
                                            <?php if (!empty($tahun_pelajaran)): ?>
                                                untuk tahun pelajaran <strong><?php echo htmlspecialchars($tahun_pelajaran); ?></strong>
                                            <?php endif; ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="text-muted">Silakan pilih unit sekolah dan tahun pelajaran untuk melihat data pegawai</p>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead>
                                            <tr>
                                                <th class="text-center"><i class="fas fa-hashtag me-1"></i>No</th>
                                                <th><i class="fas fa-user me-1"></i>Nama Pegawai</th>
                                                <th class="text-center"><i class="fas fa-id-badge me-1"></i>Status</th>
                                                <th><i class="fas fa-medal me-1"></i>Nama Jabatan</th>
                                                <th class="text-center"><i class="fas fa-wallet me-1"></i>Gaji Pokok</th>
                                                <th class="text-center"><i class="fas fa-clock me-1"></i>Jam Mengajar</th>
                                                <th class="text-center"><i class="fas fa-money-bill me-1"></i>Honor Mengajar</th>
                                                <th><i class="fas fa-home me-1"></i>Tunj. Keluarga</th>
                                                <th class="text-center"><i class="fas fa-hand-holding-usd me-1"></i>THP</th>
                                                <th class="text-center"><i class="fas fa-cogs me-1"></i>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; foreach ($pegawais as $pegawai): ?>
                                                <tr class="<?php echo $pegawai['penugasan_id'] ? 'table-success-subtle' : 'table-warning-subtle'; ?> position-relative">
                                                    <td class="text-center fw-bold"><?php echo $no++; ?></td>
                                                    <td>
                                                        <div class="name-container">
                                                            <div class="name-text text-dark fw-bold" style="font-size: 0.85rem; line-height: 1.2; word-wrap: break-word; white-space: normal;"><?php echo htmlspecialchars($pegawai['pegawai_nama']); ?></div>
                                                            <div class="nip-text"><?php echo htmlspecialchars($pegawai['nomor_induk']); ?></div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php echo getStatusBadge($pegawai['status_kepegawaian']); ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($pegawai['penugasan_id'] && $pegawai['jabatan_names']): ?>
                                                            <div class="jabatan-container">
                                                                <?php 
                                                                // Get detailed jabatan with tunjangan
                                                                $jabatanDetailStmt = $pdo->prepare("
                                                                    SELECT j.nama, j.tunjangan_jabatan
                                                                    FROM penugasan_jabatan pj
                                                                    JOIN jabatan j ON pj.jabatan_id = j.id
                                                                    WHERE pj.penugasan_id = ?
                                                                    ORDER BY j.tunjangan_jabatan DESC
                                                                ");
                                                                $jabatanDetailStmt->execute([$pegawai['penugasan_id']]);
                                                                $jabatanDetails = $jabatanDetailStmt->fetchAll();
                                                                
                                                                foreach ($jabatanDetails as $jabatan): ?>
                                                                    <div class="jabatan-item d-flex justify-content-between align-items-center mb-2">
                                                                        <span class="jabatan-label text-dark"><?php echo htmlspecialchars($jabatan['nama']); ?></span>
                                                                        <?php if ($jabatan['tunjangan_jabatan'] > 0): ?>
                                                                            <div class="jabatan-value text-primary">
                                                                                <span class="jabatan-currency-symbol" style="font-size: 0.75rem;">Rp</span>
                                                                                <span class="jabatan-currency-value fw-bold" style="font-size: 0.8rem;"><?php echo number_format($jabatan['tunjangan_jabatan'], 0, ',', '.'); ?></span>
                                                                            </div>
                                                                        <?php else: ?>
                                                                            <span class="text-muted fst-italic" style="font-size: 0.6rem;">-</span>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted fst-italic">Belum ada jabatan</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($pegawai['penugasan_id'] && $pegawai['gaji_pokok'] > 0): ?>
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-wallet text-success me-1" style="font-size: 0.7rem;"></i>
                                                                <div class="currency-display">
                                                                    <span class="currency-symbol" style="font-size: 0.8rem;">Rp</span>
                                                                    <span class="currency-value text-success fw-bold" style="font-size: 0.85rem;"><?php echo number_format($pegawai['gaji_pokok'], 0, ',', '.'); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted fst-italic" style="font-size: 0.6rem;">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($pegawai['penugasan_id']): ?>
                                                            <div class="jam-container">
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <small class="text-dark fw-bold" style="font-size: 0.6rem;">Mengajar:</small>
                                                                    <span class="badge bg-gradient-primary" style="font-size: 0.55rem;"><?php echo $pegawai['jam_mengajar']; ?> jam</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                                    <small class="text-dark fw-bold" style="font-size: 0.6rem;">Wajib:</small>
                                                                    <span class="badge bg-dark" style="font-size: 0.55rem;">
                                                                        <?php echo $pegawai['jam_wajib']; ?> jam
                                                                        <?php 
                                                                        $overrideRules = getOverrideRules($pdo, $pegawai['pegawai_id']);
                                                                        if (hasOverrideRule($overrideRules, 'custom_jam_wajib')): 
                                                                        ?>
                                                                            <i class="fas fa-cog text-warning ms-1" style="font-size: 0.5rem;" title="Custom jam wajib aktif"></i>
                                                                        <?php endif; ?>
                                                                    </span>
                                                                </div>
                                                                <div class="d-flex justify-content-between align-items-center">
                                                                    <small class="text-dark fw-bold" style="font-size: 0.6rem;">Honor:</small>
                                                                    <span class="badge bg-gradient-success" style="font-size: 0.55rem;"><?php echo $pegawai['jam_honor']; ?> jam</span>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted fst-italic">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($pegawai['penugasan_id'] && $pegawai['honor'] > 0): ?>
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-money-bill-wave text-danger me-1" style="font-size: 0.7rem;"></i>
                                                                <div class="currency-display">
                                                                    <span class="currency-symbol" style="font-size: 0.8rem; color: #dc3545;">Rp</span>
                                                                    <span class="currency-value fw-bold" style="font-size: 0.85rem; color: #8b1538;"><?php echo number_format($pegawai['honor'], 0, ',', '.'); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted fst-italic" style="font-size: 0.6rem;">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <?php if ($pegawai['penugasan_id']): ?>
                                                            <div class="tunjangan-container d-flex flex-column gap-1">
                                                                <?php if ($pegawai['tunjangan_keluarga'] > 0): ?>
                                                                    <div class="tunjangan-item d-flex justify-content-between align-items-center">
                                                                        <span class="tunjangan-label text-dark">Tunj. Keluarga:</span>
                                                                        <div class="tunjangan-value text-success ms-2">
                                                                            <span class="tunjangan-currency-symbol">Rp</span>
                                                                            <span class="tunjangan-currency-value"><?php echo number_format($pegawai['tunjangan_keluarga'], 0, ',', '.'); ?></span>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if ($pegawai['tunjangan_anak'] > 0): ?>
                                                                    <div class="tunjangan-item d-flex justify-content-between align-items-center">
                                                                        <span class="tunjangan-label text-dark">Tunj. Anak:</span>
                                                                        <div class="tunjangan-value text-info ms-2">
                                                                            <span class="tunjangan-currency-symbol">Rp</span>
                                                                            <span class="tunjangan-currency-value"><?php echo number_format($pegawai['tunjangan_anak'], 0, ',', '.'); ?></span>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if ($pegawai['tunjangan_beras'] > 0): ?>
                                                                    <div class="tunjangan-item d-flex justify-content-between align-items-center">
                                                                        <span class="tunjangan-label text-dark">Tunj. Beras:</span>
                                                                        <div class="tunjangan-value text-warning ms-2">
                                                                            <span class="tunjangan-currency-symbol">Rp</span>
                                                                            <span class="tunjangan-currency-value"><?php echo number_format($pegawai['tunjangan_beras'], 0, ',', '.'); ?></span>
                                                                        </div>
                                                                    </div>
                                                                <?php endif; ?>
                                                                <?php if ($pegawai['tunjangan_keluarga'] == 0 && $pegawai['tunjangan_anak'] == 0 && $pegawai['tunjangan_beras'] == 0): ?>
                                                                    <span class="text-muted">-</span>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($pegawai['penugasan_id'] && $pegawai['total'] > 0): ?>
                                                            <div class="d-flex align-items-center justify-content-center">
                                                                <i class="fas fa-hand-holding-usd text-success me-1" style="font-size: 0.7rem;"></i>
                                                                <div class="currency-display">
                                                                    <span class="currency-symbol" style="font-size: 0.85rem;">Rp</span>
                                                                    <span class="currency-value text-success fw-bold" style="font-size: 0.9rem;"><?php echo number_format($pegawai['total'], 0, ',', '.'); ?></span>
                                                                </div>
                                                            </div>
                                                        <?php else: ?>
                                                            <span class="text-muted fst-italic" style="font-size: 0.6rem;">-</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?php if ($pegawai['penugasan_id']): ?>
                                                            <div class="d-flex flex-column gap-1" role="group">
                                                                <button class="btn btn-sm btn-gradient-info fw-bold" onclick="showGajiDetail(<?php echo htmlspecialchars(json_encode($pegawai), ENT_QUOTES, 'UTF-8'); ?>)" title="Lihat Detail Gaji" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">
                                                                    <i class="fas fa-eye me-1" style="font-size: 0.6rem;"></i>Detail
                                                                </button>
                                                                <button class="btn btn-sm btn-gradient-primary" onclick="editPenugasan(<?php echo htmlspecialchars(json_encode($pegawai), ENT_QUOTES, 'UTF-8'); ?>)" title="Edit Penugasan" style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">
                                                                    <i class="fas fa-edit me-1" style="font-size: 0.6rem;"></i>Edit
                                                                </button>
                                                                <a href="?delete_penugasan=<?php echo $pegawai['penugasan_id']; ?>&unit_id=<?php echo $unit_id; ?>&tahun_pelajaran=<?php echo urlencode($tahun_pelajaran); ?>" 
                                                                    class="btn btn-sm btn-gradient-danger" 
                                                                    onclick="return confirm('Yakin ingin menghapus penugasan ini?')"
                                                                    title="Hapus Penugasan"
                                                                    style="font-size: 0.65rem; padding: 0.2rem 0.4rem;">
                                                                    <i class="fas fa-trash me-1" style="font-size: 0.6rem;"></i>Hapus
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            <button class="btn btn-sm btn-gradient-success fw-bold" onclick="addPenugasan(<?php echo htmlspecialchars(json_encode($pegawai), ENT_QUOTES, 'UTF-8'); ?>)" title="Tambah Penugasan" style="font-size: 0.65rem; padding: 0.25rem 0.5rem;">
                                                                <i class="fas fa-plus me-1" style="font-size: 0.6rem;"></i>Tambah
                                                            </button>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="penugasanModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header bg-gradient-primary text-white">
                        <h5 class="modal-title d-flex align-items-center">
                            <i class="fas fa-tasks me-2"></i>Input Penugasan Pegawai
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <input type="hidden" name="action" value="save_penugasan">
                        <input type="hidden" name="pegawai_id" id="modalPegawaiId">
                        <input type="hidden" name="unit_id" id="modalUnitId">
                        <input type="hidden" name="tahun_pelajaran" value="<?php echo $tahun_pelajaran; ?>">
                        
                        <div class="alert alert-info">
                            <strong>Pegawai:</strong> <span id="modalPegawaiNama"></span>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label"><strong>Gaji Pokok</strong></label>
                                <input type="text" name="gaji_pokok" id="modalGajiPokok" class="form-control" required placeholder="Contoh: 3.000.000">
                                <div class="form-text" id="gajiPokokHelpText">Gaji pokok pegawai (gunakan titik sebagai pemisah ribuan)</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><strong>Jumlah Anak</strong></label>
                                <input type="number" name="jumlah_anak" id="modalJumlahAnak" class="form-control" min="0" max="10" required placeholder="Contoh: 2">
                                <div class="form-text">Jumlah anak untuk perhitungan tunjangan</div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Jam Mengajar per Minggu</strong></label>
                            <input type="number" name="jam_mengajar" id="modalJamMengajar" class="form-control form-control-lg" min="0" required placeholder="Contoh: 24">
                            <div class="form-text">Masukkan jumlah jam mengajar dalam seminggu</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label"><strong>Jabatan yang Dipegang</strong></label>
                            <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                                <?php foreach ($jabatans as $jabatan): ?>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input jabatan-checkbox" type="checkbox" name="jabatan_ids[]" value="<?php echo $jabatan['id']; ?>" id="jabatan<?php echo $jabatan['id']; ?>">
                                        <label class="form-check-label w-100" for="jabatan<?php echo $jabatan['id']; ?>">
                                            <div class="d-flex justify-content-between">
                                                <span><?php echo htmlspecialchars($jabatan['nama']); ?></span>
                                                <small class="text-primary fw-bold"><?php echo formatRupiah($jabatan['tunjangan_jabatan']); ?></small>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <div class="form-text">Pilih semua jabatan yang dipegang pegawai (boleh lebih dari satu)</div>
                        </div>
                        
                        <div id="calculationPreview" style="display: none;"></div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Batal
                        </button>
                        <button type="submit" class="btn btn-gradient-primary fw-bold">
                            <i class="fas fa-save me-1"></i>Simpan Penugasan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="gajiDetailModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-gradient-success text-white">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="fas fa-money-bill-wave me-2"></i>Detail Perhitungan Gaji
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <div class="col-md-8">
                            <div class="card border-0 bg-light">
                                <div class="card-body py-2">
                                    <h6 class="card-title mb-1" id="detailPegawaiNama">-</h6>
                                    <small class="text-muted" id="detailPegawaiInfo">-</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="card border-0 bg-primary text-white">
                                <div class="card-body py-2">
                                    <small class="d-block">Take Home Pay</small>
                                    <h5 class="mb-0" id="detailTotalTHP">Rp 0</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white py-2">
                                    <h6 class="mb-0"><i class="fas fa-wallet me-2"></i>Gaji Pokok</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <span>Gaji Pokok:</span>
                                        <strong id="detailGajiPokok">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark py-2">
                                    <h6 class="mb-0"><i class="fas fa-medal me-2"></i>Tunjangan Jabatan</h6>
                                </div>
                                <div class="card-body" id="detailTunjanganJabatan">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white py-2">
                                    <h6 class="mb-0"><i class="fas fa-home me-2"></i>Tunjangan Keluarga</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Tunjangan Keluarga:</span>
                                            <span id="detailTunjanganKeluarga">Rp 0</span>
                                        </div>
                                        <small class="text-muted" id="detailCalculationKeluarga">-</small>
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Tunjangan Anak:</span>
                                            <span id="detailTunjanganAnak">Rp 0</span>
                                        </div>
                                        <small class="text-muted" id="detailCalculationAnak">-</small>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Tunjangan Beras:</span>
                                        <span id="detailTunjanganBeras">Rp 0</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>Subtotal:</strong>
                                        <strong id="detailSubtotalKeluarga">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-header bg-success text-white py-2">
                                    <h6 class="mb-0"><i class="fas fa-chalkboard-teacher me-2"></i>Honor Mengajar</h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Jam Mengajar:</span>
                                        <span id="detailJamMengajar">0 jam</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Jam Wajib:</span>
                                        <span>
                                            <span id="detailJamWajib">0 jam</span>
                                            <span id="detailJamWajibCustom" style="display: none;">
                                                <i class="fas fa-cog text-warning ms-1" title="Custom jam wajib aktif"></i>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Jam Honor:</span>
                                        <span id="detailJamHonor">0 jam</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Honor per Jam:</span>
                                        <span id="detailHonorPerJam">Rp 0</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between">
                                        <strong>Total Honor:</strong>
                                        <strong id="detailTotalHonor">Rp 0</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-warning mt-3" id="overrideRulesCard" style="display: none;">
                        <div class="card-header bg-warning text-dark py-2">
                            <h6 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Aturan Khusus Diterapkan</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning mb-2">
                                <small><i class="fas fa-info-circle me-1"></i>Pegawai ini memiliki aturan khusus yang mempengaruhi perhitungan gaji.</small>
                            </div>
                            <div id="overrideRulesList">
                            </div>
                        </div>
                    </div>

                    <div class="card border-dark mt-3">
                        <div class="card-header bg-dark text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-calculator me-2"></i>Ringkasan Perhitungan</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Gaji Pokok:</span>
                                <span id="summaryGajiPokok">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Tunjangan Jabatan:</span>
                                <span id="summaryTunjanganJabatan">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Total Tunjangan Keluarga:</span>
                                <span id="summaryTunjanganKeluarga">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Honor Mengajar:</span>
                                <span id="summaryHonor">Rp 0</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <strong style="font-size: 1.1em;">TAKE HOME PAY (THP):</strong>
                                <strong style="font-size: 1.2em; color: #28a745;" id="summaryTHP">Rp 0</strong>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary" onclick="printGajiDetail()">
                        <i class="fas fa-print me-1"></i>Cetak Slip Gaji
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Helper function to format number with dot separator for thousands
        function formatNumberWithDots(num) {
            if (num === null || num === undefined || isNaN(num)) return '';
            const numStr = num.toString().replace(/[^\d]/g, '');
            return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
        
        // Helper function to get raw number from formatted string
        function getRawNumber(value) {
            return parseInt(value.replace(/[^\d]/g, '')) || 0;
        }

        // Function to check if pegawai has no gaji pokok
        function isNoGajiPokok(statusKepegawaian, kelompokPekerjaan) {
            if (statusKepegawaian === 'PNS') {
                return true;
            }
            if (statusKepegawaian === 'Kontrak') {
                return true;
            }
            if (statusKepegawaian === 'Honorer' && kelompokPekerjaan === 'Pendidik') {
                return true;
            }
            return false;
        }
        
        // Fungsi untuk mengambil data detail pegawai dan membuka modal
        function showGajiDetail(pegawai) {
            console.log('showGajiDetail called with:', pegawai);
            
            // Simpan penugasan ID untuk fungsi cetak
            window.currentPenugasanId = pegawai.penugasan_id;
            
            populateGajiDetail(pegawai);
            new bootstrap.Modal(document.getElementById('gajiDetailModal')).show();
        }

        // Fungsi untuk membuka modal tambah penugasan
        function addPenugasan(pegawai) {
            console.log('addPenugasan called with:', pegawai);
            
            document.getElementById('modalPegawaiId').value = pegawai.pegawai_id;
            document.getElementById('modalPegawaiNama').textContent = pegawai.pegawai_nama;
            document.getElementById('modalUnitId').value = pegawai.unit_id;
            document.getElementById('modalJumlahAnak').value = pegawai.jumlah_anak;
            document.getElementById('modalJamMengajar').value = '';

            const gajiPokokInput = document.getElementById('modalGajiPokok');
            const gajiPokokHelpText = document.getElementById('gajiPokokHelpText');

            if (isNoGajiPokok(pegawai.status_kepegawaian, pegawai.kelompok_pekerjaan)) {
                gajiPokokInput.value = '0';
                gajiPokokInput.disabled = true;
                let helpText = '';
                if (pegawai.status_kepegawaian === 'PNS') {
                    helpText = 'Gaji pokok PNS sudah ditentukan pemerintah (tidak perlu diinput)';
                } else if (pegawai.status_kepegawaian === 'Kontrak') {
                    helpText = 'Pegawai Kontrak tidak memiliki gaji pokok (sama seperti PNS)';
                } else if (pegawai.kelompok_pekerjaan === 'Pendidik') {
                    helpText = 'Honorer Pendidik tidak memiliki gaji pokok (hanya honor mengajar)';
                }
                gajiPokokHelpText.textContent = helpText;
            } else {
                gajiPokokInput.value = ''; // Kosongkan input untuk input baru
                gajiPokokInput.disabled = false;
                gajiPokokHelpText.textContent = 'Gaji pokok pegawai (gunakan titik sebagai pemisah ribuan)';
            }
            
            document.querySelectorAll('.jabatan-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            clearCalculationPreview();
            
            new bootstrap.Modal(document.getElementById('penugasanModal')).show();
            setTimeout(updateRealtimeCalculation, 500);
        }

        // Fungsi untuk membuka modal edit penugasan
        function editPenugasan(pegawai) {
            console.log('editPenugasan called with:', pegawai);
            
            document.getElementById('modalPegawaiId').value = pegawai.pegawai_id;
            document.getElementById('modalPegawaiNama').textContent = pegawai.pegawai_nama;
            document.getElementById('modalUnitId').value = pegawai.unit_id;
            document.getElementById('modalJumlahAnak').value = pegawai.jumlah_anak;
            document.getElementById('modalJamMengajar').value = pegawai.jam_mengajar;
            
            const gajiPokokInput = document.getElementById('modalGajiPokok');
            const gajiPokokHelpText = document.getElementById('gajiPokokHelpText');
            
            if (isNoGajiPokok(pegawai.status_kepegawaian, pegawai.kelompok_pekerjaan)) {
                gajiPokokInput.value = '0';
                gajiPokokInput.disabled = true;
                let helpText = '';
                if (pegawai.status_kepegawaian === 'PNS') {
                    helpText = 'Gaji pokok PNS sudah ditentukan pemerintah (tidak perlu diinput)';
                } else if (pegawai.status_kepegawaian === 'Kontrak') {
                    helpText = 'Pegawai Kontrak tidak memiliki gaji pokok (sama seperti PNS)';
                } else if (pegawai.kelompok_pekerjaan === 'Pendidik') {
                    helpText = 'Honorer Pendidik tidak memiliki gaji pokok (hanya honor mengajar)';
                }
                gajiPokokHelpText.textContent = helpText;
            } else {
                // Perbaikan: Pastikan nilai yang diisi adalah angka murni sebelum diformat
                // Jika gaji pokok 0, kosongkan input
                const gajiPokokValue = parseFloat(pegawai.gaji_pokok) || 0;
                gajiPokokInput.value = gajiPokokValue > 0 ? formatNumberWithDots(gajiPokokValue) : '';
                gajiPokokInput.disabled = false;
                gajiPokokHelpText.textContent = 'Gaji pokok pegawai (gunakan titik sebagai pemisah ribuan)';
            }
            
            document.querySelectorAll('.jabatan-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            if (pegawai.jabatan_ids) {
                const selectedJabatan = pegawai.jabatan_ids.split(',');
                selectedJabatan.forEach(id => {
                    const checkbox = document.getElementById('jabatan' + id);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            }
            
            clearCalculationPreview();
            
            new bootstrap.Modal(document.getElementById('penugasanModal')).show();
            setTimeout(updateRealtimeCalculation, 500);
        }

        // Fungsi untuk mengisi modal detail gaji
        function populateGajiDetail(data) {
            console.log('populateGajiDetail called with:', data);
            
            if (!data) {
                alert('No data received');
                return;
            }
            
            // Format rupiah helper
            const formatRupiah = (amount) => {
                if (amount === null || amount === undefined) return 'Rp 0';
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(parseFloat(amount));
            }
            
            // Basic Info
            document.getElementById('detailPegawaiNama').textContent = data.pegawai_nama || 'N/A';
            document.getElementById('detailPegawaiInfo').textContent = 
                (data.nomor_induk || 'N/A') + ' | ' + (data.status_kepegawaian || 'N/A') + ' | ' + (data.unit_nama || 'N/A');
            
            // Gaji Pokok
            document.getElementById('detailGajiPokok').textContent = formatRupiah(data.gaji_pokok);
            
            // Tunjangan Jabatan
            let jabatanHtml = '';
            if (data.jabatan_list && data.jabatan_list.length > 0) {
                data.jabatan_list.forEach(jabatan => {
                    jabatanHtml += `
                        <div class="d-flex justify-content-between mb-2">
                            <span>${jabatan.nama}:</span>
                            <span>${formatRupiah(jabatan.tunjangan_jabatan)}</span>
                        </div>
                    `;
                });
                jabatanHtml += `
                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <strong>Total:</strong>
                        <strong>${formatRupiah(data.tunjangan_jabatan)}</strong>
                    </div>
                `;
            } else {
                jabatanHtml = '<span class="text-muted">Tidak ada jabatan</span>';
            }
            document.getElementById('detailTunjanganJabatan').innerHTML = jabatanHtml;
            
            // Tunjangan Keluarga
            document.getElementById('detailTunjanganKeluarga').textContent = formatRupiah(data.tunjangan_keluarga);
            document.getElementById('detailTunjanganAnak').textContent = formatRupiah(data.tunjangan_anak);
            document.getElementById('detailTunjanganBeras').textContent = formatRupiah(data.tunjangan_beras);
            
            // Tampilkan perhitungan detail
            const gajiPokok = data.gaji_pokok;
            if (data.status_perkawinan === 'Menikah') {
                document.getElementById('detailCalculationKeluarga').textContent = 
                    `10% × ${formatRupiah(gajiPokok)}`;
            } else {
                document.getElementById('detailCalculationKeluarga').textContent = 'Tidak menikah';
            }
            
            if (data.status_perkawinan === 'Menikah' && data.jumlah_anak > 0) {
                document.getElementById('detailCalculationAnak').textContent = 
                    `${data.jumlah_anak} anak × 5% × ${formatRupiah(gajiPokok)}`;
            } else if (data.status_perkawinan === 'Menikah' && data.jumlah_anak === 0) {
                document.getElementById('detailCalculationAnak').textContent = 'Tidak ada anak';
            } else {
                document.getElementById('detailCalculationAnak').textContent = 'Tidak menikah';
            }
            
            const subtotalKeluarga = parseFloat(data.tunjangan_keluarga || 0) + parseFloat(data.tunjangan_anak || 0) + parseFloat(data.tunjangan_beras || 0);
            document.getElementById('detailSubtotalKeluarga').textContent = formatRupiah(subtotalKeluarga);
            
            // Honor Mengajar
            const jamHonor = Math.max(0, data.jam_mengajar - data.jam_wajib);
            const totalHonor = jamHonor * (data.honor_per_jam || 0);
            
            document.getElementById('detailJamMengajar').textContent = (data.jam_mengajar || 0) + ' jam';
            document.getElementById('detailJamWajib').textContent = (data.jam_wajib || 0) + ' jam';
            
            // Cek override rule untuk jam wajib
            if (data.has_override && data.override_rules.some(rule => rule.includes('Jam wajib'))) {
                document.getElementById('detailJamWajibCustom').style.display = 'inline';
            } else {
                document.getElementById('detailJamWajibCustom').style.display = 'none';
            }
            
            document.getElementById('detailJamHonor').textContent = (jamHonor || 0) + ' jam';
            document.getElementById('detailHonorPerJam').textContent = formatRupiah(data.honor_per_jam || 0);
            document.getElementById('detailTotalHonor').textContent = formatRupiah(totalHonor);
            
            // Override Rules
            const overrideCard = document.getElementById('overrideRulesCard');
            const overrideList = document.getElementById('overrideRulesList');
            
            if (data.has_override && data.override_rules && data.override_rules.length > 0) {
                overrideCard.style.display = 'block';
                let overrideHtml = '';
                data.override_rules.forEach(rule => {
                    overrideHtml += `
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-check-circle text-warning me-2"></i>
                            <span>${rule}</span>
                        </div>
                    `;
                });
                overrideList.innerHTML = overrideHtml;
            } else {
                overrideCard.style.display = 'none';
            }
            
            // Summary
            const totalTHP = parseFloat(data.gaji_pokok || 0) + parseFloat(data.tunjangan_jabatan || 0) + subtotalKeluarga + totalHonor;
            document.getElementById('summaryGajiPokok').textContent = formatRupiah(data.gaji_pokok);
            document.getElementById('summaryTunjanganJabatan').textContent = formatRupiah(data.tunjangan_jabatan);
            document.getElementById('summaryTunjanganKeluarga').textContent = formatRupiah(subtotalKeluarga);
            document.getElementById('summaryHonor').textContent = formatRupiah(totalHonor);
            document.getElementById('summaryTHP').textContent = formatRupiah(totalTHP);
            document.getElementById('detailTotalTHP').textContent = formatRupiah(totalTHP);
            
            console.log('Modal populated successfully');
        }
        
        function printGajiDetail() {
            // Get current data from the last fetch
            const detailPegawaiNama = document.getElementById('detailPegawaiNama').textContent;
            
            // Get penugasan ID from the last showGajiDetail call
            if (window.currentPenugasanId) {
                // Open slip gaji in new window
                const slipWindow = window.open(`slip_gaji.php?penugasan_id=${window.currentPenugasanId}`, '_blank', 
                    'width=800,height=600,scrollbars=yes,resizable=yes');
                
                if (!slipWindow) {
                    alert('Pop-up diblokir. Silakan izinkan pop-up untuk mencetak slip gaji.');
                }
            } else {
                alert('Tidak ada data untuk dicetak. Silakan buka detail gaji terlebih dahulu.');
            }
        }
        
        // Auto-format gaji pokok input
        document.addEventListener('DOMContentLoaded', function() {
            const gajiPokokInput = document.getElementById('modalGajiPokok');
            const jumlahAnakInput = document.getElementById('modalJumlahAnak');
            const jamMengajarInput = document.getElementById('modalJamMengajar');
            
            if (gajiPokokInput) {
                // Perbaikan: Menggunakan titik sebagai pemisah ribuan
                function formatNumberWithDots(num) {
                    if (num === null || num === undefined || isNaN(num)) return '';
                    const numStr = num.toString().replace(/[^\d]/g, '');
                    return numStr.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                }

                // Perbaikan: Menghapus semua karakter non-digit untuk mendapatkan nilai murni
                function getRawNumber(value) {
                    return parseInt(value.replace(/[^\d]/g, '')) || 0;
                }
                
                // Auto-format as user types
                gajiPokokInput.addEventListener('input', function(e) {
                    if (e.target.disabled) return;
                    
                    let value = e.target.value;
                    let rawValue = getRawNumber(value);
                    
                    if (rawValue) {
                        e.target.value = formatNumberWithDots(rawValue);
                    } else {
                        e.target.value = '';
                    }
                    
                    updateRealtimeCalculation();
                });
                
                // Validate on blur
                gajiPokokInput.addEventListener('blur', function(e) {
                    if (e.target.disabled) return;
                    
                    let value = e.target.value;
                    let rawValue = getRawNumber(value);
                    
                    if (rawValue > 0 && rawValue < 100000) {
                        alert('Gaji pokok minimal Rp 100.000');
                        e.target.focus();
                    } else if (rawValue > 0) {
                         e.target.value = formatNumberWithDots(rawValue);
                    }
                });
            }
            
            if (jumlahAnakInput) {
                jumlahAnakInput.addEventListener('input', updateRealtimeCalculation);
                jumlahAnakInput.addEventListener('change', updateRealtimeCalculation);
            }
            
            if (jamMengajarInput) {
                jamMengajarInput.addEventListener('input', updateRealtimeCalculation);
                jamMengajarInput.addEventListener('change', updateRealtimeCalculation);
            }
            
            document.querySelectorAll('.jabatan-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateRealtimeCalculation);
            });
        });
        
        // Function to update real-time calculation preview
        function updateRealtimeCalculation() {
            const pegawaiId = document.getElementById('modalPegawaiId').value;
            const gajiPokokInput = document.getElementById('modalGajiPokok');
            const gajiPokokRaw = gajiPokokInput.value;
            const jumlahAnak = parseInt(document.getElementById('modalJumlahAnak').value) || 0;
            const jamMengajar = parseInt(document.getElementById('modalJamMengajar').value) || 0;
            
            const gajiPokok = parseInt(gajiPokokRaw.replace(/[^\d]/g, '')) || 0;
            
            const isNoGajiPokokStatus = gajiPokokInput.disabled;
            
            const selectedJabatan = [];
            document.querySelectorAll('.jabatan-checkbox:checked').forEach(checkbox => {
                selectedJabatan.push(checkbox.value);
            });
            
            if (!isNoGajiPokokStatus && gajiPokok > 0 && gajiPokok < 100000) {
                clearCalculationPreview();
                return;
            }
            
            const previewContainer = document.getElementById('calculationPreview');
            if (previewContainer) {
                previewContainer.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Menghitung...</div>';
                previewContainer.style.display = 'block';
            }
            
            const params = new URLSearchParams({
                action: 'calculate_preview',
                pegawai_id: pegawaiId,
                gaji_pokok: gajiPokok,
                jumlah_anak: jumlahAnak,
                jam_mengajar: jamMengajar,
                jabatan_ids: selectedJabatan.join(','),
                _nocache: Date.now(),
                _rand: Math.random()
            });
            
            fetch('get_calculation_preview.php?' + params.toString(), {
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showCalculationPreview(data.data);
                    } else {
                        console.error('Preview calculation error:', data.message);
                        clearCalculationPreview();
                    }
                })
                .catch(error => {
                    console.error('Error fetching calculation preview:', error);
                    clearCalculationPreview();
                });
        }
        
        function showCalculationPreview(data) {
            const previewContainer = document.getElementById('calculationPreview');
            if (!previewContainer) return;
            
            const formatRupiah = (amount) => 'Rp ' + new Intl.NumberFormat('id-ID').format(amount || 0);
            
            const html = `
                <div class="card mt-3">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-calculator"></i> Preview Perhitungan</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="preview-item d-flex justify-content-between align-items-center">
                                    <span class="preview-label">Gaji Pokok:</span>
                                    <span class="preview-value text-primary">${formatRupiah(data.gaji_pokok)}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="preview-item d-flex justify-content-between align-items-center">
                                    <span class="preview-label">Tunjangan Jabatan:</span>
                                    <span class="preview-value text-warning">${formatRupiah(data.tunjangan_jabatan)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="preview-item d-flex justify-content-between align-items-center">
                                    <span class="preview-label">Tunj. Keluarga:</span>
                                    <span class="preview-value text-success">${formatRupiah(data.tunjangan_keluarga)}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="preview-item d-flex justify-content-between align-items-center">
                                    <span class="preview-label">Tunj. Anak:</span>
                                    <span class="preview-value text-info">${formatRupiah(data.tunjangan_anak)}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="preview-item d-flex justify-content-between align-items-center">
                                    <span class="preview-label">Tunj. Beras:</span>
                                    <span class="preview-value text-warning">${formatRupiah(data.tunjangan_beras)}</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="preview-item d-flex justify-content-between align-items-center">
                                    <span class="preview-label">Honor Mengajar:</span>
                                    <span class="preview-value text-secondary">${formatRupiah(data.honor)}</span>
                                </div>
                            </div>
                        </div>
                        ${data.has_override ? `
                            <div class="row mt-3">
                                <div class="col-12">
                                    <div class="alert alert-warning py-2 mb-0">
                                        <small><i class="fas fa-exclamation-triangle me-1"></i><strong>Aturan Khusus:</strong><br>
                                        ${data.override_rules ? data.override_rules.join('<br>') : 'Ada aturan khusus yang diterapkan'}</small>
                                    </div>
                                </div>
                            </div>
                        ` : ''}
                        <hr class="my-3">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                                    <span class="fw-bold text-dark fs-6">Total THP:</span>
                                    <h5 class="text-success mb-0 fw-bold">${formatRupiah(data.total)}</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            previewContainer.innerHTML = html;
            previewContainer.style.display = 'block';
        }
        
        function clearCalculationPreview() {
            const previewContainer = document.getElementById('calculationPreview');
            if (previewContainer) {
                previewContainer.style.display = 'none';
                previewContainer.innerHTML = '';
            }
        }
        
        // Force refresh function for cache issues
        function forceRefreshData() {
            console.log('Force refreshing all data...');
            
            if ('caches' in window) {
                caches.keys().then(names => {
                    names.forEach(name => {
                        caches.delete(name);
                    });
                });
            }
            
            const url = new URL(window.location);
            url.searchParams.set('_refresh', Date.now());
            window.location.href = url.toString();
        }
        
        window.addEventListener('load', function() {
            const refreshBtn = document.createElement('button');
            refreshBtn.innerHTML = '🔄 Force Refresh';
            refreshBtn.className = 'btn btn-sm btn-outline-warning position-fixed';
            refreshBtn.style.cssText = 'top: 10px; right: 10px; z-index: 9999; font-size: 11px;';
            refreshBtn.title = 'Klik jika data masih menampilkan cache lama';
            refreshBtn.onclick = forceRefreshData;
            document.body.appendChild(refreshBtn);
            
            console.log('Override system loaded. Time:', new Date().toLocaleString());
        });

        // Export to Excel function
        function exportToExcel() {
            const currentUrl = new URL(window.location);
            const exportUrl = new URL('export_penugasan_excel.php', window.location.origin + window.location.pathname.replace(/[^/]*$/, ''));
            
            // Copy current filter parameters
            exportUrl.searchParams.set('unit_id', currentUrl.searchParams.get('unit_id') || '');
            exportUrl.searchParams.set('tahun_pelajaran', currentUrl.searchParams.get('tahun_pelajaran') || '');
            exportUrl.searchParams.set('status_kepegawaian', currentUrl.searchParams.get('status_kepegawaian') || '');
            
            const jabatanFilter = currentUrl.searchParams.getAll('jabatan_ids_filter[]');
            jabatanFilter.forEach(id => {
                exportUrl.searchParams.append('jabatan_ids_filter[]', id);
            });
            
            // Open export in new tab
            window.open(exportUrl.toString(), '_blank');
        }
    </script>
</body>
</html>