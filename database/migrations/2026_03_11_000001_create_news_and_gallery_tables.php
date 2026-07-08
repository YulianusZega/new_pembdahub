<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // News / Berita table
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->text('content')->nullable();
            $table->enum('category', ['prestasi', 'kegiatan', 'kerjasama', 'pengumuman'])->default('kegiatan');
            $table->string('image')->nullable();
            $table->string('icon', 100)->default('fa-solid fa-newspaper');
            $table->string('gradient_from', 20)->default('#2563eb');
            $table->string('gradient_to', 20)->default('#60a5fa');
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_published', 'published_at']);
            $table->index('category');
        });

        // Gallery Items table
        Schema::create('gallery_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('caption', 500)->nullable();
            $table->string('image');
            $table->enum('category', ['upacara', 'praktikum', 'olahraga', 'seni', 'bengkel', 'prestasi', 'komputer', 'lainnya'])->default('lainnya');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_items');
        Schema::dropIfExists('news');
    }
};
