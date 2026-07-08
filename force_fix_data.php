<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

echo "<pre>=== FORCE FIX SCRIPT ===\n";

try {
    // 1. FIX GURU SMK
    echo "\n[1] Memperbaiki Nama Guru SMK yang kosong...\n";
    $guruSmk = Employee::where('school_id', 3)->where('employee_type', 'guru')->get();
    $guruPass = Hash::make('gurusmks');
    
    foreach ($guruSmk as $guru) {
        $user = $guru->user;
        
        $firstName = preg_replace('/[^a-z0-9]/', '', strtolower(explode(' ', trim(preg_replace('/,.*$/', '', $guru->full_name)))[0]));
        if(empty($firstName)) $firstName = "guru";
        
        if (!$user) {
            // Find by expected email
            $expectedEmail = $firstName . '@smk.pembdahub.com';
            $user = User::where('email', $expectedEmail)->first();
        }
        
        if ($user) {
            // Update name if empty
            if (empty($user->name)) {
                $user->name = $guru->full_name;
            }
            // Update username if empty
            if (empty($user->username)) {
                $user->username = $firstName;
            }
            $user->save();
            
            // Link
            $guru->user_id = $user->id;
            $guru->save();
            echo "- Diperbaiki & Linked: {$user->email} sekarang bernama {$user->name} ({$user->username})\n";
        } else {
            // TERNYATA BELUM DIBUAT! BUAT SEKARANG!
            echo "- MEMBUAT USER GURU BARU: {$guru->full_name}...\n";
            try {
                $email = $firstName . '@smk.pembdahub.com';
                $counter = 1;
                while (User::where('email', $email)->orWhere('username', $firstName)->exists()) {
                    $email = $firstName . $counter . '@smk.pembdahub.com';
                    $firstName = $firstName . $counter;
                    $counter++;
                }
                
                $newUser = User::create([
                    'name' => $guru->full_name,
                    'username' => $firstName,
                    'email' => $email,
                    'password' => $guruPass,
                    'role' => 'guru',
                    'school_id' => 3,
                    'is_active' => true,
                    'must_change_password' => true
                ]);
                $guru->user_id = $newUser->id;
                $guru->save();
                echo "  -> BERHASIL DIBUAT: {$email}\n";
            } catch (\Exception $e) {
                echo "  -> GAGAL MEMBUAT GURU: " . $e->getMessage() . "\n";
            }
        }
    }

    // 2. FIX SISWA SMK
    echo "\n[2] Memperbaiki Siswa SMK yang tidak ada user...\n";
    $siswaSmk = Student::where('school_id', 3)->get();
    $siswaPass = Hash::make('siswasmks');
    $orphanSiswaCount = 0;
    
    foreach ($siswaSmk as $siswa) {
        if (!$siswa->user_id) {
            // Coba cari apakah ada akun siswanya berdasarkan nisn atau bagian nama
            $user = User::where('username', $siswa->nisn)->where('role', 'siswa')->first();
            
            $firstName = preg_replace('/[^a-z0-9]/', '', strtolower(explode(' ', trim($siswa->full_name))[0]));
            if(empty($firstName)) $firstName = "student";
            
            if (!$user) {
                $expectedEmail = $firstName . '@smk.pembdahub.com';
                $user = User::where('email', $expectedEmail)->where('role', 'siswa')->first();
            }

            if ($user) {
                // Link
                $siswa->user_id = $user->id;
                $siswa->save();
                
                if(empty($user->name)) {
                   $user->name = $siswa->full_name;
                }
                if(empty($user->username)) {
                   $user->username = $siswa->nisn;
                }
                $user->email = preg_replace('/[0-9]*@temp\.com/', '@smk.pembdahub.com', $user->email);
                $user->save();
                
                $orphanSiswaCount++;
            } else {
                 echo "- MEMBUAT USER SISWA BARU: {$siswa->full_name}...\n";
                 try {
                     $email = $firstName . '@smk.pembdahub.com';
                     $username = $siswa->nisn;
                     
                     if (empty($username)) $username = $firstName . rand(100,999);
                     
                     $counter = 1;
                     while (User::where('email', $email)->orWhere('username', $username)->exists()) {
                         $email = $firstName . $counter . '@smk.pembdahub.com';
                         // Jangan ubah username NISN kecuali bentrok
                         if (User::where('username', $username)->exists()) {
                             $username = $username . $counter;
                         }
                         $counter++;
                     }
                     
                     $newUser = User::create([
                        'name' => $siswa->full_name,
                        'username' => $username,
                        'email' => $email,
                        'password' => $siswaPass,
                        'role' => 'siswa',
                        'school_id' => 3,
                        'is_active' => true,
                        'must_change_password' => true
                     ]);
                     
                     $siswa->user_id = $newUser->id;
                     $siswa->save();
                     echo "  -> BERHASIL DIBUAT: {$email}\n";
                 } catch (\Exception $e) {
                     echo "  -> GAGAL MEMBUAT SISWA: " . $e->getMessage() . "\n";
                 }
            }
        }
    }
    echo "Total Siswa SMK yang dihubungkan ulang / diperbaiki: $orphanSiswaCount\n";

} catch (\Exception $e) {
    echo "ERROR UMUM: " . $e->getMessage() . "\n";
}
echo "\nSelesai!</pre>";
