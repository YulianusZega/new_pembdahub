<?php
use App\Models\User;
use App\Models\Student;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$studentCount = Student::where('school_id', 1)->count();
$userCount = User::where('role', 'siswa')->where('school_id', 1)->count();
$orphanedSiswa = User::where('role', 'siswa')->where('school_id', 1)->whereDoesntHave('student')->count();
$studentsWithoutUser = Student::where('school_id', 1)->whereNull('user_id')->count();

echo "Final SMP Verification:\n";
echo "- Total Students: {$studentCount}\n";
echo "- Total Siswa Users: {$userCount}\n";
echo "- Orphaned Users: {$orphanedSiswa}\n";
echo "- Students without User: {$studentsWithoutUser}\n";

if ($studentCount === $userCount && $orphanedSiswa === 0 && $studentsWithoutUser === 0) {
    echo "🎉 Data Konsisten! (1 Student : 1 User)\n";
} else {
    echo "⚠️ Masih ada ketidakkonsistenan data.\n";
}
