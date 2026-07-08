<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Position;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportSisfopembdaSeeder extends Seeder
{
    /**
     * Import jabatan (positions) dan tunjangan jabatan dari sisfopembda,
     * serta update gaji pokok pegawai.
     */
    public function run(): void
    {
        $this->importPositions();
        $this->updateBasicSalaries();
    }

    /**
     * Step 1: Update tunjangan jabatan yang sudah ada & tambah jabatan baru
     */
    private function importPositions(): void
    {
        $this->command->info('=== Importing Jabatan & Tunjangan ===');

        // =============================================
        // A. Update tunjangan jabatan untuk posisi GLOBAL yang sudah ada (ID 1-19)
        // =============================================
        $globalUpdates = [
            // Kepala Sekolah (id=1) — rata-rata: SMP=3jt, SMA=3.5jt, SMK=3.5jt → keep 2jt global, set per-school below
            // Wali Kelas (id=7) — SMP=250rb, SMA=325rb, SMK=250rb → already have school-specific, skip
            // Bendahara (id=13) — SMA=2jt, SMK=500rb → set per-school below
            // KTU (id=10) — KTU=1.2jt → update global
            10 => ['allowance_amount' => 1200000],  // Kepala Tata Usaha
            17 => ['allowance_amount' => 2000000],  // Security (Satpam = 2jt)
            18 => ['allowance_amount' => 300000],   // Cleaning Service = 300rb
        ];

        foreach ($globalUpdates as $id => $data) {
            $pos = Position::find($id);
            if ($pos) {
                $pos->update($data);
                $this->command->info("  Updated: {$pos->position_name} → Rp " . number_format($data['allowance_amount']));
            }
        }

        // =============================================
        // B. Tambah jabatan BARU per sekolah dari sisfopembda
        //    school_id: 1=SMP, 2=SMA, 3=SMK, null=global
        // =============================================
        $newPositions = [
            // === YAYASAN (school_id = null, global) ===
            [
                'school_id' => null,
                'position_name' => 'Ketua Yayasan',
                'position_code' => 'KET-YAY',
                'position_category' => 'structural',
                'position_level' => 1,
                'is_structural' => true,
                'allowance_amount' => 6500000,
                'description' => 'Ketua Yayasan Perguruan Pembda Nias',
            ],
            [
                'school_id' => null,
                'position_name' => 'Bendahara Yayasan',
                'position_code' => 'BEND-YAY',
                'position_category' => 'structural',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 3500000,
                'description' => 'Bendahara Yayasan Perguruan Pembda Nias',
            ],
            [
                'school_id' => null,
                'position_name' => 'Pengawas Yayasan',
                'position_code' => 'PGWS-YAY',
                'position_category' => 'structural',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 750000,
                'description' => 'Pengawas Yayasan',
            ],
            [
                'school_id' => null,
                'position_name' => 'Pembina',
                'position_code' => 'PEMB',
                'position_category' => 'structural',
                'position_level' => 1,
                'is_structural' => true,
                'allowance_amount' => 10250000,
                'description' => 'Pembina Yayasan',
            ],

            // === SMP (school_id = 1) ===
            [
                'school_id' => 1,
                'position_name' => 'Kepala Sekolah',
                'position_code' => 'KASEK-SMP',
                'position_category' => 'structural',
                'position_level' => 1,
                'is_structural' => true,
                'allowance_amount' => 3000000,
                'description' => 'Kepala Sekolah SMP',
            ],
            [
                'school_id' => 1,
                'position_name' => 'Wakil Kepala Sekolah',
                'position_code' => 'WAKASEK-SMP',
                'position_category' => 'structural',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 1875000,
                'description' => 'Wakil Kepala Sekolah SMP',
            ],
            [
                'school_id' => 1,
                'position_name' => 'PKS',
                'position_code' => 'PKS-SMP',
                'position_category' => 'structural',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 1000000,
                'description' => 'Pembantu Kepala Sekolah SMP',
            ],
            [
                'school_id' => 1,
                'position_name' => 'KTU',
                'position_code' => 'KTU-SMP',
                'position_category' => 'staff',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 1000000,
                'description' => 'Kepala Tata Usaha SMP',
            ],
            [
                'school_id' => 1,
                'position_name' => 'Wali Kelas',
                'position_code' => 'WALAS-SMP',
                'position_category' => 'functional',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 250000,
                'description' => 'Wali Kelas SMP',
            ],

            // === SMA (school_id = 2) ===
            [
                'school_id' => 2,
                'position_name' => 'Kepala Sekolah',
                'position_code' => 'KASEK-SMA',
                'position_category' => 'structural',
                'position_level' => 1,
                'is_structural' => true,
                'allowance_amount' => 3500000,
                'description' => 'Kepala Sekolah SMA',
            ],
            [
                'school_id' => 2,
                'position_name' => 'Wakil Kepala Sekolah',
                'position_code' => 'WAKASEK-SMA',
                'position_category' => 'structural',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 1300000,
                'description' => 'Wakil Kepala Sekolah SMA',
            ],
            [
                'school_id' => 2,
                'position_name' => 'PKS',
                'position_code' => 'PKS-SMA',
                'position_category' => 'structural',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 1300000,
                'description' => 'Pembantu Kepala Sekolah SMA',
            ],
            [
                'school_id' => 2,
                'position_name' => 'Bendahara',
                'position_code' => 'BEND-SMA',
                'position_category' => 'staff',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 2000000,
                'description' => 'Bendahara SMA',
            ],
            [
                'school_id' => 2,
                'position_name' => 'Tata Usaha',
                'position_code' => 'TU-SMA',
                'position_category' => 'staff',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 1650000,
                'description' => 'Tata Usaha SMA',
            ],

            // === SMK (school_id = 3) ===
            [
                'school_id' => 3,
                'position_name' => 'Kepala Sekolah',
                'position_code' => 'KASEK-SMK',
                'position_category' => 'structural',
                'position_level' => 1,
                'is_structural' => true,
                'allowance_amount' => 3500000,
                'description' => 'Kepala Sekolah SMK',
            ],
            [
                'school_id' => 3,
                'position_name' => 'PKS',
                'position_code' => 'PKS-SMK',
                'position_category' => 'structural',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 750000,
                'description' => 'Pembantu Kepala Sekolah SMK',
            ],
            [
                'school_id' => 3,
                'position_name' => 'KTU',
                'position_code' => 'KTU-SMK',
                'position_category' => 'staff',
                'position_level' => 2,
                'is_structural' => true,
                'allowance_amount' => 1000000,
                'description' => 'Kepala Tata Usaha SMK',
            ],
            [
                'school_id' => 3,
                'position_name' => 'Bendahara',
                'position_code' => 'BEND-SMK',
                'position_category' => 'staff',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 500000,
                'description' => 'Bendahara SMK',
            ],
            [
                'school_id' => 3,
                'position_name' => 'Pembantu Bendahara',
                'position_code' => 'PBEND-SMK',
                'position_category' => 'staff',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 1000000,
                'description' => 'Pembantu Bendahara SMK',
            ],
            [
                'school_id' => 3,
                'position_name' => 'Operator',
                'position_code' => 'OPR-SMK',
                'position_category' => 'staff',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 500000,
                'description' => 'Operator SMK',
            ],
            [
                'school_id' => 3,
                'position_name' => 'Kapro',
                'position_code' => 'KAPRO-SMK',
                'position_category' => 'functional',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 500000,
                'description' => 'Kepala Program Keahlian SMK',
            ],
            [
                'school_id' => 3,
                'position_name' => 'Koordinator',
                'position_code' => 'KOOR-SMK',
                'position_category' => 'functional',
                'position_level' => 3,
                'is_structural' => false,
                'allowance_amount' => 350000,
                'description' => 'Koordinator SMK',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($newPositions as $posData) {
            // Check if already exists (by name + school_id)
            $exists = Position::where('position_name', $posData['position_name'])
                ->where('school_id', $posData['school_id'])
                ->first();

            if ($exists) {
                // Update allowance if different
                if ($exists->allowance_amount != $posData['allowance_amount']) {
                    $exists->update(['allowance_amount' => $posData['allowance_amount']]);
                    $this->command->info("  Updated allowance: {$posData['position_name']} (school={$posData['school_id']}) → Rp " . number_format($posData['allowance_amount']));
                } else {
                    $skipped++;
                }
                continue;
            }

            $posData['is_active'] = true;
            Position::create($posData);
            $created++;
            $this->command->info("  Created: {$posData['position_name']} (school=" . ($posData['school_id'] ?? 'global') . ") → Rp " . number_format($posData['allowance_amount']));
        }

        $this->command->info("Positions: {$created} created, {$skipped} skipped (already exist)");
    }

    /**
     * Step 2: Update gaji pokok per pegawai berdasarkan nama
     * Matching by nama (case-insensitive, trimmed)
     */
    private function updateBasicSalaries(): void
    {
        $this->command->info(PHP_EOL . '=== Updating Gaji Pokok ===');

        // Data dari sisfopembda: nama => gaji_pokok (hanya yang > 0)
        $salaryData = [
            // SMP (unit_id=1)
            'Yonata Telaumbanua, S.Pd' => 1650000,
            'Dedi Putra Telaumbanua, S.Pd' => 1650000,
            'Beatus Nduru, S.Pd' => 2083725,       // sisfopembda: "Beatus Nduru", PembdaHub: "BEATUS NDRURU"
            'Nuriati Zega, SH' => 1852200,
            'Dewi Juli Sulastri Zega, SE' => 2100000,
            'Arman Jaya Harefa, S.E.' => 2200000,      // PTY, KTU SMP
            'Yarisman Waruwu, A.Md' => 1825000,        // PTY

            // SMA (unit_id=2)
            'Yuniria Telaumbanua' => 3749000,           // PTY, Tata Usaha SMA
            'Efaproditus Laoli' => 1850000,             // Honorer
            'Operator' => 2000000,                      // Honorer — name is literally "Operator"

            // SMK (unit_id=3)
            'Ninik Sadarwati Hia, S.Pd' => 1850000,    // GTY — sisfopembda: "Ninik Sadarwati Hia"
            'Yaitolo Ndara, S.Th' => 1600000,          // GTY — sisfopembda: "Yaitolo Ndara", PembdaHub: "Yaitolo Ndraha"
            'Markus Zebua, S.Pd' => 1600000,            // GTY
            'Martperan Putra Zebua, ST' => 2255000,     // Honorer, Non Kependidikan
            'Yulianus Zega, S.Kom' => 2500000,          // GTY — sisfopembda has extra title
            'Yelfi Deliani, S.Sos' => 1625000,          // Honorer
            'Sozaro Harefa, A.Md' => 2300000,           // Honorer
            'Herdiyana Lahagu, S.Ak' => 2000000,        // Honorer
            'Herdiyani Lahagu, S.Ap' => 2000000,        // Honorer
            'Herlinawati Telaumbanua, SE' => 1500000,   // Honorer
            'Molirhati Telaumbanua, A.Md' => 1750000,   // Honorer

            // Yayasan (unit_id=4)
            'Buala Laoli' => 3000000,                    // Satpam
        ];

        $updated = 0;
        $notFound = 0;

        foreach ($salaryData as $name => $salary) {
            // Try exact match first (case-insensitive)
            $employee = Employee::whereRaw('LOWER(TRIM(full_name)) = ?', [strtolower(trim($name))])->first();

            // Try partial match if not found
            if (!$employee) {
                $nameParts = explode(',', $name);
                $shortName = trim($nameParts[0]);
                $employee = Employee::whereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($shortName) . '%'])->first();
            }

            if ($employee) {
                $employee->update(['basic_salary' => $salary]);
                $updated++;
                $this->command->info("  ✓ {$employee->full_name} → Rp " . number_format($salary));
            } else {
                $notFound++;
                $this->command->warn("  ✗ NOT FOUND: {$name} (Rp " . number_format($salary) . ")");
            }
        }

        $this->command->info("Salary: {$updated} updated, {$notFound} not found");
    }
}
