<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Add composite indexes to improve query performance for schedules grid
     */
    public function up(): void
    {
        // Schedules table indexes
        Schema::table('schedules', function (Blueprint $table) {
            // Composite index for main grid query
            $table->index(['school_id', 'academic_year_id', 'semester'], 'idx_schedules_grid_query');
            
            // Index for conflict checking
            $table->index(['day_of_week', 'time_slot_id', 'classroom_id'], 'idx_schedules_conflicts');
            
            // Index for teacher schedule lookup
            $table->index(['teacher_id', 'day_of_week'], 'idx_schedules_teacher_day');
        });

        // Classrooms table indexes
        Schema::table('classrooms', function (Blueprint $table) {
            // Composite index for filtering classrooms
            $table->index(['school_id', 'academic_year_id', 'is_active'], 'idx_classrooms_active');
        });

        // Time slots table indexes
        Schema::table('time_slots', function (Blueprint $table) {
            // Composite index for time slot queries
            $table->index(['school_id', 'is_active', 'slot_order'], 'idx_timeslots_active');
            $table->index(['school_id', 'day_of_week'], 'idx_timeslots_day');
        });

        // Teachers table indexes
        Schema::table('teachers', function (Blueprint $table) {
            // Index for active teachers
            $table->index(['school_id', 'is_active'], 'idx_teachers_active');
        });

        // Subjects table indexes
        Schema::table('subjects', function (Blueprint $table) {
            // Index for active subjects
            $table->index(['school_id', 'is_active'], 'idx_subjects_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('idx_schedules_grid_query');
            $table->dropIndex('idx_schedules_conflicts');
            $table->dropIndex('idx_schedules_teacher_day');
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropIndex('idx_classrooms_active');
        });

        Schema::table('time_slots', function (Blueprint $table) {
            $table->dropIndex('idx_timeslots_active');
            $table->dropIndex('idx_timeslots_day');
        });

        Schema::table('teachers', function (Blueprint $table) {
            $table->dropIndex('idx_teachers_active');
        });

        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('idx_subjects_active');
        });
    }
};
