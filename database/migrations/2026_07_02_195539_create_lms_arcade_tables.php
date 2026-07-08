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
        Schema::create('lms_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('course_id')->nullable()->constrained('lms_courses')->onDelete('cascade');
            $table->foreignId('module_id')->nullable()->constrained('lms_modules')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('game_type', ['spin_wheel', 'flashcard', 'match', 'crossword', 'word_search'])->default('flashcard');
            $table->json('game_data')->nullable(); // Stores all the pairs, words, config, etc.
            $table->integer('reward_points')->default(50); // Points given upon completion
            $table->boolean('is_published')->default(true);
            $table->boolean('is_daily_challenge')->default(false);
            $table->timestamps();
        });

        Schema::create('lms_game_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('lms_games')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['started', 'completed'])->default('completed');
            $table->integer('score')->default(0); // EXP earned
            $table->timestamps();
            
            // A student can play a game many times, but we might want to restrict rewards to the first completion.
            // We can handle that logic in the controller.
            $table->index(['game_id', 'student_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_game_attempts');
        Schema::dropIfExists('lms_games');
    }
};
