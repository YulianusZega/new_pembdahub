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
        DB::table('schools')->insert([
            [
                'name' => 'SMPS Pembda 1 Gunungsitoli',
                'type' => 'SMP',
                'is_active' => false,
                'psb_is_active' => false,
            ],
            [
                'name' => 'SMAS Pembda 2 Gunungsitoli',
                'type' => 'SMA',
                'is_active' => false,
                'psb_is_active' => false,
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('schools')->whereIn('name', ['SMPS Pembda 1 Gunungsitoli', 'SMAS Pembda 2 Gunungsitoli'])->delete();
    }
};
