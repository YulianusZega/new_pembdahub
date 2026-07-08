<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'user_id' => null,
            'employee_code' => strtoupper($this->faker->unique()->lexify('EMP-?????')),
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'employee_type' => 'guru',
            'employment_status' => 'yayasan',
            'tmt_date' => $this->faker->date(),
            'is_active' => true,
        ];
    }
}
