<?php
// Script to synchronize Subject codes for SMKS Pembda Nias
// Access this via browser: https://perguruanpembda.com/sync_subjects.php?secret=pembda99

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (php_sapi_name() !== 'cli' && request('secret') !== 'pembda99') {
    die('Unauthorized');
}

use App\Models\Subject;

$schoolId = 2; // SMKS Pembda Nias

// Mapping Kode di Jadwal -> Nama Mata Pelajaran Lengkap
$subjectsToSync = [
    // --- KELOMPOK A (UMUM & DASAR) ---
    'PAN C'    => 'Pendidikan Pancasila dan Kewarganegaraan (PPKn)',
    'B.IND'    => 'Bahasa Indonesia',
    'B.ING'    => 'Bahasa Inggris',
    'INFOR'    => 'Informatika (TIK)',
    'MUL OK'   => 'Muatan Lokal',
    'MTK'      => 'Matematika',
    'KKA'      => 'Koding dan Kecerdasan Artifisial',
    'PJOK'     => 'Pendidikan Jasmani Olahraga dan Kesehatan',
    'AGM'      => 'Pendidikan Agama dan Budi Pekerti',
    'SBD'      => 'Seni Budaya',
    'BK'       => 'Bimbingan Konseling',
    'SEJ'      => 'Sejarah',
    'PIPAS'    => 'Proyek IPAS',
    'PTAK'     => 'Pendidikan Teknologi dan Kejuruan',
    'KIK'      => 'Karya Ilmiah Kejuruan',
    'PSS'      => 'Pendidikan Sistem Sinkron',
    'MPP-DPIB' => 'Mata Pelajaran Pilihan DPIB',
    'MPP-TE'   => 'Mata Pelajaran Pilihan TE',
    'MPP-TKR'  => 'Mata Pelajaran Pilihan TKR',
    'MPP-TSM'  => 'Mata Pelajaran Pilihan TSM',
    'MPP-TKJ'  => 'Mata Pelajaran Pilihan TKJ',

    // --- KELOMPOK B (KEJURUAN / SPLIT) ---
    'DDPK-DPIB'=> 'Dasar-Dasar Program Keahlian DPIB',
    'DDPK-TE'  => 'Dasar-Dasar Program Keahlian TE',
    'DDPK-TKR' => 'Dasar-Dasar Program Keahlian TKR',
    'DDPK-TSM' => 'Dasar-Dasar Program Keahlian TSM',
    'DDPK-TKJ' => 'Dasar-Dasar Program Keahlian TKJ',
    
    'KK-DPIB'  => 'Konsentrasi Keahlian DPIB',
    'KK-TE'    => 'Konsentrasi Keahlian TE',
    'KK-TKR'   => 'Konsentrasi Keahlian TKR',
    'KK-TSM'   => 'Konsentrasi Keahlian TSM',
    'KK-TKJ'   => 'Konsentrasi Keahlian TKJ',

    'Dojo SB'  => 'Dojo Seni Budaya',
    'Dojo M'   => 'Dojo Matematika',
    'Kesamaptaan' => 'Kesamaptaan',
    'DD-Elektronika' => 'Dasar-Dasar Elektronika',
    'Publik Speaking' => 'Public Speaking',
    'Robotica' => 'Robotika',
    'Desain Grafis' => 'Desain Grafis',
];

// Mapping kata kunci untuk update mapel yang sudah ada
$existingKeywords = [
    'Pancasila' => 'PAN C',
    'Bahasa Indonesia' => 'B.IND',
    'Bahasa Inggris' => 'B.ING',
    'Informatika' => 'INFOR',
    'Matematika' => 'MTK',
    'Agama' => 'AGM',
    'Seni' => 'SBD',
    'Konseling' => 'BK',
    'Sejarah' => 'SEJ',
    'IPAS' => 'PIPAS',
    'Jasmani' => 'PJOK',
    'MULOK' => 'MUL OK'
];

echo "<pre>";
echo "<h2>Memulai Sinkronisasi Mata Pelajaran SMK (School ID: $schoolId)</h2>\n";

// 1. Coba update mapel yang sudah ada berdasarkan keyword
$allSubjects = Subject::where('school_id', $schoolId)->get();

foreach ($allSubjects as $subj) {
    foreach ($existingKeywords as $keyword => $newCode) {
        if (stripos($subj->name, $keyword) !== false) {
            $oldCode = $subj->code;
            if ($oldCode !== $newCode) {
                $subj->code = $newCode;
                $subj->save();
                echo "[UPDATE] Mapel '{$subj->name}' diupdate kodenya dari '{$oldCode}' menjadi '{$newCode}'\n";
            }
            // Hapus dari list yang akan di-create agar tidak duplikat
            if (isset($subjectsToSync[$newCode])) {
                unset($subjectsToSync[$newCode]);
            }
            break; // Lanjut ke mapel berikutnya
        }
    }
}

// 2. Buat mapel baru yang belum ada
foreach ($subjectsToSync as $code => $name) {
    // Cek apakah kodenya sudah ada (just in case)
    $exists = Subject::where('school_id', $schoolId)->where('code', $code)->first();
    if (!$exists) {
        Subject::create([
            'school_id' => $schoolId,
            'name' => $name,
            'code' => $code,
            'is_active' => true
        ]);
        echo "[CREATE] Mata pelajaran baru ditambahkan: {$name} (Kode: {$code})\n";
    } else {
        echo "[SKIP] Kode {$code} sudah ada.\n";
    }
}

echo "\n<h3>Sinkronisasi Selesai!</h3>";
echo "Silakan cek di menu Mata Pelajaran pada aplikasi.";
echo "</pre>";
