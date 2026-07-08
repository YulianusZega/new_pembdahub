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
        // 1. final_project_formats
        Schema::create('final_project_formats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // 2. final_projects
        Schema::create('final_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('type'); // penelitian_ilmiah, project_akhir
            $table->string('title');
            $table->text('abstract');
            $table->foreignId('advisor_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->string('status')->default('pending'); // pending, approved, rejected, in_progress, ready_for_exam, completed
            $table->text('rejection_reason')->nullable();
            $table->dateTime('exam_date')->nullable();
            $table->string('exam_location')->nullable();
            $table->foreignId('examiner_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->decimal('grade', 5, 2)->nullable();
            $table->text('grade_notes')->nullable();
            $table->timestamps();
        });

        // 3. final_project_logs
        Schema::create('final_project_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('final_project_id')->constrained('final_projects')->onDelete('cascade');
            $table->date('log_date');
            $table->text('activity');
            $table->string('documentation_file')->nullable();
            $table->string('status')->default('submitted'); // submitted, reviewed
            $table->text('advisor_feedback')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('final_project_logs');
        Schema::dropIfExists('final_projects');
        Schema::dropIfExists('final_project_formats');
    }
};
