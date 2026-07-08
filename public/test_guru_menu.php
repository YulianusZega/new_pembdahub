<?php
if (($_GET['secret'] ?? '') !== 'pembda99') {
    die('Forbidden');
}

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

header('Content-Type: text/plain; charset=utf-8');

echo "=== TEST MENU GURU & PROFILE ===\n\n";

$teacher = \App\Models\Teacher::with('school')->find(253);
if (!$teacher) {
    die("Teacher 253 tidak ditemukan.\n");
}

echo "Teacher ID: {$teacher->id}\n";
echo "Teacher Name: {$teacher->full_name}\n";
echo "School ID: {$teacher->school_id}\n";
echo "School Name: " . ($teacher->school ? $teacher->school->name : 'NULL') . "\n";
echo "School Type: " . ($teacher->school ? $teacher->school->type : 'NULL') . "\n";

$isSmaOrSmkTeacher = in_array($teacher->school?->type ?? '', ['SMA', 'SMK']);
echo "Is SMA or SMK Teacher? " . ($isSmaOrSmkTeacher ? 'YES' : 'NO') . "\n";

// Cek apakah user 2114 dikaitkan dengan guru ini
$user = \App\Models\User::find(2114);
if ($user) {
    echo "\nUser ID: 2114\n";
    echo "User Name: {$user->name}\n";
    echo "User Username: {$user->username}\n";
    echo "User Role: {$user->role}\n";
    
    // Cek relasi $user->teacher
    $userTeacher = $user->teacher;
    if ($userTeacher) {
        echo "User->teacher relation loaded: ID {$userTeacher->id} ({$userTeacher->full_name})\n";
    } else {
        echo "!!! ERROR: User->teacher relation returned NULL !!!\n";
        
        // Cari tau apakah ada guru lain dengan user_id ini
        $otherTeachers = \App\Models\Teacher::where('user_id', 2114)->get();
        echo "Total Guru dengan user_id 2114: " . $otherTeachers->count() . "\n";
        foreach ($otherTeachers as $ot) {
            echo "  - Teacher ID: {$ot->id} | Name: {$ot->full_name}\n";
        }
    }
} else {
    echo "\nUser 2114 tidak ditemukan.\n";
}
