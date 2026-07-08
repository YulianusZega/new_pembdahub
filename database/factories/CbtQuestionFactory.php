<?php

namespace Database\Factories;

use App\Models\CbtQuestion;
use App\Models\CbtQuestionBank;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtQuestionFactory extends Factory
{
    protected $model = CbtQuestion::class;

    public function definition(): array
    {
        return [
            'question_bank_id' => CbtQuestionBank::factory(),
            'question_type' => 'multiple_choice',
            'question_text' => '<p>' . $this->faker->paragraph . '</p>',
            'question_image' => null,
            'explanation' => $this->faker->sentence,
            'points' => $this->faker->randomElement([5, 10, 15, 20]),
            'difficulty' => $this->faker->randomElement(['mudah', 'sedang', 'sulit']),
            'topic' => $this->faker->word,
            'competency' => $this->faker->sentence(3),
            'answer_key' => 'A',
            'max_words' => null,
            'is_active' => true,
        ];
    }

    public function essay(): static
    {
        return $this->state([
            'question_type' => 'essay',
            'answer_key' => $this->faker->paragraph,
            'max_words' => 500,
        ]);
    }

    public function trueFalse(): static
    {
        return $this->state([
            'question_type' => 'true_false',
            'answer_key' => $this->faker->randomElement(['true', 'false']),
        ]);
    }
}
