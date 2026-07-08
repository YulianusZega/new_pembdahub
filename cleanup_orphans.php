<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Employee;

echo "<pre>=== DEDUP & CLEANUP SCRIPT ===\n";

try {
    // 1. CLEAN UP UNLINKED GURU SMK WITH OLD EMAILS or EMPTY NAMES
    echo "\n[1] Menghapus Guru SMK yang tidak terhubung (Data Lama/Kotor)...\n";
    
    $allGuruSmk = User::where('school_id', 3)->where('role', 'guru')->get();
    $deletedGuru = 0;
    
    foreach ($allGuruSmk as $u) {
        $isLinked = Employee::where('user_id', $u->id)->exists();
        
        if (!$isLinked) {
            // Delete it because it's orphaned and old!
            echo "Menghapus Guru Orphaned: ID {$u->id} | Email: {$u->email} | Username: {$u->username}\n";
            $u->delete();
            $deletedGuru++;
        }
    }
    echo "Total Guru SMK terhapus (Orphan): $deletedGuru\n";

    // 2. CLEAN UP UNLINKED SISWA SMK
    echo "\n[2] Menghapus Siswa SMK yang tidak terhubung (Data Lama/Kotor)...\n";
    $allSiswaSmk = User::where('school_id', 3)->where('role', 'siswa')->get();
    $deletedSiswa = 0;
    
    foreach ($allSiswaSmk as $u) {
        $isLinked = \App\Models\Student::where('user_id', $u->id)->exists();
        
        if (!$isLinked) {
            echo "Menghapus Siswa Orphaned: ID {$u->id} | Email: {$u->email} | Username: {$u->username}\n";
            $u->delete();
            $deletedSiswa++;
        }
    }
    echo "Total Siswa SMK terhapus (Orphan): $deletedSiswa\n";

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "\nSelesai!</pre>";
