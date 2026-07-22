<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\TeachingAssignment;
use App\Models\Teacher;

// Check Agama Kristen
$tas = TeachingAssignment::with('teacher')->where('classroom_id', 353)->where('subject_id', 212)->get();
foreach($tas as $ta) {
    echo "TA 212 -> Teacher: " . ($ta->teacher->full_name ?? '') . " (ID: ".$ta->teacher_id.")\n";
    if ($ta->teacher_id == 195) {
        $ta->teacher_id = 215;
        $ta->save();
        echo "Fixed 195 to 215!\n";
    }
}

// Find INFOR teacher
$t_infor = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Adis%')->first();
if (!$t_infor) $t_infor = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Zai%')->first();
if (!$t_infor) $t_infor = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Inf%')->first();

if ($t_infor) {
    echo "Found INFOR teacher: " . $t_infor->id . " | " . $t_infor->full_name . "\n";
    $s_infor = \App\Models\Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Informatika%')->first();
    $ta2 = TeachingAssignment::firstOrCreate([
        'academic_year_id' => 5,
        'semester_id' => 7,
        'classroom_id' => 353,
        'subject_id' => $s_infor->id,
        'teacher_id' => $t_infor->id,
    ], [
        'hours_per_week' => 1,
        'is_active' => 1,
        'block_type' => 'all'
    ]);
    echo "TA INFOR created: " . $ta2->id . "\n";
} else {
    echo "Still can't find INFOR teacher!\n";
    // Just grab any teacher who teaches INFOR
    $ta_inf = TeachingAssignment::where('subject_id', 43)->first(); // assuming 43 is Infor
    if ($ta_inf) {
        echo "Found via TA: Teacher ID " . $ta_inf->teacher_id . "\n";
    }
}
