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
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->string('target_guru')->nullable()->after('scale_type'); // 'kejuruan', 'umum', or null
        });

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->string('teacher_type')->nullable()->after('school_id'); // 'kejuruan', 'umum', or null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('survey_questions', function (Blueprint $table) {
            $table->dropColumn('target_guru');
        });

        Schema::table('survey_responses', function (Blueprint $table) {
            $table->dropColumn('teacher_type');
        });
    }
};
