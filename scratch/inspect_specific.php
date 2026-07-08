<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;

$names = ['solidarman', 'rahayu', 'yoni', 'yonata'];
foreach ($names as $name) {
    echo "=== INSPECTING NAME LIKE '$name' ===\n";
    $emps = Employee::where('full_name', 'like', "%$name%")->get();
    foreach ($emps as $e) {
        echo "Employee ID: {$e->id} | Code: {$e->employee_code} | Name: {$e->full_name} | School ID: {$e->school_id} | User ID: " . ($e->user_id ?? 'NULL') . "\n";
        $u = User::find($e->user_id);
        if ($u) {
            echo "  User: ID: {$u->id} | Username: {$u->username} | Role: {$u->role}\n";
        }
        $t = Teacher::where('employee_id', $e->id)->first();
        if ($t) {
            echo "  Teacher: ID: {$t->id} | Code: {$t->teacher_code} | Name: {$t->full_name} | User ID: " . ($t->user_id ?? 'NULL') . "\n";
            $tu = User::find($t->user_id);
            if ($tu) {
                echo "    Teacher User: ID: {$tu->id} | Username: {$tu->username} | Role: {$tu->role}\n";
            }
        }
    }
}
