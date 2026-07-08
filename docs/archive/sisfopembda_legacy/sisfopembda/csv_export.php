<?php
// CSV Export alternative if PhpSpreadsheet is not available
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
        $headers = ['No', 'NIP', 'Nama Pegawai', 'Status'];
        if ($show_details) {
            $headers = array_merge($headers, ['Gaji Pokok', 'Tunj. Jabatan', 'Tunj. Keluarga', 'Tunj. Anak', 'Tunj. Beras', 'Honor']);
        }
        $headers[] = 'Total Gaji';
        fputcsv($output, $headers);
        
        $no = 1;
        $unitTotal = 0;
        
        foreach ($data as $row) {
            $csvRow = [
                $no++,
                $row['nomor_induk'],
                $row['pegawai_nama'],
                $row['status_kepegawaian']
            ];
            
            if ($show_details) {
                $csvRow = array_merge($csvRow, [
                    $row['gaji_pokok'],
                    $row['tunjangan_jabatan'],
                    $row['tunjangan_keluarga'],
                    $row['tunjangan_anak'],
                    $row['tunjangan_beras'],
                    $row['honor']
                ]);
            }
            
            $csvRow[] = $row['total'];
            fputcsv($output, $csvRow);
            
            $unitTotal += $row['total'];
        }
        
        if (count($data) > 1) {
            $totalRow = array_fill(0, count($headers) - 1, '');
            $totalRow[0] = 'Total ' . $unitName . ' (' . count($data) . ' pegawai)';
            $totalRow[count($headers) - 1] = $unitTotal;
            fputcsv($output, $totalRow);
        }
        
        fputcsv($output, []);
    }
    
    fclose($output);
}
?>
