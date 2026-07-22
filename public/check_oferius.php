<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;
use App\Models\TeachingAssignment;

// Fix Otiani Laoli -> Oferius Zega
$ta_wrong = TeachingAssignment::where('classroom_id', 353)->where('subject_id', 212)->where('teacher_id', 195)->first();
if ($ta_wrong) {
    $ta_wrong->teacher_id = 215; // Oferius
    $ta_wrong->save();
    echo "Fixed Oferius Zega\n";
}

// Fix MTK hours to 2
$s_mtk = \App\Models\Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Matematika%')->first();
$ta_mtk = TeachingAssignment::where('classroom_id', 353)->where('subject_id', $s_mtk->id)->where('teacher_id', 283)->first();
if ($ta_mtk) {
    $ta_mtk->hours_per_week = 2;
    $ta_mtk->save();
    echo "Fixed MTK hours to 2\n";
}

// Find INFOR
$t_adis = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Adis Zai%')->first();
$s_infor = \App\Models\Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Informatika%')->first();

if (!$t_adis) {
    $t_adis = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Adis%')->first();
}

if ($t_adis && $s_infor) {
    $ta2 = TeachingAssignment::firstOrCreate([
        'academic_year_id' => 5,
        'semester_id' => 7,
        'classroom_id' => 353,
        'subject_id' => $s_infor->id,
        'teacher_id' => $t_adis->id,
    ], [
        'hours_per_week' => 1, // Only Jam 4 as requested? "Jam 4 INFOR". I'll put 1 JP. Or maybe it's 2? Let's put 2 just in case. Wait, if they just want Jam 4, let's put 1. The user can edit it.
        'is_active' => 1,
        'block_type' => 'all'
    ]);
    echo "TA INFOR created: " . $ta2->id . " for " . $t_adis->full_name . "\n";
} else {
    echo "Adis/INFOR not found. \n";
    $subs = \App\Models\Subject::where('school_id', 3)->get();
    foreach($subs as $s) {
        if (strpos(strtolower($s->subject_name), 'inform') !== false) {
            echo "Subj: " . $s->subject_name . "\n";
        }
    }
}
