<?php

namespace Database\Factories;

use App\Models\CbtExam;
use App\Models\School;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtExamFactory extends Factory
{
    protected $model = CbtExam::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'exam_title' => $this->faker->sentence(4),
            'exam_description' => $this->faker->paragraph,
            'exam_type' => $this->faker->randomElement(['tugas', 'quiz', 'uts', 'uas']),
            'status' => 'draft',
            'start_time' => now(),
            'end_time' => now()->addHours(2),
            'duration_minutes' => 60,
            'total_questions_shown' => 10,
            'randomize_questions' => true,
            'randomize_options' => true,
            'show_result' => true,
            'show_answer_key' => false,
            'allow_review' => true,
            'passing_score' => 70,
            'max_attempts' => 1,
            'access_code' => null,
            'prevent_tab_switch' => true,
            'prevent_copy_paste' => true,
            'auto_sync_grade' => false,
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function published(): static
    {
        return $this->state(['status' => 'published']);
    }

    public function completed(): static
    {
        return $this->state(['status' => 'completed']);
    }
}
