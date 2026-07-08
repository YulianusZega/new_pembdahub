<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class SemesterFactory extends Factory
{
    protected $model = Semester::class;

    public function definition(): array
    {
        return [
            'academic_year_id' => function () {
                return AcademicYear::first()?->id ?? AcademicYear::create([
                    'year' => '2024/2025',
                    'start_date' => '2024-07-01',
                    'end_date' => '2025-06-30',
                    'is_active' => true,
                ])->id;
            },
            'semester_number' => 1,
            'semester_name' => 'Ganjil',
            'start_date' => '2024-07-01',
            'end_date' => '2024-12-15',
            'is_active' => true,
        ];
    }
}
