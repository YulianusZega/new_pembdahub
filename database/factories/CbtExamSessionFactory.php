<?php

namespace Database\Factories;

use App\Models\CbtExamSession;
use App\Models\CbtExam;
use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtExamSessionFactory extends Factory
{
    protected $model = CbtExamSession::class;

    public function definition(): array
    {
        return [
            'exam_id' => CbtExam::factory(),
            'student_id' => Student::factory(),
            'classroom_id' => Classroom::factory(),
            'attempt_number' => 1,
            'started_at' => now(),
            'finished_at' => null,
            'deadline_at' => now()->addMinutes(60),
            'status' => 'in_progress',
            'question_order' => [],
            'option_orders' => [],
            'tab_switch_count' => 0,
            'ip_address' => $this->faker->ipv4,
            'user_agent' => $this->faker->userAgent,
        ];
    }

    public function submitted(): static
    {
        return $this->state([
            'status' => 'submitted',
            'finished_at' => now(),
        ]);
    }
}
