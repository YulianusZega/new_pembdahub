<?php

namespace Database\Factories;

use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParentModelFactory extends Factory
{
    protected $model = ParentModel::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'orang_tua']),
            'student_id' => Student::factory(),
            'relation_type' => $this->faker->randomElement(['ayah', 'ibu', 'wali']),
            'full_name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'occupation' => $this->faker->jobTitle(),
            'address' => $this->faker->address(),
        ];
    }
}
