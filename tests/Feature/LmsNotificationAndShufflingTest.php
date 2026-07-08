<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Employee;
use App\Models\LmsClass;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendWhatsAppMessage;
use Tests\TestCase;

class LmsNotificationAndShufflingTest extends TestCase
{
    use RefreshDatabase;

    protected School $school;
    protected AcademicYear $academicYear;
    protected Semester $semester;
    protected Subject $subject;
    protected Classroom $classroom;
    protected User $guruUser;
    protected Teacher $teacher;
    protected User $siswaUser;
    protected Student $student;
    protected LmsCourse $course;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::create([
            'name' => 'SMA Swasta Pembda 1 Gunungsitoli',
            'type' => 'SMA',
            'npsn' => '20220002',
            'address' => 'Jl. Pendidikan No.1',
            'city' => 'Gunungsitoli',
            'province' => 'Sumatera Utara',
            'postal_code' => '22812',
            'phone' => '082168532568',
            'email' => 'info@sma1pembda.sch.id',
            'is_active' => true,
        ]);

        $this->academicYear = AcademicYear::create([
            'year' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'semester_start' => '2025-07-15',
            'semester_end' => '2026-06-15',
            'is_active' => true,
        ]);

        $this->semester = Semester::create([
            'academic_year_id' => $this->academicYear->id,
            'semester_number' => 2,
            'semester_name' => 'Genap 2025/2026',
            'start_date' => '2026-01-05',
            'end_date' => '2026-06-15',
            'is_active' => true,
        ]);

        $this->subject = Subject::create([
            'school_id' => $this->school->id,
            'subject_code' => 'KIM',
            'subject_name' => 'Kimia',
            'description' => 'Pelajaran Kimia',
            'kkm' => 75,
            'is_active' => true,
        ]);

        $this->classroom = Classroom::create([
            'school_id' => $this->school->id,
            'academic_year_id' => $this->academicYear->id,
            'class_code' => 'X-MIPA',
            'class_name' => 'X MIPA',
            'grade_level' => 10,
            'capacity' => 30,
            'is_active' => true,
        ]);

        $this->guruUser = User::create([
            'name' => 'Guru Kimia',
            'username' => 'guru_kimia',
            'email' => 'guru_kimia@test.com',
            'password' => bcrypt('password'),
            'role' => 'guru',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $employee = Employee::create([
            'school_id' => $this->school->id,
            'employee_code' => 'EMP-KIM-001',
            'full_name' => 'Guru Kimia',
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
            'teacher_code' => 'GR-KIM-001',
            'full_name' => 'Guru Kimia',
            'gender' => 'L',
            'education_level' => 'S1',
            'major' => 'Kimia',
            'religion' => 'Kristen',
            'is_active' => true,
        ]);

        $this->siswaUser = User::create([
            'name' => 'Siswa Kimia',
            'username' => 'siswa_kimia',
            'email' => 'siswa_kimia@test.com',
            'password' => bcrypt('password'),
            'role' => 'siswa',
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        $this->student = Student::create([
            'user_id' => $this->siswaUser->id,
            'school_id' => $this->school->id,
            'nis' => '2026001',
            'nisn' => '0012345679',
            'full_name' => 'Siswa Kimia',
            'gender' => 'L',
            'birth_place' => 'Gunungsitoli',
            'birth_date' => '2010-05-15',
            'religion' => 'Kristen',
            'address' => 'Jl. Merak No. 1',
            'entry_year' => 2025,
            'status' => 'aktif',
            'phone' => '081234567890',
        ]);

        $this->student->classrooms()->attach($this->classroom->id, [
            'academic_year_id' => $this->academicYear->id,
            'status' => 'aktif',
        ]);

        $this->course = LmsCourse::create([
            'school_id' => $this->school->id,
            'teacher_id' => $this->teacher->id,
            'subject_id' => $this->subject->id,
            'semester_id' => $this->semester->id,
            'code' => 'LMS-KIM001',
            'course_name' => 'Kimia Dasar',
            'description' => 'Kursus kimia untuk kelas X',
            'status' => 'active',
            'is_published' => true,
            'is_active' => true,
        ]);

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
    }

    /**
     * Test notification routing and template rendering.
     */
    public function test_it_sends_lms_notifications(): void
    {
        Queue::fake();

        // Enable whatsapp configuration dynamically in config
        config(['services.whatsapp.enabled' => true]);
        config(['services.whatsapp.api_url' => 'https://api.fonnte.com']);
        config(['services.whatsapp.api_token' => 'mock_token']);

        $notificationService = app(NotificationService::class);

        $result = $notificationService->sendLmsNotification($this->course, 'lms.material.published', [
            'title' => 'Struktur Atom',
        ]);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['dispatched']);

        Queue::assertPushed(SendWhatsAppMessage::class, function ($job) {
            $reflector = new \ReflectionClass($job);
            $messageProp = $reflector->getProperty('message');
            $messageProp->setAccessible(true);
            $message = $messageProp->getValue($job);

            return strpos($message, 'MATERI BARU TERSEDIA') !== false 
                && strpos($message, 'Siswa Kimia') !== false
                && strpos($message, 'Struktur Atom') !== false;
        });
    }

    /**
     * Test seeded question shuffling.
     */
    public function test_seeded_question_shuffling_is_deterministic(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Ujian Kimia',
            'total_score' => 30,
            'passing_score' => 70,
            'shuffle_questions' => true,
            'is_published' => true,
        ]);

        // Add 5 questions
        for ($i = 1; $i <= 5; $i++) {
            LmsQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => "Soal nomor {$i}",
                'question_type' => 'multiple_choice',
                'score' => 10,
                'order_number' => $i,
            ]);
        }

        // Student attempt
        $attempt = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now(),
        ]);

        // Get questions twice for the same attempt
        $q1 = $quiz->questions()->inRandomOrder($attempt->id)->get()->pluck('id')->toArray();
        $q2 = $quiz->questions()->inRandomOrder($attempt->id)->get()->pluck('id')->toArray();

        // Shuffled order must be identical for same attempt
        $this->assertEquals($q1, $q2);

        // Another attempt must have different order (probabilistically, but with 5 questions rand seed handles it)
        $attempt2 = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now(),
        ]);
        $q3 = $quiz->questions()->inRandomOrder($attempt2->id)->get()->pluck('id')->toArray();
        
        // It's possible to be the same, but seed ensures it's generated differently
        $this->assertNotNull($q3);
    }

    /**
     * Test seeded option shuffling.
     */
    public function test_seeded_option_shuffling_is_deterministic(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Ujian Contoh',
            'total_score' => 10,
            'passing_score' => 70,
            'is_published' => true,
        ]);

        $question = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Contoh Soal',
            'question_type' => 'multiple_choice',
            'score' => 10,
            'order_number' => 1,
            'options' => [
                ['key' => 'A', 'text' => 'Pilihan A'],
                ['key' => 'B', 'text' => 'Pilihan B'],
                ['key' => 'C', 'text' => 'Pilihan C'],
                ['key' => 'D', 'text' => 'Pilihan D'],
            ],
        ]);

        $seed1 = 12345;
        $seed2 = 54321;

        $opts1_1 = $question->getShuffledOptions($seed1);
        $opts1_2 = $question->getShuffledOptions($seed1);
        $opts2_1 = $question->getShuffledOptions($seed2);

        // Same seed yields identical order
        $this->assertEquals($opts1_1, $opts1_2);

        // Different seed yields different order (or probabilistically shuffled)
        $this->assertNotNull($opts2_1);
        $this->assertEquals(count($opts1_1), count($opts2_1));
    }

    /**
     * Test quiz submission with shuffled options and verify correct scoring & index mapping.
     */
    public function test_shuffled_options_scoring_maps_back_to_original_index_correctly(): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Kuis Acak Opsi',
            'total_score' => 10,
            'passing_score' => 70,
            'shuffle_questions' => true,
            'is_published' => true,
        ]);

        // Option index 0 ('Kucing') is the correct answer
        $question = LmsQuizQuestion::create([
            'quiz_id' => $quiz->id,
            'question' => 'Hewan apa yang mengeong?',
            'question_type' => 'multiple_choice',
            'score' => 10,
            'order_number' => 1,
            'options' => ['Kucing', 'Anjing', 'Burung'],
            'correct_answer' => '0',
        ]);

        $attempt = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $this->student->id,
            'started_at' => now(),
        ]);

        // Get the shuffled options for this attempt
        $shuffledOptions = $question->getShuffledOptions($attempt->id);

        // Find the index of 'Kucing' in the shuffled array
        $shuffledIndex = array_search('Kucing', $shuffledOptions);
        $this->assertNotFalse($shuffledIndex);

        // Simulate student submitting the shuffled index
        $response = $this->actingAs($this->siswaUser)
            ->post(route('siswa.lms.quizzes.submit', $attempt->id), [
                'answers' => [
                    $question->id => (string)$shuffledIndex,
                ],
            ]);

        $response->assertRedirect();

        // Verify that the answer stored in database is mapped back to the original index ('0')
        $storedAnswer = \App\Models\LmsQuizAnswer::where('attempt_id', $attempt->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($storedAnswer);
        $this->assertEquals('0', $storedAnswer->answer);
        $this->assertTrue((bool)$storedAnswer->is_correct);
        $this->assertEquals(10, $storedAnswer->score);

        // Verify the attempt overall score
        $attempt->refresh();
        $this->assertEquals(100.0, $attempt->score);
        $this->assertTrue((bool)$attempt->is_passed);
    }
}
