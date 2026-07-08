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
        Schema::create('teaching_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->integer('hours_per_week')->default(0)->comment('Jam pelajaran per minggu');
            $table->boolean('is_main_teacher')->default(false)->comment('Guru utama/pengampu');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Indexes
            $table->index(['teacher_id', 'academic_year_id']);
            $table->index(['classroom_id', 'subject_id']);
            $table->index('is_active');
            
            // Unique constraint: one teacher per subject per classroom per academic year
            $table->unique(['teacher_id', 'subject_id', 'classroom_id', 'academic_year_id'], 'unique_teaching_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teaching_assignments');
    }
};
