<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add audio and video columns to cbt_questions table.
 * question_image already exists from initial migration.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cbt_questions', function (Blueprint $table) {
            $table->string('question_audio')->nullable()->after('question_image');
            $table->string('question_video')->nullable()->after('question_audio');
        });
    }

    public function down(): void
    {
        Schema::table('cbt_questions', function (Blueprint $table) {
            $table->dropColumn(['question_audio', 'question_video']);
        });
    }
};
