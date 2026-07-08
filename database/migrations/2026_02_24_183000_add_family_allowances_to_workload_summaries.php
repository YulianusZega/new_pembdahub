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
        Schema::table('employee_workload_summaries', function (Blueprint $规划) {
            $规划->decimal('family_allowance', 15, 2)->default(0)->after('total_teaching_allowance');
            $规划->decimal('child_allowance', 15, 2)->default(0)->after('family_allowance');
            $规划->decimal('rice_allowance', 15, 2)->default(0)->after('child_allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_workload_summaries', function (Blueprint $table) {
            $table->dropColumn(['family_allowance', 'child_allowance', 'rice_allowance']);
        });
    }
};
