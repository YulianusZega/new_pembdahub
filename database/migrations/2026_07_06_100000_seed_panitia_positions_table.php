<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Position;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $positions = [
            [
                'position_code' => 'PAN-CBT',
                'position_name' => 'Panitia CBT / Ujian',
                'position_category' => 'functional',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 0,
                'description' => 'Tugas Tambahan Panitia Pelaksana & Pengawas CBT / Ujian Sekolah',
                'is_active' => true
            ],
            [
                'position_code' => 'PAN-PKL',
                'position_name' => 'Panitia PKL & Hubin',
                'position_category' => 'functional',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 0,
                'description' => 'Tugas Tambahan Panitia Penempatan & Monitoring PKL / Hubungan Industri',
                'is_active' => true
            ],
            [
                'position_code' => 'PAN-PROYEK',
                'position_name' => 'Panitia Project & TA',
                'position_category' => 'functional',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 0,
                'description' => 'Tugas Tambahan Panitia Project Akhir / P5 & Tugas Akhir',
                'is_active' => true
            ],
            [
                'position_code' => 'TIM-PKS',
                'position_name' => 'Tim PKS / Kedisiplinan',
                'position_category' => 'functional',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 0,
                'description' => 'Tugas Tambahan Patroli Keamanan Sekolah & Tim Kedisiplinan Siswa',
                'is_active' => true
            ],
        ];

        foreach ($positions as $pos) {
            // Gunakan updateOrCreate untuk menghindari duplikasi dan mematuhi prinsip non-destructive
            Position::updateOrCreate(
                [
                    'position_code' => $pos['position_code'],
                    'school_id' => null // posisi global
                ],
                $pos
            );
        }

        // Hapus GURU-PIKET dari tugas jabatan struktural jika sempat terbuat, sesuai arahan (bersifat temporer/harian)
        $piketPosIds = Position::whereIn('position_code', ['GURU-PIKET', 'PAN-PIKET'])->pluck('id');
        if ($piketPosIds->isNotEmpty()) {
            DB::table('employee_positions')->whereIn('position_id', $piketPosIds)->delete();
            Position::whereIn('id', $piketPosIds)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Sesuai aturan pengembangan PembdaHUB: DILARANG menghapus data tanpa konfirmasi,
        // sehingga metode down dibiarkan kosong atau aman agar tidak menghapus data relasi penting.
    }
};
