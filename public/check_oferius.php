<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;
use App\Models\TeachingAssignment;

$t_arlika = Teacher::find(283);
$s_mtk = \App\Models\Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Matematika%')->first();
if ($t_arlika && $s_mtk) {
    $ta3 = TeachingAssignment::firstOrCreate([
        'academic_year_id' => 5,
        'semester_id' => 7,
        'classroom_id' => 353,
        'subject_id' => $s_mtk->id,
        'teacher_id' => $t_arlika->id,
    ], [
        'hours_per_week' => 3,
        'is_active' => 1,
        'block_type' => 'all'
    ]);
    echo "TA MTK created: " . $ta3->id . "\n";
} else {
    echo "Teacher or Subject MTK not found\n";
}
