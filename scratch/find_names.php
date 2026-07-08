<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

// Print duplicates based on name (case insensitive, trimmed)
echo "=== DUPLICATE EMPLOYEES BY TRIMMED LOWERCASE NAME ===\n";
$emps = Employee::where('employee_type', 'guru')->get();
$grouped = $emps->groupBy(function($item) {
    return strtolower(trim($item->full_name));
});

foreach ($grouped as $name => $items) {
    if (count($items) > 1) {
        echo "\nName: $name (Count: " . count($items) . ")\n";
        foreach ($items as $item) {
            echo "  - Employee ID: {$item->id}, Code: {$item->employee_code}, NIP: {$item->nip}, Active: {$item->is_active}, User ID: " . ($item->user_id ?? 'NULL') . ", School ID: {$item->school_id}\n";
            $teacher = Teacher::where('employee_id', $item->id)->first();
            if ($teacher) {
                echo "    * Teacher ID: {$teacher->id}, Code: {$teacher->teacher_code}, User ID: " . ($teacher->user_id ?? 'NULL') . "\n";
            } else {
                echo "    * Teacher: NONE\n";
            }
        }
    }
}

echo "\n=== DUPLICATE TEACHERS BY TRIMMED LOWERCASE NAME ===\n";
$teachers = Teacher::all();
$groupedTeachers = $teachers->groupBy(function($item) {
    return strtolower(trim($item->full_name));
});

foreach ($groupedTeachers as $name => $items) {
    if (count($items) > 1) {
        echo "\nName: $name (Count: " . count($items) . ")\n";
        foreach ($items as $item) {
            echo "  - Teacher ID: {$item->id}, Code: {$item->teacher_code}, Active: {$item->is_active}, User ID: " . ($item->user_id ?? 'NULL') . ", Employee ID: " . ($item->employee_id ?? 'NULL') . ", School ID: {$item->school_id}\n";
        }
    }
}
