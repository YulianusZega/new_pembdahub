<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectFactory extends Factory
{
    protected $model = Subject::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'subject_code' => strtoupper($this->faker->unique()->lexify('???')),
            'subject_name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'kkm' => 75,
            'is_active' => true,
        ];
    }
}
