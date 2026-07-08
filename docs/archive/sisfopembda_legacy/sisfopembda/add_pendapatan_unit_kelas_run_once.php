<?php
// One-time creation script for pendapatan_unit_kelas table if missing
require_once 'config.php';
try {
    $pdo->query("SELECT 1 FROM pendapatan_unit_kelas LIMIT 1");
    echo "Table pendapatan_unit_kelas already exists\n";
} catch (Exception $e) {
    echo "Creating table pendapatan_unit_kelas...\n";
    $sql = "CREATE TABLE pendapatan_unit_kelas (\n      id INT AUTO_INCREMENT PRIMARY KEY,\n      unit_id INT NOT NULL,\n      tahun_pelajaran VARCHAR(15) NOT NULL,\n      kelas VARCHAR(10) NOT NULL,\n      jumlah_siswa INT NOT NULL DEFAULT 0,\n      uang_sekolah BIGINT NOT NULL DEFAULT 0,\n      updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,\n      UNIQUE KEY uq_unit_tahun_kelas (unit_id, tahun_pelajaran, kelas),\n      CONSTRAINT fk_puk_unit FOREIGN KEY (unit_id) REFERENCES unit(id) ON DELETE CASCADE\n    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $pdo->exec($sql);
    echo "Table created.\n";
}
