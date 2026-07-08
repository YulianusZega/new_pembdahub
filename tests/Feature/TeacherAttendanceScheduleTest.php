<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\EmployeeAttendance;
use App\Models\ReputationLog;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class TeacherAttendanceScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected User $teacherUser;
    protected Employee $employee;
    protected Teacher $teacher;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed base database (roles, default settings, etc.)
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

        // Create a school
        $this->school = School::create([
            'name' => 'Test School SMA',
            'type' => 'SMA',
            'npsn' => '87654321',
            'is_active' => true,
        ]);

        // Create teacher user
        $this->teacherUser = User::create([
            'name' => 'Guru Test',
            'email' => 'gurutest@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'must_change_password' => false,
        ]);

        // Create employee
        $this->employee = Employee::create([
            'school_id' => $this->school->id,
            'user_id' => $this->teacherUser->id,
            'employee_code' => 'EMP-GURU-001',
            'nip' => '199001012020121001',
            'full_name' => 'Guru Test',
            'gender' => 'L',
            'employee_type' => 'guru',
            'rfid_uid' => 'TEACHER_RFID_123',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        // Create teacher record
        $this->teacher = Teacher::create([
            'employee_id' => $this->employee->id,
            'user_id' => $this->teacherUser->id,
            'school_id' => $this->school->id,
            'teacher_code' => 'T-001',
            'full_name' => 'Guru Test',
            'gender' => 'L',
            'is_active' => true,
        ]);
    }

    public function test_teacher_scan_on_scheduled_day_is_recorded_as_hadir_mengajar(): void
    {
        // Monday, 22 June 2026 is a Monday
        Carbon::setTestNow('2026-06-22 08:00:00');

        $classroomId = \App\Models\Classroom::first()?->id ?? 1;
        $subjectId = \App\Models\Subject::first()?->id ?? 1;
        $academicYearId = \App\Models\AcademicYear::first()?->id ?? 1;
        $semesterId = \Illuminate\Support\Facades\DB::table('semesters')->first()?->id ?? 1;

        // Create schedule on monday
        Schedule::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->teacher->id,
            'classroom_id' => $classroomId,
            'subject_id' => $subjectId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
            'day_of_week' => 'monday',
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);

        // Hit rfid-scan endpoint
        $response = $this->postJson('/api/attendance/rfid-scan', [
            'uid' => 'TEACHER_RFID_123',
        ], [
            'X-Kiosk-API-Key' => 'RAHASIA-PEMBDAHUB-12345'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Hadir Mengajar',
                'action_code' => 'CHECK_IN'
            ]);

        $this->assertDatabaseHas('employee_attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'hadir',
            'notes' => null,
        ]);

        // Verify no point log is generated for normal check-in
        $this->assertDatabaseMissing('reputation_logs', [
            'user_id' => $this->teacherUser->id,
            'points' => 15,
        ]);
    }

    public function test_teacher_scan_on_non_scheduled_day_is_recorded_as_tugas_khusus_and_awards_points(): void
    {
        // Tuesday, 23 June 2026 is a Tuesday
        Carbon::setTestNow('2026-06-23 08:00:00');

        $classroomId = \App\Models\Classroom::first()?->id ?? 1;
        $subjectId = \App\Models\Subject::first()?->id ?? 1;
        $academicYearId = \App\Models\AcademicYear::first()?->id ?? 1;
        $semesterId = \Illuminate\Support\Facades\DB::table('semesters')->first()?->id ?? 1;

        // Create schedule on monday ONLY
        Schedule::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->teacher->id,
            'classroom_id' => $classroomId,
            'subject_id' => $subjectId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
            'day_of_week' => 'monday',
            'start_time' => '08:00:00',
            'end_time' => '09:30:00',
        ]);

        // Hit employee-rfid-scan endpoint
        $response = $this->postJson('/api/attendance/employee-rfid-scan', [
            'uid' => 'TEACHER_RFID_123',
        ], [
            'X-Kiosk-API-Key' => 'RAHASIA-PEMBDAHUB-12345'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'action' => 'masuk',
                'message' => "Selamat datang, {$this->employee->full_name}! (Tugas Khusus)",
            ]);

        $this->assertDatabaseHas('employee_attendances', [
            'employee_id' => $this->employee->id,
            'status' => 'hadir',
            'notes' => 'tugas_khusus',
        ]);

        // Verify reputation log has +15 points
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->teacherUser->id,
            'points' => 15,
            'category' => 'attendance',
            'description' => 'Tugas Khusus: Kehadiran di luar jadwal mengajar',
        ]);
    }

    public function test_teacher_scan_bypasses_late_indicator(): void
    {
        // Monday, 22 June 2026
        Carbon::setTestNow('2026-06-22 09:30:00'); // 9:30 AM is past the 7:30 limit

        $attendance = EmployeeAttendance::create([
            'employee_id' => $this->employee->id,
            'school_id' => $this->school->id,
            'date' => '2026-06-22',
            'time_in' => '09:30:00',
            'status' => 'hadir',
        ]);

        // Since it's a teacher, isLate() should return false
        $this->assertFalse($attendance->isLate());
    }

    public function test_teacher_can_access_own_attendance_page(): void
    {
        $response = $this->actingAs($this->teacherUser)
            ->get(route('guru.absensi.saya'));

        $response->assertStatus(200)
            ->assertViewIs('guru.absensi-saya')
            ->assertSee('Absensi Saya')
            ->assertSee('Kalender Kehadiran Bulanan');
    }

    public function test_staff_scan_on_weekday_is_recorded_as_normal_presence(): void
    {
        // Monday, 22 June 2026
        Carbon::setTestNow('2026-06-22 07:15:00');

        $staffUser = User::create([
            'name' => 'Staf Test',
            'email' => 'staftest@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'admin_sekolah',
            'must_change_password' => false,
        ]);

        $staff = Employee::create([
            'school_id' => $this->school->id,
            'user_id' => $staffUser->id,
            'employee_code' => 'EMP-STAF-001',
            'nip' => '199001012020121002',
            'full_name' => 'Staf Test',
            'gender' => 'L',
            'employee_type' => 'staff_tu',
            'rfid_uid' => 'STAFF_RFID_123',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/attendance/rfid-scan', [
            'uid' => 'STAFF_RFID_123',
        ], [
            'X-Kiosk-API-Key' => 'RAHASIA-PEMBDAHUB-12345'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Berhasil Masuk',
                'action_code' => 'CHECK_IN'
            ]);

        $this->assertDatabaseHas('employee_attendances', [
            'employee_id' => $staff->id,
            'status' => 'hadir',
            'notes' => null,
        ]);
    }

    public function test_staff_scan_on_weekend_is_recorded_as_tugas_khusus_and_awards_points(): void
    {
        // Saturday, 27 June 2026
        Carbon::setTestNow('2026-06-27 08:00:00');

        $staffUser = User::create([
            'name' => 'Staf Test 2',
            'email' => 'staftest2@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'admin_sekolah',
            'must_change_password' => false,
        ]);

        $staff = Employee::create([
            'school_id' => $this->school->id,
            'user_id' => $staffUser->id,
            'employee_code' => 'EMP-STAF-002',
            'nip' => '199001012020121003',
            'full_name' => 'Staf Test 2',
            'gender' => 'P',
            'employee_type' => 'staff_tu',
            'rfid_uid' => 'STAFF_RFID_456',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        $response = $this->postJson('/api/attendance/employee-rfid-scan', [
            'uid' => 'STAFF_RFID_456',
        ], [
            'X-Kiosk-API-Key' => 'RAHASIA-PEMBDAHUB-12345'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'action' => 'masuk',
                'message' => "Selamat datang, {$staff->full_name}! (Tugas Khusus)",
            ]);

        $this->assertDatabaseHas('employee_attendances', [
            'employee_id' => $staff->id,
            'status' => 'hadir',
            'notes' => 'tugas_khusus',
        ]);

        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $staffUser->id,
            'points' => 15,
            'category' => 'attendance',
            'description' => 'Tugas Khusus: Jam tambahan di luar hari masuk',
        ]);
    }

    public function test_staff_scan_lateness_is_determined_by_school_classroom_entry_time(): void
    {
        // Monday, 22 June 2026
        Carbon::setTestNow('2026-06-22 07:55:00');

        // Create a classroom for this school with entry_time = '08:00'
        \App\Models\Classroom::create([
            'school_id' => $this->school->id,
            'academic_year_id' => \App\Models\AcademicYear::first()?->id ?? 1,
            'class_name' => 'Test Class entry time',
            'class_code' => 'TEST-CODE-101',
            'grade_level' => '10',
            'entry_time' => '08:00',
            'late_tolerance' => 15,
            'is_active' => true,
        ]);

        $staffUser = User::create([
            'name' => 'Staf Lateness Test',
            'email' => 'staflateness@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'admin_sekolah',
            'must_change_password' => false,
        ]);

        $staff = Employee::create([
            'school_id' => $this->school->id,
            'user_id' => $staffUser->id,
            'employee_code' => 'EMP-STAF-009',
            'nip' => '199001012020121009',
            'full_name' => 'Staf Lateness',
            'gender' => 'L',
            'employee_type' => 'staff_tu',
            'rfid_uid' => 'STAFF_RFID_999',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        // Scan at 07:55:00 (should be on-time)
        $response = $this->postJson('/api/attendance/rfid-scan', [
            'uid' => 'STAFF_RFID_999',
        ], [
            'X-Kiosk-API-Key' => 'RAHASIA-PEMBDAHUB-12345'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Berhasil Masuk',
            ]);

        // Scan late at 08:05:00 (using a new staff record to avoid duplicate attendance of the same day)
        Carbon::setTestNow('2026-06-22 08:05:00');

        $staffUserLate = User::create([
            'name' => 'Staf Lateness Test 2',
            'email' => 'staflateness2@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'admin_sekolah',
            'must_change_password' => false,
        ]);

        $staffLate = Employee::create([
            'school_id' => $this->school->id,
            'user_id' => $staffUserLate->id,
            'employee_code' => 'EMP-STAF-010',
            'nip' => '199001012020121010',
            'full_name' => 'Staf Lateness 2',
            'gender' => 'L',
            'employee_type' => 'staff_tu',
            'rfid_uid' => 'STAFF_RFID_888',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        $responseLate = $this->postJson('/api/attendance/rfid-scan', [
            'uid' => 'STAFF_RFID_888',
        ], [
            'X-Kiosk-API-Key' => 'RAHASIA-PEMBDAHUB-12345'
        ]);

        $responseLate->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Terlambat',
            ]);
    }
}
