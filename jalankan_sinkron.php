<?php
echo "Memulai sinkronisasi akun siswa...<br>";

// Gunakan cara migrasi_portal yang sudah terbukti bekerja
try {
    $command = "php artisan pembda:sync-students 2>&1";
    $result = shell_exec($command);
    echo "<pre>$result</pre>";
    echo "Selesai!";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
