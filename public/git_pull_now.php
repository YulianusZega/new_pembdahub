<?php
/**
 * Final: Update server ke commit terbaru dari GitHub
 * Akses: https://perguruanpembda.com/git_pull_now.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') { http_response_code(403); die('Forbidden'); }

header('Content-Type: text/plain; charset=utf-8');

$root = '/home/u474310197/domains/perguruanpembda.com/public_html/pembdahub';

echo "=== GIT PULL & UPDATE ===\n\n";

// 1. Fetch latest
echo "--- 1. Fetch ---\n";
echo shell_exec("GIT_SSH_COMMAND='ssh -o StrictHostKeyChecking=no' git -C {$root} fetch origin 2>&1") . "\n";

// 2. Show before
echo "--- 2. Sebelum Update ---\n";
echo "HEAD: " . trim(shell_exec("git -C {$root} rev-parse --short HEAD 2>&1")) . "\n";
echo "origin/main: " . trim(shell_exec("git -C {$root} rev-parse --short origin/main 2>&1")) . "\n\n";

// 3. Reset to latest
echo "--- 3. Update ke origin/main ---\n";
echo shell_exec("git -C {$root} reset --hard origin/main 2>&1") . "\n";

// 4. Show after
echo "--- 4. Sesudah Update ---\n";
echo "HEAD: " . trim(shell_exec("git -C {$root} rev-parse --short HEAD 2>&1")) . "\n";
echo shell_exec("git -C {$root} log --oneline -5 2>&1") . "\n";

// 5. Clear cache
echo "--- 5. Clear Cache ---\n";
$viewDir = "{$root}/storage/framework/views/";
$cleared = 0;
if (is_dir($viewDir)) {
    foreach (glob($viewDir . '*.php') as $f) { if (@unlink($f)) $cleared++; }
}
echo "Views cleared: {$cleared}\n";
foreach (['config.php','routes-v7.php','packages.php','services.php','events.php'] as $cf) {
    $fp = "{$root}/bootstrap/cache/{$cf}";
    if (file_exists($fp) && @unlink($fp)) echo "Deleted: bootstrap/cache/{$cf}\n";
}

// 6. Verify
echo "\n--- 6. Verifikasi ---\n";
$checks = [
    ['app/Http/Controllers/Admin/ScheduleGridController.php', '$schools->first() ? $schools->first()->id : null', 'ScheduleGrid fix'],
    ['app/Http/Controllers/Admin/TeachingAssignmentController.php', 'Prioritas: (1) teacher', 'TeachingAssignment fix'],
    ['app/Http/Controllers/Admin/TimeSlotController.php', '$schools->first() ? $schools->first()->id : null', 'TimeSlot fix'],
];
foreach ($checks as [$file, $marker, $label]) {
    $path = "{$root}/{$file}";
    $ok = file_exists($path) && strpos(file_get_contents($path), $marker) !== false;
    echo ($ok ? 'OK' : 'FAIL') . " - {$label}\n";
}

echo "\n=== SELESAI! hPanel Git Deploy seharusnya bisa digunakan sekarang ===\n";
echo "Test: https://perguruanpembda.com/admin/dashboard\n";
