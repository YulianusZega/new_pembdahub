<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

function cleanName($name) {
    $name = strtolower($name);
    // Remove titles like S.Pd, M.Pd, S.Ag, S.E, S.Th, Dra., Drs., SH, A.Md.T, etc.
    $name = preg_replace('/\b(s\.pd\b|m\.pd\b|s\.ag\b|s\.e\b|s\.th\b|dra\b|drs\b|sh\b|a\.md\.t\b|s\.kom\b|s\.fil\b|gr\b)/', '', $name);
    $name = preg_replace('/[^a-z]/', '', $name);
    return $name;
}

$emps = Employee::where('employee_type', 'guru')->get();
echo "=== SIMILAR GURU EMPLOYEES ===\n";

for ($i = 0; $i < count($emps); $i++) {
    for ($j = $i + 1; $j < count($emps); $j++) {
        $e1 = $emps[$i];
        $e2 = $emps[$j];
        
        $c1 = cleanName($e1->full_name);
        $c2 = cleanName($e2->full_name);
        
        // If clean names are identical or very close (levenshtein distance <= 2)
        if ($c1 === $c2 || (strlen($c1) > 4 && levenshtein($c1, $c2) <= 2)) {
            echo "\nMatch found:\n";
            echo "  1. Employee ID: {$e1->id}, Code: {$e1->employee_code}, Name: {$e1->full_name}, School ID: {$e1->school_id}, User ID: " . ($e1->user_id ?? 'NULL') . "\n";
            $t1 = Teacher::where('employee_id', $e1->id)->first();
            if ($t1) {
                echo "     * Teacher ID: {$t1->id}, Code: {$t1->teacher_code}, User ID: " . ($t1->user_id ?? 'NULL') . "\n";
            }
            echo "  2. Employee ID: {$e2->id}, Code: {$e2->employee_code}, Name: {$e2->full_name}, School ID: {$e2->school_id}, User ID: " . ($e2->user_id ?? 'NULL') . "\n";
            $t2 = Teacher::where('employee_id', $e2->id)->first();
            if ($t2) {
                echo "     * Teacher ID: {$t2->id}, Code: {$t2->teacher_code}, User ID: " . ($t2->user_id ?? 'NULL') . "\n";
            }
        }
    }
}
