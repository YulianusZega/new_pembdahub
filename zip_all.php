<?php
$zip = new ZipArchive();
if ($zip->open(__DIR__ . '/app_update.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $dirs = ['app', 'routes', 'resources'];
    foreach ($dirs as $d) {
        $dir = new RecursiveDirectoryIterator(__DIR__ . '/' . $d);
        $iterator = new RecursiveIteratorIterator($dir);
        foreach ($iterator as $file) {
            if (!$file->isDir()) {
                $relativePath = str_replace(__DIR__ . '/', '', $file->getPathname());
                $relativePath = str_replace('\\', '/', $relativePath);
                $zip->addFile($file->getPathname(), $relativePath);
            }
        }
    }
    $zip->close();
    echo "ZIP_CREATED_SUCCESSFULLY\n";
} else {
    echo "FAILED\n";
}
