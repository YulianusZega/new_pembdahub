<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Performance indexes for frequently queried columns.
     * Phase B: Performance & Caching optimization.
     */
    public function up(): void
    {
        // Each index is checked individually to handle partial runs gracefully.
        $indexes = [
            // student_bills
            ['student_bills', ['student_id', 'status'], 'idx_bills_student_status'],
            ['student_bills', ['student_id', 'year', 'month'], 'idx_bills_student_period'],
            ['student_bills', ['academic_year_id', 'payment_type_id'], 'idx_bills_year_type'],
            ['student_bills', ['status', 'year', 'month'], 'idx_bills_status_period'],
            // payments
            ['payments', ['student_id', 'payment_date'], 'idx_payments_student_date'],
            ['payments', ['is_verified', 'payment_date'], 'idx_payments_verified_date'],
            // grades
            ['grades', ['teacher_id', 'semester_id'], 'idx_grades_teacher_semester'],
            // attendances
            ['attendances', ['student_id', 'date'], 'idx_attendances_student_date'],
            ['attendances', ['student_id', 'status', 'date'], 'idx_attendances_student_status_date'],
            // schedules
            ['schedules', ['classroom_id', 'day_of_week'], 'idx_schedules_class_day'],
            ['schedules', ['teacher_id', 'day_of_week'], 'idx_schedules_teacher_day'],
            // student_classes
            ['student_classes', ['student_id', 'academic_year_id', 'status'], 'idx_sc_student_year_status'],
            ['student_classes', ['classroom_id', 'academic_year_id'], 'idx_sc_classroom_year'],
            // applicants
            ['applicants', ['school_id', 'status'], 'idx_applicants_school_status'],
            ['applicants', ['academic_year_id', 'school_id'], 'idx_applicants_year_school'],
            // report_cards
            ['report_cards', ['semester_id', 'status'], 'idx_reports_semester_status'],
            ['report_cards', ['student_id', 'semester_id'], 'idx_reports_student_semester'],
            // users
            ['users', ['role', 'school_id', 'is_active'], 'idx_users_role_school_active'],
        ];

        foreach ($indexes as [$table, $columns, $name]) {
            if (!$this->indexExists($table, $name)) {
                Schema::table($table, function (Blueprint $table) use ($columns, $name) {
                    $table->index($columns, $name);
                });
            }
        }
    }

    /**
     * Check if an index exists on a table (works with MySQL & SQLite).
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            $indexes = \DB::select("PRAGMA index_list(\"{$table}\")");
            foreach ($indexes as $idx) {
                if ($idx->name === $indexName) {
                    return true;
                }
            }
            return false;
        }

        // MySQL / MariaDB
        $indexes = \DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $drops = [
            'student_bills' => ['idx_bills_student_status', 'idx_bills_student_period', 'idx_bills_year_type', 'idx_bills_status_period'],
            'payments' => ['idx_payments_student_date', 'idx_payments_verified_date'],
            'grades' => ['idx_grades_teacher_semester'],
            'attendances' => ['idx_attendances_student_date', 'idx_attendances_student_status_date'],
            'schedules' => ['idx_schedules_class_day', 'idx_schedules_teacher_day'],
            'student_classes' => ['idx_sc_student_year_status', 'idx_sc_classroom_year'],
            'applicants' => ['idx_applicants_school_status', 'idx_applicants_year_school'],
            'report_cards' => ['idx_reports_semester_status', 'idx_reports_student_semester'],
            'users' => ['idx_users_role_school_active'],
        ];

        foreach ($drops as $table => $indexes) {
            Schema::table($table, function (Blueprint $table) use ($indexes) {
                foreach ($indexes as $index) {
                    try {
                        $table->dropIndex($index);
                    } catch (\Exception $e) {
                        // Index may not exist
                    }
                }
            });
        }
    }
};
