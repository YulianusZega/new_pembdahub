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

echo "=== CHECKING ORPHAN NAMES IN TEACHERS TABLE ===\n";
foreach ($orphans as $e) {
    $cleanName = trim(preg_replace('/\b(s\.pd\b|m\.pd\b|s\.ag\b|s\.e\b|s\.th\b|dra\b|drs\b|sh\b|a\.md\.t\b|s\.kom\b|s\.fil\b|gr\b)/i', '', $e->full_name));
    // Remove trailing comma
    $cleanName = rtrim($cleanName, ',');
    $cleanName = trim($cleanName);
    
    $matches = Teacher::where('full_name', 'like', "%$cleanName%")->get();
    echo "Employee: {$e->full_name} (ID: {$e->id}, Code: {$e->employee_code}, School ID: {$e->school_id})\n";
    if ($matches->isEmpty()) {
        echo "  -> NO matches in teachers table!\n";
    } else {
        foreach ($matches as $m) {
            echo "  -> MATCH in teachers: ID: {$m->id}, Code: {$m->teacher_code}, Name: {$m->full_name}, School ID: {$m->school_id}, Employee ID: " . ($m->employee_id ?? 'NULL') . "\n";
        }
    }
}
