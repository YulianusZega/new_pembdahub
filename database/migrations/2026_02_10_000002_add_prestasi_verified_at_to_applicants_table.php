<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Adds prestasi_verified_at column to applicants table.
     * This column tracks when admin verifies achievement data for prestasi path applicants.
     * 
     * Prestasi Flow: submitted → prestasi_verified → document_verified → tested/scored → accepted
     * Reguler Flow:  submitted → payment_verified → document_verified → tested/scored → accepted
     */
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'prestasi_verified_at')) {
                $table->timestamp('prestasi_verified_at')->nullable();
            }
            if (!Schema::hasColumn('applicants', 'prestasi_rejection_reason')) {
                $table->text('prestasi_rejection_reason')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['prestasi_verified_at', 'prestasi_rejection_reason']);
        });
    }
};
