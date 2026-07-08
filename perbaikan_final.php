<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

echo "<pre style='background:#111; color:#0f0; padding:20px; border-radius:10px; font-size:14px; font-family:monospace;'>";
echo "<h1>=== ALGORITMA PERBAIKAN TOTAL (GURU & SISTEMA SMK) ===</h1>\n";

try {
    // ============================================
    // 1. BERSIHKAN DATA SAMPAH (ORPHANED USERS)
    // ============================================
    echo "<h3>[TAHAP 1] Membersihkan Data Sampah (User Tanpa Identitas)...</h3>\n";
    $allUsers = User::whereIn('school_id', [1, 3])->whereIn('role', ['guru', 'siswa'])->get();
    $deletedUsers = 0;

    foreach ($allUsers as $u) {
        $isLinked = false;
        if ($u->role === 'guru') {
            $isLinked = Employee::where('user_id', $u->id)->exists();
        } else if ($u->role === 'siswa') {
            $isLinked = Student::where('user_id', $u->id)->exists();
        }

        if (!$isLinked) {
            echo "[-] MENGHAPUS DATA YATIM/KOSONG: ID {$u->id} | Role: {$u->role} | Email: {$u->email} | Username: {$u->username}\n";
            $u->delete();
            $deletedUsers++;
        }
    }
    echo "<b>Total Data Sampah Terhapus: $deletedUsers</b>\n";

    // ============================================
    // 2. PERBAIKI GURU (School 1 & 3)
    // ============================================
    echo "\n<h3>[TAHAP 2] Memperbaiki & Sinkronisasi GURU...</h3>\n";
    foreach ([1 => 'gurusmpsp2', 3 => 'gurusmks'] as $schoolId => $passRaw) {
        $guruPass = Hash::make($passRaw);
        $domain = $schoolId == 1 ? 'smpp2.pembdahub.com' : 'smk.pembdahub.com';
        
        $gurus = Employee::where('school_id', $schoolId)->where('employee_type', 'guru')->get();
        
        foreach ($gurus as $guru) {
            $firstName = preg_replace('/[^a-z0-9]/', '', strtolower(explode(' ', trim(preg_replace('/,.*$/', '', $guru->full_name)))[0]));
            if(empty($firstName)) $firstName = "guru";
            
            $user = $guru->user;
            
            if (!$user) {
                // Find fallback by likely username/email or just CREATE.
                $user = User::create([
                    'name' => $guru->full_name,
                    'username' => $firstName . time() . uniqid(), // Temp unik
                    'email' => time() . uniqid() . '@temp.com',
                    'password' => $guruPass,
                    'role' => 'guru',
                    'school_id' => $schoolId,
                    'is_active' => true,
                    'must_change_password' => true
                ]);
                $guru->user_id = $user->id;
                $guru->save();
            }

            // Atur/Update nilai sebenarnya (Pastikan nama tidak kosong)
            $expectedEmail = $firstName . '@' . $domain;
            $expectedUsername = $firstName;
            
            $counter = 1;
            while (
                User::where('email', $expectedEmail)->where('id', '!=', $user->id)->exists() ||
                User::where('username', $expectedUsername)->where('id', '!=', $user->id)->exists()
            ) {
                $expectedEmail = $firstName . $counter . '@' . $domain;
                $expectedUsername = $firstName . $counter;
                $counter++;
            }

            $user->name = $guru->full_name; // SINKRONISASI NAMA (YG KOSONG SEBELUMNYA)
            $user->username = $expectedUsername;
            $user->email = $expectedEmail;
            $user->password = $guruPass;
            $user->save();

            echo "[+] SUCCESS GURU: {$user->name} -> Username: {$user->username} | Email: {$user->email}\n";
        }
    }

    // ============================================
    // 3. PERBAIKI SISWA (School 1 & 3)
    // ============================================
    echo "\n<h3>[TAHAP 3] Memperbaiki & Sinkronisasi SISWA...</h3>\n";
    foreach ([1 => 'siswasmpsp2', 3 => 'siswasmks'] as $schoolId => $passRaw) {
        $siswaPass = Hash::make($passRaw);
        $domain = $schoolId == 1 ? 'smpp2.pembdahub.com' : 'smk.pembdahub.com';
        
        $siswas = Student::where('school_id', $schoolId)->get();
        
        foreach ($siswas as $siswa) {
            $firstName = preg_replace('/[^a-z0-9]/', '', strtolower(explode(' ', trim($siswa->full_name))[0]));
            if(empty($firstName)) $firstName = "student";
            
            $user = $siswa->user;
            
            if (!$user) {
                $expectedEmail = $firstName . '@' . $domain;
                
                // Coba cari if orphaned before:
                $existingUser = User::where('username', $siswa->nisn)->where('role', 'siswa')->first();
                if(!$existingUser) {
                    $existingUser = User::where('email', $expectedEmail)->where('role', 'siswa')->first();
                }

                if ($existingUser) {
                    $user = $existingUser;
                } else {
                    $user = User::create([
                        'name' => $siswa->full_name,
                        'username' => $firstName . time() . uniqid(), // Temp unik
                        'email' => time() . uniqid() . '@temp.com',
                        'password' => $siswaPass,
                        'role' => 'siswa',
                        'school_id' => $schoolId,
                        'is_active' => true,
                        'must_change_password' => true
                    ]);
                }
                $siswa->user_id = $user->id;
                $siswa->save();
            }

            // Atur/Update nilai sebenarnya
            $expectedEmail = $firstName . '@' . $domain;
            $expectedUsername = $siswa->nisn ?: ($firstName . rand(100,9999));
            
            $counter = 1;
            while (
                User::where('email', $expectedEmail)->where('id', '!=', $user->id)->exists() ||
                User::where('username', $expectedUsername)->where('id', '!=', $user->id)->exists()
            ) {
                $expectedEmail = $firstName . $counter . '@' . $domain;
                // Jangan ubah username yg valid nisn, jadikan temp
                if(User::where('username', $expectedUsername)->where('id', '!=', $user->id)->exists()) {
                    $expectedUsername = $expectedUsername . $counter;
                }
                $counter++;
            }

            $user->name = $siswa->full_name; // SINKRONISASI NAMA
            $user->username = $expectedUsername; // SINKRONISASI USERNAME
            $user->email = $expectedEmail;
            $user->password = $siswaPass;
            $user->save();
            
            // Log ke output tapi jangan banyak sekali supaya tidak lag (hanya tanda titik)
            echo "."; 
        }
    }

    echo "\n\n<b><h2 style='color:#0f0;'>✅ PERBAIKAN 100% SUKSES DAN SELESAI! SILAKAN CEK DI WEBSITE!</h2></b>\n";

} catch (\Exception $e) {
    echo "\n<h2 style='color:red;'>[ERROR TERJADI] " . $e->getMessage() . "</h2>\n";
}
echo "</pre>";
