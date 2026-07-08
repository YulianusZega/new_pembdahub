<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Grade;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\ParentModel;
use App\Models\Semester;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GradeAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ensure an active semester exists for GradeService
        Semester::factory()->create(['is_active' => true]);
    }

    /** @test */
    public function all_authenticated_users_can_access_grades_index()
    {
        $roles = ['superadmin', 'admin_sekolah', 'guru', 'siswa', 'orang_tua'];

        foreach ($roles as $role) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get(route('admin.grades.index'));
            $response->assertStatus(200);
        }
    }

    /** @test */
    public function guru_can_create_grade()
    {
        $guruUser = User::factory()->create(['role' => 'guru']);
        $teacher = Teacher::factory()->create(['user_id' => $guruUser->id]);
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();

        $response = $this->actingAs($guruUser)
            ->post(route('admin.grades.store'), [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'grade_type' => 'uts',
                'score' => 85,
            ]);

        $response->assertRedirect(route('admin.grades.index'));
        $this->assertDatabaseHas('grades', [
            'student_id' => $student->id,
            'score' => 85,
        ]);
    }

    /** @test */
    public function siswa_cannot_create_grade()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $teacher = Teacher::factory()->create();

        $response = $this->actingAs($siswa)
            ->post(route('admin.grades.store'), [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'grade_type' => 'uts',
                'score' => 85,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function guru_can_update_own_grades()
    {
        $guruUser = User::factory()->create(['role' => 'guru']);
        $teacher = Teacher::factory()->create(['user_id' => $guruUser->id]);
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $classroom = Classroom::factory()->create();
        $grade = Grade::factory()->create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
        ]);

        $response = $this->actingAs($guruUser)
            ->put(route('admin.grades.update', $grade), [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'classroom_id' => $classroom->id,
                'score' => 90,
            ]);

        $response->assertRedirect(route('admin.grades.index'));
    }

    /** @test */
    public function guru_cannot_update_other_teacher_grades()
    {
        $guruUser = User::factory()->create(['role' => 'guru']);
        Teacher::factory()->create(['user_id' => $guruUser->id]);
        $otherTeacher = Teacher::factory()->create();
        $grade = Grade::factory()->create(['teacher_id' => $otherTeacher->id]);

        $classroom = Classroom::factory()->create();

        $response = $this->actingAs($guruUser)
            ->put(route('admin.grades.update', $grade), [
                'student_id' => $grade->student_id,
                'subject_id' => $grade->subject_id,
                'teacher_id' => $otherTeacher->id,
                'classroom_id' => $classroom->id,
                'score' => 90,
            ]);

        $response->assertStatus(403);
    }

    /** @test */
    public function admin_can_update_any_grade()
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $teacher = Teacher::factory()->create();
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $classroom = Classroom::factory()->create();
        $grade = Grade::factory()->create([
            'teacher_id' => $teacher->id,
            'student_id' => $student->id,
            'subject_id' => $subject->id,
        ]);

        $response = $this->actingAs($admin)
            ->put(route('admin.grades.update', $grade), [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'classroom_id' => $classroom->id,
                'score' => 95,
            ]);

        $response->assertRedirect(route('admin.grades.index'));
    }

    /** @test */
    public function guru_can_delete_own_grades()
    {
        $guruUser = User::factory()->create(['role' => 'guru']);
        $teacher = Teacher::factory()->create(['user_id' => $guruUser->id]);
        $grade = Grade::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->actingAs($guruUser)
            ->delete(route('admin.grades.destroy', $grade));

        $response->assertRedirect(route('admin.grades.index'));
    }

    /** @test */
    public function guru_cannot_delete_other_teacher_grades()
    {
        $guruUser = User::factory()->create(['role' => 'guru']);
        Teacher::factory()->create(['user_id' => $guruUser->id]);
        $otherTeacher = Teacher::factory()->create();
        $grade = Grade::factory()->create(['teacher_id' => $otherTeacher->id]);

        $response = $this->actingAs($guruUser)
            ->delete(route('admin.grades.destroy', $grade));

        $response->assertStatus(403);
    }

    /** @test */
    public function siswa_can_view_own_grades()
    {
        $siswaUser = User::factory()->create(['role' => 'siswa']);
        $student = Student::factory()->create(['user_id' => $siswaUser->id]);
        $grade = Grade::factory()->create(['student_id' => $student->id]);

        $response = $this->actingAs($siswaUser)
            ->get(route('admin.grades.index'));

        $response->assertStatus(200);
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
        Grade::factory()->create(['student_id' => $child->id]);

        $response = $this->actingAs($orangtuaUser)
            ->get(route('admin.grades.index'));

        $response->assertStatus(200);
    }
}
