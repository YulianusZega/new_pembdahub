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
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->string('group_code', 50)->nullable()->after('sk_reference')->comment('Link combined classes taught together');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->string('group_code', 50)->nullable()->after('room')->comment('Link combined schedules');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teaching_assignments', function (Blueprint $table) {
            $table->dropColumn('group_code');
        });

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('group_code');
        });
    }
};
