<?php

namespace Database\Seeders;

use App\Models\TefaEmployee;
use Illuminate\Database\Seeder;

class TefaEmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (TefaEmployee::count() === 0) {
            $defaultEmployees = [
                [
                    'unit_name' => 'Bengkelin Tefa SMKS Pembda Nias',
                    'name' => 'Karyawan Tefa 1 (Mekanik Kepala)',
                    'position' => 'Mekanik Kepala',
                    'phone' => '081234567890',
                    'is_active' => true,
                ],
                [
                    'unit_name' => 'Bengkelin Tefa SMKS Pembda Nias',
                    'name' => 'Karyawan Tefa 2 (Teknisi Mesin)',
                    'position' => 'Teknisi Mesin',
                    'phone' => '081234567891',
                    'is_active' => true,
                ],
                [
                    'unit_name' => 'Bengkelin Tefa SMKS Pembda Nias',
                    'name' => 'Karyawan Tefa 3 (Admin Layanan)',
                    'position' => 'Admin Layanan',
                    'phone' => '081234567892',
                    'is_active' => true,
                ],
            ];

            foreach ($defaultEmployees as $emp) {
                TefaEmployee::create($emp);
            }

            $this->command?->info('3 Karyawan Bengkelin Tefa SMKS Pembda Nias berhasil diseed!');
        }
    }
}
