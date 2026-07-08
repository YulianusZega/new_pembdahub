<?php

namespace Tests\Feature;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $adminUser;
    private Classroom $classroom;
    private Subject $subject;
    private Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the database
        $this->artisan('db:seed');

        // Get an admin user for testing
        $this->adminUser = User::where('role', 'admin_sekolah')->first();
        $this->adminUser->update(['must_change_password' => false]);

        // Get test data
        $this->classroom = Classroom::first();
        $this->subject = Subject::first();
        $this->teacher = Teacher::first();
    }

    /** @test */
    public function can_view_schedules_list()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/schedules');

        $response->assertStatus(200);
        $response->assertViewIs('admin.schedules.index');
    }

    /** @test */
    public function can_view_create_schedule_page()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/schedules/create');

        $response->assertStatus(200);
        $response->assertViewIs('admin.schedules.create');
    }

    /** @test */
    public function can_create_schedule()
    {
        $data = [
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 1,
            'start_time' => '07:00',
            'end_time' => '08:30',
            'room' => 'Ruang 101',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/admin/schedules', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'day_of_week' => 1,
        ]);
    }

    /** @test */
    public function can_view_schedule_detail()
    {
        $schedule = Schedule::first();

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/schedules/{$schedule->id}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.schedules.show');
    }

    /** @test */
    public function can_view_edit_schedule_page()
    {
        $schedule = Schedule::first();

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/schedules/{$schedule->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.schedules.edit');
    }

    /** @test */
    public function can_update_schedule()
    {
        $schedule = Schedule::first();

        $data = [
            'classroom_id' => $schedule->classroom_id,
            'subject_id' => $schedule->subject_id,
            'teacher_id' => $schedule->teacher_id,
            'day_of_week' => 3,
            'start_time' => '10:00',
            'end_time' => '11:00',
            'room' => 'Ruang Update',
        ];

        $response = $this->actingAs($this->adminUser)
            ->patch("/admin/schedules/{$schedule->id}", $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('schedules', [
            'id' => $schedule->id,
            'day_of_week' => 3,
            'room' => 'Ruang Update',
        ]);
    }

    /** @test */
    public function can_delete_schedule()
    {
        $schedule = Schedule::first();
        $scheduleId = $schedule->id;

        $response = $this->actingAs($this->adminUser)
            ->delete("/admin/schedules/{$scheduleId}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('schedules', [
            'id' => $scheduleId,
        ]);
    }

    /** @test */
    public function validates_required_fields()
    {
        $data = [
            'classroom_id' => '',
            'subject_id' => '',
            'teacher_id' => '',
            'day_of_week' => '',
            'start_time' => '',
            'end_time' => '',
        ];

        $response = $this->actingAs($this->adminUser)
            ->post('/admin/schedules', $data);

        $response->assertSessionHasErrors([
            'classroom_id',
            'subject_id',
            'teacher_id',
            'day_of_week',
            'start_time',
            'end_time',
        ]);
    }

    /** @test */
    public function can_filter_schedules_by_day()
    {
        $response = $this->actingAs($this->adminUser)
            ->get('/admin/schedules?day=1');

        $response->assertStatus(200);
        $response->assertViewIs('admin.schedules.index');
    }

    /** @test */
    public function can_search_schedules()
    {
        $classroom = Classroom::first();

        $response = $this->actingAs($this->adminUser)
            ->get("/admin/schedules?search={$classroom->class_name}");

        $response->assertStatus(200);
        $response->assertViewIs('admin.schedules.index');
    }
}
