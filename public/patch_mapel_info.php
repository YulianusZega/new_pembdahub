<?php
/**
 * Patch Info Banner Kompetensi Guru - create.blade.php
 * Upload ke public_html/, akses sekali, lalu HAPUS.
 * Akses: https://perguruanpembda.com/patch_mapel_info.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Patch Mapel Info Banner</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}</style></head><body>";
echo "<h1>🔧 Patch: Info Banner Kompetensi Guru</h1>";

$base = __DIR__ . '/../';
$viewPath = $base . 'resources/views/admin/assignments/teaching/create.blade.php';

if (!file_exists($viewPath)) {
    echo "<p class='err'>❌ File tidak ditemukan: $viewPath</p>";
    die("</body></html>");
}

$content = file_get_contents($viewPath);
echo "<p class='info'>ℹ️ Ukuran file: " . number_format(strlen($content)) . " bytes</p>";
$changed = false;

// ============================================================
// PATCH 1: Tambah info banner sebelum "Section 2: Penugasan Baru"
// ============================================================
$bannerMarker = "@if(\$selectedTeacher)\n        <div class=\"bg-amber-50 border-l-4 border-amber-500";

if (strpos($content, $bannerMarker) !== false) {
    echo "<p class='ok'>✅ Info banner sudah ada. Tidak perlu patch 1.</p>";
} else {
    // Temukan anchor sebelum Section 2
    $anchor = "<!-- Section 2: Penugasan Baru -->";
    if (strpos($content, $anchor) !== false) {
        $banner = "@if(\$selectedTeacher)\n" .
        "        <div class=\"bg-amber-50 border-l-4 border-amber-500 p-5 mb-6 rounded-r-2xl shadow-sm\">\n" .
        "            <div class=\"flex items-start gap-3\">\n" .
        "                <i class=\"fas fa-info-circle text-amber-500 text-2xl mt-0.5\"></i>\n" .
        "                <div class=\"text-sm text-amber-900 leading-relaxed\">\n" .
        "                    <h4 class=\"font-bold text-base mb-1\">💡 Informasi Pilihan Mata Pelajaran</h4>\n" .
        "                    <p class=\"mb-2\">Daftar Mata Pelajaran yang muncul pada pilihan di bawah ini <b>tersaring secara otomatis</b> berdasarkan <b>Kompetensi / Mapel yang Diampu</b> oleh <b>{{ \$selectedTeacher->full_name }}</b>.</p>\n" .
        "                    <p>Apabila mata pelajaran yang ingin Anda tugaskan <b>tidak muncul</b> dalam daftar, silakan tambahkan terlebih dahulu mata pelajaran tersebut ke profil guru yang bersangkutan melalui menu: <a href=\"{{ route('admin.teachers.competencies', \$selectedTeacher->id) }}\" target=\"_blank\" class=\"inline-flex items-center gap-1 font-bold text-amber-900 underline hover:text-amber-700 bg-amber-100 px-2 py-0.5 rounded transition-all\">Kompetensi Guru <i class=\"fas fa-external-link-alt text-xs\"></i></a>.</p>\n" .
        "                </div>\n" .
        "            </div>\n" .
        "        </div>\n" .
        "        @endif\n\n        ";

        $content = str_replace($anchor, $banner . $anchor, $content);
        echo "<p class='ok'>✅ Info banner berhasil ditambahkan sebelum Section 2!</p>";
        $changed = true;
    } else {
        echo "<p class='err'>❌ Anchor '<!-- Section 2 -->' tidak ditemukan.</p>";
    }
}

// ============================================================
// PATCH 2: Tambah hint "Mapel tidak muncul?" di bawah select mapel
// ============================================================
$hintMarker = "Mapel tidak muncul?";

if (strpos($content, $hintMarker) !== false) {
    echo "<p class='ok'>✅ Hint mapel sudah ada. Tidak perlu patch 2.</p>";
} else {
    // Temukan anchor: tutup select mapel (</select> dalam blok subjects)
    $anchor2 = "@endforeach\n                                </select>\n                            </div>";

    $hint = "@endforeach\n" .
            "                                </select>\n" .
            "                                @if(\$selectedTeacher)\n" .
            "                                    <p class=\"mt-1 text-xs text-amber-700\">Mapel tidak muncul? <a href=\"{{ route('admin.teachers.competencies', \$selectedTeacher->id) }}\" target=\"_blank\" class=\"underline font-bold\">Atur Kompetensi Guru</a></p>\n" .
            "                                @endif\n" .
            "                            </div>";

    if (strpos($content, $anchor2) !== false) {
        // Hanya replace 1x (yang pertama = dropdown mapel)
        $content = preg_replace('/' . preg_quote($anchor2, '/') . '/', $hint, $content, 1);
        echo "<p class='ok'>✅ Hint 'Mapel tidak muncul?' berhasil ditambahkan!</p>";
        $changed = true;
    } else {
        echo "<p class='warn'>⚠️ Anchor tutup select tidak ditemukan persis. Mencoba alternatif...</p>";
        // Coba dengan \r\n
        $anchor2b = "@endforeach\r\n                                </select>\r\n                            </div>";
        $hintb = "@endforeach\r\n" .
                "                                </select>\r\n" .
                "                                @if(\$selectedTeacher)\r\n" .
                "                                    <p class=\"mt-1 text-xs text-amber-700\">Mapel tidak muncul? <a href=\"{{ route('admin.teachers.competencies', \$selectedTeacher->id) }}\" target=\"_blank\" class=\"underline font-bold\">Atur Kompetensi Guru</a></p>\r\n" .
                "                                @endif\r\n" .
                "                            </div>";
        if (strpos($content, $anchor2b) !== false) {
            $content = preg_replace('/' . preg_quote($anchor2b, '/') . '/', $hintb, $content, 1);
            echo "<p class='ok'>✅ Hint berhasil ditambahkan (CRLF mode)!</p>";
            $changed = true;
        } else {
            echo "<p class='err'>❌ Anchor tidak ditemukan. Tidak bisa patch hint.</p>";
        }
    }
}

// Save file jika ada perubahan
if ($changed) {
    file_put_contents($viewPath, $content);
    echo "<p class='ok'>✅ File create.blade.php disimpan.</p>";
} else {
    echo "<p class='info'>ℹ️ Tidak ada perubahan yang perlu disimpan.</p>";
}

// Clear view cache
echo "<h2>Membersihkan Cache Views...</h2>";
$viewCacheDir = $base . 'storage/framework/views/';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ $cleared view cache dihapus.</p>";
}

echo "<hr><p style='color:#ff5252;font-weight:bold;'>⚠️ HAPUS file patch_mapel_info.php setelah selesai!</p>";
echo "<p><a href='https://perguruanpembda.com/admin/assignments/teaching/create?teacher_id=295' target='_blank' style='color:#03dac6'>→ Test halaman penugasan mengajar sekarang</a></p>";
echo "</body></html>";
