<?php
require_once 'config.php';
require_once 'auth.php';
require_once 'override_functions.php';
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Check authentication and permission
$auth = new Auth($pdo);
$auth->requireLogin();
$auth->requirePermission('penugasan');

// Get filter values (same as input_penugasan.php)
$unit_id = isset($_GET['unit_id']) ? (int)$_GET['unit_id'] : 0;
$tahun_pelajaran = isset($_GET['tahun_pelajaran']) ? $_GET['tahun_pelajaran'] : '2025/2026';
$status_kepegawaian = isset($_GET['status_kepegawaian']) ? $_GET['status_kepegawaian'] : '';
$jabatan_ids_filter = isset($_GET['jabatan_ids_filter']) ? $_GET['jabatan_ids_filter'] : [];

// Initialize pegawais array
$pegawais = [];

// Step 1: Ambil daftar pegawai_id dari tabel penugasan.
// Jika unit_id = 0 berarti mode "Semua Unit" -> hilangkan filter unit.
if ($unit_id === 0) {
    $sql_penugasan_ids = "SELECT DISTINCT pegawai_id FROM penugasan WHERE tahun_pelajaran = ?";
    $stmt_ids = $pdo->prepare($sql_penugasan_ids);
    $stmt_ids->execute([$tahun_pelajaran]);
} else {
    $sql_penugasan_ids = "SELECT DISTINCT pegawai_id FROM penugasan WHERE unit_id = ? AND tahun_pelajaran = ?";
    $stmt_ids = $pdo->prepare($sql_penugasan_ids);
    $stmt_ids->execute([$unit_id, $tahun_pelajaran]);
}
$pegawai_ids = $stmt_ids->fetchAll(PDO::FETCH_COLUMN);

if (empty($pegawai_ids)) {
    // No employees with assignments, exit gracefully by creating an empty Excel file with a message.
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Data Penugasan');
    $sheet->setCellValue('A1', 'Tidak ada data penugasan untuk filter yang dipilih.');
    
    $writer = new Xlsx($spreadsheet);
    $filename = 'Data_Penugasan_Kosong_' . date('Y-m-d') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer->save('php://output');
    exit;
}

// Step 2: Get pegawai data based on the list of IDs from penugasan and other filters.
$where = [];
$params = [];

// Main condition is the list of IDs from penugasan
$placeholders = implode(',', array_fill(0, count($pegawai_ids), '?'));
$where[] = "p.id IN ($placeholders)";
$params = array_merge($params, $pegawai_ids);

// Add other filters if they are set
if ($status_kepegawaian) {
    $where[] = "p.status_kepegawaian = ?";
    $params[] = $status_kepegawaian;
}

// Step 3: Get ALL pegawai details for the filtered list
$sql_pegawai = "SELECT 
            p.id as pegawai_id,
            p.nomor_induk,
            p.nama as pegawai_nama,
            p.status_kepegawaian,
            p.status_perkawinan,
            p.kelompok_pekerjaan,
            p.jumlah_anak,
            p.gaji_pokok,
            p.unit_id
        FROM pegawai p
        WHERE " . implode(' AND ', $where) . "
        ORDER BY p.id";

$stmt = $pdo->prepare($sql_pegawai);
$stmt->execute($params);
$pegawais = $stmt->fetchAll(PDO::FETCH_ASSOC); // Use FETCH_ASSOC for consistency
// NOTE: Dulu ada deduplikasi berdasarkan nama. Itu dihapus karena riskan bila ada dua orang berbeda dengan nama sama.
//       Identitas unik harus berdasarkan pegawai_id; jika ada duplikasi nama di DB, perbaiki di sumber data.

// --- Prefetch Jabatan Priority Map (untuk optimasi sorting, hindari query dalam usort) ---
$priorityMap = [];
$penugasanIds = array_filter(array_column($pegawais, 'penugasan_id'));// might be empty at this stage; will be filled after step 4 if needed
// penugasan_id belum diisi sampai Step 4; jadi priorityMap final akan dihitung ulang sesudah Step 4.
// Kita akan lakukan prefetch kedua kali tepat sebelum sorting setelah penugasan_id diisi.


// Step 4: For each pegawai, get their unit & penugasan representative record
foreach ($pegawais as &$pegawai) {
    // Dapatkan 1 record penugasan representatif:
    // - Jika spesifik unit: ambil penugasan di unit tsb (ORDER BY id DESC)
    // - Jika semua unit: ambil penugasan dengan total terbesar (prioritas) lalu id DESC
    if ($unit_id === 0) {
        $penugasan_sql = "SELECT 
                id as penugasan_id,
                unit_id,
                jam_mengajar,
                jam_wajib,
                jam_honor,
                honor,
                tunjangan_keluarga,
                tunjangan_anak,
                tunjangan_beras,
                tunjangan_jabatan,
                total
            FROM penugasan
            WHERE pegawai_id = ? AND tahun_pelajaran = ?
            ORDER BY total DESC, id DESC
            LIMIT 1";
        $penugasan_stmt = $pdo->prepare($penugasan_sql);
        $penugasan_stmt->execute([$pegawai['pegawai_id'], $tahun_pelajaran]);
    } else {
        $penugasan_sql = "SELECT 
                id as penugasan_id,
                unit_id,
                jam_mengajar,
                jam_wajib,
                jam_honor,
                honor,
                tunjangan_keluarga,
                tunjangan_anak,
                tunjangan_beras,
                tunjangan_jabatan,
                total
            FROM penugasan
            WHERE pegawai_id = ? AND unit_id = ? AND tahun_pelajaran = ?
            ORDER BY id DESC
            LIMIT 1";
        $penugasan_stmt = $pdo->prepare($penugasan_sql);
        $penugasan_stmt->execute([$pegawai['pegawai_id'], $unit_id, $tahun_pelajaran]);
    }
    $penugasan_data = $penugasan_stmt->fetch(PDO::FETCH_ASSOC);

    // Tentukan unit_nama:
    if ($unit_id === 0) {
        // Jika mode semua unit dan penugasan_data punya unit_id gunakan itu; kalau tidak ada pakai home unit pegawai
        $target_unit_id = $penugasan_data['unit_id'] ?? $pegawai['unit_id'] ?? null;
    } else {
        $target_unit_id = $unit_id; // sudah difilter spesifik
    }
    if ($target_unit_id) {
        $unit_stmt = $pdo->prepare("SELECT nama as unit_nama FROM unit WHERE id = ?");
        $unit_stmt->execute([$target_unit_id]);
        $unit_row = $unit_stmt->fetch();
        $pegawai['unit_nama'] = $unit_row ? $unit_row['unit_nama'] : '-';
    } else {
        $pegawai['unit_nama'] = '-';
    }
    
    if ($penugasan_data) {
        // Merge penugasan data into the main pegawai array
        $pegawai = array_merge($pegawai, $penugasan_data);
        // Pastikan unit_id di row pegawai mencerminkan unit penugasan representatif (agar perhitungan jam_honor benar)
        if (!empty($penugasan_data['unit_id'])) {
            $pegawai['unit_id'] = $penugasan_data['unit_id'];
        }
        // Prefetch jabatan_priority langsung dari tabel jabatan.prioritas bila ada
        try {
            $prioStmtSingle = $pdo->prepare("SELECT MIN(j.prioritas) pr FROM penugasan_jabatan pj JOIN jabatan j ON pj.jabatan_id=j.id WHERE pj.penugasan_id = ?");
            $prioStmtSingle->execute([$pegawai['penugasan_id']]);
            $rowPr = $prioStmtSingle->fetch(PDO::FETCH_ASSOC);
            if ($rowPr && $rowPr['pr'] !== null) {
                $pegawai['jabatan_priority'] = (int)$rowPr['pr'];
            }
        } catch (Throwable $e) {
            // Kolom prioritas mungkin belum ada, akan fallback nanti via pola nama
        }
    } else {
        // Ensure keys exist to prevent errors later
        $pegawai['penugasan_id'] = null;
        $pegawai['jam_mengajar'] = 0;
        $pegawai['jam_wajib'] = 0;
        $pegawai['jam_honor'] = 0;
        $pegawai['honor'] = 0;
        $pegawai['tunjangan_keluarga'] = 0;
        $pegawai['tunjangan_anak'] = 0;
        $pegawai['tunjangan_beras'] = 0;
        $pegawai['tunjangan_jabatan'] = 0;
        $pegawai['total'] = 0;
    }
}
unset($pegawai); // Unset reference

// Filter by Jabatan if specified
if (!empty($jabatan_ids_filter) && is_array($jabatan_ids_filter)) {
    $pegawais_filtered_by_jabatan = [];
    foreach ($pegawais as $pegawai) {
        if (isset($pegawai['penugasan_id'])) {
            $sql_check_jabatan = "SELECT 1 FROM penugasan_jabatan WHERE penugasan_id = ? AND jabatan_id IN (" . implode(',', array_fill(0, count($jabatan_ids_filter), '?')) . ") LIMIT 1";
            $stmt_check_jabatan = $pdo->prepare($sql_check_jabatan);
            $params_check = array_merge([$pegawai['penugasan_id']], $jabatan_ids_filter);
            $stmt_check_jabatan->execute($params_check);
            if ($stmt_check_jabatan->fetchColumn()) {
                $pegawais_filtered_by_jabatan[] = $pegawai;
            }
        }
    }
    $pegawais = $pegawais_filtered_by_jabatan; // Replace with filtered list
}

// (Sorting ditunda hingga setelah jabatan priority dihitung di loop jabatan di bawah)


// --- Spreadsheet Generation --- // Ensure $pegawais is always an array
if (!is_array($pegawais)) {
    $pegawais = [];
}

// Get jabatan data for each pegawai & build list of penugasan IDs for priority prefetch
$collectPenugasanIds = [];
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
                    WHEN j.nama LIKE 'Wakasek%' OR j.nama LIKE 'Wakil %' THEN 2
                    WHEN j.nama LIKE 'PKS%' THEN 3
                    WHEN j.nama LIKE 'Wali %' THEN 4
                    WHEN j.nama LIKE 'KTU%' THEN 5
                    WHEN j.nama LIKE 'Kapro%' OR j.nama LIKE 'Kepala Program%' THEN 6
                    ELSE 7
                END, j.nama
        ");
        $jabatanStmt->execute([$pegawai['penugasan_id']]);
        $jabatanList = $jabatanStmt->fetchAll();
    $collectPenugasanIds[] = $pegawai['penugasan_id'];
        
        $pegawai['jabatan_names'] = implode(', ', array_column($jabatanList, 'nama'));
        $pegawai['jabatan_list'] = $jabatanList;
        
    // Calculate jabatan_priority dari pola nama HANYA jika belum diisi dari DB sebelumnya
        if (!isset($pegawai['jabatan_priority']) || $pegawai['jabatan_priority'] === null) {
            if (!empty($jabatanList)) {
                $priorities = array_map(function($j) {
                    // Gunakan hanya kata/prefix pertama; deteksi berbasis awal string
                    $n = trim($j['nama']);
                    $lower = strtolower($n);
                    if (preg_match('/^(kasek|kepala sekolah)/i', $n)) return 1;
                    if (preg_match('/^(wakasek|wakil )/i', $n)) return 2;
                    if (preg_match('/^pks/i', $n)) return 3;
                    if (preg_match('/^wali /i', $n)) return 4; // Wali Kelas
                    if (preg_match('/^ktu/i', $n)) return 5;
                    if (preg_match('/^(kapro|kepala program)/i', $n)) return 6;
                    return 7; // lainnya
                }, $jabatanList);
                $pegawai['jabatan_priority'] = min($priorities);
            } else {
                $pegawai['jabatan_priority'] = 7;
            }
        }
        
        // Recalculate honor berdasarkan unit dan jam_honor yang benar
        if ($pegawai['penugasan_id'] && $pegawai['unit_id']) {
            $jamHonorStmt = $pdo->prepare("
                SELECT jam_wajib, honor_per_jam 
                FROM jam_honor 
                WHERE unit_id = ? AND status_kepegawaian = ?
            ");
            $jamHonorStmt->execute([$pegawai['unit_id'], $pegawai['status_kepegawaian']]);
            $jamHonorData = $jamHonorStmt->fetch();
            
            if ($jamHonorData) {
                // Apply override rules (per-pegawai) when available so export matches recalc logic
                $overrideRules = [];
                try {
                    $overrideRules = getOverrideRules($pdo, $pegawai['pegawai_id']);
                } catch (Throwable $e) {
                    // ignore if helper not available or error
                }

                // Determine jam_wajib allowing override
                $default_jam_wajib = $jamHonorData['jam_wajib'];
                if (function_exists('getCustomJamWajib')) {
                    $jam_wajib_correct = getCustomJamWajib($overrideRules, $default_jam_wajib);
                } else {
                    $jam_wajib_correct = $default_jam_wajib;
                }

                $honor_per_jam_correct = $jamHonorData['honor_per_jam'];

                // Check override that disables honor calculation
                $skip_honor = false;
                foreach ($overrideRules as $rule) {
                    if (!empty($rule['is_active']) && ($rule['rule_type'] ?? '') === 'no_honor_calculation') {
                        $skip_honor = true;
                        break;
                    }
                }

                if ($skip_honor) {
                    $jam_honor_correct = 0;
                    $honor_correct = 0;
                } else {
                    $jam_honor_correct = max(0, $pegawai['jam_mengajar'] - $jam_wajib_correct);
                    $honor_correct = $jam_honor_correct * $honor_per_jam_correct;
                }

                // Update data pegawai dengan nilai yang benar
                $pegawai['jam_wajib'] = $jam_wajib_correct;
                $pegawai['jam_honor'] = $jam_honor_correct;
                $pegawai['honor'] = $honor_correct;
                $pegawai['honor_per_jam'] = $honor_per_jam_correct;
            }
        }
        // Recompute total (gaji_pokok + honor + tunjangan-tunjangan) agar konsisten bila honor berubah
        $pegawai['total'] = ($pegawai['gaji_pokok'] ?? 0)
            + ($pegawai['honor'] ?? 0)
            + ($pegawai['tunjangan_keluarga'] ?? 0)
            + ($pegawai['tunjangan_anak'] ?? 0)
            + ($pegawai['tunjangan_beras'] ?? 0)
            + ($pegawai['tunjangan_jabatan'] ?? 0);
    } else {
        $pegawai['jabatan_names'] = '';
        $pegawai['jabatan_list'] = [];
        // Set jabatan_priority if not set in main query
        if (!isset($pegawai['jabatan_priority']) || $pegawai['jabatan_priority'] === null) {
            $pegawai['jabatan_priority'] = 7;
        }
        // Ensure total is still coherent (all zero)
        $pegawai['total'] = ($pegawai['gaji_pokok'] ?? 0)
            + ($pegawai['honor'] ?? 0)
            + ($pegawai['tunjangan_keluarga'] ?? 0)
            + ($pegawai['tunjangan_anak'] ?? 0)
            + ($pegawai['tunjangan_beras'] ?? 0)
            + ($pegawai['tunjangan_jabatan'] ?? 0);
    }
}
unset($pegawai);

// (Prefetch priority via kolom prioritas dihilangkan: sekarang seluruh penentuan prioritas murni dari prefix nama jabatan)

// Final sort hanya berdasarkan prioritas jabatan (permintaan revisi user)
// Daftar prioritas (prefix awal nama jabatan):
// 1 Kasek / Kepala Sekolah
// 2 Wakasek / Wakil ...
// 3 PKS
// 4 KTU
// 5 Kapro / Kepala Program
// 6 Wali Kelas / Walikelas
// 7 Bendahara
// 8 Kordinator / Koordinator
// 9 lainnya
usort($pegawais, function($a, $b) {
    $mapPriority = function($row) {
        if (!empty($row['jabatan_list'])) {
            $min = 99;
            foreach ($row['jabatan_list'] as $j) {
                $n = strtolower(trim($j['nama']));
                $p = 9; // default 'lainnya'
                if (preg_match('/^(kasek|kepala sekolah)/i', $n)) $p = 1; else
                if (preg_match('/^(wakasek|wakil )/i', $n)) $p = 2; else
                if (preg_match('/^pks\b/i', $n)) $p = 3; else
                if (preg_match('/^ktu\b/i', $n)) $p = 4; else
                if (preg_match('/^(kapro|kepala program)/i', $n)) $p = 5; else
                if (preg_match('/^wali( |kelas)/i', $n)) $p = 6; else
                if (preg_match('/^bendahara/i', $n)) $p = 7; else
                if (preg_match('/^(kordinator|koordinator)/i', $n)) $p = 8; // tetap di atas 'lainnya'
                if ($p < $min) $min = $p;
            }
            return $min;
        }
        // Tidak punya jabatan sama sekali: letakkan PALING BAWAH (lebih besar dari 9)
        return 10;
    };
    $pa = $mapPriority($a);
    $pb = $mapPriority($b);
    if ($pa !== $pb) return $pa <=> $pb;
    // Tie-break kedua: status kepegawaian (PNS, GTY, PTY, Honorer; lainnya setelah)
    $statusOrder = [ 'PNS' => 1, 'GTY' => 2, 'PTY' => 3, 'Honorer' => 4 ];
    $sa = $statusOrder[$a['status_kepegawaian']] ?? 99;
    $sb = $statusOrder[$b['status_kepegawaian']] ?? 99;
    if ($sa !== $sb) return $sa <=> $sb;
    // Jika sama-sama Honorer: kelompokkan Pendidik lebih dulu, lalu Non Kependidikan.
    // Dalam masing-masing kelompok, urutkan THP (total) desc.
    if (($a['status_kepegawaian'] ?? '') === 'Honorer' && ($b['status_kepegawaian'] ?? '') === 'Honorer') {
        $groupA = $a['kelompok_pekerjaan'] ?? '';
        $groupB = $b['kelompok_pekerjaan'] ?? '';
        $rank = function($g){
            if ($g === 'Pendidik') return 0; // paling atas dalam Honorer
            if ($g === 'Non Kependidikan') return 1;
            return 2; // tidak terdefinisi / lain-lain
        };
        $ra = $rank($groupA);
        $rb = $rank($groupB);
        if ($ra !== $rb) return $ra <=> $rb;
        // Sama kelompok: THP desc (khusus disebut untuk Non Kependidikan; diterapkan juga ke Pendidik agar konsisten)
        $ta = (float)($a['total'] ?? 0);
        $tb = (float)($b['total'] ?? 0);
        if ($ta !== $tb) return $tb <=> $ta;
    }
    // Tie-break berikutnya: nama alfabetis
    return strcmp($a['pegawai_nama'], $b['pegawai_nama']);
});

// Get unit name for filename
$unit_name = 'All_Units';
if ($unit_id && $unit_id > 0) {
    $unitStmt = $pdo->prepare("SELECT nama FROM unit WHERE id = ?");
    $unitStmt->execute([$unit_id]);
    $unit_data = $unitStmt->fetch();
    if ($unit_data) {
        $unit_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $unit_data['nama']);
    }
}

// Create new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set document properties
$spreadsheet->getProperties()
    ->setCreator("SISFOPEMBDA")
    ->setLastModifiedBy("SISFOPEMBDA")
    ->setTitle("Data Penugasan Pegawai")
    ->setSubject("Export Data Penugasan")
    ->setDescription("Data penugasan pegawai yang diekspor dari sistem SISFOPEMBDA");

// Set headers - Row 1 (Main headers with grouped columns)
$headers_row1 = [
    'A1' => 'No',
    'B1' => 'Nama Pegawai', 
    'C1' => 'Status',
    'D1' => 'Jabatan',
    'E1' => 'Tj.Jabatan',
    'F1' => 'Gaji Pokok',
    'G1' => 'Honorarium', // Grup untuk G,H,I,J
    'K1' => 'Tunjangan Keluarga', // Grup untuk K,L,M
    'N1' => 'Total (THP)'
];

// Set headers - Row 2 (Sub headers)
$headers_row2 = [
    'G2' => 'Mengajar',
    'H2' => 'Wajib', 
    'I2' => 'Tugas',
    'J2' => 'Honor',
    'K2' => 'Keluarga',
    'L2' => 'Anak',
    'M2' => 'Beras'
];

// Set header values for row 1
foreach ($headers_row1 as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Set header values for row 2
foreach ($headers_row2 as $cell => $value) {
    $sheet->setCellValue($cell, $value);
}

// Merge cells for grouped headers
// A1:A2 (No)
$sheet->mergeCells('A1:A2');
// B1:B2 (Nama Pegawai)  
$sheet->mergeCells('B1:B2');
// C1:C2 (Status)
$sheet->mergeCells('C1:C2');
// D1:D2 (Jabatan)
$sheet->mergeCells('D1:D2');
// E1:E2 (Tj.Jabatan)
$sheet->mergeCells('E1:E2');
// F1:F2 (Gaji Pokok)
$sheet->mergeCells('F1:F2');
// G1:J1 (Honorarium group)
$sheet->mergeCells('G1:J1');
// K1:M1 (Tunjangan Keluarga group)
$sheet->mergeCells('K1:M1');
// N1:N2 (Total THP)
$sheet->mergeCells('N1:N2');

// Style headers row 1 and 2
$headerRange = 'A1:N2';
$sheet->getStyle($headerRange)->applyFromArray([
    'font' => [
        'bold' => true,
        'color' => ['rgb' => 'FFFFFF']
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['rgb' => '4472C4']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'allBorders' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['rgb' => '000000']
        ]
    ]
]);

// Apply wrap text to header column D (Jabatan)
$sheet->getStyle('D1')->getAlignment()->setWrapText(true);

// Set column widths
$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(25);
$sheet->getColumnDimension('C')->setWidth(11);
$sheet->getColumnDimension('D')->setWidth(16);
$sheet->getColumnDimension('E')->setWidth(11);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(10);
$sheet->getColumnDimension('I')->setWidth(12);
$sheet->getColumnDimension('J')->setWidth(10);
$sheet->getColumnDimension('K')->setWidth(10);
$sheet->getColumnDimension('L')->setWidth(10);
$sheet->getColumnDimension('M')->setWidth(12);
$sheet->getColumnDimension('N')->setWidth(12);

// Fill data
$row = 3; // Start from row 3 because header now uses 2 rows
$no = 1;

// Debug output (will be visible in logs if enabled)
// Total pegawai to process: " . count($pegawais) . " 
// This should be exactly 19 for SMK Swasta Pembda Nias

// Initialize totals for summary
$totals = [
    'gaji_pokok' => 0,
    'jam_mengajar' => 0,
    'jam_wajib' => 0,
    'jam_honor' => 0,
    'honor' => 0,
    'tunjangan_keluarga' => 0,
    'tunjangan_anak' => 0,
    'tunjangan_beras' => 0,
    'tunjangan_jabatan' => 0,
    'total' => 0
];

// Pegawai list is already unique from our simplified query approach

foreach ($pegawais as $pegawai) {
    // Debug: Add pegawai info as comment in Excel (for troubleshooting)
    // Processing pegawai: " . $pegawai['pegawai_nama'] . " (ID: " . $pegawai['pegawai_id'] . ")
    
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $pegawai['pegawai_nama']);
    $sheet->setCellValue('C' . $row, $pegawai['status_kepegawaian']);
    $sheet->setCellValue('D' . $row, $pegawai['jabatan_names']);
    
    // Only show values if > 0, otherwise empty
    $gajiPokok = $pegawai['gaji_pokok'] ?: 0;
    $jamMengajar = $pegawai['jam_mengajar'] ?: 0;
    $jamWajib = $pegawai['jam_wajib'] ?: 0;
    $jamHonor = $pegawai['jam_honor'] ?: 0;
    $honor = $pegawai['honor'] ?: 0;
    $tunjanganKeluarga = $pegawai['tunjangan_keluarga'] ?: 0;
    $tunjanganAnak = $pegawai['tunjangan_anak'] ?: 0;
    $tunjanganBeras = $pegawai['tunjangan_beras'] ?: 0;
    $tunjanganJabatan = $pegawai['tunjangan_jabatan'] ?: 0;
    $total = $pegawai['total'] ?: 0;
    
    $sheet->setCellValue('E' . $row, $tunjanganJabatan > 0 ? $tunjanganJabatan : '');
    $sheet->setCellValue('F' . $row, $gajiPokok > 0 ? $gajiPokok : '');
    $sheet->setCellValue('G' . $row, $jamMengajar > 0 ? $jamMengajar : '');
    $sheet->setCellValue('H' . $row, $jamWajib > 0 ? $jamWajib : '');
    $sheet->setCellValue('I' . $row, $jamHonor > 0 ? $jamHonor : '');
    $sheet->setCellValue('J' . $row, $honor > 0 ? $honor : '');
    $sheet->setCellValue('K' . $row, $tunjanganKeluarga > 0 ? $tunjanganKeluarga : '');
    $sheet->setCellValue('L' . $row, $tunjanganAnak > 0 ? $tunjanganAnak : '');
    $sheet->setCellValue('M' . $row, $tunjanganBeras > 0 ? $tunjanganBeras : '');
    $sheet->setCellValue('N' . $row, $total > 0 ? $total : '');
    
    // Add to totals
    $totals['gaji_pokok'] += $gajiPokok;
    $totals['jam_mengajar'] += $jamMengajar;
    $totals['jam_wajib'] += $jamWajib;
    $totals['jam_honor'] += $jamHonor;
    $totals['honor'] += $honor;
    $totals['tunjangan_keluarga'] += $tunjanganKeluarga;
    $totals['tunjangan_anak'] += $tunjanganAnak;
    $totals['tunjangan_beras'] += $tunjanganBeras;
    $totals['tunjangan_jabatan'] += $tunjanganJabatan;
    $totals['total'] += $total;
    
    $row++;
}

// Add totals row
if ($row > 3) {
    $sheet->setCellValue('A' . $row, '');
    $sheet->setCellValue('B' . $row, 'TOTAL');
    $sheet->setCellValue('C' . $row, '');
    $sheet->setCellValue('D' . $row, '');
    $sheet->setCellValue('E' . $row, $totals['tunjangan_jabatan'] > 0 ? $totals['tunjangan_jabatan'] : '');
    $sheet->setCellValue('F' . $row, $totals['gaji_pokok'] > 0 ? $totals['gaji_pokok'] : '');
    $sheet->setCellValue('G' . $row, $totals['jam_mengajar'] > 0 ? $totals['jam_mengajar'] : '');
    $sheet->setCellValue('H' . $row, $totals['jam_wajib'] > 0 ? $totals['jam_wajib'] : '');
    $sheet->setCellValue('I' . $row, $totals['jam_honor'] > 0 ? $totals['jam_honor'] : '');
    $sheet->setCellValue('J' . $row, $totals['honor'] > 0 ? $totals['honor'] : '');
    $sheet->setCellValue('K' . $row, $totals['tunjangan_keluarga'] > 0 ? $totals['tunjangan_keluarga'] : '');
    $sheet->setCellValue('L' . $row, $totals['tunjangan_anak'] > 0 ? $totals['tunjangan_anak'] : '');
    $sheet->setCellValue('M' . $row, $totals['tunjangan_beras'] > 0 ? $totals['tunjangan_beras'] : '');
    $sheet->setCellValue('N' . $row, $totals['total'] > 0 ? $totals['total'] : '');
    
    // Style totals row
    $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
        'font' => [
            'bold' => true,
            'color' => ['rgb' => 'FFFFFF']
        ],
        'fill' => [
            'fillType' => Fill::FILL_SOLID,
            'startColor' => ['rgb' => '4472C4']
        ],
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THICK,
                'color' => ['rgb' => '000000']
            ]
        ],
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ]);
    // Left align label TOTAL for clarity
    $sheet->getStyle('B' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
    
    // Right align for currency columns in totals
    $currencyColumns = ['E', 'F', 'J', 'K', 'L', 'M', 'N'];
    foreach ($currencyColumns as $col) {
        $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0');
    }
    
    // Center align for jam columns in totals
    $jamColumns = ['G', 'H', 'I'];
    foreach ($jamColumns as $col) {
        $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    }
}

// Apply data styling
if ($row > 3) {
    $dataRange = 'A3:N' . ($row - 1);
    $sheet->getStyle($dataRange)->applyFromArray([
        'borders' => [
            'allBorders' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['rgb' => 'CCCCCC']
            ]
        ],
        'alignment' => [
            'vertical' => Alignment::VERTICAL_CENTER
        ]
    ]);
    
    // Center align for specific columns
    $sheet->getStyle('A3:A' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('C3:C' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('G3:I' . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    // Wrap text for column D (Jabatan)
    $sheet->getStyle('D3:D' . ($row - 1))->getAlignment()->setWrapText(true);
    $sheet->getStyle('D3:D' . ($row - 1))->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    
    // Set auto row height for data rows to accommodate wrapped text
    for ($i = 3; $i < $row; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(-1); // -1 = auto height
    }
    
    // Right align for currency columns
    $currencyColumns = ['E', 'F', 'J', 'K', 'L', 'M', 'N'];
    foreach ($currencyColumns as $col) {
        $sheet->getStyle($col . '3:' . $col . ($row - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle($col . '3:' . $col . ($row - 1))->getNumberFormat()->setFormatCode('#,##0');
    }
}

// --- Otorisasi (Subsidi Operasional) & Grand Total Integration ---
// Tambah dua baris tambahan: OTORISASI dan GRAND TOTAL (Total THP + Otorisasi)
try {
    $otorisasiTotal = 0;
    $otorisasiPerUnit = [];
    if ($unit_id && $unit_id > 0) {
        // Mode satu unit
        $stmtOto = $pdo->prepare("SELECT COALESCE(SUM(nilai),0) FROM otorisasi WHERE unit_id = ? AND tahun_pelajaran = ?");
        $stmtOto->execute([$unit_id, $tahun_pelajaran]);
        $otorisasiTotal = (float)$stmtOto->fetchColumn();
    } else {
        // Mode semua unit: ambil breakdown per unit & total keseluruhan
        $stmtOtoAll = $pdo->prepare("SELECT u.id, u.nama, COALESCE(SUM(o.nilai),0) total_otorisasi
                                     FROM unit u
                                     LEFT JOIN otorisasi o ON o.unit_id = u.id AND o.tahun_pelajaran = ?
                                     GROUP BY u.id, u.nama
                                     HAVING total_otorisasi > 0
                                     ORDER BY u.nama");
        $stmtOtoAll->execute([$tahun_pelajaran]);
        $otorisasiPerUnit = $stmtOtoAll->fetchAll(PDO::FETCH_ASSOC);
        foreach ($otorisasiPerUnit as $r) { $otorisasiTotal += (float)$r['total_otorisasi']; }
    }
} catch (Throwable $e) {
    $otorisasiTotal = 0; // Jika tabel belum ada / error, abaikan tanpa hentikan export
    $otorisasiPerUnit = [];
}

// Posisi mulai baris tambahan setelah (mungkin) totals row
$insertRow = $row + 1; // $row saat ini adalah baris totals atau baris pertama kosong jika tidak ada data

// Jika tidak ada data pegawai (row <=3), siapkan label dasar lebih dulu
if ($row <= 3) {
    $sheet->setCellValue('B' . $row, 'TOTAL');
    $sheet->setCellValue('N' . $row, 0);
    $sheet->getStyle('A' . $row . ':N' . $row)->applyFromArray([
        'font' => ['bold' => true],
        'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ] ],
    ]);
    $insertRow = $row + 1;
}

// Jika mode semua unit dan ada breakdown, tulis tiap unit terlebih dahulu
if ($unit_id === 0 && !empty($otorisasiPerUnit)) {
    foreach ($otorisasiPerUnit as $ou) {
        $sheet->setCellValue('B' . $insertRow, 'OTORISASI - ' . $ou['nama']);
        $sheet->setCellValue('N' . $insertRow, $ou['total_otorisasi'] > 0 ? $ou['total_otorisasi'] : '');
        $sheet->getStyle('B' . $insertRow . ':N' . $insertRow)->applyFromArray([
            'font' => ['bold' => false],
            'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ] ],
        ]);
        $sheet->getStyle('N' . $insertRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('N' . $insertRow)->getNumberFormat()->setFormatCode('#,##0');
        $insertRow++;
    }
}

// Baris OTORISASI (Total)
$sheet->setCellValue('B' . $insertRow, 'OTORISASI (Subsidi Operasional)');
$sheet->setCellValue('N' . $insertRow, $otorisasiTotal > 0 ? $otorisasiTotal : '');
$sheet->getStyle('B' . $insertRow . ':N' . $insertRow)->applyFromArray([
    'font' => ['bold' => true],
    'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THIN ] ],
    'fill' => [ 'fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DEEAF6'] ]
]);
$sheet->getStyle('B' . $insertRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('N' . $insertRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('N' . $insertRow)->getNumberFormat()->setFormatCode('#,##0');

// Baris GRAND TOTAL (THP + seluruh otorisasi)
$grandRow = $insertRow + 1;
$grandTotal = ($totals['total'] ?? 0) + $otorisasiTotal;
$sheet->setCellValue('B' . $grandRow, 'GRAND TOTAL (THP + Otorisasi)');
$sheet->setCellValue('N' . $grandRow, $grandTotal > 0 ? $grandTotal : '');
$sheet->mergeCells('B' . $grandRow . ':M' . $grandRow);
$sheet->getStyle('B' . $grandRow . ':N' . $grandRow)->applyFromArray([
    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    'fill' => [ 'fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '305496'] ],
    'borders' => [ 'allBorders' => [ 'borderStyle' => Border::BORDER_THICK ] ],
    'alignment' => [ 'vertical' => Alignment::VERTICAL_CENTER ]
]);
$sheet->getStyle('B' . $grandRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
$sheet->getStyle('N' . $grandRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
$sheet->getStyle('N' . $grandRow)->getNumberFormat()->setFormatCode('#,##0');

// Add title and info
$sheet->insertNewRowBefore(1, 3);
$sheet->setCellValue('A1', 'DATA PENUGASAN PEGAWAI YAYAN PERGURUAN PEMBDA NIAS');
$sheet->setCellValue('A2', 'Tahun Pelajaran : ' . $tahun_pelajaran);
if ($unit_id && $unit_id > 0) {
    $sheet->setCellValue('A3', 'Unit : ' . ($unit_data['nama'] ?? 'N/A'));
}

// Style title
$sheet->getStyle('A1')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 16,
        'color' => ['rgb' => '2F5597']
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER
    ]
]);

$sheet->getStyle('A2:A3')->applyFromArray([
    'font' => [
        'bold' => true,
        'size' => 12
    ]
]);

// Merge title cells
$sheet->mergeCells('A1:N1');
if ($unit_id && $unit_id > 0) {
    $sheet->mergeCells('A3:N3');
}

// Set filename
$filename = 'Data_Penugasan_' . $unit_name . '_' . str_replace('/', '_', $tahun_pelajaran) . '_' . date('Y-m-d_H-i-s') . '.xlsx';

// Set headers for download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

// Save file
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
