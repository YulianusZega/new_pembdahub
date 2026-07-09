<?php
$zip = new ZipArchive();
$filename = __DIR__ . "/vendor.zip";

if (file_exists($filename)) {
    unlink($filename);
}

if ($zip->open($filename, ZipArchive::CREATE)!==TRUE) {
    exit("Cannot open <$filename>\n");
}

$dir = __DIR__ . '/vendor';

$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    if (!$file->isDir()) {
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen(realpath($dir)) + 1);
        // Normalize slashes for ZIP
        $relativePath = str_replace('\\', '/', $relativePath);
        $zip->addFile($filePath, 'vendor/' . $relativePath);
    }
}

$zip->close();
echo "vendor.zip created successfully.\n";
