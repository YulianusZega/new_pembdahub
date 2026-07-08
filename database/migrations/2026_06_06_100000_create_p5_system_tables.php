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
        Schema::create('p5_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('theme'); // e.g., Gaya Hidup Berkelanjutan, Kearifan Lokal, etc.
            $table->text('description')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('p5_project_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p5_project_id')->constrained('p5_projects')->onDelete('cascade');
            $table->string('dimension'); // e.g., Gotong Royong, Kreatif
            $table->text('sub_element'); // e.g., Bekerja sama dalam merawat tanaman
            $table->timestamps();
        });

        Schema::create('p5_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p5_project_id')->constrained('p5_projects')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('p5_project_target_id')->constrained('p5_project_targets')->onDelete('cascade');
            $table->enum('score', ['MB', 'SB', 'BSH', 'SAB']);
            $table->timestamps();

            $table->unique(['p5_project_id', 'student_id', 'p5_project_target_id'], 'p5_assessments_unique');
        });

        Schema::create('p5_project_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('p5_project_id')->constrained('p5_projects')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['p5_project_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p5_project_notes');
        Schema::dropIfExists('p5_assessments');
        Schema::dropIfExists('p5_project_targets');
        Schema::dropIfExists('p5_projects');
    }
};
