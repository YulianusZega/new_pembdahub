<?php
require_once 'config.php';

try {
    // SQL statement to add the 'prioritas' column to the 'jabatan' table
    // It defaults to 7, which is a low priority.
    $sql = "ALTER TABLE jabatan ADD COLUMN prioritas INT(11) DEFAULT 7";

    // Execute the SQL statement
    $pdo->exec($sql);

    echo "<h1>Sukses!</h1>";
    echo "<h2>Kolom 'prioritas' berhasil ditambahkan ke tabel 'jabatan'.</h2>";
    echo "<p>Nilai default untuk prioritas telah diatur ke 7 (prioritas rendah).</p>";
    echo "<p>Anda sekarang dapat mencoba kembali fitur ekspor Excel. Seharusnya sekarang berfungsi dengan benar.</p>";
    echo "<p style='color:grey;'>File ini ('add_prioritas_column.php') sekarang aman untuk dihapus.</p>";

} catch (PDOException $e) {
    // Handle potential errors, such as if the column already exists
    echo "<h1>Error atau Peringatan</h1>";
    echo "<p>Terjadi kesalahan saat mencoba menambahkan kolom 'prioritas'.</p>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<p><b>Penting:</b> Ini kemungkinan besar terjadi karena kolom 'prioritas' <b>sudah ada</b>. Jika demikian, tidak ada yang perlu dikhawatirkan. Silakan coba lagi fitur ekspor Excel.</p>";
}
?>
