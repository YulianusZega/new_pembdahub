<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Menambahkan indexes untuk meningkatkan performance query yang sering digunakan
     */
    public function up(): void
    {
        // Indexes untuk tabel grades (nilai)
        Schema::table('grades', function (Blueprint $table) {
            // Composite index untuk query by student + subject + semester
            $table->index(['student_id', 'subject_id', 'semester_id'], 'idx_grades_student_subject_semester');

            // Index untuk grade_type filtering
            $table->index('grade_type', 'idx_grades_type');

            // Index untuk teacher_id queries
            $table->index('teacher_id', 'idx_grades_teacher');
        });

        // Indexes untuk tabel students (siswa)
        Schema::table('students', function (Blueprint $table) {
            // Index untuk status filtering (aktif, lulus, keluar, pindah)
            $table->index('status', 'idx_students_status');

            // Index untuk school_id queries
            $table->index('school_id', 'idx_students_school');

            // Index untuk entry_year filtering
            $table->index('entry_year', 'idx_students_entry_year');

            // Index untuk full name search (jika sering search by name)
            $table->index('full_name', 'idx_students_fullname');
        });

        // Indexes untuk tabel schedules (jadwal)
        Schema::table('schedules', function (Blueprint $table) {
            // Composite index untuk query by classroom + day
            $table->index(['classroom_id', 'day_of_week'], 'idx_schedules_classroom_day');

            // Index untuk teacher_id queries
            $table->index('teacher_id', 'idx_schedules_teacher');

            // Index untuk subject_id queries
            $table->index('subject_id', 'idx_schedules_subject');

            // Index untuk semester_id filtering
            $table->index('semester_id', 'idx_schedules_semester');
        });

        // Indexes untuk tabel attendances (absensi)
        Schema::table('attendances', function (Blueprint $table) {
            // Composite index untuk query by student + date
            $table->index(['student_id', 'date'], 'idx_attendances_student_date');

            // Index untuk classroom_id queries
            $table->index('classroom_id', 'idx_attendances_classroom');

            // Index untuk status filtering
            $table->index('status', 'idx_attendances_status');

            // Index untuk date range queries
            $table->index('date', 'idx_attendances_date');
        });

        // Indexes untuk tabel student_classes (relasi siswa-kelas)
        Schema::table('student_classes', function (Blueprint $table) {
            // Composite index untuk query by student + academic_year
            $table->index(['student_id', 'academic_year_id'], 'idx_student_classes_student_year');

            // Index untuk classroom_id queries
            $table->index('classroom_id', 'idx_student_classes_classroom');

            // Index untuk status filtering
            $table->index('status', 'idx_student_classes_status');
        });

        // Indexes untuk tabel classrooms (kelas)
        Schema::table('classrooms', function (Blueprint $table) {
            // Index untuk school_id queries
            $table->index('school_id', 'idx_classrooms_school');

            // Index untuk academic_year_id queries
            $table->index('academic_year_id', 'idx_classrooms_year');

            // Index untuk grade_level filtering
            $table->index('grade_level', 'idx_classrooms_level');
        });

        // Indexes untuk tabel subjects (mata pelajaran)
        Schema::table('subjects', function (Blueprint $table) {
            // Index untuk school_id queries
            $table->index('school_id', 'idx_subjects_school');

            // Index untuk subject_code search
            $table->index('subject_code', 'idx_subjects_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes dari grades
        Schema::table('grades', function (Blueprint $table) {
            $table->dropIndex('idx_grades_student_subject_semester');
            $table->dropIndex('idx_grades_type');
            $table->dropIndex('idx_grades_teacher');
        });

        // Drop indexes dari students
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('idx_students_status');
            $table->dropIndex('idx_students_school');
            $table->dropIndex('idx_students_entry_year');
            $table->dropIndex('idx_students_fullname');
        });

        // Drop indexes dari schedules
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex('idx_schedules_classroom_day');
            $table->dropIndex('idx_schedules_teacher');
            $table->dropIndex('idx_schedules_subject');
            $table->dropIndex('idx_schedules_semester');
        });

        // Drop indexes dari attendances
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex('idx_attendances_student_date');
            $table->dropIndex('idx_attendances_classroom');
            $table->dropIndex('idx_attendances_status');
            $table->dropIndex('idx_attendances_date');
        });

        // Drop indexes dari student_classes
        Schema::table('student_classes', function (Blueprint $table) {
            $table->dropIndex('idx_student_classes_student_year');
            $table->dropIndex('idx_student_classes_classroom');
            $table->dropIndex('idx_student_classes_status');
        });

        // Drop indexes dari classrooms
        Schema::table('classrooms', function (Blueprint $table) {
            $table->dropIndex('idx_classrooms_school');
            $table->dropIndex('idx_classrooms_year');
            $table->dropIndex('idx_classrooms_level');
        });

        // Drop indexes dari subjects
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropIndex('idx_subjects_school');
            $table->dropIndex('idx_subjects_code');
        });
    }
};
