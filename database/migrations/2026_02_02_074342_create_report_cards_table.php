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
        // Report Cards - Rapor Digital
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('semester_id')->constrained('semesters')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            
            // Nilai & Status
            $table->decimal('average_score', 5, 2)->nullable()->comment('Rata-rata nilai semester');
            $table->enum('predicate', ['A', 'B', 'C', 'D'])->nullable()->comment('Predikat nilai');
            $table->integer('rank')->nullable()->comment('Ranking di kelas');
            $table->integer('total_students')->nullable()->comment('Total siswa di kelas');
            
            // Kehadiran
            $table->integer('total_days')->default(0)->comment('Total hari efektif');
            $table->integer('days_present')->default(0)->comment('Hadir');
            $table->integer('days_sick')->default(0)->comment('Sakit');
            $table->integer('days_permission')->default(0)->comment('Izin');
            $table->integer('days_absent')->default(0)->comment('Alpa/Tanpa Keterangan');
            
            // Catatan & Status
            $table->text('teacher_notes')->nullable()->comment('Catatan wali kelas');
            $table->text('principal_notes')->nullable()->comment('Catatan kepala sekolah');
            $table->text('achievements')->nullable()->comment('Prestasi/Penghargaan');
            $table->text('recommendations')->nullable()->comment('Saran pengembangan');
            
            // Status Rapor
            $table->enum('status', ['draft', 'finalized', 'published'])->default('draft');
            $table->foreignId('finalized_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('finalized_at')->nullable();
            $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('published_at')->nullable();
            
            $table->timestamps();
            
            // Unique constraint: One report card per student per semester
            $table->unique(['student_id', 'semester_id']);
            
            // Indexes for performance
            $table->index('academic_year_id');
            $table->index('classroom_id');
            $table->index('status');
        });
        
        // Student Achievements - Prestasi & Penghargaan
        Schema::create('student_achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->onDelete('cascade');
            $table->string('title')->comment('Nama prestasi/penghargaan');
            $table->enum('type', ['academic', 'sport', 'art', 'competition', 'other'])->default('other');
            $table->enum('level', ['school', 'district', 'city', 'province', 'national', 'international'])->default('school');
            $table->enum('rank', ['winner', 'runner_up', 'third_place', 'participant'])->nullable();
            $table->date('achievement_date')->nullable();
            $table->text('description')->nullable();
            $table->string('certificate_file')->nullable()->comment('Path to certificate file');
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Indexes
            $table->index('student_id');
            $table->index('type');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_achievements');
        Schema::dropIfExists('report_cards');
    }
};
