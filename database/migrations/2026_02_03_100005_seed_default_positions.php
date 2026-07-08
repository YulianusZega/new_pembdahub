<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Seed default positions yang umum digunakan
        $positions = [
            // Structural positions
            ['name' => 'Kepala Sekolah', 'code' => 'KEPSEK', 'category' => 'structural', 'level' => 1, 'is_structural' => true, 'allowance' => 2000000],
            ['name' => 'Wakil Kepala Sekolah Kurikulum', 'code' => 'WAKA-KUR', 'category' => 'structural', 'level' => 2, 'is_structural' => true, 'allowance' => 1500000],
            ['name' => 'Wakil Kepala Sekolah Kesiswaan', 'code' => 'WAKA-SIS', 'category' => 'structural', 'level' => 2, 'is_structural' => true, 'allowance' => 1500000],
            ['name' => 'Wakil Kepala Sekolah Sarana Prasarana', 'code' => 'WAKA-SAR', 'category' => 'structural', 'level' => 2, 'is_structural' => true, 'allowance' => 1500000],
            ['name' => 'Wakil Kepala Sekolah Humas', 'code' => 'WAKA-HUM', 'category' => 'structural', 'level' => 2, 'is_structural' => true, 'allowance' => 1500000],
            
            // Functional positions (Guru)
            ['name' => 'Guru Mata Pelajaran', 'code' => 'GURU', 'category' => 'functional', 'level' => 3, 'is_structural' => false, 'allowance' => 500000],
            ['name' => 'Wali Kelas', 'code' => 'WAKEL', 'category' => 'functional', 'level' => 3, 'is_structural' => false, 'allowance' => 300000],
            ['name' => 'Guru BK/Konseling', 'code' => 'GURU-BK', 'category' => 'functional', 'level' => 3, 'is_structural' => false, 'allowance' => 500000],
            ['name' => 'Koordinator Mata Pelajaran', 'code' => 'KOORD-MAPEL', 'category' => 'functional', 'level' => 3, 'is_structural' => false, 'allowance' => 400000],
            
            // Staff positions
            ['name' => 'Kepala Tata Usaha', 'code' => 'KA-TU', 'category' => 'staff', 'level' => 2, 'is_structural' => false, 'allowance' => 800000],
            ['name' => 'Staff Tata Usaha', 'code' => 'STAFF-TU', 'category' => 'staff', 'level' => 3, 'is_structural' => false, 'allowance' => 300000],
            ['name' => 'Staff Keuangan', 'code' => 'STAFF-KEU', 'category' => 'staff', 'level' => 3, 'is_structural' => false, 'allowance' => 400000],
            ['name' => 'Bendahara', 'code' => 'BENDAHARA', 'category' => 'staff', 'level' => 2, 'is_structural' => false, 'allowance' => 700000],
            ['name' => 'Kepala Perpustakaan', 'code' => 'KA-PERPUS', 'category' => 'staff', 'level' => 3, 'is_structural' => false, 'allowance' => 500000],
            ['name' => 'Staff Perpustakaan', 'code' => 'STAFF-PERPUS', 'category' => 'staff', 'level' => 3, 'is_structural' => false, 'allowance' => 300000],
            ['name' => 'Laboran', 'code' => 'LABORAN', 'category' => 'staff', 'level' => 3, 'is_structural' => false, 'allowance' => 300000],
            
            // Support positions
            ['name' => 'Security', 'code' => 'SECURITY', 'category' => 'support', 'level' => 3, 'is_structural' => false, 'allowance' => 200000],
            ['name' => 'Cleaning Service', 'code' => 'CLEANING', 'category' => 'support', 'level' => 3, 'is_structural' => false, 'allowance' => 200000],
            ['name' => 'Driver', 'code' => 'DRIVER', 'category' => 'support', 'level' => 3, 'is_structural' => false, 'allowance' => 250000],
        ];

        foreach ($positions as $pos) {
            DB::table('positions')->insert([
                'school_id' => null, // Global positions
                'position_name' => $pos['name'],
                'position_code' => $pos['code'],
                'position_category' => $pos['category'],
                'position_level' => $pos['level'],
                'is_structural' => $pos['is_structural'],
                'allowance_amount' => $pos['allowance'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('positions')->truncate();
    }
};
