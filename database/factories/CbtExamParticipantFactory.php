<?php

namespace Database\Factories;

use App\Models\CbtExamParticipant;
use App\Models\CbtExam;
use App\Models\Classroom;
use Illuminate\Database\Eloquent\Factories\Factory;

class CbtExamParticipantFactory extends Factory
{
    protected $model = CbtExamParticipant::class;

    public function definition(): array
    {
        return [
            'exam_id' => CbtExam::factory(),
            'classroom_id' => Classroom::factory(),
        ];
    }
}
