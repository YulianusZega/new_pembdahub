<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Seed Yayasan school entry if not exists
        $exists = DB::table('schools')->where('type', 'yayasan')->exists();

        if (!$exists) {
            DB::table('schools')->insert([
                'name' => 'Yayasan Perguruan PEMBDA Nias',
                'type' => 'yayasan',
                'address' => 'Gunungsitoli, Nias',
                'city' => 'Gunungsitoli',
                'province' => 'Sumatera Utara',
                'is_active' => true,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('schools')->where('type', 'yayasan')->delete();
    }
};
