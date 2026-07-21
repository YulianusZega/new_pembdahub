<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
if (php_sapi_name() !== 'cli' && request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\TeachingAssignment;
use App\Models\Schedule;
use App\Models\TimeSlot;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

$schoolId = 3; 
$academicYearId = 5; 
$semester = 'ganjil';

echo "<pre style='font-family: monospace; font-size: 12px; line-height: 1.4;'>";
echo "<h2>Memulai FULL AUTO PLOT Jadwal SMK (24 KELAS)...</h2>\n";

DB::table('schedules')->where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->where('semester_id', 7)->delete();
DB::table('teaching_assignments')->whereIn('classroom_id', function($q) use ($schoolId) {
    $q->select('id')->from('classrooms')->where('school_id', $schoolId);
})->where('academic_year_id', $academicYearId)->where('semester_id', 7)->delete();
echo "[INFO] Semua jadwal dan penugasan mengajar lama dihapus.\n";

$teacherMapping = [
    'Y. Ndraha'   => 'Yaitolo Ndraha, S.Th', 'Ester Tel'   => 'Ester Claryta Tel, S.Pd',
    'Adis Zai'    => 'Adiyusu Zai, S.Pd', 'Markus Zeb'  => 'Markus Zebua, S.Pd',
    'Arlika Zeb'  => 'Arlika Zebua, S.Pd', 'Devi Hal'    => 'Devi Asri M. Halawa, S.Kom',
    'Ofer Zega'   => 'Oferius Zega, S.Ag', 'Tn Hal'      => 'Tonaaro Halawa, S.Pd',
    'Fider Har'   => 'Fider Putri Hartati Gea, SS', 'Fidel Har'   => 'Fidel Imanuel Harefa, S.Pd',
    'Immel Tel'   => 'Immeldha V. Tel, S.Pd', 'Nofika'      => 'Nofika Putri Zamasi, S.Psi',
    'Yelfi'       => 'Yelfi Deliani, S.Sos', 'Elven Nehe'  => 'Elven Hardyus S. Nehe, S.Pd',
    'Oti Laoli'   => 'Otiani Laoli, S.Pd', 'Solid Mend'  => 'Solidarman J. Mendrofa, S.Pd',
    'Resman Har'  => 'Resman H.N. Harefa, S.Pd', 'Hilda Hulu'  => 'Hilda Natalia Hulu, S.Pd',
    'Okta Zai'    => 'Oktalena', 'Erwin Mend'  => 'Erwin Setiawan Mendrofa',
    'Firwanus Zg' => 'Firwanus Zega, S.Pd', 'Jul Taf'     => 'Julianus Tafonao, S.PdK',
    'Putra Zeb'   => 'Martperan Putra Zebua, ST', 'Darius Mend' => 'Darius Mendrofa, S.Pd',
    'Herman Tel'  => 'Herman Putra Tel., S.Pd', 'Fil. Hulu'   => 'Filiaro Hulu, ST',
    'Agus Tel'    => 'Agusman J. Telaumb, S.Pd', 'Peniel Zeb'  => 'Peniel Zebua, S.Pd',
    'Desman Tel'  => 'Desman Telaumbanua, S.Pd', 'Nover Tel'   => 'Noverius Telaumbanua, S.Pd',
    'Rian Har'    => 'Rian Perwira Harefa, S.Kom', 'Yamo Tel'    => 'Yamonaha Tel, S.Kom',
    'Yul Zega'    => 'Yulianus Zega, S.Kom', 'N.Hia'       => 'Ninisadarwati Hia, S.Pd',
    'Defe Har'    => 'Defelinu Harefa, ST', 'Sabar Zal'   => 'Sabar Jaya Zalukhu, S.Pd',
    'Elda Zend'   => 'Eldasari Zendrato, S.Pd., B.Ed'
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

function resolveSubject($code, &$subjectCache, $schoolId) {
    $code = trim($code);
    if (isset($subjectCache[$code])) return $subjectCache[$code];
    
    $aliases = ['B.IN D'=>'B.IND', 'B.IN G'=>'B.ING', 'INFO R'=>'INFOR', 'MUL OK'=>'MUL OK', 'PIPA S'=>'PIPAS', 'S EJ'=>'SEJ', 'PAN'=>'PAN C', 'DDPK TKR'=>'DDPK-TKR', 'DDPK TSM'=>'DDPK-TSM', 'DDPK TKJ'=>'DDPK-TKJ', 'KK TKJ'=>'KK-TKJ', 'KK TKR'=>'KK-TKR', 'KK TE'=>'KK-TE', 'KK DPIB'=>'KK-DPIB', 'Digital Marketing'=>'INFOR'];
    $lookupCode = $aliases[$code] ?? $code;
    
    if (isset($subjectCache[$lookupCode])) return $subjectCache[$lookupCode];
    
    // Auto-create missing subject
    $newSubject = Subject::create([
        'school_id' => $schoolId,
        'code' => $lookupCode,
        'name' => $lookupCode,
        'subject_code' => $lookupCode,
        'subject_name' => $lookupCode,
        'is_active' => true
    ]);
    
    $subjectCache[$lookupCode] = $newSubject->id;
    return $newSubject->id;
}

$timeSlotsDB = TimeSlot::where('school_id', $schoolId)->where('is_teaching_slot', true)->get();
$timeSlotMap = []; 
foreach ($timeSlotsDB as $ts) {
    if (preg_match('/(\d+)/', $ts->slot_name, $m)) {
        $jamKe = (int)$m[1];
        $timeSlotMap[strtolower($ts->day_of_week)][$jamKe] = $ts->id;
    }
}

$masterData = [
    'X DPIB' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'INFOR', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>5, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>9, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'thursday', 'start'=>6, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-DPIB', 'guru'=>'Herman Tel', 'tipe'=>'split'],['day'=>'friday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-DPIB', 'guru'=>'Herman Tel', 'tipe'=>'split']
    ],
    'X TE' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'INFOR', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>5, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>9, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'thursday', 'start'=>6, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TE', 'guru'=>'Fil. Hulu', 'tipe'=>'split'],['day'=>'friday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TE', 'guru'=>'Fil. Hulu', 'tipe'=>'split']
    ],
    'X TKR.2' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'INFOR', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>5, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>9, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'thursday', 'start'=>6, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TKR', 'guru'=>'Agus Tel', 'tipe'=>'split'],['day'=>'thursday', 'start'=>1, 'end'=>2, 'mapel'=>'DDPK-TKR', 'guru'=>'Agus Tel', 'tipe'=>'split'],['day'=>'friday', 'start'=>5, 'end'=>8, 'mapel'=>'DDPK-TKR', 'guru'=>'Agus Tel', 'tipe'=>'split']
    ],
    'X TKR.1' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'split'],['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>5, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'PJOK', 'guru'=>'Solid Mend', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>4, 'end'=>5, 'mapel'=>'KKA', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>6, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>8, 'end'=>10, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>8, 'end'=>9, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'thursday', 'start'=>10, 'end'=>10, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'N.Hia', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>5, 'end'=>10, 'mapel'=>'DDPK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'split'],['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TKR', 'guru'=>'Desman Tel', 'tipe'=>'split']
    ],
    'X TSM.1' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'INFOR', 'guru'=>'Yamo Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>8, 'mapel'=>'BK', 'guru'=>'Firwanus Zg', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>5, 'end'=>7, 'mapel'=>'SEJ', 'guru'=>'Fidel Har', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>8, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Resman Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'SBD', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TSM', 'guru'=>'Nover Tel', 'tipe'=>'split']
    ],
    'X TSM.2' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'SBD', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KKA', 'guru'=>'Yamo Tel', 'tipe'=>'split'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>9, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>10, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>6, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>9, 'end'=>10, 'mapel'=>'SEJ', 'guru'=>'Fidel Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TSM', 'guru'=>'Rian Har', 'tipe'=>'split'],['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TSM', 'guru'=>'Rian Har', 'tipe'=>'split']
    ],
    'X ACP' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>4, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>8, 'end'=>10, 'mapel'=>'KKA', 'guru'=>'Devi Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>2, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>3, 'end'=>5, 'mapel'=>'MTK', 'guru'=>'Okta Zai', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>6, 'end'=>8, 'mapel'=>'PJOK', 'guru'=>'Solid Mend', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>9, 'end'=>10, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>2, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'thursday', 'start'=>3, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'INFOR', 'guru'=>'Erwin Mend', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>5, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>7, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'BK', 'guru'=>'Firwanus Zg', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>5, 'end'=>10, 'mapel'=>'DD-Elektronika', 'guru'=>'Darius Mend', 'tipe'=>'split'],['day'=>'wednesday', 'start'=>5, 'end'=>10, 'mapel'=>'DDPK-TKJ', 'guru'=>'Yamo Tel', 'tipe'=>'split'],['day'=>'thursday', 'start'=>1, 'end'=>2, 'mapel'=>'DDPK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'split'],['day'=>'thursday', 'start'=>5, 'end'=>6, 'mapel'=>'Publik Speaking', 'guru'=>'Immel Tel', 'tipe'=>'split'],['day'=>'friday', 'start'=>3, 'end'=>8, 'mapel'=>'DDPK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'split']
    ],
    'X TKJ Industri' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Firwanus Zg', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>10, 'mapel'=>'PIPAS', 'guru'=>'Elven Nehe', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'INFOR', 'guru'=>'Yamo Tel', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>5, 'end'=>8, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>9, 'end'=>10, 'mapel'=>'SBD', 'guru'=>'Immel Tel', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'KKA', 'guru'=>'Yamo Tel', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>5, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>7, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'B.ING', 'guru'=>'Oti Laoli', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>6, 'mapel'=>'DDPK-TKJ', 'guru'=>'Yamo Tel', 'tipe'=>'split']
    ],
    'XI DPIB' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'split'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>5, 'end'=>6, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-DPIB', 'guru'=>'Putra Zeb', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'thursday', 'start'=>7, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'PSS', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>4, 'end'=>9, 'mapel'=>'KK-DPIB', 'guru'=>'Resman Har', 'tipe'=>'split'],['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-DPIB', 'guru'=>'Putra Zeb', 'tipe'=>'split'],['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-DPIB', 'guru'=>'Herman Tel', 'tipe'=>'split']
    ],
    'XI TE' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Markus Zeb', 'tipe'=>'split'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'split'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'B.IND', 'guru'=>'Ester Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>5, 'end'=>6, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-TE', 'guru'=>'Darius Mend', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'thursday', 'start'=>7, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'PSS', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'tuesday', 'start'=>5, 'end'=>10, 'mapel'=>'KK-TE', 'guru'=>'Yul Zega', 'tipe'=>'split'],['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TE', 'guru'=>'Fil. Hulu', 'tipe'=>'split'],['day'=>'thursday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TE', 'guru'=>'Darius Mend', 'tipe'=>'split']
    ],
    'XI TKR 1' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'SEJ', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'N.Hia', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>5, 'end'=>7, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>8, 'end'=>10, 'mapel'=>'MPP-TKR', 'guru'=>'Desman Tel', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'thursday', 'start'=>7, 'end'=>10, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>2, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'friday', 'start'=>3, 'end'=>5, 'mapel'=>'PSS', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>7, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all']
    ],
    'XI TKR 2' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'AGM', 'guru'=>'Ofer Zega', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>6, 'mapel'=>'B.ING', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'monday', 'start'=>7, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'SEJ', 'guru'=>'Markus Zeb', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>5, 'end'=>6, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>9, 'mapel'=>'MPP-TSM', 'guru'=>'Sabar Zal', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>2, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'thursday', 'start'=>3, 'end'=>5, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'thursday', 'start'=>6, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'thursday', 'start'=>9, 'end'=>10, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PSS', 'guru'=>'Fider Har', 'tipe'=>'all']
    ],
    'XI TSM 1' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'PSS', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'KIK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>4, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>5, 'end'=>7, 'mapel'=>'KIK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>8, 'end'=>10, 'mapel'=>'MPP-TSM', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'PTAK', 'guru'=>'Firwanus Zg', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'thursday', 'start'=>7, 'end'=>8, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>5, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>7, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'SEJ', 'guru'=>'Markus Zeb', 'tipe'=>'all']
    ],
    'XI TSM 2' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KIK', 'guru'=>'Arlika Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>2, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>3, 'end'=>5, 'mapel'=>'PSS', 'guru'=>'Immel Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>8, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>9, 'end'=>10, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>2, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>3, 'end'=>5, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>6, 'end'=>8, 'mapel'=>'MPP-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>9, 'end'=>10, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'PTAK', 'guru'=>'Firwanus Zg', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'SEJ', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'thursday', 'start'=>7, 'end'=>8, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'thursday', 'start'=>9, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'KIK', 'guru'=>'Arlika Zeb', 'tipe'=>'all']
    ],
    'XI ACP' => [
        ['day'=>'monday', 'start'=>2, 'end'=>3, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'monday', 'start'=>4, 'end'=>7, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>6, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>9, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>9, 'end'=>10, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'MPP-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'MTK', 'guru'=>'Okta Zai', 'tipe'=>'all'],['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'PSS', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>5, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>8, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'friday', 'start'=>9, 'end'=>10, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all']
    ],
    'XI TKJ' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>6, 'mapel'=>'B.ING', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>9, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>4, 'end'=>6, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>9, 'end'=>10, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'MPP-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'MTK', 'guru'=>'Okta Zai', 'tipe'=>'all'],['day'=>'thursday', 'start'=>8, 'end'=>10, 'mapel'=>'PSS', 'guru'=>'Fider Har', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'PAN C', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>5, 'mapel'=>'SEJ', 'guru'=>'Yelfi', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>8, 'mapel'=>'PJOK', 'guru'=>'Tn Hal', 'tipe'=>'all'],['day'=>'friday', 'start'=>9, 'end'=>10, 'mapel'=>'PTAK', 'guru'=>'Resman Har', 'tipe'=>'all']
    ],
    'XII TE' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TE', 'guru'=>'Fil Hulu', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TE', 'guru'=>'Fil Hulu', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'AGM', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TE', 'guru'=>'Darius Mend', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Darius Mend', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MPP-TE', 'guru'=>'Darius Mend', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all']
    ],
    'XII TKJ' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'AGM', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Darius Mend', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'thursday', 'start'=>5, 'end'=>7, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MPP-TKJ', 'guru'=>'Erwin Mend', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all']
    ],
    'XII TKR INDUSTRI' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'MTK', 'guru'=>'N.Hia', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>10, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'thursday', 'start'=>7, 'end'=>8, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'thursday', 'start'=>9, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>5, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>7, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all']
    ],
    'XII TKR 2' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'MTK', 'guru'=>'N.Hia', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>10, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TKR', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Peniel Zeb', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>6, 'mapel'=>'MUL OK', 'guru'=>'Fider Har', 'tipe'=>'all'],['day'=>'thursday', 'start'=>7, 'end'=>8, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'thursday', 'start'=>9, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>3, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'friday', 'start'=>4, 'end'=>5, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>6, 'end'=>7, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all']
    ],
    'XII TSM 1' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>8, 'end'=>10, 'mapel'=>'BK', 'guru'=>'Firwanus Zg', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all']
    ],
    'XII TSM 2' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>5, 'mapel'=>'B.ING', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>6, 'end'=>7, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>8, 'end'=>10, 'mapel'=>'BK', 'guru'=>'Firwanus Zg', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-TSM', 'guru'=>'Defe Har', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Fidel Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'AGM', 'guru'=>'Jul Taf', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'MTK', 'guru'=>'Adis Zai', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all']
    ],
    'XII ACP' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-TKJ', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-TKJ', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-TKJ', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>6, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'N.Hia', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>2, 'mapel'=>'Digital Marketing', 'guru'=>'Darius Mend', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>3, 'end'=>6, 'mapel'=>'KK-TKJ', 'guru'=>'Devi Hal', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Devi Hal', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'thursday', 'start'=>6, 'end'=>9, 'mapel'=>'AGM', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all']
    ],
    'XII DPIB' => [
        ['day'=>'monday', 'start'=>2, 'end'=>4, 'mapel'=>'KK-DPIB', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'monday', 'start'=>5, 'end'=>7, 'mapel'=>'KK-DPIB', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>1, 'end'=>3, 'mapel'=>'MPP-DPIB', 'guru'=>'Putra Zeb', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>4, 'end'=>6, 'mapel'=>'BK', 'guru'=>'Nofika', 'tipe'=>'all'],['day'=>'tuesday', 'start'=>7, 'end'=>10, 'mapel'=>'MTK', 'guru'=>'N.Hia', 'tipe'=>'all'],
        ['day'=>'wednesday', 'start'=>1, 'end'=>6, 'mapel'=>'KK-DPIB', 'guru'=>'Resman Har', 'tipe'=>'all'],['day'=>'wednesday', 'start'=>7, 'end'=>8, 'mapel'=>'KIK', 'guru'=>'Resman Har', 'tipe'=>'all'],
        ['day'=>'thursday', 'start'=>1, 'end'=>3, 'mapel'=>'MUL OK', 'guru'=>'Nover Tel', 'tipe'=>'all'],['day'=>'thursday', 'start'=>4, 'end'=>5, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all'],['day'=>'thursday', 'start'=>6, 'end'=>9, 'mapel'=>'AGM', 'guru'=>'Y. Ndraha', 'tipe'=>'all'],
        ['day'=>'friday', 'start'=>1, 'end'=>4, 'mapel'=>'PAN C', 'guru'=>'Markus Zeb', 'tipe'=>'all'],['day'=>'friday', 'start'=>5, 'end'=>7, 'mapel'=>'B.IND', 'guru'=>'Hilda Hulu', 'tipe'=>'all'],['day'=>'friday', 'start'=>8, 'end'=>10, 'mapel'=>'KIK', 'guru'=>'Elda Zend', 'tipe'=>'all']
    ]
];

$schedulePlacements = [];
$allClassrooms = Classroom::where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->get();

function normalizeClassName($name) {
    $name = strtoupper($name);
    $name = str_replace(['.','-','_'], ' ', $name);
    $name = preg_replace('/\s+/', ' ', $name);
    // Aliases translation
    $name = str_replace(' TJKT', ' TKJ', $name);
    $name = str_replace(' TE', ' TAV', $name); // TE is TAV
    
    // Sometimes TKR 1 is called TKR INDUSTRI, we can handle it if needed
    // But let's just make both variants match.
    return trim($name);
}

foreach ($masterData as $pdfClassName => $slots) {
    $normPdf = normalizeClassName($pdfClassName);
    
    // Special cases
    if ($normPdf == 'X TKJ INDUSTRI') $normPdf = 'X TKJ';
    if ($normPdf == 'X TKR 1') $normPdf = 'X TKR INDUSTRI';
    
    $classroom = $allClassrooms->first(function($c) use ($normPdf) {
        $dbName = normalizeClassName($c->class_name);
        // If DB is "X TKJ" and pdf is "X TKJ", it matches perfectly.
        if ($dbName === $normPdf) return true;
        // Also allow contains if one is longer
        if (strpos($dbName, $normPdf) !== false || strpos($normPdf, $dbName) !== false) return true;
        
        return false;
    });

    if (!$classroom) {
        echo "[WARNING] Kelas tidak ditemukan di DB: $pdfClassName (Mencari: $normPdf)\n";
        continue;
    }

    $assignmentCalculations = [];
    foreach ($slots as $slot) {
        $subjectId = resolveSubject($slot['mapel'], $subjectCache, $schoolId);
        $teacherId = $teacherCache[$slot['guru']] ?? null;
        if (!$subjectId || !$teacherId) continue;
        
        $duration = ($slot['end'] - $slot['start']) + 1;
        $key = "{$subjectId}_{$teacherId}_{$slot['tipe']}";
        if (!isset($assignmentCalculations[$key])) {
            $assignmentCalculations[$key] = ['subject_id'=>$subjectId, 'teacher_id'=>$teacherId, 'tipe'=>$slot['tipe'], 'jp'=>0];
        }
        $assignmentCalculations[$key]['jp'] += $duration;
    }

    $taIds = [];
    $classroomId = $classroom->id;
    foreach ($assignmentCalculations as $key => $data) {
        $subjectId = $data['subject_id'];
        $teacherId = $data['teacher_id'];
        $ta = null;
        try {
            $ta = TeachingAssignment::firstOrCreate([
                'academic_year_id' => $academicYearId,
                'semester_id' => 7,
                'classroom_id' => $classroomId,
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
                'block_type' => $data['tipe'],
            ], [
                'hours_per_week' => $data['jp']
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            $ta = TeachingAssignment::where([
                'academic_year_id' => $academicYearId,
                'semester_id' => 7,
                'classroom_id' => $classroomId,
                'subject_id' => $subjectId,
                'teacher_id' => $teacherId,
            ])->first();
            if (!$ta) throw $e;
        }
        $taIds[$key] = $ta->id;
    }

    foreach ($slots as $slot) {
        $subjectId = resolveSubject($slot['mapel'], $subjectCache, $schoolId);
        $teacherId = $teacherCache[$slot['guru']] ?? null;
        if (!$subjectId || !$teacherId) continue;
        
        $day = $slot['day']; $start = $slot['start'];
        $taId = $taIds["{$subjectId}_{$teacherId}_{$slot['tipe']}"] ?? null;

        if ($taId) {
            $schedulePlacements[$day][$start][$teacherId][] = [
                'classroom_id' => $classroom->id, 'subject_id' => $subjectId, 'duration' => ($slot['end'] - $slot['start']) + 1,
                'ta_id' => $taId, 'tipe' => $slot['tipe']
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
                    'school_id' => $schoolId, 'academic_year_id' => $academicYearId, 'semester' => 'ganjil', 'semester_id' => 7,
                    'classroom_id' => $c['classroom_id'], 'subject_id' => $c['subject_id'], 'teacher_id' => $teacherId,
                    'time_slot_id' => $timeSlotId, 'day_of_week' => $day, 'duration_slots' => $c['duration'],
                    'teaching_assignment_id' => $c['ta_id'], 'group_code' => $groupCode
                ]);
                $plotCount++;
            }
        }
    }
}

echo "<h3>BERHASIL! 🎉</h3>Total $plotCount blok jadwal telah dipetakan ke dalam Grid. <br>Kelas gabungan telah mendapatkan badge GAB secara otomatis.<br>Silakan refresh menu Jadwal Pelajaran Anda.</pre>";
