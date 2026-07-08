<?php
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "MATCHING ORPHANED USERS TO UNLINKED STUDENTS\n";
$output .= "===========================================\n\n";

$orphanedUsers = User::where('role', 'siswa')
    ->whereNotExists(function($query) {
        $query->select(DB::raw(1))
              ->from('students')
              ->whereRaw('students.user_id = users.id');
    })->get();

$output .= "Checking for matches by Name...\n\n";

foreach ($orphanedUsers as $user) {
    $match = Student::where('full_name', 'like', '%' . $user->name . '%')
        ->whereNull('user_id')
        ->first();
    
    if ($match) {
        $output .= "MATCH FOUND!\n";
        $output .= "User: [ID: {$user->id}] {$user->name} (School: {$user->school_id})\n";
        $output .= "Student Record: [ID: {$match->id}] {$match->full_name} (School: {$match->school_id})\n";
        if ($user->school_id != $match->school_id) {
            $output .= "!!! WARNING: School ID Mismatch (User: {$user->school_id} vs Student: {$match->school_id})\n";
        }
        $output .= "---------------------------\n";
    }
}

file_put_contents('matching_results.txt', $output);
echo "Results written to matching_results.txt\n";
