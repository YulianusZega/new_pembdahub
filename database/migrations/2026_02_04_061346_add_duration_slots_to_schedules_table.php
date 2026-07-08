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
        Schema::table('schedules', function (Blueprint $table) {
            $table->tinyInteger('duration_slots')->default(1)->after('time_slot_id')->comment('Jumlah jam pelajaran berturut-turut (1-4)');
            $table->index('duration_slots');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropIndex(['duration_slots']);
            $table->dropColumn('duration_slots');
        });
    }
};
