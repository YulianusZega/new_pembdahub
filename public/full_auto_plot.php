<?php
// Script to FULLY AUTO PLOT Teaching Assignments & Schedules for SMKS Pembda Nias
// Access this via browser: https://perguruanpembda.com/full_auto_plot.php?secret=pembda99

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
use App\Models\Schedule;
use App\Models\TimeSlot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$schoolId = 2; // SMKS Pembda Nias
$academicYearId = 5; // TP. 2026/2027
$semester = 'ganjil';

echo "<pre style='font-family: monospace; font-size: 12px; line-height: 1.4;'>";
echo "<h2>Memulai FULL AUTO PLOT Jadwal SMK...</h2>\n";

// 1. Wipe existing schedules & assignments for SMK to start fresh
DB::table('schedules')->where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->where('semester', $semester)->delete();
DB::table('teaching_assignments')->where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->where('semester', $semester)->delete();
echo "[INFO] Semua jadwal dan penugasan mengajar lama telah dihapus untuk SMK TP Ini.\n\n";

// 2. Teacher Mapping
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
    'Elda Zend'   => 'Eldasari Zendrato, S.Pd., B.Ed',
];

$teacherCache = [];
foreach ($teacherMapping as $short => $full) {
    $t = Teacher::where('school_id', $schoolId)->where('full_name', 'like', "%{$full}%")->first();
    if ($t) $teacherCache[$short] = $t->id;
}

$subjectCache = [];
foreach (Subject::where('school_id', $schoolId)->get() as $s) {
    $subjectCache[$s->code] = $s->id;
}

function resolveSubject($code, $subjectCache) {
    $code = trim($code);
    if (isset($subjectCache[$code])) return $subjectCache[$code];
    $aliases = [
        'B.IN D' => 'B.IND', 'B.IN G' => 'B.ING', 'INFO R' => 'INFOR', 
        'MUL OK' => 'MUL OK', 'PIPA S' => 'PIPAS', 'S EJ' => 'SEJ',
        'PAN' => 'PAN C', 'DDPK TKR' => 'DDPK-TKR', 'DDPK TSM' => 'DDPK-TSM',
        'DDPK TKJ' => 'DDPK-TKJ', 'KK TKJ' => 'KK-TKJ', 'KK TKR' => 'KK-TKR',
        'KK TE' => 'KK-TE', 'KK DPIB' => 'KK-DPIB', 'Digital Marketing' => 'INFOR'
    ];
    if (isset($aliases[$code]) && isset($subjectCache[$aliases[$code]])) {
        return $subjectCache[$aliases[$code]];
    }
    return null;
}

// 3. Waktu / Slots Mapping
$timeSlotsDB = TimeSlot::where('school_id', $schoolId)->where('is_teaching_slot', true)->get();
$timeSlotMap = []; 
foreach ($timeSlotsDB as $ts) {
    if (preg_match('/(\d+)/', $ts->slot_name, $m)) {
        $jamKe = (int)$m[1];
        $day = strtolower($ts->day_of_week);
        $timeSlotMap[$day][$jamKe] = $ts->id;
    }
}

// 4. Data Jadwal Ekstensif
$masterData = [
    'X DPIB' => [
        ['day'=>'monday', 'start'=>1, 'end'=>3, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'monday', 'start'=>4, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'INFOR', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>5, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>7, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Devi Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>7, 'end'=>9, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>6, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-DPIB', 'guru'=>'Herman Tel', 'tipe'=>'split'],
        ['day'=>'friday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-DPIB', 'guru'=>'Herman Tel', 'tipe'=>'split'],
    ],
    'X TE' => [
        ['day'=>'monday', 'start'=>1, 'end'=>3, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'monday', 'start'=>4, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'INFOR', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>5, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>7, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Devi Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>7, 'end'=>9, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>6, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TE', 'guru'=>'Fil. Hulu', 'tipe'=>'split'],
        ['day'=>'friday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TE', 'guru'=>'Fil. Hulu', 'tipe'=>'split'],
    ],
    'X TKR.2' => [
        ['day'=>'monday', 'start'=>1, 'end'=>3, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'monday', 'start'=>4, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'INFOR', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>5, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>7, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Devi Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>7, 'end'=>9, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>6, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TKR', 'guru'=>'Agus Tel', 'tipe'=>'split'],
        ['day'=>'thursday', 'start'=>1, 'end'=>2, 'mapel'=>'DDPK-TKR', 'guru'=>'Agus Tel', 'tipe'=>'split'],
        ['day'=>'friday', 'start'=>5, 'end'=>8, 'mapel'=>'DDPK-TKR', 'guru'=>'Agus Tel', 'tipe'=>'split'],
    ],
    'XI DPIB' => [
        ['day'=>'monday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'monday', 'start'=>5, 'end'=>6, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>5, 'end'=>6, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-DPIB', 'guru'=>'Putra Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>7, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'PSS', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>4, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>4, 'end'=>9, 'mapel'=>'KK-DPIB', 'guru'=>'Resman Har', 'tipe'=>'split'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-DPIB', 'guru'=>'Putra Zeb', 'tipe'=>'split'],
        ['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-DPIB', 'guru'=>'Herman Tel', 'tipe'=>'split'],
    ],
    'XI TE' => [
        ['day'=>'monday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'monday', 'start'=>5, 'end'=>6, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>5, 'end'=>6, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-TE', 'guru'=>'Darius Mend', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>7, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'PSS', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>4, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>5, 'end'=>10, 'mapel'=>'KK-TE', 'guru'=>'Yul Zega', 'tipe'=>'split'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TE', 'guru'=>'Fil. Hulu', 'tipe'=>'split'],
        ['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TE', 'guru'=>'Darius Mend', 'tipe'=>'split'],
    ],
    'XII TE' => [
        ['day'=>'monday', 'start'=>1, 'end'=>7, 'mapel'=>'KK-TE', 'guru'=>'Fil. Hulu', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'AGM', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>6, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TE', 'guru'=>'Darius Mend', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Darius Mend', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MPP-TE', 'guru'=>'Darius Mend', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],
    ],
    'XII TJKT' => [
        ['day'=>'monday', 'start'=>1, 'end'=>7, 'mapel'=>'KK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'AGM', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>6, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Darius Mend', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MPP-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],
    ],
];

$schedulePlacements = [];

$allClassrooms = Classroom::where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->get();

foreach ($masterData as $className => $slots) {
    $classroom = $allClassrooms->first(function($c) use ($className) {
        return stripos($c->class_name, $className) !== false;
    });

    if (!$classroom) continue;

    $assignmentCalculations = [];
    foreach ($slots as $slot) {
        $subjectId = resolveSubject($slot['mapel'], $subjectCache);
        $teacherId = $teacherCache[$slot['guru']] ?? null;
        if (!$subjectId || !$teacherId) continue;
        
        $duration = ($slot['end'] - $slot['start']) + 1;
        $key = "{$subjectId}_{$teacherId}_{$slot['tipe']}";
        
        if (!isset($assignmentCalculations[$key])) {
            $assignmentCalculations[$key] = [
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'tipe' => $slot['tipe'],
                'jp' => 0
            ];
        }
        $assignmentCalculations[$key]['jp'] += $duration;
    }

    $taIds = [];
    foreach ($assignmentCalculations as $key => $data) {
        $ta = TeachingAssignment::firstOrCreate([
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'semester' => $semester,
            'classroom_id' => $classroom->id,
            'subject_id' => $data['subject_id'],
            'teacher_id' => $data['teacher_id'],
            'block_type' => $data['tipe']
        ], [
            'hours_per_week' => $data['jp'],
            'is_complete' => false
        ]);
        
        $taIds[$key] = $ta->id;
    }

    foreach ($slots as $slot) {
        $subjectId = resolveSubject($slot['mapel'], $subjectCache);
        $teacherId = $teacherCache[$slot['guru']] ?? null;
        if (!$subjectId || !$teacherId) continue;
        
        $day = $slot['day'];
        $start = $slot['start'];
        $taKey = "{$subjectId}_{$teacherId}_{$slot['tipe']}";
        $taId = $taIds[$taKey] ?? null;

        if ($taId) {
            $schedulePlacements[$day][$start][$teacherId][] = [
                'classroom_id' => $classroom->id,
                'subject_id' => $subjectId,
                'duration' => ($slot['end'] - $slot['start']) + 1,
                'ta_id' => $taId,
                'tipe' => $slot['tipe']
            ];
        }
    }
}

$plotCount = 0;
foreach ($schedulePlacements as $day => $startSlots) {
    foreach ($startSlots as $start => $teachers) {
        foreach ($teachers as $teacherId => $classes) {
            $isGabungan = count($classes) > 1;
            $groupCode = $isGabungan ? 'GAB-' . strtoupper(Str::random(4)) : null;

            foreach ($classes as $c) {
                $timeSlotId = $timeSlotMap[$day][$start] ?? null;
                if (!$timeSlotId) continue;

                Schedule::create([
                    'school_id' => $schoolId,
                    'academic_year_id' => $academicYearId,
                    'semester' => $semester,
                    'classroom_id' => $c['classroom_id'],
                    'subject_id' => $c['subject_id'],
                    'teacher_id' => $teacherId,
                    'time_slot_id' => $timeSlotId,
                    'day_of_week' => $day,
                    'duration_slots' => $c['duration'],
                    'teaching_assignment_id' => $c['ta_id'],
                    'group_code' => $groupCode
                ]);
                $plotCount++;
            }
        }
    }
}

DB::statement("UPDATE teaching_assignments ta 
    SET is_complete = (
        SELECT COALESCE(SUM(duration_slots), 0) >= ta.hours_per_week 
        FROM schedules s 
        WHERE s.teaching_assignment_id = ta.id
    )
    WHERE school_id = $schoolId AND academic_year_id = $academicYearId");

echo "<h3>BERHASIL! 🎉</h3>";
echo "Total $plotCount blok jadwal telah dipetakan ke dalam Grid. <br>";
echo "Kelas gabungan telah mendapatkan badge GAB secara otomatis.<br>";
echo "Silakan refresh menu Jadwal Pelajaran Anda.";
echo "</pre>";
