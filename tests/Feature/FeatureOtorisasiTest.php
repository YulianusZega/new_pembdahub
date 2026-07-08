<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\Grade;
use App\Models\ReportCard;
use App\Models\Semester;
use App\Models\Setting;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureOtorisasiTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser()
    {
        return User::factory()->create([
            'role' => 'superadmin',
            'is_active' => true,
        ]);
    }

    private function createStudentUser()
    {
        $school = \App\Models\School::factory()->create(['type' => 'SMP']);
        
        $user = User::factory()->create([
            'role' => 'siswa',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $student = Student::factory()->create([
            'user_id' => $user->id,
            'school_id' => $school->id,
        ]);

        return [$user, $student];
    }

    private function createTeacherUser()
    {
        $school = \App\Models\School::factory()->create(['type' => 'SMP']);
        
        $user = User::factory()->create([
            'role' => 'guru',
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $employee = Employee::factory()->create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        $teacher = Teacher::factory()->create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'is_active' => true,
        ]);

        return [$user, $teacher, $employee];
    }

    /**
     * Test admin can view and update feature settings
     */
    public function test_admin_can_manage_feature_settings()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)->get('/admin/settings/features');
        $response->assertStatus(200);
        $response->assertSee('Pusat Otorisasi Fitur');

        $response = $this->actingAs($admin)->put('/admin/settings/features', [
            'show_report_card' => '0',
            'siswa_view_attendance_recap' => '1',
            'siswa_view_reputation_leaderboard' => '0',
        ]);
        $response->assertStatus(302);
        
        $this->assertFalse(Setting::getValue('show_report_card', true));
        $this->assertTrue(Setting::getValue('siswa_view_attendance_recap', true));
        $this->assertFalse(Setting::getValue('siswa_view_reputation_leaderboard', true));
    }

    /**
     * Test student visibility toggles
     */
    public function test_student_feature_toggles()
    {
        list($studentUser, $student) = $this->createStudentUser();

        // 1. Test Attendance Recap
        Setting::setValue('siswa_view_attendance_recap', true, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/siswa/absensi');
        $response->assertStatus(200);

        Setting::setValue('siswa_view_attendance_recap', false, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/siswa/absensi');
        $response->assertStatus(403);

        // 2. Test Leaderboard
        Setting::setValue('siswa_view_reputation_leaderboard', true, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/hall-of-fame');
        $response->assertStatus(200);

        Setting::setValue('siswa_view_reputation_leaderboard', false, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/hall-of-fame');
        $response->assertStatus(403);

        // 3. Test CBT
        Setting::setValue('siswa_access_cbt', true, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/siswa/cbt');
        $response->assertStatus(200);

        Setting::setValue('siswa_access_cbt', false, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/siswa/cbt');
        $response->assertStatus(403);

        // 4. Test LMS
        Setting::setValue('siswa_access_lms', true, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/siswa/lms');
        $response->assertStatus(200);

        Setting::setValue('siswa_access_lms', false, 'boolean', 'features');
        $response = $this->actingAs($studentUser)->get('/siswa/lms');
        $response->assertStatus(403);
    }

    /**
     * Test teacher visibility toggles
     */
    public function test_teacher_feature_toggles()
    {
        list($teacherUser, $teacher, $employee) = $this->createTeacherUser();

        // 1. Test Leaderboard
        Setting::setValue('guru_view_reputation_leaderboard', true, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/hall-of-fame');
        $response->assertStatus(200);

        Setting::setValue('guru_view_reputation_leaderboard', false, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/hall-of-fame');
        $response->assertStatus(403);

        // 2. Test CBT
        Setting::setValue('guru_access_cbt', true, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/guru/cbt/exams');
        $response->assertStatus(200);

        Setting::setValue('guru_access_cbt', false, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/guru/cbt/exams');
        $response->assertStatus(403);

        // 3. Test LMS
        Setting::setValue('guru_access_lms', true, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/guru/lms');
        $response->assertStatus(200);

        Setting::setValue('guru_access_lms', false, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/guru/lms');
        $response->assertStatus(403);

        // 4. Test Cuti Mandiri
        Setting::setValue('pegawai_can_request_leave', true, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/guru/leaves');
        $response->assertStatus(200);

        Setting::setValue('pegawai_can_request_leave', false, 'boolean', 'features');
        $response = $this->actingAs($teacherUser)->get('/guru/leaves');
        $response->assertStatus(403);
    }

    /**
     * Test grade editing limits based on finalized report card and toggle
     */
    public function test_grade_editing_limits()
    {
        list($teacherUser, $teacher, $employee) = $this->createTeacherUser();
        $schoolId = $teacher->school_id;

        $academicYear = AcademicYear::factory()->create(['is_active' => true]);
        $semester = Semester::factory()->create(['academic_year_id' => $academicYear->id, 'is_active' => true]);
        
        $classroom = Classroom::factory()->create([
            'school_id' => $schoolId,
            'academic_year_id' => $academicYear->id,
            'homeroom_teacher_id' => $teacher->id,
            'is_active' => true,
        ]);

        $student = Student::factory()->create([
            'school_id' => $schoolId,
        ]);

        StudentClass::create([
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'aktif',
        ]);

        $subject = Subject::factory()->create(['school_id' => $schoolId]);

        // Assign teacher to teach this subject in this classroom
        \App\Models\TeachingAssignment::create([
            'teacher_id' => $teacher->id,
            'classroom_id' => $classroom->id,
            'subject_id' => $subject->id,
            'academic_year_id' => $academicYear->id,
            'is_active' => true,
        ]);

        // Create a finalized report card for student
        $reportCard = ReportCard::create([
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
            'semester_id' => $semester->id,
            'academic_year_id' => $academicYear->id,
            'status' => 'finalized',
        ]);

        // Create a grade record
        $grade = Grade::create([
            'student_id' => $student->id,
            'subject_id' => $subject->id,
            'teacher_id' => $teacher->id,
            'semester_id' => $semester->id,
            'grade_type' => 'tugas',
            'score' => 80.0,
            'notes' => 'Tugas 1',
        ]);

        // Scenario 1: guru_can_edit_grades is false -> should fail to update/delete/create bulk
        Setting::setValue('guru_can_edit_grades', false, 'boolean', 'features');

        // Try update
        $response = $this->actingAs($teacherUser)->put("/guru/nilai/{$grade->id}", [
            'score' => 95,
            'notes' => 'Update test',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertEquals(80.0, $grade->fresh()->score);

        // Try store bulk
        $response = $this->actingAs($teacherUser)->post('/guru/nilai/store-bulk', [
            'classroom_id' => $classroom->id,
            'subject_id' => $subject->id,
            'grade_type' => 'tugas',
            'semester_id' => $semester->id,
            'component_name' => 'Tugas 2',
            'scores' => [$student->id => 90],
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertFalse(Grade::where('notes', 'Tugas 2')->exists());

        // Try destroy
        $response = $this->actingAs($teacherUser)->delete("/guru/nilai/{$grade->id}");
        $response->assertStatus(302);
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('grades', ['id' => $grade->id]);

        // Scenario 2: guru_can_edit_grades is true -> should succeed
        Setting::setValue('guru_can_edit_grades', true, 'boolean', 'features');

        // Try update
        $response = $this->actingAs($teacherUser)->put("/guru/nilai/{$grade->id}", [
            'score' => 95,
            'notes' => 'Update test',
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertEquals(95.0, $grade->fresh()->score);

        // Try store bulk
        $response = $this->actingAs($teacherUser)->post('/guru/nilai/store-bulk', [
            'classroom_id' => $classroom->id,
            'subject_id' => $subject->id,
            'grade_type' => 'tugas',
            'semester_id' => $semester->id,
            'component_name' => 'Tugas 2',
            'scores' => [$student->id => 90],
        ]);
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertTrue(Grade::where('notes', 'Tugas 2')->exists());

        // Try destroy
        $response = $this->actingAs($teacherUser)->delete("/guru/nilai/{$grade->id}");
        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('grades', ['id' => $grade->id]);
    }

    /**
     * Test user can view and update their profile settings
     */
    public function test_user_can_view_and_update_profile_settings()
    {
        list($studentUser, $student) = $this->createStudentUser();

        // 1. Can view profile settings page
        $response = $this->actingAs($studentUser)->get('/profile/settings');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Profil & Keamanan');

        // 2. Can update username and email
        $response = $this->actingAs($studentUser)->put('/profile/settings', [
            'username' => 'new_student_username',
            'email' => 'new_student_email@example.com',
        ]);
        $response->assertRedirect('/profile/settings');
        $response->assertSessionHas('success');

        $this->assertEquals('new_student_username', $studentUser->fresh()->username);
        $this->assertEquals('new_student_email@example.com', $studentUser->fresh()->email);

        // 3. Can change password
        $response = $this->actingAs($studentUser)->put('/profile/settings', [
            'username' => 'new_student_username',
            'email' => 'new_student_email@example.com',
            'current_password' => 'password',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);
        $response->assertRedirect('/profile/settings');
        $response->assertSessionHas('success');

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('newpassword123', $studentUser->fresh()->password));
    }

    /**
     * Test WhatsApp automatic notification settings toggles
     */
    public function test_whatsapp_notification_settings_toggles()
    {
        $school = \App\Models\School::factory()->create(['type' => 'SMP']);
        $academicYear = AcademicYear::factory()->create(['is_active' => true]);
        
        $applicant = \App\Models\Applicant::create([
            'school_id' => $school->id,
            'academic_year_id' => $academicYear->id,
            'nisn' => '1234567890',
            'full_name' => 'Calon Siswa Baru',
            'gender' => 'L',
            'birth_place' => 'Jakarta',
            'birth_date' => '2010-01-01',
            'religion' => 'Kristen',
            'address' => 'Jl. Test',
            'father_name' => 'Test',
            'mother_name' => 'Test',
            'previous_school' => 'SD Test',
            'registration_number' => 'PSB-SMP-2026-0001',
            'phone' => '081234567890',
            'email' => 'calon@example.com',
            'admission_path' => 'reguler',
            'status' => 'submitted',
        ]);

        $notificationService = app(\App\Services\NotificationService::class);

        // 1. When setting is false, it should block registration notification
        Setting::setValue('wa_send_psb_registration', false, 'boolean', 'features');
        $result = $notificationService->sendPSBRegistration($applicant);
        
        $this->assertArrayHasKey('whatsapp', $result);
        $this->assertFalse($result['whatsapp']['success']);
        $this->assertEquals('WhatsApp notifikasi pendaftaran PSB dinonaktifkan.', $result['whatsapp']['message']);

        // 2. Test payment reminder toggle
        $student = Student::factory()->create(['school_id' => $school->id, 'phone' => '081234567890']);
        Setting::setValue('wa_send_payment_reminder', false, 'boolean', 'features');
        $resultPayment = $notificationService->sendPaymentReminder($student, [
            'bill_type' => 'SPP',
            'amount' => 200000,
            'due_date' => '2026-07-10',
        ]);
        $this->assertFalse($resultPayment['success']);
        $this->assertEquals('WhatsApp notifikasi tagihan dinonaktifkan.', $resultPayment['message']);

        // 3. Test LMS notification toggle
        Setting::setValue('wa_send_lms_notification', false, 'boolean', 'features');
        $course = new \App\Models\LmsCourse();
        $resultLms = $notificationService->sendLmsNotification($course, 'lms.material.published');
        $this->assertFalse($resultLms['success']);
        $this->assertEquals('WhatsApp notifikasi LMS dinonaktifkan.', $resultLms['message']);
    }
}
