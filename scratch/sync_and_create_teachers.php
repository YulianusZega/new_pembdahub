<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

DB::beginTransaction();

try {
    echo "=== RUNNING DATABASE SYNCHRONIZATION ===\n";
    
    // 1. FIX USER ID MISMATCHES
    echo "\n[1] Fixing mismatched user IDs in teachers table...\n";
    $teachers = Teacher::all();
    $mismatchesFixed = 0;
    
    foreach ($teachers as $t) {
        $e = $t->employee;
        if ($e && $t->user_id != $e->user_id) {
            $oldUserId = $t->user_id;
            $newUserId = $e->user_id;
            
            echo "- Teacher '{$t->full_name}' (ID: {$t->id}): changing user_id from {$oldUserId} to {$newUserId}\n";
            
            $t->user_id = $newUserId;
            $t->save();
            
            $mismatchesFixed++;
            
            // Delete old placeholder account (starts with 'guru')
            $oldUser = User::find($oldUserId);
            if ($oldUser && str_starts_with($oldUser->username, 'guru')) {
                echo "  -> Deleting unused placeholder user: {$oldUser->username}\n";
                $oldUser->delete();
            }
        }
    }
    echo "Total user ID mismatches fixed: {$mismatchesFixed}\n";

    // 2. CREATE TEACHER RECORDS FOR ORPHANED EMPLOYEES
    echo "\n[2] Creating missing Teacher records for orphaned guru employees...\n";
    $orphans = Employee::where('employee_type', 'guru')
        ->whereDoesntHave('teacher')
        ->get();
    $teachersCreated = 0;
    
    foreach ($orphans as $e) {
        echo "- Employee '{$e->full_name}' (ID: {$e->id}, Code: {$e->employee_code}): creating Teacher record\n";
        
        Teacher::create([
            'employee_id' => $e->id,
            'user_id' => $e->user_id,
            'school_id' => $e->school_id,
            'teacher_code' => $e->employee_code ?: 'TCH-' . $e->id,
            'full_name' => $e->full_name,
            'gender' => $e->gender ?: 'L',
            'birth_place' => $e->birth_place,
            'birth_date' => $e->birth_date,
            'religion' => $e->religion,
            'address' => $e->address,
            'phone' => $e->phone,
            'photo' => $e->photo,
            'position' => null,
            'is_active' => $e->is_active ?? 1,
        ]);
        
        $teachersCreated++;
    }
    echo "Total missing Teacher records created: {$teachersCreated}\n";

    DB::commit();
    echo "\n✅ SUCCESS: Database synchronization completed successfully!\n";

} catch (\Exception $ex) {
    DB::rollBack();
    echo "\n❌ ERROR: Database synchronization failed: " . $ex->getMessage() . "\n";
    echo $ex->getTraceAsString() . "\n";
}
