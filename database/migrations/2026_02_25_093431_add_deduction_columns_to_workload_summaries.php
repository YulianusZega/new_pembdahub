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
        Schema::table('employee_workload_summaries', function (Blueprint $table) {
            $table->decimal('bpjs_kesehatan', 15, 2)->default(0)->after('rice_allowance');
            $table->decimal('bpjs_ketenagakerjaan', 15, 2)->default(0)->after('bpjs_kesehatan');
            $table->decimal('total_deductions', 15, 2)->default(0)->after('bpjs_ketenagakerjaan');
            $table->decimal('gross_pay', 15, 2)->default(0)->after('total_allowance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_workload_summaries', function (Blueprint $table) {
            $table->dropColumn(['bpjs_kesehatan', 'bpjs_ketenagakerjaan', 'total_deductions', 'gross_pay']);
        });
    }
};
