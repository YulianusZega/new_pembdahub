<?php
$base = __DIR__ . '/../';

if (isset($_GET['restore'])) {
    $target = $_GET['restore']; // e.g. app.3445
    // Validasi target
    if (preg_match('/^(app|routes|resources)\.\d+$/', $target)) {
        $type = explode('.', $target)[0];
        
        // Singkirkan yang saat ini
        if (is_dir($base . $type)) {
            rename($base . $type, $base . 'rusak_' . $type . '_' . time());
        }
        
        // Pulihkan
        if (rename($base . $target, $base . $type)) {
            echo "<h2 style='color:green'>✅ Berhasil memulihkan $target menjadi $type!</h2>";
        } else {
            echo "<h2 style='color:red'>❌ Gagal memulihkan $target!</h2>";
        }
    }
    echo "<a href='?'>Kembali</a>";
    exit;
}

echo "<h1>Penyelamat Kode LMS Bapak/Ibu</h1>";
echo "<p>Karena ekstrak tadi terjadi beberapa kali, skrip saya yang sebelumnya ternyata memulihkan folder yang salah (memulihkan folder sesudah ter-reset, bukan sebelum ter-reset).</p>";
echo "<p>Silakan klik tombol <b>Pulihkan Ini</b> pada folder cadangan yang <b>Jam Pembuatannya Paling Lama / Paling Awal</b> di bawah ini.</p>";

$dirs = ['app', 'routes', 'resources'];
foreach ($dirs as $d) {
    echo "<h3>Cadangan untuk folder <b>$d</b>:</h3><ul>";
    $backups = glob($base . $d . '.*', GLOB_ONLYDIR);
    
    // Urutkan dari yang tertua ke terbaru
    usort($backups, function($a, $b) {
        return filemtime($a) - filemtime($b);
    });
    
    if (empty($backups)) {
        echo "<li>Tidak ada cadangan.</li>";
    }
    
    foreach ($backups as $b) {
        $name = basename($b);
        $time = date('Y-m-d H:i:s', filemtime($b));
        echo "<li><b>$name</b> (Dibuat pada: $time) 👉 <a href='?restore=$name'><button style='padding:5px 10px; background:blue; color:white; border:none; border-radius:3px; cursor:pointer'>Pulihkan Ini</button></a></li>";
    }
    echo "</ul><hr>";
}
?>
