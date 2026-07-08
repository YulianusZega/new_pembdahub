<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Employee;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;

$dryRun = true; // Set to false to apply changes

echo "=== DRY RUN: FIXING USER ID MISMATCHES ===\n";
$teachers = Teacher::all();
$mismatchesCount = 0;

foreach ($teachers as $t) {
    $e = $t->employee;
    if ($e && $t->user_id != $e->user_id) {
        $mismatchesCount++;
        echo "Teacher: {$t->full_name} (ID: {$t->id})\n";
        echo "  - Current Teacher user_id: {$t->user_id} (Username: " . (User::find($t->user_id)->username ?? 'N/A') . ")\n";
        echo "  - Target Employee user_id: {$e->user_id} (Username: " . (User::find($e->user_id)->username ?? 'N/A') . ")\n";
        
        if (!$dryRun) {
            $oldUserId = $t->user_id;
            // Update teacher user_id
            $t->user_id = $e->user_id;
            $t->save();
            echo "    [SUCCESS] Updated Teacher user_id to {$e->user_id}\n";
            
            // Delete old unused user account if it starts with 'guru'
            $oldUser = User::find($oldUserId);
            if ($oldUser && str_starts_with($oldUser->username, 'guru')) {
                $oldUser->delete();
                echo "    [SUCCESS] Deleted old placeholder user account: {$oldUser->username}\n";
            }
        }
    }
}
echo "Total mismatched teachers found: $mismatchesCount\n\n";

echo "=== DRY RUN: CLEANING UP ORPHAN GURU EMPLOYEES ===\n";
$orphans = Employee::where('employee_type', 'guru')
    ->whereDoesntHave('teacher')
    ->get();
$orphansCount = 0;

foreach ($orphans as $e) {
    $orphansCount++;
    echo "Orphaned Employee: {$e->full_name} (ID: {$e->id}, Code: {$e->employee_code}, School ID: {$e->school_id})\n";
    echo "  - Associated User ID: " . ($e->user_id ?? 'NONE') . " (Username: " . (User::find($e->user_id)->username ?? 'N/A') . ")\n";
    
    if (!$dryRun) {
        // Delete related positions
        $positionsDeleted = DB::table('employee_positions')->where('employee_id', $e->id)->delete();
        echo "    Deleted $positionsDeleted positions\n";
        
        // Delete user account
        if ($e->user_id) {
            $user = User::find($e->user_id);
            if ($user) {
                $user->delete();
                echo "    Deleted User account: {$user->username}\n";
            }
        }
        
        // Delete employee
        $e->delete();
        echo "    [SUCCESS] Deleted Employee ID {$e->id}\n";
    }
}
echo "Total orphaned guru employees found: $orphansCount\n";
