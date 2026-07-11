<?php
header('Content-Type: text/plain');
$filePath = __DIR__ . '/../resources/views/forum/show.blade.php';

if (file_exists($filePath)) {
    echo "File exists.\n";
    echo "Modified Time: " . date("Y-m-d H:i:s", filemtime($filePath)) . "\n";
    echo "Size: " . filesize($filePath) . " bytes\n";
    echo "\n=== COMPOSE BAR SECTION ===\n";
    $lines = file($filePath);
    // Print around the compose bar (lines 350 to 420)
    for ($i = 360; $i < min(430, count($lines)); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
} else {
    echo "File not found at: " . $filePath . "\n";
}
