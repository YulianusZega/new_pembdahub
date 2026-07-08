<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TeacherControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::factory()->create();
        $this->admin = User::factory()->create([
            'role' => 'superadmin',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);
    }

    /** @test */
    public function admin_can_view_teachers_index()
    {
        $response = $this->actingAs($this->admin)
            ->get(route('admin.teachers.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_create_teacher_without_account()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.teachers.store'), [
                'school_id' => $this->school->id,
                'teacher_code' => 'GR-001',
                'full_name' => 'Pak Budi',
                'gender' => 'L',
                'employment_status' => 'yayasan',
                'tmt_date' => '2024-01-01',
                'marital_status' => 'belum_menikah',
            ]);

        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('teachers', ['full_name' => 'Pak Budi']);
        $this->assertDatabaseHas('employees', ['employee_code' => 'GR-001']);
    }

    /** @test */
    public function admin_can_create_teacher_with_account()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.teachers.store'), [
                'school_id' => $this->school->id,
                'teacher_code' => 'GR-002',
                'full_name' => 'Bu Ani',
                'gender' => 'P',
                'employment_status' => 'pns',
                'tmt_date' => '2024-01-01',
                'marital_status' => 'belum_menikah',
                'create_account' => true,
                'email' => 'ani@test.com',
                'password' => 'Password1A',
            ]);

        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseHas('users', [
            'email' => 'ani@test.com',
            'role' => 'guru',
        ]);
    }

    /** @test */
    public function teacher_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.teachers.store'), []);

        $response->assertSessionHasErrors(['school_id', 'teacher_code', 'full_name', 'gender', 'employment_status', 'tmt_date', 'marital_status']);
    }

    /** @test */
    public function teacher_code_must_be_unique()
    {
        Employee::factory()->create([
            'employee_code' => 'GR-DUP',
            'school_id' => $this->school->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->post(route('admin.teachers.store'), [
                'school_id' => $this->school->id,
                'teacher_code' => 'GR-DUP',
                'full_name' => 'Test',
                'gender' => 'L',
                'employment_status' => 'yayasan',
                'tmt_date' => '2024-01-01',
                'marital_status' => 'belum_menikah',
            ]);

        $response->assertSessionHasErrors('teacher_code');
    }

    /** @test */
    public function admin_can_delete_teacher_and_user()
    {
        $user = User::factory()->create(['role' => 'guru']);
        $employee = Employee::factory()->create([
            'school_id' => $this->school->id,
            'user_id' => $user->id,
        ]);
        $teacher = Teacher::factory()->create([
            'school_id' => $this->school->id,
            'employee_id' => $employee->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.teachers.destroy', $teacher));

        $response->assertRedirect(route('admin.teachers.index'));
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
