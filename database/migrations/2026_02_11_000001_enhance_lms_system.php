<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * LMS Enhancement Migration - Phase 12 Completion
 * 
 * New features:
 * 1. Discussion Forum (threads + replies)
 * 2. Material Progress Tracking
 * 3. Course Announcements
 * 4. Multi-attempt quizzes (max_attempts)
 * 5. Assignment resubmission support
 * 6. Soft deletes on LMS tables (never hard-delete student/teacher data)
 * 
 * IMPORTANT: Foreign keys to students/teachers use SET NULL on delete,
 * so student/teacher data is NEVER removed when LMS records are cleaned up.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ================================================================
        // 1. COURSE ANNOUNCEMENTS
        // ================================================================
        if (!Schema::hasTable('lms_announcements')) {
            Schema::create('lms_announcements', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('title', 200);
                $table->text('content');
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_published')->default(true);
                $table->timestamp('published_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['course_id', 'is_published']);
            });
        }

        // ================================================================
        // 2. DISCUSSION FORUM
        // ================================================================
        if (!Schema::hasTable('lms_discussions')) {
            Schema::create('lms_discussions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('title', 300);
                $table->text('content');
                $table->enum('type', ['discussion', 'question', 'announcement'])->default('discussion');
                $table->boolean('is_pinned')->default(false);
                $table->boolean('is_locked')->default(false);
                $table->boolean('is_resolved')->default(false);
                $table->unsignedInteger('replies_count')->default(0);
                $table->timestamp('last_reply_at')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index(['course_id', 'type']);
                $table->index(['course_id', 'is_pinned', 'created_at']);
            });
        }

        if (!Schema::hasTable('lms_discussion_replies')) {
            Schema::create('lms_discussion_replies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('discussion_id')->constrained('lms_discussions')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
                $table->unsignedBigInteger('parent_id')->nullable();
                $table->text('content');
                $table->boolean('is_best_answer')->default(false);
                $table->timestamps();
                $table->softDeletes();
                $table->foreign('parent_id')->references('id')->on('lms_discussion_replies')->onDelete('cascade');
                $table->index('discussion_id');
            });
        }

        // ================================================================
        // 3. MATERIAL PROGRESS TRACKING
        // ================================================================
        if (!Schema::hasTable('lms_material_progress')) {
            Schema::create('lms_material_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('material_id')->constrained('lms_materials')->onDelete('cascade');
                $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
                $table->enum('status', ['viewed', 'in_progress', 'completed'])->default('viewed');
                $table->integer('progress_percent')->default(0);
                $table->integer('time_spent_seconds')->default(0);
                $table->timestamp('first_viewed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();
                $table->unique(['material_id', 'student_id']);
                $table->index('student_id');
            });
        }

        // ================================================================
        // 4. ENHANCE EXISTING TABLES
        // ================================================================

        // Add max_attempts + shuffle to quizzes
        if (Schema::hasTable('lms_quizzes')) {
            Schema::table('lms_quizzes', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_quizzes', 'max_attempts')) {
                    $table->unsignedInteger('max_attempts')->default(1)->after('passing_score');
                }
                if (!Schema::hasColumn('lms_quizzes', 'shuffle_questions')) {
                    $table->boolean('shuffle_questions')->default(false)->after('max_attempts');
                }
                if (!Schema::hasColumn('lms_quizzes', 'show_result')) {
                    $table->boolean('show_result')->default(true)->after('shuffle_questions');
                }
                if (!Schema::hasColumn('lms_quizzes', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // Add allow_resubmit + module_id to assignments
        if (Schema::hasTable('lms_assignments')) {
            Schema::table('lms_assignments', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_assignments', 'allow_resubmit')) {
                    $table->boolean('allow_resubmit')->default(false)->after('is_published');
                }
                if (!Schema::hasColumn('lms_assignments', 'max_resubmissions')) {
                    $table->unsignedInteger('max_resubmissions')->default(1)->after('allow_resubmit');
                }
                if (!Schema::hasColumn('lms_assignments', 'module_id')) {
                    $table->unsignedBigInteger('module_id')->nullable()->after('course_id');
                }
                if (!Schema::hasColumn('lms_assignments', 'assignment_type')) {
                    $table->enum('assignment_type', ['file', 'text', 'file_text', 'link'])->default('file_text')->after('description');
                }
                if (!Schema::hasColumn('lms_assignments', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // Add resubmit tracking to submissions
        if (Schema::hasTable('lms_submissions')) {
            Schema::table('lms_submissions', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_submissions', 'attempt_number')) {
                    $table->unsignedInteger('attempt_number')->default(1)->after('graded_by');
                }
                if (!Schema::hasColumn('lms_submissions', 'teacher_notes')) {
                    $table->text('teacher_notes')->nullable()->after('feedback');
                }
                if (!Schema::hasColumn('lms_submissions', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // Add soft deletes to courses
        if (Schema::hasTable('lms_courses')) {
            Schema::table('lms_courses', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_courses', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // Add soft deletes to materials  
        if (Schema::hasTable('lms_materials')) {
            Schema::table('lms_materials', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_materials', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // Add soft deletes to modules
        if (Schema::hasTable('lms_modules')) {
            Schema::table('lms_modules', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_modules', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

        // ================================================================
        // 5. FIX FOREIGN KEY CASCADE → SET NULL for student/teacher refs
        // ================================================================
        // Note: We don't drop and re-add FKs here to avoid data loss.
        // Instead the application layer ensures soft-deletes are always used.
        // Student and Teacher records should NEVER be hard-deleted.
    }

    public function down(): void
    {
        Schema::dropIfExists('lms_material_progress');
        Schema::dropIfExists('lms_discussion_replies');
        Schema::dropIfExists('lms_discussions');
        Schema::dropIfExists('lms_announcements');

        $dropColumns = [
            'lms_quizzes' => ['max_attempts', 'shuffle_questions', 'show_result', 'deleted_at'],
            'lms_assignments' => ['allow_resubmit', 'max_resubmissions', 'module_id', 'assignment_type', 'deleted_at'],
            'lms_submissions' => ['attempt_number', 'teacher_notes', 'deleted_at'],
            'lms_courses' => ['deleted_at'],
            'lms_materials' => ['deleted_at'],
            'lms_modules' => ['deleted_at'],
        ];

        foreach ($dropColumns as $tableName => $columns) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                    foreach ($columns as $col) {
                        if (Schema::hasColumn($tableName, $col)) {
                            $table->dropColumn($col);
                        }
                    }
                });
            }
        }
    }
};
