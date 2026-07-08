<?php
if (($_GET['secret'] ?? '') !== 'pembda99') {
    http_response_code(403);
    echo 'Access denied.';
    exit;
}

echo "<pre style='background:#111;color:#0f0;padding:20px;font-family:monospace;'>";

// 1. Delete all compiled Blade views
$viewsDir = __DIR__ . '/../storage/framework/views/';
if (is_dir($viewsDir)) {
    $files = glob($viewsDir . '*.php');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
            $count++;
        }
    }
    echo "✅ Deleted {$count} compiled Blade view files.\n";
} else {
    echo "❌ Views directory not found: {$viewsDir}\n";
}

// 2. Delete route cache
$routeCache = __DIR__ . '/../bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    @unlink($routeCache);
    echo "✅ Deleted route cache.\n";
} else {
    echo "ℹ️ No route cache found.\n";
}

// 3. Reset OPcache
if (function_exists('opcache_reset')) {
    @opcache_reset();
    echo "✅ OPcache reset.\n";
}

echo "\n🎉 DONE! All view & route caches cleared.\n";
echo "</pre>";
