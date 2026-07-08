<?php

namespace Database\Factories;

use App\Models\StudentClass;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentClassFactory extends Factory
{
    protected $model = StudentClass::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'classroom_id' => Classroom::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'status' => 'aktif',
            'entry_date' => now()->format('Y-m-d'),
        ];
    }
}
