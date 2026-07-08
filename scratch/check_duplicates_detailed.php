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

echo "=== CHECKING IF ORPHANS HAVE ACTIVE CORRESPONDING EMPLOYEES ===\n";
foreach ($orphans as $e) {
    $cleanName = trim(preg_replace('/\b(s\.pd\b|m\.pd\b|s\.ag\b|s\.e\b|s\.th\b|dra\b|drs\b|sh\b|a\.md\.t\b|s\.kom\b|s\.fil\b|gr\b)/i', '', $e->full_name));
    $cleanName = rtrim($cleanName, ',');
    $cleanName = trim($cleanName);
    
    // Find OTHER employees with similar name
    $others = Employee::where('id', '!=', $e->id)
        ->where('full_name', 'like', "%$cleanName%")
        ->get();
        
    echo "Orphan: {$e->full_name} (ID: {$e->id}, Code: {$e->employee_code})\n";
    if ($others->isEmpty()) {
        echo "  -> No other employee with similar name.\n";
    } else {
        foreach ($others as $o) {
            $hasTeacher = $o->teacher ? "Yes (Teacher ID: {$o->teacher->id})" : "No";
            echo "  -> Found other employee: ID: {$o->id}, Code: {$o->employee_code}, Name: {$o->full_name}, School ID: {$o->school_id}, Has Teacher: $hasTeacher\n";
        }
    }
}
