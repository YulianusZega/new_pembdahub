<?php
/**
 * Script untuk memindahkan/menyalin file dari folder lama ke folder pembdahub baru.
 * Akses: https://perguruanpembda.com/move_storage.php?secret=pembda99
 */
if (($_GET['secret'] ?? '') !== 'pembda99') {
    http_response_code(403);
    die('Forbidden');
}

header('Content-Type: text/html; charset=utf-8');
echo "<h2>Proses Migrasi File ke pembdahub</h2>";

$basePath = __DIR__ . '/../..'; // public_html/
$targetStorage = __DIR__ . '/../storage/app/public';
$targetAudio = __DIR__ . '/audio';

// Pastikan folder target ada
if (!is_dir($targetStorage)) @mkdir($targetStorage, 0775, true);
if (!is_dir($targetAudio)) @mkdir($targetAudio, 0775, true);

// Fungsi untuk copy folder secara rekursif
function copy_dir($src, $dst) {
    $count = 0;
    $dir = opendir($src);
    @mkdir($dst, 0775, true);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $srcFile = $src . '/' . $file;
            $dstFile = $dst . '/' . $file;
            if (is_dir($srcFile)) {
                $count += copy_dir($srcFile, $dstFile);
            } else {
                if (!file_exists($dstFile)) {
                    if (copy($srcFile, $dstFile)) {
                        $count++;
                    }
                }
            }
        }
    }
    closedir($dir);
    return $count;
}

$oldStorages = [
    $basePath . '/pembdahub_lama/storage/app/public',
    $basePath . '/pembdahub_backup/storage/app/public',
    $basePath . '/pembdahub_backup/storage.5201/app/public'
];

$totalCopied = 0;
foreach ($oldStorages as $oldDir) {
    if (is_dir($oldDir)) {
        echo "Menyalin dari: <code>{$oldDir}</code>...<br>";
        $copied = copy_dir($oldDir, $targetStorage);
        $totalCopied += $copied;
        echo "<span style='color:green'>Berhasil menyalin {$copied} file/folder.</span><br><br>";
    }
}

// Copy file audio Mars
$oldAudioPaths = [
    $basePath . '/pembdahub_lama/public/audio/mars-pembda.mp4',
    $basePath . '/pembdahub_backup/public/audio/mars-pembda.mp4',
    $basePath . '/audio/mars-pembda.mp4' // Jika ditaruh di public_html
];

$audioCopied = false;
foreach ($oldAudioPaths as $audioSrc) {
    if (file_exists($audioSrc)) {
        $audioDst = $targetAudio . '/mars-pembda.mp4';
        if (!file_exists($audioDst)) {
            if (copy($audioSrc, $audioDst)) {
                echo "<span style='color:green'>Berhasil menyalin file audio Mars dari: {$audioSrc}</span><br>";
                $audioCopied = true;
                break;
            }
        } else {
            echo "<span style='color:blue'>File audio Mars sudah ada di pembdahub.</span><br>";
            $audioCopied = true;
            break;
        }
    }
}

if (!$audioCopied) {
    echo "<span style='color:red'>File audio Mars tidak ditemukan di folder lama manapun.</span><br>";
}

echo "<h3>Migrasi Selesai!</h3>";
echo "Total file yang disalin ke storage pembdahub: <b>{$totalCopied}</b> file.<br><br>";
echo "<a href='/clear-cache.php?secret=pembda99' style='padding:10px 15px; background:blue; color:white; text-decoration:none; border-radius:5px;'>Selanjutnya: Jalankan Storage Link (Klik Disini)</a>";

// Kita tidak menghapus file dari folder lama demi keamanan data (backup).
?>
