<?php

/**
 * LMS Premium Features Functional Simulation Test
 * 
 * Run via terminal: php scratch/simulation_test.php
 */

// 1. Bootstrap Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsMaterial;
use App\Models\LmsAssignment;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Student;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendWhatsAppMessage;

class SimulationTester
{
    private $course;
    private $teacher;
    private $students;

    public function run()
    {
        $this->printHeader("STARTING LMS PREMIUM FEATURES FUNCTIONAL SIMULATION");

        try {
            DB::beginTransaction();

            $this->setupFixtureData();

            $this->testPoint1_VideoConference();
            $this->testPoint2_WhatsAppNotifications();
            $this->testPoint3_QuizShuffling();
            $this->testPoint4_ClassroomAvatarsAndAnalytics();

        } catch (\Exception $e) {
            $this->printError("Simulation failed with exception: " . $e->getMessage());
            echo $e->getTraceAsString() . "\n";
        } finally {
            DB::rollBack();
            $this->printHeader("SIMULATION COMPLETED (DATABASE CHANGES ROLLED BACK)");
        }
    }

    private function setupFixtureData()
    {
        $this->printSection("Setting Up Fixture Data");

        // Find or create a teacher
        $this->teacher = Teacher::first() ?? Teacher::create([
            'school_id' => 1,
            'teacher_code' => 'TCH-SIM-001',
            'full_name' => 'Dr. Robert Oppenheimer',
            'gender' => 'L',
            'is_active' => true,
        ]);
        echo "✓ Teacher: {$this->teacher->full_name}\n";

        // Find or create a classroom
        $classroom = Classroom::create([
            'school_id' => 1,
            'academic_year_id' => 1,
            'class_code' => 'XI-SDR',
            'class_name' => 'XI Sudirman',
            'grade_level' => 11,
            'is_active' => true,
        ]);
        echo "✓ Classroom: {$classroom->class_name}\n";

        // Create a course
        $this->course = LmsCourse::create([
            'school_id' => 1,
            'teacher_id' => $this->teacher->id,
            'subject_id' => 1,
            'semester_id' => 1,
            'classroom_id' => $classroom->id,
            'code' => 'LMS-SIM001',
            'course_name' => 'Fisika Kuantum',
            'status' => 'active',
            'is_published' => true,
            'is_active' => true,
        ]);
        echo "✓ Course: {$this->course->course_name} ({$this->course->code})\n";

        // Create LMS class
        $lmsClass = \App\Models\LmsClass::create([
            'course_id' => $this->course->id,
            'classroom_id' => $classroom->id,
            'school_id' => 1,
        ]);

        // Create 2 students and enroll them
        $names = ['Albert Einstein', 'Niels Bohr'];
        $phones = ['081234567001', '081234567002'];
        $this->students = [];

        foreach ($names as $idx => $name) {
            $student = Student::create([
                'school_id' => 1,
                'nis' => '999900' . $idx,
                'nisn' => '999900000' . $idx,
                'full_name' => $name,
                'gender' => 'L',
                'status' => 'aktif',
                'phone' => $phones[$idx],
                'entry_year' => 2025,
            ]);

            LmsEnrollment::create([
                'lms_class_id' => $lmsClass->id,
                'student_id' => $student->id,
                'status' => 'enrolled',
                'enrolled_at' => now(),
            ]);

            $this->students[] = $student;
            echo "✓ Student Enrolled: {$student->full_name} (WA: {$student->phone})\n";
        }
    }

    private function testPoint1_VideoConference()
    {
        $this->printSection("POINT 1: Video Conference Tatap Muka Virtual (Jitsi Meet)");

        // 1. Simulate starting meeting
        $this->course->update([
            'meeting_active' => true,
            'meeting_started_at' => now(),
        ]);
        echo "-> Guru memulai tatap muka virtual...\n";
        echo "✓ Database State: meeting_active = " . ($this->course->meeting_active ? 'TRUE' : 'FALSE') . "\n";
        echo "✓ Database State: meeting_started_at = " . $this->course->meeting_started_at . "\n";

        // 2. Generate room name securely
        $roomName = 'PembdaHub_Course_' . $this->course->id . '_' . md5($this->course->code . config('app.key'));
        echo "✓ Secured Jitsi Room Name generated: {$roomName}\n";

        // 3. Simulate stopping meeting
        $this->course->update([
            'meeting_active' => false,
            'meeting_started_at' => null,
        ]);
        echo "-> Guru menghentikan tatap muka virtual...\n";
        echo "✓ Database State: meeting_active = " . ($this->course->meeting_active ? 'TRUE' : 'FALSE') . "\n";
    }

    private function testPoint2_WhatsAppNotifications()
    {
        $this->printSection("POINT 2: Notifikasi Otomatis via WhatsApp (Queued Jobs)");

        // Fake the queue to inspect pushed jobs
        Queue::fake();

        // Enable whatsapp configuration
        config(['services.whatsapp.enabled' => true]);
        config(['services.whatsapp.api_url' => 'https://api.fonnte.com']);
        config(['services.whatsapp.api_token' => 'simulated_token']);

        $notificationService = app(NotificationService::class);

        // 1. Simulate publishing new material
        echo "-> Guru mempublikasikan materi baru...\n";
        $notificationService->sendLmsNotification($this->course, 'lms.material.published', [
            'title' => 'Teori Relativitas Khusus',
        ]);

        // 2. Simulate publishing new assignment
        echo "-> Guru mempublikasikan tugas baru...\n";
        $notificationService->sendLmsNotification($this->course, 'lms.assignment.published', [
            'title' => 'Esai Paradoks Kembar',
            'due_date' => now()->addDays(5)->format('d M Y H:i'),
        ]);

        // 3. Simulate publishing new quiz
        echo "-> Guru menerbitkan kuis baru...\n";
        $notificationService->sendLmsNotification($this->course, 'lms.quiz.published', [
            'title' => 'Kuis Mekanika Gelombang',
        ]);

        // 4. Simulate starting a virtual meeting
        echo "-> Guru memulai virtual meeting...\n";
        $notificationService->sendLmsNotification($this->course, 'lms.meeting.started');

        // Check queued messages in the Fake Queue
        $pushedJobs = Queue::pushedJobs();
        echo "✓ Total WhatsApp Message Jobs Queued: " . count($pushedJobs[SendWhatsAppMessage::class] ?? []) . "\n";

        foreach ($pushedJobs[SendWhatsAppMessage::class] ?? [] as $idx => $jobData) {
            $job = $jobData['job'];
            $reflector = new \ReflectionClass($job);
            
            $phoneProp = $reflector->getProperty('phone');
            $phoneProp->setAccessible(true);
            $phone = $phoneProp->getValue($job);

            $msgProp = $reflector->getProperty('message');
            $msgProp->setAccessible(true);
            $message = $msgProp->getValue($job);

            echo "   [Job #" . ($idx + 1) . "] To: {$phone}\n";
            echo "   Message preview: " . str_replace("\n", " ", substr($message, 0, 120)) . "...\n\n";
        }
    }

    private function testPoint3_QuizShuffling()
    {
        $this->printSection("POINT 3: Pengacakan Seeded & Keamanan Kuis (Anti-Cheat)");

        // 1. Create a quiz with shuffle_questions enabled
        $quiz = LmsQuiz::create([
            'course_id' => $this->course->id,
            'title' => 'Ujian Akhir Kuantum',
            'total_score' => 30,
            'passing_score' => 70,
            'shuffle_questions' => true,
            'is_published' => true,
        ]);

        // 2. Add 5 questions with options
        for ($i = 1; $i <= 5; $i++) {
            LmsQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => "Soal nomor {$i}",
                'question_type' => 'multiple_choice',
                'score' => 10,
                'order_number' => $i,
                'options' => [
                    ['key' => 'A', 'text' => "Pilihan A untuk soal {$i}"],
                    ['key' => 'B', 'text' => "Pilihan B untuk soal {$i}"],
                    ['key' => 'C', 'text' => "Pilihan C untuk soal {$i}"],
                    ['key' => 'D', 'text' => "Pilihan D untuk soal {$i}"],
                ],
            ]);
        }
        echo "✓ Quiz created with 5 questions. shuffle_questions = TRUE\n";

        // 3. Student A starts attempt
        $studentA = $this->students[0];
        $attemptA = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $studentA->id,
            'started_at' => now(),
        ]);
        echo "-> Siswa A ({$studentA->full_name}) memulai kuis. Attempt ID: {$attemptA->id}\n";

        // Fetch questions for Student A (Load 1)
        $qA_1 = $quiz->questions()->inRandomOrder($attemptA->id)->get();
        echo "✓ Load 1 Question IDs for Student A: " . implode(', ', $qA_1->pluck('id')->toArray()) . "\n";

        // Fetch options for Question 1 under Student A (Load 1)
        $optA_1 = $qA_1[0]->getShuffledOptions($attemptA->id);
        echo "✓ Question 1 Shuffled Options for Student A: " . implode(', ', array_map(fn($o) => $o['key'], $optA_1)) . "\n";

        // Fetch questions for Student A (Load 2 - simulate Page Refresh)
        $qA_2 = $quiz->questions()->inRandomOrder($attemptA->id)->get();
        echo "-> Siswa A melakukan refresh halaman kuis...\n";
        echo "✓ Load 2 Question IDs for Student A: " . implode(', ', $qA_2->pluck('id')->toArray()) . "\n";

        $optA_2 = $qA_2[0]->getShuffledOptions($attemptA->id);
        echo "✓ Question 1 Shuffled Options for Student A: " . implode(', ', array_map(fn($o) => $o['key'], $optA_2)) . "\n";

        // Assert determinism
        if ($qA_1->pluck('id')->toArray() === $qA_2->pluck('id')->toArray() && $optA_1 === $optA_2) {
            echo "🔥 SUCCESS: Shuffling is completely STABLE & DETERMINISTIC on page refresh for the same student!\n";
        } else {
            echo "❌ ERROR: Shuffling is unstable on page refresh!\n";
        }

        // 4. Student B starts attempt
        $studentB = $this->students[1];
        $attemptB = LmsQuizAttempt::create([
            'quiz_id' => $quiz->id,
            'student_id' => $studentB->id,
            'started_at' => now(),
        ]);
        echo "\n-> Siswa B ({$studentB->full_name}) memulai kuis. Attempt ID: {$attemptB->id}\n";

        // Fetch questions for Student B
        $qB = $quiz->questions()->inRandomOrder($attemptB->id)->get();
        echo "✓ Question IDs for Student B: " . implode(', ', $qB->pluck('id')->toArray()) . "\n";

        // Fetch options for Question 1 under Student B
        $optB = $quiz->questions()->first()->getShuffledOptions($attemptB->id);
        echo "✓ Question 1 Shuffled Options for Student B: " . implode(', ', array_map(fn($o) => $o['key'], $optB)) . "\n";

        // Verify Student A vs Student B order are randomized/different
        if ($qA_1->pluck('id')->toArray() !== $qB->pluck('id')->toArray()) {
            echo "🔥 SUCCESS: Questions are randomized differently between Student A and Student B!\n";
        } else {
            echo "⚠️ INFO: Question orders matched by random chance (unlikely) or seed is not functioning.\n";
        }
    }

    private function testPoint4_ClassroomAvatarsAndAnalytics()
    {
        $this->printSection("POINT 4: Classroom Avatar Premium & Analytics");

        // 1. Verify classroom avatar visual fix for XI Sudirman
        $classroom = Classroom::where('class_name', 'XI Sudirman')->first();
        $avatarConfig = $classroom->getAvatarConfig();

        echo "-> Menghitung visual avatar untuk kelas: {$classroom->class_name}...\n";
        echo "✓ Avatar Initials: {$avatarConfig['initials']}\n";
        echo "✓ Avatar Gradient Classes: {$avatarConfig['gradient']}\n";
        echo "✓ Avatar Ring Border Classes: {$avatarConfig['ring']}\n";
        echo "✓ Avatar SVG Icon: " . (empty($avatarConfig['icon']) ? 'Fallback' : 'Icon Present (length: ' . strlen($avatarConfig['icon']) . ')') . "\n";

        if (strpos($avatarConfig['gradient'], 'from-green-600') !== false) {
            echo "🔥 SUCCESS: XI Sudirman visual avatar resolved with high-contrast gradient (Green/Emerald)!\n";
        } else {
            echo "❌ ERROR: XI Sudirman visual avatar is not correct.\n";
        }

        // 2. Verify Kejuruan SMKS Pembda Nias avatars based on Kejuruan keyword DPIB
        $classroomDPIB = Classroom::create([
            'school_id' => 3, // SMKS
            'academic_year_id' => 1,
            'class_code' => 'X-DPIB',
            'class_name' => 'X DPIB',
            'grade_level' => 10,
            'is_active' => true,
        ]);
        $avatarDPIB = $classroomDPIB->getAvatarConfig();
        echo "\n-> Menghitung visual avatar untuk kelas SMK: {$classroomDPIB->class_name}...\n";
        echo "✓ DPIB Initials: {$avatarDPIB['initials']}\n";
        echo "✓ DPIB Gradient: {$avatarDPIB['gradient']}\n";
        echo "✓ DPIB Vocational Icon length: " . strlen($avatarDPIB['icon']) . "\n";

        if (strpos($avatarDPIB['icon'], 'M3 21h18') !== false) {
            echo "🔥 SUCCESS: SMK DPIB class resolved with architectural drawing SVG paths!\n";
        } else {
            echo "❌ ERROR: SMK DPIB class did not resolve with architectural SVG path.\n";
        }
    }

    private function printHeader($text)
    {
        echo "\n" . str_repeat("=", 80) . "\n";
        echo "  " . $text . "\n";
        echo str_repeat("=", 80) . "\n";
    }

    private function printSection($text)
    {
        echo "\n" . str_repeat("-", 60) . "\n";
        echo "  " . $text . "\n";
        echo str_repeat("-", 60) . "\n";
    }

    private function printError($text)
    {
        echo "\n\033[31m[ERROR] " . $text . "\033[0m\n";
    }
}

// Run the simulation
$tester = new SimulationTester();
$tester->run();
