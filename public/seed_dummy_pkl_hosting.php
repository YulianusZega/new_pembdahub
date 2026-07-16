<?php
if (!isset($_GET['secret']) || $_GET['secret'] !== 'pembda99') {
    die('Unauthorized');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Dudi;
use App\Models\PklPlacement;
use App\Models\PklLog;
use App\Models\PklMonitoring;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

function createDummyImage($text, $path) {
    $width = 600;
    $height = 400;
    
    // Create image
    $image = imagecreatetruecolor($width, $height);
    
    // Colors
    $bg = imagecolorallocate($image, 240, 245, 255); // Light blue background
    $textColor = imagecolorallocate($image, 30, 50, 100); // Dark text
    $borderColor = imagecolorallocate($image, 100, 150, 255);
    
    // Fill background
    imagefilledrectangle($image, 0, 0, $width, $height, $bg);
    imagerectangle($image, 10, 10, $width - 10, $height - 10, $borderColor);
    
    // Split text into lines
    $lines = explode("\n", wordwrap($text, 60, "\n"));
    
    $y = 150;
    $font = 5;
    foreach ($lines as $line) {
        $tw = imagefontwidth($font) * strlen($line);
        $x = ($width - $tw) / 2;
        imagestring($image, $font, $x, $y, $line, $textColor);
        $y += 30;
    }
    
    $fullPath = storage_path('app/public/' . $path);
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    
    imagepng($image, $fullPath);
    imagedestroy($image);
    
    return $path;
}

function createDummyPdf($text, $path) {
    $fullPath = storage_path('app/public/' . $path);
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($fullPath, "DUMMY PDF CONTENT\n\n" . $text);
    return $path;
}

try {
    echo "<pre>";
    echo "Memulai pembuatan simulasi PKL di Server...\n";

    $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
    if (!$activeYear) {
        die("ERROR: Tidak ada Tahun Pelajaran aktif.\n");
    }

    // 1. Dapatkan Ninisadarwati Hia, S.Pd atau guru lain
    $teacher = Teacher::where('full_name', 'like', '%Ninisadarwati%')->first();
    if (!$teacher) {
        $teacher = Teacher::where('full_name', 'not like', '%Agustiani%')->first();
    }
    
    if (!$teacher) {
        die("ERROR: Tidak ada guru yang tersedia untuk dijadikan pembimbing.\n");
    }
    echo "Guru Pembimbing: " . $teacher->full_name . "\n";

    // 2. Buat DUDI
    $dudi = Dudi::firstOrCreate(
        ['name' => 'BENGKELIN'],
        [
            'address' => 'Jl. Simulasi Otomotif No. 1, Kota Virtual',
            'contact_person' => 'Bapak Kepala Mekanik',
            'phone' => '081234567890'
        ]
    );
    echo "DUDI berhasil disiapkan: BENGKELIN\n";

    // 3. Dapatkan 4 Siswa kelas XII (atau sembarang siswa aktif)
    $students = Student::whereHas('classrooms', function($q) use ($activeYear) {
        $q->where('classrooms.academic_year_id', $activeYear->id);
    })->take(4)->get();

    if ($students->count() < 4) {
        $students = Student::take(4)->get(); // Fallback
    }

    if ($students->isEmpty()) {
        die("ERROR: Tidak ada siswa di database.\n");
    }

    echo "Siswa yang ditempatkan: " . $students->count() . " orang\n";

    // Create Perangkat File
    $pdfPath = 'pkl_perangkat/dummy_perangkat_' . time() . '.pdf';
    createDummyPdf("DOKUMEN PERANGKAT PKL\nDitandatangani oleh BENGKELIN", $pdfPath);

    // 4. Buat Placements & Logs
    foreach ($students as $student) {
        $placement = PklPlacement::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $activeYear->id,
            ],
            [
                'dudi_id' => $dudi->id,
                'teacher_id' => $teacher->id,
                'shift' => 'Shift A',
                'is_perangkat_ready' => true,
                'perangkat_file_path' => $pdfPath
            ]
        );

        // Buat 2 Logbook
        for ($i=1; $i<=2; $i++) {
            $imgPath = 'pkl_logs/dummy_' . $placement->id . '_log_' . $i . '_' . time() . '.png';
            createDummyImage("FOTO KEGIATAN LOGBOOK\nSiswa: " . $student->full_name . "\nKegiatan hari ke-" . $i, $imgPath);
            
            PklLog::firstOrCreate(
                [
                    'pkl_placement_id' => $placement->id,
                    'log_date' => now()->subDays(5 - $i)->format('Y-m-d')
                ],
                [
                    'activity' => 'Melakukan servis ringan dan penggantian oli pada mobil pelanggan di BENGKELIN (Hari ' . $i . ').',
                    'photo' => $imgPath,
                    'status' => 'approved'
                ]
            );
        }
    }
    echo "Penempatan PKL dan Logbook Siswa berhasil dibuat.\n";

    // 5. Buat 1 Laporan Monitoring Guru
    $monImgPath = 'pkl_monitorings/photos/dummy_mon_' . time() . '.png';
    createDummyImage("FOTO KUNJUNGAN MONITORING\nLokasi: BENGKELIN\nOleh: " . $teacher->full_name, $monImgPath);

    $monPdfPath = 'pkl_monitorings/letters/dummy_surat_' . time() . '.pdf';
    createDummyPdf("SURAT TUGAS MONITORING BENGKELIN", $monPdfPath);

    PklMonitoring::firstOrCreate(
        [
            'teacher_id' => $teacher->id,
            'dudi_id' => $dudi->id,
            'shift' => 'Shift A',
            'monitoring_date' => now()->subDays(1)->format('Y-m-d')
        ],
        [
            'notes' => 'Siswa dalam kondisi baik. Pihak mekanik BENGKELIN memberikan apresiasi atas kedisiplinan siswa.',
            'status' => 'submitted',
            'photo_path' => $monImgPath,
            'assignment_letter_path' => $monPdfPath
        ]
    );
    echo "Laporan Monitoring Guru berhasil dibuat.\n";

    echo "\nSIMULASI SELESAI! Silakan login sebagai Guru: " . $teacher->full_name . " atau Yayasan/Admin.\n";
    echo "</pre>";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
