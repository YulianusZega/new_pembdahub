<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncTeacherAccounts extends Command
{
    protected $signature = 'pembda:sync-teachers';
    protected $description = 'Sinkronisasi email dan password guru SMP & SMK';

    public function handle()
    {
        $this->info("=== MEMULAI SINKRONISASI AKUN GURU ===");

        DB::beginTransaction();
        try {
            // 1. PROSES GURU SMP (School ID: 1)
            $this->info("Memproses Guru SMP (School ID: 1)...");
            $smpPass = Hash::make('gurusmpsp2');
            $smpDomain = 'smpp2.pembdahub.com';
            $this->processTeachers(1, $smpPass, $smpDomain);

            // 2. PROSES GURU SMK (School ID: 3)
            $this->info("Memproses Guru SMK (School ID: 3)...");
            $smkPass = Hash::make('gurusmks');
            $smkDomain = 'smk.pembdahub.com';
            $this->processTeachers(3, $smkPass, $smkDomain);

            DB::commit();
            $this->info("✅ BERHASIL SELESAI!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("GAGAL: " . $e->getMessage());
        }
    }

    private function processTeachers($schoolId, $passwordHash, $domain)
    {
        $teachers = Employee::where('school_id', $schoolId)
            ->where('employee_type', 'guru')
            ->get();

        $usedEmails = [];
        $count = 0;

        foreach ($teachers as $teacher) {
            $user = $teacher->user;
            
            // Ambil nama depan untuk Username & Email
            $fullName = trim($teacher->full_name);
            $cleanName = preg_replace('/,.*$/', '', $fullName); // Hapus gelar
            $nameParts = explode(' ', $cleanName);
            $firstName = strtolower($nameParts[0]);
            $firstName = preg_replace('/[^a-z0-9]/', '', $firstName);
            
            if (empty($firstName)) $firstName = "guru";

            // Logic Email & Username
            $newEmail = $firstName . '@' . $domain;
            $username = $firstName;
            
            $counter = 1;
            while (
                in_array($newEmail, $usedEmails) || 
                User::where('email', $newEmail)->where('id', '!=', ($user->id ?? 0))->exists() ||
                User::where('username', $username)->where('id', '!=', ($user->id ?? 0))->exists()
            ) {
                $newEmail = $firstName . $counter . '@' . $domain;
                $username = $firstName . $counter;
                $counter++;
            }
            $usedEmails[] = $newEmail;

            // Jika belum ada user, buat baru
            if (!$user) {
                $user = User::create([
                    'name' => $teacher->full_name,
                    'username' => $username,
                    'email' => $newEmail,
                    'password' => $passwordHash,
                    'role' => 'guru',
                    'school_id' => $schoolId,
                    'is_active' => true
                ]);
                $teacher->update(['user_id' => $user->id]);
            } else {
                // Update User yang sudah ada
                $user->update([
                    'name' => $teacher->full_name,
                    'username' => $username,
                    'email' => $newEmail,
                    'password' => $passwordHash,
                    'role' => 'guru',
                    'must_change_password' => true
                ]);
            }
            
            $count++;
        }

        $this->info("✓ Berhasil memproses $count guru untuk School ID: $schoolId.");
    }
}
