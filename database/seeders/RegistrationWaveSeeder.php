<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\RegistrationWave;

class RegistrationWaveSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get active academic year (2026)
        $academicYear = AcademicYear::where('year', '2026/2027')->first();
        
        if (!$academicYear) {
            $this->command->error('Academic year 2026/2027 not found!');
            return;
        }

        // Update schools with test requirements based on type
        School::where('type', 'SMP')->update([
            'requires_test' => false,
            'test_type' => null,
        ]);

        School::where('type', 'SMK')->update([
            'requires_test' => false,
            'test_type' => null,
        ]);

        School::where('type', 'SMA')->update([
            'requires_test' => true,
            'test_type' => 'wawancara',
        ]);

        $this->command->info('✅ Schools test requirements updated');

        // Create registration waves for each school
        $schools = School::where('is_active', true)->get();

        foreach ($schools as $school) {
            if ($school->type === 'SMK') {
                // SMK has 2 waves
                RegistrationWave::create([
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => 'Gelombang 1',
                    'wave_number' => 1,
                    'start_date' => '2026-03-01',
                    'end_date' => '2026-04-30',
                    'quota' => null, // Unlimited
                    'is_active' => true,
                    'description' => 'Gelombang 1 Penerimaan Siswa Baru ' . $school->name,
                ]);

                RegistrationWave::create([
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => 'Gelombang 2',
                    'wave_number' => 2,
                    'start_date' => '2026-05-01',
                    'end_date' => '2026-06-30',
                    'quota' => null,
                    'is_active' => true,
                    'description' => 'Gelombang 2 Penerimaan Siswa Baru ' . $school->name,
                ]);

                $this->command->info("✅ Created 2 waves for {$school->name} (SMK)");
            } else {
                // SMP and SMA - Single period
                $startDate = $school->type === 'SMA' ? '2026-03-02' : '2026-03-01';
                $endDate = $school->type === 'SMA' ? '2026-07-11' : '2026-06-30';

                RegistrationWave::create([
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'name' => 'Penerimaan ' . $academicYear->year,
                    'wave_number' => 1,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'quota' => null,
                    'is_active' => true,
                    'description' => 'Penerimaan Siswa Baru ' . $school->name . ' TA ' . $academicYear->year,
                ]);

                $this->command->info("✅ Created registration period for {$school->name} ({$school->type})");
            }
        }

        $this->command->info('🎉 Registration waves seeding completed!');
    }
}
