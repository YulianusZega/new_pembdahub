<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "<h1>🔍 Diagnostik Fitur LMS</h1>";

// 1. Cek Ketersediaan File Kode
$file = __DIR__ . '/../app/Http/Controllers/Guru/DashboardController.php';
if (file_exists($file)) {
    $content = file_get_contents($file);
    if (strpos($content, 'LmsCourse') !== false || strpos($content, 'courses') !== false) {
        echo "<h2 style='color:green'>✅ KODE AMAN: File DashboardController memiliki logika LMS Course!</h2>";
    } else {
        echo "<h2 style='color:red'>❌ KODE HILANG: File DashboardController TIDAK memiliki logika LMS Course (Ini adalah kode lama dari GitHub).</h2>";
    }
} else {
    echo "<h2 style='color:red'>❌ File DashboardController tidak ditemukan!</h2>";
}

// 2. Cek Ketersediaan Data di Database
try {
    $count = \DB::table('lms_courses')->count();
    echo "<h2 style='color:green'>✅ DATABASE AMAN: Ditemukan <b>$count</b> data Course di dalam Database.</h2>";
} catch (\Exception $e) {
    echo "<h2 style='color:red'>❌ DATABASE ERROR: Tabel lms_courses tidak ditemukan atau error! (" . $e->getMessage() . ")</h2>";
}

echo "<hr><p>Mohon fotokan/copy hasil di atas dan kirimkan ke saya.</p>";
