<?php
if (($_GET['secret'] ?? '') !== 'pembda99') {
    die('Forbidden');
}

header('Content-Type: text/plain; charset=utf-8');

$file = __DIR__ . '/../app/Http/Controllers/Guru/FinalProjectTeacherController.php';

if (file_exists($file)) {
    echo "=== FILE EXISTS in production ===\n";
    echo file_get_contents($file);
} else {
    echo "=== FILE NOT FOUND at: $file ===\n";
}
