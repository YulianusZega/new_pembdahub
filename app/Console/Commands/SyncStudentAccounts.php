<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncStudentAccounts extends Command
{
    protected $signature = 'pembda:sync-students';
    protected $description = 'Sinkronisasi email dan password siswa SMP & SMK';

    public function handle()
    {
        $this->info("=== MEMULAI SINKRONISASI AKUN SISWA ===");

        DB::beginTransaction();
        try {
            // PROSES SMP
            $this->info("Memproses SMP...");
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
                if (empty($fn)) $fn = "student";
                $em = $fn . '@smpp2.pembdahub.com'; $c = 1;
                while (in_array($em, $usedSmp) || User::where('email', $em)->where('id', '!=', $u->id)->exists()) {
                    $em = $fn . $c . '@smpp2.pembdahub.com'; $c++;
                }
                $usedSmp[] = $em;
                $u->update([
                    'name' => $s->full_name,
                    'email' => $em, 
                    'password' => $smpPass, 
                    'username' => $s->nisn, 
                    'must_change_password' => true
                ]);
            }

            // PROSES SMK
            $this->info("Memproses SMK...");
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
                if (empty($fn)) $fn = "student";
                $em = $fn . '@smk.pembdahub.com'; $c = 1;
                while (in_array($em, $usedSmk) || User::where('email', $em)->where('id', '!=', $u->id)->exists()) {
                    $em = $fn . $c . '@smk.pembdahub.com'; $c++;
                }
                $usedSmk[] = $em;
                $u->update([
                    'name' => $s->full_name,
                    'email' => $em, 
                    'password' => $smkPass, 
                    'username' => $s->nisn,
                    'must_change_password' => true
                ]);
            }

            DB::commit();
            $this->info("✅ BERHASIL SELESAI!");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("GAGAL: " . $e->getMessage());
        }
    }
}
