<?php

namespace Database\Factories;

use App\Models\CbtQuestionOption;
use App\Models\CbtQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtQuestionOptionFactory extends Factory
{
    protected $model = CbtQuestionOption::class;

    public function definition(): array
    {
        return [
            'question_id' => CbtQuestion::factory(),
            'option_label' => $this->faker->randomElement(['A', 'B', 'C', 'D']),
            'option_text' => $this->faker->sentence,
            'option_image' => null,
            'is_correct' => false,
            'sort_order' => 1,
        ];
    }

    public function correct(): static
    {
        return $this->state(['is_correct' => true]);
    }
}
