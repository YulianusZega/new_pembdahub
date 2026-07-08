<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'user_id' => User::factory()->state(['role' => 'guru']),
            'school_id' => School::factory(),
            'teacher_code' => strtoupper($this->faker->unique()->lexify('GR-???')),
            'full_name' => $this->faker->name(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'is_active' => true,
        ];
    }
}
