<?php

namespace Database\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company . ' School',
            'type' => 'SMA',
            'address' => $this->faker->address,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'phone' => $this->faker->phoneNumber,
        ];
    }
}
