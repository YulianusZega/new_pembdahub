<?php

namespace Database\Factories;

use App\Models\CbtAnswer;
use App\Models\CbtExamSession;
use App\Models\CbtQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtAnswerFactory extends Factory
{
    protected $model = CbtAnswer::class;

    public function definition(): array
    {
        return [
            'session_id' => CbtExamSession::factory(),
            'question_id' => CbtQuestion::factory(),
            'selected_option' => 'A',
            'text_answer' => null,
            'is_correct' => false,
            'score_obtained' => 0,
            'is_flagged' => false,
            'manual_score' => null,
            'teacher_feedback' => null,
            'graded_by' => null,
            'graded_at' => null,
            'time_spent_seconds' => $this->faker->numberBetween(10, 300),
        ];
    }

    public function correct(): static
    {
        return $this->state(['is_correct' => true, 'score_obtained' => 10]);
    }

    public function essay(): static
    {
        return $this->state([
            'selected_option' => null,
            'text_answer' => $this->faker->paragraphs(2, true),
        ]);
    }
}
