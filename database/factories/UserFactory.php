<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'username' => fake()->unique()->userName(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'guru',
            'school_id' => null,
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function superadmin(): static
    {
        return $this->state(fn () => ['role' => 'superadmin']);
    }

    public function adminSekolah(): static
    {
        return $this->state(fn () => ['role' => 'admin_sekolah']);
    }

    public function guru(): static
    {
        return $this->state(fn () => ['role' => 'guru']);
    }

    public function siswa(): static
    {
        return $this->state(fn () => ['role' => 'siswa']);
    }

    public function orangTua(): static
    {
        return $this->state(fn () => ['role' => 'orang_tua']);
    }

    public function bendahara(): static
    {
        return $this->state(fn () => ['role' => 'bendahara']);
    }
}
