<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/recover_app.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    
    // Add app directory
    $dir = new RecursiveDirectoryIterator(__DIR__ . '/app');
    $iterator = new RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $relativePath = str_replace(__DIR__ . '/', '', $file->getPathname());
            // fix windows slashes
            $relativePath = str_replace('\\', '/', $relativePath);
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }
    
    // Add routes directory
    $dir = new RecursiveDirectoryIterator(__DIR__ . '/routes');
    $iterator = new RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $relativePath = str_replace(__DIR__ . '/', '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    // Add views profile
    $dir = new RecursiveDirectoryIterator(__DIR__ . '/resources/views/profile');
    $iterator = new RecursiveIteratorIterator($dir);
    foreach ($iterator as $file) {
        if (!$file->isDir()) {
            $relativePath = str_replace(__DIR__ . '/', '', $file->getPathname());
            $relativePath = str_replace('\\', '/', $relativePath);
            $zip->addFile($file->getPathname(), $relativePath);
        }
    }

    $zip->close();
    echo "RECOVERY_ZIP_CREATED_SUCCESSFULLY\n";
} else {
    echo "FAILED\n";
}
