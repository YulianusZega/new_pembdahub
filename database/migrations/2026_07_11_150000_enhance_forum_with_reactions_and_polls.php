<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // 1. Tambah kolom parent_reply_id ke forum_replies
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_reply_id')->nullable()->after('id');
            $table->foreign('parent_reply_id')->references('id')->on('forum_replies')->onDelete('set null');
        });

        // 2. Buat tabel forum_reactions
        Schema::create('forum_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_thread_id')->nullable();
            $table->unsignedBigInteger('forum_reply_id')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('emoji', 10); // e.g. '🔥','❤️','😂','🤔','💡','👏'
            $table->timestamps();

            $table->foreign('forum_thread_id')->references('id')->on('forum_threads')->onDelete('cascade');
            $table->foreign('forum_reply_id')->references('id')->on('forum_replies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Prevent duplicate reactions by same user on same thread/reply for same emoji
            $table->unique(['user_id', 'forum_thread_id', 'emoji']);
            $table->unique(['user_id', 'forum_reply_id', 'emoji']);
        });

        // 3. Buat tabel forum_polls
        Schema::create('forum_polls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_thread_id');
            $table->string('question', 500);
            $table->boolean('is_multiple_choice')->default(false);
            $table->timestamp('closes_at')->nullable();
            $table->timestamps();

            $table->foreign('forum_thread_id')->references('id')->on('forum_threads')->onDelete('cascade');
        });

        // 4. Buat tabel forum_poll_options
        Schema::create('forum_poll_options', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_poll_id');
            $table->string('option_text', 255);
            $table->unsignedInteger('votes_count')->default(0);
            $table->timestamps();

            $table->foreign('forum_poll_id')->references('id')->on('forum_polls')->onDelete('cascade');
        });

        // 5. Buat tabel forum_poll_votes
        Schema::create('forum_poll_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('forum_poll_id');
            $table->unsignedBigInteger('forum_poll_option_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('forum_poll_id')->references('id')->on('forum_polls')->onDelete('cascade');
            $table->foreign('forum_poll_option_id')->references('id')->on('forum_poll_options')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            // Unique index for single choice: user can only vote once per poll
            $table->unique(['forum_poll_id', 'user_id']);
        });

        // 6. Migrasi data lama kategori 'portfolio' menjadi 'performance'
        DB::table('forum_threads')
            ->where('category', 'portfolio')
            ->update(['category' => 'performance']);
    }

    public function down()
    {
        Schema::dropIfExists('forum_poll_votes');
        Schema::dropIfExists('forum_poll_options');
        Schema::dropIfExists('forum_polls');
        Schema::dropIfExists('forum_reactions');
        
        Schema::table('forum_replies', function (Blueprint $table) {
            $table->dropForeign(['parent_reply_id']);
            $table->dropColumn('parent_reply_id');
        });
    }
};
