<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'nisn' => $this->faker->unique()->numerify('00########'),
            'nis' => $this->faker->unique()->numerify('20######'),
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->date('Y-m-d', '2010-12-31'),
            'religion' => 'Kristen',
            'address' => $this->faker->address(),
            'phone' => $this->faker->phoneNumber(),
            'entry_year' => 2024,
            'status' => 'aktif',
        ];
    }
}
