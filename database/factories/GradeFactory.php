<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    protected $model = Grade::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'subject_id' => Subject::factory(),
            'teacher_id' => Teacher::factory(),
            'semester_id' => function () {
                return Semester::first()?->id ?? Semester::factory()->create()->id;
            },
            'grade_type' => $this->faker->randomElement(['tugas', 'uts', 'uas', 'sikap']),
            'score' => $this->faker->numberBetween(60, 100),
            'is_remedial' => false,
            'created_by' => null,
            'notes' => null,
        ];
    }
}
