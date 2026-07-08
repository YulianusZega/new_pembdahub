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
        Schema::create('forum_threads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->string('category'); // diskusi, sharing, info, performance, gallery, project_idea, committee, charity
            $table->string('image_path')->nullable(); // for image gallery
            $table->string('attachment_path')->nullable(); // for files
            $table->string('attachment_name')->nullable();
            $table->string('reference_type')->nullable(); // for linking badges, achievements, or grades
            $table->unsignedBigInteger('reference_id')->nullable();
            
            // Collaboration / Charity Specifics
            $table->string('status')->default('seeking_members'); // seeking_members, active, completed
            $table->decimal('charity_target_amount', 12, 2)->nullable();
            $table->decimal('charity_current_amount', 12, 2)->default(0)->nullable();
            $table->unsignedInteger('charity_target_volunteers')->nullable();
            
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->boolean('is_accepted')->default(false); // Mark as best answer
            $table->timestamps();
        });

        Schema::create('forum_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('forum_thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'forum_thread_id']);
        });

        Schema::create('forum_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_thread_id')->constrained('forum_threads')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, approved, rejected
            $table->text('notes')->nullable(); // optional application note
            $table->timestamps();
            $table->unique(['forum_thread_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forum_members');
        Schema::dropIfExists('forum_likes');
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_threads');
    }
};
