<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

use App\Models\Teacher;
use App\Models\TeachingAssignment;

// 1. Create Teacher Guru TKJ (42)
$guruTKJ = Teacher::firstOrCreate([
    'school_id' => 3,
    'full_name' => 'Guru TKJ (42)',
], [
    'is_active' => 1,
    'teacher_code' => '42',
]);
echo "Created/Found Teacher: " . $guruTKJ->full_name . " (ID: " . $guruTKJ->id . ")\n";

// 2. Assign INFOR to Guru TKJ in X TSM 2
$s_infor = \App\Models\Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Informatika%')->first();

if ($s_infor) {
    // Check if Adiyusu Zai has TA in X TSM 2, if so, transfer to Guru TKJ
    $t_adis = Teacher::where('school_id', 3)->where('full_name', 'LIKE', '%Adiyusu Zai%')->first();
    if ($t_adis) {
        $existingTA = TeachingAssignment::where('classroom_id', 353)
            ->where('subject_id', $s_infor->id)
            ->where('teacher_id', $t_adis->id)
            ->first();
            
        if ($existingTA) {
            $existingTA->teacher_id = $guruTKJ->id;
            $existingTA->hours_per_week = 1;
            $existingTA->save();
            echo "Transferred INFOR TA from Adiyusu to Guru TKJ!\n";
        } else {
            // Create new
            $ta = TeachingAssignment::firstOrCreate([
                'academic_year_id' => 5,
                'semester_id' => 7,
                'classroom_id' => 353,
                'subject_id' => $s_infor->id,
                'teacher_id' => $guruTKJ->id,
            ], [
                'hours_per_week' => 1,
                'is_active' => 1,
                'block_type' => 'all'
            ]);
            echo "Created NEW INFOR TA for Guru TKJ!\n";
        }
    }
}
