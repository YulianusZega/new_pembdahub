<?php
require_once 'config.php';

$tahun = '2025/2026';
if (PHP_SAPI === 'cli') {
    if (!empty($argv[1])) $tahun = $argv[1];
} else {
    if (!empty($_GET['tahun'])) $tahun = $_GET['tahun'];
}

$outDir = __DIR__ . DIRECTORY_SEPARATOR . 'backups';
if (!is_dir($outDir)) mkdir($outDir, 0777, true);
$filename = $outDir . DIRECTORY_SEPARATOR . 'penugasan_backup_' . str_replace(['/',' '], ['_','_'], $tahun) . '_' . date('Ymd_His') . '.csv';

try {
    $stmt = $pdo->prepare("SELECT * FROM penugasan WHERE tahun_pelajaran = ? ORDER BY id");
    $stmt->execute([$tahun]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $fh = fopen($filename, 'w');
    if ($fh === false) throw new Exception('Cannot open file for writing: ' . $filename);

    if (!empty($rows)) {
        // header
        fputcsv($fh, array_keys($rows[0]));
        foreach ($rows as $r) {
            // convert null -> empty string for CSV
            $line = array_map(function($v){ return $v === null ? '' : $v; }, $r);
            fputcsv($fh, $line);
        }
    }
    fclose($fh);

    echo "Backup saved to: $filename\n";
} catch (Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

?>
