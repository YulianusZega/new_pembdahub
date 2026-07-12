<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_forums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('school_id'); // From which school this forum belongs
            $table->string('category');
            $table->string('title');
            $table->longText('content');
            $table->string('image_path')->nullable();
            $table->unsignedInteger('views_count')->default(0);
            $table->timestamps();

            // Make sure the index is fast for filtering by school
            $table->index('school_id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_forums');
    }
};
