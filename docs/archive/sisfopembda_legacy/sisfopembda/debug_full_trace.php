<?php
require_once 'config.php';

echo "<h1>Full Debug Export Logic Trace</h1>";

// Exactly mirror export logic
$unit_id = 2;
$status_kepegawaian = '';
$tahun_pelajaran = '2025/2026';
$jabatan_ids = [];

// Parse jabatan filter
$jabatan_ids_filter = [];
if (!empty($jabatan_ids)) {
    $jabatan_ids_filter = explode(',', $jabatan_ids);
    $jabatan_ids_filter = array_map('intval', $jabatan_ids_filter);
    $jabatan_ids_filter = array_filter($jabatan_ids_filter);
}

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

echo "<h2>Step 1: Initial Query</h2>";
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
$pegawais = $stmt->fetchAll();

echo "<p>Count: " . count($pegawais) . "</p>";
$yarisman_step1 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Yarisman Waruwu'; }));
$dewi_step1 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Dewi Juli Sulastri Zega'; }));
echo "<p>Yarisman: $yarisman_step1, Dewi: $dewi_step1</p>";

echo "<h2>Step 2: Add Unit and Penugasan Data</h2>";
foreach ($pegawais as &$pegawai) {
    // Get unit name
    $unit_sql = "SELECT nama as unit_nama FROM unit WHERE id = ?";
    $unit_stmt = $pdo->prepare($unit_sql);
    $unit_stmt->execute([$pegawai['unit_id']]);
    $unit_data = $unit_stmt->fetch();
    $pegawai['unit_nama'] = $unit_data ? $unit_data['unit_nama'] : '-';
    
    // Get penugasan data
    $penugasan_sql = "SELECT 
                        id as penugasan_id,
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
    $penugasan_stmt->execute([$pegawai['pegawai_id'], $pegawai['unit_id'], $tahun_pelajaran]);
    $penugasan_data = $penugasan_stmt->fetch();
    
    if ($penugasan_data) {
        $pegawai['penugasan_id'] = $penugasan_data['penugasan_id'];
        $pegawai['jam_mengajar'] = $penugasan_data['jam_mengajar'];
        $pegawai['jam_wajib'] = $penugasan_data['jam_wajib'];
        $pegawai['jam_honor'] = $penugasan_data['jam_honor'];
        $pegawai['honor'] = $penugasan_data['honor'];
        $pegawai['tunjangan_keluarga'] = $penugasan_data['tunjangan_keluarga'];
        $pegawai['tunjangan_anak'] = $penugasan_data['tunjangan_anak'];
        $pegawai['tunjangan_beras'] = $penugasan_data['tunjangan_beras'];
        $pegawai['tunjangan_jabatan'] = $penugasan_data['tunjangan_jabatan'];
        $pegawai['total'] = $penugasan_data['total'];
    } else {
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

echo "<p>Count: " . count($pegawais) . "</p>";
$yarisman_step2 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Yarisman Waruwu'; }));
$dewi_step2 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Dewi Juli Sulastri Zega'; }));
echo "<p>Yarisman: $yarisman_step2, Dewi: $dewi_step2</p>";

echo "<h2>Step 3: Apply Jabatan Filter</h2>";
if (!empty($jabatan_ids_filter)) {
    $filtered_pegawais = [];
    foreach ($pegawais as $pegawai) {
        if ($pegawai['penugasan_id']) {
            $placeholders_filter = implode(',', array_fill(0, count($jabatan_ids_filter), '?'));
            $jabatan_check_sql = "SELECT COUNT(*) FROM penugasan_jabatan pj 
                                 JOIN jabatan j ON pj.jabatan_id = j.id 
                                 WHERE pj.penugasan_id = ? AND j.id IN ($placeholders_filter)";
            $jabatan_check_params = array_merge([$pegawai['penugasan_id']], $jabatan_ids_filter);
            $jabatan_check_stmt = $pdo->prepare($jabatan_check_sql);
            $jabatan_check_stmt->execute($jabatan_check_params);
            
            if ($jabatan_check_stmt->fetchColumn() > 0) {
                $filtered_pegawais[] = $pegawai;
            }
        } else {
            $filtered_pegawais[] = $pegawai;
        }
    }
    $pegawais = $filtered_pegawais;
    echo "<p>Jabatan filter applied</p>";
} else {
    echo "<p>No jabatan filter</p>";
}

echo "<p>Count: " . count($pegawais) . "</p>";
$yarisman_step3 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Yarisman Waruwu'; }));
$dewi_step3 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Dewi Juli Sulastri Zega'; }));
echo "<p>Yarisman: $yarisman_step3, Dewi: $dewi_step3</p>";

echo "<h2>Step 4: Sorting</h2>";
usort($pegawais, function($a, $b) {
    $statusPriority = ['PNS' => 1, 'GTY' => 2, 'Honorer' => 3, 'PTY' => 4];
    $statusA = $statusPriority[$a['status_kepegawaian']] ?? 5;
    $statusB = $statusPriority[$b['status_kepegawaian']] ?? 5;
    if ($statusA !== $statusB) {
        return $statusA - $statusB;
    }
    return strcmp($a['pegawai_nama'], $b['pegawai_nama']);
});

echo "<p>Count: " . count($pegawais) . "</p>";
$yarisman_step4 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Yarisman Waruwu'; }));
$dewi_step4 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Dewi Juli Sulastri Zega'; }));
echo "<p>Yarisman: $yarisman_step4, Dewi: $dewi_step4</p>";

echo "<h2>Step 5: Ensure Array Type</h2>";
if (!is_array($pegawais)) {
    $pegawais = [];
}

echo "<p>Count: " . count($pegawais) . "</p>";
$yarisman_step5 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Yarisman Waruwu'; }));
$dewi_step5 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Dewi Juli Sulastri Zega'; }));
echo "<p>Yarisman: $yarisman_step5, Dewi: $dewi_step5</p>";

echo "<h2>Step 6: Get Jabatan Data</h2>";
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
        $pegawai['jabatan_list'] = $jabatanList;
    } else {
        $pegawai['jabatan_names'] = '';
        $pegawai['jabatan_list'] = [];
    }
}

echo "<p>Count: " . count($pegawais) . "</p>";
$yarisman_step6 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Yarisman Waruwu'; }));
$dewi_step6 = count(array_filter($pegawais, function($p) { return $p['pegawai_nama'] == 'Dewi Juli Sulastri Zega'; }));
echo "<p>Yarisman: $yarisman_step6, Dewi: $dewi_step6</p>";

echo "<h2>Final List</h2>";
echo "<table border='1'>";
echo "<tr><th>Row</th><th>ID</th><th>Nama</th><th>Status</th></tr>";
for ($i = 0; $i < count($pegawais); $i++) {
    $p = $pegawais[$i];
    $style = '';
    if ($p['pegawai_nama'] == 'Yarisman Waruwu') {
        $style = 'background-color: #ccffcc;';
    } elseif ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') {
        $style = 'background-color: #ffcccc;';
    }
    echo "<tr style='$style'><td>" . ($i + 1) . "</td><td>{$p['pegawai_id']}</td><td>{$p['pegawai_nama']}</td><td>{$p['status_kepegawaian']}</td></tr>";
}
echo "</table>";

echo "<h2>Summary</h2>";
echo "<table border='1'>";
echo "<tr><th>Step</th><th>Total</th><th>Yarisman</th><th>Dewi</th></tr>";
echo "<tr><td>1. Initial Query</td><td>" . count($pegawais) . "</td><td>$yarisman_step1</td><td>$dewi_step1</td></tr>";
echo "<tr><td>2. Add Data</td><td>" . count($pegawais) . "</td><td>$yarisman_step2</td><td>$dewi_step2</td></tr>";
echo "<tr><td>3. Jabatan Filter</td><td>" . count($pegawais) . "</td><td>$yarisman_step3</td><td>$dewi_step3</td></tr>";
echo "<tr><td>4. Sorting</td><td>" . count($pegawais) . "</td><td>$yarisman_step4</td><td>$dewi_step4</td></tr>";
echo "<tr><td>5. Array Check</td><td>" . count($pegawais) . "</td><td>$yarisman_step5</td><td>$dewi_step5</td></tr>";
echo "<tr><td>6. Jabatan Data</td><td>" . count($pegawais) . "</td><td>$yarisman_step6</td><td>$dewi_step6</td></tr>";
echo "</table>";

?>
