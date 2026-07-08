<?php
// Aktifkan Error Reporting untuk melihat masalahnya jika gagal
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Tingkatkan batas waktu dan memori (proses 1000 Bcrypt butuh waktu)
set_time_limit(600); 
ini_set('memory_limit', '512M');

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

echo "<pre>=== SISTEM SINKRONISASI AKUN PEMBDA HUB ===\n";
echo "Waktu Mulai: " . date('Y-m-d H:i:s') . "\n\n";

DB::beginTransaction();
try {
    // --- PROSES SMP ---
    echo "Processing SMP (School ID: 1)...\n";
    $smpPass = Hash::make('siswasmpsp2');
    $smpStudents = Student::where('school_id', 1)->get();
    $usedSmp = [];
    
    foreach ($smpStudents as $s) {
        $u = $s->user ?: User::create([
            'name' => $s->full_name,
            'username' => $s->nisn,
            'email' => Str::random(10) . '@temp.com',
            'password' => $smpPass,
            'role' => 'siswa',
            'school_id' => 1,
            'is_active' => true
        ]);
        
        if (!$s->user_id) $s->update(['user_id' => $u->id]);

        $fn = preg_replace('/[^a-z0-9]/', '', strtolower(explode(' ', trim($s->full_name))[0]));
        if (empty($fn)) $fn = "user";

        $em = $fn . '@smpp2.pembdahub.com'; $c = 1;
        while (in_array($em, $usedSmp) || User::where('email', $em)->where('id', '!=', $u->id)->exists()) {
            $em = $fn . $c . '@smpp2.pembdahub.com'; $c++;
        }
        $usedSmp[] = $em;
        
        $u->update(['email' => $em, 'password' => $smpPass, 'username' => $s->nisn, 'must_change_password' => true]);
    }
    echo "✓ SMP: " . count($smpStudents) . " akun sinkron.\n\n";

    // --- PROSES SMK ---
    echo "Processing SMK (School ID: 3)...\n";
    $smkPass = Hash::make('siswasmks');
    $smkStudents = Student::where('school_id', 3)->get();
    $usedSmk = [];
    
    foreach ($smkStudents as $s) {
        $u = $s->user ?: User::create([
            'name' => $s->full_name,
            'email' => Str::random(10) . '@temp.com',
            'password' => $smkPass,
            'role' => 'siswa',
            'school_id' => 3,
            'is_active' => true
        ]);
        
        if (!$s->user_id) $s->update(['user_id' => $u->id]);

        $fn = preg_replace('/[^a-z0-9]/', '', strtolower(explode(' ', trim($s->full_name))[0]));
        if (empty($fn)) $fn = "user";

        $em = $fn . '@smk.pembdahub.com'; $c = 1;
        while (in_array($em, $usedSmk) || User::where('email', $em)->where('id', '!=', $u->id)->exists()) {
            $em = $fn . $c . '@smk.pembdahub.com'; $c++;
        }
        $usedSmk[] = $em;
        
        $u->update(['email' => $em, 'password' => $smkPass, 'must_change_password' => true]);
    }
    echo "✓ SMK: " . count($smkStudents) . " akun sinkron.\n";

    DB::commit();
    echo "\n✅ SEMUA PROSES BERHASIL SELESAI PADA " . date('H:i:s') . "\n";
    echo "Script ini sudah bisa dihapus dari server.</pre>";

} catch (Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR TERDETEKSI:\n" . $e->getMessage() . "\n";
    echo "At File: " . $e->getFile() . " line " . $e->getLine() . "</pre>";
}
