<?php
echo "<h2>MENGHAPUS SEMUA FILE DEBUG/TEST/EXPORT</h2>";

$patterns = [
    'debug_*.php',
    'debug_*.txt', 
    'debug_*.js',
    'test_*.php',
    'test_*.html',
    'fix_*.php',
    'comprehensive_*.php',
    'final_*.php',
    'hunt_*.php',
    'deep_*.php',
    'step_*.php',
    'raw_*.php',
    'verify_*.php',
    'demo_*.php',
    'export_excel_*.php',
    '*.xls',
    '*.xlsx'
];

$deleted = [];
$failed = [];

foreach($patterns as $pattern) {
    $files = glob($pattern);
    foreach($files as $file) {
        if(file_exists($file)) {
            if(unlink($file)) {
                $deleted[] = $file;
                echo "<p style='color: green;'>✅ Deleted: $file</p>";
            } else {
                $failed[] = $file;
                echo "<p style='color: red;'>❌ Failed to delete: $file</p>";
            }
        }
    }
}

echo "<h3>SUMMARY:</h3>";
echo "<p><strong>Files deleted:</strong> " . count($deleted) . "</p>";
echo "<p><strong>Files failed:</strong> " . count($failed) . "</p>";

if(count($deleted) > 0) {
    echo "<h4>Deleted files:</h4>";
    echo "<ul>";
    foreach($deleted as $file) {
        echo "<li>$file</li>";
    }
    echo "</ul>";
}

if(count($failed) > 0) {
    echo "<h4 style='color: red;'>Failed files:</h4>";
    echo "<ul>";
    foreach($failed as $file) {
        echo "<li style='color: red;'>$file</li>";
    }
    echo "</ul>";
}

echo "<p><strong>Cleaning completed!</strong></p>";

// Auto-delete this script too
echo "<p>Deleting this cleanup script...</p>";
unlink(__FILE__);
?>
