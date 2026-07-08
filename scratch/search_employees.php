<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;

echo "=== SEARCH IN EMPLOYEES TABLE ===\n";

// 1. Search by name containing admin or bendahara
$empByName = Employee::where('full_name', 'like', '%admin%')
    ->orWhere('full_name', 'like', '%bendahara%')
    ->get();
echo "Found by name: " . $empByName->count() . "\n";
foreach ($empByName as $e) {
    echo "ID: {$e->id} | Name: {$e->full_name} | Phone: {$e->phone} | Type: {$e->employee_type}\n";
}

// 2. Search by position containing admin or bendahara
$employees = Employee::active()->with(['positions', 'school'])->get();
$foundByPos = 0;
foreach ($employees as $e) {
    foreach ($e->positions as $pos) {
        $name = strtolower($pos->position_name);
        if (str_contains($name, 'admin') || str_contains($name, 'bendahara') || str_contains($name, 'operator') || str_contains($name, 'keuangan')) {
            echo "ID: {$e->id} | Name: {$e->full_name} | Phone: {$e->phone} | Position: {$pos->position_name} | School: {$e->school->name}\n";
            $foundByPos++;
        }
    }
}
echo "Found by position: " . $foundByPos . "\n";
