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
        if (Schema::hasTable('lms_quiz_questions')) {
            Schema::table('lms_quiz_questions', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_quiz_questions', 'image_path')) {
                    $table->string('image_path')->nullable()->after('correct_answer');
                }
                if (!Schema::hasColumn('lms_quiz_questions', 'video_url')) {
                    $table->string('video_url')->nullable()->after('image_path');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lms_quiz_questions')) {
            Schema::table('lms_quiz_questions', function (Blueprint $table) {
                if (Schema::hasColumn('lms_quiz_questions', 'image_path')) {
                    $table->dropColumn('image_path');
                }
                if (Schema::hasColumn('lms_quiz_questions', 'video_url')) {
                    $table->dropColumn('video_url');
                }
            });
        }
    }
};
