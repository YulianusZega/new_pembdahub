<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;

echo "=== USERS WITH ROLE admin_sekolah OR bendahara ===\n";
$users = User::whereIn('role', ['admin_sekolah', 'bendahara'])->get();
foreach ($users as $u) {
    $emp = Employee::where('user_id', $u->id)->first();
    $empByFK = $u->employee_id ? Employee::find($u->employee_id) : null;
    echo "User ID: {$u->id} | Name: {$u->name} | Role: {$u->role}\n";
    echo "  - Employee by user_id: " . ($emp ? "FOUND (ID: {$emp->id}, Name: {$emp->full_name}, Phone: {$emp->phone})" : "NOT FOUND") . "\n";
    echo "  - Employee by employee_id: " . ($empByFK ? "FOUND (ID: {$empByFK->id}, Name: {$empByFK->full_name}, Phone: {$empByFK->phone})" : "NOT FOUND") . "\n";
}
