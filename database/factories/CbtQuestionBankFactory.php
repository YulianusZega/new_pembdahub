<?php

namespace Database\Factories;

use App\Models\CbtQuestionBank;
use App\Models\School;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtQuestionBankFactory extends Factory
{
    protected $model = CbtQuestionBank::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'bank_name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'grade_level' => $this->faker->randomElement(['7', '8', '9', '10', '11', '12']),
            'total_questions' => 0,
            'is_active' => true,
            'is_shared' => false,
        ];
    }
}
