<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'academic_year_id' => function () {
                return AcademicYear::first()?->id ?? AcademicYear::factory()->create()->id;
            },
            'class_code' => strtoupper($this->faker->unique()->lexify('??-?')),
            'class_name' => 'Kelas ' . $this->faker->unique()->numerify('##-?'),
            'grade_level' => $this->faker->randomElement([7, 8, 9, 10, 11, 12]),
            'capacity' => 30,
            'is_active' => true,
        ];
    }
}
