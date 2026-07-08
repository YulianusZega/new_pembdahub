<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\Grade;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use App\Models\TimeSlot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuruPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected User $adminUser;
    protected User $siswaUser;
    protected User $orangtuaUser;
    protected User $otherGuruUser;
    protected Teacher $teacher;
    protected Teacher $otherTeacher;
    protected School $school;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected Classroom $classroom;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze time to a Monday to ensure schedule tests work correctly regardless of the day the test runs
        \Carbon\Carbon::setTestNow('2026-06-22 09:00:00');

        // Create school
        $this->school = School::create([
            'name' => 'SMPS Pembda 2 Gunungsitoli',
            'type' => 'SMP',
            'npsn' => '20220001',
            'address' => 'Jl. Pelita No.31',
            'city' => 'Gunungsitoli',
            'province' => 'Sumatera Utara',
            'postal_code' => '22812',
            'phone' => '082168532567',
            'email' => 'info@smp2pembda.sch.id',
            'is_active' => true,
        ]);

        // Create active academic year
        $this->academicYear = AcademicYear::create([
            'year' => 'TP. 2025/2026',
            'start_date' => '2025-07-14',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        // Create active semester
        $this->semester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'semester_number' => 1,
            'semester_name' => 'Semester Ganjil 2025/2026',
            'start_date' => '2025-07-14',
            'end_date' => '2025-12-20',
            'is_active' => true,
        ]);

        // Create classroom
        $this->classroom = Classroom::create([
            'class_name' => 'VII-A',
            'class_code' => 'VII-A',
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'grade_level' => 7,
            'capacity' => 30,
        ]);

        // Create subject
        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'subject_code' => 'MTK',
            'subject_name' => 'Matematika',
            'name' => 'Matematika',
            'kkm' => 75,
            'is_active' => true,
        ]);

        // Create guru user
        $this->guruUser = User::create([
            'name' => 'Pak Ahmad',
            'email' => 'ahmad@guru.test',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'school_id' => $this->school->id,
        ]);

        // Create employee for teacher
        $employee = Employee::create([
            'school_id' => $this->school->id,
            'employee_code' => 'EMP-001',
            'full_name' => 'Ahmad Guru',
            'gender' => 'L',
            'employee_type' => 'guru',
            'employment_status' => 'yayasan',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        // Create teacher record
        $this->teacher = Teacher::create([
            'employee_id' => $employee->id,
            'user_id' => $this->guruUser->id,
            'school_id' => $this->school->id,
            'teacher_code' => 'GR-001',
            'full_name' => 'Ahmad Guru',
            'gender' => 'L',
            'education_level' => 'S1',
            'major' => 'Matematika',
            'religion' => 'Islam',
            'is_active' => true,
        ]);

        // Create a schedule linking teacher to classroom
        $todayDay = strtolower(now()->format('l'));

        $timeSlot = TimeSlot::create([
            'school_id' => $this->school->id,
            'day_of_week' => $todayDay,
            'slot_name' => 'Les 1',
            'slot_type' => 'lesson',
            'slot_order' => 1,
            'start_time' => '07:30',
            'end_time' => '08:15',
            'duration_minutes' => 45,
            'is_teaching_slot' => true,
            'is_active' => true,
        ]);

        Schedule::create([
            'school_id' => $this->school->id,
            'classroom_id' => $this->classroom->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'time_slot_id' => $timeSlot->id,
            'day_of_week' => $todayDay,
            'start_time' => '07:30',
            'end_time' => '08:15',
        ]);

        // Create admin user
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        // Create siswa user
        $this->siswaUser = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa@test.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        // Create orangtua user
        $this->orangtuaUser = User::create([
            'name' => 'Orang Tua Test',
            'email' => 'ortu@test.test',
            'password' => bcrypt('password'),
            'role' => 'orang_tua',
        ]);

        // Create another guru for cross-access testing
        $this->otherGuruUser = User::create([
            'name' => 'Pak Budi',
            'email' => 'budi@guru.test',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'school_id' => $this->school->id,
        ]);

        $otherEmployee = Employee::create([
            'school_id' => $this->school->id,
            'employee_code' => 'EMP-002',
            'full_name' => 'Budi Guru',
            'gender' => 'L',
            'employee_type' => 'guru',
            'employment_status' => 'yayasan',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        $this->otherTeacher = Teacher::create([
            'employee_id' => $otherEmployee->id,
            'user_id' => $this->otherGuruUser->id,
            'school_id' => $this->school->id,
            'teacher_code' => 'GR-002',
            'full_name' => 'Budi Guru',
            'gender' => 'L',
            'education_level' => 'S1',
            'major' => 'Bahasa Indonesia',
            'religion' => 'Kristen',
            'is_active' => true,
        ]);
    }

    // ===========================
    // AUTHENTICATION & ACCESS
    // ===========================

    public function test_guru_can_access_dashboard()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('guru.dashboard');
        $response->assertSee('Ahmad');
    }

    public function test_unauthenticated_user_cannot_access_guru_dashboard()
    {
        $response = $this->get('/guru/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_guru_dashboard()
    {
        $response = $this->actingAs($this->adminUser)->get('/guru/dashboard');
        $response->assertStatus(403);
    }

    public function test_siswa_cannot_access_guru_dashboard()
    {
        $response = $this->actingAs($this->siswaUser)->get('/guru/dashboard');
        $response->assertStatus(403);
    }

    public function test_orangtua_cannot_access_guru_dashboard()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/guru/dashboard');
        $response->assertStatus(403);
    }

    // ===========================
    // DASHBOARD
    // ===========================

    public function test_dashboard_shows_teacher_info()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Ahmad');
        $response->assertSee($this->school->name);
    }

    public function test_dashboard_shows_classroom_count()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
        // Teacher has 1 classroom via schedule
        $response->assertSee('Total Kelas');
    }

    public function test_dashboard_shows_today_schedule()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Jadwal Mengajar Hari Ini');
        $response->assertSee('Matematika');
        $response->assertSee('VII-A');
    }

    public function test_dashboard_shows_grades_count()
    {
        // Create some grades by this teacher
        $student = Student::create([
            'school_id' => $this->school->id,
            'nisn' => '9988776655',
            'full_name' => 'Siswa Test',
            'gender' => 'L',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);

        Grade::create([
            'student_id' => $student->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'semester_id' => $this->semester->id,
            'grade_type' => 'tugas',
            'score' => 85,
            'created_by' => $this->guruUser->id,
        ]);

        $response = $this->actingAs($this->guruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Nilai Diinput');
    }

    public function test_dashboard_shows_homeroom_class()
    {
        // Set teacher as homeroom teacher
        $this->classroom->update(['homeroom_teacher_id' => $this->teacher->id]);

        $response = $this->actingAs($this->guruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Wali Kelas');
        $response->assertSee('VII-A');
    }

    // ===========================
    // JADWAL
    // ===========================

    public function test_guru_can_access_jadwal()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/jadwal');
        $response->assertStatus(200);
        $response->assertViewIs('guru.jadwal');
    }

    public function test_jadwal_shows_schedule()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/jadwal');
        $response->assertStatus(200);
        $response->assertSee('Matematika');
        $response->assertSee('VII-A');
    }

    public function test_jadwal_handles_no_schedule()
    {
        // Other guru has no schedule
        $response = $this->actingAs($this->otherGuruUser)->get('/guru/jadwal');
        $response->assertStatus(200);
        $response->assertSee('Belum ada jadwal mengajar');
    }

    // ===========================
    // KELAS
    // ===========================

    public function test_guru_can_access_kelas()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/kelas');
        $response->assertStatus(200);
        $response->assertViewIs('guru.kelas');
    }

    public function test_kelas_shows_assigned_classrooms()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/kelas');
        $response->assertStatus(200);
        $response->assertSee('VII-A');
    }

    public function test_kelas_handles_no_classrooms()
    {
        $response = $this->actingAs($this->otherGuruUser)->get('/guru/kelas');
        $response->assertStatus(200);
        $response->assertSee('Belum ada kelas');
    }

    // ===========================
    // SISWA PER KELAS
    // ===========================

    public function test_guru_can_view_students_in_assigned_classroom()
    {
        // Add student to classroom
        $student = Student::create([
            'school_id' => $this->school->id,
            'nisn' => '1122334455',
            'full_name' => 'Budi Pratama',
            'gender' => 'L',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);
        $student->classrooms()->attach($this->classroom->id, [
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->guruUser)->get("/guru/kelas/{$this->classroom->id}/siswa");
        $response->assertStatus(200);
        $response->assertViewIs('guru.siswa-kelas');
        $response->assertSee('Budi Pratama');
    }

    public function test_guru_cannot_view_students_in_unassigned_classroom()
    {
        // Create a classroom the other guru teaches
        $otherClassroom = Classroom::create([
            'class_name' => 'VIII-B',
            'class_code' => 'VIII-B',
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'grade_level' => 8,
            'capacity' => 30,
        ]);

        // Other guru has no access to VII-A's direct classroom; this guru has no access to VIII-B
        $response = $this->actingAs($this->otherGuruUser)->get("/guru/kelas/{$this->classroom->id}/siswa");
        $response->assertStatus(403);
    }

    public function test_homeroom_teacher_can_view_students()
    {
        // Set other guru as homeroom teacher for classroom
        $this->classroom->update(['homeroom_teacher_id' => $this->otherTeacher->id]);

        $response = $this->actingAs($this->otherGuruUser)->get("/guru/kelas/{$this->classroom->id}/siswa");
        $response->assertStatus(200);
        $response->assertSee('VII-A');
    }

    public function test_siswa_kelas_handles_empty_students()
    {
        $response = $this->actingAs($this->guruUser)->get("/guru/kelas/{$this->classroom->id}/siswa");
        $response->assertStatus(200);
        $response->assertSee('Belum ada siswa');
    }

    // ===========================
    // NILAI
    // ===========================

    public function test_guru_can_access_nilai()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/nilai');
        $response->assertStatus(200);
        $response->assertViewIs('guru.nilai');
    }

    public function test_nilai_shows_grades_by_subject()
    {
        $student = Student::create([
            'school_id' => $this->school->id,
            'nisn' => '5566778899',
            'full_name' => 'Ani Lestari',
            'gender' => 'P',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);

        Grade::create([
            'student_id' => $student->id,
            'subject_id' => $this->subject->id,
            'teacher_id' => $this->teacher->id,
            'semester_id' => $this->semester->id,
            'grade_type' => 'uts',
            'score' => 78,
            'created_by' => $this->guruUser->id,
        ]);

        $response = $this->actingAs($this->guruUser)->get('/guru/nilai');
        $response->assertStatus(200);
        $response->assertSee('Matematika');
        $response->assertSee('Ani Lestari');
        $response->assertSee('78');
    }

    public function test_nilai_can_filter_by_semester()
    {
        $newSemester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'semester_number' => 2,
            'semester_name' => 'Semester Genap 2025/2026',
            'start_date' => '2026-01-05',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->guruUser)->get("/guru/nilai?semester_id={$newSemester->id}");
        $response->assertStatus(200);
        $response->assertSee('Belum ada nilai');
    }

    public function test_nilai_handles_empty_grades()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/nilai');
        $response->assertStatus(200);
        $response->assertSee('Belum ada nilai');
    }

    // ===========================
    // ABSENSI
    // ===========================

    public function test_guru_can_access_absensi()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/absensi');
        $response->assertStatus(200);
        $response->assertViewIs('guru.absensi');
    }

    public function test_absensi_shows_class_selector()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/absensi');
        $response->assertStatus(200);
        $response->assertSee('Pilih Kelas');
        $response->assertSee('VII-A');
    }

    public function test_absensi_shows_data_when_classroom_selected()
    {
        $student = Student::create([
            'school_id' => $this->school->id,
            'nisn' => '4455667788',
            'full_name' => 'Charlie Siswa',
            'gender' => 'L',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'classroom_id' => $this->classroom->id,
            'date' => now()->format('Y-m-d'),
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->guruUser)->get("/guru/absensi?classroom_id={$this->classroom->id}");
        $response->assertStatus(200);
        $response->assertSee('Charlie Siswa');
        $response->assertSee('Hadir');
    }

    public function test_absensi_handles_no_data()
    {
        $response = $this->actingAs($this->guruUser)->get("/guru/absensi?classroom_id={$this->classroom->id}");
        $response->assertStatus(200);
        $response->assertSee('Belum ada data absensi');
    }

    // ===========================
    // PROFIL
    // ===========================

    public function test_guru_can_access_profil()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/profil');
        $response->assertStatus(200);
        $response->assertViewIs('guru.profil');
    }

    public function test_profil_shows_teacher_data()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/profil');
        $response->assertStatus(200);
        $response->assertSee('Ahmad Guru');
        $response->assertSee('GR-001');
        $response->assertSee('S1');
        $response->assertSee('Matematika');
    }

    public function test_profil_shows_employee_data()
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/profil');
        $response->assertStatus(200);
        $response->assertSee('EMP-001');
        $response->assertSee('Yayasan');
    }

    // ===========================
    // CROSS-ROLE ACCESS CONTROL
    // ===========================

    public function test_siswa_cannot_access_any_guru_route()
    {
        $routes = ['/guru/dashboard', '/guru/jadwal', '/guru/kelas', '/guru/nilai', '/guru/absensi', '/guru/profil'];
        foreach ($routes as $route) {
            $response = $this->actingAs($this->siswaUser)->get($route);
            $response->assertStatus(403, "Expected 403 for siswa at $route");
        }
    }

    public function test_orangtua_cannot_access_any_guru_route()
    {
        $routes = ['/guru/dashboard', '/guru/jadwal', '/guru/kelas', '/guru/nilai', '/guru/absensi', '/guru/profil'];
        foreach ($routes as $route) {
            $response = $this->actingAs($this->orangtuaUser)->get($route);
            $response->assertStatus(403, "Expected 403 for orangtua at $route");
        }
    }

    // ===========================
    // EDGE CASES
    // ===========================

    public function test_dashboard_handles_no_active_year()
    {
        $this->academicYear->update(['is_active' => false]);
        $response = $this->actingAs($this->guruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
    }

    public function test_other_guru_sees_only_their_data()
    {
        // Other guru has no schedules/classrooms
        $response = $this->actingAs($this->otherGuruUser)->get('/guru/dashboard');
        $response->assertStatus(200);
        // Should show 0 classrooms
        $response->assertDontSee('VII-A');
    }
}
