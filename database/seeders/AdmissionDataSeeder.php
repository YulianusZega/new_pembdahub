<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class AdmissionDataSeeder extends Seeder
{
    public function run(): void
    {
        $academicYear = AcademicYear::where('is_active', true)->first();
        $schools = School::all();

        foreach ($schools as $school) {
            $this->seedAdmissionFees($school, $academicYear);
            $this->seedAdmissionTests($school, $academicYear);
            $this->seedAdmissionDiscounts($school, $academicYear);
        }

        $this->command->info('✅ Admission data seeded successfully!');
    }

    private function seedAdmissionFees($school, $academicYear)
    {
        $fees = [];

        // BIAYA PENDAFTARAN - Rp 50,000 untuk semua
        $fees[] = [
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'fee_type' => 'registration',
            'fee_name' => 'Biaya Pendaftaran',
            'amount' => 50000,
            'description' => 'Biaya administrasi pendaftaran siswa baru',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // UANG ALAT - Hanya untuk SMK (Rp 250,000)
        if (stripos($school->name, 'SMK') !== false) {
            $fees[] = [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'fee_type' => 'equipment',
                'fee_name' => 'Uang Alat Praktikum',
                'amount' => 250000,
                'description' => 'Biaya peralatan praktikum dan bengkel untuk siswa SMK',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // UANG PANGKAL (Daftar Ulang)
        if (stripos($school->name, 'SMP') !== false) {
            $amount = 500000;
        } elseif (stripos($school->name, 'SMA') !== false) {
            $amount = 750000;
        } else { // SMK
            $amount = 1000000;
        }

        $fees[] = [
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'fee_type' => 'reregistration',
            'fee_name' => 'Uang Pangkal / Daftar Ulang',
            'amount' => $amount,
            'description' => 'Biaya daftar ulang untuk siswa yang diterima',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('admission_fees')->insert($fees);
    }

    private function seedAdmissionTests($school, $academicYear)
    {
        $tests = [];

        if (stripos($school->name, 'SMP') !== false) {
            // TES SMP
            $tests = [
                ['test_name' => 'Matematika', 'max_score' => 100, 'weight' => 1.00, 'order' => 1],
                ['test_name' => 'IPA (Sains)', 'max_score' => 100, 'weight' => 1.00, 'order' => 2],
                ['test_name' => 'Bahasa Indonesia', 'max_score' => 100, 'weight' => 1.00, 'order' => 3],
            ];
        } elseif (stripos($school->name, 'SMA') !== false) {
            // TES SMA
            $tests = [
                ['test_name' => 'Matematika', 'max_score' => 100, 'weight' => 1.20, 'order' => 1],
                ['test_name' => 'IPA (Sains)', 'max_score' => 100, 'weight' => 1.00, 'order' => 2],
                ['test_name' => 'IPS (Sosial)', 'max_score' => 100, 'weight' => 1.00, 'order' => 3],
                ['test_name' => 'Bahasa Inggris', 'max_score' => 100, 'weight' => 1.00, 'order' => 4],
            ];
        } else { // SMK
            // TES SMK
            $tests = [
                ['test_name' => 'Matematika', 'max_score' => 100, 'weight' => 1.00, 'order' => 1],
                ['test_name' => 'Bahasa Indonesia', 'max_score' => 100, 'weight' => 1.00, 'order' => 2],
                ['test_name' => 'Tes Minat & Bakat', 'max_score' => 100, 'weight' => 1.20, 'order' => 3],
            ];
        }

        foreach ($tests as $test) {
            DB::table('admission_tests')->insert([
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'test_name' => $test['test_name'],
                'max_score' => $test['max_score'],
                'weight' => $test['weight'],
                'order' => $test['order'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedAdmissionDiscounts($school, $academicYear)
    {
        $discounts = [];

        // DISKON BEST 3 - Hanya untuk SMP
        if (stripos($school->name, 'SMP') !== false) {
            $discounts[] = [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'discount_name' => 'Best 3 - Rank 1',
                'discount_type' => 'percentage',
                'discount_value' => 50, // 50%
                'applies_to' => 'spp',
                'duration_months' => 12,
                'criteria' => json_encode(['min_rank' => 1, 'max_rank' => 1]),
                'description' => 'Diskon 50% SPP untuk ranking 1 selama 1 tahun',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $discounts[] = [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'discount_name' => 'Best 3 - Rank 2',
                'discount_type' => 'percentage',
                'discount_value' => 30, // 30%
                'applies_to' => 'spp',
                'duration_months' => 12,
                'criteria' => json_encode(['min_rank' => 2, 'max_rank' => 2]),
                'description' => 'Diskon 30% SPP untuk ranking 2 selama 1 tahun',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $discounts[] = [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'discount_name' => 'Best 3 - Rank 3',
                'discount_type' => 'percentage',
                'discount_value' => 20, // 20%
                'applies_to' => 'spp',
                'duration_months' => 12,
                'criteria' => json_encode(['min_rank' => 3, 'max_rank' => 3]),
                'description' => 'Diskon 20% SPP untuk ranking 3 selama 1 tahun',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // DISKON UMUM (Semua Sekolah)
        $discounts[] = [
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'discount_name' => 'Diskon Sibling (Adik Kakak)',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'applies_to' => 'spp',
            'duration_months' => 12,
            'criteria' => json_encode(['type' => 'sibling']),
            'description' => 'Diskon 10% SPP untuk adik/kakak yang bersekolah di sekolah yang sama',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $discounts[] = [
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'discount_name' => 'Beasiswa Tidak Mampu',
            'discount_type' => 'percentage',
            'discount_value' => 100,
            'applies_to' => 'all',
            'duration_months' => 12,
            'criteria' => json_encode(['type' => 'scholarship', 'requires_approval' => true]),
            'description' => 'Beasiswa penuh untuk siswa dari keluarga tidak mampu (perlu approval)',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('admission_discounts')->insert($discounts);
    }
}
