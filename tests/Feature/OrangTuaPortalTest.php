<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\Grade;
use App\Models\ParentModel;
use App\Models\PaymentType;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrangTuaPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $orangtuaUser;
    protected User $siswaUser;
    protected User $adminUser;
    protected User $otherOrangtuaUser;
    protected Student $child1;
    protected Student $child2;
    protected Student $otherChild;
    protected School $school;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected Classroom $classroom1;
    protected Classroom $classroom2;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->academicYear = AcademicYear::create([
            'year' => 'TP. 2025/2026',
            'start_date' => '2025-07-14',
            'end_date' => '2026-06-30',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'semester_number' => 1,
            'semester_name' => 'Semester Ganjil 2025/2026',
            'start_date' => '2025-07-14',
            'end_date' => '2025-12-20',
            'is_active' => true,
        ]);

        $this->classroom1 = Classroom::create([
            'class_name' => 'VII-A',
            'class_code' => 'VII-A',
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'grade_level' => 7,
            'capacity' => 30,
        ]);
        $this->classroom2 = Classroom::create([
            'class_name' => 'IX-B',
            'class_code' => 'IX-B',
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'grade_level' => 9,
            'capacity' => 30,
        ]);

        // Parent user
        $this->orangtuaUser = User::create([
            'name' => 'Pak Joko',
            'email' => 'joko@parent.test',
            'password' => bcrypt('password'),
            'role' => 'orang_tua',
        ]);

        // Child 1
        $this->child1 = Student::create([
            'school_id' => $this->school->id,
            'nis' => '2025001',
            'nisn' => '0012345678',
            'full_name' => 'Anak Pertama',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2012-03-10',
            'religion' => 'Kristen',
            'address' => 'Jl. Merpati No. 10',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);
        $this->child1->classrooms()->attach($this->classroom1->id, [
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        // Child 2
        $this->child2 = Student::create([
            'school_id' => $this->school->id,
            'nis' => '2025002',
            'nisn' => '0012345679',
            'full_name' => 'Anak Kedua',
            'gender' => 'P',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-08-20',
            'religion' => 'Kristen',
            'address' => 'Jl. Merpati No. 10',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);
        $this->child2->classrooms()->attach($this->classroom2->id, [
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        // Link parent to children
        ParentModel::create([
            'user_id' => $this->orangtuaUser->id,
            'student_id' => $this->child1->id,
            'relation_type' => 'ayah',
            'full_name' => 'Pak Joko',
            'phone' => '081234567890',
        ]);
        ParentModel::create([
            'user_id' => $this->orangtuaUser->id,
            'student_id' => $this->child2->id,
            'relation_type' => 'ayah',
            'full_name' => 'Pak Joko',
            'phone' => '081234567890',
        ]);

        // Another parent (for isolation testing)
        $this->otherOrangtuaUser = User::create([
            'name' => 'Ibu Sari',
            'email' => 'sari@parent.test',
            'password' => bcrypt('password'),
            'role' => 'orang_tua',
        ]);
        $this->otherChild = Student::create([
            'school_id' => $this->school->id,
            'nis' => '2025003',
            'nisn' => '0012345680',
            'full_name' => 'Anak Orang Lain',
            'gender' => 'L',
            'birth_place' => 'Medan',
            'birth_date' => '2011-01-15',
            'religion' => 'Islam',
            'address' => 'Jl. Kenanga No. 5',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);
        ParentModel::create([
            'user_id' => $this->otherOrangtuaUser->id,
            'student_id' => $this->otherChild->id,
            'relation_type' => 'ibu',
            'full_name' => 'Ibu Sari',
            'phone' => '081234567891',
        ]);

        // Siswa user (for cross-role test)
        $this->siswaUser = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa@test.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        // Admin user (for cross-role test)
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);
    }

    // ===========================
    // AUTHENTICATION & ACCESS
    // ===========================

    public function test_orangtua_can_access_dashboard()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('orangtua.dashboard');
    }

    public function test_unauthenticated_user_cannot_access_orangtua_dashboard()
    {
        $response = $this->get('/orang-tua/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_siswa_cannot_access_orangtua_dashboard()
    {
        $response = $this->actingAs($this->siswaUser)->get('/orang-tua/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_orangtua_dashboard()
    {
        $response = $this->actingAs($this->adminUser)->get('/orang-tua/dashboard');
        $response->assertStatus(403);
    }

    // ===========================
    // DASHBOARD - MULTI-CHILD
    // ===========================

    public function test_dashboard_shows_all_children()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Anak Pertama');
        $response->assertSee('Anak Kedua');
    }

    public function test_dashboard_shows_children_classrooms()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/dashboard');
        $response->assertStatus(200);
        $response->assertSee('VII-A');
        $response->assertSee('IX-B');
    }

    public function test_dashboard_does_not_show_other_parents_children()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/dashboard');
        $response->assertStatus(200);
        $response->assertDontSee('Anak Orang Lain');
    }

    // ===========================
    // CHILD DETAIL - NILAI
    // ===========================

    public function test_orangtua_can_view_child_nilai()
    {
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK', 'school_id' => $this->school->id]);
        Grade::create([
            'student_id' => $this->child1->id,
            'subject_id' => $subject->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'score' => 88,
            'grade_type' => 'uts',
        ]);

        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child1->id . '/nilai');
        $response->assertStatus(200);
        $response->assertViewIs('orangtua.nilai');
        $response->assertSee('Matematika');
        $response->assertSee('88');
    }

    public function test_orangtua_cannot_view_other_parents_child_nilai()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->otherChild->id . '/nilai');
        $response->assertStatus(404);
    }

    // ===========================
    // CHILD DETAIL - TAGIHAN
    // ===========================

    public function test_orangtua_can_view_child_tagihan()
    {
        $paymentType = PaymentType::create(['school_id' => $this->school->id, 'type_code' => 'SPP', 'type_name' => 'SPP Bulanan', 'amount' => 600000, 'is_recurring' => true, 'is_active' => true]);
        StudentBill::create([
            'student_id' => $this->child1->id,
            'payment_type_id' => $paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 600000,
            'paid_amount' => 200000,
            'status' => 'cicilan',
            'year' => 2025,
            'month' => 8,
        ]);

        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child1->id . '/tagihan');
        $response->assertStatus(200);
        $response->assertViewIs('orangtua.tagihan');
        $response->assertSee('600.000');
    }

    public function test_orangtua_cannot_view_other_parents_child_tagihan()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->otherChild->id . '/tagihan');
        $response->assertStatus(404);
    }

    // ===========================
    // CHILD DETAIL - ABSENSI
    // ===========================

    public function test_orangtua_can_view_child_absensi()
    {
        Attendance::create([
            'student_id' => $this->child1->id,
            'classroom_id' => $this->classroom1->id,
            'date' => '2025-07-15',
            'status' => 'hadir',
        ]);
        Attendance::create([
            'student_id' => $this->child1->id,
            'classroom_id' => $this->classroom1->id,
            'date' => '2025-07-16',
            'status' => 'alpha',
        ]);

        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child1->id . '/absensi');
        $response->assertStatus(200);
        $response->assertViewIs('orangtua.absensi');
        $response->assertViewHas('summary', function ($summary) {
            return $summary['present'] === 1 && $summary['absent'] === 1;
        });
    }

    public function test_orangtua_cannot_view_other_parents_child_absensi()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->otherChild->id . '/absensi');
        $response->assertStatus(404);
    }

    // ===========================
    // CHILD DETAIL - JADWAL
    // ===========================

    public function test_orangtua_can_view_child_jadwal()
    {
        $subject = Subject::create(['name' => 'Bahasa Inggris', 'code' => 'BIG', 'school_id' => $this->school->id]);
        $teacherUser = User::create(['name' => 'Teacher', 'email' => 'teacher@test.test', 'password' => bcrypt('password'), 'role' => 'guru']);
        $employee = Employee::create(['school_id' => $this->school->id, 'user_id' => $teacherUser->id, 'employee_code' => 'EMP001', 'full_name' => 'Mr. John', 'gender' => 'L', 'employee_type' => 'guru', 'employment_status' => 'yayasan', 'tmt_date' => '2024-01-15', 'is_active' => true]);
        $teacher = Teacher::create(['employee_id' => $employee->id, 'user_id' => $teacherUser->id, 'school_id' => $this->school->id, 'teacher_code' => 'GR001', 'full_name' => 'Mr. John', 'gender' => 'L']);

        Schedule::create([
            'classroom_id' => $this->classroom1->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'semester_id' => $this->semester->id,
            'day_of_week' => 'tuesday',
            'start_time' => '08:00',
            'end_time' => '09:30',
        ]);

        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child1->id . '/jadwal');
        $response->assertStatus(200);
        $response->assertViewIs('orangtua.jadwal');
        $response->assertSee('Bahasa Inggris');
    }

    public function test_orangtua_cannot_view_other_parents_child_jadwal()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->otherChild->id . '/jadwal');
        $response->assertStatus(404);
    }

    // ===========================
    // CROSS-ROLE FULL ISOLATION
    // ===========================

    public function test_siswa_cannot_access_any_orangtua_route()
    {
        $routes = [
            '/orang-tua/dashboard',
            '/orang-tua/anak/' . $this->child1->id . '/nilai',
            '/orang-tua/anak/' . $this->child1->id . '/tagihan',
            '/orang-tua/anak/' . $this->child1->id . '/absensi',
            '/orang-tua/anak/' . $this->child1->id . '/jadwal',
        ];
        foreach ($routes as $route) {
            $response = $this->actingAs($this->siswaUser)->get($route);
            $response->assertStatus(403, "Siswa should not access {$route}");
        }
    }

    // ===========================
    // EDGE CASES
    // ===========================

    public function test_dashboard_handles_parent_with_no_children()
    {
        $lonelyParent = User::create([
            'name' => 'Lonely Parent',
            'email' => 'lonely@parent.test',
            'password' => bcrypt('password'),
            'role' => 'orang_tua',
        ]);

        $response = $this->actingAs($lonelyParent)->get('/orang-tua/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Belum ada data anak');
    }

    public function test_nilai_handles_empty_grades()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child1->id . '/nilai');
        $response->assertStatus(200);
        $response->assertSee('Belum ada nilai');
    }

    public function test_jadwal_handles_no_schedule()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child1->id . '/jadwal');
        $response->assertStatus(200);
        $response->assertSee('Belum ada jadwal');
    }

    public function test_tagihan_handles_no_bills()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child1->id . '/tagihan');
        $response->assertStatus(200);
        $response->assertSee('Tidak ada tagihan');
    }

    public function test_orangtua_can_view_second_child_data()
    {
        $subject = Subject::create(['name' => 'Fisika', 'code' => 'FIS', 'school_id' => $this->school->id]);
        Grade::create([
            'student_id' => $this->child2->id,
            'subject_id' => $subject->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'score' => 95,
            'grade_type' => 'uas',
        ]);

        $response = $this->actingAs($this->orangtuaUser)->get('/orang-tua/anak/' . $this->child2->id . '/nilai');
        $response->assertStatus(200);
        $response->assertSee('Anak Kedua');
        $response->assertSee('95');
    }

    public function test_orangtua_can_download_child_published_report_card_when_enabled()
    {
        // Enable report card visibility
        \App\Models\Setting::setValue('show_report_card', true, 'boolean', 'raport');

        // Create a published report card for child1
        $reportCard = \App\Models\ReportCard::create([
            'student_id' => $this->child1->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom1->id,
            'average_score' => 88,
            'predicate' => 'B',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->orangtuaUser)
            ->get("/orang-tua/anak/{$this->child1->id}/raport/{$reportCard->id}/download");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_orangtua_cannot_download_child_report_card_when_disabled()
    {
        // Disable report card visibility
        \App\Models\Setting::setValue('show_report_card', false, 'boolean', 'raport');

        // Create a published report card for child1
        $reportCard = \App\Models\ReportCard::create([
            'student_id' => $this->child1->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom1->id,
            'average_score' => 88,
            'predicate' => 'B',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->orangtuaUser)
            ->get("/orang-tua/anak/{$this->child1->id}/raport/{$reportCard->id}/download");

        $response->assertStatus(403);
    }
}
