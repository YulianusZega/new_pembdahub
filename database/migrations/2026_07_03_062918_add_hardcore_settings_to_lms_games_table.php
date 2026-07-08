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
        Schema::table('lms_games', function (Blueprint $table) {
            $table->integer('time_limit')->nullable()->after('reward_points')->comment('Batas waktu per soal dalam detik');
            $table->integer('lives_count')->nullable()->after('time_limit')->comment('Jumlah nyawa untuk Hardcore Mode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lms_games', function (Blueprint $table) {
            $table->dropColumn(['time_limit', 'lives_count']);
        });
    }
};
