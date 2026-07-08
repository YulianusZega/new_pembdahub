<?php
$zipFile = __DIR__ . '/public_photos_linux_safe.zip';
$sourceDir = __DIR__ . '/storage/app/public';

if (file_exists($zipFile)) {
    unlink($zipFile);
}

$zip = new ZipArchive();
if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($sourceDir),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $name => $file) {
        if (!$file->isDir()) {
            $filePath = $file->getRealPath();
            $relativePath = substr($filePath, strlen(realpath($sourceDir)) + 1);
            // Paksa ganti backslash \ ke slash / agar aman di Linux Hostinger!
            $relativePath = str_replace('\\', '/', $relativePath);
            
            $zip->addFile($filePath, $relativePath);
        }
    }
    $zip->close();
    echo "BERHASIL: File ZIP aman untuk Linux telah dibuat di $zipFile\n";
} else {
    echo "GAGAL membuat ZIP.\n";
}
