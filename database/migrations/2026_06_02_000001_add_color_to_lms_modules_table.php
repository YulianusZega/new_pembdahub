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
        if (Schema::hasTable('lms_modules')) {
            Schema::table('lms_modules', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_modules', 'color')) {
                    $table->string('color', 20)->nullable()->after('is_active');
                }
            });
        }

        if (Schema::hasTable('lms_quizzes')) {
            Schema::table('lms_quizzes', function (Blueprint $table) {
                if (!Schema::hasColumn('lms_quizzes', 'module_id')) {
                    $table->unsignedBigInteger('module_id')->nullable()->after('course_id');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('lms_modules')) {
            Schema::table('lms_modules', function (Blueprint $table) {
                if (Schema::hasColumn('lms_modules', 'color')) {
                    $table->dropColumn('color');
                }
            });
        }

        if (Schema::hasTable('lms_quizzes')) {
            Schema::table('lms_quizzes', function (Blueprint $table) {
                if (Schema::hasColumn('lms_quizzes', 'module_id')) {
                    $table->dropColumn('module_id');
                }
            });
        }
    }
};
