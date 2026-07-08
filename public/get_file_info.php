<?php
if (($_GET['secret'] ?? '') !== 'pembda99') {
    die('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== FILE DIAGNOSTICS ON SERVER ===\n\n";

$customPath = $_GET['path'] ?? null;

if ($customPath) {
    // Sanitize path to prevent directory traversal
    $customPath = str_replace(['..', '\\'], ['', '/'], $customPath);
    $path = __DIR__ . '/../' . $customPath;
    if (file_exists($path)) {
        echo "File: $customPath\n";
        echo "  Size: " . filesize($path) . " bytes\n";
        echo "  MD5: " . md5_file($path) . "\n";
        echo "  Last Modified: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
        if (isset($_GET['show']) && $_GET['show'] === 'yes') {
            echo "\n--- CONTENT ---\n";
            echo file_get_contents($path);
        }
    } else {
        echo "File: $customPath => NOT FOUND\n";
    }
} else {
    $files = [
        'app/Http/Controllers/Guru/DashboardController.php',
        'app/Http/Controllers/Guru/NilaiController.php',
        'resources/views/guru/nilai-input.blade.php',
        'resources/views/guru/nilai.blade.php',
    ];

    foreach ($files as $f) {
        $path = __DIR__ . '/../' . $f;
        if (file_exists($path)) {
            echo "File: $f\n";
            echo "  Size: " . filesize($path) . " bytes\n";
            echo "  MD5: " . md5_file($path) . "\n";
            echo "  Last Modified: " . date('Y-m-d H:i:s', filemtime($path)) . "\n";
        } else {
            echo "File: $f => NOT FOUND\n";
        }
        echo "---\n";
    }
}
