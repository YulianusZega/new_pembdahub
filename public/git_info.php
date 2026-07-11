<?php
header('Content-Type: text/plain');

$gitDir = __DIR__ . '/../.git';

if (!file_exists($gitDir)) {
    echo "Error: .git directory not found at " . $gitDir . "\n";
    exit;
}

// 1. Read local commit hash
$headFile = $gitDir . '/HEAD';
if (file_exists($headFile)) {
    $head = trim(file_get_contents($headFile));
    echo "HEAD: " . $head . "\n";
    if (strpos($head, 'ref:') === 0) {
        $refPath = trim(substr($head, 4));
        $refFile = $gitDir . '/' . $refPath;
        if (file_exists($refFile)) {
            echo "Current Commit: " . trim(file_get_contents($refFile)) . "\n";
        } else {
            echo "Ref file not found: " . $refFile . "\n";
        }
    }
}

// 2. Try executing git status
if (function_exists('shell_exec')) {
    echo "\n=== Shell Exec Git Status ===\n";
    // Change directory to project root and run git status
    $output = shell_exec('cd ' . escapeshellarg(__DIR__ . '/..') . ' && git status 2>&1');
    echo $output . "\n";
    
    echo "\n=== Shell Exec Git Log (Last 3) ===\n";
    $log = shell_exec('cd ' . escapeshellarg(__DIR__ . '/..') . ' && git log -n 3 --oneline 2>&1');
    echo $log . "\n";
} else {
    echo "\nshell_exec is disabled on this server.\n";
}
