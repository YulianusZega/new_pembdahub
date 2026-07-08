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
        Schema::table('final_projects', function (Blueprint $table) {
            $table->string('current_stage', 50)->default('bab1')->after('status');
        });

        Schema::table('final_project_logs', function (Blueprint $table) {
            $table->string('stage', 50)->default('bab1')->after('log_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('final_projects', function (Blueprint $table) {
            $table->dropColumn('current_stage');
        });

        Schema::table('final_project_logs', function (Blueprint $table) {
            $table->dropColumn('stage');
        });
    }
};
