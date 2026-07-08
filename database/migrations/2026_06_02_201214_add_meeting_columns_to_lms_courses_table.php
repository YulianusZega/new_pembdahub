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
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->boolean('meeting_active')->default(false)->after('is_active');
            $table->timestamp('meeting_started_at')->nullable()->after('meeting_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_courses', function (Blueprint $table) {
            $table->dropColumn(['meeting_active', 'meeting_started_at']);
        });
    }
};
