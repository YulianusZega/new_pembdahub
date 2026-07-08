<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $year = $this->faker->unique()->numberBetween(2020, 2030);

        return [
            'year' => $year . '/' . ($year + 1),
            'start_date' => $year . '-07-01',
            'end_date' => ($year + 1) . '-06-30',
            'semester_start' => $year . '-07-01',
            'semester_end' => $year . '-12-31',
            'is_active' => true,
        ];
    }
}
