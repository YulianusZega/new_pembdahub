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
        // 1. Surveys Table
        Schema::create('surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('target_respondent'); // 'guru', 'siswa', 'semua'
            $table->string('status')->default('draft'); // 'draft', 'active', 'closed'
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // 2. Survey Questions Table
        Schema::create('survey_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id');
            $table->text('question_text');
            $table->string('type'); // 'scale', 'text'
            $table->string('scale_type')->nullable(); // 'likert_5', 'likert_4', 'competence_5', 'yes_no'
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
        });

        // 3. Survey Responses Table
        Schema::create('survey_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('survey_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('school_id')->nullable();
            $table->timestamps();

            $table->foreign('survey_id')->references('id')->on('surveys')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // 4. Survey Answers Table
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('response_id');
            $table->unsignedBigInteger('question_id');
            $table->integer('rating')->nullable(); // 1-5 for scale questions
            $table->text('answer_text')->nullable(); // for text questions
            $table->timestamps();

            $table->foreign('response_id')->references('id')->on('survey_responses')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('survey_questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
        Schema::dropIfExists('survey_responses');
        Schema::dropIfExists('survey_questions');
        Schema::dropIfExists('surveys');
    }
};
