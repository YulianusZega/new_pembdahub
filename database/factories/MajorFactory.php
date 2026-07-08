<?php

namespace Database\Factories;

use App\Models\Major;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class MajorFactory extends Factory
{
    protected $model = Major::class;

    public function definition()
    {
        return [
            'school_id' => School::factory(),
            'major_code' => strtoupper($this->faker->lexify('???')),
            'major_name' => $this->faker->words(2, true),
            'description' => $this->faker->sentence(),
            'is_active' => $this->faker->boolean(70),
        ];
    }
}
