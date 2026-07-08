<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\ParentModel;
use App\Policies\StudentPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StudentPolicyTest extends TestCase
{
    use RefreshDatabase;

    private StudentPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new StudentPolicy();
    }

    /** @test */
    public function superadmin_can_view_any_students()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);

        $this->assertTrue($this->policy->viewAny($superadmin));
    }

    /** @test */
    public function admin_can_view_any_students()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);

        $this->assertTrue($this->policy->viewAny($admin));
    }

    /** @test */
    public function guru_can_view_any_students()
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($this->policy->viewAny($guru));
    }

    /** @test */
    public function siswa_cannot_view_any_students()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($this->policy->viewAny($siswa));
    }

    /** @test */
    public function orangtua_cannot_view_any_students()
    {
        $orangtua = User::factory()->create(['role' => 'orang_tua']);

        $this->assertFalse($this->policy->viewAny($orangtua));
    }

    /** @test */
    public function superadmin_can_view_any_student()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $student = Student::factory()->create();

        $this->assertTrue($this->policy->view($superadmin, $student));
    }

    /** @test */
    public function siswa_can_only_view_own_profile()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        $ownStudent = Student::factory()->create(['user_id' => $siswaUser->id]);
        $otherStudent = Student::factory()->create();

        $this->assertTrue($this->policy->view($siswaUser, $ownStudent));
        $this->assertFalse($this->policy->view($siswaUser, $otherStudent));
    }

    /** @test */
    public function orangtua_can_view_their_children()
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

        $this->assertTrue($this->policy->view($orangtuaUser, $child));
        $this->assertFalse($this->policy->view($orangtuaUser, $otherStudent));
    }

    /** @test */
    public function only_superadmin_and_admin_can_create_students()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($this->policy->create($superadmin));
        $this->assertTrue($this->policy->create($admin));
        $this->assertFalse($this->policy->create($guru));
        $this->assertFalse($this->policy->create($siswa));
    }

    /** @test */
    public function superadmin_can_update_any_student()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $student = Student::factory()->create();

        $this->assertTrue($this->policy->update($superadmin, $student));
    }

    /** @test */
    public function siswa_can_update_own_profile()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        $ownStudent = Student::factory()->create(['user_id' => $siswaUser->id]);
        $otherStudent = Student::factory()->create();

        $this->assertTrue($this->policy->update($siswaUser, $ownStudent));
        $this->assertFalse($this->policy->update($siswaUser, $otherStudent));
    }

    /** @test */
    public function orangtua_cannot_update_children_profile()
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

        $this->assertFalse($this->policy->update($orangtuaUser, $child));
        $this->assertFalse($this->policy->update($orangtuaUser, $otherStudent));
    }

    /** @test */
    public function only_superadmin_can_delete_students()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $student = Student::factory()->create();

        $this->assertTrue($this->policy->delete($superadmin, $student));
        $this->assertFalse($this->policy->delete($admin, $student));
    }

    /** @test */
    public function only_superadmin_can_restore_students()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $student = Student::factory()->create();

        $this->assertTrue($this->policy->restore($superadmin, $student));
        $this->assertFalse($this->policy->restore($admin, $student));
    }

    /** @test */
    public function only_superadmin_can_force_delete_students()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $student = Student::factory()->create();

        $this->assertTrue($this->policy->forceDelete($superadmin, $student));
        $this->assertFalse($this->policy->forceDelete($admin, $student));
    }

    /** @test */
    public function only_superadmin_and_admin_can_import_students()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);

        $this->assertTrue($this->policy->import($superadmin));
        $this->assertTrue($this->policy->import($admin));
        $this->assertFalse($this->policy->import($guru));
    }

    /** @test */
    public function admin_and_guru_can_export_students()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($this->policy->export($admin));
        $this->assertTrue($this->policy->export($guru));
        $this->assertFalse($this->policy->export($siswa));
    }
}
