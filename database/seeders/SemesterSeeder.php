<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all academic years
        $academicYears = DB::table('academic_years')->get();

        foreach ($academicYears as $academicYear) {
            // Semester 1 (Ganjil - Odd/July-December)
            DB::table('semesters')->insert([
                'academic_year_id' => $academicYear->id,
                'semester_number' => 1,
                'semester_name' => 'Ganjil',
                'start_date' => $academicYear->semester_start,
                'end_date' => $academicYear->semester_end,
                'is_active' => true,
            ]);

            // Semester 2 (Genap - Even/January-June)
            DB::table('semesters')->insert([
                'academic_year_id' => $academicYear->id,
                'semester_number' => 2,
                'semester_name' => 'Genap',
                'start_date' => $academicYear->start_date < $academicYear->end_date ?
                    $academicYear->start_date : now()->addMonths(6)->toDateString(),
                'end_date' => $academicYear->end_date,
                'is_active' => false,
            ]);
        }

        $this->command->info('Semesters seeded successfully!');
    }
}
