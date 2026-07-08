<?php
/**
 * Patch: Fix Default School Selection for SuperAdmin in Schedule & TimeSlot Controllers
 * Upload ke public_html/pembdahub/public/
 * Akses: https://perguruanpembda.com/patch_schedules.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

echo "<html><head><title>Patch Schedules & Time Slots</title>";
echo "<style>body{font-family:monospace;background:#1a1a2e;color:#e0e0e0;padding:20px;line-height:1.8}.ok{color:#00e676}.err{color:#ff5252}.info{color:#40c4ff}.warn{color:#ffc107}h1{color:#bb86fc}h2{color:#03dac6}pre{background:#16213e;padding:12px;border-radius:8px;overflow-x:auto;font-size:12px}</style></head><body>";
echo "<h1>🔧 Patch: Fix Default School SuperAdmin (Jadwal & Jam Pelajaran)</h1>";

$laravelRoot = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';

// 1. Patch ScheduleGridController.php
echo "<h2>1️⃣ ScheduleGridController.php</h2>";
$gridFile = $laravelRoot . '/app/Http/Controllers/Admin/ScheduleGridController.php';

if (!file_exists($gridFile)) {
    echo "<p class='err'>❌ File tidak ditemukan: $gridFile</p>";
} else {
    $content = file_get_contents($gridFile);
    if (strpos($content, '$schools->first() ? $schools->first()->id : null') !== false) {
        echo "<p class='ok'>✅ ScheduleGridController sudah dipatch sebelumnya!</p>";
    } else {
        $oldTarget = "\$selectedSchoolId = \$request->filled('school_id') && \$user->isSuperAdmin()\n            ? \$request->school_id\n            : \$user->school_id;";
        $newReplace = "if (\$user->isSuperAdmin()) {\n            \$selectedSchoolId = \$request->filled('school_id')\n                ? \$request->school_id\n                : (\$schools->first() ? \$schools->first()->id : null);\n        } else {\n            \$selectedSchoolId = \$user->school_id;\n        }";
        
        // Coba exact replace
        $newContent = str_replace($oldTarget, $newReplace, $content);
        if ($newContent === $content) {
            // Coba regex replace jika spasi/newline sedikit berbeda
            $newContent = preg_replace(
                '/\$selectedSchoolId\s*=\s*\$request->filled\(\'school_id\'\)\s*&&\s*\$user->isSuperAdmin\(\)\s*\n?\s*\?\s*\$request->school_id\s*\n?\s*:\s*\$user->school_id\s*;/s',
                $newReplace,
                $content
            );
        }
        
        if ($newContent && $newContent !== $content) {
            file_put_contents($gridFile, $newContent);
            echo "<p class='ok'>✅ Berhasil mempatch ScheduleGridController.php!</p>";
        } else {
            echo "<p class='err'>❌ Gagal menemukan pola di ScheduleGridController.php. Cek manual.</p>";
        }
    }
}

// 2. Patch TimeSlotController.php
echo "<h2>2️⃣ TimeSlotController.php</h2>";
$slotFile = $laravelRoot . '/app/Http/Controllers/Admin/TimeSlotController.php';

if (!file_exists($slotFile)) {
    echo "<p class='err'>❌ File tidak ditemukan: $slotFile</p>";
} else {
    $content = file_get_contents($slotFile);
    $patched = false;
    
    // Fix index method
    if (strpos($content, '$schools->first() ? $schools->first()->id : null') !== false) {
        echo "<p class='ok'>✅ TimeSlotController (index) sudah dipatch!</p>";
    } else {
        $oldTarget = "\$selectedSchoolId = \$request->filled('school_id') && \$user->isSuperAdmin()\n            ? \$request->school_id\n            : \$user->school_id;";
        $newReplace = "if (\$user->isSuperAdmin()) {\n            \$selectedSchoolId = \$request->filled('school_id')\n                ? \$request->school_id\n                : (\$schools->first() ? \$schools->first()->id : null);\n        } else {\n            \$selectedSchoolId = \$user->school_id;\n        }";
        
        $newContent = str_replace($oldTarget, $newReplace, $content);
        if ($newContent === $content) {
            $newContent = preg_replace(
                '/\$selectedSchoolId\s*=\s*\$request->filled\(\'school_id\'\)\s*&&\s*\$user->isSuperAdmin\(\)\s*\n?\s*\?\s*\$request->school_id\s*\n?\s*:\s*\$user->school_id\s*;/s',
                $newReplace,
                $content
            );
        }
        if ($newContent && $newContent !== $content) {
            $content = $newContent;
            $patched = true;
            echo "<p class='ok'>✅ Berhasil mempatch method index di TimeSlotController.php!</p>";
        }
    }

    // Fix create method
    if (strpos($content, '$request->get(\'school_id\', $schools->first() ? $schools->first()->id : null)') !== false) {
        echo "<p class='ok'>✅ TimeSlotController (create) sudah dipatch!</p>";
    } else {
        $oldTarget2 = "\$selectedSchoolId = \$request->get('school_id', \$user->school_id);";
        $newReplace2 = "if (\$user->isSuperAdmin()) {\n            \$selectedSchoolId = \$request->get('school_id', \$schools->first() ? \$schools->first()->id : null);\n        } else {\n            \$selectedSchoolId = \$request->get('school_id', \$user->school_id);\n        }";
        
        $newContent = str_replace($oldTarget2, $newReplace2, $content);
        if ($newContent && $newContent !== $content) {
            $content = $newContent;
            $patched = true;
            echo "<p class='ok'>✅ Berhasil mempatch method create di TimeSlotController.php!</p>";
        }
    }

    if ($patched) {
        file_put_contents($slotFile, $content);
        echo "<p class='ok'>✅ File TimeSlotController.php disimpan!</p>";
    }
}

// 3. Clear Cache
echo "<h2>3️⃣ Bersihkan Cache</h2>";
foreach (['config.php','routes-v7.php','packages.php','services.php'] as $cf) {
    $fp = "$laravelRoot/bootstrap/cache/$cf";
    if (file_exists($fp) && @unlink($fp)) echo "<p class='ok'>✅ Deleted: bootstrap/cache/$cf</p>";
}
$viewCacheDir = "$laravelRoot/storage/framework/views/";
if (is_dir($viewCacheDir)) {
    $files = glob($viewCacheDir . '*.php');
    $cleared = 0;
    foreach ($files as $f) { if (@unlink($f)) $cleared++; }
    echo "<p class='ok'>✅ $cleared view cache dihapus.</p>";
}

echo "<hr><p class='warn'>⚠️ Hapus file patch_schedules.php setelah selesai!</p>";
echo "<p><a href='https://perguruanpembda.com/admin/schedules/grid' target='_blank' style='color:#03dac6'>→ Cek Halaman Jadwal Grid</a></p>";
echo "</body></html>";
