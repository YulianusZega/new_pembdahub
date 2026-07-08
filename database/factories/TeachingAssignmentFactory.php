<?php

namespace Database\Factories;

use App\Models\TeachingAssignment;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeachingAssignmentFactory extends Factory
{
    protected $model = TeachingAssignment::class;

    public function definition(): array
    {
        return [
            'teacher_id' => Teacher::factory(),
            'subject_id' => Subject::factory(),
            'classroom_id' => Classroom::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'hours_per_week' => $this->faker->numberBetween(2, 8),
            'is_main_teacher' => $this->faker->boolean(30),
            'is_active' => true,
        ];
    }
}
