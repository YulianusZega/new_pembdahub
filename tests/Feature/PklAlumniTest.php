<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\User;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\PklPlacement;
use App\Models\PklLog;
use App\Models\PklGrade;
use App\Models\AlumniProfile;
use App\Models\TracerStudy;
use App\Models\JobPosting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class PklAlumniTest extends TestCase
{
    use RefreshDatabase;

    protected $studentUser;
    protected $student;
    protected $teacherUser;
    protected $teacher;
    protected $academicYear;
    protected $smk;

    public function setUp(): void
    {
        parent::setUp();
        // Seed base data
        $this->artisan('db:seed', ['--class' => 'DatabaseSeeder']);

        // Fetch active academic year and SMK school
        $this->academicYear = AcademicYear::where('is_active', true)->first();
        $this->smk = School::where('type', 'SMK')->first();

        // Ensure we have a teacher from SMK
        $this->teacher = Teacher::where('school_id', $this->smk->id)->first();
        if (!$this->teacher) {
            // Find any teacher or create one
            $this->teacher = Teacher::first();
        }
        $this->teacherUser = $this->teacher->user;
        $this->teacherUser->update(['must_change_password' => false]);

        // Find or create student
        $this->student = Student::where('school_id', $this->smk->id)->first();
        if (!$this->student) {
            $this->student = Student::first();
        }
        $this->studentUser = $this->student->user;
        $this->studentUser->update(['must_change_password' => false]);
    }

    public function test_student_can_view_pkl_dashboard()
    {
        // Create placement
        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => Str::random(32),
        ]);

        $response = $this->actingAs($this->studentUser)
            ->get(route('siswa.pkl.index'));

        $response->assertStatus(200);
        $response->assertViewIs('siswa.pkl.index');
        $response->assertSee('PT. Nias Digital');
    }

    public function test_student_can_submit_daily_log()
    {
        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => Str::random(32),
        ]);

        $logData = [
            'log_date' => date('Y-m-d'),
            'activity' => 'Melakukan troubleshooting jaringan router Mikrotik di ruang server.',
            'latitude' => 1.282928,
            'longitude' => 97.619028,
        ];

        $response = $this->actingAs($this->studentUser)
            ->post(route('siswa.pkl.log.store'), $logData);

        $response->assertRedirect(route('siswa.pkl.index'));
        $this->assertDatabaseHas('pkl_logs', [
            'pkl_placement_id' => $placement->id,
            'activity' => $logData['activity'],
            'latitude' => $logData['latitude'],
            'longitude' => $logData['longitude'],
            'status' => 'submitted',
        ]);

        $log = PklLog::where('pkl_placement_id', $placement->id)->first();
        $this->assertEquals($logData['log_date'], $log->log_date->format('Y-m-d'));
    }

    public function test_mentor_can_access_portal_via_signed_token()
    {
        $token = Str::random(32);
        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => $token,
        ]);

        $response = $this->get(route('mentor.pkl.portal', $token));

        $response->assertStatus(200);
        $response->assertViewIs('mentor.pkl_portal');
        $response->assertSee($this->student->full_name);
    }

    public function test_mentor_can_approve_daily_log()
    {
        $token = Str::random(32);
        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => $token,
        ]);

        $log = PklLog::create([
            'pkl_placement_id' => $placement->id,
            'log_date' => date('Y-m-d'),
            'activity' => 'Melakukan pemeliharaan server.',
            'status' => 'submitted',
        ]);

        $response = $this->post(route('mentor.pkl.log.approve', [$token, $log->id]));

        $response->assertRedirect(route('mentor.pkl.portal', $token));
        $this->assertDatabaseHas('pkl_logs', [
            'id' => $log->id,
            'status' => 'approved',
        ]);
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->studentUser->id,
            'points' => 10,
            'category' => 'pkl',
        ]);
    }

    public function test_mentor_can_reject_daily_log_with_notes()
    {
        $token = Str::random(32);
        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => $token,
        ]);

        $log = PklLog::create([
            'pkl_placement_id' => $placement->id,
            'log_date' => date('Y-m-d'),
            'activity' => 'Merapikan inventaris.',
            'status' => 'submitted',
        ]);

        // First approve to generate reputation log
        $this->post(route('mentor.pkl.log.approve', [$token, $log->id]));
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->studentUser->id,
            'points' => 10,
            'category' => 'pkl',
        ]);

        // Now reject
        $response = $this->post(route('mentor.pkl.log.reject', [$token, $log->id]), [
            'mentor_notes' => 'Harap perjelas kegiatan yang dilakukan.',
        ]);

        $response->assertRedirect(route('mentor.pkl.portal', $token));
        $this->assertDatabaseHas('pkl_logs', [
            'id' => $log->id,
            'status' => 'rejected',
            'mentor_notes' => 'Harap perjelas kegiatan yang dilakukan.',
        ]);
        // Assert that the reputation log is deleted/rolled back
        $this->assertDatabaseMissing('reputation_logs', [
            'user_id' => $this->studentUser->id,
            'points' => 10,
            'category' => 'pkl',
        ]);
    }

    public function test_mentor_can_submit_grades()
    {
        $token = Str::random(32);
        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => $token,
        ]);

        $gradeData = [
            'score_discipline' => 90,
            'score_teamwork' => 85,
            'score_technical' => 95,
            'score_safety' => 80,
            'notes' => 'Kinerja siswa sangat baik dan disiplin tinggi.',
        ];

        $response = $this->post(route('mentor.pkl.grade.store', $token), $gradeData);

        $response->assertRedirect(route('mentor.pkl.portal', $token));
        $this->assertDatabaseHas('pkl_grades', [
            'pkl_placement_id' => $placement->id,
            'score_discipline' => 90,
            'score_teamwork' => 85,
            'score_technical' => 95,
            'score_safety' => 80,
            'score_average' => 87.5,
            'notes' => 'Kinerja siswa sangat baik dan disiplin tinggi.',
        ]);

        // Assert student reputation log for completion
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->studentUser->id,
            'points' => 100,
            'category' => 'pkl_completed',
        ]);

        // Assert teacher reputation log for supervising completion
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->teacherUser->id,
            'points' => 50,
            'category' => 'pkl_monitoring',
        ]);
    }

    public function test_teacher_can_view_assigned_pkl_students()
    {
        // Assign placement
        PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => Str::random(32),
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->get(route('guru.pkl.index'));

        $response->assertStatus(200);
        $response->assertViewIs('guru.pkl.index');
        $response->assertSee('PT. Nias Digital');
    }

    public function test_teacher_can_view_specific_student_logs()
    {
        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => Str::random(32),
        ]);

        $response = $this->actingAs($this->teacherUser)
            ->get(route('guru.pkl.show', $placement->id));

        $response->assertStatus(200);
        $response->assertViewIs('guru.pkl.show');
        $response->assertSee($this->student->full_name);
    }

    public function test_alumni_can_submit_tracer_study()
    {
        $this->student->update(['status' => 'alumni']);

        $alumniProfile = AlumniProfile::create([
            'student_id' => $this->student->id,
            'school_id' => $this->smk->id,
            'full_name' => $this->student->full_name,
            'graduation_year' => 2025,
            'email' => $this->studentUser->email,
        ]);

        $tracerData = [
            'employment_status' => 'kuliah',
            'university_name' => 'Universitas Sumatera Utara',
            'major' => 'Sains Data',
            'feedback_for_school' => 'Fasilitas komputer lab mohon ditingkatkan.',
        ];

        $response = $this->actingAs($this->studentUser)
            ->post(route('alumni.tracer.submit'), $tracerData);

        $response->assertRedirect(route('alumni.tracer.form'));
        $this->assertDatabaseHas('tracer_studies', [
            'alumni_profile_id' => $alumniProfile->id,
            'employment_status' => 'kuliah',
            'university_name' => 'Universitas Sumatera Utara',
            'major' => 'Sains Data',
            'feedback_for_school' => 'Fasilitas komputer lab mohon ditingkatkan.',
        ]);

        // Assert alumni student reputation log for Tracer Study
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->studentUser->id,
            'points' => 50,
            'category' => 'alumni_tracer',
        ]);
    }

    public function test_alumni_can_view_job_board()
    {
        JobPosting::create([
            'company_name' => 'CV. Jaya Raya',
            'title' => 'Staf Administrasi',
            'description' => 'Membantu pengelolaan administrasi kantor.',
            'requirements' => 'Lulusan SMK Jurusan Akuntansi/Perkantoran.',
            'contact_email' => 'hrd@jayaraya.id',
            'is_active' => true,
            'created_by' => $this->studentUser->id,
        ]);

        $response = $this->actingAs($this->studentUser)
            ->get(route('alumni.jobs.index'));

        $response->assertStatus(200);
        $response->assertViewIs('alumni.jobs');
        $response->assertSee('CV. Jaya Raya');
        $response->assertSee('Staf Administrasi');
    }

    public function test_admin_can_view_placements_index()
    {
        $admin = User::where('role', 'admin_sekolah')->where('school_id', $this->smk->id)->first();
        $admin->update(['must_change_password' => false]);

        $response = $this->actingAs($admin)
            ->get(route('admin.pkl-alumni.placements.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pkl_alumni.placements.index');
    }

    public function test_admin_can_create_placement()
    {
        $admin = User::where('role', 'admin_sekolah')->where('school_id', $this->smk->id)->first();
        $admin->update(['must_change_password' => false]);

        $placementData = [
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital Baru',
            'company_address' => 'Jl. Baru No. 1, Gunungsitoli',
            'mentor_name' => 'Jane Doe',
            'mentor_phone' => '081299887766',
            'start_date' => '2026-02-01',
            'end_date' => '2026-07-31',
            'teacher_id' => $this->teacher->id,
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.pkl-alumni.placements.store'), $placementData);

        $response->assertRedirect(route('admin.pkl-alumni.placements.index'));
        $this->assertDatabaseHas('pkl_placements', [
            'student_id' => $this->student->id,
            'company_name' => 'PT. Nias Digital Baru',
            'mentor_name' => 'Jane Doe',
        ]);
    }

    public function test_admin_can_view_placement_show()
    {
        $admin = User::where('role', 'admin_sekolah')->where('school_id', $this->smk->id)->first();
        $admin->update(['must_change_password' => false]);

        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => Str::random(32),
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.pkl-alumni.placements.show', $placement->id));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pkl_alumni.placements.show');
        $response->assertSee('PT. Nias Digital');
    }

    public function test_admin_can_update_placement()
    {
        $admin = User::where('role', 'admin_sekolah')->where('school_id', $this->smk->id)->first();
        $admin->update(['must_change_password' => false]);

        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => Str::random(32),
        ]);

        $updateData = [
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital Diupdate',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'completed',
        ];

        $response = $this->actingAs($admin)
            ->put(route('admin.pkl-alumni.placements.update', $placement->id), $updateData);

        $response->assertRedirect(route('admin.pkl-alumni.placements.index'));
        $this->assertDatabaseHas('pkl_placements', [
            'id' => $placement->id,
            'company_name' => 'PT. Nias Digital Diupdate',
            'status' => 'completed',
        ]);
    }

    public function test_admin_can_delete_placement()
    {
        $admin = User::where('role', 'admin_sekolah')->where('school_id', $this->smk->id)->first();
        $admin->update(['must_change_password' => false]);

        $placement = PklPlacement::create([
            'student_id' => $this->student->id,
            'academic_year_id' => $this->academicYear->id,
            'company_name' => 'PT. Nias Digital',
            'company_address' => 'Jl. Diponegoro No. 100, Gunungsitoli',
            'mentor_name' => 'John Doe',
            'mentor_phone' => '081234567890',
            'start_date' => '2026-01-01',
            'end_date' => '2026-06-30',
            'teacher_id' => $this->teacher->id,
            'status' => 'active',
            'signed_token' => Str::random(32),
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('admin.pkl-alumni.placements.destroy', $placement->id));

        $response->assertRedirect(route('admin.pkl-alumni.placements.index'));
        $this->assertDatabaseMissing('pkl_placements', [
            'id' => $placement->id,
        ]);
    }

    public function test_admin_can_view_tracer_studies_list()
    {
        $admin = User::where('role', 'admin_sekolah')->where('school_id', $this->smk->id)->first();
        $admin->update(['must_change_password' => false]);

        $response = $this->actingAs($admin)
            ->get(route('admin.pkl-alumni.tracer.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.pkl_alumni.tracer.index');
    }

    public function test_admin_can_create_job_posting()
    {
        $admin = User::where('role', 'admin_sekolah')->where('school_id', $this->smk->id)->first();
        $admin->update(['must_change_password' => false]);

        $jobData = [
            'company_name' => 'CV. Gunungsitoli Tech',
            'title' => 'Junior Web Developer',
            'description' => 'Membantu pembuatan aplikasi web berbasis Laravel.',
            'requirements' => 'Menguasai PHP dasar.',
            'contact_email' => 'hrd@gstech.co.id',
            'contact_phone' => '081234567890',
            'salary_range' => 'Rp 2.500.000 - Rp 3.500.000',
        ];

        $response = $this->actingAs($admin)
            ->post(route('admin.pkl-alumni.jobs.store'), $jobData);

        $response->assertRedirect(route('admin.pkl-alumni.jobs.index'));
        $this->assertDatabaseHas('job_postings', [
            'company_name' => 'CV. Gunungsitoli Tech',
            'title' => 'Junior Web Developer',
        ]);
    }

    public function test_non_admin_cannot_access_pkl_alumni_admin_routes()
    {
        $response = $this->actingAs($this->studentUser)
            ->get(route('admin.pkl-alumni.placements.index'));

        $response->assertStatus(403);
    }
}
