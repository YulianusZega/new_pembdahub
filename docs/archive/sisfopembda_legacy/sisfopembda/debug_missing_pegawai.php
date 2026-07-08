<?php
require_once 'config.php';

echo "<h3>Debug Pegawai yang Hilang - Yeremia Harefa</h3>";

// Cek data pegawai
echo "<h4>1. Data Pegawai ID 79:</h4>";
$stmt = $pdo->prepare("SELECT * FROM pegawai WHERE id = 79");
$stmt->execute();
$pegawai = $stmt->fetch();

if ($pegawai) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    foreach ($pegawai as $key => $value) {
        echo "<tr><td>$key</td><td>$value</td></tr>";
    }
    echo "</table>";
} else {
    echo "Pegawai tidak ditemukan!";
}

// Cek data penugasan
echo "<h4>2. Data Penugasan untuk Pegawai ID 79:</h4>";
$stmt2 = $pdo->prepare("
    SELECT pen.*, u.nama as unit_nama 
    FROM penugasan pen
    JOIN unit u ON pen.unit_id = u.id
    WHERE pen.pegawai_id = 79
");
$stmt2->execute();
$penugasan = $stmt2->fetchAll();

if ($penugasan) {
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Unit</th><th>Tahun</th><th>Jam Mengajar</th><th>Jam Wajib</th><th>Jam Honor</th><th>Honor</th></tr>";
    foreach ($penugasan as $p) {
        echo "<tr>";
        echo "<td>" . $p['id'] . "</td>";
        echo "<td>" . $p['unit_nama'] . "</td>";
        echo "<td>" . $p['tahun_pelajaran'] . "</td>";
        echo "<td>" . $p['jam_mengajar'] . "</td>";
        echo "<td>" . $p['jam_wajib'] . "</td>";
        echo "<td>" . $p['jam_honor'] . "</td>";
        echo "<td>" . $p['honor'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p><strong>TIDAK ADA DATA PENUGASAN!</strong> Ini sebabnya pegawai tidak muncul di export.</p>";
}

// Cek jabatan
echo "<h4>3. Data Jabatan untuk Penugasan Pegawai ID 79:</h4>";
if ($penugasan) {
    foreach ($penugasan as $p) {
        $stmt3 = $pdo->prepare("
            SELECT j.nama as jabatan_nama 
            FROM penugasan_jabatan pj
            JOIN jabatan j ON pj.jabatan_id = j.id
            WHERE pj.penugasan_id = ?
        ");
        $stmt3->execute([$p['id']]);
        $jabatan = $stmt3->fetchAll();
        
        echo "<p>Penugasan ID " . $p['id'] . ":</p>";
        if ($jabatan) {
            echo "<ul>";
            foreach ($jabatan as $jab) {
                echo "<li>" . $jab['jabatan_nama'] . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Tidak ada jabatan.</p>";
        }
    }
} else {
    echo "<p>Tidak ada penugasan, jadi tidak ada jabatan.</p>";
}

// Test query export
echo "<h4>4. Test Query Export untuk Unit SMK:</h4>";
$tahun_pelajaran = '2025/2026';
$where = ["p.unit_id IS NOT NULL"];
$params = [$tahun_pelajaran];

// Filter unit SMK
$where[] = "u.nama = 'SMK Swasta Pembda Nias'";

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
        ORDER BY p.nama";

echo "<p><strong>Query:</strong></p>";
echo "<pre>" . $sql . "</pre>";

$stmt_test = $pdo->prepare($sql);
$stmt_test->execute($params);
$results = $stmt_test->fetchAll();

echo "<p><strong>Total hasil:</strong> " . count($results) . "</p>";

// Cari Yeremia Harefa
$found = false;
foreach ($results as $result) {
    if ($result['pegawai_id'] == 79) {
        $found = true;
        echo "<p><strong>✓ Yeremia Harefa DITEMUKAN di query:</strong></p>";
        echo "<table border='1'>";
        foreach ($result as $key => $value) {
            echo "<tr><td>$key</td><td>$value</td></tr>";
        }
        echo "</table>";
        break;
    }
}

if (!$found) {
    echo "<p><strong>❌ Yeremia Harefa TIDAK DITEMUKAN di query!</strong></p>";
    echo "<p>Kemungkinan penyebab:</p>";
    echo "<ul>";
    echo "<li>Tidak ada data penugasan untuk tahun 2025/2026</li>";
    echo "<li>Unit_id tidak match dengan unit SMK</li>";
    echo "<li>Data tidak memenuhi kondisi WHERE</li>";
    echo "</ul>";
}

echo "<h4>5. Solusi:</h4>";
echo "<p>Untuk menampilkan Yeremia Harefa di export, perlu:</p>";
echo "<ol>";
echo "<li>Buat data penugasan untuk tahun 2025/2026</li>";
echo "<li>Pastikan unit_id = 3 (SMK Swasta Pembda Nias)</li>";
echo "<li>Isi data jam mengajar, jabatan, dll</li>";
echo "</ol>";
?>
