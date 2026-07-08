<?php

namespace Database\Factories;

use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'bill_id' => StudentBill::factory(),
            'student_id' => Student::factory(),
            'amount_paid' => $this->faker->randomElement([250000, 500000, 750000]),
            'payment_method' => $this->faker->randomElement(['cash', 'transfer', 'qris']),
            'reference_number' => $this->faker->optional()->numerify('REF-########'),
            'payment_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'receipt_number' => 'KWT-' . now()->format('Ymd') . '-' . $this->faker->unique()->numerify('####'),
            'notes' => $this->faker->optional()->sentence(),
            'is_verified' => true,
            'verified_by' => User::factory(),
            'verified_at' => now(),
            'processed_by' => User::factory(),
        ];
    }

    public function unverified(): static
    {
        return $this->state([
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
        ]);
    }
}
