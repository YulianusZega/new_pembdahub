<?php
/**
 * Diagnose & Fix create.blade.php
 * Upload ke public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/fix_view.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Fix View</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.6}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:12px;border-radius:8px;overflow-x:auto;font-size:12px;white-space:pre-wrap;word-break:break-all}</style></head><body>";
echo "<h1>🔍 Diagnose & Fix create.blade.php</h1>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';
$viewPath = $laravelRoot . '/resources/views/admin/assignments/teaching/create.blade.php';

if (!file_exists($viewPath)) {
    echo "<p class='err'>❌ File tidak ditemukan!</p>";
    die("</body></html>");
}

$content = file_get_contents($viewPath);
$lines = explode("\n", str_replace("\r\n", "\n", $content));

echo "<p class='info'>Total baris: <b>" . count($lines) . "</b> | Ukuran: <b>" . number_format(strlen($content)) . " bytes</b></p>";

// Cari baris yang berisi school_filter, Semua Sekolah, isSuperAdmin, grid-cols-4
$keywords = ['school_filter', 'Semua Sekolah', 'isSuperAdmin', 'grid-cols-4', 'grid-cols-3', 'Pilih Guru'];
echo "<h2>📋 Baris-baris Relevan (sekitar filter sekolah)</h2>";
echo "<pre>";
foreach ($lines as $i => $line) {
    foreach ($keywords as $kw) {
        if (stripos($line, $kw) !== false) {
            $lineNo = $i + 1;
            $prev = isset($lines[$i-1]) ? ($i) . ': ' . htmlspecialchars($lines[$i-1]) . "\n" : '';
            echo "<span class='warn'>→ {$lineNo}: " . htmlspecialchars($line) . "</span>\n";
            break;
        }
    }
}
echo "</pre>";

// Tampilkan baris 60-110 (area grid/filter)
echo "<h2>📄 Baris 60–115 (area form filter)</h2>";
echo "<pre>";
for ($i = 59; $i < min(115, count($lines)); $i++) {
    $lineNo = $i + 1;
    $line = htmlspecialchars($lines[$i]);
    // Highlight baris penting
    if (stripos($lines[$i], 'school') !== false || stripos($lines[$i], 'isSuperAdmin') !== false || stripos($lines[$i], 'grid-cols') !== false) {
        echo "<span class='warn'>{$lineNo}: {$line}</span>\n";
    } else {
        echo "{$lineNo}: {$line}\n";
    }
}
echo "</pre>";

// Cek apakah ada school_filter
$hasSchoolFilter = strpos($content, 'school_filter') !== false;
$hasSemuaSekolah = strpos($content, 'Semua Sekolah') !== false;
$hasSuperAdmin = strpos($content, 'isSuperAdmin') !== false;
$hasGrid4 = strpos($content, 'grid-cols-4') !== false;

echo "<h2>🔎 Status Saat Ini</h2>";
echo "<p class='" . ($hasSchoolFilter ? 'err' : 'ok') . "'>" . ($hasSchoolFilter ? '❌' : '✅') . " school_filter: " . ($hasSchoolFilter ? 'MASIH ADA' : 'TIDAK ADA') . "</p>";
echo "<p class='" . ($hasSemuaSekolah ? 'err' : 'ok') . "'>" . ($hasSemuaSekolah ? '❌' : '✅') . " Semua Sekolah: " . ($hasSemuaSekolah ? 'MASIH ADA' : 'TIDAK ADA') . "</p>";
echo "<p class='" . ($hasSuperAdmin ? 'warn' : 'ok') . "'>" . ($hasSuperAdmin ? '⚠️' : '✅') . " isSuperAdmin block: " . ($hasSuperAdmin ? 'ADA' : 'TIDAK ADA') . "</p>";
echo "<p class='" . ($hasGrid4 ? 'err' : 'ok') . "'>" . ($hasGrid4 ? '❌' : '✅') . " grid-cols-4: " . ($hasGrid4 ? 'MASIH ADA' : 'TIDAK ADA') . "</p>";

// Jika masih ada filter, coba fix dengan pendekatan lebih agresif
if ($hasSchoolFilter || $hasSemuaSekolah || $hasGrid4) {
    echo "<h2>🔧 Menerapkan Fix Agresif</h2>";
    
    $original = $content;
    $patched = false;
    
    // Strategi 1: Hapus semua blok @if yang mengandung school_filter
    $result = preg_replace(
        '/@if\s*\([^)]*isSuperAdmin[^)]*\)\s*\n\s*<div[^>]*grid-cols-4[^>]*>.*?@endif\s*\n?/s',
        '<div class="grid grid-cols-1 md:grid-cols-3 gap-6">' . "\n",
        $content
    );
    if ($result && $result !== $content) {
        $content = $result;
        $patched = true;
        echo "<p class='ok'>✅ Strategi 1 (regex grid-cols-4) berhasil!</p>";
    }
    
    // Strategi 2: Jika masih ada school_filter
    if (strpos($content, 'school_filter') !== false) {
        // Cari dan hapus select#school_filter beserta wrapper div-nya
        $result2 = preg_replace(
            '/<div>\s*<label[^>]*>.*?<\/select>\s*<p[^>]*>.*?<\/p>\s*<\/div>\s*/s',
            '',
            $content,
            1
        );
        if ($result2 && strpos($result2, 'school_filter') === false) {
            $content = $result2;
            $patched = true;
            echo "<p class='ok'>✅ Strategi 2 (hapus div select school_filter) berhasil!</p>";
        }
    }
    
    // Strategi 3: Hapus baris yang mengandung school_filter dan Semua Sekolah
    if (strpos($content, 'school_filter') !== false || strpos($content, 'Semua Sekolah') !== false) {
        $lines2 = explode("\n", $content);
        $newLines = [];
        $skipUntil = -1;
        
        for ($i = 0; $i < count($lines2); $i++) {
            $line = $lines2[$i];
            
            // Deteksi awal blok school_filter
            if ($i >= $skipUntil && (strpos($line, 'school_filter') !== false || strpos($line, 'Semua Sekolah') !== false)) {
                // Cari ke atas untuk menemukan awal <div>
                $startDiv = $i;
                for ($j = $i - 1; $j >= max(0, $i - 20); $j--) {
                    if (trim($lines2[$j]) === '<div>' || preg_match('/^\s*<div\s*>/', $lines2[$j])) {
                        $startDiv = $j;
                        break;
                    }
                }
                
                // Cari ke bawah untuk menemukan </div> penutup
                $depth = 0;
                $endDiv = $i;
                for ($k = $startDiv; $k < min($i + 30, count($lines2)); $k++) {
                    $depth += substr_count($lines2[$k], '<div');
                    $depth -= substr_count($lines2[$k], '</div>');
                    if ($depth <= 0 && $k > $startDiv) {
                        $endDiv = $k;
                        break;
                    }
                }
                
                // Skip baris dari startDiv sampai endDiv
                for ($m = $startDiv; $m <= $endDiv; $m++) {
                    if (isset($newLines[$m])) unset($newLines[$m]);
                }
                $skipUntil = $endDiv + 1;
                continue;
            }
            
            if ($i < $skipUntil) continue;
            $newLines[] = $line;
        }
        
        $result3 = implode("\n", $newLines);
        if (strpos($result3, 'school_filter') === false && strpos($result3, 'Semua Sekolah') === false) {
            $content = $result3;
            $patched = true;
            echo "<p class='ok'>✅ Strategi 3 (line-by-line div removal) berhasil!</p>";
        } else {
            echo "<p class='err'>❌ Strategi 3 gagal.</p>";
        }
    }
    
    // Ganti grid-cols-4 dengan grid-cols-3 jika masih ada
    if (strpos($content, 'grid-cols-4') !== false) {
        $content = str_replace('md:grid-cols-4', 'md:grid-cols-3', $content);
        echo "<p class='ok'>✅ grid-cols-4 → grid-cols-3 diganti.</p>";
        $patched = true;
    }
    
    if ($patched) {
        file_put_contents($viewPath, $content);
        echo "<p class='ok'>✅ File disimpan!</p>";
        
        // Verifikasi akhir
        $finalContent = file_get_contents($viewPath);
        $stillHas = strpos($finalContent, 'school_filter') !== false || strpos($finalContent, 'Semua Sekolah') !== false;
        echo "<p class='" . ($stillHas ? 'err' : 'ok') . "'>" . ($stillHas ? '❌ Masih ada filter sekolah!' : '✅ Filter sekolah berhasil dihapus!') . "</p>";
    } else {
        echo "<p class='err'>❌ Semua strategi gagal. Lihat baris relevan di atas untuk debug manual.</p>";
    }
} else {
    echo "<h2>✅ View Sudah Bersih</h2>";
    echo "<p class='ok'>✅ Tidak ada school_filter atau Semua Sekolah di view. Tidak perlu fix!</p>";
}

// Clear view cache
$viewCacheDir = $laravelRoot . '/storage/framework/views/';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ $cleared view cache dihapus.</p>";
}

echo "<hr><p class='warn'>⚠️ Hapus fix_view.php setelah selesai!</p>";
echo "</body></html>";
