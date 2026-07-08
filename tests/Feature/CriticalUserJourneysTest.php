<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use App\Models\Grade;
use App\Models\School;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\ParentModel;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CriticalUserJourneysTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function complete_student_enrollment_journey()
    {
        $school = School::factory()->create();
        $admin = User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => $school->id,
        ]);

        // Admin creates a student
        $response = $this->actingAs($admin)
            ->post(route('admin.students.store'), [
                'school_id' => $school->id,
                'nisn' => '2024001001',
                'nis' => '2024001',
                'full_name' => 'Ahmad Rizki',
                'gender' => 'L',
                'entry_year' => '2024',
            ]);

        $response->assertRedirect();
        $student = Student::where('nisn', '2024001001')->first();
        $this->assertNotNull($student);
        $this->assertEquals('Ahmad Rizki', $student->full_name);
    }

    /** @test */
    public function complete_grading_workflow()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $teacher = Teacher::factory()->create(['user_id' => $guru->id]);
        $student = Student::factory()->create();
        $subject = Subject::factory()->create();
        $semester = Semester::factory()->create();
        $classroom = Classroom::factory()->create([
            'school_id' => $student->school_id,
        ]);

        // Guru creates a grade
        $response = $this->actingAs($guru)
            ->post(route('admin.grades.store'), [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'grade_type' => 'uts',
                'score' => 85,
            ]);

        $response->assertRedirect();
        $grade = Grade::where('student_id', $student->id)->first();
        $this->assertNotNull($grade);
        $this->assertEquals(85, $grade->score);

        // Guru updates the grade
        $response = $this->actingAs($guru)
            ->put(route('admin.grades.update', $grade), [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'classroom_id' => $classroom->id,
                'score' => 90,
            ]);

        $response->assertRedirect();
        $grade->refresh();
        $this->assertEquals(90, $grade->score);

        // Other guru should not be able to update (sends valid data)
        $otherGuru = User::factory()->create(['role' => 'guru']);
        Teacher::factory()->create(['user_id' => $otherGuru->id]);
        $response = $this->actingAs($otherGuru)
            ->put(route('admin.grades.update', $grade), [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'classroom_id' => $classroom->id,
                'score' => 100,
            ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function user_management_workflow()
    {
        $superadmin = User::factory()->create(['role' => 'superadmin']);
        $school = School::factory()->create();

        // Superadmin creates an admin
        $response = $this->actingAs($superadmin)
            ->post(route('admin.users.store'), [
                'name' => 'Admin Sekolah',
                'username' => 'adminsekolah',
                'email' => 'admin@school.com',
                'password' => 'Password123A',
                'password_confirmation' => 'Password123A',
                'role' => 'admin_sekolah',
                'school_id' => $school->id,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $newAdmin = User::where('email', 'admin@school.com')->first();
        $this->assertNotNull($newAdmin);

        // Admin creates a guru
        $response = $this->actingAs($newAdmin)
            ->post(route('admin.users.store'), [
                'name' => 'Guru Matematika',
                'username' => 'gurumtk',
                'email' => 'guru@school.com',
                'password' => 'Password123A',
                'password_confirmation' => 'Password123A',
                'role' => 'guru',
                'school_id' => $school->id,
                'is_active' => true,
            ]);

        $response->assertRedirect();
        $guru = User::where('email', 'guru@school.com')->first();
        $this->assertNotNull($guru);

        // Admin cannot update superadmin
        $response = $this->actingAs($newAdmin)
            ->put(route('admin.users.update', $superadmin), [
                'name' => 'Hacked Name',
                'username' => $superadmin->username,
                'email' => $superadmin->email,
                'role' => $superadmin->role,
                'school_id' => $superadmin->school_id,
            ]);
        $response->assertStatus(403);

        // Guru cannot create users
        $response = $this->actingAs($guru)
            ->post(route('admin.users.store'), [
                'name' => 'Unauthorized User',
                'email' => 'hack@test.com',
                'password' => 'password',
                'role' => 'siswa',
            ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function parent_viewing_children_data_workflow()
    {
        $orangtuaUser = User::factory()->create(['role' => 'orang_tua']);
        $child = Student::factory()->create();
        ParentModel::create([
            'user_id' => $orangtuaUser->id,
            'student_id' => $child->id,
            'relation_type' => 'ayah',
            'full_name' => $orangtuaUser->name,
        ]);

        // Orang tua can view child profile
        $response = $this->actingAs($orangtuaUser)
            ->get(route('admin.students.show', $child));
        $response->assertStatus(200);

        // Orang tua can view grades
        $response = $this->actingAs($orangtuaUser)
            ->get(route('admin.grades.index'));
        $response->assertStatus(200);

        // Cannot view other students
        $otherStudent = Student::factory()->create();
        $response = $this->actingAs($orangtuaUser)
            ->get(route('admin.students.show', $otherStudent));
        $response->assertStatus(403);

        // Cannot update child profile (send valid data to pass validation)
        $response = $this->actingAs($orangtuaUser)
            ->put(route('admin.students.update', $child), [
                'school_id' => $child->school_id,
                'nisn' => $child->nisn,
                'full_name' => 'Changed Name',
                'gender' => $child->gender,
            ]);
        $response->assertStatus(403);
    }

    /** @test */
    public function cross_school_authorization_test()
    {
        $school1 = School::factory()->create(['name' => 'School A']);
        $school2 = School::factory()->create(['name' => 'School B']);

        $adminSchool1 = User::factory()->create([
            'role' => 'admin_sekolah',
            'school_id' => $school1->id,
        ]);

        $studentSchool2 = Student::factory()->create([
            'school_id' => $school2->id,
        ]);

        $response = $this->actingAs($adminSchool1)
            ->get(route('admin.students.show', $studentSchool2));

        $response->assertStatus(200);
    }

    /** @test */
    public function unauthorized_access_attempts()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $school = School::factory()->create();
        $otherStudent = Student::factory()->create(['school_id' => $school->id]);

        // Siswa cannot access students index
        $response = $this->actingAs($siswa)
            ->get(route('admin.students.index'));
        $response->assertStatus(403);

        // Siswa cannot create students (send valid data to reach policy)
        $response = $this->actingAs($siswa)
            ->post(route('admin.students.store'), [
                'school_id' => $school->id,
                'nisn' => '9999999999',
                'full_name' => 'Unauthorized Student',
                'gender' => 'L',
                'entry_year' => '2024',
            ]);
        $response->assertStatus(403);

        // Siswa cannot view other student profiles
        $response = $this->actingAs($siswa)
            ->get(route('admin.students.show', $otherStudent));
        $response->assertStatus(403);

        // Siswa cannot create grades (send valid data to reach policy)
        $teacher = Teacher::factory()->create();
        $subject = Subject::factory()->create();
        $response = $this->actingAs($siswa)
            ->post(route('admin.grades.store'), [
                'student_id' => $otherStudent->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'grade_type' => 'uts',
                'score' => 85,
            ]);
        $response->assertStatus(403);

        // Siswa cannot access users index
        $response = $this->actingAs($siswa)
            ->get(route('admin.users.index'));
        $response->assertStatus(403);
    }
}
