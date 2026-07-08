<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ================================================================
        // 5. LEARNING MANAGEMENT SYSTEM (LMS)
        // ================================================================

        // 1. LMS Courses
        Schema::create('lms_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('classroom_id')->nullable()->constrained('classrooms')->onDelete('set null');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->string('code', 50)->nullable();
            $table->string('course_name', 200);
            $table->text('description')->nullable();
            $table->string('cover_image', 255)->nullable();
            $table->boolean('is_published')->default(false);
            $table->enum('status', ['draft', 'active', 'archived'])->default('draft');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['teacher_id', 'semester_id']);
        });

        // 2. LMS Modules (Bab/Topik dalam Course)
        Schema::create('lms_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->integer('sequence')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('course_id');
        });

        // 3. LMS Classes (Link Course ke Classroom)
        Schema::create('lms_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->enum('status', ['active', 'ended', 'archived'])->default('active');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'classroom_id']);
            $table->index('classroom_id');
        });

        // 4. LMS Enrollments (Pendaftaran Siswa)
        Schema::create('lms_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lms_class_id')->constrained('lms_classes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['enrolled', 'in_progress', 'completed', 'dropped'])->default('enrolled');
            $table->dateTime('enrolled_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['lms_class_id', 'student_id']);
            $table->index('student_id');
        });

        // 5. LMS Materials (linked directly to Course, optionally to Module)
        Schema::create('lms_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->unsignedBigInteger('module_id')->nullable();
            $table->string('title', 200);
            $table->text('content')->nullable();
            $table->enum('material_type', ['pdf', 'document', 'video', 'text', 'image', 'link']);
            $table->string('file_path', 255)->nullable();
            $table->text('file_url')->nullable();
            $table->integer('file_size')->nullable();
            $table->integer('order_number')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('course_id');
            $table->index('order_number');
        });

        // 6. LMS Assignments (Tugas)
        Schema::create('lms_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->dateTime('deadline')->nullable();
            $table->decimal('max_score', 5, 2)->default(100);
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index('course_id');
            $table->index('deadline');
        });

        // 7. LMS Submissions (Pengumpulan Tugas)
        Schema::create('lms_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('lms_assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->string('file_path', 255)->nullable();
            $table->integer('file_size')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['draft', 'submitted', 'graded', 'late'])->default('draft');
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->foreignId('graded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->unique(['assignment_id', 'student_id']);
            $table->index('student_id');
        });

        // 8. LMS Quizzes
        Schema::create('lms_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->integer('time_limit')->nullable();
            $table->decimal('total_score', 5, 2)->default(100);
            $table->integer('passing_score')->default(75);
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index('course_id');
        });

        // 9. LMS Quiz Questions
        Schema::create('lms_quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->onDelete('cascade');
            $table->text('question');
            $table->enum('question_type', ['multiple_choice', 'true_false', 'short_answer', 'essay']);
            $table->json('options')->nullable();
            $table->text('correct_answer')->nullable();
            $table->integer('order_number')->default(0);
            $table->decimal('score', 5, 2)->default(1);
            $table->timestamps();

            $table->index('quiz_id');
        });

        // 10. LMS Quiz Attempts
        Schema::create('lms_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('lms_quizzes')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->dateTime('started_at');
            $table->dateTime('finished_at')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->boolean('is_passed')->nullable();
            $table->timestamps();

            $table->index(['quiz_id', 'student_id']);
        });

        // 11. LMS Quiz Answers
        Schema::create('lms_quiz_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('lms_quiz_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('lms_quiz_questions')->onDelete('cascade');
            $table->text('answer')->nullable();
            $table->boolean('is_correct')->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(['attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_quiz_answers');
        Schema::dropIfExists('lms_quiz_attempts');
        Schema::dropIfExists('lms_quiz_questions');
        Schema::dropIfExists('lms_quizzes');
        Schema::dropIfExists('lms_submissions');
        Schema::dropIfExists('lms_assignments');
        Schema::dropIfExists('lms_materials');
        Schema::dropIfExists('lms_enrollments');
        Schema::dropIfExists('lms_classes');
        Schema::dropIfExists('lms_modules');
        Schema::dropIfExists('lms_courses');
    }
};
