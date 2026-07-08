<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\StudentClass;
use App\Models\Schedule;
use App\Models\Attendance;
use App\Models\StudentCounselingRecord;
use App\Models\CounselingParticipant;
use App\Models\StudentRecommendation;
use App\Models\StudentDevelopmentNote;
use App\Models\Reputation;
use App\Models\ReputationLog;
use App\Models\Badge;
use App\Models\UserBadge;
use App\Models\LmsCourse;
use App\Models\LmsClass;
use App\Models\LmsModule;
use App\Models\LmsMaterial;
use App\Models\LmsMaterialProgress;
use App\Models\LmsAssignment;
use App\Models\LmsSubmission;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizAnswer;
use App\Models\LmsAnnouncement;
use App\Models\LmsDiscussion;
use App\Models\LmsDiscussionReply;
use App\Models\LmsEnrollment;
use App\Models\CbtQuestionBank;
use App\Models\CbtQuestion;
use App\Models\CbtQuestionOption;
use App\Models\CbtExam;
use App\Models\CbtExamParticipant;
use App\Models\CbtExamQuestion;
use App\Models\CbtExamSession;
use App\Models\CbtAnswer;
use App\Models\CbtExamResult;
use App\Models\Grade;
use App\Models\FinalGrade;
use App\Models\ReportCard;
use App\Models\User;
use App\Models\GradeWeight;

class ComprehensiveSimulationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting Comprehensive Simulation Seeder...');

        $academicYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $semester = Semester::where('is_active', true)->first() ?? Semester::first();

        if (!$academicYear || !$semester) {
            $this->command->error('❌ No active Academic Year or Semester found. Please seed basic data first.');
            return;
        }

        $schools = School::all();
        if ($schools->isEmpty()) {
            $this->command->error('❌ No schools found. Please seed basic data first.');
            return;
        }

        $faker = \Faker\Factory::create('id_ID');

        // Wrap dalam transaksi database agar I/O disk sangat cepat (100x lebih cepat)
        // dan menghindari koneksi web server timeout di hosting
        DB::beginTransaction();
        try {
            // Step 1: Generate attendance logs (attendances) for the past 14 days
            $this->seedAttendance($academicYear, $semester, $faker);

            // Step 2: Generate counseling sessions, recommendations, and development notes
            $this->seedCounseling($academicYear, $semester, $faker);

            // Step 3: Populate user reputations, logs, and badge assignments
            $this->seedReputations($faker);

            // Step 4: Simulate LMS course activity
            $this->seedLmsActivity($academicYear, $semester, $faker);

            // Step 5: Simulate CBT online exams
            $this->seedCbtExams($academicYear, $semester, $faker);

            // Step 6: Generate main grades and calculate report card grades
            $this->seedGradesAndReportCards($academicYear, $semester);

            DB::commit();
            $this->command->info('🎉 Comprehensive Simulation Seeder finished successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error during seeding: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Step 1: Seed Attendance Logs
     */
    private function seedAttendance($academicYear, $semester, $faker)
    {
        $this->command->info('📅 Seeding student attendances...');

        $classrooms = Classroom::all();
        $count = 0;

        foreach ($classrooms as $classroom) {
            $students = Student::whereHas('studentClasses', function ($q) use ($classroom) {
                $q->where('classroom_id', $classroom->id)->where('status', 'aktif');
            })->get();

            if ($students->isEmpty()) {
                continue;
            }

            // Find or create a schedule for this classroom
            $schedule = Schedule::where('classroom_id', $classroom->id)->first();
            if (!$schedule) {
                $subject = Subject::where('school_id', $classroom->school_id)->first();
                $teacher = Teacher::where('school_id', $classroom->school_id)->first();
                if ($subject && $teacher) {
                    $schedule = Schedule::create([
                        'classroom_id' => $classroom->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'semester_id' => $semester->id,
                        'day_of_week' => 1,
                        'start_time' => '07:30:00',
                        'end_time' => '09:00:00',
                    ]);
                }
            }

            // Loop 14 days back
            for ($i = 0; $i < 14; $i++) {
                $date = Carbon::now()->subDays($i);
                if ($date->isWeekend()) {
                    continue;
                }

                foreach ($students as $student) {
                    // Check if already has attendance for this day
                    $exists = Attendance::where('student_id', $student->id)
                        ->where('date', $date->format('Y-m-d'))
                        ->exists();
                    if ($exists) {
                        continue;
                    }

                    $rand = rand(1, 100);
                    if ($rand <= 88) {
                        $status = 'hadir';
                    } elseif ($rand <= 93) {
                        $status = 'sakit';
                    } elseif ($rand <= 97) {
                        $status = 'izin';
                    } else {
                        $status = 'alpha';
                    }

                    $timeIn = null;
                    $timeOut = null;
                    $recordedVia = 'manual';

                    if ($status === 'hadir') {
                        $timeIn = '07:' . sprintf('%02d', rand(0, 14)) . ':' . sprintf('%02d', rand(0, 59));
                        $timeOut = '13:' . sprintf('%02d', rand(0, 15)) . ':' . sprintf('%02d', rand(0, 59));
                        $recordedVia = $faker->randomElement(['rfid', 'manual', 'qr_gps']);
                    }

                    Attendance::create([
                        'student_id' => $student->id,
                        'classroom_id' => $classroom->id,
                        'schedule_id' => $schedule ? $schedule->id : null,
                        'date' => $date->format('Y-m-d'),
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'status' => $status,
                        'recorded_via' => $recordedVia,
                        'notes' => $status === 'sakit' ? 'Sakit demam/flu' : ($status === 'izin' ? 'Izin keluarga' : null),
                        'created_by' => 1,
                    ]);
                    $count++;
                }
            }
        }
        $this->command->info("   Created {$count} attendance records.");
    }

    /**
     * Step 2: Seed Counseling (BK) Records
     */
    private function seedCounseling($academicYear, $semester, $faker)
    {
        $this->command->info('🏫 Seeding counseling and student development logs...');

        $schools = School::all();

        foreach ($schools as $school) {
            $counselor = User::where('school_id', $school->id)->where('role', 'guru')->first() 
                ?? User::where('school_id', $school->id)->where('role', 'admin_sekolah')->first()
                ?? User::where('role', 'superadmin')->first();

            if (!$counselor) {
                continue;
            }

            $students = Student::where('school_id', $school->id)->limit(3)->get();
            if ($students->isEmpty()) {
                continue;
            }

            foreach ($students as $student) {
                $studentClass = StudentClass::where('student_id', $student->id)->where('status', 'aktif')->first();
                $classroomId = $studentClass ? $studentClass->classroom_id : null;

                // 1. Create a Pelanggaran record (Infraction)
                $infractionRecord = StudentCounselingRecord::create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'record_type' => 'pelanggaran',
                    'category' => 'kedisiplinan',
                    'severity' => $faker->randomElement(['ringan', 'sedang']),
                    'title' => 'Terlambat Masuk Kelas & Atribut Tidak Lengkap',
                    'description' => 'Siswa datang terlambat 20 menit pada jam pelajaran pertama dan tidak mengenakan dasi sekolah.',
                    'background' => 'Siswa mengaku bangun kesiangan karena bermain game hingga larut malam.',
                    'action_taken' => 'Siswa diberikan teguran lisan, menulis surat pernyataan, dan dibina untuk disiplin waktu.',
                    'result' => 'Siswa menunjukkan penyesalan dan berjanji untuk tidur lebih awal.',
                    'follow_up' => 'Wali kelas akan memantau kedatangan siswa selama 1 minggu ke depan.',
                    'incident_date' => Carbon::now()->subDays(rand(2, 10)),
                    'location' => 'Gerbang Sekolah & Ruang BK',
                    'parent_notified' => true,
                    'parent_notified_date' => Carbon::now()->subDays(rand(1, 2)),
                    'parent_response' => 'Orang tua berterima kasih atas laporannya dan akan mengawasi jam tidur anak di rumah.',
                    'status' => 'resolved',
                    'resolved_date' => Carbon::now()->subDays(1),
                    'counselor_id' => $counselor->id,
                    'is_confidential' => false,
                ]);

                // Add participant
                CounselingParticipant::create([
                    'counseling_record_id' => $infractionRecord->id,
                    'user_id' => $counselor->id,
                    'role' => 'guru_bk',
                    'notes' => 'Telah memberikan pembinaan dasar kedisiplinan.',
                ]);

                if ($classroomId) {
                    $classroom = Classroom::find($classroomId);
                    if ($classroom && $classroom->homeroom_teacher_id) {
                        $homeroomTeacher = Teacher::find($classroom->homeroom_teacher_id);
                        if ($homeroomTeacher && $homeroomTeacher->user_id) {
                            CounselingParticipant::create([
                                'counseling_record_id' => $infractionRecord->id,
                                'user_id' => $homeroomTeacher->user_id,
                                'role' => 'wali_kelas',
                                'notes' => 'Akan terus mengawasi kehadiran siswa di kelas.',
                            ]);
                        }
                    }
                }

                // 2. Create a Penghargaan record (Achievement)
                StudentCounselingRecord::create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'record_type' => 'penghargaan',
                    'category' => $faker->randomElement(['olahraga', 'seni', 'keagamaan']),
                    'achievement_level' => $faker->randomElement(['kabupaten', 'sekolah']),
                    'title' => 'Juara 1 Turnamen Futsal / Kompetisi Seni Regional',
                    'description' => 'Siswa berhasil meraih prestasi luar biasa sebagai Juara Terbaik dalam kompetisi pelajar mewakili sekolah.',
                    'action_taken' => 'Sekolah memberikan sertifikat apresiasi dan piagam penghargaan saat upacara bendera.',
                    'result' => 'Siswa merasa termotivasi untuk terus berprestasi dan mengharumkan nama sekolah.',
                    'incident_date' => Carbon::now()->subDays(rand(5, 15)),
                    'status' => 'resolved',
                    'resolved_date' => Carbon::now()->subDays(5),
                    'counselor_id' => $counselor->id,
                ]);

                // 3. Create Recommendations
                StudentRecommendation::create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'counseling_record_id' => $infractionRecord->id,
                    'recommender_role' => 'guru_bk',
                    'recommended_by' => $counselor->id,
                    'category' => 'perilaku',
                    'title' => 'Rekomendasi Bimbingan Kedisiplinan Mandiri',
                    'description' => 'Siswa disarankan untuk mengikuti sesi konseling kelompok terarah untuk meningkatkan manajemen waktu.',
                    'expected_outcome' => 'Siswa tidak terlambat lagi dan dapat membagi waktu belajar dengan bermain.',
                    'priority' => 'sedang',
                    'status' => 'in_progress',
                    'target_date' => Carbon::now()->addDays(14),
                ]);

                // 4. Create development notes
                StudentDevelopmentNote::create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'aspect' => 'sikap',
                    'observation' => 'Secara umum siswa sopan dan aktif berinteraksi sosial di kelas, namun perlu pendampingan dalam hal kedisiplinan kehadiran.',
                    'progress' => 'Ada perubahan positif setelah sesi konseling pertama, siswa mulai hadir tepat waktu.',
                    'challenges' => 'Kebiasaan tidur larut malam yang harus diubah secara konsisten.',
                    'suggestion' => 'Orang tua disarankan membatasi waktu penggunaan HP pada malam hari maksimal pukul 21:00.',
                    'noted_by' => $counselor->id,
                    'noted_by_role' => 'guru_bk',
                ]);

                StudentDevelopmentNote::create([
                    'student_id' => $student->id,
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'aspect' => 'akademik',
                    'observation' => 'Siswa memiliki kecerdasan yang baik dan berpartisipasi aktif dalam pelajaran diskusi.',
                    'progress' => 'Nilai harian stabil di atas KKM.',
                    'noted_by' => $counselor->id,
                    'noted_by_role' => 'guru_bk',
                ]);
            }
        }
    }

    /**
     * Step 3: Seed Reputation Point System
     */
    private function seedReputations($faker)
    {
        $this->command->info('🏆 Seeding student reputation points and badges...');

        $students = Student::all();
        $badges = Badge::all();
        if ($badges->isEmpty()) {
            $badgeSeeder = new ReputationBadgeSeeder();
            $badgeSeeder->run();
            $badges = Badge::all();
        }

        $count = 0;
        foreach ($students as $student) {
            $user = $student->user;
            if (!$user) {
                continue;
            }

            $reputation = Reputation::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'total_points' => 0,
                    'level_name' => 'Newbie',
                ]
            );

            $logs = [
                [
                    'points' => 50,
                    'category' => 'attendance',
                    'description' => 'Kehadiran 100% di kelas minggu ini',
                ],
                [
                    'points' => 30,
                    'category' => 'lms',
                    'description' => 'Menyelesaikan Modul 1 Pembelajaran',
                ],
                [
                    'points' => 100,
                    'category' => 'exam',
                    'description' => 'Mendapatkan nilai sempurna pada Ujian Harian',
                ],
            ];

            if (rand(0, 10) > 7) {
                $logs[] = [
                    'points' => -15,
                    'category' => 'counseling',
                    'description' => 'Catatan pelanggaran kedisiplinan: terlambat masuk kelas',
                ];
            }

            if (rand(0, 10) > 6) {
                $logs[] = [
                    'points' => 150,
                    'category' => 'achievement',
                    'description' => 'Penghargaan: Juara 1 Turnamen Olahraga/Seni Sekolah',
                ];
            }

            $totalPoints = 0;
            foreach ($logs as $logData) {
                ReputationLog::create([
                    'user_id' => $user->id,
                    'points' => $logData['points'],
                    'category' => $logData['category'],
                    'description' => $logData['description'],
                    'created_at' => Carbon::now()->subDays(rand(1, 10)),
                ]);
                $totalPoints += $logData['points'];
            }

            $levelName = 'Newbie';
            if ($totalPoints >= 300) {
                $levelName = 'Platinum';
            } elseif ($totalPoints >= 200) {
                $levelName = 'Gold';
            } elseif ($totalPoints >= 100) {
                $levelName = 'Silver';
            } elseif ($totalPoints >= 50) {
                $levelName = 'Bronze';
            }

            $reputation->update([
                'total_points' => $totalPoints,
                'level_name' => $levelName,
            ]);

            foreach ($badges as $badge) {
                if ($totalPoints >= $badge->requirement_value) {
                    UserBadge::firstOrCreate([
                        'user_id' => $user->id,
                        'badge_id' => $badge->id,
                    ], [
                        'earned_at' => Carbon::now()->subDays(rand(0, 2)),
                    ]);
                }
            }
            $count++;
        }
        $this->command->info("   Seeded reputations and badges for {$count} students.");
    }

    /**
     * Step 4: Simulate LMS Course Activity
     */
    private function seedLmsActivity($academicYear, $semester, $faker)
    {
        $this->command->info('📚 Seeding LMS activities (announcements, discussions, submissions, quizzes)...');

        $courses = LmsCourse::all();
        if ($courses->isEmpty()) {
            $this->command->warn('   No LMS courses found. Running LmsSeeder first...');
            $lmsSeeder = new LmsSeeder();
            $lmsSeeder->run();
            $courses = LmsCourse::all();
        }

        $announcementCount = 0;
        $discussionCount = 0;
        $progressCount = 0;
        $submissionCount = 0;
        $quizAttemptCount = 0;

        foreach ($courses as $course) {
            $teacherUser = User::find($course->teacher->user_id ?? $course->teacher_id);
            if (!$teacherUser) {
                continue;
            }

            // 1. Course Announcement
            LmsAnnouncement::create([
                'course_id' => $course->id,
                'user_id' => $teacherUser->id,
                'title' => 'Pengumuman Penting: Materi Tambahan & Tugas Mandiri',
                'content' => 'Halo semuanya, silakan baca materi Bab 1 dan kumpulkan tugas sebelum tenggat waktu. Jika ada hal yang kurang dipahami silakan diskusikan di forum.',
                'is_pinned' => true,
                'is_published' => true,
                'published_at' => Carbon::now()->subDays(2),
            ]);
            $announcementCount++;

            // 2. Course Discussion Forum
            $discussion = LmsDiscussion::create([
                'course_id' => $course->id,
                'user_id' => $teacherUser->id,
                'title' => 'Forum Diskusi Bab 1: Tanya Jawab Materi',
                'content' => 'Gunakan ruang ini untuk mendiskusikan poin-poin sulit dari materi Bab 1. Tanyakan di sini jika ada kendala pengerjaan tugas.',
                'type' => 'discussion',
                'is_pinned' => false,
                'is_locked' => false,
                'is_resolved' => false,
                'replies_count' => 3,
                'last_reply_at' => Carbon::now()->subHours(5),
            ]);
            $discussionCount++;

            // Get enrolled students
            $enrollments = LmsEnrollment::where('lms_class_id', function ($q) use ($course) {
                $q->select('id')->from('lms_classes')->where('course_id', $course->id)->limit(1);
            })->get();

            if ($enrollments->isEmpty()) {
                $lmsClass = LmsClass::where('course_id', $course->id)->first();
                if ($lmsClass) {
                    $studentIds = StudentClass::where('classroom_id', $lmsClass->classroom_id)
                        ->where('status', 'aktif')
                        ->pluck('student_id');
                    foreach ($studentIds as $studentId) {
                        LmsEnrollment::firstOrCreate([
                            'lms_class_id' => $lmsClass->id,
                            'student_id' => $studentId,
                        ], [
                            'status' => 'enrolled',
                            'enrolled_at' => now(),
                        ]);
                    }
                    $enrollments = LmsEnrollment::where('lms_class_id', $lmsClass->id)->get();
                }
            }

            $materials = LmsMaterial::where('course_id', $course->id)->get();
            $assignments = LmsAssignment::where('course_id', $course->id)->get();
            $quizzes = LmsQuiz::where('course_id', $course->id)->with('questions')->get();

            foreach ($enrollments as $enrollment) {
                $student = Student::find($enrollment->student_id);
                if (!$student) continue;

                // 3. Material Progress Tracking
                foreach ($materials as $material) {
                    LmsMaterialProgress::updateOrCreate(
                        [
                            'material_id' => $material->id,
                            'student_id' => $student->id,
                        ],
                        [
                            'status' => 'completed',
                            'progress_percent' => 100,
                            'time_spent_seconds' => rand(120, 600),
                            'first_viewed_at' => Carbon::now()->subDays(5),
                            'completed_at' => Carbon::now()->subDays(4),
                        ]
                    );
                    $progressCount++;
                }

                // 4. Assignments Submissions & Grading
                foreach ($assignments as $assignment) {
                    $submission = LmsSubmission::updateOrCreate(
                        [
                            'assignment_id' => $assignment->id,
                            'student_id' => $student->id,
                        ],
                        [
                            'submission_text' => 'Tugas telah saya selesaikan pak/bu. Berikut jawaban atas soal-soal latihan Bab 1 yang saya kerjakan berdasarkan materi pdf di atas. Mohon feedbacknya.',
                            'status' => 'graded',
                            'score' => rand(80, 98),
                            'feedback' => 'Sangat bagus. Analisis tajam dan rapi!',
                            'submitted_at' => Carbon::now()->subDays(3),
                            'graded_at' => Carbon::now()->subDays(2),
                            'graded_by' => $teacherUser->id,
                        ]
                    );

                    // Sync to main grades table
                    Grade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $course->subject_id,
                            'semester_id' => $course->semester_id ?? $semester->id,
                            'grade_type' => 'tugas',
                            'lms_source_type' => 'submission',
                            'lms_source_id' => $submission->id,
                        ],
                        [
                            'teacher_id' => $course->teacher_id,
                            'score' => $submission->score,
                            'is_remedial' => false,
                            'notes' => 'LMS Tugas: ' . $assignment->title,
                            'created_by' => $teacherUser->id,
                        ]
                    );
                    $submissionCount++;
                }

                // 5. Quizzes Attempts & Grading
                foreach ($quizzes as $quiz) {
                    $attempt = LmsQuizAttempt::create([
                        'quiz_id' => $quiz->id,
                        'student_id' => $student->id,
                        'started_at' => Carbon::now()->subDays(2)->subMinutes(30),
                        'finished_at' => Carbon::now()->subDays(2),
                        'score' => 0, // Will update below
                        'is_passed' => true,
                    ]);

                    $totalScore = 0;
                    $maxTotal = 0;

                    foreach ($quiz->questions as $question) {
                        $isCorrect = rand(1, 10) > 3; // 70% correct
                        $points = $isCorrect ? $question->score : 0;
                        $totalScore += $points;
                        $maxTotal += $question->score;

                        LmsQuizAnswer::create([
                            'attempt_id' => $attempt->id,
                            'question_id' => $question->id,
                            'answer' => $isCorrect ? $question->correct_answer : 'Jawaban salah',
                            'is_correct' => $isCorrect,
                            'score' => $points,
                        ]);
                    }

                    $percentage = $maxTotal > 0 ? ($totalScore / $maxTotal) * 100 : 80;
                    $attempt->update([
                        'score' => round($percentage, 2),
                        'is_passed' => $percentage >= $quiz->passing_score,
                    ]);

                    // Sync quiz score to main grades table
                    Grade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $course->subject_id,
                            'semester_id' => $course->semester_id ?? $semester->id,
                            'grade_type' => 'tugas',
                            'lms_source_type' => 'quiz_attempt',
                            'lms_source_id' => $attempt->id,
                        ],
                        [
                            'teacher_id' => $course->teacher_id,
                            'score' => round($percentage, 2),
                            'is_remedial' => false,
                            'notes' => 'LMS Kuis: ' . $quiz->title,
                            'created_by' => $teacherUser->id,
                        ]
                    );
                    $quizAttemptCount++;
                }

                // 6. Discussion replies from student
                LmsDiscussionReply::create([
                    'discussion_id' => $discussion->id,
                    'user_id' => $student->user_id,
                    'content' => 'Saya ingin bertanya pak, untuk latihan soal di Bab 1 bagian nomor 3, apakah pengerjaan harus merinci langkah-langkah rumusnya?',
                    'is_best_answer' => false,
                ]);
            }

            // Add reply from teacher
            LmsDiscussionReply::create([
                'discussion_id' => $discussion->id,
                'user_id' => $teacherUser->id,
                'content' => 'Ya, wajib dituliskan langkah pengerjaan atau jalannya rumus agar nilainya bisa maksimal.',
                'is_best_answer' => true,
            ]);
        }

        $this->command->info("   Populated LMS logs successfully.");
    }

    /**
     * Step 5: Simulate CBT Online Exams
     */
    private function seedCbtExams($academicYear, $semester, $faker)
    {
        $this->command->info('🖥️ Seeding CBT exams and mock results...');

        $schools = School::all();

        $cbtQuestionsData = [
            [
                'question_text' => 'Siapakah pendiri Kerajaan Majapahit?',
                'options' => [
                    ['text' => 'Raden Wijaya', 'correct' => true],
                    ['text' => 'Hayam Wuruk', 'correct' => false],
                    ['text' => 'Gajah Mada', 'correct' => false],
                    ['text' => 'Ken Arok', 'correct' => false],
                ]
            ],
            [
                'question_text' => 'Apakah ibukota dari Provinsi Sumatera Utara?',
                'options' => [
                    ['text' => 'Medan', 'correct' => true],
                    ['text' => 'Gunungsitoli', 'correct' => false],
                    ['text' => 'Sibolga', 'correct' => false],
                    ['text' => 'Binjai', 'correct' => false],
                ]
            ],
            [
                'question_text' => 'Berapakah hasil dari 25 x 4 + 50?',
                'options' => [
                    ['text' => '100', 'correct' => false],
                    ['text' => '150', 'correct' => true],
                    ['text' => '200', 'correct' => false],
                    ['text' => '250', 'correct' => false],
                ]
            ],
            [
                'question_text' => 'Lambang negara Indonesia adalah...',
                'options' => [
                    ['text' => 'Garuda Pancasila', 'correct' => true],
                    ['text' => 'Bintang', 'correct' => false],
                    ['text' => 'Rantai', 'correct' => false],
                    ['text' => 'Pohon Beringin', 'correct' => false],
                ]
            ],
            [
                'question_text' => 'Siapakah Presiden pertama Indonesia?',
                'options' => [
                    ['text' => 'Soeharto', 'correct' => false],
                    ['text' => 'Soekarno', 'correct' => true],
                    ['text' => 'B.J. Habibie', 'correct' => false],
                    ['text' => 'Abdurrahman Wahid', 'correct' => false],
                ]
            ]
        ];

        foreach ($schools as $school) {
            $classroom = Classroom::where('school_id', $school->id)->first();
            if (!$classroom) {
                continue;
            }

            $teacher = Teacher::where('school_id', $school->id)->first();
            $subject = Subject::where('school_id', $school->id)->first();

            if (!$teacher || !$subject) {
                continue;
            }

            $adminUser = User::where('school_id', $school->id)->where('role', 'admin_sekolah')->first()
                ?? User::where('role', 'superadmin')->first();

            // 1. Create CBT Question Bank
            $bank = CbtQuestionBank::create([
                'school_id' => $school->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $academicYear->id,
                'bank_name' => 'Bank Soal Simulasi CBT: Pengetahuan Umum',
                'description' => 'Simulasi ujian online CBT untuk testing modul.',
                'grade_level' => (string) $classroom->grade_level,
                'total_questions' => count($cbtQuestionsData),
                'is_active' => true,
                'is_shared' => true,
            ]);

            // 2. Create Questions & Options
            $questionIds = [];
            $answersMap = [];
            foreach ($cbtQuestionsData as $qData) {
                $question = CbtQuestion::create([
                    'question_bank_id' => $bank->id,
                    'question_type' => 'multiple_choice',
                    'question_text' => $qData['question_text'],
                    'points' => 20,
                    'difficulty' => 'sedang',
                    'is_active' => true,
                ]);
                $questionIds[] = $question->id;

                foreach ($qData['options'] as $oIdx => $opt) {
                    $label = chr(65 + $oIdx);
                    CbtQuestionOption::create([
                        'question_id' => $question->id,
                        'option_label' => $label,
                        'option_text' => $opt['text'],
                        'is_correct' => $opt['correct'],
                        'sort_order' => $oIdx,
                    ]);

                    if ($opt['correct']) {
                        $answersMap[$question->id] = $label;
                    }
                }
            }

            // 3. Create CBT Exam
            $exam = CbtExam::create([
                'school_id' => $school->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'exam_title' => 'Simulasi CBT Pengetahuan Umum - ' . $classroom->class_name,
                'exam_description' => 'Ujian akhir semester berbasis CBT.',
                'exam_type' => 'uas',
                'status' => 'published',
                'start_time' => Carbon::now()->subDays(1),
                'end_time' => Carbon::now()->addDays(5),
                'duration_minutes' => 60,
                'total_questions_shown' => count($cbtQuestionsData),
                'randomize_questions' => false,
                'randomize_options' => false,
                'show_result' => true,
                'show_answer_key' => true,
                'allow_review' => true,
                'passing_score' => 70,
                'max_attempts' => 1,
                'prevent_tab_switch' => false,
                'prevent_copy_paste' => false,
                'auto_sync_grade' => true,
                'created_by' => $adminUser->id,
            ]);

            // 4. Link Question Bank to Exam
            DB::table('cbt_exam_question_bank')->insert([
                'exam_id' => $exam->id,
                'question_bank_id' => $bank->id,
                'questions_to_pick' => count($cbtQuestionsData),
            ]);

            // 5. Link Exam to Classroom
            CbtExamParticipant::create([
                'exam_id' => $exam->id,
                'classroom_id' => $classroom->id,
            ]);

            // 6. Link questions to the exam
            foreach ($questionIds as $sortIdx => $qId) {
                CbtExamQuestion::create([
                    'exam_id' => $exam->id,
                    'question_id' => $qId,
                    'sort_order' => $sortIdx + 1,
                ]);
            }

            // 7. Simulate student attempts
            $students = Student::whereHas('studentClasses', function ($q) use ($classroom) {
                $q->where('classroom_id', $classroom->id)->where('status', 'aktif');
            })->get();

            foreach ($students as $student) {
                $session = CbtExamSession::create([
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'classroom_id' => $classroom->id,
                    'attempt_number' => 1,
                    'started_at' => Carbon::now()->subDays(1)->subMinutes(45),
                    'finished_at' => Carbon::now()->subDays(1),
                    'status' => 'graded',
                    'question_order' => $questionIds,
                    'ip_address' => '192.168.1.' . rand(10, 100),
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Chrome/120.0.0.0',
                ]);

                $correctCount = 0;
                $wrongCount = 0;
                $totalScore = 0;

                foreach ($questionIds as $qId) {
                    $isCorrect = rand(1, 10) > 3; // 70% chance correct
                    $correctLabel = $answersMap[$qId];
                    
                    if ($isCorrect) {
                        $selectedOption = $correctLabel;
                        $correctCount++;
                        $scoreObtained = 20;
                    } else {
                        $wrongLabels = array_values(array_filter(['A', 'B', 'C', 'D'], fn($x) => $x !== $correctLabel));
                        $selectedOption = $wrongLabels[array_rand($wrongLabels)];
                        $wrongCount++;
                        $scoreObtained = 0;
                    }

                    $totalScore += $scoreObtained;

                    CbtAnswer::create([
                        'session_id' => $session->id,
                        'question_id' => $qId,
                        'selected_option' => $selectedOption,
                        'is_correct' => $isCorrect,
                        'score_obtained' => $scoreObtained,
                    ]);
                }

                $result = CbtExamResult::create([
                    'exam_id' => $exam->id,
                    'session_id' => $session->id,
                    'student_id' => $student->id,
                    'total_questions' => count($questionIds),
                    'answered_questions' => count($questionIds),
                    'correct_answers' => $correctCount,
                    'wrong_answers' => $wrongCount,
                    'unanswered' => 0,
                    'total_score' => $totalScore,
                    'max_score' => 100,
                    'percentage_score' => $totalScore,
                    'final_score' => $totalScore,
                    'is_passed' => $totalScore >= $exam->passing_score,
                    'predicate' => $totalScore >= 90 ? 'A' : ($totalScore >= 80 ? 'B' : ($totalScore >= 70 ? 'C' : 'D')),
                ]);

                $grade = Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'semester_id' => $semester->id,
                    'grade_type' => 'uas',
                    'score' => $totalScore,
                    'is_remedial' => false,
                    'notes' => 'Nilai Ujian CBT: ' . $exam->exam_title,
                    'created_by' => $adminUser->id,
                ]);

                $result->update([
                    'grade_synced' => true,
                    'synced_grade_id' => $grade->id,
                ]);
            }
        }
    }

    /**
     * Step 6: Seed Grades and Final Report Cards (Rapor)
     */
    private function seedGradesAndReportCards($academicYear, $semester)
    {
        $this->command->info('📊 Seeding grades and report cards...');

        $classrooms = Classroom::all();

        foreach ($classrooms as $classroom) {
            $schoolId = $classroom->school_id;
            $students = Student::whereHas('studentClasses', function ($q) use ($classroom) {
                $q->where('classroom_id', $classroom->id)->where('status', 'aktif');
            })->get();

            if ($students->isEmpty()) {
                continue;
            }

            $subjects = Subject::where('school_id', $schoolId)->get();
            if ($subjects->isEmpty()) {
                continue;
            }

            $teacher = Teacher::where('school_id', $schoolId)->first();
            $teacherId = $teacher ? $teacher->id : 1;

            // Step 6A: Ensure all students have grades in each subject
            foreach ($students as $student) {
                foreach ($subjects as $subject) {
                    $existingTypes = Grade::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->where('semester_id', $semester->id)
                        ->pluck('grade_type')
                        ->toArray();

                    // Seed Tugas (needs 3)
                    $tugasCount = Grade::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->where('semester_id', $semester->id)
                        ->where('grade_type', 'tugas')
                        ->count();
                    for ($t = $tugasCount; $t < 3; $t++) {
                        Grade::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacherId,
                            'semester_id' => $semester->id,
                            'grade_type' => 'tugas',
                            'score' => rand(70, 96),
                            'is_remedial' => false,
                            'notes' => 'Tugas ' . ($t + 1),
                            'created_by' => 1,
                        ]);
                    }

                    // Seed UTS (needs 1)
                    if (!in_array('uts', $existingTypes)) {
                        Grade::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacherId,
                            'semester_id' => $semester->id,
                            'grade_type' => 'uts',
                            'score' => rand(72, 94),
                            'is_remedial' => false,
                            'created_by' => 1,
                        ]);
                    }

                    // Seed UAS (needs 1)
                    if (!in_array('uas', $existingTypes)) {
                        Grade::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacherId,
                            'semester_id' => $semester->id,
                            'grade_type' => 'uas',
                            'score' => rand(75, 95),
                            'is_remedial' => false,
                            'created_by' => 1,
                        ]);
                    }

                    // Seed Sikap (needs 1)
                    if (!in_array('sikap', $existingTypes)) {
                        Grade::create([
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacherId,
                            'semester_id' => $semester->id,
                            'grade_type' => 'sikap',
                            'score' => rand(80, 98),
                            'is_remedial' => false,
                            'created_by' => 1,
                        ]);
                    }

                    // Step 6B: Calculate and save final grades
                    $grades = Grade::where('student_id', $student->id)
                        ->where('subject_id', $subject->id)
                        ->where('semester_id', $semester->id)
                        ->get();

                    $weightsObj = GradeWeight::getForSchool($schoolId);
                    $w = $weightsObj->getWeightsAsDecimal();

                    $tugasAvg = $grades->where('grade_type', 'tugas')->avg('score') ?? 70;
                    $ptsVal = $grades->where('grade_type', 'uts')->avg('score') ?? 70;
                    $pasVal = $grades->where('grade_type', 'uas')->avg('score') ?? 70;
                    $sikapVal = $grades->where('grade_type', 'sikap')->avg('score') ?? 80;

                    $finalScore = ($tugasAvg * $w['tugas']) + ($ptsVal * $w['pts']) + ($pasVal * $w['pas']) + ($sikapVal * $w['sikap']);
                    $kkm = $subject->kkm ?? 75;
                    $isPassed = $finalScore >= $kkm;
                    $predicate = FinalGrade::scoreToPredicate($finalScore, $kkm);

                    FinalGrade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'subject_id' => $subject->id,
                            'semester_id' => $semester->id,
                        ],
                        [
                            'teacher_id' => $teacherId,
                            'tugas_score' => round($tugasAvg, 2),
                            'pts_score' => round($ptsVal, 2),
                            'pas_score' => round($pasVal, 2),
                            'sikap_score' => round($sikapVal, 2),
                            'final_score' => round($finalScore, 2),
                            'kkm' => $kkm,
                            'is_passed' => $isPassed,
                            'predicate' => $predicate,
                        ]
                    );
                }
            }

            // Step 6C: Generate Report Cards & Calculate Rank inside this Classroom
            $studentAverages = [];
            foreach ($students as $student) {
                $avg = FinalGrade::where('student_id', $student->id)
                    ->where('semester_id', $semester->id)
                    ->avg('final_score') ?? 0;
                $studentAverages[$student->id] = $avg;
            }

            arsort($studentAverages);
            $ranks = [];
            $rankVal = 1;
            foreach ($studentAverages as $studentId => $avg) {
                $ranks[$studentId] = $rankVal++;
            }

            foreach ($students as $student) {
                $attendances = Attendance::where('student_id', $student->id)
                    ->where('classroom_id', $classroom->id)
                    ->get();

                $present = $attendances->where('status', 'hadir')->count();
                $sick = $attendances->where('status', 'sakit')->count();
                $permission = $attendances->where('status', 'izin')->count();
                $absent = $attendances->where('status', 'alpha')->count();
                $totalDays = $attendances->count();

                $averageScore = $studentAverages[$student->id];
                $predicate = FinalGrade::scoreToPredicate($averageScore, 75);

                ReportCard::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'semester_id' => $semester->id,
                        'academic_year_id' => $academicYear->id,
                        'classroom_id' => $classroom->id,
                    ],
                    [
                        'average_score' => round($averageScore, 2),
                        'predicate' => $predicate,
                        'rank' => $ranks[$student->id],
                        'total_students' => $students->count(),
                        'total_days' => $totalDays,
                        'days_present' => $present,
                        'days_sick' => $sick,
                        'days_permission' => $permission,
                        'days_absent' => $absent,
                        'teacher_notes' => 'Menunjukkan perkembangan akademis yang positif dan kematangan kepribadian di kelas. Terus pertahankan kerajinan belajarmu!',
                        'principal_notes' => 'Selamat atas pencapaian belajarmu di semester ini.',
                        'status' => 'published',
                        'finalized_by' => 1,
                        'finalized_at' => now(),
                        'published_by' => 1,
                        'published_at' => now(),
                    ]
                );
            }
        }
    }
}
