<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\CbtQuestionBank;
use App\Models\Employee;
use App\Models\School;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected User $guruUser;
    protected Teacher $teacher;
    protected School $school;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected Subject $subject;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup base models
        $this->school = School::create([
            'name' => 'SMKS Swasta Pembda Nias',
            'type' => 'SMK',
            'npsn' => '20220003',
            'address' => 'Jl. Pendidikan No.10',
            'city' => 'Gunungsitoli',
            'province' => 'Sumatera Utara',
            'postal_code' => '22812',
            'phone' => '081234567890',
            'email' => 'info@smkpembda.sch.id',
            'is_active' => true,
        ]);

        $this->academicYear = AcademicYear::create([
            'year' => 'TP. 2026/2027',
            'start_date' => '2026-07-13',
            'end_date' => '2027-06-30',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'semester_number' => 1,
            'semester_name' => 'Semester Ganjil 2026/2027',
            'start_date' => '2026-07-13',
            'end_date' => '2026-12-20',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'subject_code' => 'INF-X',
            'subject_name' => 'Informatika',
            'name' => 'Informatika',
            'kkm' => 75,
            'is_active' => true,
        ]);

        $this->guruUser = User::create([
            'name' => 'Guru Penguji',
            'email' => 'guru@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'school_id' => $this->school->id,
        ]);

        $employee = Employee::create([
            'school_id' => $this->school->id,
            'employee_code' => 'EMP-AI-01',
            'full_name' => 'Guru Penguji AI',
            'gender' => 'L',
            'employee_type' => 'guru',
            'employment_status' => 'yayasan',
            'tmt_date' => '2025-01-01',
            'is_active' => true,
        ]);

        $this->teacher = Teacher::create([
            'employee_id' => $employee->id,
            'user_id' => $this->guruUser->id,
            'school_id' => $this->school->id,
            'teacher_code' => 'GR-AI-01',
            'full_name' => 'Guru Penguji AI',
            'gender' => 'L',
            'education_level' => 'S1',
            'major' => 'Informatika',
            'religion' => 'Protestan',
            'is_active' => true,
        ]);
    }

    /**
     * Test that teacher can access the AI views.
     */
    public function test_teacher_can_access_ai_assistants(): void
    {
        $response = $this->actingAs($this->guruUser)->get('/guru/ai/lesson-plan');
        $response->assertStatus(200);

        $response2 = $this->actingAs($this->guruUser)->get('/guru/ai/question-generator');
        $response2->assertStatus(200);
    }

    /**
     * Test that student cannot access AI assistants.
     */
    public function test_student_cannot_access_ai_assistants(): void
    {
        $siswaUser = User::create([
            'name' => 'Siswa Test',
            'email' => 'siswa@pembda.test',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'school_id' => $this->school->id,
        ]);

        $response = $this->actingAs($siswaUser)->get('/guru/ai/lesson-plan');
        $response->assertStatus(403);

        $response2 = $this->actingAs($siswaUser)->get('/guru/ai/question-generator');
        $response2->assertStatus(403);
    }

    /**
     * Test generating RPP in mock mode.
     */
    public function test_rpp_generation_mock_mode(): void
    {
        $response = $this->actingAs($this->guruUser)->postJson('/guru/ai/lesson-plan/generate', [
            'school_type' => 'SMK',
            'grade_level' => 'Kelas X (Fase E)',
            'subject'     => 'Informatika',
            'topic'       => 'Sistem Komputer',
            'objectives'  => 'Siswa mampu memahami cara kerja CPU dan RAM.',
            'duration'    => '2 x 45 Menit',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'markdown',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertStringContainsString('Sistem Komputer', $response->json('markdown'));
    }

    /**
     * Test RPP download to MS Word format.
     */
    public function test_rpp_word_export(): void
    {
        $response = $this->actingAs($this->guruUser)->post('/guru/ai/lesson-plan/download', [
            'subject' => 'Informatika',
            'topic'   => 'Sistem Komputer',
            'markdown_content' => "# Modul Ajar\n* Topik: Sistem Komputer\n* Kelas: X",
        ]);

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/msword');
        $response->assertHeader('Content-Disposition', 'attachment;Filename=Modul_Ajar_informatika_sistem-komputer_' . date('Ymd') . '.doc');
    }

    /**
     * Test CBT question generation from pasted text in mock mode.
     */
    public function test_cbt_question_generation_from_text(): void
    {
        $bank = CbtQuestionBank::create([
            'school_id'        => $this->school->id,
            'subject_id'       => $this->subject->id,
            'teacher_id'       => $this->teacher->id,
            'academic_year_id' => $this->academicYear->id,
            'bank_name'        => 'Bank Soal Uji Coba AI',
            'description'      => 'Test',
            'grade_level'      => '10',
            'total_questions'  => 0,
            'is_active'        => true,
        ]);

        $response = $this->actingAs($this->guruUser)->postJson('/guru/ai/question-generator/generate', [
            'question_bank_id' => $bank->id,
            'content_type'     => 'text',
            'raw_text'         => 'Materi ringkas: CPU mengolah data aritmatika sedangkan RAM menyimpan data secara volatile.',
            'num_questions'    => 3,
            'difficulty'       => 'Medium',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'questions',
        ]);
        $this->assertTrue($response->json('success'));
        $this->assertCount(3, $response->json('questions'));
    }

    /**
     * Test saving generated questions database operations.
     */
    public function test_save_generated_questions_to_database(): void
    {
        $bank = CbtQuestionBank::create([
            'school_id'        => $this->school->id,
            'subject_id'       => $this->subject->id,
            'teacher_id'       => $this->teacher->id,
            'academic_year_id' => $this->academicYear->id,
            'bank_name'        => 'Bank Soal Penyelamatan',
            'description'      => 'Test',
            'grade_level'      => '10',
            'total_questions'  => 0,
            'is_active'        => true,
        ]);

        $response = $this->actingAs($this->guruUser)->postJson('/guru/ai/question-generator/save', [
            'question_bank_id' => $bank->id,
            'questions' => [
                [
                    'question' => 'Komponen apa yang menyimpan data komputer secara volatile?',
                    'options' => [
                        'A' => 'Hard Disk',
                        'B' => 'SSD',
                        'C' => 'RAM',
                        'D' => 'Processor',
                        'E' => 'Motherboard',
                    ],
                    'answer' => 'C',
                    'explanation' => 'RAM bersifat volatile karena data hilang saat listrik mati.'
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('cbt_questions', [
            'question_bank_id' => $bank->id,
            'question_text'    => 'Komponen apa yang menyimpan data komputer secara volatile?',
            'answer_key'       => 'C',
        ]);

        $questionId = \DB::table('cbt_questions')->where('question_text', 'Komponen apa yang menyimpan data komputer secara volatile?')->value('id');

        $this->assertDatabaseHas('cbt_question_options', [
            'question_id'  => $questionId,
            'option_label' => 'C',
            'option_text'  => 'RAM',
            'is_correct'   => true,
        ]);

        $this->assertDatabaseHas('cbt_question_options', [
            'question_id'  => $questionId,
            'option_label' => 'A',
            'option_text'  => 'Hard Disk',
            'is_correct'   => false,
        ]);

        // Bank total questions should be updated
        $this->assertEquals(1, $bank->fresh()->total_questions);
    }
}
