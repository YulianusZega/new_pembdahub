<?php
/**
 * Check commit yang aktif di server
 * Akses: https://perguruanpembda.com/check_commit.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Access denied.'); }

header('Content-Type: text/plain');
$root = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';

echo "=== SERVER GIT STATUS ===\n\n";

// Current commit
echo "--- Current HEAD ---\n";
echo shell_exec("git -C {$root} log --oneline -5 2>&1") . "\n";

// Remote URL
echo "--- Remote URL ---\n";
echo shell_exec("git -C {$root} remote get-url origin 2>&1") . "\n";

// Modified files
echo "--- Modified Files ---\n";
echo shell_exec("git -C {$root} status --short 2>&1") . "\n";

// Check key files
echo "--- File Verification ---\n";
$checks = [
    ['file' => 'app/Http/Controllers/Admin/ScheduleGridController.php',
     'marker' => '$schools->first() ? $schools->first()->id : null',
     'label' => 'ScheduleGridController (default school fix)'],
    ['file' => 'app/Http/Controllers/Admin/TeachingAssignmentController.php',
     'marker' => 'Prioritas: (1) teacher',
     'label' => 'TeachingAssignmentController (school auto-detect)'],
    ['file' => 'app/Http/Controllers/Admin/TimeSlotController.php',
     'marker' => '$schools->first() ? $schools->first()->id : null',
     'label' => 'TimeSlotController (default school fix)'],
    ['file' => 'resources/views/admin/assignments/teaching/create.blade.php',
     'marker' => 'school_filter',
     'label' => 'create.blade.php (school_filter HARUS TIDAK ADA)',
     'invert' => true],
    ['file' => 'resources/views/admin/assignments/teaching/create.blade.php',
     'marker' => 'Informasi Pilihan Mata Pelajaran',
     'label' => 'create.blade.php (info banner kompetensi)'],
];

foreach ($checks as $c) {
    $path = "{$root}/{$c['file']}";
    if (!file_exists($path)) {
        echo "❌ NOT FOUND: {$c['label']}\n";
        continue;
    }
    $content = file_get_contents($path);
    $found = strpos($content, $c['marker']) !== false;
    $invert = $c['invert'] ?? false;
    $ok = $invert ? !$found : $found;
    echo ($ok ? '✅' : '❌') . " {$c['label']}\n";
}
