<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== LATEST STUDENT ATTENDANCE RECORDS ===\n";
$latest = \App\Models\Attendance::with('student')->latest('id')->take(10)->get();
foreach ($latest as $l) {
    echo "ID: {$l->id} | Date: {$l->date} | Student: " . ($l->student->full_name ?? 'N/A') . " | Status: {$l->status} | In: {$l->time_in} | Out: {$l->time_out}\n";
}

echo "\n=== LATEST EMPLOYEE ATTENDANCE RECORDS ===\n";
$latestEmp = \App\Models\EmployeeAttendance::with('employee')->latest('id')->take(10)->get();
foreach ($latestEmp as $l) {
    echo "ID: {$l->id} | Date: {$l->date} | Employee: " . ($l->employee->full_name ?? 'N/A') . " | Status: {$l->status} | In: {$l->time_in} | Out: {$l->time_out}\n";
}
