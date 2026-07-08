<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA) as a school record
     * with type='yayasan' (Opsi A architecture).
     */
    public function up(): void
    {
        // Update existing yayasan record or insert a new one
        $existing = DB::table('schools')->whereRaw('LOWER(type) = ?', ['yayasan'])->first();

        if ($existing) {
            // Update name and normalize type
            DB::table('schools')->where('id', $existing->id)->update([
                'name' => 'Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA)',
                'type' => 'yayasan',
            ]);
        } else {
            // Insert the Yayasan record
            DB::table('schools')->insert([
                'name' => 'Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA)',
                'type' => 'yayasan',
                'address' => 'Jl. Pelita No.09, Kelurahan Ilir',
                'city' => 'Kota Gunungsitoli',
                'province' => 'Sumatera Utara',
                'is_active' => true,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('schools')->where('type', 'yayasan')->delete();
    }
};
