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
            $table->string('shift')->nullable()->after('company_name');
            $table->boolean('is_perangkat_ready')->default(false)->after('shift');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_placements', function (Blueprint $table) {
            $table->dropColumn(['shift', 'is_perangkat_ready']);
        });
    }
};
