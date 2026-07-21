<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());
if (php_sapi_name() !== 'cli' && request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;

$schoolId = 3; 
$academicYearId = 5; 

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
    'Okta Zai'    => 'Okta Zai', 'Erwin Mend'  => 'Erwin Setiawan Mendrofa',
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

echo "<pre>";
echo "--- TEACHER MAPPING CHECK ---\n";
foreach ($teacherMapping as $short => $full) {
    // Note: I modified Okta Lena Zai to Okta Zai above because the user said "Okta Zai nama guru baru dari Okta Lena Zai"
    $t = Teacher::where('school_id', $schoolId)->where('full_name', 'like', "%{$full}%")->first();
    if (!$t) {
        echo "MISSING TEACHER: '$short' -> '$full'\n";
    }
}

echo "\n--- SUBJECT MAPPING CHECK ---\n";
$subjectCache = [];
foreach (Subject::where('school_id', $schoolId)->get() as $s) {
    $subjectCache[$s->code] = $s->id;
}

$aliases = ['B.IN D'=>'B.IND', 'B.IN G'=>'B.ING', 'INFO R'=>'INFOR', 'MUL OK'=>'MUL OK', 'PIPA S'=>'PIPAS', 'S EJ'=>'SEJ', 'PAN'=>'PAN C', 'DDPK TKR'=>'DDPK-TKR', 'DDPK TSM'=>'DDPK-TSM', 'DDPK TKJ'=>'DDPK-TKJ', 'KK TKJ'=>'KK-TKJ', 'KK TKR'=>'KK-TKR', 'KK TE'=>'KK-TE', 'KK DPIB'=>'KK-DPIB', 'Digital Marketing'=>'INFOR'];
$allSubjectsInArray = ['PAN C','B.IND','INFOR','MUL OK','MTK','KKA','AGM','PJOK','B.ING','SBD','BK','SEJ','PIPAS','DDPK-DPIB','DDPK-TE','DDPK-TKR','DDPK-TSM','DD-Elektronika','DDPK-TKJ','Publik Speaking','PTAK','MPP-DPIB','KIK','PSS','KK-DPIB','MPP-TE','KK-TE','MPP-TKR','MPP-TSM','KK-TSM','Digital Marketing','KK-TKJ'];

foreach ($allSubjectsInArray as $code) {
    $found = false;
    if (isset($subjectCache[$code])) $found = true;
    else if (isset($aliases[$code]) && isset($subjectCache[$aliases[$code]])) $found = true;
    
    if (!$found) {
        echo "MISSING SUBJECT: '$code'\n";
    }
}

echo "\n--- CLASSROOM MAPPING CHECK ---\n";
$classesInArray = ['X DPIB', 'X TE', 'X TKR.2', 'X TKR.1', 'X TSM.1', 'X TSM.2', 'X ACP', 'X TKJ Industri', 'XI DPIB', 'XI TE', 'XI TKR 1', 'XI TKR 2', 'XI TSM 1', 'XI TSM 2', 'XI ACP', 'XI TKJ', 'XII TE', 'XII TKJ', 'XII TKR INDUSTRI', 'XII TKR 2', 'XII TSM 1', 'XII TSM 2', 'XII ACP', 'XII DPIB'];
$allClassrooms = Classroom::where('school_id', $schoolId)->where('academic_year_id', $academicYearId)->get();

foreach ($classesInArray as $className) {
    $classroom = $allClassrooms->first(function($c) use ($className) { return stripos($c->class_name, $className) !== false; });
    if (!$classroom) {
        echo "MISSING CLASSROOM: '$className'\n";
    }
}
echo "</pre>";
