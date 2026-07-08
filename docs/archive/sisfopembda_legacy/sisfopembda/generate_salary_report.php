<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'override_functions.php';

// Check if action is provided
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'preview') {
    generatePreview();
} else {
    echo "<div style='text-align: center; padding: 50px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; margin: 20px; border-radius: 5px;'>";
    echo "<h2>❌ Export Functions Disabled</h2>";
    echo "<p>Export functions (Excel, PDF, CSV) have been temporarily disabled.</p>";
    echo "<p>Only preview is currently available.</p>";
    echo "<p><a href='laporan_gaji.php' style='color: #721c24; text-decoration: underline;'>← Back to Reports</a></p>";
    echo "</div>";
}

function generatePreview() {
    global $conn;
    
    $unit_id = $_POST['unit_id'] ?? '';
    $tahun_pelajaran = $_POST['tahun_pelajaran'] ?? '';
    $show_details = isset($_POST['show_details']) && $_POST['show_details'] == '1';
    $group_by_unit = isset($_POST['group_by_unit']) && $_POST['group_by_unit'] == '1';
    
    if (empty($tahun_pelajaran)) {
        echo "<p style='color: red;'>Error: Tahun pelajaran harus dipilih.</p>";
        return;
    }
    
    // Query untuk mengambil data
    $whereClause = "pen.tahun_pelajaran = '$tahun_pelajaran'";
    if (!empty($unit_id)) {
        $whereClause .= " AND pen.unit_id = '$unit_id'";
    }
    
    $query = "
    SELECT 
        pen.id,
        pen.pegawai_id,
        peg.nama,
        peg.nip,
        u.nama as unit_nama,
        j.nama as jabatan_nama,
        pen.jam_wajib,
        pen.jam_mengajar,
        pen.honor,
        pen.jam_honor,
        pen.tunjangan_keluarga,
        pen.tunjangan_anak,
        pen.tunjangan_beras,
        pen.tunjangan_jabatan,
        pen.total
    FROM penugasan pen
    JOIN pegawai peg ON pen.pegawai_id = peg.id
    JOIN unit u ON pen.unit_id = u.id
    JOIN jabatan j ON peg.jabatan_id = j.id
    WHERE $whereClause
    ORDER BY u.nama, peg.nama
    ";
    
    $result = mysqli_query($conn, $query);
    
    if (!$result) {
        echo "<p style='color: red;'>Error: " . mysqli_error($conn) . "</p>";
        return;
    }
    
    echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; font-weight: bold; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .table-responsive { overflow-x: auto; }
    .unit-header { background-color: #e9ecef; font-weight: bold; }
    .total-row { background-color: #f8f9fa; font-weight: bold; }
    .grand-total { background-color: #dee2e6; font-weight: bold; font-size: 1.1em; }
    .disabled-notice { 
        color: #721c24; 
        text-align: center; 
        padding: 15px; 
        font-weight: bold; 
        background-color: #f8d7da; 
        margin: 10px 0; 
        border: 1px solid #f5c6cb; 
        border-radius: 5px;
    }
    </style>";
    
    echo "<div class='disabled-notice'>📋 PREVIEW MODE ONLY - Export functions disabled</div>";
    echo "<h2>Preview Laporan Gaji - $tahun_pelajaran</h2>";
    
    if ($group_by_unit) {
        generateGroupedPreview($result, $show_details);
    } else {
        generateFlatPreview($result, $show_details);
    }
    
    echo "<div style='margin-top: 30px; text-align: center; padding: 20px; background-color: #e9ecef; border-radius: 5px;'>";
    echo "<p><strong>Note:</strong> This is preview mode only. Export functions are temporarily disabled.</p>";
    echo "<p><a href='laporan_gaji.php' style='padding: 10px 20px; background-color: #007bff; color: white; text-decoration: none; border-radius: 3px;'>← Kembali ke Laporan</a></p>";
    echo "</div>";
}

function generateGroupedPreview($result, $show_details) {
    $groupedData = [];
    
    // Group data by unit
    while ($row = mysqli_fetch_assoc($result)) {
        $groupedData[$row['unit_nama']][] = $row;
    }
    
    $grandTotalHonor = 0;
    $grandTotalTunjanganKeluarga = 0;
    $grandTotalTunjanganAnak = 0;
    $grandTotalTunjanganBeras = 0;
    $grandTotalTunjanganJabatan = 0;
    $grandTotalGaji = 0;
    
    foreach ($groupedData as $unitName => $data) {
        echo "<h3>Unit: $unitName</h3>";
        
        echo "<div class='table-responsive'>";
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>No</th>";
        echo "<th>Nama</th>";
        echo "<th>NIP</th>";
        echo "<th>Jabatan</th>";
        if ($show_details) {
            echo "<th>Tunjangan Jabatan</th>";
            echo "<th>Tunjangan Keluarga</th>";
            echo "<th>Tunjangan Anak</th>";
            echo "<th>Tunjangan Beras</th>";
        }
        echo "<th>Jam Mengajar</th>";
        echo "<th>Jam Wajib</th>";
        echo "<th>Jam Honor</th>";
        echo "<th>Honor</th>";
        echo "<th>Total Gaji</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        $no = 1;
        $unitTotalHonor = 0;
        $unitTotalTunjanganKeluarga = 0;
        $unitTotalTunjanganAnak = 0;
        $unitTotalTunjanganBeras = 0;
        $unitTotalTunjanganJabatan = 0;
        $unitTotalGaji = 0;
        
        foreach ($data as $row) {
            $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];
            
            $unitTotalHonor += $row['honor'];
            $unitTotalTunjanganKeluarga += $row['tunjangan_keluarga'];
            $unitTotalTunjanganAnak += $row['tunjangan_anak'];
            $unitTotalTunjanganBeras += $row['tunjangan_beras'];
            $unitTotalTunjanganJabatan += $tunjanganJabatanTotal;
            $unitTotalGaji += $row['total'];
            
            echo "<tr>";
            echo "<td>$no</td>";
            echo "<td>{$row['nama']}</td>";
            echo "<td>{$row['nip']}</td>";
            echo "<td>{$row['jabatan_nama']}</td>";
            if ($show_details) {
                echo "<td class='text-right'>" . formatRupiah($tunjanganJabatanTotal) . "</td>";
                echo "<td class='text-right'>" . formatRupiah($row['tunjangan_keluarga']) . "</td>";
                echo "<td class='text-right'>" . formatRupiah($row['tunjangan_anak']) . "</td>";
                echo "<td class='text-right'>" . formatRupiah($row['tunjangan_beras']) . "</td>";
            }
            echo "<td class='text-center'>{$row['jam_mengajar']}</td>";
            echo "<td class='text-center'>{$row['jam_wajib']}</td>";
            echo "<td class='text-center'>{$row['jam_honor']}</td>";
            echo "<td class='text-right'>" . formatRupiah($row['honor']) . "</td>";
            echo "<td class='text-right'>" . formatRupiah($row['total']) . "</td>";
            echo "</tr>";
            $no++;
        }
        
        // Unit totals
        echo "<tr class='total-row'>";
        echo "<td colspan='" . ($show_details ? 4 : 4) . "'><strong>Subtotal $unitName</strong></td>";
        if ($show_details) {
            echo "<td class='text-right'><strong>" . formatRupiah($unitTotalTunjanganJabatan) . "</strong></td>";
            echo "<td class='text-right'><strong>" . formatRupiah($unitTotalTunjanganKeluarga) . "</strong></td>";
            echo "<td class='text-right'><strong>" . formatRupiah($unitTotalTunjanganAnak) . "</strong></td>";
            echo "<td class='text-right'><strong>" . formatRupiah($unitTotalTunjanganBeras) . "</strong></td>";
        }
        echo "<td colspan='3'></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($unitTotalHonor) . "</strong></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($unitTotalGaji) . "</strong></td>";
        echo "</tr>";
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
        
        $grandTotalHonor += $unitTotalHonor;
        $grandTotalTunjanganKeluarga += $unitTotalTunjanganKeluarga;
        $grandTotalTunjanganAnak += $unitTotalTunjanganAnak;
        $grandTotalTunjanganBeras += $unitTotalTunjanganBeras;
        $grandTotalTunjanganJabatan += $unitTotalTunjanganJabatan;
        $grandTotalGaji += $unitTotalGaji;
    }
    
    // Grand totals
    echo "<div class='table-responsive'>";
    echo "<table>";
    echo "<tr class='grand-total'>";
    echo "<td colspan='" . ($show_details ? 4 : 4) . "'><strong>GRAND TOTAL</strong></td>";
    if ($show_details) {
        echo "<td class='text-right'><strong>" . formatRupiah($grandTotalTunjanganJabatan) . "</strong></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($grandTotalTunjanganKeluarga) . "</strong></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($grandTotalTunjanganAnak) . "</strong></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($grandTotalTunjanganBeras) . "</strong></td>";
    }
    echo "<td colspan='3'></td>";
    echo "<td class='text-right'><strong>" . formatRupiah($grandTotalHonor) . "</strong></td>";
    echo "<td class='text-right'><strong>" . formatRupiah($grandTotalGaji) . "</strong></td>";
    echo "</tr>";
    echo "</table>";
    echo "</div>";
}

function generateFlatPreview($result, $show_details) {
    echo "<div class='table-responsive'>";
    echo "<table>";
    echo "<thead>";
    echo "<tr>";
    echo "<th>No</th>";
    echo "<th>Nama</th>";
    echo "<th>NIP</th>";
    echo "<th>Unit</th>";
    echo "<th>Jabatan</th>";
    if ($show_details) {
        echo "<th>Tunjangan Jabatan</th>";
        echo "<th>Tunjangan Keluarga</th>";
        echo "<th>Tunjangan Anak</th>";
        echo "<th>Tunjangan Beras</th>";
    }
    echo "<th>Jam Mengajar</th>";
    echo "<th>Jam Wajib</th>";
    echo "<th>Jam Honor</th>";
    echo "<th>Honor</th>";
    echo "<th>Total Gaji</th>";
    echo "</tr>";
    echo "</thead>";
    echo "<tbody>";
    
    $no = 1;
    $totalHonor = 0;
    $totalTunjanganKeluarga = 0;
    $totalTunjanganAnak = 0;
    $totalTunjanganBeras = 0;
    $totalTunjanganJabatan = 0;
    $totalGaji = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];
        
        $totalHonor += $row['honor'];
        $totalTunjanganKeluarga += $row['tunjangan_keluarga'];
        $totalTunjanganAnak += $row['tunjangan_anak'];
        $totalTunjanganBeras += $row['tunjangan_beras'];
        $totalTunjanganJabatan += $tunjanganJabatanTotal;
        $totalGaji += $row['total'];
        
        echo "<tr>";
        echo "<td>$no</td>";
        echo "<td>{$row['nama']}</td>";
        echo "<td>{$row['nip']}</td>";
        echo "<td>{$row['unit_nama']}</td>";
        echo "<td>{$row['jabatan_nama']}</td>";
        if ($show_details) {
            echo "<td class='text-right'>" . formatRupiah($tunjanganJabatanTotal) . "</td>";
            echo "<td class='text-right'>" . formatRupiah($row['tunjangan_keluarga']) . "</td>";
            echo "<td class='text-right'>" . formatRupiah($row['tunjangan_anak']) . "</td>";
            echo "<td class='text-right'>" . formatRupiah($row['tunjangan_beras']) . "</td>";
        }
        echo "<td class='text-center'>{$row['jam_mengajar']}</td>";
        echo "<td class='text-center'>{$row['jam_wajib']}</td>";
        echo "<td class='text-center'>{$row['jam_honor']}</td>";
        echo "<td class='text-right'>" . formatRupiah($row['honor']) . "</td>";
        echo "<td class='text-right'>" . formatRupiah($row['total']) . "</td>";
        echo "</tr>";
        $no++;
    }
    
    // Total row
    echo "<tr class='total-row'>";
    echo "<td colspan='" . ($show_details ? 5 : 5) . "'><strong>TOTAL</strong></td>";
    if ($show_details) {
        echo "<td class='text-right'><strong>" . formatRupiah($totalTunjanganJabatan) . "</strong></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($totalTunjanganKeluarga) . "</strong></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($totalTunjanganAnak) . "</strong></td>";
        echo "<td class='text-right'><strong>" . formatRupiah($totalTunjanganBeras) . "</strong></td>";
    }
    echo "<td colspan='3'></td>";
    echo "<td class='text-right'><strong>" . formatRupiah($totalHonor) . "</strong></td>";
    echo "<td class='text-right'><strong>" . formatRupiah($totalGaji) . "</strong></td>";
    echo "</tr>";
    
    echo "</tbody>";
    echo "</table>";
    echo "</div>";
}
?>
    
    // Simple format that Excel can understand - no special spacing
    return "Rp " . number_format($angka, 0, ',', '.');
}

// Helper function for calculating tunjangan jabatan (centralized)
function calculateTunjanganJabatan($jabatan_list, $pdo) {
    if (empty($jabatan_list)) return 0;
    
    $jabatanArr = array_map('trim', explode(',', $jabatan_list));
    $tunjanganJabatanTotal = 0;
    
    foreach ($jabatanArr as $j) {
        if (empty($j)) continue;
        
        // TEMPORARY FIX: Hardcode known problematic values
        if ($j === 'PKS') {
            $tunjanganJabatanTotal += 750000;
            continue;
        }
        if ($j === 'Wali Kelas') {
            $tunjanganJabatanTotal += 250000;
            continue;
        }
        
        $stmt = $pdo->prepare("SELECT tunjangan_jabatan FROM jabatan WHERE nama = ? LIMIT 1");
        $stmt->execute([$j]);
        $res = $stmt->fetch();
        
        if ($res && isset($res['tunjangan_jabatan'])) {
            $rawValue = $res['tunjangan_jabatan'];
            $tunjanganValue = floatval($rawValue);
            
            // Check if value seems to be in wrong scale (like 750.00 instead of 750000)
            if ($tunjanganValue > 0 && $tunjanganValue < 1000) {
                // Likely stored as thousands, multiply by 1000
                $tunjanganValue = $tunjanganValue * 1000;
            }
            
            $tunjanganJabatanTotal += $tunjanganValue;
            
            // Debug logging
            error_log("DEBUG: $j => raw:$rawValue => final:$tunjanganValue");
        }
    }
    
    return $tunjanganJabatanTotal;
}

// Helper function for jabatan ordering
function getJabatanOrderClause($alias = 'j') {
    return "
        CASE 
            WHEN {$alias}.nama LIKE 'Kasek%' OR {$alias}.nama LIKE 'Kepala Sekolah%' THEN 1
            WHEN {$alias}.nama LIKE 'Wakasek%' OR {$alias}.nama LIKE 'Wakil Kepala Sekolah%' THEN 2
            WHEN {$alias}.nama LIKE 'PKS%' THEN 3
            WHEN {$alias}.nama LIKE 'KTU%' OR {$alias}.nama LIKE 'Kepala Tata Usaha%' THEN 4
            WHEN {$alias}.nama LIKE 'Kapro%' OR {$alias}.nama LIKE 'Kepala Program%' THEN 5
            WHEN {$alias}.nama LIKE 'Wali Kelas%' OR {$alias}.nama LIKE 'Walikelas%' THEN 6
            ELSE 7
        END
    ";
}

// Helper function for status ordering
function getStatusOrderClause($alias = 'peg') {
    return "
        CASE {$alias}.status_kepegawaian
            WHEN 'PNS' THEN 1
            WHEN 'GTY' THEN 2
            WHEN 'Honorer' THEN 3
            WHEN 'PTY' THEN 4
            ELSE 5
        END
    ";
}

// Check authentication
$auth = new Auth($pdo);
$auth->requireLogin();

$action = $_GET['action'] ?? 'preview';
$tahun_pelajaran = $_GET['tahun_pelajaran'] ?? '';
$unit_id = $_GET['unit_id'] ?? '';
$status_kepegawaian = $_GET['status_kepegawaian'] ?? '';
$show_details = ($_GET['show_details'] ?? '0') === '1';
$group_by_unit = ($_GET['group_by_unit'] ?? '0') === '1';

if (!$tahun_pelajaran) {
    die('Tahun pelajaran harus dipilih');
}

// Build WHERE conditions
$whereConditions = ["pen.tahun_pelajaran = ?"];
$params = [$tahun_pelajaran];

if ($unit_id) {
    $whereConditions[] = "pen.unit_id = ?";
    $params[] = $unit_id;
}

if ($status_kepegawaian) {
    $whereConditions[] = "peg.status_kepegawaian = ?";
    $params[] = $status_kepegawaian;
}

$whereClause = implode(' AND ', $whereConditions);

// Build ORDER BY clause with custom hierarchy
$jabatanOrder = getJabatanOrderClause();
$statusOrder = getStatusOrderClause();

if ($group_by_unit) {
    $orderBy = "u.nama, jabatan_priority, {$statusOrder}, peg.nama";
} else {
    $orderBy = "jabatan_priority, {$statusOrder}, peg.nama";
}

// Get salary data
$sql = "
    SELECT 
        pen.id as penugasan_id,
        peg.id as pegawai_id,
        peg.nama as pegawai_nama,
        peg.status_kepegawaian,
        peg.status_perkawinan,
        peg.jumlah_anak,
        peg.gaji_pokok,
        u.nama as unit_nama,
        pen.jam_mengajar,
        pen.jam_wajib,
        CASE 
            WHEN pen.jam_wajib >= pen.jam_mengajar THEN 0
            ELSE (pen.jam_mengajar - pen.jam_wajib)
        END as jam_honor_calculated,
        pen.jam_honor,
        pen.honor,
        pen.tunjangan_keluarga,
        pen.tunjangan_anak,
        pen.tunjangan_beras,
        pen.tunjangan_jabatan,
        pen.total,
        pen.tahun_pelajaran,
        GROUP_CONCAT(j.nama ORDER BY {$jabatanOrder} SEPARATOR ', ') as jabatan_list,
        MIN({$jabatanOrder}) as jabatan_priority
    FROM penugasan pen
    JOIN pegawai peg ON pen.pegawai_id = peg.id
    JOIN unit u ON pen.unit_id = u.id
    LEFT JOIN penugasan_jabatan pj ON pen.id = pj.penugasan_id
    LEFT JOIN jabatan j ON pj.jabatan_id = j.id
    WHERE {$whereClause}
    GROUP BY pen.id
    ORDER BY {$orderBy}
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$salaryData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Apply override rules to data

// Group by unit if requested
$groupedData = [];
if ($group_by_unit) {
    foreach ($salaryData as $row) {
        $unitName = $row['unit_nama'];
        if (!isset($groupedData[$unitName])) {
            $groupedData[$unitName] = [];
        }
        $groupedData[$unitName][] = $row;
    }
} else {
    $groupedData['Semua Unit'] = $salaryData;
}

// Handle different actions
switch ($action) {
    case 'preview':
        generatePreview($groupedData, $show_details, $group_by_unit, $tahun_pelajaran);
        break;
    case 'excel':
        generateExcel($groupedData, $show_details, $group_by_unit, $tahun_pelajaran, $unit_id);
        break;
    case 'csv':
        generateCSV($groupedData, $show_details, $group_by_unit, $tahun_pelajaran, $unit_id);
        break;
    case 'pdf':
        generatePDF($groupedData, $show_details, $group_by_unit, $tahun_pelajaran, $unit_id);
        break;
    default:
        die('Invalid action');
}

function generatePreview($groupedData, $show_details, $group_by_unit, $tahun_pelajaran) {
    echo '<style>
    .currency-cell {
        position: relative;
        width: 140px;
        padding: 8px 12px !important;
        text-align: left !important;
    }
    .currency-cell span {
        display: inline-block;
        width: 100%;
    }
    .currency-cell span:first-child {
        width: 25px;
        text-align: left;
    }
    .currency-cell span:last-child {
        width: calc(100% - 25px);
        text-align: right;
    }
    </style>';
    echo '<div class="table-responsive">';
    
    $totalGajiPokok = 0;
    $totalTunjanganJabatan = 0;
    $totalTunjanganKeluarga = 0;
    $totalTunjanganAnak = 0;
    $totalTunjanganBeras = 0;
    $totalHonor = 0;
    $totalGrandTotal = 0;
    $totalPegawai = 0;
    $totalJamMengajar = 0;
    $totalJamWajib = 0;
    $totalJamHonor = 0;
    
    foreach ($groupedData as $unitName => $data) {
        if ($group_by_unit && count($groupedData) > 1) {
            echo '<h6 class="mt-4 mb-3 text-primary"><i class="fas fa-building me-2"></i>' . htmlspecialchars($unitName) . '</h6>';
        }
        
        echo '<table class="table table-bordered table-striped salary-table">';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 50px;">No</th>';
        echo '<th>Nama Pegawai</th>';
        echo '<th>Status</th>';
        echo '<th>Jabatan</th>';
        
        if ($show_details) {
            echo '<th>Tunj. Jabatan</th>';
            echo '<th>Gaji Pokok</th>';
        }
        
        echo '<th>Jam Mengajar</th>';
        echo '<th>Jam Wajib</th>';
        echo '<th>Jam Honor</th>';
        
        if ($show_details) {
            echo '<th>Honor</th>';
            echo '<th>Tunj. Keluarga</th>';
            echo '<th>Tunj. Anak</th>';
            echo '<th>Tunj. Beras</th>';
        }
        echo '<th>Total Gaji</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $no = 1;
        $unitTotalGajiPokok = 0;
        $unitTotalTunjanganJabatan = 0;
        $unitTotalTunjanganKeluarga = 0;
        $unitTotalTunjanganAnak = 0;
        $unitTotalTunjanganBeras = 0;
        $unitTotalHonor = 0;
        $unitTotalGaji = 0;
        $unitTotalJamMengajar = 0;
        $unitTotalJamWajib = 0;
        $unitTotalJamHonor = 0;
        
    global $pdo;
        foreach ($data as $row) {
            // FIXED: Use SAME source for preview and export - from penugasan table
            // Do NOT recalculate, use what's already in the database
            $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];

            $recalculatedTotal = (float)$row['gaji_pokok'] +
                                 $tunjanganJabatanTotal +
                                 (float)$row['honor'] +
                                 (float)$row['tunjangan_keluarga'] +
                                 (float)$row['tunjangan_anak'] +
                                 (float)$row['tunjangan_beras'];

            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($row['pegawai_nama']) . '</td>';
            echo '<td>' . htmlspecialchars($row['status_kepegawaian']) . '</td>';
            echo '<td>' . htmlspecialchars($row['jabatan_list'] ?: '-') . '</td>';
            
            if ($show_details) {
                echo '<td class="text-end currency-cell">' . formatRupiah($tunjanganJabatanTotal) . '</td>';
                echo '<td class="text-end currency-cell">' . formatRupiah($row['gaji_pokok']) . '</td>';
            }
            
            echo '<td class="text-center">' . $row['jam_mengajar'] . '</td>';
            echo '<td class="text-center">' . $row['jam_wajib'] . '</td>';
            echo '<td class="text-center">' . $row['jam_honor_calculated'] . '</td>';
            
            if ($show_details) {
                echo '<td class="text-end currency-cell">' . formatRupiah($row['honor']) . '</td>';
                echo '<td class="text-end currency-cell">' . formatRupiah($row['tunjangan_keluarga']) . '</td>';
                echo '<td class="text-end currency-cell">' . formatRupiah($row['tunjangan_anak']) . '</td>';
                echo '<td class="text-end currency-cell">' . formatRupiah($row['tunjangan_beras']) . '</td>';
            }
            
            echo '<td class="text-end fw-bold currency-cell">' . formatRupiah($recalculatedTotal) . '</td>';
            echo '</tr>';
            
            // Add to unit totals using correct values
            $unitTotalGajiPokok += $row['gaji_pokok'];
            $unitTotalTunjanganJabatan += $tunjanganJabatanTotal;
            $unitTotalTunjanganKeluarga += $row['tunjangan_keluarga'];
            $unitTotalTunjanganAnak += $row['tunjangan_anak'];
            $unitTotalTunjanganBeras += $row['tunjangan_beras'];
            $unitTotalHonor += $row['honor'];
            $unitTotalGaji += $recalculatedTotal;
            $unitTotalJamMengajar += $row['jam_mengajar'];
            $unitTotalJamWajib += $row['jam_wajib'];
            $unitTotalJamHonor += $row['jam_honor_calculated'];
            $totalPegawai++;
        }
        
        // Unit total row
        if ($group_by_unit && count($data) > 1) {
            echo '<tr class="total-row">';
            echo '<td colspan="4"><strong>Total ' . htmlspecialchars($unitName) . ' (' . count($data) . ' pegawai)</strong></td>';
            
            if ($show_details) {
                echo '<td class="text-end currency-cell"><strong>' . formatRupiah($unitTotalTunjanganJabatan) . '</strong></td>';
                echo '<td class="text-end currency-cell"><strong>' . formatRupiah($unitTotalGajiPokok) . '</strong></td>';
            }
            
            echo '<td class="text-center"><strong>' . $unitTotalJamMengajar . '</strong></td>';
            echo '<td class="text-center"><strong>' . $unitTotalJamWajib . '</strong></td>';
            echo '<td class="text-center"><strong>' . $unitTotalJamHonor . '</strong></td>';
            
            if ($show_details) {
                echo '<td class="text-end currency-cell"><strong>' . formatRupiah($unitTotalHonor) . '</strong></td>';
                echo '<td class="text-end currency-cell"><strong>' . formatRupiah($unitTotalTunjanganKeluarga) . '</strong></td>';
                echo '<td class="text-end currency-cell"><strong>' . formatRupiah($unitTotalTunjanganAnak) . '</strong></td>';
                echo '<td class="text-end currency-cell"><strong>' . formatRupiah($unitTotalTunjanganBeras) . '</strong></td>';
            }
            
            echo '<td class="text-end currency-cell"><strong>' . formatRupiah($unitTotalGaji) . '</strong></td>';
            echo '</tr>';
        }
        
        echo '</tbody>';
        echo '</table>';
        
        // Add to grand totals
        $totalGajiPokok += $unitTotalGajiPokok;
        $totalTunjanganJabatan += $unitTotalTunjanganJabatan;
        $totalTunjanganKeluarga += $unitTotalTunjanganKeluarga;
        $totalTunjanganAnak += $unitTotalTunjanganAnak;
        $totalTunjanganBeras += $unitTotalTunjanganBeras;
        $totalHonor += $unitTotalHonor;
        $totalGrandTotal += $unitTotalGaji;
        $totalJamMengajar += $unitTotalJamMengajar;
        $totalJamWajib += $unitTotalJamWajib;
        $totalJamHonor += $unitTotalJamHonor;
    }
    
    echo '</div>';
    echo '</div>';
}

function generateExcel($groupedData, $show_details, $group_by_unit, $tahun_pelajaran, $unit_id) {
    global $pdo;
    
    // Get unit name for display and filename
    $unitNameDisplay = 'Semua Unit';
    $unitName = 'Semua_Unit';
    if ($unit_id) {
        $stmt = $pdo->prepare("SELECT nama FROM unit WHERE id = ?");
        $stmt->execute([$unit_id]);
        $unit = $stmt->fetch();
        if ($unit) {
            $unitNameDisplay = $unit['nama'];
            $unitName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $unit['nama']);
        }
    }
    
    $filename = "Daftar_Gaji_{$unitName}_{$tahun_pelajaran}_" . date('Ymd_His') . ".xls";
    
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Use simple HTML table format for Excel compatibility with proper styling
    echo '<html><head>';
    echo '<meta charset="UTF-8">';
    echo '<style>';
    echo 'table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }';
    echo 'td, th { border: 1px solid black; padding: 5px; vertical-align: top; }';
    echo '.currency { text-align: right; font-family: "Courier New", monospace; }';
    echo '.center { text-align: center; }';
    echo '.header { background-color: #cccccc; font-weight: bold; text-align: center; }';
    echo '.total-row { background-color: #ffffcc; font-weight: bold; }';
    echo '</style>';
    echo '</head><body>';
    
    $totalCols = $show_details ? 14 : 8;
    echo '<table cellpadding="5" cellspacing="0" style="width:100%; margin-left:auto; margin-right:auto;">';
    echo '<tr><td colspan="' . $totalCols . '" style="border:none; text-align:center; font-size:20pt; font-weight:bold;">Daftar Gaji Guru dan Pegawai</td></tr>';
    echo '<tr><td colspan="' . $totalCols . '" style="border:none; text-align:center; font-size:22pt; font-weight:bold;">Yayasan Perguruan Pembda Nias</td></tr>';
    echo '<tr><td colspan="' . $totalCols . '" style="border:none; text-align:center; font-size:16pt; font-weight:bold;">' . htmlspecialchars($unitNameDisplay) . '</td></tr>';
    echo '<tr><td colspan="' . $totalCols . '" style="border:none; text-align:center; font-size:16pt; font-weight:bold;">TP ' . htmlspecialchars($tahun_pelajaran) . '</td></tr>';
    echo '<tr><td colspan="' . $totalCols . '" style="border:none;">&nbsp;</td></tr>';
    echo '</table>';
    
    foreach ($groupedData as $unitName => $data) {
        echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;width:100%;">';
        
        if ($group_by_unit && count($groupedData) > 1) {
            echo '<tr>';
            echo '<td colspan="' . $totalCols . '" style="font-weight: bold; background-color: #eeeeee;">Unit: ' . htmlspecialchars($unitName) . '</td>';
            echo '</tr>';
        }
        
        // Table headers
        echo '<tr>';
        echo '<td class="header">No</td>';
        echo '<td class="header">Nama Pegawai</td>';
        echo '<td class="header">Status</td>';
        echo '<td class="header">Jabatan</td>';
        
        if ($show_details) {
            echo '<td class="header">Tunj. Jabatan</td>';
            echo '<td class="header">Gaji Pokok</td>';
        }
        
        echo '<td class="header">Jam Mengajar</td>';
        echo '<td class="header">Jam Wajib</td>';
        echo '<td class="header">Jam Honor</td>';
        
        if ($show_details) {
            echo '<td class="header">Honor</td>';
            echo '<td class="header">Tunj. Keluarga</td>';
            echo '<td class="header">Tunj. Anak</td>';
            echo '<td class="header">Tunj. Beras</td>';
        }
        echo '<td class="header">Total Gaji</td>';
        echo '</tr>';
        
        $no = 1;
    $no = 1;
        foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($row['pegawai_nama']) . '</td>';
            echo '<td>' . htmlspecialchars($row['status_kepegawaian']) . '</td>';
            echo '<td>' . htmlspecialchars($row['jabatan_list'] ?: '-') . '</td>';
            
            if ($show_details) {
                // FIXED: Use SAME source as preview - from penugasan table
                $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];
                echo '<td class="currency">' . formatRupiahExport($tunjanganJabatanTotal) . '</td>';
                echo '<td class="currency">' . formatRupiahExport($row['gaji_pokok']) . '</td>';
            }
            
            echo '<td class="center">' . $row['jam_mengajar'] . '</td>';
            echo '<td class="center">' . $row['jam_wajib'] . '</td>';
            echo '<td class="center">' . $row['jam_honor_calculated'] . '</td>';
            
            if ($show_details) {
                echo '<td class="currency">' . formatRupiahExport($row['honor']) . '</td>';
                echo '<td class="currency">' . formatRupiahExport($row['tunjangan_keluarga']) . '</td>';
                echo '<td class="currency">' . formatRupiahExport($row['tunjangan_anak']) . '</td>';
                echo '<td class="currency">' . formatRupiahExport($row['tunjangan_beras']) . '</td>';
            }
            
            $recalculatedTotal = (float)$row['gaji_pokok'] +
                                 $tunjanganJabatanTotal +
                                 (float)$row['honor'] +
                                 (float)$row['tunjangan_keluarga'] +
                                 (float)$row['tunjangan_anak'] +
                                 (float)$row['tunjangan_beras'];

            echo '<td class="currency" style="font-weight:bold; background-color: #ffff99;">' . formatRupiahExport($recalculatedTotal) . '</td>';
            echo '</tr>';
        }
        
        // Calculate totals using SAME source as preview/export
        $unitTotalJamMengajar = 0;
        $unitTotalJamWajib = 0;
        $unitTotalJamHonor = 0;
        $unitTotalGajiPokok = 0;
        $unitTotalTunjanganJabatan = 0;
        $unitTotalHonor = 0;
        $unitTotalTunjanganKeluarga = 0;
        $unitTotalTunjanganAnak = 0;
        $unitTotalTunjanganBeras = 0;
        $unitTotalGaji = 0;
        
        foreach ($data as $row) {
            // Use penugasan.tunjangan_jabatan directly for consistency
            $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];

            $recalculatedTotal = (float)$row['gaji_pokok'] +
                                 $tunjanganJabatanTotal +
                                 (float)$row['honor'] +
                                 (float)$row['tunjangan_keluarga'] +
                                 (float)$row['tunjangan_anak'] +
                                 (float)$row['tunjangan_beras'];

            $unitTotalJamMengajar += $row['jam_mengajar'];
            $unitTotalJamWajib += $row['jam_wajib'];
            $unitTotalJamHonor += $row['jam_honor_calculated'];
            $unitTotalGajiPokok += $row['gaji_pokok'];
            $unitTotalTunjanganJabatan += $tunjanganJabatanTotal;
            $unitTotalHonor += $row['honor'];
            $unitTotalTunjanganKeluarga += $row['tunjangan_keluarga'];
            $unitTotalTunjanganAnak += $row['tunjangan_anak'];
            $unitTotalTunjanganBeras += $row['tunjangan_beras'];
            $unitTotalGaji += $recalculatedTotal;
        }
        
        // Baris rekapitulasi dalam tabel
        echo '<tr class="total-row">';
        echo '<td class="center">-</td>';
        echo '<td>TOTAL</td>';
        echo '<td class="center">' . count($data) . ' org</td>';
        echo '<td>-</td>';
        
        if ($show_details) {
            echo '<td class="currency">' . formatRupiahExport($unitTotalTunjanganJabatan) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($unitTotalGajiPokok) . '</td>';
        }
        
        echo '<td class="center">' . $unitTotalJamMengajar . '</td>';
        echo '<td class="center">' . $unitTotalJamWajib . '</td>';
        echo '<td class="center">' . $unitTotalJamHonor . '</td>';
        
        if ($show_details) {
            echo '<td class="currency">' . formatRupiahExport($unitTotalHonor) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($unitTotalTunjanganKeluarga) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($unitTotalTunjanganAnak) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($unitTotalTunjanganBeras) . '</td>';
        }
        
        echo '<td class="currency" style="background-color:#28a745;color:white;font-weight:bold;">' . formatRupiahExport($unitTotalGaji) . '</td>';
        echo '</tr>';
        
        echo '</table>';
        echo '<br>';
    }
    
    // Grand Total untuk semua unit (jika ada lebih dari 1 unit)
    if (count($groupedData) > 1) {
        // Hitung grand total dari semua unit
        $grandTotalJamMengajar = 0;
        $grandTotalJamWajib = 0; 
        $grandTotalJamHonor = 0;
        $grandTotalGajiPokok = 0;
        $grandTotalTunjanganJabatan = 0;
        $grandTotalHonor = 0;
        $grandTotalTunjanganKeluarga = 0;
        $grandTotalTunjanganAnak = 0;
        $grandTotalTunjanganBeras = 0;
        $grandTotalGaji = 0;
        $grandTotalPegawai = 0;
        
        foreach ($groupedData as $unitName => $data) {
            foreach ($data as $row) {
                // Use penugasan.tunjangan_jabatan directly for GRAND TOTAL consistency
                $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];

                $recalculatedTotal = (float)$row['gaji_pokok'] +
                                     $tunjanganJabatanTotal +
                                     (float)$row['honor'] +
                                     (float)$row['tunjangan_keluarga'] +
                                     (float)$row['tunjangan_anak'] +
                                     (float)$row['tunjangan_beras'];

                $grandTotalJamMengajar += $row['jam_mengajar'];
                $grandTotalJamWajib += $row['jam_wajib'];
                $grandTotalJamHonor += $row['jam_honor_calculated'];
                $grandTotalGajiPokok += $row['gaji_pokok'];
                $grandTotalTunjanganJabatan += $tunjanganJabatanTotal;
                $grandTotalHonor += $row['honor'];
                $grandTotalTunjanganKeluarga += $row['tunjangan_keluarga'];
                $grandTotalTunjanganAnak += $row['tunjangan_anak'];
                $grandTotalTunjanganBeras += $row['tunjangan_beras'];
                $grandTotalGaji += $recalculatedTotal;
                $grandTotalPegawai++;
            }
        }
        
        // Tampilkan Grand Total
        echo '<table border="1" cellpadding="5" cellspacing="0" style="border-collapse:collapse;width:100%;margin-top:20px;">';
        echo '<tr style="background-color:#dc3545;color:white;border:3px solid #000;font-weight:bold;font-size:14pt;">';
        echo '<td class="center">-</td>';
        echo '<td>GRAND TOTAL KESELURUHAN</td>';
        echo '<td class="center">' . $grandTotalPegawai . ' org</td>';
        echo '<td>-</td>';
        
        if ($show_details) {
            echo '<td class="currency">' . formatRupiahExport($grandTotalTunjanganJabatan) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($grandTotalGajiPokok) . '</td>';
        }
        
        echo '<td class="center">' . $grandTotalJamMengajar . '</td>';
        echo '<td class="center">' . $grandTotalJamWajib . '</td>';
        echo '<td class="center">' . $grandTotalJamHonor . '</td>';
        
        if ($show_details) {
            echo '<td class="currency">' . formatRupiahExport($grandTotalHonor) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($grandTotalTunjanganKeluarga) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($grandTotalTunjanganAnak) . '</td>';
            echo '<td class="currency">' . formatRupiahExport($grandTotalTunjanganBeras) . '</td>';
        }
        
        echo '<td class="currency" style="background-color:#ffc107;color:#000;font-size:16pt;font-weight:bold;">' . formatRupiahExport($grandTotalGaji) . '</td>';
        echo '</tr>';
        echo '</table>';
    }
    
    // Footer informasi
    echo '<br><br>';
    echo '<table cellpadding="5" cellspacing="0" style="width:100%;">';
    echo '<tr><td colspan="' . $totalCols . '" style="border:none;font-size:10pt;color:#666;">';
    echo 'Laporan digenerate pada: ' . date('d/m/Y H:i:s') . ' | ';
    echo 'Total Pegawai: ' . ($grandTotalPegawai ?? array_sum(array_map('count', $groupedData))) . ' orang | ';
    echo 'System: SISFOPEMBDA';
    echo '</td></tr>';
    echo '</table>';
    
    echo '</body>';
    echo '</html>';
}

function generatePDF($groupedData, $show_details, $group_by_unit, $tahun_pelajaran, $unit_id) {
    global $pdo;
    
    // Get unit name
    $unitName = 'Semua Unit';
    if ($unit_id) {
        $stmt = $pdo->prepare("SELECT nama FROM unit WHERE id = ?");
        $stmt->execute([$unit_id]);
        $unit = $stmt->fetch();
        if ($unit) {
            $unitName = $unit['nama'];
        }
    }
    
    // For simplicity, we'll generate HTML that can be printed as PDF
    header('Content-Type: text/html; charset=UTF-8');
    
    echo '<!DOCTYPE html>';
    echo '<html><head>';
    echo '<meta charset="UTF-8">';
    echo '<title>Daftar Gaji Guru dan Pegawai</title>';
    echo '<style>';
    echo 'body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }';
    echo 'table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }';
    echo 'th, td { border: 1px solid #000; padding: 5px; text-align: left; }';
    echo 'th { background-color: #f0f0f0; font-weight: bold; }';
    echo '.text-center { text-align: center; }';
    echo '.text-right { text-align: right; }';
    echo '.header { text-align: center; margin-bottom: 30px; }';
    echo '.total-row { background-color: #ffffcc; font-weight: bold; }';
    echo '@media print { body { margin: 0; } }';
    echo '</style>';
    echo '</head><body>';
    
    echo '<div class="header">';
    echo '<h2>DAFTAR GAJI PEGAWAI</h2>';
    echo '<h3>' . htmlspecialchars($unitName) . '</h3>';
    echo '<p>Tahun Pelajaran: ' . htmlspecialchars($tahun_pelajaran) . '</p>';
    echo '<p>Dicetak tanggal: ' . date('d F Y H:i:s') . '</p>';
    echo '</div>';
    
    $totalGrandTotal = 0;
    $totalPegawai = 0;
    
    foreach ($groupedData as $unitNameGroup => $data) {
        if ($group_by_unit && count($groupedData) > 1) {
            echo '<h4>' . htmlspecialchars($unitNameGroup) . '</h4>';
        }
        
        echo '<table>';
        echo '<thead>';
        echo '<tr>';
        echo '<th style="width: 30px;">No</th>';
        echo '<th>Nama Pegawai</th>';
        echo '<th>Status</th>';
        echo '<th>Jabatan</th>';
        if ($show_details) {
            echo '<th>Tunj. Jabatan</th>';
            echo '<th>Gaji Pokok</th>';
        }
        echo '<th>Jam Mengajar</th>';
        echo '<th>Jam Wajib</th>';
        echo '<th>Jam Honor</th>';
        if ($show_details) {
            echo '<th>Honor</th>';
            echo '<th>Tunj. Keluarga</th>';
            echo '<th>Tunj. Anak</th>';
            echo '<th>Tunj. Beras</th>';
        }
        echo '<th>Total Gaji</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';
        
        $no = 1;
        $unitTotal = 0;
        
        // Initialize unit totals for each column
        $unitTotalJamMengajar = 0;
        $unitTotalJamWajib = 0;
        $unitTotalJamHonor = 0;
        $unitTotalGajiPokok = 0;
        $unitTotalTunjanganJabatan = 0;
        $unitTotalHonor = 0;
        $unitTotalTunjanganKeluarga = 0;
        $unitTotalTunjanganAnak = 0;
        $unitTotalTunjanganBeras = 0;
        $unitTotalGaji = 0;
        
        foreach ($data as $row) {
            echo '<tr>';
            echo '<td>' . $no++ . '</td>';
            echo '<td>' . htmlspecialchars($row['pegawai_nama']) . '</td>';
            echo '<td>' . htmlspecialchars($row['status_kepegawaian']) . '</td>';
            echo '<td>' . htmlspecialchars($row['jabatan_list'] ?: '-') . '</td>';
            
            if ($show_details) {
                // Use penugasan.tunjangan_jabatan directly for PDF consistency
                $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];
                echo '<td class="text-right">' . formatRupiahExport($tunjanganJabatanTotal) . '</td>';
                echo '<td class="text-right">' . formatRupiahExport($row['gaji_pokok']) . '</td>';
            }
            
            echo '<td class="text-center">' . $row['jam_mengajar'] . '</td>';
            echo '<td class="text-center">' . $row['jam_wajib'] . '</td>';
            echo '<td class="text-center">' . $row['jam_honor_calculated'] . '</td>';
            
            if ($show_details) {
                echo '<td class="text-right">' . formatRupiahExport($row['honor']) . '</td>';
                echo '<td class="text-right">' . formatRupiahExport($row['tunjangan_keluarga']) . '</td>';
                echo '<td class="text-right">' . formatRupiahExport($row['tunjangan_anak']) . '</td>';
                echo '<td class="text-right">' . formatRupiahExport($row['tunjangan_beras']) . '</td>';
            }
            
            $recalculatedTotal = (float)$row['gaji_pokok'] +
                                 $tunjanganJabatanTotal +
                                 (float)$row['honor'] +
                                 (float)$row['tunjangan_keluarga'] +
                                 (float)$row['tunjangan_anak'] +
                                 (float)$row['tunjangan_beras'];
            
            echo '<td class="text-right">' . formatRupiahExport($recalculatedTotal) . '</td>';
            echo '</tr>';
            
            // Add to unit totals - using recalculated values
            $unitTotalJamMengajar += $row['jam_mengajar'];
            $unitTotalJamWajib += $row['jam_wajib'];
            $unitTotalJamHonor += $row['jam_honor_calculated'];
            $unitTotalGajiPokok += $row['gaji_pokok'];
            $unitTotalTunjanganJabatan += $tunjanganJabatanTotal;
            $unitTotalHonor += $row['honor'];
            $unitTotalTunjanganKeluarga += $row['tunjangan_keluarga'];
            $unitTotalTunjanganAnak += $row['tunjangan_anak'];
            $unitTotalTunjanganBeras += $row['tunjangan_beras'];
            $unitTotalGaji += $recalculatedTotal;
            
            $unitTotal += $recalculatedTotal;
            $totalPegawai++;
        }
        
        // Add total row for this unit
        echo '<tr style="background-color:#f8f9fa;border-top:2px solid #000;font-weight:bold;">';
        echo '<td style="text-align:center;">-</td>';
        echo '<td>TOTAL</td>';
        echo '<td style="text-align:center;">' . count($data) . ' org</td>';
        echo '<td>-</td>';
        
        if ($show_details) {
            echo '<td class="text-right">' . formatRupiahExport($unitTotalTunjanganJabatan) . '</td>';
            echo '<td class="text-right">' . formatRupiahExport($unitTotalGajiPokok) . '</td>';
        }
        
        echo '<td style="text-align:center;">' . $unitTotalJamMengajar . '</td>';
        echo '<td style="text-align:center;">' . $unitTotalJamWajib . '</td>';
        echo '<td style="text-align:center;">' . $unitTotalJamHonor . '</td>';
        
        if ($show_details) {
            echo '<td class="text-right">' . formatRupiahExport($unitTotalHonor) . '</td>';
            echo '<td class="text-right">' . formatRupiahExport($unitTotalTunjanganKeluarga) . '</td>';
            echo '<td class="text-right">' . formatRupiahExport($unitTotalTunjanganAnak) . '</td>';
            echo '<td class="text-right">' . formatRupiahExport($unitTotalTunjanganBeras) . '</td>';
        }
        
        echo '<td class="text-right" style="background-color:#28a745;color:white;">' . formatRupiahExport($unitTotalGaji) . '</td>';
        echo '</tr>';
        
        echo '</tbody>';
        echo '</table>';
        
        $totalGrandTotal += $unitTotal;
    }
    
    echo '<div style="margin-top: 30px; border: 2px solid #000; padding: 15px;">';
    echo '<h4>RINGKASAN TOTAL</h4>';
    echo '<p><strong>Total Pegawai:</strong> ' . $totalPegawai . ' orang</p>';
    echo '<p><strong>Total Gaji Keseluruhan:</strong> Rp ' . number_format($totalGrandTotal, 0, ',', '.') . '</p>';
    echo '</div>';
    
    echo '<script>window.print();</script>';
    echo '</body></html>';
}

function generateCSV($groupedData, $show_details, $group_by_unit, $tahun_pelajaran, $unit_id) {
    global $pdo;
    
    // Get unit name for filename
    $unitName = 'Semua_Unit';
    if ($unit_id) {
        $stmt = $pdo->prepare("SELECT nama FROM unit WHERE id = ?");
        $stmt->execute([$unit_id]);
        $unit = $stmt->fetch();
        if ($unit) {
            $unitName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $unit['nama']);
        }
    }
    
    $filename = "Daftar_Gaji_{$unitName}_{$tahun_pelajaran}_" . date('Ymd_His') . ".csv";
    
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    
    // Add BOM for UTF-8
    echo "\xEF\xBB\xBF";
    
    $output = fopen('php://output', 'w');
    
    // Header rows
    fputcsv($output, ['DAFTAR GAJI PEGAWAI']);
    fputcsv($output, ['Tahun Pelajaran: ' . $tahun_pelajaran]);
    fputcsv($output, []);
    
    foreach ($groupedData as $unitName => $data) {
        if ($group_by_unit && count($groupedData) > 1) {
            fputcsv($output, ['Unit: ' . $unitName]);
        }
        
        // Table headers
        $headers = ['No', 'Nama Pegawai', 'Status', 'Jabatan'];
        if ($show_details) {
            $headers = array_merge($headers, ['Tunj. Jabatan', 'Gaji Pokok']);
        }
        $headers = array_merge($headers, ['Jam Mengajar', 'Jam Wajib', 'Jam Honor']);
        if ($show_details) {
            $headers = array_merge($headers, ['Honor', 'Tunj. Keluarga', 'Tunj. Anak', 'Tunj. Beras']);
        }
        $headers[] = 'Total Gaji';
        fputcsv($output, $headers);
        
        $no = 1;
        $unitTotal = 0;
        
        foreach ($data as $row) {
            $csvRow = [
                $no++,
                $row['pegawai_nama'],
                $row['status_kepegawaian'],
                $row['jabatan_list'] ?: '-'
            ];
            if ($show_details) {
                // Use penugasan.tunjangan_jabatan directly for CSV consistency
                $tunjanganJabatanTotal = (float)$row['tunjangan_jabatan'];
                $csvRow = array_merge($csvRow, [
                    formatRupiahExport($tunjanganJabatanTotal),
                    formatRupiahExport($row['gaji_pokok'])
                ]);
            }
            $csvRow = array_merge($csvRow, [
                $row['jam_mengajar'],
                $row['jam_wajib'],
                $row['jam_honor_calculated']
            ]);
            if ($show_details) {
                $csvRow = array_merge($csvRow, [
                    formatRupiahExport($row['honor']),
                    formatRupiahExport($row['tunjangan_keluarga']),
                    formatRupiahExport($row['tunjangan_anak']),
                    formatRupiahExport($row['tunjangan_beras'])
                ]);
            }
            
            $recalculatedTotal = (float)$row['gaji_pokok'] +
                                 $tunjanganJabatanTotal +
                                 (float)$row['honor'] +
                                 (float)$row['tunjangan_keluarga'] +
                                 (float)$row['tunjangan_anak'] +
                                 (float)$row['tunjangan_beras'];
            
            $csvRow[] = formatRupiahExport($recalculatedTotal);
            fputcsv($output, $csvRow);
            $unitTotal += $recalculatedTotal;
        }
        
        if (count($data) > 1) {
            $totalRow = array_fill(0, count($headers) - 1, '');
            $totalRow[0] = 'Total ' . $unitName . ' (' . count($data) . ' pegawai)';
            $totalRow[count($headers) - 1] = formatRupiahExport($unitTotal);
            fputcsv($output, $totalRow);
        }
        
        fputcsv($output, []);
    }
    
    fclose($output);
}
?>
