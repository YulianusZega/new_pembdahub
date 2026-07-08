<?php

namespace Tests\Unit\Policies;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ParentModel;
use App\Policies\GradePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradePolicyTest extends TestCase
{
    use RefreshDatabase;

    private GradePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->policy = new GradePolicy();
    }

    /** @test */
    public function all_authenticated_users_can_view_any_grades()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $orangtua = User::factory()->create(['role' => 'orang_tua']);

        $this->assertTrue($this->policy->viewAny($superadmin));
        $this->assertTrue($this->policy->viewAny($guru));
        $this->assertTrue($this->policy->viewAny($siswa));
        $this->assertTrue($this->policy->viewAny($orangtua));
    }

    /** @test */
    public function admin_can_view_any_grade()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $grade = Grade::factory()->create();

        $this->assertTrue($this->policy->view($admin, $grade));
    }

    /** @test */
    public function guru_can_view_grades_they_created()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $teacher = Teacher::factory()->create(['user_id' => $guru->id]);
        $ownGrade = Grade::factory()->create(['teacher_id' => $teacher->id]);
        $otherGrade = Grade::factory()->create();

        $this->assertTrue($this->policy->view($guru, $ownGrade));
        $this->assertFalse($this->policy->view($guru, $otherGrade));
    }

    /** @test */
    public function siswa_can_view_own_grades()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        $student = Student::factory()->create(['user_id' => $siswaUser->id]);
        $ownGrade = Grade::factory()->create(['student_id' => $student->id]);
        $otherGrade = Grade::factory()->create();

        $this->assertTrue($this->policy->view($siswaUser, $ownGrade));
        $this->assertFalse($this->policy->view($siswaUser, $otherGrade));
    }

    /** @test */
    public function orangtua_can_view_children_grades()
    {
        $orangtuaUser = User::factory()->create(['role' => 'orang_tua']);
        $child = Student::factory()->create();
        ParentModel::create([
            'user_id' => $orangtuaUser->id,
            'student_id' => $child->id,
            'relation_type' => 'ayah',
            'full_name' => $orangtuaUser->name,
        ]);
        $childGrade = Grade::factory()->create(['student_id' => $child->id]);
        $otherGrade = Grade::factory()->create();

        $this->assertTrue($this->policy->view($orangtuaUser, $childGrade));
        $this->assertFalse($this->policy->view($orangtuaUser, $otherGrade));
    }

    /** @test */
    public function only_admin_and_guru_can_create_grades()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($this->policy->create($admin));
        $this->assertTrue($this->policy->create($guru));
        $this->assertFalse($this->policy->create($siswa));
    }

    /** @test */
    public function admin_can_update_any_grade()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $grade = Grade::factory()->create();

        $this->assertTrue($this->policy->update($admin, $grade));
    }

    /** @test */
    public function guru_can_only_update_grades_they_created()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $teacher = Teacher::factory()->create(['user_id' => $guru->id]);
        $ownGrade = Grade::factory()->create(['teacher_id' => $teacher->id]);
        $otherGrade = Grade::factory()->create();

        $this->assertTrue($this->policy->update($guru, $ownGrade));
        $this->assertFalse($this->policy->update($guru, $otherGrade));
    }

    /** @test */
    public function siswa_cannot_update_grades()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        $student = Student::factory()->create(['user_id' => $siswaUser->id]);
        $grade = Grade::factory()->create(['student_id' => $student->id]);

        $this->assertFalse($this->policy->update($siswaUser, $grade));
    }

    /** @test */
    public function admin_can_delete_any_grade()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $grade = Grade::factory()->create();

        $this->assertTrue($this->policy->delete($admin, $grade));
    }

    /** @test */
    public function guru_can_only_delete_grades_they_created()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $teacher = Teacher::factory()->create(['user_id' => $guru->id]);
        $ownGrade = Grade::factory()->create(['teacher_id' => $teacher->id]);
        $otherGrade = Grade::factory()->create();

        $this->assertTrue($this->policy->delete($guru, $ownGrade));
        $this->assertFalse($this->policy->delete($guru, $otherGrade));
    }

    /** @test */
    public function only_admin_and_guru_can_bulk_create_grades()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($this->policy->bulkCreate($admin));
        $this->assertTrue($this->policy->bulkCreate($guru));
        $this->assertFalse($this->policy->bulkCreate($siswa));
    }
}
