<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CbtExam;
use App\Models\CbtExamParticipant;
use App\Models\CbtExamSession;
use App\Models\CbtExamResult;
use App\Models\CbtQuestionBank;
use App\Models\CbtQuestion;
use App\Models\CbtQuestionOption;
use App\Models\CbtAnswer;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;

class CbtExamTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // MODEL & FACTORY TESTS
    // ==========================================

    public function test_can_create_exam(): void
    {
        $exam = CbtExam::factory()->create();

        $this->assertDatabaseHas('cbt_exams', [
            'id' => $exam->id,
            'status' => 'draft',
        ]);
        $this->assertNotNull($exam->subject_id);
        $this->assertNotNull($exam->teacher_id);
    }

    public function test_exam_has_correct_casts(): void
    {
        $exam = CbtExam::factory()->create();

        $this->assertIsBool($exam->randomize_questions);
        $this->assertIsBool($exam->prevent_tab_switch);
        $this->assertIsFloat($exam->passing_score);
        $this->assertIsInt($exam->duration_minutes);
    }

    public function test_exam_status_states(): void
    {
        $draft = CbtExam::factory()->create();
        $active = CbtExam::factory()->active()->create();
        $completed = CbtExam::factory()->completed()->create();

        $this->assertEquals('draft', $draft->status);
        $this->assertEquals('active', $active->status);
        $this->assertEquals('completed', $completed->status);
    }

    public function test_question_bank_and_questions(): void
    {
        $bank = CbtQuestionBank::factory()->create();
        $question = CbtQuestion::factory()->create(['question_bank_id' => $bank->id]);

        $this->assertDatabaseHas('cbt_questions', [
            'id' => $question->id,
            'question_bank_id' => $bank->id,
        ]);
        $this->assertTrue($bank->questions->contains($question));
    }

    public function test_question_with_options(): void
    {
        $question = CbtQuestion::factory()->create();
        $options = collect(['A', 'B', 'C', 'D'])->map(function ($label, $idx) use ($question) {
            return CbtQuestionOption::factory()->create([
                'question_id' => $question->id,
                'option_label' => $label,
                'is_correct' => $label === 'A',
                'sort_order' => $idx + 1,
            ]);
        });

        $question->refresh();
        $this->assertCount(4, $question->options);
        $this->assertEquals(1, $question->options->where('is_correct', true)->count());
    }

    public function test_exam_participant_uses_classroom(): void
    {
        $exam = CbtExam::factory()->create();
        $classroom = Classroom::factory()->create();

        $participant = CbtExamParticipant::factory()->create([
            'exam_id' => $exam->id,
            'classroom_id' => $classroom->id,
        ]);

        $this->assertDatabaseHas('cbt_exam_participants', [
            'exam_id' => $exam->id,
            'classroom_id' => $classroom->id,
        ]);
    }

    public function test_exam_session_creation(): void
    {
        $exam = CbtExam::factory()->active()->create();
        $student = Student::factory()->create();

        $session = CbtExamSession::factory()->create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
        ]);

        $this->assertDatabaseHas('cbt_exam_sessions', [
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'status' => 'in_progress',
        ]);
        $this->assertIsArray($session->question_order);
    }

    public function test_answer_saving(): void
    {
        $session = CbtExamSession::factory()->create();
        $question = CbtQuestion::factory()->create();

        $answer = CbtAnswer::factory()->create([
            'session_id' => $session->id,
            'question_id' => $question->id,
            'selected_option' => 'B',
        ]);

        $this->assertDatabaseHas('cbt_answers', [
            'session_id' => $session->id,
            'question_id' => $question->id,
            'selected_option' => 'B',
        ]);
    }

    public function test_exam_result_with_scores(): void
    {
        $result = CbtExamResult::factory()->passed()->create();

        $this->assertTrue($result->is_passed);
        $this->assertEquals(80, $result->final_score);
        $this->assertEquals('B', $result->predicate);
    }

    public function test_exam_result_predicate_calculation(): void
    {
        $this->assertEquals('A', CbtExamResult::calculatePredicate(90, 65));
        $this->assertEquals('B', CbtExamResult::calculatePredicate(80, 65));
        $this->assertEquals('C', CbtExamResult::calculatePredicate(65, 65));
        $this->assertEquals('D', CbtExamResult::calculatePredicate(40, 65));
    }

    // ==========================================
    // RELATIONSHIP TESTS
    // ==========================================

    public function test_exam_relationships(): void
    {
        $exam = CbtExam::factory()->create();

        $this->assertNotNull($exam->subject);
        $this->assertNotNull($exam->teacher);
        $this->assertNotNull($exam->semester);
    }

    public function test_session_belongs_to_exam_and_student(): void
    {
        $session = CbtExamSession::factory()->create();

        $this->assertNotNull($session->exam);
        $this->assertNotNull($session->student);
    }

    // ==========================================
    // GURU PORTAL TESTS
    // ==========================================

    public function test_guru_can_view_exam_list(): void
    {
        $teacher = Teacher::factory()->create();
        $user = $teacher->user;
        $exam = CbtExam::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->actingAs($user)->get(route('guru.cbt.exams.index'));

        $response->assertStatus(200);
    }

    public function test_guru_can_view_bank_soal(): void
    {
        $teacher = Teacher::factory()->create();
        $user = $teacher->user;
        $bank = CbtQuestionBank::factory()->create(['teacher_id' => $teacher->id]);

        $response = $this->actingAs($user)->get(route('guru.cbt.banks.index'));

        $response->assertStatus(200);
    }

    public function test_guru_can_publish_exam(): void
    {
        $teacher = Teacher::factory()->create();
        $user = $teacher->user;
        $exam = CbtExam::factory()->create([
            'teacher_id' => $teacher->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->post(route('guru.cbt.exams.publish', $exam));

        $this->assertEquals('published', $exam->fresh()->status);
    }

    // ==========================================
    // SISWA PORTAL TESTS
    // ==========================================

    public function test_siswa_can_view_available_exams(): void
    {
        $student = Student::factory()->create();
        $user = User::factory()->create(['role' => 'siswa']);
        $student->update(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('siswa.cbt.index'));

        $response->assertStatus(200);
    }

    public function test_siswa_answer_saved_via_ajax(): void
    {
        $student = Student::factory()->create();
        $user = User::factory()->create(['role' => 'siswa']);
        $student->update(['user_id' => $user->id]);

        $session = CbtExamSession::factory()->create([
            'student_id' => $student->id,
        ]);
        $question = CbtQuestion::factory()->create();

        $response = $this->actingAs($user)->postJson(
            route('siswa.cbt.save-answer', $session),
            ['question_id' => $question->id, 'selected_option' => 'C']
        );

        $response->assertJson(['success' => true]);
    }

    public function test_siswa_tab_switch_tracked(): void
    {
        $student = Student::factory()->create();
        $user = User::factory()->create(['role' => 'siswa']);
        $student->update(['user_id' => $user->id]);

        $session = CbtExamSession::factory()->create([
            'student_id' => $student->id,
            'tab_switch_count' => 0,
        ]);

        $response = $this->actingAs($user)->postJson(
            route('siswa.cbt.tab-switch', $session)
        );

        $this->assertEquals(1, $session->fresh()->tab_switch_count);
    }

    public function test_manual_essay_grading_syncs_to_grades_table(): void
    {
        $teacher = Teacher::factory()->create();
        $student = Student::factory()->create();
        $exam = CbtExam::factory()->create([
            'teacher_id' => $teacher->id,
            'auto_sync_grade' => true,
        ]);
        
        $session = CbtExamSession::factory()->create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'status' => 'in_progress',
        ]);
        
        $question = CbtQuestion::factory()->create([
            'question_type' => 'essay',
            'points' => 10,
        ]);

        \App\Models\CbtExamQuestion::create([
            'exam_id' => $exam->id,
            'question_id' => $question->id,
            'points_override' => 10,
        ]);
        
        $answer = CbtAnswer::create([
            'session_id' => $session->id,
            'question_id' => $question->id,
            'text_answer' => 'This is my essay answer.',
        ]);
        
        // Initial submit: grades synced as 0 since essay is not graded yet
        $cbtService = app(\App\Services\CbtService::class);
        $result = $cbtService->submitSession($session);
        
        $this->assertTrue($result->grade_synced);
        $this->assertEquals(0, $result->final_score);
        
        $grade = \App\Models\Grade::where('lms_source_type', 'cbt_exam')
            ->where('lms_source_id', $result->id)
            ->first();
            
        $this->assertNotNull($grade);
        $this->assertEquals(0, $grade->score);
        
        // Teacher grades the essay answer manually to 8 points
        $cbtService->gradeEssayAnswer($answer, 8, 'Good job!', $teacher->user_id);
        
        // Verify result and grade are updated
        $this->assertEquals(80, $result->fresh()->final_score);
        $this->assertEquals(80, $grade->fresh()->score);
    }
}
