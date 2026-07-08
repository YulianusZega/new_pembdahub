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
        Schema::create('pkl_placements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('company_name');
            $table->string('company_address');
            $table->string('mentor_name');
            $table->string('mentor_phone'); // Used for WhatsApp notifications
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade'); // internal supervisor
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');
            $table->string('signed_token')->unique(); // For Signed URLs access
            $table->timestamps();
        });

        Schema::create('pkl_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_placement_id')->constrained('pkl_placements')->onDelete('cascade');
            $table->date('log_date');
            $table->text('activity');
            $table->string('photo')->nullable(); // uploaded proof
            $table->decimal('latitude', 10, 7)->nullable(); // GPS check-in validation
            $table->decimal('longitude', 10, 7)->nullable();
            $table->enum('status', ['submitted', 'approved', 'rejected'])->default('submitted');
            $table->text('mentor_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->unique(['pkl_placement_id', 'log_date']);
        });

        Schema::create('pkl_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pkl_placement_id')->constrained('pkl_placements')->onDelete('cascade');
            $table->integer('score_discipline');
            $table->integer('score_teamwork');
            $table->integer('score_technical');
            $table->integer('score_safety');
            $table->decimal('score_average', 5, 2);
            $table->text('notes')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->string('full_name');
            $table->integer('graduation_year');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->timestamps();
        });

        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_profile_id')->constrained('alumni_profiles')->onDelete('cascade');
            $table->enum('employment_status', ['kerja', 'kuliah', 'wirausaha', 'mencari_kerja', 'lainnya']);
            $table->string('company_name')->nullable();
            $table->string('job_title')->nullable();
            $table->string('salary_range')->nullable();
            $table->string('university_name')->nullable();
            $table->string('major')->nullable();
            $table->string('wirausaha_field')->nullable();
            $table->text('feedback_for_school')->nullable();
            $table->date('survey_date');
            $table->timestamps();
        });

        Schema::create('job_postings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('title');
            $table->text('description');
            $table->text('requirements')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('salary_range')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_postings');
        Schema::dropIfExists('tracer_studies');
        Schema::dropIfExists('alumni_profiles');
        Schema::dropIfExists('pkl_grades');
        Schema::dropIfExists('pkl_logs');
        Schema::dropIfExists('pkl_placements');
    }
};
