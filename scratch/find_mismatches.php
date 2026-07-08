<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;

$teachers = Teacher::all();
echo "=== MISMATCHED USER IDS BETWEEN EMPLOYEES AND TEACHERS ===\n";
foreach ($teachers as $t) {
    $e = $t->employee;
    if ($e) {
        if ($t->user_id != $e->user_id) {
            echo "Teacher ID: {$t->id} | Employee ID: {$e->id} | Name: {$t->full_name}\n";
            echo "  * Teacher user_id: " . ($t->user_id ?? 'NULL') . "\n";
            echo "  * Employee user_id: " . ($e->user_id ?? 'NULL') . "\n";
            
            $tu = User::find($t->user_id);
            $eu = User::find($e->user_id);
            
            if ($tu) {
                echo "    Teacher User: ID: {$tu->id} | Username: {$tu->username} | Email: {$tu->email} | Role: {$tu->role}\n";
            }
            if ($eu) {
                echo "    Employee User: ID: {$eu->id} | Username: {$eu->username} | Email: {$eu->email} | Role: {$eu->role}\n";
            }
        }
    } else {
        echo "Teacher ID: {$t->id} has NO corresponding Employee!\n";
    }
}
