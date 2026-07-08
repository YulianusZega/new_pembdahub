<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class TreasurerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all schools
        $schools = School::all();

        $created = 0;
        $skipped = 0;

        foreach ($schools as $school) {
            // Generate unique username and email
            $schoolCode = $school->school_code ?? 'SCH' . $school->id;
            $schoolCode = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($schoolCode));
            
            $username = 'bendahara_' . $schoolCode;
            $email = 'bendahara.' . $schoolCode . '@pembdahub.sch.id';

            // Check if user already exists
            if (User::where('username', $username)->orWhere('email', $email)->exists()) {
                $this->command->warn("⚠️  Akun bendahara untuk {$school->school_name} sudah ada, dilewati.");
                $skipped++;
                continue;
            }

            // Create bendahara account for each school
            User::create([
                'name' => 'Bendahara ' . $school->school_name,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('Bendahara@2026!'),
                'role' => 'bendahara',
                'school_id' => $school->id,
                'is_active' => true,
                'must_change_password' => true,
            ]);

            $created++;
        }

        $this->command->info("✅ Berhasil membuat {$created} akun bendahara");
        if ($skipped > 0) {
            $this->command->info("⏭️  {$skipped} akun dilewati (sudah ada)");
        }
        $this->command->info('📝 Username format: bendahara_[kode_sekolah]');
        $this->command->info('🔑 Password default: Bendahara@2026!');
        $this->command->info('⚠️  Semua user wajib ubah password saat login pertama!');
    }
}
