<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\LmsAssignment;
use App\Models\LmsClass;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsMaterial;
use App\Models\LmsModule;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAnswer;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\LmsSubmission;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsTest extends TestCase
{
    use RefreshDatabase;

    // ── shared fixtures ──────────────────────────────────
    protected School $school;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected Subject $subject;
    protected Classroom $classroom;

    protected User $guruUser;
    protected Teacher $teacher;

    protected User $siswaUser;
    protected Student $student;

    protected User $otherGuruUser;
    protected Teacher $otherTeacher;

    protected LmsCourse $course;
    protected LmsModule $module;

    protected function setUp(): void
    {
        parent::setUp();

        // ─── School & Academic ───
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
            'year' => '2024/2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'semester_start' => '2024-07-15',
            'semester_end' => '2025-06-15',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'semester_number' => 1,
            'semester_name' => 'Ganjil 2024/2025',
            'start_date' => '2024-07-15',
            'end_date' => '2024-12-15',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'subject_code' => 'MTK',
            'subject_name' => 'Matematika',
            'description' => 'Pelajaran Matematika',
            'kkm' => 75,
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'class_code' => 'VII-A',
            'class_name' => 'VII-A',
            'grade_level' => 7,
            'capacity' => 30,
            'is_active' => true,
        ]);

        // ─── Guru User + Teacher ───
        $this->guruUser = User::create([
            'name' => 'Guru LMS',
            'username' => 'guru_lms',
            'email' => 'guru_lms@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $employee = Employee::create([
            'school_id' => $this->school->id,
            'employee_code' => 'EMP-LMS-001',
            'full_name' => 'Guru LMS',
            'gender' => 'L',
            'employee_type' => 'guru',
            'employment_status' => 'yayasan',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create([
            'employee_id' => $employee->id,
            'user_id' => $this->guruUser->id,
            'school_id' => $this->school->id,
            'teacher_code' => 'GR-LMS-001',
            'full_name' => 'Guru LMS',
            'gender' => 'L',
            'education_level' => 'S1',
            'major' => 'Matematika',
            'religion' => 'Islam',
            'is_active' => true,
        ]);

        // ─── Siswa User + Student ───
        $this->siswaUser = User::create([
            'name' => 'Siswa LMS',
            'username' => 'siswa_lms',
            'email' => 'siswa_lms@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'user_id' => $this->siswaUser->id,
            'school_id' => $this->school->id,
            'nis' => '2025001',
            'nisn' => '0012345678',
            'full_name' => 'Siswa LMS',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2012-05-15',
            'religion' => 'Kristen',
            'address' => 'Jl. Merpati No. 10',
            'entry_year' => 2025,
            'status' => 'aktif',
        ]);

        $this->student->classrooms()->attach($this->classroom->id, [
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        // ─── Other Teacher (for access control tests) ───
        $this->otherGuruUser = User::create([
            'name' => 'Guru Lain',
            'username' => 'guru_lain',
            'email' => 'guru_lain@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $otherEmployee = Employee::create([
            'school_id' => $this->school->id,
            'employee_code' => 'EMP-LMS-002',
            'full_name' => 'Guru Lain',
            'gender' => 'P',
            'employee_type' => 'guru',
            'employment_status' => 'yayasan',
            'tmt_date' => '2020-01-01',
            'is_active' => true,
        ]);

        $this->otherTeacher = Teacher::create([
            'employee_id' => $otherEmployee->id,
            'user_id' => $this->otherGuruUser->id,
            'school_id' => $this->school->id,
            'teacher_code' => 'GR-LMS-002',
            'full_name' => 'Guru Lain',
            'gender' => 'P',
            'education_level' => 'S1',
            'major' => 'Bahasa Indonesia',
            'religion' => 'Kristen',
            'is_active' => true,
        ]);

        // ─── Create a base LMS Course ───
        $this->course = LmsCourse::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'semester_id' => $this->semester->id,
            'code' => 'LMS-TEST0001',
            'course_name' => 'Matematika Dasar',
            'description' => 'Kursus matematika untuk kelas VII',
            'status' => 'active',
            'is_published' => true,
            'is_active' => true,
        ]);

        // Assign classroom and enroll student
        $lmsClass = LmsClass::create([
            'course_id' => $this->course->id,
            'classroom_id' => $this->classroom->id,
            'school_id' => $this->school->id,
            'status' => 'active',
        ]);

        LmsEnrollment::create([
            'lms_class_id' => $lmsClass->id,
            'student_id' => $this->student->id,
            'status' => 'enrolled',
            'enrolled_at' => now(),
        ]);

        $this->module = LmsModule::create([
            'course_id' => $this->course->id,
            'title' => 'Bab 1: Bilangan Bulat',
            'sequence' => 1,
            'is_active' => true,
        ]);
    }

    // ================================================================
    //  GURU – COURSE CRUD
    // ================================================================

    public function test_guru_can_view_lms_index(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/lms');
        $response->assertStatus(200);
        $response->assertSee('Matematika Dasar');
    }

    public function test_guru_can_view_create_course_form(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/lms/create');
        $response->assertStatus(200);
        $response->assertSee('Buat Course');
    }

    public function test_guru_can_store_course(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/lms', [
            'name' => 'Fisika Kelas VII',
            'subject_id' => $this->subject->id,
            'semester_id' => $this->semester->id,
            'description' => 'Course fisika baru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_courses', [
            'course_name' => 'Fisika Kelas VII',
            'teacher_id' => $this->teacher->id,
        ]);
    }

    public function test_guru_store_course_with_classrooms_auto_enrolls_students(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/lms', [
            'name' => 'Matematika Lanjut',
            'subject_id' => $this->subject->id,
            'semester_id' => $this->semester->id,
            'classroom_ids' => [$this->classroom->id],
        ]);

        $response->assertRedirect();
        $newCourse = LmsCourse::where('course_name', 'Matematika Lanjut')->first();
        $this->assertNotNull($newCourse);
        $this->assertDatabaseHas('lms_classes', [
            'course_id' => $newCourse->id,
            'classroom_id' => $this->classroom->id,
        ]);
        // Student should be auto-enrolled
        $lmsClass = LmsClass::where('course_id', $newCourse->id)->first();
        $this->assertDatabaseHas('lms_enrollments', [
            'lms_class_id' => $lmsClass->id,
            'student_id' => $this->student->id,
            'status' => 'enrolled',
        ]);
    }

    public function test_guru_can_view_course_detail(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/lms/' . $this->course->id);
        $response->assertStatus(200);
        $response->assertSee('Matematika Dasar');
    }

    public function test_guru_can_view_edit_form(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/lms/' . $this->course->id . '/edit');
        $response->assertStatus(200);
        $response->assertSee('Matematika Dasar');
    }

    public function test_guru_can_update_course(): void
    {
        $response = $this->actingAs($this->guruUser)->put('/guru/lms/' . $this->course->id, [
            'name' => 'Matematika Dasar Revisi',
            'description' => 'Deskripsi baru',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_courses', [
            'id' => $this->course->id,
            'course_name' => 'Matematika Dasar Revisi',
        ]);
    }

    public function test_guru_can_delete_course(): void
    {
        $response = $this->actingAs($this->guruUser)->delete('/guru/lms/' . $this->course->id);
        $response->assertRedirect('/guru/lms');
        $this->assertSoftDeleted('lms_courses', ['id' => $this->course->id]);
    }

    public function test_guru_cannot_access_other_teachers_course(): void
    {
        // Other teacher tries to access this teacher's course
        $response = $this->actingAs($this->otherGuruUser)->get('/guru/lms/' . $this->course->id);
        $response->assertStatus(403);
    }

    // ================================================================
    //  GURU – MODULE CRUD
    // ================================================================

    public function test_guru_can_add_module(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/lms/' . $this->course->id . '/modules', [
            'title' => 'Bab 1: Bilangan Bulat',
            'description' => 'Pengenalan bilangan bulat',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_modules', [
            'course_id' => $this->course->id,
            'title' => 'Bab 1: Bilangan Bulat',
        ]);
    }

    public function test_guru_can_update_module(): void
    {
        $module = LmsModule::create([
            'course_id' => $this->course->id,
            'title' => 'Old Title',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->put('/guru/lms/modules/' . $module->id, [
            'title' => 'New Title',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_modules', [
            'id' => $module->id,
            'title' => 'New Title',
        ]);
    }

    public function test_guru_can_delete_module(): void
    {
        $module = LmsModule::create([
            'course_id' => $this->course->id,
            'title' => 'Module to Delete',
            'sequence' => 1,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->delete('/guru/lms/modules/' . $module->id);
        $response->assertRedirect();
        $this->assertSoftDeleted('lms_modules', ['id' => $module->id]);
    }

    // ================================================================
    //  GURU – MATERIAL CRUD
    // ================================================================

    public function test_guru_can_add_text_material(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/lms/' . $this->course->id . '/materials', [
            'module_id' => $this->module->id,
            'title' => 'Materi Teks',
            'material_type' => 'text',
            'content' => 'Ini adalah konten materi teks.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_materials', [
            'course_id' => $this->course->id,
            'title' => 'Materi Teks',
            'material_type' => 'text',
        ]);
    }

    public function test_guru_can_add_link_material(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/lms/' . $this->course->id . '/materials', [
            'module_id' => $this->module->id,
            'title' => 'Video YouTube',
            'material_type' => 'link',
            'file_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_materials', [
            'course_id' => $this->course->id,
            'title' => 'Video YouTube',
            'material_type' => 'link',
        ]);
    }

    public function test_guru_can_delete_material(): void
    {
        $material = LmsMaterial::create([
            'course_id' => $this->course->id,
            'title' => 'Delete Me',
            'material_type' => 'text',
            'content' => 'Text',
            'order_number' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->delete('/guru/lms/materials/' . $material->id);
        $response->assertRedirect();
        $this->assertSoftDeleted('lms_materials', ['id' => $material->id]);
    }

    // ================================================================
    //  GURU – ASSIGNMENT CRUD
    // ================================================================

    public function test_guru_can_view_create_assignment_form(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/lms/' . $this->course->id . '/assignments/create');
        $response->assertStatus(200);
        $response->assertSee('Buat Tugas');
    }

    public function test_guru_can_store_assignment(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/lms/' . $this->course->id . '/assignments', [
            'module_id' => $this->module->id,
            'title' => 'Tugas Bab 1',
            'description' => 'Kerjakan soal 1-10',
            'due_date' => now()->addDays(7)->format('Y-m-d H:i:s'),
            'max_score' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_assignments', [
            'course_id' => $this->course->id,
            'title' => 'Tugas Bab 1',
        ]);
    }

    public function test_guru_can_view_assignment_detail(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Assignment Detail',
            'max_score' => 100,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->get('/guru/lms/assignments/' . $assignment->id);
        $response->assertStatus(200);
        $response->assertSee('Assignment Detail');
    }

    public function test_guru_can_update_assignment(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Old Assignment',
            'max_score' => 100,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->put('/guru/lms/assignments/' . $assignment->id, [
            'title' => 'Updated Assignment',
            'description' => 'New instructions',
            'max_score' => 80,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_assignments', [
            'id' => $assignment->id,
            'title' => 'Updated Assignment',
            'max_score' => 80,
        ]);
    }

    public function test_guru_can_delete_assignment(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Delete This',
            'max_score' => 100,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->delete('/guru/lms/assignments/' . $assignment->id);
        $response->assertRedirect();
        $this->assertSoftDeleted('lms_assignments', ['id' => $assignment->id]);
    }

    public function test_guru_can_grade_submission(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Gradeable',
            'max_score' => 100,
            'is_published' => true,
        ]);

        $submission = LmsSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'submission_text' => 'Jawaban saya',
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->guruUser)->post('/guru/lms/submissions/' . $submission->id . '/grade', [
            'score' => 85,
            'feedback' => 'Bagus! Perlu sedikit perbaikan.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_submissions', [
            'id' => $submission->id,
            'score' => 85,
            'status' => 'graded',
            'graded_by' => $this->guruUser->id,
        ]);
    }

    // ================================================================
    //  GURU – QUIZ CRUD
    // ================================================================

    public function test_guru_can_view_create_quiz_form(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/lms/' . $this->course->id . '/quizzes/create');
        $response->assertStatus(200);
        $response->assertSee('Buat Quiz');
    }

    public function test_guru_can_store_quiz(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/lms/' . $this->course->id . '/quizzes', [
            'module_id' => $this->module->id,
            'title' => 'Quiz Bab 1',
            'description' => 'Quiz tentang bilangan bulat',
            'time_limit' => 30,
            'passing_score' => 75,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_quizzes', [
            'course_id' => $this->course->id,
            'title' => 'Quiz Bab 1',
            'passing_score' => 75,
        ]);
    }

    public function test_guru_can_view_quiz_detail(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Quiz Detail',
            'total_score' => 100,
            'passing_score' => 75,
        ]);

        $response = $this->actingAs($this->guruUser)->get('/guru/lms/quizzes/' . $quiz->id);
        $response->assertStatus(200);
        $response->assertSee('Quiz Detail');
    }

    public function test_guru_can_update_quiz(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Old Quiz',
            'total_score' => 100,
            'passing_score' => 75,
        ]);

        $response = $this->actingAs($this->guruUser)->put('/guru/lms/quizzes/' . $quiz->id, [
            'title' => 'Updated Quiz',
            'passing_score' => 60,
            'is_published' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_quizzes', [
            'id' => $quiz->id,
            'title' => 'Updated Quiz',
            'passing_score' => 60,
            'is_published' => true,
        ]);
    }

    public function test_guru_can_publish_quiz(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Publish Me',
            'total_score' => 100,
            'passing_score' => 75,
            'is_published' => false,
        ]);

        $response = $this->actingAs($this->guruUser)->put('/guru/lms/quizzes/' . $quiz->id, [
            'title' => 'Publish Me',
            'passing_score' => 75,
            'is_published' => true,
        ]);

        $response->assertRedirect();
        $this->assertTrue(LmsQuiz::find($quiz->id)->is_published);
    }

    public function test_guru_can_delete_quiz(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Delete Quiz',
            'total_score' => 100,
            'passing_score' => 75,
        ]);

        $response = $this->actingAs($this->guruUser)->delete('/guru/lms/quizzes/' . $quiz->id);
        $response->assertRedirect();
        $this->assertSoftDeleted('lms_quizzes', ['id' => $quiz->id]);
    }

    // ================================================================
    //  GURU – QUESTION CRUD
    // ================================================================

    public function test_guru_can_add_multiple_choice_question(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Quiz MC',
            'total_score' => 0,
            'passing_score' => 75,
        ]);

        $response = $this->actingAs($this->guruUser)->post('/guru/lms/quizzes/' . $quiz->id . '/questions', [
            'question' => 'Berapakah 2 + 3?',
            'question_type' => 'multiple_choice',
            'score' => 10,
            'correct_answer' => 'B',
            'options' => [
                ['key' => 'A', 'text' => '4'],
                ['key' => 'B', 'text' => '5'],
                ['key' => 'C', 'text' => '6'],
                ['key' => 'D', 'text' => '7'],
            ],
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_quiz_questions', [
            'quiz_id' => $quiz->id,
            'question' => 'Berapakah 2 + 3?',
            'question_type' => 'multiple_choice',
            'correct_answer' => 'B',
        ]);
        // Total score should be updated
        $this->assertEquals(10, LmsQuiz::find($quiz->id)->total_score);
    }

    public function test_guru_can_add_true_false_question(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Quiz TF',
            'total_score' => 0,
            'passing_score' => 75,
        ]);

        $response = $this->actingAs($this->guruUser)->post('/guru/lms/quizzes/' . $quiz->id . '/questions', [
            'question' => 'Apakah 2 + 2 = 4?',
            'question_type' => 'true_false',
            'score' => 5,
            'correct_answer' => 'true',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_quiz_questions', [
            'quiz_id' => $quiz->id,
            'question_type' => 'true_false',
            'correct_answer' => 'true',
        ]);
    }

    public function test_guru_can_update_question(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Q',
            'total_score' => 10,
            'passing_score' => 75,
        ]);

        $question = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Old question',
            'question_type' => 'short_answer',
            'score' => 10,
            'order_number' => 1,
        ]);

        $response = $this->actingAs($this->guruUser)->put('/guru/lms/questions/' . $question->id, [
            'question' => 'Updated question',
            'score' => 15,
            'correct_answer' => 'Updated correct answer',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_quiz_questions', [
            'id' => $question->id,
            'question' => 'Updated question',
            'score' => 15,
            'correct_answer' => 'Updated correct answer',
        ]);
    }

    public function test_guru_can_delete_question(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Del Q',
            'total_score' => 10,
            'passing_score' => 75,
        ]);

        $question = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Delete me',
            'question_type' => 'essay',
            'score' => 10,
            'order_number' => 1,
        ]);

        $response = $this->actingAs($this->guruUser)->delete('/guru/lms/questions/' . $question->id);
        $response->assertRedirect();
        $this->assertDatabaseMissing('lms_quiz_questions', ['id' => $question->id]);
        // Total score updated to 0
        $this->assertEquals(0, LmsQuiz::find($quiz->id)->total_score);
    }

    public function test_guru_can_view_quiz_results(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Results Quiz',
            'total_score' => 100,
            'passing_score' => 75,
        ]);

        LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now()->subHour(),
            'finished_at' => now(),
            'score' => 80,
            'is_passed' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->get('/guru/lms/quizzes/' . $quiz->id . '/results');
        $response->assertStatus(200);
        $response->assertSee('Siswa LMS');
    }

    // ================================================================
    //  GURU – ACCESS CONTROL
    // ================================================================

    public function test_other_teacher_cannot_update_course(): void
    {
        $response = $this->actingAs($this->otherGuruUser)->put('/guru/lms/' . $this->course->id, [
            'name' => 'Hacked',
            'description' => 'x',
            'status' => 'active',
        ]);
        $response->assertStatus(403);
    }

    public function test_other_teacher_cannot_add_module(): void
    {
        $response = $this->actingAs($this->otherGuruUser)->post('/guru/lms/' . $this->course->id . '/modules', [
            'title' => 'Hacked Module',
        ]);
        $response->assertStatus(403);
    }

    public function test_other_teacher_cannot_grade_submission(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Secure',
            'max_score' => 100,
            'is_published' => true,
        ]);
        $submission = LmsSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $response = $this->actingAs($this->otherGuruUser)->post('/guru/lms/submissions/' . $submission->id . '/grade', [
            'score' => 100,
        ]);
        $response->assertStatus(403);
    }

    public function test_siswa_cannot_access_guru_lms(): void
    {
        $response = $this->actingAs($this->siswaUser)->get('/guru/lms');
        $response->assertStatus(403);
    }

    // ================================================================
    //  SISWA – LMS FEATURES
    // ================================================================

    public function test_siswa_can_view_lms_index(): void
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/lms');
        $response->assertStatus(200);
        $response->assertSee('Matematika Dasar');
    }

    public function test_siswa_can_view_enrolled_course(): void
    {
        $response = $this->actingAs($this->siswaUser)->get('/siswa/lms/' . $this->course->id);
        $response->assertStatus(200);
        $response->assertSee('Matematika Dasar');
    }

    public function test_siswa_cannot_view_unenrolled_course(): void
    {
        $otherCourse = LmsCourse::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->otherTeacher->id,
            'subject_id' => $this->subject->id,
            'semester_id' => $this->semester->id,
            'code' => 'LMS-OTHER001',
            'course_name' => 'Course Lain',
            'status' => 'active',
            'is_published' => true,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/lms/' . $otherCourse->id);
        $response->assertStatus(403);
    }

    public function test_siswa_can_submit_assignment(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Submit This',
            'max_score' => 100,
            'deadline' => now()->addDays(7),
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->siswaUser)->post('/siswa/lms/assignments/' . $assignment->id . '/submit', [
            'submission_text' => 'Ini jawaban saya',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'status' => 'submitted',
        ]);
    }

    public function test_siswa_late_submission_detected(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Past Due',
            'max_score' => 100,
            'deadline' => now()->subDay(), // Already past due
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->siswaUser)->post('/siswa/lms/assignments/' . $assignment->id . '/submit', [
            'submission_text' => 'Terlambat',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('lms_submissions', [
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'status' => 'late',
        ]);
    }

    public function test_siswa_can_start_quiz(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Available Quiz',
            'total_score' => 10,
            'passing_score' => 75,
            'is_published' => true,
            // No start/end time means always available
        ]);

        LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Test Q?',
            'question_type' => 'true_false',
            'correct_answer' => 'true',
            'score' => 10,
            'order_number' => 1,
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/lms/quizzes/' . $quiz->id . '/start');
        $response->assertStatus(200);
        $response->assertSee('Test Q?');

        // Attempt should be created
        $this->assertDatabaseHas('lms_quiz_attempts', [
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
        ]);
    }

    public function test_siswa_quiz_auto_grading(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Auto Grade Quiz',
            'total_score' => 20,
            'passing_score' => 50,
            'is_published' => true,
        ]);

        $q1 = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => '2 + 2 = 4?',
            'question_type' => 'true_false',
            'correct_answer' => 'true',
            'score' => 10,
            'order_number' => 1,
        ]);

        $q2 = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Pilih jawaban yang benar',
            'question_type' => 'multiple_choice',
            'options' => [
                ['key' => 'A', 'text' => '1'],
                ['key' => 'B', 'text' => '2'],
            ],
            'correct_answer' => 'B',
            'score' => 10,
            'order_number' => 2,
        ]);

        $attempt = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now()->subMinutes(5),
        ]);

        $response = $this->actingAs($this->siswaUser)->post('/siswa/lms/attempts/' . $attempt->id . '/submit', [
            'answers' => [
                $q1->id => 'true',   // correct
                $q2->id => 'A',       // wrong
            ],
        ]);

        $response->assertRedirect();

        $attempt->refresh();
        $this->assertNotNull($attempt->finished_at);
        $this->assertEquals(50, $attempt->score); // 10/20 = 50%
        $this->assertTrue($attempt->is_passed);   // 50% >= 50% passing

        // Check individual answers
        $this->assertDatabaseHas('lms_quiz_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $q1->id,
            'is_correct' => true,
        ]);
        $this->assertDatabaseHas('lms_quiz_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $q2->id,
            'is_correct' => false,
        ]);
    }

    public function test_siswa_cannot_resubmit_finished_quiz(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Finished Quiz',
            'total_score' => 10,
            'passing_score' => 75,
            'is_published' => true,
        ]);

        $attempt = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now()->subHour(),
            'finished_at' => now()->subMinutes(30),
            'score' => 80,
            'is_passed' => true,
        ]);

        $response = $this->actingAs($this->siswaUser)->post('/siswa/lms/attempts/' . $attempt->id . '/submit', [
            'answers' => [],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_siswa_cannot_start_unavailable_quiz(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Future Quiz',
            'total_score' => 10,
            'passing_score' => 75,
            'is_published' => true,
            'start_time' => now()->addDays(7),
            'end_time' => now()->addDays(14),
        ]);

        $response = $this->actingAs($this->siswaUser)->get('/siswa/lms/quizzes/' . $quiz->id . '/start');
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_guru_cannot_access_siswa_lms(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/siswa/lms');
        $response->assertStatus(403);
    }

    // ================================================================
    //  MODEL UNIT TESTS
    // ================================================================

    public function test_lms_course_relationships(): void
    {
        $this->assertEquals($this->school->id, $this->course->school->id);
        $this->assertEquals($this->teacher->id, $this->course->teacher->id);
        $this->assertEquals($this->subject->id, $this->course->subject->id);
        $this->assertEquals($this->semester->id, $this->course->semester->id);
        $this->assertCount(1, $this->course->lmsClasses);
    }

    public function test_lms_course_scopes(): void
    {
        $activeCourses = LmsCourse::active()->get();
        $this->assertTrue($activeCourses->contains($this->course));

        $teacherCourses = LmsCourse::byTeacher($this->teacher->id)->get();
        $this->assertTrue($teacherCourses->contains($this->course));
    }

    public function test_lms_assignment_is_overdue(): void
    {
        $pastDue = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Past',
            'max_score' => 100,
            'deadline' => now()->subDay(),
            'is_published' => true,
        ]);
        $this->assertTrue($pastDue->isOverdue());

        $futureDue = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Future',
            'max_score' => 100,
            'deadline' => now()->addDay(),
            'is_published' => true,
        ]);
        $this->assertFalse($futureDue->isOverdue());
    }

    public function test_lms_quiz_is_available(): void
    {
        $noTimeLimits = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Always',
            'total_score' => 10,
            'passing_score' => 75,
            'is_published' => true,
        ]);
        $this->assertTrue($noTimeLimits->isAvailable());

        $unpublished = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Draft',
            'total_score' => 10,
            'passing_score' => 75,
            'is_published' => false,
        ]);
        $this->assertFalse($unpublished->isAvailable());
    }

    public function test_lms_quiz_question_is_auto_gradable(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Q',
            'total_score' => 10,
            'passing_score' => 75,
        ]);

        $mc = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'MC',
            'question_type' => 'multiple_choice',
            'score' => 5,
            'order_number' => 1,
        ]);
        $this->assertTrue($mc->isAutoGradable());

        $essay = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Essay',
            'question_type' => 'essay',
            'score' => 5,
            'order_number' => 2,
        ]);
        $this->assertFalse($essay->isAutoGradable());
    }

    public function test_lms_submission_status_label(): void
    {
        $assignment = LmsAssignment::create([
            'course_id' => $this->course->id,
            'title' => 'Labels',
            'max_score' => 100,
            'is_published' => true,
        ]);

        $submission = LmsSubmission::create([
            'assignment_id' => $assignment->id,
            'student_id' => $this->student->id,
            'status' => 'submitted',
        ]);

        $this->assertEquals('Dikumpulkan', $submission->getStatusLabel());

        $submission->update(['status' => 'graded']);
        $submission->refresh();
        $this->assertEquals('Dinilai', $submission->getStatusLabel());
    }

    public function test_lms_material_content_type_label(): void
    {
        $material = LmsMaterial::create([
            'course_id' => $this->course->id,
            'title' => 'PDF Mat',
            'material_type' => 'pdf',
            'order_number' => 1,
            'is_published' => true,
        ]);

        $this->assertEquals('PDF', $material->getContentTypeLabel());
    }

    public function test_lms_quiz_attempt_duration(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Dur',
            'total_score' => 10,
            'passing_score' => 75,
        ]);

        $attempt = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now()->subMinutes(25),
            'finished_at' => now(),
        ]);

        $this->assertEquals(25, $attempt->duration);
    }

    public function test_siswa_track_material_completion_awards_reputation_points(): void
    {
        $material = LmsMaterial::create([
            'course_id' => $this->course->id,
            'title' => 'Track Material Test',
            'material_type' => 'text',
            'content' => 'Material content test.',
            'order_number' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->siswaUser)->post('/siswa/lms/materials/' . $material->id . '/track', [
            'status' => 'completed',
            'time_spent' => 120,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('lms_material_progress', [
            'material_id' => $material->id,
            'student_id' => $this->student->id,
            'status' => 'completed',
            'progress_percent' => 100,
        ]);

        // Student should receive +10 reputation points
        $this->assertDatabaseHas('reputation_logs', [
            'user_id' => $this->siswaUser->id,
            'points' => 10,
            'category' => 'lms',
        ]);
    }

    public function test_guru_can_update_course_classroom_sync(): void
    {
        $newClassroom = \App\Models\Classroom::create([
            'school_id' => $this->teacher->school_id,
            'academic_year_id' => $this->course->semester->academic_year_id ?? 1,
            'class_code' => 'TEST-SYNC',
            'class_name' => 'Kelas Sync Test',
            'grade_level' => 7,
            'capacity' => 30,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->guruUser)->put('/guru/lms/' . $this->course->id, [
            'name' => 'Updated Name',
            'status' => 'active',
            'code' => 'LMS-UPDATED',
            'classroom_ids' => [$newClassroom->id],
        ]);

        $response->assertRedirect('/guru/lms/' . $this->course->id);
        $this->assertDatabaseHas('lms_classes', [
            'course_id' => $this->course->id,
            'classroom_id' => $newClassroom->id,
        ]);
    }

    public function test_guru_can_grade_quiz_attempt(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Quiz Essay',
            'total_score' => 20,
            'passing_score' => 70,
            'is_published' => true,
        ]);

        $essayQ = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Explain photosyntesis',
            'question_type' => 'essay',
            'score' => 20,
            'order_number' => 1,
        ]);

        $attempt = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now(),
            'finished_at' => now(),
            'score' => 0,
            'is_passed' => false,
        ]);

        $answer = LmsQuizAnswer::create([
            'attempt_id' => $attempt->id,
            'question_id' => $essayQ->id,
            'answer' => 'Its a process...',
            'score' => null,
            'is_correct' => false,
        ]);

        $response = $this->actingAs($this->guruUser)->post("/guru/lms/quizzes/attempts/{$attempt->id}/grade", [
            'grades' => [
                $essayQ->id => [
                    'score' => 15,
                    'is_correct' => 1,
                ]
            ]
        ]);

        $response->assertRedirect("/guru/lms/quizzes/{$quiz->id}/results");
        $this->assertDatabaseHas('lms_quiz_answers', [
            'attempt_id' => $attempt->id,
            'question_id' => $essayQ->id,
            'score' => 15,
            'is_correct' => true,
        ]);

        $attempt->refresh();
        $this->assertEquals(75.0, $attempt->score); // 15 / 20 * 100
        $this->assertTrue((bool)$attempt->is_passed);
    }

    public function test_siswa_track_material_validation(): void
    {
        // 1. Non-existent material
        $response = $this->actingAs($this->siswaUser)->post('/siswa/lms/materials/99999/track', [
            'status' => 'completed',
        ]);
        $response->assertStatus(404);

        // 2. Material in unenrolled course
        $otherCourse = LmsCourse::create([
            'school_id' => $this->teacher->school_id,
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->course->subject_id,
            'semester_id' => $this->course->semester_id,
            'code' => 'LMS-OTHER',
            'course_name' => 'Other Course',
            'status' => 'active',
            'is_published' => true,
        ]);

        $otherMaterial = LmsMaterial::create([
            'course_id' => $otherCourse->id,
            'title' => 'Other Material',
            'material_type' => 'text',
            'content' => 'Content',
            'order_number' => 1,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->siswaUser)->post('/siswa/lms/materials/' . $otherMaterial->id . '/track', [
            'status' => 'completed',
        ]);
        $response->assertStatus(403);
    }
}
