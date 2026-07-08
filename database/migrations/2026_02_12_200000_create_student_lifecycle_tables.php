<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Module 1: Student Status Lifecycle
 *
 * Tables:
 * - student_status_histories: Full audit trail of student status changes
 * - student_promotions: Grade promotion/retention records per academic year
 *
 * Fixes:
 * - Adds 'entry_date' column to student_classes if not exists
 * - Adds 'naik_kelas' to students.status enum (via string migration)
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Student Status History - complete audit trail
        Schema::create('student_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('from_status', 30)->nullable()->comment('Previous status');
            $table->string('to_status', 30)->comment('New status');
            $table->string('reason')->nullable()->comment('Reason for status change');
            $table->text('notes')->nullable();
            $table->string('document_number')->nullable()->comment('SK/surat reference');
            $table->date('effective_date')->comment('When status change takes effect');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['student_id', 'effective_date'], 'idx_ssh_student_date');
            $table->index(['school_id', 'to_status'], 'idx_ssh_school_status');
        });

        // 2. Student Promotions - end-of-year decisions
        Schema::create('student_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('from_classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('to_classroom_id')->nullable()->constrained('classrooms')->nullOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->enum('decision', ['naik', 'tinggal', 'lulus', 'pindah', 'keluar'])->comment('Promotion decision');
            $table->float('average_score')->nullable()->comment('Final average score');
            $table->integer('total_subjects')->nullable();
            $table->integer('passed_subjects')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'academic_year_id'], 'uq_promotion_student_year');
            $table->index('decision', 'idx_promotion_decision');
        });

        // 3. Expand students.status to support more statuses (change from enum to varchar)
        if (DB::connection()->getDriverName() !== 'sqlite') {
            if (Schema::hasColumn('students', 'status')) {
                Schema::table('students', function (Blueprint $table) {
                    $table->string('status', 30)->default('aktif')->change();
                });
            }
        }

        // 4. Add entry_date to student_classes if not exists
        if (!Schema::hasColumn('student_classes', 'entry_date')) {
            Schema::table('student_classes', function (Blueprint $table) {
                $table->date('entry_date')->nullable()->after('status');
            });
        }

        // 5. Expand student_classes.status to support more statuses
        if (DB::connection()->getDriverName() !== 'sqlite') {
            if (Schema::hasColumn('student_classes', 'status')) {
                Schema::table('student_classes', function (Blueprint $table) {
                    $table->string('status', 30)->default('aktif')->change();
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('student_promotions');
        Schema::dropIfExists('student_status_histories');
    }
};
