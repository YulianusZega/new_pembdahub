<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceReputationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Student $student;
    protected Classroom $classroom;
    protected School $school;
    protected AcademicYear $academicYear;

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze time to a weekday morning so that recorded attendance registers as 'hadir' (present) rather than 'terlambat' (late)
        \Carbon\Carbon::setTestNow('2026-06-22 07:15:00');

        // Seed base data
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

        // Create a school
        $this->school = School::create([
            'name' => 'Test School',
            'type' => 'SMA',
            'npsn' => '12345678',
            'is_active' => true,
        ]);

        // Create an academic year
        $this->academicYear = AcademicYear::create([
            'year' => 'TP. 2025/2026',
            'start_date' => '2025-07-14',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        // Create a classroom
        $this->classroom = Classroom::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'class_code' => 'X-A',
            'class_name' => 'X A',
            'class_type' => 'reguler',
            'grade_level' => 10,
            'is_active' => true,
        ]);

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
            'must_change_password' => false,
        ]);

        // Create student user & student record
        $studentUser = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'must_change_password' => false,
        ]);

        $this->student = Student::create([
            'school_id' => $this->school->id,
            'user_id' => $studentUser->id,
            'nisn' => '0099887766',
            'full_name' => 'Siswa Test',
            'gender' => 'L',
            'status' => 'aktif',
            'entry_year' => 2025,
        ]);

        StudentClass::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        // Deactivate all other active academic years to avoid conflicts with DatabaseSeeder
        AcademicYear::where('id', '!=', $this->academicYear->id)->update(['is_active' => false]);
    }

    public function test_single_attendance_creation_awards_reputation_points(): void
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.attendances.store'), [
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => date('Y-m-d'),
            'status' => 'hadir',
        ]);

        $response->assertRedirect(route('admin.attendances.index'));

        // Assert attendance record exists
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'status' => 'hadir',
        ]);

        // Assert reputation log exists for student with +10 points
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->student->user_id,
            'points' => 10,
            'category' => 'attendance',
        ]);
    }

    public function test_single_attendance_update_adjusts_reputation_points(): void
    {
        // 1. Pre-create attendance
        $attendance = Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => date('Y-m-d'),
            'status' => 'hadir',
        ]);

        // Pre-create reputation log
        \App\Models\ReputationLog::log($this->student->user_id, 10, 'attendance', 'Hadir', $attendance);

        // Verify initial setup
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->student->user_id,
            'points' => 10,
            'category' => 'attendance',
        ]);

        // 2. Perform update via PUT request to 'alpha'
        $response = $this->actingAs($this->adminUser)->put(route('admin.attendances.update', $attendance->id), [
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => date('Y-m-d'),
            'status' => 'alpha',
        ]);

        $response->assertRedirect(route('admin.attendances.index'));

        // Assert attendance updated
        $this->assertEquals('alpha', $attendance->fresh()->status);

        // Assert reputation log updated/replaced to -10 points
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->student->user_id,
            'points' => -10,
            'category' => 'attendance',
        ]);
    }

    public function test_single_attendance_deletion_rolls_back_reputation_points(): void
    {
        // Pre-create attendance
        $attendance = Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => date('Y-m-d'),
            'status' => 'hadir',
        ]);

        // Pre-create reputation log
        \App\Models\ReputationLog::log($this->student->user_id, 10, 'attendance', 'Hadir', $attendance);

        // Delete attendance via DELETE request
        $response = $this->actingAs($this->adminUser)->delete(route('admin.attendances.destroy', $attendance->id));

        $response->assertRedirect(route('admin.attendances.index'));

        // Assert attendance deleted
        $this->assertDatabaseMissing('attendances', [
            'id' => $attendance->id,
        ]);

        // Assert reputation log rolled back/deleted
        $this->assertDatabaseMissing('reputation_logs', [
            'user_id' => $this->student->user_id,
            'reference_type' => get_class($attendance),
            'reference_id' => $attendance->id,
        ]);
    }

    public function test_rfid_scan_awards_reputation_points(): void
    {
        $this->classroom->update([
            'entry_time' => '12:00',
            'late_tolerance' => 15,
        ]);

        $this->student->update(['rfid_uid' => 'RFID-1234-ABCD']);

        $response = $this->withHeaders([
            'X-Kiosk-API-Key' => 'RAHASIA-PEMBDAHUB-12345',
        ])->postJson('/api/attendance/rfid-scan', [
            'uid' => 'RFID-1234-ABCD',
            'type' => 'rfid',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('status', 'success');

        // Assert attendance record exists
        $this->assertDatabaseHas('attendances', [
            'student_id' => $this->student->id,
            'recorded_via' => 'rfid',
            'status' => 'hadir',
        ]);

        // Assert reputation log exists for student with +10 points
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->student->user_id,
            'points' => 10,
            'category' => 'attendance',
        ]);
    }

    public function test_guru_attendance_input_awards_reputation_points(): void
    {
        $guruUser = User::create([
            'name' => 'Guru Test',
            'email' => 'guru@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'must_change_password' => false,
        ]);

        $teacher = \App\Models\Teacher::create([
            'school_id' => $this->school->id,
            'user_id' => $guruUser->id,
            'teacher_code' => 'T-TEST',
            'full_name' => 'Guru Test',
            'gender' => 'L',
            'is_active' => true,
        ]);

        $subject = \App\Models\Subject::create([
            'school_id' => $this->school->id,
            'subject_code' => 'SUBJ-TEST',
            'name' => 'Subject Test',
        ]);

        // Assign teacher to classroom
        \App\Models\TeachingAssignment::create([
            'classroom_id' => $this->classroom->id,
            'teacher_id' => $teacher->id,
            'academic_year_id' => $this->academicYear->id,
            'subject_id' => $subject->id,
            'is_active' => true,
        ]);

        $response = $this->actingAs($guruUser)->post(route('guru.absensi.store'), [
            'classroom_id' => $this->classroom->id,
            'date' => date('Y-m-d'),
            'statuses' => [
                $this->student->id => 'hadir',
            ],
        ]);

        $response->assertRedirect(route('guru.absensi'));

        // Verify student got reputation points
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->student->user_id,
            'points' => 10,
            'category' => 'attendance',
        ]);
    }

    public function test_mass_update_cleans_up_reputation_logs(): void
    {
        // 1. Pre-create attendance
        $attendance = Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => date('Y-m-d'),
            'status' => 'hadir',
        ]);

        // Pre-create reputation log
        \App\Models\ReputationLog::log($this->student->user_id, 10, 'attendance', 'Hadir', $attendance);

        // Verify initial setup
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->student->user_id,
            'points' => 10,
        ]);

        // 2. Perform mass update reset via POST
        $response = $this->actingAs($this->adminUser)->post(route('admin.attendances.mass-update.store'), [
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'status' => 'reset',
            'target_type' => 'classroom',
            'classroom_id' => $this->classroom->id,
        ]);

        $response->assertRedirect(route('admin.attendances.index'));

        // Assert attendance record deleted
        $this->assertDatabaseMissing('attendances', [
            'id' => $attendance->id,
        ]);

        // Assert reputation log deleted/rolled back
        $this->assertDatabaseMissing('reputation_logs', [
            'user_id' => $this->student->user_id,
            'reference_id' => $attendance->id,
        ]);
    }
}

