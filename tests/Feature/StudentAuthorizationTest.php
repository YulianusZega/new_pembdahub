<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\School;
use App\Models\ParentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function superadmin_can_access_students_index()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($superadmin)
            ->get(route('admin.students.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function admin_can_access_students_index()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);

        $response = $this->actingAs($admin)
            ->get(route('admin.students.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function guru_can_access_students_index()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $response = $this->actingAs($guru)
            ->get(route('admin.students.index'));

        $response->assertStatus(200);
    }

    /** @test */
    public function siswa_cannot_access_students_index()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $response = $this->actingAs($siswa)
            ->get(route('admin.students.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_create_student()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $school = School::factory()->create();

        $response = $this->actingAs($superadmin)
            ->post(route('admin.students.store'), [
                'nisn' => '0012345678',
                'nis' => '12345',
                'full_name' => 'Test Student',
                'school_id' => $school->id,
                'gender' => 'L',
                'entry_year' => '2024',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('students', ['nisn' => '0012345678']);
    }

    /** @test */
    public function guru_cannot_create_student()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $school = School::factory()->create();

        $response = $this->actingAs($guru)
            ->post(route('admin.students.store'), [
                'nisn' => '0012345679',
                'nis' => '12345',
                'full_name' => 'Test Student',
                'school_id' => $school->id,
                'gender' => 'L',
                'entry_year' => '2024',
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function siswa_can_view_own_profile()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        $student = Student::factory()->create(['user_id' => $siswaUser->id]);

        $response = $this->actingAs($siswaUser)
            ->get(route('admin.students.show', $student));

        $response->assertStatus(200);
    }

    /** @test */
    public function siswa_cannot_view_other_student_profile()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        Student::factory()->create(['user_id' => $siswaUser->id]);
        $otherStudent = Student::factory()->create();

        $response = $this->actingAs($siswaUser)
            ->get(route('admin.students.show', $otherStudent));

        $response->assertStatus(403);
    }

    /** @test */
    public function siswa_can_update_own_profile()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        $student = Student::factory()->create(['user_id' => $siswaUser->id]);

        $response = $this->actingAs($siswaUser)
            ->put(route('admin.students.update', $student), [
                'nisn' => $student->nisn,
                'nis' => $student->nis,
                'full_name' => 'Updated Name',
                'school_id' => $student->school_id,
                'gender' => $student->gender,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('students', ['full_name' => 'Updated Name']);
    }

    /** @test */
    public function siswa_cannot_update_other_student()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        Student::factory()->create(['user_id' => $siswaUser->id]);
        $otherStudent = Student::factory()->create();

        $response = $this->actingAs($siswaUser)
            ->put(route('admin.students.update', $otherStudent), [
                'school_id' => $otherStudent->school_id,
                'nisn' => $otherStudent->nisn,
                'full_name' => 'Hacked Name',
                'gender' => $otherStudent->gender,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function only_superadmin_can_delete_students()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $student = Student::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('admin.students.destroy', $student));

        $response->assertStatus(403);
    }

    /** @test */
    public function superadmin_can_delete_student()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $student = Student::factory()->create();

        $response = $this->actingAs($superadmin)
            ->delete(route('admin.students.destroy', $student));

        $response->assertRedirect();
        $this->assertDatabaseMissing('students', ['id' => $student->id]);
    }

    /** @test */
    public function orangtua_can_view_children_profile()
    {
        $orangtuaUser = User::factory()->create(['role' => 'orang_tua']);
        $child = Student::factory()->create();
        ParentModel::create([
            'user_id' => $orangtuaUser->id,
            'student_id' => $child->id,
            'relation_type' => 'ayah',
            'full_name' => $orangtuaUser->name,
        ]);

        $response = $this->actingAs($orangtuaUser)
            ->get(route('admin.students.show', $child));

        $response->assertStatus(200);
    }

    /** @test */
    public function orangtua_cannot_view_other_students()
    {
        $orangtuaUser = User::factory()->create(['role' => 'orang_tua']);
        $child = Student::factory()->create();
        ParentModel::create([
            'user_id' => $orangtuaUser->id,
            'student_id' => $child->id,
            'relation_type' => 'ayah',
            'full_name' => $orangtuaUser->name,
        ]);
        $otherStudent = Student::factory()->create();

        $response = $this->actingAs($orangtuaUser)
            ->get(route('admin.students.show', $otherStudent));

        $response->assertStatus(403);
    }
}
