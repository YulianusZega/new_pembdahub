<?php

namespace Database\Factories;

use App\Models\CbtExamResult;
use App\Models\CbtExam;
use App\Models\CbtExamSession;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtExamResultFactory extends Factory
{
    protected $model = CbtExamResult::class;

    public function definition(): array
    {
        $correct = $this->faker->numberBetween(3, 10);
        $wrong = $this->faker->numberBetween(0, 5);
        $unanswered = $this->faker->numberBetween(0, 2);
        $total = $correct + $wrong + $unanswered;
        $score = ($total > 0) ? round(($correct / $total) * 100, 2) : 0;

        return [
            'exam_id' => CbtExam::factory(),
            'session_id' => CbtExamSession::factory(),
            'student_id' => Student::factory(),
            'total_questions' => $total,
            'answered_questions' => $correct + $wrong,
            'correct_answers' => $correct,
            'wrong_answers' => $wrong,
            'unanswered' => $unanswered,
            'total_score' => $correct * 10,
            'max_score' => $total * 10,
            'percentage_score' => $score,
            'final_score' => $score,
            'is_passed' => $score >= 70,
            'predicate' => CbtExamResult::calculatePredicate($score),
            'rank' => null,
            'time_spent_seconds' => $this->faker->numberBetween(600, 3600),
            'grade_synced' => false,
            'synced_grade_id' => null,
        ];
    }

    public function passed(): static
    {
        return $this->state(fn() => [
            'correct_answers' => 8, 'wrong_answers' => 2, 'unanswered' => 0,
            'total_questions' => 10, 'answered_questions' => 10,
            'final_score' => 80, 'percentage_score' => 80,
            'is_passed' => true, 'predicate' => 'B',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn() => [
            'correct_answers' => 3, 'wrong_answers' => 7, 'unanswered' => 0,
            'total_questions' => 10, 'answered_questions' => 10,
            'final_score' => 30, 'percentage_score' => 30,
            'is_passed' => false, 'predicate' => 'D',
        ]);
    }
}
