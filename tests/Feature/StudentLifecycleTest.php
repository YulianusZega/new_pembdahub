<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use App\Services\StudentLifecycleService;

class StudentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private StudentLifecycleService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(StudentLifecycleService::class);
    }

    // ==========================================
    // STATUS TRANSITION TESTS
    // ==========================================

    public function test_can_enroll_new_student(): void
    {
        $school = School::factory()->create();
        $classroom = Classroom::factory()->create(['school_id' => $school->id]);
        AcademicYear::factory()->create(['is_active' => true]);

        $student = Student::factory()->create([
            'school_id' => $school->id,
            'status' => 'calon',
        ]);

        $history = $this->service->enrollStudent($student, $classroom);

        $this->assertEquals('aktif', $student->fresh()->status);
        $this->assertDatabaseHas('student_status_histories', [
            'student_id' => $student->id,
            'from_status' => 'calon',
            'to_status' => 'aktif',
        ]);
    }

    public function test_student_status_transition_creates_history(): void
    {
        $student = Student::factory()->create(['status' => 'aktif']);

        $history = $student->transitionTo('cuti', 'Sakit berkepanjangan');

        $this->assertEquals('cuti', $student->fresh()->status);
        $this->assertEquals('aktif', $history->from_status);
        $this->assertEquals('cuti', $history->to_status);
        $this->assertEquals('Sakit berkepanjangan', $history->reason);
    }

    public function test_invalid_transition_throws_exception(): void
    {
        $student = Student::factory()->create(['status' => 'calon']);

        $this->expectException(\InvalidArgumentException::class);

        // calon cannot directly go to lulus
        $student->transitionTo('lulus');
    }

    public function test_graduation_creates_alumni_record(): void
    {
        $student = Student::factory()->create(['status' => 'aktif']);

        $student->transitionTo('lulus', 'Lulus');

        $this->assertEquals('lulus', $student->fresh()->status);
        $this->assertDatabaseHas('alumni', [
            'student_id' => $student->id,
        ]);
    }

    // ==========================================
    // ADMIN PORTAL TESTS
    // ==========================================

    public function test_admin_can_view_lifecycle_history(): void
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $school = School::factory()->create();
        $admin->update(['school_id' => $school->id]);
        $student = Student::factory()->create(['school_id' => $school->id]);

        $response = $this->actingAs($admin)->get(
            route('admin.students.lifecycle.history', $student)
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_transition_form(): void
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $school = School::factory()->create();
        $admin->update(['school_id' => $school->id]);
        $student = Student::factory()->create([
            'school_id' => $school->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get(
            route('admin.students.lifecycle.transition', $student)
        );

        $response->assertStatus(200);
    }

    public function test_admin_can_view_promotions_page(): void
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $school = School::factory()->create();
        $admin->update(['school_id' => $school->id]);

        $response = $this->actingAs($admin)->get(route('admin.promotions.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_view_alumni_list(): void
    {
        $admin = User::factory()->create(['role' => 'admin_sekolah']);
        $school = School::factory()->create();
        $admin->update(['school_id' => $school->id]);

        $response = $this->actingAs($admin)->get(route('admin.alumni.index'));

        $response->assertStatus(200);
    }
}
