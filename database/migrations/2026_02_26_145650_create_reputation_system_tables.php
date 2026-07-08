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
        // Reputations table: Current standing of each user
        Schema::create('reputations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('total_points')->default(0);
            $table->string('level_name')->default('Newbie');
            $table->integer('rank_global')->nullable();
            $table->timestamps();
            
            $table->index('total_points');
        });

        // Reputation Logs: History of points earned/lost
        Schema::create('reputation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('points');
            $table->string('category'); // e.g. attendance, exam, payment, lms
            $table->string('reference_type')->nullable(); // Model class name
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('description');
            $table->timestamp('created_at')->useCurrent();
        });

        // Badges: Definition of available awards
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('icon')->nullable(); // FontAwesome class
            $table->string('color')->nullable(); // Tailwind color class
            $table->text('description')->nullable();
            $table->string('requirement_type')->nullable(); // 'points', 'attendance', 'payment'
            $table->integer('requirement_value')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // User Badges: Link users with earned badges
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('reputation_logs');
        Schema::dropIfExists('reputations');
    }
};
