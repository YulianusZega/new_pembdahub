<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module 4: CBT (Computer Based Test)
 *
 * Tables:
 * - cbt_exams: Master ujian/test
 * - cbt_question_banks: Bank soal per mata pelajaran
 * - cbt_questions: Soal individual
 * - cbt_question_options: Opsi jawaban (multiple choice)
 * - cbt_exam_participants: Peserta ujian per kelas
 * - cbt_exam_sessions: Sesi ujian (waktu mulai/selesai per peserta)
 * - cbt_answers: Jawaban peserta
 * - cbt_exam_results: Hasil ujian per peserta
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Question Banks - organized by subject/teacher
        Schema::create('cbt_question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');

            $table->string('bank_name')->comment('Nama bank soal');
            $table->text('description')->nullable();
            $table->enum('grade_level', ['7', '8', '9', '10', '11', '12'])
                ->comment('Tingkat kelas');
            $table->integer('total_questions')->default(0)->comment('Counter cache');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_shared')->default(false)->comment('Shared with other teachers');
            $table->timestamps();

            $table->index(['school_id', 'subject_id'], 'idx_cqb_school_subject');
            $table->index(['teacher_id', 'is_active'], 'idx_cqb_teacher_active');
        });

        // 2. Questions - the actual questions
        Schema::create('cbt_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')
                ->constrained('cbt_question_banks')->onDelete('cascade');

            $table->enum('question_type', [
                'multiple_choice',   // Pilihan ganda (A/B/C/D/E)
                'true_false',        // Benar/Salah
                'essay',             // Esai
                'fill_blank',        // Isian singkat
            ])->default('multiple_choice');

            $table->text('question_text')->comment('Isi soal (supports HTML/rich text)');
            $table->string('question_image')->nullable()->comment('Gambar soal jika ada');
            $table->text('explanation')->nullable()->comment('Pembahasan jawaban');
            $table->integer('points')->default(1)->comment('Bobot nilai soal');

            $table->enum('difficulty', ['mudah', 'sedang', 'sulit'])->default('sedang');
            $table->string('topic')->nullable()->comment('Topik/bab');
            $table->string('competency')->nullable()->comment('Kompetensi dasar terkait');

            // For essay grading
            $table->text('answer_key')->nullable()->comment('Kunci jawaban (essay/fill_blank)');
            $table->integer('max_words')->nullable()->comment('Max word limit for essay');

            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['question_bank_id', 'question_type'], 'idx_cq_bank_type');
            $table->index(['question_bank_id', 'difficulty'], 'idx_cq_bank_difficulty');
            $table->index('is_active', 'idx_cq_active');
        });

        // 3. Question Options - for multiple choice questions
        Schema::create('cbt_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')
                ->constrained('cbt_questions')->onDelete('cascade');

            $table->char('option_label', 1)->comment('A, B, C, D, E');
            $table->text('option_text')->comment('Isi opsi jawaban');
            $table->string('option_image')->nullable();
            $table->boolean('is_correct')->default(false)->comment('Apakah ini jawaban benar');
            $table->integer('sort_order')->default(0);

            $table->index(['question_id', 'is_correct'], 'idx_cqo_question_correct');
        });

        // 4. Exams - the actual test/ujian
        Schema::create('cbt_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');

            $table->string('exam_title')->comment('Judul ujian');
            $table->text('exam_description')->nullable();

            $table->enum('exam_type', ['tugas', 'quiz', 'uts', 'uas', 'remedial', 'tryout'])
                ->comment('Jenis ujian - integrates with grade_type');

            $table->enum('status', ['draft', 'published', 'active', 'completed', 'archived'])
                ->default('draft');

            // Scheduling
            $table->datetime('start_time')->nullable()->comment('Waktu mulai ujian');
            $table->datetime('end_time')->nullable()->comment('Waktu selesai ujian');
            $table->integer('duration_minutes')->comment('Durasi pengerjaan (menit)');

            // Question configuration
            $table->integer('total_questions_shown')->comment('Jumlah soal yang ditampilkan');
            $table->boolean('randomize_questions')->default(true)->comment('Acak urutan soal');
            $table->boolean('randomize_options')->default(true)->comment('Acak urutan opsi');
            $table->boolean('show_result')->default(false)->comment('Tampilkan hasil langsung');
            $table->boolean('show_answer_key')->default(false)->comment('Tampilkan kunci jawaban');
            $table->boolean('allow_review')->default(false)->comment('Izinkan review sebelum submit');

            // Scoring
            $table->float('passing_score')->default(75)->comment('Nilai KKM/passing');
            $table->integer('max_attempts')->default(1)->comment('Maksimal percobaan');

            // Security
            $table->string('access_code')->nullable()->comment('Kode akses ujian');
            $table->boolean('prevent_tab_switch')->default(true)->comment('Deteksi berpindah tab');
            $table->boolean('prevent_copy_paste')->default(true)->comment('Blokir copy paste');

            // Grade integration
            $table->boolean('auto_sync_grade')->default(true)
                ->comment('Auto sync score to grades table');

            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->index(['school_id', 'exam_type', 'status'], 'idx_ce_school_type_status');
            $table->index(['teacher_id', 'subject_id'], 'idx_ce_teacher_subject');
            $table->index(['start_time', 'end_time'], 'idx_ce_schedule');
            $table->index('status', 'idx_ce_status');
        });

        // 5. Exam-QuestionBank pivot  (which banks are used in an exam)
        Schema::create('cbt_exam_question_bank', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('cbt_exams')->onDelete('cascade');
            $table->foreignId('question_bank_id')
                ->constrained('cbt_question_banks')->onDelete('cascade');
            $table->integer('questions_to_pick')->comment('Jumlah soal yang diambil dari bank ini');

            $table->unique(['exam_id', 'question_bank_id'], 'uq_ceqb_exam_bank');
        });

        // 6. Exam-specific questions (selected/assigned questions for the exam)
        Schema::create('cbt_exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('cbt_exams')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('cbt_questions')->onDelete('cascade');
            $table->integer('sort_order')->default(0)->comment('Fixed order for non-random');
            $table->integer('points_override')->nullable()->comment('Override bobot soal');

            $table->unique(['exam_id', 'question_id'], 'uq_ceq_exam_question');
            $table->index(['exam_id', 'sort_order'], 'idx_ceq_exam_order');
        });

        // 7. Exam Participants - who can take the exam (per classroom)
        Schema::create('cbt_exam_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('cbt_exams')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');

            $table->unique(['exam_id', 'classroom_id'], 'uq_cep_exam_class');
        });

        // 8. Exam Sessions - individual student attempt
        Schema::create('cbt_exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('cbt_exams')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');

            $table->integer('attempt_number')->default(1);
            $table->datetime('started_at')->nullable();
            $table->datetime('finished_at')->nullable();
            $table->datetime('deadline_at')->nullable()->comment('Personal deadline (start + duration)');

            $table->enum('status', ['not_started', 'in_progress', 'submitted', 'timeout', 'graded'])
                ->default('not_started');

            // Question order for this student (randomized)
            $table->json('question_order')->nullable()
                ->comment('JSON array of question IDs in display order');
            $table->json('option_orders')->nullable()
                ->comment('JSON object {question_id: [option_order]} for randomized options');

            // Anti-cheat
            $table->integer('tab_switch_count')->default(0);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();

            $table->unique(['exam_id', 'student_id', 'attempt_number'], 'uq_ces_exam_student_attempt');
            $table->index(['exam_id', 'status'], 'idx_ces_exam_status');
            $table->index(['student_id', 'status'], 'idx_ces_student_status');
        });

        // 9. Student Answers
        Schema::create('cbt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                ->constrained('cbt_exam_sessions')->onDelete('cascade');
            $table->foreignId('question_id')
                ->constrained('cbt_questions')->onDelete('cascade');

            $table->char('selected_option', 1)->nullable()->comment('A/B/C/D/E for MC');
            $table->text('text_answer')->nullable()->comment('For essay/fill_blank');
            $table->boolean('is_correct')->nullable()->comment('NULL until graded');
            $table->float('score_obtained')->default(0)->comment('Points earned');
            $table->boolean('is_flagged')->default(false)->comment('Student flagged for review');

            // For essay grading
            $table->float('manual_score')->nullable()->comment('Manual grade by teacher');
            $table->text('teacher_feedback')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('graded_at')->nullable();

            $table->integer('time_spent_seconds')->default(0)->comment('Waktu per soal');
            $table->timestamps();

            $table->unique(['session_id', 'question_id'], 'uq_ca_session_question');
            $table->index(['session_id', 'is_correct'], 'idx_ca_session_correct');
        });

        // 10. Exam Results - aggregated per student per exam
        Schema::create('cbt_exam_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('cbt_exams')->onDelete('cascade');
            $table->foreignId('session_id')
                ->constrained('cbt_exam_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            $table->integer('total_questions')->default(0);
            $table->integer('answered_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('wrong_answers')->default(0);
            $table->integer('unanswered')->default(0);

            $table->float('total_score')->default(0)->comment('Total points earned');
            $table->float('max_score')->default(0)->comment('Maximum possible score');
            $table->float('percentage_score')->default(0)->comment('Score as percentage');
            $table->float('final_score')->default(0)->comment('Normalized 0-100 score');

            $table->boolean('is_passed')->default(false);
            $table->string('predicate', 2)->nullable()->comment('A/B/C/D');
            $table->integer('rank')->nullable()->comment('Rank among participants');

            $table->integer('time_spent_seconds')->default(0)->comment('Total time used');
            $table->boolean('grade_synced')->default(false)->comment('Already synced to grades table');
            $table->foreignId('synced_grade_id')->nullable()
                ->constrained('grades')->nullOnDelete()
                ->comment('Link to grades table entry');

            $table->timestamps();

            $table->unique(['exam_id', 'student_id', 'session_id'], 'uq_cer_exam_student_session');
            $table->index(['exam_id', 'final_score'], 'idx_cer_exam_score');
            $table->index(['student_id', 'is_passed'], 'idx_cer_student_passed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cbt_exam_results');
        Schema::dropIfExists('cbt_answers');
        Schema::dropIfExists('cbt_exam_sessions');
        Schema::dropIfExists('cbt_exam_participants');
        Schema::dropIfExists('cbt_exam_questions');
        Schema::dropIfExists('cbt_exam_question_bank');
        Schema::dropIfExists('cbt_exams');
        Schema::dropIfExists('cbt_question_options');
        Schema::dropIfExists('cbt_questions');
        Schema::dropIfExists('cbt_question_banks');
    }
};
