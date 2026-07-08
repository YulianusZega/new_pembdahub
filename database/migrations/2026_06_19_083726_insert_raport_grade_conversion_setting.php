<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('settings')->insert([
            'key' => 'raport_grade_conversion',
            'value' => json_encode([
                '7' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
                '8' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
                '9' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
                '10' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
                '11' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
                '12' => ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70],
            ]),
            'type' => 'json',
            'group' => 'raport',
            'description' => 'Konfigurasi konversi predikat rapor per tingkat kelas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->where('key', 'raport_grade_conversion')->delete();
    }
};
