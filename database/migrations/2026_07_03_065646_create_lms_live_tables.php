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
        Schema::create('lms_live_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('lms_games')->onDelete('cascade');
            $table->foreignId('host_id')->constrained('users')->onDelete('cascade');
            $table->string('pin_code', 10)->unique();
            $table->enum('status', ['waiting', 'question', 'leaderboard', 'finished'])->default('waiting');
            $table->integer('current_question_index')->default(0);
            $table->timestamp('question_started_at')->nullable(); // Used for scoring based on time
            $table->timestamps();
        });

        Schema::create('lms_live_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('lms_live_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('cascade');
            $table->string('nickname');
            $table->integer('score')->default(0);
            $table->integer('streak')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('lms_live_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('lms_live_sessions')->onDelete('cascade');
            $table->foreignId('player_id')->constrained('lms_live_players')->onDelete('cascade');
            $table->integer('question_index');
            $table->string('answer_value');
            $table->boolean('is_correct');
            $table->integer('points_earned')->default(0);
            $table->integer('time_taken_ms')->default(0);
            $table->timestamps();

            $table->unique(['session_id', 'player_id', 'question_index'], 'unique_player_answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_live_answers');
        Schema::dropIfExists('lms_live_players');
        Schema::dropIfExists('lms_live_sessions');
    }
};
