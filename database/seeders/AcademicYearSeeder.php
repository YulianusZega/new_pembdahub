<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use Illuminate\Support\Facades\DB;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing academic years (respects foreign keys)
        AcademicYear::query()->delete();
        
        // Insert default academic years
        $academicYears = [
            [
                'year' => '2024/2025',
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'semester_start' => '2024-07-01',
                'semester_end' => '2024-12-31',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => '2025/2026',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'semester_start' => '2025-07-01',
                'semester_end' => '2025-12-31',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'year' => '2026/2027',
                'start_date' => '2026-07-01',
                'end_date' => '2027-06-30',
                'semester_start' => '2026-07-01',
                'semester_end' => '2026-12-31',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($academicYears as $year) {
            AcademicYear::create($year);
        }
    }
}
