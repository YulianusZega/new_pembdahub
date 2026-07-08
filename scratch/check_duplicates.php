<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== DUPLICATE TEACHERS (BY CODE) ===\n";
$dupCodes = Teacher::select('teacher_code', DB::raw('count(*) as count'))
    ->groupBy('teacher_code')
    ->having('count', '>', 1)
    ->pluck('teacher_code');

foreach ($dupCodes as $code) {
    echo "\nTeacher Code: $code\n";
    $teachers = Teacher::where('teacher_code', $code)->get();
    foreach ($teachers as $t) {
        $user = $t->user;
        $employee = $t->employee;
        echo "  - Teacher ID: {$t->id}, Name: {$t->full_name}, User ID: " . ($t->user_id ?? 'NULL') . ", Employee ID: " . ($t->employee_id ?? 'NULL') . ", Active: {$t->is_active}, School ID: {$t->school_id}\n";
        if ($user) {
            echo "    * User: ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role}\n";
        }
        if ($employee) {
            echo "    * Employee: ID: {$employee->id}, Code: {$employee->employee_code}, User ID: " . ($employee->user_id ?? 'NULL') . ", Active: {$employee->is_active}\n";
        }
    }
}

echo "\n=== DUPLICATE EMPLOYEES (BY CODE) ===\n";
$dupEmpCodes = Employee::select('employee_code', DB::raw('count(*) as count'))
    ->where('employee_type', 'guru')
    ->groupBy('employee_code')
    ->having('count', '>', 1)
    ->pluck('employee_code');

foreach ($dupEmpCodes as $code) {
    echo "\nEmployee Code: $code\n";
    $employees = Employee::where('employee_code', $code)->get();
    foreach ($employees as $e) {
        $user = $e->user;
        $teacher = $e->teacher;
        echo "  - Employee ID: {$e->id}, Name: {$e->full_name}, User ID: " . ($e->user_id ?? 'NULL') . ", Active: {$e->is_active}, School ID: {$e->school_id}\n";
        if ($user) {
            echo "    * User: ID: {$user->id}, Username: {$user->username}, Email: {$user->email}, Role: {$user->role}\n";
        }
        if ($teacher) {
            echo "    * Teacher: ID: {$teacher->id}, Code: {$teacher->teacher_code}, Active: {$teacher->is_active}\n";
        }
    }
}

echo "\n=== ALL TEACHERS LIST ===\n";
$teachers = Teacher::all();
foreach ($teachers as $t) {
    echo "ID: {$t->id} | Code: {$t->teacher_code} | Name: {$t->full_name} | EmpID: {$t->employee_id} | UserID: {$t->user_id} | Active: {$t->is_active}\n";
}

echo "\n=== ALL GURU EMPLOYEES LIST ===\n";
$employees = Employee::where('employee_type', 'guru')->get();
foreach ($employees as $e) {
    echo "ID: {$e->id} | Code: {$e->employee_code} | Name: {$e->full_name} | UserID: {$e->user_id} | Active: {$e->is_active}\n";
}
