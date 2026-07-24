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
        // 1. Knowledge Materials
        if (!Schema::hasTable('knowledge_materials')) {
            Schema::create('knowledge_materials', function (Blueprint $table) {
                $table->id();
                $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
                $table->foreignId('subject_id')->nullable()->constrained('subjects')->onDelete('set null');
                $table->string('title', 255);
                $table->string('slug', 255)->unique();
                $table->text('description')->nullable();
                $table->enum('type', ['document', 'video', 'audio', 'link'])->default('document');
                $table->enum('category_type', ['sekolah', 'umum'])->default('sekolah');
                $table->string('file_path', 255)->nullable();
                $table->text('external_url')->nullable();
                $table->string('thumbnail_path', 255)->nullable();
                $table->boolean('is_public')->default(true);
                $table->boolean('allow_download')->default(true);
                $table->unsignedInteger('views_count')->default(0);
                $table->unsignedInteger('likes_count')->default(0);
                $table->unsignedInteger('bookmarks_count')->default(0);
                $table->unsignedInteger('downloads_count')->default(0);
                $table->timestamps();

                $table->index(['teacher_id', 'category_type']);
                $table->index(['type', 'is_public']);
            });
        }

        // 2. Knowledge Likes
        if (!Schema::hasTable('knowledge_likes')) {
            Schema::create('knowledge_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('knowledge_material_id')->constrained('knowledge_materials')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('ip_address', 45)->nullable();
                $table->timestamps();

                $table->unique(['knowledge_material_id', 'user_id']);
            });
        }

        // 3. Knowledge Bookmarks
        if (!Schema::hasTable('knowledge_bookmarks')) {
            Schema::create('knowledge_bookmarks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('knowledge_material_id')->constrained('knowledge_materials')->onDelete('cascade');
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();

                $table->unique(['knowledge_material_id', 'user_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_bookmarks');
        Schema::dropIfExists('knowledge_likes');
        Schema::dropIfExists('knowledge_materials');
    }
};
