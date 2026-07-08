<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\AchievementFeeExemptionRule;

class AchievementFeeExemptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Aturan Pembebasan Biaya Pendaftaran Rp 50.000:
     * 1. Juara 1, 2, 3 dari SMPS Pembda 2 → SMA Swasta Pembda 1 / SMK
     * 2. Juara 1 dari SMP luar → SMA/SMK Pembda
     * 3. Juara 1 dari SD → SMPS Pembda 2
     */
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$academicYear) {
            $this->command->error('❌ No active academic year found!');
            return;
        }

        // Get schools
        $smpsPembda2 = School::where('name', 'LIKE', '%SMP%Pembda%2%')->first();
        $smaSwastaPembda1 = School::where('name', 'LIKE', '%SMA%Pembda%1%')->first();
        $smkPembda = School::where('name', 'LIKE', '%SMK%')->first();

        $rules = [];

        // Rule 1: Juara 1, 2, 3 dari SMPS Pembda 2 → SMA Swasta Pembda 1
        if ($smpsPembda2 && $smaSwastaPembda1) {
            $rules[] = [
                'target_school_id' => $smaSwastaPembda1->id,
                'academic_year_id' => $academicYear->id,
                'previous_school_type' => 'pembda',
                'previous_school_name' => 'SMPS Pembda 2',
                'previous_school_level' => 'SMP',
                'eligible_ranks' => json_encode(['1', '2', '3']),
                'proof_type' => 'both',
                'exemption_fee_type' => 'registration',
                'exemption_amount' => 50000,
                'exemption_type' => 'full',
                'description' => 'Bebas biaya pendaftaran Rp 50.000 untuk Juara 1, 2, 3 lulusan SMPS Pembda 2 yang masuk SMA Swasta Pembda 1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Rule 2: Juara 1, 2, 3 dari SMPS Pembda 2 → SMK Pembda
        if ($smpsPembda2 && $smkPembda) {
            $rules[] = [
                'target_school_id' => $smkPembda->id,
                'academic_year_id' => $academicYear->id,
                'previous_school_type' => 'pembda',
                'previous_school_name' => 'SMPS Pembda 2',
                'previous_school_level' => 'SMP',
                'eligible_ranks' => json_encode(['1', '2', '3']),
                'proof_type' => 'both',
                'exemption_fee_type' => 'registration',
                'exemption_amount' => 50000,
                'exemption_type' => 'full',
                'description' => 'Bebas biaya pendaftaran Rp 50.000 untuk Juara 1, 2, 3 lulusan SMPS Pembda 2 yang masuk SMK Pembda',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Rule 3: Juara 1 dari SMP luar → SMA Swasta Pembda 1
        if ($smaSwastaPembda1) {
            $rules[] = [
                'target_school_id' => $smaSwastaPembda1->id,
                'academic_year_id' => $academicYear->id,
                'previous_school_type' => 'external',
                'previous_school_name' => null,
                'previous_school_level' => 'SMP',
                'eligible_ranks' => json_encode(['1']),
                'proof_type' => 'both',
                'exemption_fee_type' => 'registration',
                'exemption_amount' => 50000,
                'exemption_type' => 'full',
                'description' => 'Bebas biaya pendaftaran Rp 50.000 untuk Juara 1 lulusan SMP luar yang masuk SMA Swasta Pembda 1',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Rule 4: Juara 1 dari SMP luar → SMK Pembda
        if ($smkPembda) {
            $rules[] = [
                'target_school_id' => $smkPembda->id,
                'academic_year_id' => $academicYear->id,
                'previous_school_type' => 'external',
                'previous_school_name' => null,
                'previous_school_level' => 'SMP',
                'eligible_ranks' => json_encode(['1']),
                'proof_type' => 'both',
                'exemption_fee_type' => 'registration',
                'exemption_amount' => 50000,
                'exemption_type' => 'full',
                'description' => 'Bebas biaya pendaftaran Rp 50.000 untuk Juara 1 lulusan SMP luar yang masuk SMK Pembda',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Rule 5: Juara 1 dari SD → SMPS Pembda 2
        if ($smpsPembda2) {
            $rules[] = [
                'target_school_id' => $smpsPembda2->id,
                'academic_year_id' => $academicYear->id,
                'previous_school_type' => 'external',
                'previous_school_name' => null,
                'previous_school_level' => 'SD',
                'eligible_ranks' => json_encode(['1']),
                'proof_type' => 'both',
                'exemption_fee_type' => 'registration',
                'exemption_amount' => 50000,
                'exemption_type' => 'full',
                'description' => 'Bebas biaya pendaftaran Rp 50.000 untuk Juara 1 lulusan SD yang masuk SMPS Pembda 2',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($rules)) {
            AchievementFeeExemptionRule::insert($rules);
            $this->command->info('✅ Achievement Fee Exemption Rules seeded successfully! (' . count($rules) . ' rules)');
        } else {
            $this->command->warn('⚠️ No schools found to seed exemption rules');
        }
    }
}
