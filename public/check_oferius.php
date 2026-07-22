<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;
use App\Models\TeachingAssignment;

$teachers = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Arlika%')->get();
foreach($teachers as $t) {
    echo "Arlika ID: " . $t->id . "\n";
}

// Add TA for Oferius Zega (195) -> Agama Kristen (212) in Class 353
$ta1 = TeachingAssignment::firstOrCreate([
    'academic_year_id' => 5,
    'semester_id' => 7,
    'classroom_id' => 353,
    'subject_id' => 212,
    'teacher_id' => 195,
], [
    'hours_per_week' => 3,
    'is_active' => 1,
    'block_type' => 'all'
]);
echo "TA Agama created: " . $ta1->id . "\n";

// Add TA for Adis Zai -> Informatika in Class 353
$t_adis = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Adis%')->first();
$s_infor = \App\Models\Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Informatika%')->first();
if ($t_adis && $s_infor) {
    $ta2 = TeachingAssignment::firstOrCreate([
        'academic_year_id' => 5,
        'semester_id' => 7,
        'classroom_id' => 353,
        'subject_id' => $s_infor->id,
        'teacher_id' => $t_adis->id,
    ], [
        'hours_per_week' => 4,
        'is_active' => 1,
        'block_type' => 'all'
    ]);
    echo "TA Informatika created: " . $ta2->id . "\n";
}
