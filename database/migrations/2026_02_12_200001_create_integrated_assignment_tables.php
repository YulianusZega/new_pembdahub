<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Module 2: Integrated Teacher Assignment (Jabatan + Tugas Mengajar)
 *
 * Extends existing tables:
 * - teaching_assignments: Add teaching_load_type, allowance fields
 * - employee_positions: Add workload-related fields
 *
 * New tables:
 * - employee_workload_summaries: Per-semester aggregated workload for payroll
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend teaching_assignments with load type & allowance
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->enum('teaching_load_type', ['wajib', 'tambahan', 'pengganti'])
                ->default('wajib')
                ->after('is_main_teacher')
                ->comment('wajib=required load, tambahan=extra, pengganti=substitute');

            $table->decimal('hourly_rate', 12, 2)->default(0)->after('teaching_load_type')
                ->comment('Rate per jam mengajar');

            $table->decimal('teaching_allowance', 12, 2)->default(0)->after('hourly_rate')
                ->comment('Total allowance = hours_per_week * hourly_rate');

            $table->text('sk_reference')->nullable()->after('teaching_allowance')
                ->comment('SK penugasan mengajar');

            $table->foreignId('semester_id')->nullable()->after('academic_year_id')
                ->constrained('semesters')->nullOnDelete();
        });

        // 2. Extend employee_positions with workload context
        if (!Schema::hasColumn('employee_positions', 'workload_hours')) {
            Schema::table('employee_positions', function (Blueprint $table) {
                $table->integer('workload_hours')->default(0)->after('is_primary')
                    ->comment('Jam kerja per minggu untuk jabatan ini');
                $table->decimal('position_allowance', 12, 2)->default(0)->after('workload_hours')
                    ->comment('Override tunjangan jabatan (0 = use position default)');
            });
        }

        // 3. Workload summaries for payroll integration
        Schema::create('employee_workload_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');

            // Position-based (Jabatan)
            $table->integer('total_position_count')->default(0)->comment('Jumlah jabatan aktif');
            $table->decimal('total_position_allowance', 12, 2)->default(0)->comment('Total tunjangan jabatan');

            // Teaching-based (Mengajar)
            $table->integer('total_teaching_hours')->default(0)->comment('Total jam mengajar per minggu');
            $table->integer('total_teaching_classes')->default(0)->comment('Jumlah kelas yang diajar');
            $table->integer('total_teaching_subjects')->default(0)->comment('Jumlah mapel yang diajar');
            $table->decimal('total_teaching_allowance', 12, 2)->default(0)->comment('Total tunjangan mengajar');

            // Combined
            $table->decimal('total_allowance', 12, 2)->default(0)->comment('Position + Teaching allowance');
            $table->decimal('basic_salary', 12, 2)->default(0)->comment('Snapshot of basic salary');
            $table->decimal('total_compensation', 12, 2)->default(0)->comment('basic_salary + total_allowance');

            $table->enum('status', ['draft', 'confirmed', 'locked'])->default('draft');
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'semester_id'], 'uq_workload_employee_semester');
            $table->index(['academic_year_id', 'semester_id', 'status'], 'idx_workload_year_sem_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_workload_summaries');

        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->dropForeign(['semester_id']);
            $table->dropColumn([
                'teaching_load_type', 'hourly_rate', 'teaching_allowance',
                'sk_reference', 'semester_id'
            ]);
        });

        if (Schema::hasColumn('employee_positions', 'workload_hours')) {
            Schema::table('employee_positions', function (Blueprint $table) {
                $table->dropColumn(['workload_hours', 'position_allowance']);
            });
        }
    }
};
