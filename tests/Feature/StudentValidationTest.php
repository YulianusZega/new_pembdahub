<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Facades\Hash;

class StudentValidationTest extends TestCase
{
    use RefreshDatabase;

    protected function createSuperAdmin()
    {
        return User::create([
            'name' => 'Test Admin',
            'email' => 'testadmin2@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    public function test_birth_date_must_be_before_today()
    {
        $user = $this->createSuperAdmin();
        $school = School::create(['name' => 'Sekolah Test', 'type' => 'SMA']);

        $tomorrow = now()->addDay()->format('Y-m-d');

        $post = [
            'school_id' => $school->id,
            'nisn' => '20230001',
            'full_name' => 'Future Birth',
            'gender' => 'L',
            'entry_year' => '2023',
            'birth_date' => $tomorrow,
        ];

        $this->actingAs($user)
            ->post(route('admin.students.store'), $post)
            ->assertSessionHasErrors(['birth_date']);
    }

    public function test_religion_field_is_accepted_optional()
    {
        $user = $this->createSuperAdmin();
        $school = School::create(['name' => 'Sekolah Test', 'type' => 'SMA']);

        $post = [
            'school_id' => $school->id,
            'nisn' => '20230002',
            'full_name' => 'Religious Student',
            'gender' => 'P',
            'entry_year' => '2023',
            'religion' => 'Kristen',
        ];

        $this->actingAs($user)
            ->post(route('admin.students.store'), $post)
            ->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('students', ['nisn' => '20230002', 'religion' => 'Kristen']);
    }
}
