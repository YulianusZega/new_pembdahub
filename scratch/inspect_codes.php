<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Teacher;
use App\Models\Employee;

echo "--- Sample Teacher ---\n";
$teacher = Teacher::first();
if ($teacher) {
    echo "ID: {$teacher->id}\n";
    echo "Teacher Code: {$teacher->teacher_code}\n";
    echo "Full Name: {$teacher->full_name}\n";
    echo "Employee ID: {$teacher->employee_id}\n";
    if ($teacher->employee) {
        echo "Employee Code: {$teacher->employee->employee_code}\n";
        echo "Employee NIP: {$teacher->employee->nip}\n";
    } else {
        echo "No linked employee record!\n";
    }
} else {
    echo "No teacher found!\n";
}

echo "\n--- Sample Employee ---\n";
$employee = Employee::first();
if ($employee) {
    echo "ID: {$employee->id}\n";
    echo "Employee Code: {$employee->employee_code}\n";
    echo "NIP: {$employee->nip}\n";
    echo "Full Name: {$employee->full_name}\n";
} else {
    echo "No employee found!\n";
}
