<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>🔍 Deteksi Error 500</h1>";

// Coba panggil Laravel untuk melihat errornya secara langsung
try {
    require __DIR__ . '/../vendor/autoload.php';
    $app = require_once __DIR__ . '/../bootstrap/app.php';
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    $response = $kernel->handle(
        $request = Illuminate\Http\Request::capture()
    );
    echo "<h2 style='color:green'>Aneh, sistem mendeteksi tidak ada error saat skrip ini dijalankan.</h2>";
} catch (\Throwable $e) {
    echo "<h2 style='color:red'>Ditemukan Error Utama:</h2>";
    echo "<div style='background:#fee; padding:15px; border-left:5px solid red; font-family:monospace;'>";
    echo "<b>Pesan:</b> " . $e->getMessage() . "<br><br>";
    echo "<b>File:</b> " . $e->getFile() . " (Baris " . $e->getLine() . ")<br>";
    echo "</div>";
}
?>
