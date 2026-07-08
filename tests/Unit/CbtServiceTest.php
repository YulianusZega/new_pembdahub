<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\CbtExam;
use App\Models\CbtExamSession;
use App\Models\CbtExamResult;
use App\Models\CbtExamQuestion;
use App\Models\CbtQuestionBank;
use App\Models\CbtQuestion;
use App\Models\CbtQuestionOption;
use App\Models\CbtAnswer;
use App\Models\Student;
use App\Models\Classroom;
use App\Services\CbtService;

class CbtServiceTest extends TestCase
{
    use RefreshDatabase;

    private CbtService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CbtService::class);
    }

    /**
     * Helper: create exam with bank, questions, and options
     */
    private function createExamWithQuestions(int $questionCount = 5): array
    {
        $exam = CbtExam::factory()->active()->create([
            'duration_minutes' => 60,
            'passing_score' => 70,
            'randomize_questions' => false,
            'randomize_options' => false,
        ]);

        $bank = CbtQuestionBank::factory()->create([
            'school_id' => $exam->school_id,
            'teacher_id' => $exam->teacher_id,
        ]);

        // Attach bank to exam via M2M
        $exam->questionBanks()->attach($bank->id, ['questions_to_pick' => $questionCount]);

        $questions = [];
        for ($i = 0; $i < $questionCount; $i++) {
            $q = CbtQuestion::factory()->create([
                'question_bank_id' => $bank->id,
                'points' => 10,
            ]);

            // Create 4 options, A is correct
            foreach (['A', 'B', 'C', 'D'] as $idx => $label) {
                CbtQuestionOption::factory()->create([
                    'question_id' => $q->id,
                    'option_label' => $label,
                    'is_correct' => $label === 'A',
                    'sort_order' => $idx + 1,
                ]);
            }
            $questions[] = $q;
        }

        return compact('exam', 'bank', 'questions');
    }

    // ==========================================
    // prepareExamQuestions
    // ==========================================

    public function test_prepare_exam_questions_from_bank(): void
    {
        ['exam' => $exam, 'questions' => $questions] = $this->createExamWithQuestions(5);

        $this->service->prepareExamQuestions($exam);

        $exam->refresh();
        $this->assertEquals(5, $exam->total_questions_shown);
        $this->assertCount(5, $exam->examQuestions);
    }

    public function test_prepare_exam_questions_respects_pick_count(): void
    {
        $exam = CbtExam::factory()->active()->create();
        $bank = CbtQuestionBank::factory()->create();

        // Create 10 questions but only pick 3
        for ($i = 0; $i < 10; $i++) {
            CbtQuestion::factory()->create(['question_bank_id' => $bank->id]);
        }
        $exam->questionBanks()->attach($bank->id, ['questions_to_pick' => 3]);

        $this->service->prepareExamQuestions($exam);

        $exam->refresh();
        $this->assertEquals(3, $exam->total_questions_shown);
    }

    // ==========================================
    // startExamSession
    // ==========================================

    public function test_start_exam_session_creates_session(): void
    {
        ['exam' => $exam, 'questions' => $questions] = $this->createExamWithQuestions(3);
        $this->service->prepareExamQuestions($exam);

        $student = Student::factory()->create();
        $classroom = Classroom::factory()->create();

        $session = $this->service->startExamSession($exam, $student, $classroom->id);

        $this->assertInstanceOf(CbtExamSession::class, $session);
        $this->assertEquals('in_progress', $session->status);
        $this->assertEquals(1, $session->attempt_number);
        $this->assertNotNull($session->deadline_at);
        $this->assertIsArray($session->question_order);
        $this->assertCount(3, $session->question_order);
    }

    public function test_start_exam_session_returns_existing_if_in_progress(): void
    {
        ['exam' => $exam] = $this->createExamWithQuestions(2);
        $this->service->prepareExamQuestions($exam);
        $student = Student::factory()->create();
        $classroom = Classroom::factory()->create();

        $session1 = $this->service->startExamSession($exam, $student, $classroom->id);
        $session2 = $this->service->startExamSession($exam, $student, $classroom->id);

        $this->assertEquals($session1->id, $session2->id);
    }

    public function test_start_exam_session_exceeding_max_attempts(): void
    {
        $exam = CbtExam::factory()->active()->create(['max_attempts' => 1]);
        $student = Student::factory()->create();
        $classroom = Classroom::factory()->create();

        // Create a submitted session
        CbtExamSession::factory()->submitted()->create([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
            'classroom_id' => $classroom->id,
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->startExamSession($exam, $student, $classroom->id);
    }

    // ==========================================
    // saveAnswer
    // ==========================================

    public function test_save_answer_mc(): void
    {
        $session = CbtExamSession::factory()->create([
            'status' => 'in_progress',
            'deadline_at' => now()->addMinutes(30),
        ]);
        $question = CbtQuestion::factory()->create();

        $answer = $this->service->saveAnswer($session, $question->id, [
            'selected_option' => 'B',
        ]);

        $this->assertEquals('B', $answer->selected_option);
        $this->assertDatabaseHas('cbt_answers', [
            'session_id' => $session->id,
            'question_id' => $question->id,
            'selected_option' => 'B',
        ]);
    }

    public function test_save_answer_essay(): void
    {
        $session = CbtExamSession::factory()->create([
            'status' => 'in_progress',
            'deadline_at' => now()->addMinutes(30),
        ]);
        $question = CbtQuestion::factory()->essay()->create();

        $answer = $this->service->saveAnswer($session, $question->id, [
            'text_answer' => 'Jawaban essay saya adalah ...',
        ]);

        $this->assertEquals('Jawaban essay saya adalah ...', $answer->text_answer);
    }

    public function test_save_answer_fails_when_session_not_active(): void
    {
        $session = CbtExamSession::factory()->submitted()->create();
        $question = CbtQuestion::factory()->create();

        $this->expectException(\RuntimeException::class);
        $this->service->saveAnswer($session, $question->id, ['selected_option' => 'A']);
    }

    // ==========================================
    // autoGrade & calculateResult
    // ==========================================

    public function test_submit_session_auto_grades_mc(): void
    {
        ['exam' => $exam, 'questions' => $questions] = $this->createExamWithQuestions(3);
        $this->service->prepareExamQuestions($exam);

        $student = Student::factory()->create();
        $classroom = Classroom::factory()->create();
        $session = $this->service->startExamSession($exam, $student, $classroom->id);

        // Answer all 3 with correct option (A)
        foreach ($questions as $q) {
            $this->service->saveAnswer($session, $q->id, ['selected_option' => 'A']);
        }

        $result = $this->service->submitSession($session);

        $this->assertEquals(3, $result->correct_answers);
        $this->assertEquals(0, $result->wrong_answers);
        $this->assertEquals(100, $result->final_score);
        $this->assertTrue($result->is_passed);
        $this->assertEquals('A', $result->predicate);
    }

    public function test_submit_session_with_wrong_answers(): void
    {
        ['exam' => $exam, 'questions' => $questions] = $this->createExamWithQuestions(4);
        $this->service->prepareExamQuestions($exam);

        $student = Student::factory()->create();
        $classroom = Classroom::factory()->create();
        $session = $this->service->startExamSession($exam, $student, $classroom->id);

        // Answer 2 correct, 2 wrong
        $this->service->saveAnswer($session, $questions[0]->id, ['selected_option' => 'A']); // correct
        $this->service->saveAnswer($session, $questions[1]->id, ['selected_option' => 'A']); // correct
        $this->service->saveAnswer($session, $questions[2]->id, ['selected_option' => 'B']); // wrong
        $this->service->saveAnswer($session, $questions[3]->id, ['selected_option' => 'C']); // wrong

        $result = $this->service->submitSession($session);

        $this->assertEquals(2, $result->correct_answers);
        $this->assertEquals(2, $result->wrong_answers);
        $this->assertEquals(50, $result->final_score); // 20/40 * 100
        $this->assertFalse($result->is_passed);
    }

    // ==========================================
    // calculateRankings
    // ==========================================

    public function test_calculate_rankings_orders_by_score(): void
    {
        $exam = CbtExam::factory()->completed()->create();

        $r1 = CbtExamResult::factory()->create(['exam_id' => $exam->id, 'final_score' => 90]);
        $r2 = CbtExamResult::factory()->create(['exam_id' => $exam->id, 'final_score' => 60]);
        $r3 = CbtExamResult::factory()->create(['exam_id' => $exam->id, 'final_score' => 80]);

        $this->service->calculateRankings($exam);

        $this->assertEquals(1, $r1->fresh()->rank);
        $this->assertEquals(2, $r3->fresh()->rank);
        $this->assertEquals(3, $r2->fresh()->rank);
    }

    // ==========================================
    // getExamStatistics
    // ==========================================

    public function test_exam_statistics_empty(): void
    {
        $exam = CbtExam::factory()->create();

        $stats = $this->service->getExamStatistics($exam);

        $this->assertEquals(0, $stats['total_participants']);
    }

    public function test_exam_statistics_with_results(): void
    {
        $exam = CbtExam::factory()->create(['passing_score' => 70]);

        CbtExamResult::factory()->create(['exam_id' => $exam->id, 'final_score' => 90, 'is_passed' => true]);
        CbtExamResult::factory()->create(['exam_id' => $exam->id, 'final_score' => 80, 'is_passed' => true]);
        CbtExamResult::factory()->create(['exam_id' => $exam->id, 'final_score' => 50, 'is_passed' => false]);

        $stats = $this->service->getExamStatistics($exam);

        $this->assertEquals(3, $stats['total_participants']);
        $this->assertEquals(90, $stats['highest_score']);
        $this->assertEquals(50, $stats['lowest_score']);
        $this->assertEquals(2, $stats['passed_count']);
        $this->assertEquals(1, $stats['failed_count']);
    }

    // ==========================================
    // gradeEssayAnswer
    // ==========================================

    public function test_grade_essay_answer(): void
    {
        $exam = CbtExam::factory()->create();
        $session = CbtExamSession::factory()->create(['exam_id' => $exam->id]);
        $question = CbtQuestion::factory()->essay()->create(['points' => 20]);

        CbtExamQuestion::create([
            'exam_id' => $exam->id,
            'question_id' => $question->id,
            'sort_order' => 1,
        ]);

        $answer = CbtAnswer::factory()->essay()->create([
            'session_id' => $session->id,
            'question_id' => $question->id,
        ]);

        $graded = $this->service->gradeEssayAnswer($answer, 15.0, 'Bagus!', 1);

        $this->assertEquals(15.0, $graded->manual_score);
        $this->assertEquals(15.0, $graded->score_obtained);
        $this->assertEquals('Bagus!', $graded->teacher_feedback);
        $this->assertTrue($graded->is_correct);
    }
}
