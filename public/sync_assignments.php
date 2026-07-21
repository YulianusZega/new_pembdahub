<?php
// Script to automatically generate Teaching Assignments (Penugasan Mengajar) for SMKS Pembda Nias
// Access this via browser: https://perguruanpembda.com/sync_assignments.php?secret=pembda99

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (php_sapi_name() !== 'cli' && request('secret') !== 'pembda99') {
    die('Unauthorized');
}

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\TeachingAssignment;

$schoolId = 2; // SMKS Pembda Nias
$academicYearId = 5; // TP. 2026/2027
$semester = 'ganjil';

echo "<pre style='font-family: monospace; font-size: 13px; line-height: 1.5;'>";
echo "<h2>Memulai Sinkronisasi Penugasan Mengajar SMK</h2>\n";

// 1. Teacher Mapping (Singkatan PDF -> Nama Lengkap di DB)
$teacherMapping = [
    'Y. Ndraha'   => 'Yaitolo Ndraha, S.Th',
    'Ester Tel'   => 'Ester Claryta Tel, S.Pd',
    'Adis Zai'    => 'Adiyusu Zai, S.Pd',
    'Markus Zeb'  => 'Markus Zebua, S.Pd',
    'Arlika Zeb'  => 'Arlika Zebua, S.Pd',
    'Devi Hal'    => 'Devi Asri M. Halawa, S.Kom',
    'Ofer Zega'   => 'Oferius Zega, S.Ag',
    'Tn Hal'      => 'Tonaaro Halawa, S.Pd',
    'Fider Har'   => 'Fider Putri Hartati Gea, SS',
    'Fidel Har'   => 'Fidel Imanuel Harefa, S.Pd',
    'Immel Tel'   => 'Immeldha V. Tel, S.Pd',
    'Nofika'      => 'Nofika Putri Zamasi, S.Psi',
    'Yelfi'       => 'Yelfi Deliani, S.Sos',
    'Elven Nehe'  => 'Elven Hardyus S. Nehe, S.Pd',
    'Oti Laoli'   => 'Otiani Laoli, S.Pd',
    'Solid Mend'  => 'Solidarman J. Mendrofa, S.Pd',
    'Resman Har'  => 'Resman H.N. Harefa, S.Pd',
    'Hilda Hulu'  => 'Hilda Natalia Hulu, S.Pd',
    'Okta Zai'    => 'Okta Lena Zai',
    'Erwin Mend'  => 'Erwin Setiawan Mendrofa',
    'Firwanus Zg' => 'Firwanus Zega, S.Pd',
    'Jul Taf'     => 'Julianus Tafonao, S.PdK',
    'Putra Zeb'   => 'Martperan Putra Zebua, ST',
    'Darius Mend' => 'Darius Mendrofa, S.Pd',
    'Herman Tel'  => 'Herman Putra Tel., S.Pd',
    'Fil. Hulu'   => 'Filiaro Hulu, ST',
    'Agus Tel'    => 'Agusman J. Telaumb, S.Pd',
    'Peniel Zeb'  => 'Peniel Zebua, S.Pd',
    'Desman Tel'  => 'Desman Telaumbanua, S.Pd',
    'Nover Tel'   => 'Noverius Telaumbanua, S.Pd',
    'Rian Har'    => 'Rian Perwira Harefa, S.Kom',
    'Yamo Tel'    => 'Yamonaha Tel, S.Kom',
    'Yul Zega'    => 'Yulianus Zega, S.Kom',
    'Fider Gea'   => 'Fider Putri Hartati Gea, SS',
    'N.Hia'       => 'Ninisadarwati Hia, S.Pd',
    'Defe Har'    => 'Defelinu Harefa, ST',
    'Sabar Zal'   => 'Sabar Jaya Zalukhu, S.Pd',
];

// Load guru ke cache memori (Singkatan -> ID)
$teacherCache = [];
foreach ($teacherMapping as $short => $full) {
    $t = Teacher::where('school_id', $schoolId)->where('full_name', 'like', "%{$full}%")->first();
    if ($t) {
        $teacherCache[$short] = $t->id;
    } else {
        echo "<span style='color:red'>[WARNING] Guru '{$full}' (disingkat '{$short}') tidak ditemukan di database!</span>\n";
    }
}

// Load mapel ke cache memori (Kode -> ID)
$subjectCache = [];
$allSubjects = Subject::where('school_id', $schoolId)->get();
foreach ($allSubjects as $s) {
    $subjectCache[$s->code] = $s->id;
}

// Helper function to resolve Subject Code
function resolveSubject($code, $subjectCache) {
    $code = trim($code);
    if (isset($subjectCache[$code])) return $subjectCache[$code];
    $aliases = [
        'B.IN D' => 'B.IND', 'B.IN G' => 'B.ING', 'INFO R' => 'INFOR', 
        'MUL OK' => 'MUL OK', 'PIPA S' => 'PIPAS', 'S EJ' => 'SEJ',
        'PAN' => 'PAN C', 'DDPK TKR' => 'DDPK-TKR', 'DDPK TSM' => 'DDPK-TSM',
        'DDPK TKJ' => 'DDPK-TKJ', 'KK TKJ' => 'KK-TKJ', 'KK TKR' => 'KK-TKR',
        'KK TE' => 'KK-TE', 'KK DPIB' => 'KK-DPIB'
    ];
    if (isset($aliases[$code]) && isset($subjectCache[$aliases[$code]])) {
        return $subjectCache[$aliases[$code]];
    }
    return null;
}

// 2. Data Penugasan Hasil Analisis PDF
$classAssignments = [
    'X DPIB' => [
        ['mapel' => 'PAN C', 'guru' => 'Y. Ndraha', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'B.IND', 'guru' => 'Ester Tel', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'INFOR', 'guru' => 'Adis Zai', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'MUL OK', 'guru' => 'Markus Zeb', 'jp' => 2, 'tipe' => 'all'],
        ['mapel' => 'MTK', 'guru' => 'Arlika Zeb', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'KKA', 'guru' => 'Devi Hal', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'AGM', 'guru' => 'Ofer Zega', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'PJOK', 'guru' => 'Tn Hal', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'B.ING', 'guru' => 'Fider Har', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'SBD', 'guru' => 'Immel Tel', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'BK', 'guru' => 'Nofika', 'jp' => 2, 'tipe' => 'all'],
        ['mapel' => 'SEJ', 'guru' => 'Yelfi', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'PIPAS', 'guru' => 'Elven Nehe', 'jp' => 4, 'tipe' => 'all'],
        // Kelompok B
        ['mapel' => 'DDPK-DPIB', 'guru' => 'Putra Zeb', 'jp' => 6, 'tipe' => 'split'],
        ['mapel' => 'DDPK-DPIB', 'guru' => 'Herman Tel', 'jp' => 6, 'tipe' => 'split'],
    ],
    'X TE' => [
        ['mapel' => 'PAN C', 'guru' => 'Y. Ndraha', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'B.IND', 'guru' => 'Ester Tel', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'INFOR', 'guru' => 'Adis Zai', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'MUL OK', 'guru' => 'Markus Zeb', 'jp' => 2, 'tipe' => 'all'],
        ['mapel' => 'MTK', 'guru' => 'Arlika Zeb', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'KKA', 'guru' => 'Devi Hal', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'AGM', 'guru' => 'Ofer Zega', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'PJOK', 'guru' => 'Tn Hal', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'B.ING', 'guru' => 'Fider Har', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'SBD', 'guru' => 'Immel Tel', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'BK', 'guru' => 'Nofika', 'jp' => 2, 'tipe' => 'all'],
        ['mapel' => 'SEJ', 'guru' => 'Yelfi', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'PIPAS', 'guru' => 'Elven Nehe', 'jp' => 4, 'tipe' => 'all'],
        // Kelompok B
        ['mapel' => 'DDPK-TE', 'guru' => 'Darius Mend', 'jp' => 6, 'tipe' => 'split'],
        ['mapel' => 'DDPK-TE', 'guru' => 'Fil. Hulu', 'jp' => 6, 'tipe' => 'split'],
    ],
    'X TKR.1' => [
        ['mapel' => 'B.ING', 'guru' => 'Oti Laoli', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'PAN C', 'guru' => 'Y. Ndraha', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'PIPAS', 'guru' => 'Elven Nehe', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'B.IND', 'guru' => 'Ester Tel', 'jp' => 4, 'tipe' => 'all'],
        ['mapel' => 'SEJ', 'guru' => 'Yelfi', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'PJOK', 'guru' => 'Solid Mend', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'AGM', 'guru' => 'Ofer Zega', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'KKA', 'guru' => 'Resman Har', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'MUL OK', 'guru' => 'Markus Zeb', 'jp' => 2, 'tipe' => 'all'],
        ['mapel' => 'SBD', 'guru' => 'Immel Tel', 'jp' => 3, 'tipe' => 'all'],
        ['mapel' => 'BK', 'guru' => 'Nofika', 'jp' => 2, 'tipe' => 'all'],
        ['mapel' => 'MTK', 'guru' => 'N.Hia', 'jp' => 4, 'tipe' => 'all'],
        // Kelompok B
        ['mapel' => 'DDPK-TKR', 'guru' => 'Peniel Zeb', 'jp' => 6, 'tipe' => 'split'],
        ['mapel' => 'DDPK-TKR', 'guru' => 'Desman Tel', 'jp' => 6, 'tipe' => 'split'],
        ['mapel' => 'Dojo SB', 'guru' => 'Fider Gea', 'jp' => 2, 'tipe' => 'split'],
        ['mapel' => 'Kesamaptaan', 'guru' => 'Solid Mend', 'jp' => 2, 'tipe' => 'split'],
        ['mapel' => 'Dojo M', 'guru' => 'Elven Nehe', 'jp' => 2, 'tipe' => 'split'],
    ],
];

// Proses Insert
foreach ($classAssignments as $className => $assignments) {
    $classroom = Classroom::where('school_id', $schoolId)
        ->where('academic_year_id', $academicYearId)
        ->where('class_name', 'like', "%{$className}%")
        ->first();

    if (!$classroom) {
        echo "<span style='color:orange'>[SKIP] Kelas '{$className}' tidak ditemukan.</span>\n";
        continue;
    }

    echo "<h3>Memproses Kelas: {$className}</h3>";

    foreach ($assignments as $a) {
        $subjectId = resolveSubject($a['mapel'], $subjectCache);
        $teacherId = $teacherCache[$a['guru']] ?? null;

        if (!$subjectId) {
            echo "  - <span style='color:red'>Gagal!</span> Mapel '{$a['mapel']}' tidak dikenali.\n";
            continue;
        }

        if ($teacherId) {
            $exists = TeachingAssignment::where([
                'school_id' => $schoolId,
                'academic_year_id' => $academicYearId,
                'semester' => $semester,
                'classroom_id' => $classroom->id,
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'block_type' => $a['tipe']
            ])->first();

            if (!$exists) {
                TeachingAssignment::create([
                    'school_id' => $schoolId,
                    'academic_year_id' => $academicYearId,
                    'semester' => $semester,
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subjectId,
                    'teacher_id' => $teacherId,
                    'hours_per_week' => $a['jp'],
                    'block_type' => $a['tipe'],
                    'is_complete' => false
                ]);
                echo "  - <span style='color:green'>[Sukses]</span> Plot Mapel <b>{$a['mapel']}</b> ke Guru <b>{$a['guru']}</b> ({$a['jp']} JP)\n";
            } else {
                echo "  - <span style='color:gray'>[Skip]</span> Mapel {$a['mapel']} (Guru {$a['guru']}) sudah ada.\n";
            }
        }
    }
}

echo "\n<hr><b>Selesai!</b> Anda dapat mengedit atau menambahkan kelas lainnya langsung di dalam array <code>\$classAssignments</code> pada script ini.";
echo "</pre>";
