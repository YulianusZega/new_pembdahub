<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\Grade;
use App\Models\ParentModel;
use App\Models\PaymentType;
use App\Models\ReportCard;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\School;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimeSlot;
use App\Models\User;
use App\Models\FinalGrade;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiswaPortalTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswaUser;
    protected User $adminUser;
    protected User $orangtuaUser;
    protected Student $student;
    protected School $school;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected Classroom $classroom;

    protected function setUp(): void
    {
        parent::setUp();

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

        // Create siswa user
        $this->siswaUser = User::create([
            'name' => 'Budi Siswa',
            'email' => 'budi@student.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);

        // Create student record
        $this->student = Student::create([
            'user_id' => $this->siswaUser->id,
            'school_id' => $this->school->id,
            'nis' => '2025001',
            'nisn' => '0012345678',
            'full_name' => 'Budi Pratama',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2012-05-15',
            'religion' => 'Kristen',
            'address' => 'Jl. Merpati No. 10',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);

        // Assign student to classroom
        $this->student->classrooms()->attach($this->classroom->id, [
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        // Create admin user (for auth testing)
        $this->adminUser = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.test',
            'password' => bcrypt('password'),
            'role' => 'superadmin',
        ]);

        // Create orangtua user (for cross-role testing)
        $this->orangtuaUser = User::create([
            'name' => 'Orang Tua Test',
            'email' => 'ortu@test.test',
            'password' => bcrypt('password'),
            'role' => 'orang_tua',
        ]);
    }

    // ===========================
    // AUTHENTICATION & ACCESS
    // ===========================

    public function test_siswa_can_access_dashboard()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('siswa.dashboard');
        $response->assertSee('Budi');
    }

    public function test_unauthenticated_user_cannot_access_siswa_dashboard()
    {
        $response = $this->get('/siswa/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_cannot_access_siswa_dashboard()
    {
        $response = $this->actingAs($this->adminUser)->get('/siswa/dashboard');
        $response->assertStatus(403);
    }

    public function test_orangtua_cannot_access_siswa_dashboard()
    {
        $response = $this->actingAs($this->orangtuaUser)->get('/siswa/dashboard');
        $response->assertStatus(403);
    }

    // ===========================
    // DASHBOARD
    // ===========================

    public function test_dashboard_shows_student_info()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Budi');
        $response->assertSee('VII-A');
    }

    public function test_dashboard_shows_average_score()
    {
        // Create grades
        $subject = Subject::create(['name' => 'Matematika', 'code' => 'MTK', 'school_id' => $this->school->id]);
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $subject->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'score' => 85,
            'grade_type' => 'ulangan_harian',
        ]);
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $subject->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'score' => 90,
            'grade_type' => 'uts',
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/dashboard');
        $response->assertStatus(200);
        // Should show average ~87.5
        $response->assertSee('87.5');
    }

    public function test_dashboard_shows_outstanding_bills()
    {
        $paymentType = PaymentType::create(['school_id' => $this->school->id, 'type_code' => 'SPP', 'type_name' => 'SPP Bulanan', 'amount' => 500000, 'is_recurring' => true, 'is_active' => true]);
        StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 500000,
            'paid_amount' => 0,
            'status' => 'belum_bayar',
            'year' => 2025,
            'month' => 7,
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/dashboard');
        $response->assertStatus(200);
        $response->assertSee('500k');
    }

    // ===========================
    // JADWAL
    // ===========================

    public function test_siswa_can_access_jadwal()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/jadwal');
        $response->assertStatus(200);
        $response->assertViewIs('siswa.jadwal');
    }

    public function test_jadwal_shows_schedule()
    {
        $subject = Subject::create(['name' => 'Bahasa Indonesia', 'code' => 'BIN', 'school_id' => $this->school->id]);
        $teacherUser = User::create(['name' => 'Guru Test', 'email' => 'guru@test.test', 'password' => bcrypt('password'), 'role' => 'guru']);
        $employee = Employee::create(['school_id' => $this->school->id, 'user_id' => $teacherUser->id, 'employee_code' => 'EMP001', 'full_name' => 'Ibu Sri', 'gender' => 'P', 'employee_type' => 'guru', 'employment_status' => 'yayasan', 'tmt_date' => '2024-01-15', 'is_active' => true]);
        $teacher = Teacher::create(['employee_id' => $employee->id, 'user_id' => $teacherUser->id, 'school_id' => $this->school->id, 'teacher_code' => 'GR001', 'full_name' => 'Ibu Sri', 'gender' => 'P']);

        $timeSlot = TimeSlot::create([
            'school_id' => $this->school->id,
            'day_of_week' => 'monday',
            'slot_name' => 'Les 1',
            'slot_type' => 'lesson',
            'slot_order' => 1,
            'start_time' => '07:30:00',
            'end_time' => '09:00:00',
            'duration_minutes' => 90,
            'is_teaching_slot' => true,
            'is_active' => true,
        ]);

        Schedule::create([
            'classroom_id' => $this->classroom->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'semester_id' => $this->semester->id,
            'day_of_week' => 'monday',
            'time_slot_id' => $timeSlot->id,
            'start_time' => '07:30',
            'end_time' => '09:00',
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/jadwal');
        $response->assertStatus(200);
        $response->assertSee('Bahasa Indonesia');
        $response->assertSee('Senin');
    }

    // ===========================
    // NILAI
    // ===========================

    public function test_siswa_can_access_nilai()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/nilai');
        $response->assertStatus(200);
        $response->assertViewIs('siswa.nilai');
    }

    public function test_nilai_shows_grades_by_subject()
    {
        $subject = Subject::create(['name' => 'IPA', 'code' => 'IPA', 'school_id' => $this->school->id]);
        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $subject->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'score' => 78,
            'grade_type' => 'ulangan_harian',
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/nilai');
        $response->assertStatus(200);
        $response->assertSee('IPA');
        $response->assertSee('78');
    }

    public function test_nilai_can_filter_by_semester()
    {
        $semester2 = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'semester_number' => 2,
            'semester_name' => 'Semester Genap 2025/2026',
            'start_date' => '2026-01-05',
            'end_date' => '2026-06-30',
            'is_active' => false,
        ]);
        $subject = Subject::create(['name' => 'IPS', 'code' => 'IPS', 'school_id' => $this->school->id]);

        Grade::create([
            'student_id' => $this->student->id,
            'subject_id' => $subject->id,
            'semester_id' => $semester2->id,
            'academic_year_id' => $this->academicYear->id,
            'score' => 92,
            'grade_type' => 'uas',
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/nilai?semester_id=' . $semester2->id);
        $response->assertStatus(200);
        $response->assertSee('92');
    }

    // ===========================
    // TAGIHAN
    // ===========================

    public function test_siswa_can_access_tagihan()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/tagihan');
        $response->assertStatus(200);
        $response->assertViewIs('siswa.tagihan');
    }

    public function test_tagihan_shows_bills()
    {
        $paymentType = PaymentType::create(['school_id' => $this->school->id, 'type_code' => 'SPP', 'type_name' => 'SPP Bulanan', 'amount' => 750000, 'is_recurring' => true, 'is_active' => true]);
        StudentBill::create([
            'student_id' => $this->student->id,
            'payment_type_id' => $paymentType->id,
            'academic_year_id' => $this->academicYear->id,
            'amount' => 750000,
            'paid_amount' => 250000,
            'status' => 'cicilan',
            'year' => 2025,
            'month' => 8,
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/tagihan');
        $response->assertStatus(200);
        $response->assertSee('750.000');
        $response->assertSee('250.000');
    }

    // ===========================
    // ABSENSI
    // ===========================

    public function test_siswa_can_access_absensi()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/absensi');
        $response->assertStatus(200);
        $response->assertViewIs('siswa.absensi');
    }

    public function test_absensi_shows_summary()
    {
        Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => '2025-07-15',
            'status' => 'hadir',
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => '2025-07-16',
            'status' => 'sakit',
        ]);
        Attendance::create([
            'student_id' => $this->student->id,
            'classroom_id' => $this->classroom->id,
            'date' => '2025-07-17',
            'status' => 'hadir',
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/absensi');
        $response->assertStatus(200);
        $response->assertViewHas('summary', function ($summary) {
            return $summary['present'] === 2
                && $summary['sick'] === 1;
        });
    }

    // ===========================
    // PROFIL
    // ===========================

    public function test_siswa_can_access_profil()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/profil');
        $response->assertStatus(200);
        $response->assertViewIs('siswa.profil');
        $response->assertSee('Budi Pratama');
        $response->assertSee('Gunungsitoli');
    }

    // ===========================
    // CROSS-ROLE ACCESS CHECKS
    // ===========================

    public function test_admin_cannot_access_any_siswa_route()
    {
        $routes = ['/siswa/dashboard', '/siswa/jadwal', '/siswa/nilai', '/siswa/tagihan', '/siswa/absensi', '/siswa/profil'];
        foreach ($routes as $route) {
            $response = $this->actingAs($this->adminUser)->get($route);
            $response->assertStatus(403, "Admin should not access {$route}");
        }
    }

    public function test_orangtua_cannot_access_any_siswa_route()
    {
        $routes = ['/siswa/dashboard', '/siswa/jadwal', '/siswa/nilai', '/siswa/tagihan', '/siswa/absensi', '/siswa/profil'];
        foreach ($routes as $route) {
            $response = $this->actingAs($this->orangtuaUser)->get($route);
            $response->assertStatus(403, "Orang Tua should not access {$route}");
        }
    }

    // ===========================
    // EDGE CASES
    // ===========================

    public function test_dashboard_handles_no_classroom()
    {
        // Detach from classroom
        $this->student->classrooms()->detach();

        $response = $this->actingAs($this->siswaUser)->get('/siswa/dashboard');
        $response->assertStatus(200);
    }

    public function test_jadwal_handles_no_classroom()
    {
        $this->student->classrooms()->detach();

        $response = $this->actingAs($this->siswaUser)->get('/siswa/jadwal');
        $response->assertStatus(200);
    }

    public function test_nilai_handles_empty_grades()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/nilai');
        $response->assertStatus(200);
        $response->assertViewHas('subjectGrades', function ($sg) {
            return $sg->count() === 0;
        });
    }

    public function test_tagihan_handles_no_bills()
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/tagihan');
        $response->assertStatus(200);
        $response->assertViewHas('totalTagihan', 0);
    }

    public function test_siswa_can_print_own_published_report_card()
    {
        // Enable report card visibility
        \App\Models\Setting::setValue('show_report_card', true, 'boolean', 'raport');

        // Create a published report card for this student
        $reportCard = ReportCard::create([
            'student_id' => $this->student->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'average_score' => 85,
            'predicate' => 'B',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->get("/siswa/raport/{$reportCard->id}/print");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_siswa_cannot_print_report_card_when_disabled_by_admin()
    {
        // Disable report card visibility
        \App\Models\Setting::setValue('show_report_card', false, 'boolean', 'raport');

        // Create a published report card for this student
        $reportCard = ReportCard::create([
            'student_id' => $this->student->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'average_score' => 85,
            'predicate' => 'B',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->get("/siswa/raport/{$reportCard->id}/print");

        $response->assertStatus(403);
    }

    public function test_siswa_cannot_print_other_student_report_card()
    {
        // Create another student and their published report card
        $otherUser = User::create([
            'name' => 'Other Student',
            'email' => 'other@student.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
        ]);
        $otherStudent = Student::create([
            'user_id' => $otherUser->id,
            'school_id' => $this->school->id,
            'nis' => '2025002',
            'nisn' => '0012345679',
            'full_name' => 'Other Student',
            'gender' => 'L',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);
        $reportCard = ReportCard::create([
            'student_id' => $otherStudent->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'average_score' => 85,
            'predicate' => 'B',
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->get("/siswa/raport/{$reportCard->id}/print");

        $response->assertStatus(403);
    }

    public function test_siswa_cannot_print_own_draft_report_card()
    {
        // Create a draft report card for this student
        $reportCard = ReportCard::create([
            'student_id' => $this->student->id,
            'semester_id' => $this->semester->id,
            'academic_year_id' => $this->academicYear->id,
            'classroom_id' => $this->classroom->id,
            'average_score' => 85,
            'predicate' => 'B',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->siswaUser)
            ->get("/siswa/raport/{$reportCard->id}/print");

        $response->assertStatus(403);
    }

    public function test_grade_predicate_converts_dynamically_using_kkm()
    {
        // By default, the migration sets grade X (10) to use 'kkm_interval'
        // Let's assert scoreToPredicate uses dynamic KKM calculation.
        // For KKM = 75, interval = (100-75)/3 = 8.33
        // A >= 91.67 (92), B >= 83.33 (83), C >= 75
        $this->assertEquals('A', FinalGrade::scoreToPredicate(92, 75, 10));
        $this->assertEquals('B', FinalGrade::scoreToPredicate(85, 75, 10));
        $this->assertEquals('C', FinalGrade::scoreToPredicate(77, 75, 10));
        $this->assertEquals('D', FinalGrade::scoreToPredicate(70, 75, 10));
    }

    public function test_grade_predicate_converts_using_static_thresholds()
    {
        // Set setting for grade 10 to use static thresholds: A >= 95, B >= 85, C >= 75
        $rules = [
            '7' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
            '8' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
            '9' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
            '10' => ['mode' => 'static', 'static_a' => 95, 'static_b' => 85, 'static_c' => 75],
            '11' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
            '12' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
        ];
        
        Setting::setValue('raport_grade_conversion', $rules, 'json', 'raport');

        // Assert scoreToPredicate respects static thresholds for grade 10
        $this->assertEquals('A', FinalGrade::scoreToPredicate(96, 75, 10));
        $this->assertEquals('B', FinalGrade::scoreToPredicate(91, 75, 10)); // 91 is A under dynamic KKM, but B under static
        $this->assertEquals('B', FinalGrade::scoreToPredicate(86, 75, 10));
        $this->assertEquals('C', FinalGrade::scoreToPredicate(80, 75, 10));
        $this->assertEquals('D', FinalGrade::scoreToPredicate(70, 75, 10));
    }
}
