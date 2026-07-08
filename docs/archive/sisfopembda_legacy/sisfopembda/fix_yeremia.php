<?php
require_once 'config.php';

echo "<h3>Perbaikan Yeremia Harefa</h3>";

try {
    // 1. Cek data saat ini
    $stmt = $pdo->prepare("SELECT id, nama, unit_id FROM pegawai WHERE id = 79");
    $stmt->execute();
    $yeremia = $stmt->fetch();
    
    echo "<h4>1. Data Yeremia saat ini:</h4>";
    if ($yeremia) {
        echo "<p>ID: " . $yeremia['id'] . "</p>";
        echo "<p>Nama: " . $yeremia['nama'] . "</p>";
        echo "<p>Unit ID: " . ($yeremia['unit_id'] ?? 'NULL') . "</p>";
        
        // 2. Perbaiki unit_id jika perlu
        if ($yeremia['unit_id'] != 3) {
            echo "<h4>2. Memperbaiki Unit ID:</h4>";
            $stmt_update = $pdo->prepare("UPDATE pegawai SET unit_id = 3 WHERE id = 79");
            $result = $stmt_update->execute();
            
            if ($result) {
                echo "<p>✅ <strong>BERHASIL!</strong> Unit ID Yeremia Harefa telah diperbaiki ke 3 (SMK Swasta Pembda Nias)</p>";
            } else {
                echo "<p>❌ Gagal update unit_id</p>";
            }
        } else {
            echo "<p>✅ Unit ID sudah benar (3)</p>";
        }
        
        // 3. Cek penugasan
        echo "<h4>3. Cek Penugasan:</h4>";
        $stmt_penugasan = $pdo->prepare("
            SELECT pen.id, pen.tahun_pelajaran, pen.jam_mengajar 
            FROM penugasan pen 
            WHERE pen.pegawai_id = 79 AND pen.tahun_pelajaran = '2025/2026'
        ");
        $stmt_penugasan->execute();
        $penugasan = $stmt_penugasan->fetch();
        
        if ($penugasan) {
            echo "<p>✅ Ada penugasan untuk tahun 2025/2026</p>";
            echo "<p>Penugasan ID: " . $penugasan['id'] . "</p>";
            echo "<p>Jam Mengajar: " . $penugasan['jam_mengajar'] . "</p>";
        } else {
            echo "<p>❌ Tidak ada penugasan untuk tahun 2025/2026</p>";
            echo "<p>Perlu buat penugasan baru melalui input_penugasan.php</p>";
        }
        
        // 4. Test query export setelah perbaikan
        echo "<h4>4. Test Query Export Setelah Perbaikan:</h4>";
        $tahun_pelajaran = '2025/2026';
        $where = ["p.unit_id IS NOT NULL"];
        $params = [$tahun_pelajaran];
        $where[] = "u.nama = 'SMK Swasta Pembda Nias'";
        
        $sql = "SELECT DISTINCT p.id as pegawai_id, p.nama as pegawai_nama, pen.id as penugasan_id
                FROM pegawai p
                LEFT JOIN unit u ON p.unit_id = u.id
                LEFT JOIN penugasan pen ON p.id = pen.pegawai_id AND pen.unit_id = u.id AND pen.tahun_pelajaran = ?
                WHERE " . implode(' AND ', $where) . "
                AND p.id = 79";
        
        $stmt_test = $pdo->prepare($sql);
        $stmt_test->execute($params);
        $test_result = $stmt_test->fetch();
        
        if ($test_result) {
            echo "<p>✅ <strong>YEREMIA SEKARANG MUNCUL DI QUERY EXPORT!</strong></p>";
            echo "<p>Pegawai ID: " . $test_result['pegawai_id'] . "</p>";
            echo "<p>Nama: " . $test_result['pegawai_nama'] . "</p>";
            echo "<p>Penugasan ID: " . ($test_result['penugasan_id'] ?? 'NULL') . "</p>";
        } else {
            echo "<p>❌ Yeremia masih tidak muncul di query export</p>";
        }
        
    } else {
        echo "<p>❌ Pegawai dengan ID 79 tidak ditemukan!</p>";
    }
    
    echo "<div style='background: #d4edda; padding: 15px; margin: 20px 0; border-radius: 5px;'>";
    echo "<h4 style='color: #155724; margin: 0;'>✅ PERBAIKAN SELESAI!</h4>";
    echo "<p style='margin: 10px 0 0 0; color: #155724;'>Silakan test export Excel lagi. Yeremia Harefa seharusnya sudah muncul!</p>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<p>❌ Error: " . $e->getMessage() . "</p>";
}
?>
