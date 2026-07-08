<?php

namespace Database\Factories;

use App\Models\PaymentType;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentTypeFactory extends Factory
{
    protected $model = PaymentType::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'type_code' => strtoupper($this->faker->unique()->lexify('PAY-???')),
            'type_name' => $this->faker->randomElement(['SPP', 'Seragam', 'Buku', 'Ujian', 'Kegiatan']),
            'description' => $this->faker->sentence(),
            'amount' => $this->faker->randomElement([250000, 500000, 750000, 1000000]),
            'is_recurring' => $this->faker->boolean(60),
            'allow_installment' => $this->faker->boolean(40),
            'is_active' => true,
        ];
    }
}
