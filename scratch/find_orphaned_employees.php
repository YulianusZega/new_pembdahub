<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;

$orphans = Employee::where('employee_type', 'guru')
    ->whereDoesntHave('teacher')
    ->get();

echo "=== GURU EMPLOYEES WITHOUT TEACHER RECORD ===\n";
foreach ($orphans as $e) {
    echo "Employee ID: {$e->id} | Code: {$e->employee_code} | Name: {$e->full_name} | School ID: {$e->school_id}\n";
}
