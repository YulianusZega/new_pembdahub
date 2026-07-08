<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;
use App\Models\School;

echo "=== ROLES IN USERS TABLE ===\n";
$roles = User::select('role', \DB::raw('count(*) as count'))->groupBy('role')->get();
foreach ($roles as $r) {
    echo "- Role: {$r->role} | Count: {$r->count}\n";
}

echo "\n=== ADMINS & BENDAHARA ===\n";
$users = User::whereIn('role', ['superadmin', 'admin_sekolah', 'bendahara', 'ketua_yayasan'])->get();
foreach ($users as $u) {
    echo "User ID: {$u->id} | Name: {$u->name} | Role: {$u->role} | School ID: {$u->school_id}\n";
}

echo "\n=== EMPLOYEES BY POSITION ===\n";
$employees = Employee::active()->with(['school', 'positions'])->get();
echo "Total Active Employees: " . $employees->count() . "\n";

$principals = [];
$teachers = [];
$staff = [];

foreach ($employees as $emp) {
    $primaryPos = $emp->getPrimaryPosition();
    $posName = $primaryPos ? $primaryPos->position_name : ($emp->employee_type === 'guru' ? 'Guru' : 'Pegawai');
    
    // Check if position or name implies principal (Kepala Sekolah)
    $isPrincipal = false;
    if ($primaryPos && (str_contains(strtolower($primaryPos->position_name), 'kepala sekolah') || str_contains(strtolower($primaryPos->position_code), 'kepsek') || str_contains(strtolower($primaryPos->position_code), 'kasek'))) {
        $isPrincipal = true;
    }
    // Also check employee_positions table
    foreach ($emp->positions as $pos) {
        if (str_contains(strtolower($pos->position_name), 'kepala sekolah') || str_contains(strtolower($pos->position_code), 'kepsek')) {
            $isPrincipal = true;
            $posName = $pos->position_name;
        }
    }

    if ($isPrincipal) {
        $principals[] = [
            'id' => $emp->id,
            'name' => $emp->full_name,
            'school' => $emp->school->name ?? 'N/A',
            'phone' => $emp->phone,
            'position' => $posName
        ];
    } elseif ($emp->employee_type === 'guru') {
        $teachers[] = [
            'id' => $emp->id,
            'name' => $emp->full_name,
            'school' => $emp->school->name ?? 'N/A',
            'phone' => $emp->phone,
            'position' => $posName
        ];
    } else {
        $staff[] = [
            'id' => $emp->id,
            'name' => $emp->full_name,
            'school' => $emp->school->name ?? 'N/A',
            'phone' => $emp->phone,
            'position' => $posName
        ];
    }
}

echo "\n=== PRINCIPALS FOUND ===\n";
foreach ($principals as $p) {
    echo "- Name: {$p['name']} | School: {$p['school']} | Phone: {$p['phone']} | Pos: {$p['position']}\n";
}

echo "\nTotal Principals: " . count($principals) . "\n";
echo "Total Teachers: " . count($teachers) . "\n";
echo "Total Staff: " . count($staff) . "\n";
