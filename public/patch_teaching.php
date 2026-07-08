<?php
/**
 * Emergency File Patcher - Teaching Assignment Fix
 * Upload file ini ke public_html/ di Hostinger, akses sekali, lalu HAPUS.
 * Akses: https://perguruanpembda.com/patch_teaching.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

$base = __DIR__ . '/../';

echo "<html><head><title>Patch Teaching Fix</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}h1{color:#bb86fc}</style></head><body>";
echo "<h1>🔧 Emergency Patch: Teaching Assignment</h1>";

// ==========================================
// FILE 1: Controller - TeachingAssignmentController.php
// ==========================================
$controllerPath = $base . 'app/Http/Controllers/Admin/TeachingAssignmentController.php';
$controllerContent = file_get_contents($controllerPath);

// Cek apakah sudah dipatch (ada tanda baru)
if (strpos($controllerContent, 'Prioritas: (1) teacher') !== false) {
    echo "<p class='ok'>✅ Controller sudah dipatch sebelumnya.</p>";
} else {
    // Patch: ganti logika school_id
    $old = '$teacherId = $request->teacher_id;
        $selectedSchoolId = $request->school_id ?? ($selectedTeacher ? $selectedTeacher->school_id : null);';
    
    // Coba cari pola lama yang mungkin ada
    $patterns = [
        // Pattern dari kode asli sebelum semua patch
        "\$teacherId = \$request->teacher_id;\n        \$selectedSchoolId = \$request->school_id;",
        "\$teacherId = \$request->teacher_id;\n        \$selectedTeacher = \$teacherId ? Teacher::with('school')->find(\$teacherId) : null;\n        \$selectedSchoolId = \$request->school_id ?? (\$selectedTeacher ? \$selectedTeacher->school_id : null);",
    ];
    
    echo "<p class='info'>ℹ️ Memeriksa versi controller yang ter-deploy...</p>";
    
    if (strpos($controllerContent, '$selectedTeacher = $teacherId ? Teacher::with') !== false) {
        echo "<p class='ok'>✅ Controller sudah versi terbaru (e3bb3a9+).</p>";
        
        // Cek apakah ada school fix
        if (strpos($controllerContent, "? \$selectedTeacher->school_id") !== false) {
            echo "<p class='ok'>✅ School fix (378ae12) sudah ada di controller.</p>";
        } else {
            echo "<p class='err'>❌ School fix belum ada. Menerapkan patch...</p>";
            // Apply school fix
            $oldPattern = "\$selectedSchoolId = \$request->school_id ?? (\$selectedTeacher ? \$selectedTeacher->school_id : null);";
            $newPattern = "// Prioritas: (1) teacher's school_id, (2) explicit school_id from request\n        // Gunakan filled() bukan ?? agar empty string \"\" dari URL tidak menimpa fallback\n        \$selectedSchoolId = \$selectedTeacher\n            ? \$selectedTeacher->school_id\n            : (\$request->filled('school_id') ? \$request->school_id : null);";
            
            if (strpos($controllerContent, $oldPattern) !== false) {
                $controllerContent = str_replace($oldPattern, $newPattern, $controllerContent);
                file_put_contents($controllerPath, $controllerContent);
                echo "<p class='ok'>✅ School fix berhasil diterapkan ke controller!</p>";
            } else {
                echo "<p class='err'>❌ Pola tidak ditemukan. Controller mungkin versi berbeda.</p>";
            }
        }
    } else {
        echo "<p class='err'>❌ Controller adalah versi LAMA (sebelum e3bb3a9). Perlu Git pull lengkap.</p>";
    }
}

// ==========================================
// FILE 2: View - create.blade.php
// ==========================================
$viewPath = $base . 'resources/views/admin/assignments/teaching/create.blade.php';

if (!file_exists($viewPath)) {
    echo "<p class='err'>❌ File create.blade.php tidak ditemukan di: $viewPath</p>";
} else {
    $viewContent = file_get_contents($viewPath);
    
    if (strpos($viewContent, 'school_filter') === false && strpos($viewContent, 'Semua Sekolah') === false) {
        echo "<p class='ok'>✅ View create.blade.php sudah dipatch (tidak ada filter sekolah).</p>";
    } else {
        echo "<p class='info'>ℹ️ View masih mengandung filter sekolah. Menerapkan patch...</p>";
        
        // Hapus seluruh blok school filter (SuperAdmin 4-column grid)
        // Pattern untuk grid 4 kolom dengan school filter
        $oldGrid4 = '@if(auth()->user()->isSuperAdmin())
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- School Filter -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-school mr-1"></i> Sekolah
                        </label>
                        <select id="school_filter" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            <option value="">-- Semua Sekolah --</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ ($selectedSchoolId ?? \'\') == $school->id ? \'selected\' : \'\' }}>
                                    {{ $school->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Filter daftar guru berdasarkan sekolah</p>
                    </div>
                @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @endif';
        
        $newGrid3 = '<div class="grid grid-cols-1 md:grid-cols-3 gap-6">';
        
        if (strpos($viewContent, 'school_filter') !== false) {
            // Coba replace dengan regex yang lebih fleksibel
            $patched = preg_replace(
                '/@if\(auth\(\)->user\(\)->isSuperAdmin\(\)\)\s*<div class="grid grid-cols-1 md:grid-cols-4 gap-6">.*?@endif/s',
                '<div class="grid grid-cols-1 md:grid-cols-3 gap-6">',
                $viewContent
            );
            
            if ($patched !== $viewContent) {
                file_put_contents($viewPath, $patched);
                echo "<p class='ok'>✅ School filter berhasil dihapus dari create.blade.php!</p>";
            } else {
                echo "<p class='err'>❌ Regex tidak cocok. Mencoba pendekatan lain...</p>";
                
                // Pendekatan manual: cari dan replace baris per baris
                $lines = explode("\n", $viewContent);
                $newLines = [];
                $skip = false;
                $skipUntil = '';
                $replaced = false;
                
                for ($i = 0; $i < count($lines); $i++) {
                    $line = $lines[$i];
                    
                    if (!$replaced && strpos($line, 'isSuperAdmin()') !== false && isset($lines[$i+1]) && strpos($lines[$i+1], 'grid-cols-4') !== false) {
                        // Mulai skip
                        $skip = true;
                        echo "<p class='info'>ℹ️ Menemukan blok SuperAdmin di baris $i, memulai penghapusan...</p>";
                        continue;
                    }
                    
                    if ($skip) {
                        // Cari @endif yang menutup blok ini
                        if (trim($line) === '@endif') {
                            $skip = false;
                            $replaced = true;
                            // Ganti dengan grid 3 kolom
                            $newLines[] = '                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">';
                            echo "<p class='ok'>✅ Blok school filter dihapus (baris $i)!</p>";
                        }
                        continue;
                    }
                    
                    $newLines[] = $line;
                }
                
                if ($replaced) {
                    file_put_contents($viewPath, implode("\n", $newLines));
                    echo "<p class='ok'>✅ create.blade.php berhasil dipatch via line-by-line!</p>";
                } else {
                    echo "<p class='err'>❌ Gagal menemukan blok untuk dihapus. Cek manual.</p>";
                }
            }
        } else {
            echo "<p class='ok'>✅ school_filter tidak ditemukan, view mungkin sudah benar.</p>";
        }
    }
}

// ==========================================
// Clear view cache
// ==========================================
echo "<h2 style='color:#03dac6'>Membersihkan Cache Views...</h2>";
$viewCacheDir = $base . 'storage/framework/views/';
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $file) {
        if (@unlink($file)) $cleared++;
    }
    echo "<p class='ok'>✅ $cleared compiled view cache dihapus.</p>";
}

echo "<hr><p class='err'>⚠️ PENTING: Hapus file patch_teaching.php setelah selesai!</p>";
echo "</body></html>";
