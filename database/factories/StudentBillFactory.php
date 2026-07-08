<?php

namespace Database\Factories;

use App\Models\StudentBill;
use App\Models\Student;
use App\Models\PaymentType;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class StudentBillFactory extends Factory
{
    protected $model = StudentBill::class;

    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'payment_type_id' => PaymentType::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'month' => $this->faker->numberBetween(1, 12),
            'year' => now()->year,
            'amount' => $this->faker->randomElement([250000, 500000, 750000]),
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'notes' => null,
        ];
    }

    public function lunas(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'paid_amount' => $attributes['amount'],
                'status' => 'lunas',
            ];
        });
    }

    public function cicilan(): static
    {
        return $this->state(function (array $attributes) {
            $partial = intval($attributes['amount'] * 0.5);
            return [
                'paid_amount' => $partial,
                'status' => 'cicilan',
            ];
        });
    }
}
