<?php
require_once 'config.php';

echo "<h1>Debug Export Logic - Step-by-Step Analysis</h1>";
echo "<style>
    body { font-family: sans-serif; }
    table { border-collapse: collapse; margin: 20px 0; width: 100%; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }
    th { background-color: #f2f2f2; }
    .highlight-good { background-color: #e6ffed; }
    .highlight-bad { background-color: #ffe6e6; }
    .step { 
        background-color: #007bff; 
        color: white; 
        padding: 10px; 
        margin-top: 20px; 
        border-radius: 5px;
    }
    pre { background-color: #eee; padding: 10px; border-radius: 5px; }
</style>";

// --- Hardcoded Filters (same as export) ---
$unit_id = 2;
$tahun_pelajaran = '2025/2026';
$status_kepegawaian = '';
$jabatan_ids_filter = [];

// --- STEP 1: Initial Pegawai Fetch ---
echo "<h2 class='step'>Step 1: Initial Pegawai Fetch</h2>";

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

$sql_pegawai = "SELECT p.id as pegawai_id, p.nama as pegawai_nama, p.status_kepegawaian, p.unit_id
                FROM pegawai p
                WHERE " . implode(' AND ', $where) . "
                ORDER BY p.id";

$stmt = $pdo->prepare($sql_pegawai);
$stmt->execute($params);
$pegawais = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Found <strong>" . count($pegawais) . "</strong> pegawai records initially.</p>";
echo "<table><tr><th>ID</th><th>Nama</th><th>Status</th><th>Unit ID</th><th>Note</th></tr>";
$ids_step1 = [];
foreach ($pegawais as $p) {
    $note = '';
    if (in_array($p['pegawai_id'], $ids_step1)) {
        $note = "<strong style='color:red;'>DUPLICATE ID!</strong>";
    }
    $ids_step1[] = $p['pegawai_id'];
    
    $class = '';
    if ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') $class = 'highlight-bad';
    if ($p['pegawai_nama'] == 'Yarisman Waruwu') $class = 'highlight-good';

    echo "<tr class='$class'><td>{$p['pegawai_id']}</td><td>{$p['pegawai_nama']}</td><td>{$p['status_kepegawaian']}</td><td>{$p['unit_id']}</td><td>$note</td></tr>";
}
echo "</table>";

// --- STEP 2: Loop and Fetch Penugasan & Jabatan Data ---
echo "<h2 class='step'>Step 2: Loop and Fetch Details</h2>";
echo "<p>Looping through each of the " . count($pegawais) . " pegawai to get their specific penugasan and jabatan data.</p>";

foreach ($pegawais as &$pegawai) {
    // Get unit name
    $unit_sql = "SELECT nama as unit_nama FROM unit WHERE id = ?";
    $unit_stmt = $pdo->prepare($unit_sql);
    $unit_stmt->execute([$pegawai['unit_id']]);
    $unit_data = $unit_stmt->fetch();
    $pegawai['unit_nama'] = $unit_data ? $unit_data['unit_nama'] : '-';

    // Get penugasan data
    $penugasan_sql = "SELECT id as penugasan_id FROM penugasan 
                      WHERE pegawai_id = ? AND unit_id = ? AND tahun_pelajaran = ?
                      ORDER BY id DESC LIMIT 1";
    $penugasan_stmt = $pdo->prepare($penugasan_sql);
    $penugasan_stmt->execute([$pegawai['pegawai_id'], $pegawai['unit_id'], $tahun_pelajaran]);
    $penugasan_data = $penugasan_stmt->fetch();
    $pegawai['penugasan_id'] = $penugasan_data ? $penugasan_data['penugasan_id'] : null;

    // Get jabatan data
    if ($pegawai['penugasan_id']) {
        $jabatanStmt = $pdo->prepare("SELECT j.nama FROM penugasan_jabatan pj JOIN jabatan j ON pj.jabatan_id = j.id WHERE pj.penugasan_id = ?");
        $jabatanStmt->execute([$pegawai['penugasan_id']]);
        $jabatanList = $jabatanStmt->fetchAll(PDO::FETCH_COLUMN);
        $pegawai['jabatan_names'] = implode(', ', $jabatanList);
    } else {
        $pegawai['jabatan_names'] = '';
    }
}
unset($pegawai);

echo "<p>Finished fetching details. Now checking the state of the array.</p>";

// --- STEP 3: Analyze Array State After Loop ---
echo "<h2 class='step'>Step 3: Analyze Array After Loop</h2>";
echo "<p>Total records in array: <strong>" . count($pegawais) . "</strong></p>";
echo "<table><tr><th>ID</th><th>Nama</th><th>Penugasan ID</th><th>Jabatan</th><th>Note</th></tr>";
$ids_step3 = [];
$duplicate_found = false;
foreach ($pegawais as $p) {
    $note = '';
    if (in_array($p['pegawai_id'], $ids_step3)) {
        $note = "<strong style='color:red;'>DUPLICATE ID DETECTED!</strong>";
        $duplicate_found = true;
    }
    $ids_step3[] = $p['pegawai_id'];

    $class = '';
    if ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') $class = 'highlight-bad';
    if ($p['pegawai_nama'] == 'Yarisman Waruwu') $class = 'highlight-good';

    echo "<tr class='$class'><td>{$p['pegawai_id']}</td><td>{$p['pegawai_nama']}</td><td>{$p['penugasan_id']}</td><td>{$p['jabatan_names']}</td><td>$note</td></tr>";
}
echo "</table>";

if ($duplicate_found) {
    echo "<p style='color:red; font-weight:bold;'>ANALYSIS: Duplicates were introduced. This is unexpected and points to a fundamental issue in how PHP is handling the array, or a misunderstanding of the data structure. The loop itself should not create duplicates.</p>";
} else {
    echo "<p style='color:green; font-weight:bold;'>ANALYSIS: No duplicates were introduced during the detail-fetching loop. The array is still clean.</p>";
}


// --- STEP 4: Jabatan Filter Logic ---
echo "<h2 class='step'>Step 4: Jabatan Filter Logic</h2>";
if (!empty($jabatan_ids_filter)) {
    echo "<p>Jabatan filter is active. Re-creating the pegawai array.</p>";
    // This block would run if the filter was active.
} else {
    echo "<p>Jabatan filter is NOT active. The pegawai array is unchanged in this step.</p>";
}

// --- STEP 5: Final Sort and Final Analysis ---
echo "<h2 class='step'>Step 5: Final Analysis After Sorting</h2>";

usort($pegawais, function($a, $b) {
    $priorityA = $a['jabatan_priority'] ?? 7;
    $priorityB = $b['jabatan_priority'] ?? 7;
    if ($priorityA !== $priorityB) return $priorityA - $priorityB;
    $statusPriority = ['PNS' => 1, 'GTY' => 2, 'Honorer' => 3, 'PTY' => 4];
    $statusA = $statusPriority[$a['status_kepegawaian']] ?? 5;
    $statusB = $statusPriority[$b['status_kepegawaian']] ?? 5;
    if ($statusA !== $statusB) return $statusA - $statusB;
    return strcmp($a['pegawai_nama'], $b['pegawai_nama']);
});

echo "<p>Array has been sorted. Final check for duplicates and missing persons.</p>";
echo "<p>Total records in final array: <strong>" . count($pegawais) . "</strong></p>";
echo "<table><tr><th>Final Row #</th><th>ID</th><th>Nama</th><th>Note</th></tr>";
$final_ids = [];
$final_duplicate_found = false;
$yarisman_found = false;
$dewi_count = 0;
$row_num = 1;
foreach ($pegawais as $p) {
    $note = '';
    if (in_array($p['pegawai_id'], $final_ids)) {
        $note = "<strong style='color:red;'>DUPLICATE ID!</strong>";
        $final_duplicate_found = true;
    }
    $final_ids[] = $p['pegawai_id'];

    if ($p['pegawai_nama'] == 'Yarisman Waruwu') $yarisman_found = true;
    if ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') $dewi_count++;
    
    $class = '';
    if ($p['pegawai_nama'] == 'Dewi Juli Sulastri Zega') $class = 'highlight-bad';
    if ($p['pegawai_nama'] == 'Yarisman Waruwu') $class = 'highlight-good';

    echo "<tr class='$class'><td>$row_num</td><td>{$p['pegawai_id']}</td><td>{$p['pegawai_nama']}</td><td>$note</td></tr>";
    $row_num++;
}
echo "</table>";

echo "<h3>Final Conclusion:</h3>";
if (count($pegawais) == 19 && !$final_duplicate_found && $yarisman_found && $dewi_count == 1) {
    echo "<p style='color:green; font-weight:bold;'>✓ SUCCESS: The logic appears correct. The final array has 19 unique employees, including Yarisman and a single Dewi.</p>";
} else {
    echo "<p style='color:red; font-weight:bold;'>✗ FAILURE: The logic is flawed. See details below:</p>";
    echo "<ul>";
    if (count($pegawais) != 19) echo "<li>- Expected 19 employees, but got " . count($pegawais) . ".</li>";
    if ($final_duplicate_found) echo "<li>- Duplicates were found in the final array.</li>";
    if (!$yarisman_found) echo "<li>- Yarisman Waruwu is MISSING.</li>";
    if ($dewi_count > 1) echo "<li>- Dewi Juli Sulastri Zega appears $dewi_count times.</li>";
    echo "</ul>";
}

?>
