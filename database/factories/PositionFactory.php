<?php

namespace Database\Factories;

use App\Models\Position;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'position_name' => $this->faker->randomElement([
                'Kepala Sekolah', 'Wakil Kepala Sekolah', 'Guru Tetap',
                'Guru Honorer', 'Tata Usaha', 'Bendahara', 'Staf',
            ]),
            'position_code' => strtoupper($this->faker->unique()->lexify('POS-???')),
            'position_category' => $this->faker->randomElement(['struktural', 'fungsional', 'staf']),
            'position_level' => $this->faker->numberBetween(1, 5),
            'is_structural' => $this->faker->boolean(40),
            'allowance_amount' => $this->faker->randomElement([0, 250000, 500000, 1000000]),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
