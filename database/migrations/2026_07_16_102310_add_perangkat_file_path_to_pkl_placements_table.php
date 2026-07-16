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
        Schema::table('pkl_placements', function (Blueprint $table) {
            $table->string('perangkat_file_path')->nullable()->after('is_perangkat_ready');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_placements', function (Blueprint $table) {
            $table->dropColumn('perangkat_file_path');
        });
    }
};
