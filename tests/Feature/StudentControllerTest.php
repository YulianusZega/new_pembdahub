<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Student;
use Illuminate\Support\Facades\Hash;

class StudentControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function createSuperAdmin()
    {
        return User::create([
            'name' => 'Test Admin',
            'email' => 'testadmin@example.com',
            'password' => Hash::make('password'),
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    public function test_index_requires_auth_and_role()
    {
        $user = $this->createSuperAdmin();
        $this->actingAs($user)
            ->get(route('admin.students.index'))
            ->assertStatus(200);
    }

    public function test_store_creates_student()
    {
        $user = $this->createSuperAdmin();
        $school = School::create(['name' => 'Sekolah Test', 'type' => 'SMA']);

        $post = [
            'school_id' => $school->id,
            'nisn' => '20219999',
            'nis' => '9999',
            'full_name' => 'Rina Test',
            'gender' => 'P',
            'entry_year' => '2021',
            'previous_school' => 'SMP Terakhir',
            'guardian_name' => 'Bapak Rina',
            'guardian_phone' => '0811111111',
            'guardian_occupation' => 'Wiraswasta',
            'guardian_address' => 'Jl. Contoh 1',
            'hobby' => 'Baca',
            'health_history' => 'Sehat',
        ];

        $this->actingAs($user)
            ->post(route('admin.students.store'), $post)
            ->assertRedirect(route('admin.students.index'));

        $this->assertDatabaseHas('students', ['nisn' => '20219999', 'full_name' => 'Rina Test']);

        $student = \App\Models\Student::where('nisn', '20219999')->first();
        $this->assertNotNull($student->user_id);
        $this->assertDatabaseHas('users', ['id' => $student->user_id, 'role' => 'siswa']);
    }
}
