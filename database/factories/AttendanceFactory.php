<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    protected $model = Attendance::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'classroom_id' => Classroom::factory(),
            'schedule_id' => null,
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
            'status' => $this->faker->randomElement(['hadir', 'sakit', 'izin', 'alfa']),
            'notes' => $this->faker->optional()->sentence(),
            'created_by' => User::factory(),
        ];
    }

    public function hadir(): static
    {
        return $this->state(['status' => 'hadir']);
    }

    public function alfa(): static
    {
        return $this->state(['status' => 'alfa']);
    }
}
