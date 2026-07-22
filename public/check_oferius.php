<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

if (request('secret') !== 'pembda99') die('Unauthorized');

try {
    // 1. Create Employee
    $emp = \App\Models\Employee::firstOrCreate([
        'employee_code' => 'TKJ-42',
    ], [
        'full_name' => 'Guru TKJ (42)',
        'school_id' => 3,
        'gender' => 'L',
        'is_active' => 1,
        'tmt_date' => '2026-07-01', // Provide default
        'employee_type' => 'guru',
    ]);
    
    // 2. Create Teacher
    $guruTKJ = \App\Models\Teacher::firstOrCreate([
        'school_id' => 3,
        'full_name' => 'Guru TKJ (42)',
    ], [
        'employee_id' => $emp->id,
        'is_active' => 1,
        'teacher_code' => '42',
        'gender' => 'L',
    ]);
    echo "Created/Found Teacher: " . $guruTKJ->full_name . " (ID: " . $guruTKJ->id . ")\n";

    // 3. Assign INFOR to Guru TKJ in X TSM 2
    $s_infor = \App\Models\Subject::where('school_id', 3)->where('subject_name', 'LIKE', '%Informatika%')->first();

    if ($s_infor) {
        $ta = \App\Models\TeachingAssignment::firstOrCreate([
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
        echo "Created NEW INFOR TA for Guru TKJ! TA_ID: " . $ta->id . "\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
