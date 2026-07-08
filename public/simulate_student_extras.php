<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\LmsCourse;
use App\Models\LmsAssignment;
use App\Models\LmsSubmission;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\CbtQuestionBank;
use App\Models\CbtQuestion;
use App\Models\CbtQuestionOption;
use App\Models\CbtExam;
use App\Models\CbtExamSession;
use App\Models\CbtExamParticipant;
use App\Models\CbtExamResult;
use App\Models\StudentAchievement;
use App\Models\StudentCounselingRecord;
use Carbon\Carbon;

function out($msg) {
    global $isCli;
    if ($isCli) {
        echo $msg . "\n";
    } else {
        echo "<p style='margin:5px 0; font-family:monospace; font-size:14px;'>" . htmlspecialchars($msg) . "</p>";
    }
}

try {
    $siswa = Student::whereHas('user', function($q) {
        $q->where('email', 'siswademo1@pembda.hub');
    })->first();

    if (!$siswa) {
        throw new \Exception("Siswa Demo 1 tidak ditemukan.");
    }

    $course = LmsCourse::where('code', 'FIS-XII-DEMO-2026')->first();
    if (!$course) {
        throw new \Exception("Course LMS tidak ditemukan.");
    }

    out("[9/10] Menyelesaikan Course LMS (Tugas & Kuis) untuk Siswa Demo 1...");
    // 1. Assignment Submissions
    $assignments = LmsAssignment::where('course_id', $course->id)->get();
    foreach ($assignments as $assignment) {
        LmsSubmission::updateOrCreate(
            [
                'assignment_id' => $assignment->id,
                'student_id' => $siswa->id,
            ],
            [
                'submitted_at' => Carbon::now()->subDays(rand(1, 3)),
                'submission_text' => '<p>Ini adalah jawaban tugas simulasi dari siswa.</p>',
                'status' => 'graded',
                'score' => rand(80, 95),
                'feedback' => 'Bagus sekali, pemahamanmu sudah sangat baik.',
                'graded_at' => Carbon::now()->subDays(1),
                'graded_by' => $course->teacher_id,
            ]
        );
        \App\Models\Grade::updateOrCreate(
            [
                'student_id' => $siswa->id,
                'subject_id' => $course->subject_id,
                'semester_id' => $course->semester_id,
                'grade_type' => 'tugas',
            ],
            [
                'teacher_id' => $course->teacher_id,
                'score' => rand(80, 95),
                'notes' => 'Nilai Tugas LMS (Simulasi)',
            ]
        );
    }
    
    // 2. Quiz Attempts
    $quizzes = LmsQuiz::where('course_id', $course->id)->get();
    foreach ($quizzes as $quiz) {
        LmsQuizAttempt::updateOrCreate(
            [
                'quiz_id' => $quiz->id,
                'student_id' => $siswa->id,
            ],
            [
                'started_at' => Carbon::now()->subDays(rand(1, 3)),
                'finished_at' => Carbon::now()->subDays(rand(1, 3))->addMinutes(15),
                'score' => rand(75, 100),
                'is_passed' => true,
            ]
        );
        \App\Models\Grade::updateOrCreate(
            [
                'student_id' => $siswa->id,
                'subject_id' => $course->subject_id,
                'semester_id' => $course->semester_id,
                'grade_type' => 'tugas',
            ],
            [
                'teacher_id' => $course->teacher_id,
                'score' => rand(75, 100),
                'notes' => 'Nilai Kuis LMS (Simulasi)',
            ]
        );
    }
    out("      -> Nilai Tugas dan Kuis LMS berhasil ditambahkan untuk Siswa Demo 1!");

    out("[10/10] Membuat Simulasi Ujian CBT (Bank Soal, Jadwal & Nilai)...");
    
    $teacherId = $course->teacher_id;
    $subjectId = $course->subject_id;
    
    $academicYearId = \App\Models\AcademicYear::where('is_active', true)->value('id');
    
    // 3. Create CBT Bank
    $bank = CbtQuestionBank::updateOrCreate(
        [
            'bank_name' => 'Bank Soal Persamaan Kuadrat (Simulasi)',
            'subject_id' => $subjectId,
            'teacher_id' => $teacherId,
        ],
        [
            'school_id' => $course->classroom->school_id ?? 1,
            'academic_year_id' => $academicYearId,
            'description' => 'Soal Simulasi',
            'grade_level' => '12',
            'total_questions' => 5,
            'is_active' => true,
        ]
    );

    // Create 5 CBT Questions
    if (CbtQuestion::where('question_bank_id', $bank->id)->count() < 5) {
        for ($i = 1; $i <= 5; $i++) {
            $q = CbtQuestion::create([
                'question_bank_id' => $bank->id,
                'question_type' => 'multiple_choice',
                'question_text' => '<p>Soal Simulasi CBT Fisika ke-' . $i . '</p>',
                'points' => 20,
            ]);
            // 4 Options
            CbtQuestionOption::insert([
                ['question_id' => $q->id, 'option_label' => 'A', 'option_text' => 'Pilihan A', 'is_correct' => true],
                ['question_id' => $q->id, 'option_label' => 'B', 'option_text' => 'Pilihan B', 'is_correct' => false],
                ['question_id' => $q->id, 'option_label' => 'C', 'option_text' => 'Pilihan C', 'is_correct' => false],
                ['question_id' => $q->id, 'option_label' => 'D', 'option_text' => 'Pilihan D', 'is_correct' => false],
            ]);
        }
    }
    
    // Create CBT Exam
    $exam = CbtExam::updateOrCreate(
        [
            'exam_title' => 'Ujian Tengah Semester (Simulasi) - Fisika',
            'academic_year_id' => $academicYearId,
            'subject_id' => $subjectId,
        ],
        [
            'school_id' => $course->classroom->school_id ?? 1,
            'teacher_id' => $teacherId,
            'exam_description' => 'Ujian Tengah Semester (Simulasi)',
            'exam_type' => 'uts',
            'semester_id' => $course->semester_id,
            'duration_minutes' => 60,
            'total_questions_shown' => 5,
            'created_by' => 1,
            'status' => 'published',
            'show_result' => true,
        ]
    );
    
    // Attach Bank to Exam if not attached
    DB::table('cbt_exam_question_bank')->updateOrInsert(
        ['exam_id' => $exam->id, 'question_bank_id' => $bank->id],
        ['questions_to_pick' => 5]
    );

    // Assign Demo XII to Exam Participants
    $classroomId = $course->classroom_id ?? \App\Models\Classroom::where('class_code', 'XII-DEMO-2026')->value('id');
    if ($classroomId) {
        CbtExamParticipant::updateOrCreate(
            ['exam_id' => $exam->id, 'classroom_id' => $classroomId],
            []
        );
    }

    // Create CBT Session (Student Attempt)
    $session = CbtExamSession::updateOrCreate(
        [
            'exam_id' => $exam->id,
            'student_id' => $siswa->id,
        ],
        [
            'classroom_id' => $classroomId,
            'attempt_number' => 1,
            'started_at' => Carbon::now()->subDays(1)->setTime(8, 0, 0),
            'finished_at' => Carbon::now()->subDays(1)->setTime(8, 45, 0),
            'status' => 'submitted',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Simulasi Browser',
        ]
    );

    CbtExamResult::updateOrCreate(
        [
            'session_id' => $session->id,
            'exam_id' => $exam->id,
            'student_id' => $siswa->id,
        ],
        [
            'total_questions' => 5,
            'answered_questions' => 5,
            'correct_answers' => 4,
            'wrong_answers' => 1,
            'total_score' => 80.00,
            'percentage_score' => 80.00,
            'final_score' => 80.00,
            'is_passed' => true,
        ]
    );
    \App\Models\Grade::updateOrCreate(
        [
            'student_id' => $siswa->id,
            'subject_id' => $subjectId,
            'semester_id' => $course->semester_id,
            'grade_type' => 'uts',
        ],
        [
            'teacher_id' => $teacherId,
            'score' => 80.00,
            'notes' => 'Nilai UTS CBT (Simulasi)',
        ]
    );
    out("      -> Bank Soal, Jadwal Ujian, dan Nilai CBT (Skor 80) berhasil ditambahkan untuk Siswa Demo 1!");

    out("[11/11] Menambahkan Catatan Perkembangan (Prestasi & Pembinaan)...");
    
    $prestasiList = [
        ['Juara 1 Olimpiade Fisika Tingkat Kabupaten', 'academic', 'city', Carbon::now()->subMonths(1)],
        ['Juara 2 Lomba Karya Tulis Ilmiah Provinsi', 'academic', 'province', Carbon::now()->subMonths(2)],
        ['Ketua Panitia Pensi Sekolah', 'other', 'school', Carbon::now()->subMonths(3)],
    ];
    foreach ($prestasiList as $p) {
        StudentAchievement::updateOrCreate(
            [
                'student_id' => $siswa->id,
                'title' => $p[0],
            ],
            [
                'type' => $p[1],
                'level' => $p[2],
                'achievement_date' => $p[3],
                'description' => 'Prestasi gemilang yang dicapai oleh siswa dalam bidang ' . $p[1],
                'academic_year_id' => $academicYearId,
            ]
        );
    }

    // Pembinaan / Konseling (1 record)
    StudentCounselingRecord::updateOrCreate(
        [
            'student_id' => $siswa->id,
            'incident_date' => Carbon::now()->subDays(10),
        ],
        [
            'record_type' => 'pelanggaran',
            'category' => 'absensi',
            'title' => 'Keterlambatan Berulang',
            'description' => 'Siswa datang terlambat 3 kali dalam seminggu.',
            'action_taken' => 'Memberikan teguran lisan dan bimbingan mengenai manajemen waktu.',
            'counselor_id' => \App\Models\Employee::first()->user_id ?? 1,
            'status' => 'resolved',
            'academic_year_id' => $academicYearId,
            'school_id' => $course->classroom->school_id ?? 1,
            'semester_id' => $course->semester_id ?? 1,
        ]
    );
    out("      -> 3 Catatan Prestasi dan 1 Catatan Pembinaan berhasil ditambahkan!");

} catch (\Exception $e) {
    out("ERROR EXTRA: " . $e->getMessage() . " on line " . $e->getLine());
}
