<?php
// Debug Final Export Check
require_once 'config.php';

echo "<h1>Debug Final Export Check - Simple Approach</h1>";
echo "<style>
    table { border-collapse: collapse; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
    .highlight { background-color: #ffff99; }
    .error { background-color: #ffcccc; }
    .success { background-color: #ccffcc; }
</style>";

// Mirror the exact logic from export file
$unit_id = 2;
$status_kepegawaian = '';
$tahun_pelajaran = '2025/2026';

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

// Step 1: Get ALL pegawai - simple query
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

echo "<h2>Query Being Used:</h2>";
echo "<pre>" . htmlspecialchars($sql_pegawai) . "</pre>";
echo "<p>Parameters: " . implode(', ', $params) . "</p>";

$stmt = $pdo->prepare($sql_pegawai);
$stmt->execute($params);
$pegawais = $stmt->fetchAll();

echo "<h2>Pegawai Results (Step 1):</h2>";
echo "<table>";
echo "<tr><th>Row</th><th>ID</th><th>Nama</th><th>Unit ID</th></tr>";
$row_num = 1;
foreach ($pegawais as $p) {
    $class = '';
    if ($p['pegawai_nama'] == 'Yarisman Waruwu') {
        $class = 'highlight';
    } elseif ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') {
        $class = 'success';
    }
    echo "<tr class='$class'>";
    echo "<td>$row_num</td>";
    echo "<td>{$p['pegawai_id']}</td>";
    echo "<td>{$p['pegawai_nama']}</td>";
    echo "<td>{$p['unit_id']}</td>";
    echo "</tr>";
    $row_num++;
}
echo "</table>";
echo "<p><strong>Total Pegawai: " . count($pegawais) . "</strong></p>";

// Step 2: Check for any duplicates
echo "<h2>Duplicate Check:</h2>";
$ids = array_column($pegawais, 'pegawai_id');
$names = array_column($pegawais, 'pegawai_nama');

$duplicate_ids = array_diff_assoc($ids, array_unique($ids));
$duplicate_names = array_diff_assoc($names, array_unique($names));

if (empty($duplicate_ids) && empty($duplicate_names)) {
    echo "<p class='success'>✓ No duplicates found!</p>";
} else {
    echo "<p class='error'>✗ Duplicates found:</p>";
    if (!empty($duplicate_ids)) {
        echo "<p>Duplicate IDs: " . implode(', ', $duplicate_ids) . "</p>";
    }
    if (!empty($duplicate_names)) {
        echo "<p>Duplicate Names: " . implode(', ', $duplicate_names) . "</p>";
    }
}

// Step 3: Check specific names
echo "<h2>Specific Names Check:</h2>";
$yarisman_count = 0;
$dewi_count = 0;
foreach ($pegawais as $p) {
    if ($p['pegawai_nama'] == 'Yarisman Waruwu') {
        $yarisman_count++;
        echo "<p class='highlight'>Yarisman Waruwu found - ID: {$p['pegawai_id']}</p>";
    }
    if ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') {
        $dewi_count++;
        echo "<p class='success'>Dewi Juli Sulastri Zega found - ID: {$p['pegawai_id']}</p>";
    }
}

echo "<h3>Count Summary:</h3>";
echo "<ul>";
echo "<li>Yarisman Waruwu: $yarisman_count times</li>";
echo "<li>Dewi Juli Sulastri Zega: $dewi_count times</li>";
echo "</ul>";

if ($yarisman_count == 1 && $dewi_count == 1) {
    echo "<p class='success'>✓ Both names appear exactly once - PERFECT!</p>";
} else {
    echo "<p class='error'>✗ Problem with name counts</p>";
}

// Step 4: Show final sorted list exactly as it would appear in Excel
echo "<h2>Final Excel Order Preview:</h2>";
echo "<table>";
echo "<tr><th>Excel Row</th><th>No</th><th>ID</th><th>Nama</th></tr>";
$excel_row = 3; // Starting from row 3 in Excel (after headers)
$no = 1;
foreach ($pegawais as $p) {
    $class = '';
    if ($p['pegawai_nama'] == 'Yarisman Waruwu') {
        $class = 'highlight';
    } elseif ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') {
        $class = 'success';
    }
    echo "<tr class='$class'>";
    echo "<td>$excel_row</td>";
    echo "<td>$no</td>";
    echo "<td>{$p['pegawai_id']}</td>";
    echo "<td>{$p['pegawai_nama']}</td>";
    echo "</tr>";
    $excel_row++;
    $no++;
}
echo "</table>";

echo "<h2>Summary:</h2>";
echo "<ul>";
echo "<li>Query returns: " . count($pegawais) . " pegawai</li>";
echo "<li>Expected: 19 pegawai</li>";
echo "<li>Duplicates: " . (empty($duplicate_ids) && empty($duplicate_names) ? 'NONE' : 'FOUND') . "</li>";
echo "<li>Yarisman count: $yarisman_count</li>";
echo "<li>Dewi count: $dewi_count</li>";
echo "<li>Status: " . (count($pegawais) == 19 && $yarisman_count == 1 && $dewi_count == 1 ? '<span style="color: green;">SUCCESS</span>' : '<span style="color: red;">PROBLEM</span>') . "</li>";
echo "</ul>";

?>
