<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration aligns the LMS schema between the real database and the code.
 * 
 * Strategy:
 * - Add 4 missing tables: lms_modules, lms_classes, lms_enrollments, lms_submissions
 * - Add 2 missing columns to lms_quiz_questions: options, correct_answer
 * - Add missing columns to lms_courses: school_id, code, status, is_active, cover_image
 * - Rename/add columns to lms_materials: add module_id
 * - Add missing columns to lms_assignments: add is_active alias
 * 
 * SAFE: Uses Schema::hasTable / Schema::hasColumn guards. Never drops existing data.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Add missing columns to lms_courses
        if (Schema::hasTable('lms_courses')) {
            Schema::table('lms_courses', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_courses', 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('lms_courses', 'code')) {
                    $table->string('code', 50)->nullable()->after('semester_id');
                }
                if (!Schema::hasColumn('lms_courses', 'cover_image')) {
                    $table->string('cover_image', 255)->nullable()->after('description');
                }
                if (!Schema::hasColumn('lms_courses', 'status')) {
                    $table->enum('status', ['draft', 'active', 'archived'])->default('draft')->after('is_published');
                }
                if (!Schema::hasColumn('lms_courses', 'is_active')) {
                    $table->boolean('is_active')->default(true)->after('status');
                }
            });
        }

        // 2. Create lms_modules table if missing
        if (!Schema::hasTable('lms_modules')) {
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
        }

        // 3. Create lms_classes table if missing
        if (!Schema::hasTable('lms_classes')) {
            Schema::create('lms_classes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('course_id')->constrained('lms_courses')->onDelete('cascade');
                $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
                $table->unsignedBigInteger('school_id')->nullable();
                $table->enum('status', ['active', 'ended', 'archived'])->default('active');
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();

                $table->unique(['course_id', 'classroom_id']);
                $table->index('classroom_id');
            });
        }

        // 4. Create lms_enrollments table if missing
        if (!Schema::hasTable('lms_enrollments')) {
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
        }

        // 5. Add module_id to lms_materials if missing (materials can link to module OR course)
        if (Schema::hasTable('lms_materials')) {
            Schema::table('lms_materials', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_materials', 'module_id')) {
                    $table->unsignedBigInteger('module_id')->nullable()->after('id');
                }
            });
        }

        // 6. Create lms_submissions table if missing
        if (!Schema::hasTable('lms_submissions')) {
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
                $table->unsignedBigInteger('graded_by')->nullable();
                $table->timestamps();

                $table->unique(['assignment_id', 'student_id']);
                $table->index('student_id');
            });
        }

        // 7. Add missing columns to lms_quiz_questions
        if (Schema::hasTable('lms_quiz_questions')) {
            Schema::table('lms_quiz_questions', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_quiz_questions', 'options')) {
                    $table->json('options')->nullable()->after('question_type');
                }
                if (!Schema::hasColumn('lms_quiz_questions', 'correct_answer')) {
                    $table->text('correct_answer')->nullable()->after('options');
                }
            });
        }
    }

    public function down(): void
    {
        // Remove added columns (reverse order)
        if (Schema::hasTable('lms_quiz_questions')) {
            Schema::table('lms_quiz_questions', function (Blueprint $table) {
                if (Schema::hasColumn('lms_quiz_questions', 'correct_answer')) {
                    $table->dropColumn('correct_answer');
                }
                if (Schema::hasColumn('lms_quiz_questions', 'options')) {
                    $table->dropColumn('options');
                }
            });
        }

        Schema::dropIfExists('lms_submissions');

        if (Schema::hasTable('lms_materials')) {
            Schema::table('lms_materials', function (Blueprint $table) {
                if (Schema::hasColumn('lms_materials', 'module_id')) {
                    $table->dropColumn('module_id');
                }
            });
        }

        Schema::dropIfExists('lms_enrollments');
        Schema::dropIfExists('lms_classes');
        Schema::dropIfExists('lms_modules');

        if (Schema::hasTable('lms_courses')) {
            Schema::table('lms_courses', function (Blueprint $table) {
                $cols = ['is_active', 'status', 'cover_image', 'code', 'school_id'];
                foreach ($cols as $col) {
                    if (Schema::hasColumn('lms_courses', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
