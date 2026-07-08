<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Seeder untuk menyelaraskan tabel positions di pembda_hub
 * dengan data jabatan RESMI dari database sisfopembda.
 * 
 * Sumber data: u474310197_sisfopembda.sql → tabel `jabatan`
 * 
 * Yang dilakukan:
 * 1. Mapping position lama → jabatan sisfopembda
 * 2. Replace semua positions dengan data sisfopembda + jabatan SMK khusus
 * 3. Update semua employee_positions.position_id sesuai mapping baru
 * 4. Hapus positions sample/default yang tidak ada di sisfopembda
 */
class SyncPositionsFromSisfopembdaSeeder extends Seeder
{
    /**
     * Data jabatan dari sisfopembda (SOURCE OF TRUTH)
     * Persis seperti di u474310197_sisfopembda.sql
     */
    private function getSisfopembdaJabatan(): array
    {
        return [
            // Yayasan
            ['nama' => 'Ketua Yayasan',           'tunjangan' => 6500000,  'code' => 'KET-YAY',     'category' => 'structural', 'level' => 1, 'school_id' => null],
            ['nama' => 'Bendahara Yayasan',        'tunjangan' => 3500000,  'code' => 'BEND-YAY',    'category' => 'structural', 'level' => 1, 'school_id' => null],
            ['nama' => 'Pengawas Yayasan',         'tunjangan' => 750000,   'code' => 'PGWS-YAY',    'category' => 'structural', 'level' => 1, 'school_id' => null],
            ['nama' => 'Pembina',                  'tunjangan' => 10250000, 'code' => 'PEMB',         'category' => 'structural', 'level' => 1, 'school_id' => null],
            
            // SMP (school_id = 1)
            ['nama' => 'Kasek SMP',                'tunjangan' => 3000000,  'code' => 'KASEK-SMP',    'category' => 'structural', 'level' => 1, 'school_id' => 1],
            ['nama' => 'Wakasek SMP',              'tunjangan' => 1875000,  'code' => 'WAKASEK-SMP',  'category' => 'structural', 'level' => 2, 'school_id' => 1],
            ['nama' => 'PKS SMP',                  'tunjangan' => 1000000,  'code' => 'PKS-SMP',      'category' => 'structural', 'level' => 2, 'school_id' => 1],
            ['nama' => 'KTU SMP',                  'tunjangan' => 1000000,  'code' => 'KTU-SMP',      'category' => 'staff',      'level' => 2, 'school_id' => 1],
            ['nama' => 'Wali Kelas SMP',           'tunjangan' => 250000,   'code' => 'WALIKELAS-SMP','category' => 'functional', 'level' => 3, 'school_id' => 1],
            ['nama' => 'Satpam',                   'tunjangan' => 2000000,  'code' => 'SATPAM',       'category' => 'support',    'level' => 3, 'school_id' => null],
            ['nama' => 'Cleaning Service',         'tunjangan' => 300000,   'code' => 'CLEANING',     'category' => 'support',    'level' => 3, 'school_id' => null],

            // SMA (school_id = 2)
            ['nama' => 'Kasek SMA',                'tunjangan' => 3500000,  'code' => 'KASEK-SMA',    'category' => 'structural', 'level' => 1, 'school_id' => 2],
            ['nama' => 'Wakasek SMA',              'tunjangan' => 1300000,  'code' => 'WAKASEK-SMA',  'category' => 'structural', 'level' => 2, 'school_id' => 2],
            ['nama' => 'PKS SMA',                  'tunjangan' => 1300000,  'code' => 'PKS-SMA',      'category' => 'structural', 'level' => 2, 'school_id' => 2],
            ['nama' => 'Bendahara SMA',            'tunjangan' => 2000000,  'code' => 'BEND-SMA',     'category' => 'staff',      'level' => 2, 'school_id' => 2],
            ['nama' => 'Tata Usaha SMA',           'tunjangan' => 1650000,  'code' => 'TU-SMA',       'category' => 'staff',      'level' => 2, 'school_id' => 2],
            ['nama' => 'Wali Kelas SMA',           'tunjangan' => 325000,   'code' => 'WALIKELAS-SMA','category' => 'functional', 'level' => 3, 'school_id' => 2],

            // SMK (school_id = 3)
            ['nama' => 'Kasek SMK',                'tunjangan' => 3500000,  'code' => 'KASEK-SMK',    'category' => 'structural', 'level' => 1, 'school_id' => 3],
            ['nama' => 'PKS SMK',                  'tunjangan' => 750000,   'code' => 'PKS-SMK',      'category' => 'structural', 'level' => 2, 'school_id' => 3],
            ['nama' => 'KTU SMK',                  'tunjangan' => 1000000,  'code' => 'KTU-SMK',      'category' => 'staff',      'level' => 2, 'school_id' => 3],
            ['nama' => 'KTU',                      'tunjangan' => 1200000,  'code' => 'KTU',          'category' => 'staff',      'level' => 2, 'school_id' => null],
            ['nama' => 'Bendahara SMK',            'tunjangan' => 500000,   'code' => 'BEND-SMK',     'category' => 'staff',      'level' => 2, 'school_id' => 3],
            ['nama' => 'Pembantu Bendahara SMK',   'tunjangan' => 1000000,  'code' => 'PBEND-SMK',    'category' => 'staff',      'level' => 2, 'school_id' => 3],
            ['nama' => 'Operator SMK',             'tunjangan' => 500000,   'code' => 'OPR-SMK',      'category' => 'staff',      'level' => 3, 'school_id' => 3],
            ['nama' => 'Kapro',                    'tunjangan' => 500000,   'code' => 'KAPRO-SMK',    'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Koordinator',              'tunjangan' => 350000,   'code' => 'KOOR-SMK',     'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Wali Kelas SMK',           'tunjangan' => 250000,   'code' => 'WALIKELAS-SMK','category' => 'functional', 'level' => 3, 'school_id' => 3],

            // Jabatan khusus SMK (dari ImportSmkTeachersCommand - struktur organisasi SMK)
            ['nama' => 'Wakil Kepala Sekolah',             'tunjangan' => 0, 'code' => 'WAKASEK',          'category' => 'structural', 'level' => 2, 'school_id' => 3],
            ['nama' => 'Koord. Bidang Bahan Ajar & Pelatihan','tunjangan' => 0, 'code' => 'KOORD_AJAR',   'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Kepala Perpustakaan',              'tunjangan' => 0, 'code' => 'KAPERPUS',         'category' => 'staff',      'level' => 3, 'school_id' => 3],
            ['nama' => 'Pembantu Kepala Sekolah Bidang Kurikulum','tunjangan' => 0, 'code' => 'PKS_KURIKULUM','category' => 'structural','level' => 2, 'school_id' => 3],
            ['nama' => 'Seksi Kominfo/Kreatif',            'tunjangan' => 0, 'code' => 'SEKSI_KOMINFO',    'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Seksi Pengembangan Diri',          'tunjangan' => 0, 'code' => 'SEKSI_PENGDIRI',   'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Pembantu Kepala Sekolah Bidang Kesiswaan','tunjangan' => 0, 'code' => 'PKS_KESISWAAN','category' => 'structural','level' => 2, 'school_id' => 3],
            ['nama' => 'Seksi BKK',                        'tunjangan' => 0, 'code' => 'SEKSI_BKK',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Seksi Penataan Lingkungan',        'tunjangan' => 0, 'code' => 'SEKSI_LINGKUNGAN', 'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Pembantu Kepala Sekolah Bidang Hubinmas','tunjangan' => 0, 'code' => 'PKS_HUBINMAS','category' => 'structural','level' => 2, 'school_id' => 3],
            ['nama' => 'Seksi Humas Dan Sosial',           'tunjangan' => 0, 'code' => 'SEKSI_HUMAS',      'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Seksi Pemeliharaan',               'tunjangan' => 0, 'code' => 'SEKSI_PEMELIHARAAN','category' => 'functional','level' => 3, 'school_id' => 3],
            ['nama' => 'Pembantu Kepala Sekolah Bidang Sarana Dan Prasarana','tunjangan' => 0, 'code' => 'PKS_SARPRAS','category' => 'structural','level' => 2, 'school_id' => 3],
            ['nama' => 'Kapro Desain Pemodelan Dan Informasi Bangunan (DPIB)','tunjangan' => 0, 'code' => 'KAPRO_DPIB','category' => 'functional','level' => 3, 'school_id' => 3],
            ['nama' => 'Kepala Bengkel TE/TAV',            'tunjangan' => 0, 'code' => 'KABENG_TAV',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Kepala LAB DPIB',                  'tunjangan' => 0, 'code' => 'KALAB_DPIB',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Kapro Teknik Otomotif (TO/TKR-TSM)','tunjangan' => 0, 'code' => 'KAPRO_TO',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Kepala Bengkel TKR',               'tunjangan' => 0, 'code' => 'KABENG_TKR',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Kepala Bengkel TSM',               'tunjangan' => 0, 'code' => 'KABENG_TSM',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Kapro TJKT/TKJ/PIC ACP',          'tunjangan' => 0, 'code' => 'KAPRO_TJKT',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
            ['nama' => 'Kepala LAB TKJ',                   'tunjangan' => 0, 'code' => 'KALAB_TKJ',       'category' => 'functional', 'level' => 3, 'school_id' => 3],
        ];
    }

    /**
     * Mapping dari OLD position_id → position_code yang sesuai di sisfopembda
     * Ini menerjemahkan position lama yang sample/default ke jabatan resmi
     */
    private function getOldToNewMapping(): array
    {
        return [
            // OLD ID => NEW position code
            // Sample/default positions → mapped to sisfopembda equivalent
            1  => 'KASEK-SMP',       // Kepala Sekolah (global, Rp2jt) → Kasek SMP (Rp3jt) - 3 dia pakai
            2  => 'WAKASEK-SMP',     // Waka Kurikulum (global) → Wakasek SMP
            6  => '__REMOVE__',      // Guru Mata Pelajaran (sample,Rp500rb) → HAPUS (tidak ada di sisfopembda)
            7  => 'WALIKELAS-SMP',   // Wali Kelas (global default) → Wali Kelas SMP
            8  => '__REMOVE__',      // Guru BK (sample,Rp500rb) → HAPUS (tidak ada di sisfopembda)
            9  => 'KOOR-SMK',        // Koordinator Mapel → Koordinator SMK
            
            // Already-correct positions from ImportSisfopembdaSeeder
            // These match by code and stay as-is
            22 => 'KASEK-SMK',       // Kasek SMK
            44 => 'WALIKELAS-SMP',   // Wali Kelas (global, old) → Wali Kelas SMP
            47 => 'WALIKELAS-SMP',   // Wali Kelas SMP → same
            45 => 'WALIKELAS-SMA',   // Wali Kelas SMA
            46 => 'WALIKELAS-SMK',   // Wali Kelas SMK

            // Positions that don't exist in sisfopembda - delete unused, keep used as-is
            3  => '__DELETE_UNUSED__', // Waka Kesiswaan global
            4  => '__DELETE_UNUSED__', // Waka Sarpras global
            5  => '__DELETE_UNUSED__', // Waka Humas global
            10 => '__DELETE_UNUSED__', // KA-TU global (sisfopembda punya KTU saja)
            11 => '__DELETE_UNUSED__', // Staff TU global
            12 => '__DELETE_UNUSED__', // Staff Keuangan global
            13 => '__DELETE_UNUSED__', // Bendahara global
            14 => '__DELETE_UNUSED__', // Kepala Perpustakaan global
            15 => '__DELETE_UNUSED__', // Staff Perpustakaan global
            16 => '__DELETE_UNUSED__', // Laboran global
            17 => '__DELETE_UNUSED__', // Security global (sisfopembda punya Satpam)
            18 => 'CLEANING',         // Cleaning Service
            19 => '__DELETE_UNUSED__', // Driver

            // Already correct - sisfopembda based positions
            48 => 'KET-YAY',
            49 => 'BEND-YAY',
            50 => 'PGWS-YAY',
            51 => 'PEMB',
            52 => 'KASEK-SMP',
            53 => 'WAKASEK-SMP',
            54 => 'PKS-SMP',
            55 => 'KTU-SMP',
            56 => 'KASEK-SMA',
            57 => 'WAKASEK-SMA',
            58 => 'PKS-SMA',
            59 => 'BEND-SMA',
            60 => 'TU-SMA',
            61 => 'PKS-SMK',
            62 => 'KTU-SMK',
            63 => 'BEND-SMK',
            64 => 'PBEND-SMK',
            65 => 'OPR-SMK',
            66 => 'KAPRO-SMK',
            67 => 'KOOR-SMK',

            // SMK struktur organisasi - keep as-is
            23 => 'WAKASEK',
            24 => 'KOORD_AJAR',
            25 => 'KAPERPUS',
            26 => 'PKS_KURIKULUM',
            27 => 'SEKSI_KOMINFO',
            28 => 'SEKSI_PENGDIRI',
            29 => 'PKS_KESISWAAN',
            30 => 'SEKSI_BKK',
            31 => 'SEKSI_LINGKUNGAN',
            32 => 'PKS_HUBINMAS',
            33 => 'SEKSI_HUMAS',
            34 => 'SEKSI_PEMELIHARAAN',
            35 => 'PKS_SARPRAS',
            36 => 'KAPRO_DPIB',
            37 => 'KABENG_TAV',
            38 => 'KALAB_DPIB',
            39 => 'KAPRO_TO',
            40 => 'KABENG_TKR',
            41 => 'KABENG_TSM',
            42 => 'KAPRO_TJKT',
            43 => 'KALAB_TKJ',
        ];
    }

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('================================================================');
        $this->command->info('  Sync Positions dari Sisfopembda - Data Resmi');
        $this->command->info('================================================================');
        
        DB::beginTransaction();
        
        try {
            // Step 1: Insert semua jabatan sisfopembda, get new IDs
            $newPositionMap = $this->insertSisfopembdaPositions();
            
            // Step 2: Remap employee_positions ke new position IDs
            $this->remapEmployeePositions($newPositionMap);
            
            // Step 3: Delete old positions
            $this->deleteOldPositions($newPositionMap);
            
            DB::commit();
            $this->command->info('');
            $this->command->info('✅ Semua jabatan berhasil disinkronkan dengan data sisfopembda!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            Log::error('SyncPositionsFromSisfopembda failed: ' . $e->getMessage());
        }
    }

    /**
     * Step 1: Truncate positions dan insert ulang dari sisfopembda
     * Return: map of position_code => new_id
     */
    private function insertSisfopembdaPositions(): array
    {
        $this->command->info('');
        $this->command->info('📋 [Step 1] Inserting jabatan sisfopembda...');

        $jabatanList = $this->getSisfopembdaJabatan();
        $now = now();
        $codeToId = [];

        // First, record existing positions that we want to keep by code
        $existing = DB::table('positions')->get();
        $existingByCode = [];
        foreach ($existing as $e) {
            $key = $e->position_code;
            if ($e->school_id) $key .= '_s' . $e->school_id;
            $existingByCode[$key] = $e;
        }

        foreach ($jabatanList as $jab) {
            $lookupKey = $jab['code'];
            if ($jab['school_id']) $lookupKey .= '_s' . $jab['school_id'];

            // Check if position with same code+school already exists
            $existingPos = $existingByCode[$lookupKey] ?? null;

            if ($existingPos) {
                // Update existing position with sisfopembda data
                DB::table('positions')
                    ->where('id', $existingPos->id)
                    ->update([
                        'position_name'     => $jab['nama'],
                        'position_code'     => $jab['code'],
                        'position_category' => $jab['category'],
                        'position_level'    => $jab['level'],
                        'school_id'         => $jab['school_id'],
                        'allowance_amount'  => $jab['tunjangan'],
                        'is_structural'     => in_array($jab['category'], ['structural']),
                        'is_active'         => true,
                        'updated_at'        => $now,
                    ]);
                
                $codeToId[$jab['code']] = $existingPos->id;
                $this->command->line("  ↻ Updated ID:{$existingPos->id} {$jab['nama']} → Rp " . number_format($jab['tunjangan'], 0, ',', '.'));
            } else {
                // Insert new position
                $newId = DB::table('positions')->insertGetId([
                    'position_name'     => $jab['nama'],
                    'position_code'     => $jab['code'],
                    'position_category' => $jab['category'],
                    'position_level'    => $jab['level'],
                    'school_id'         => $jab['school_id'],
                    'allowance_amount'  => $jab['tunjangan'],
                    'is_structural'     => in_array($jab['category'], ['structural']),
                    'is_active'         => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ]);

                $codeToId[$jab['code']] = $newId;
                $this->command->info("  ✅ New ID:{$newId} {$jab['nama']} → Rp " . number_format($jab['tunjangan'], 0, ',', '.'));
            }
        }

        $this->command->info("  → Total jabatan: " . count($codeToId));
        return $codeToId;
    }

    /**
     * Step 2: Remap employee_positions.position_id ke position ID baru
     */
    private function remapEmployeePositions(array $codeToId): void
    {
        $this->command->info('');
        $this->command->info('📋 [Step 2] Remapping employee_positions...');

        $mapping = $this->getOldToNewMapping();
        $remapped = 0;
        $removed = 0;
        $skipped = 0;

        // Get all employee_positions
        $allEp = DB::table('employee_positions')->get();

        foreach ($allEp as $ep) {
            $oldPosId = $ep->position_id;
            
            if (!isset($mapping[$oldPosId])) {
                $this->command->warn("  ⚠ position_id:{$oldPosId} tidak ada di mapping, skip ep:{$ep->id}");
                $skipped++;
                continue;
            }

            $targetCode = $mapping[$oldPosId];

            if ($targetCode === '__REMOVE__') {
                // Jabatan sample yang tidak ada di sisfopembda → hapus employee_position
                $pos = DB::table('positions')->where('id', $oldPosId)->first();
                $emp = DB::table('employees')->where('id', $ep->employee_id)->first();
                $empName = $emp->full_name ?? "emp:{$ep->employee_id}";
                $posName = $pos->position_name ?? "pos:{$oldPosId}";
                
                DB::table('employee_positions')->where('id', $ep->id)->delete();
                $this->command->warn("  🗑 Removed: {$empName} ← {$posName} (tidak ada di sisfopembda)");
                $removed++;
                continue;
            }

            if ($targetCode === '__DELETE_UNUSED__') {
                // Hapus jika unused, keep jika used (should not be used)
                DB::table('employee_positions')->where('id', $ep->id)->delete();
                $removed++;
                continue;
            }

            if (!isset($codeToId[$targetCode])) {
                $this->command->warn("  ⚠ Target code '{$targetCode}' tidak ditemukan di new positions, skip ep:{$ep->id}");
                $skipped++;
                continue;
            }

            $newPosId = $codeToId[$targetCode];

            if ($oldPosId != $newPosId) {
                DB::table('employee_positions')
                    ->where('id', $ep->id)
                    ->update(['position_id' => $newPosId, 'updated_at' => now()]);
                
                $emp = DB::table('employees')->where('id', $ep->employee_id)->first();
                $empName = $emp->full_name ?? "emp:{$ep->employee_id}";
                $this->command->line("  ↻ {$empName}: pos {$oldPosId} → {$newPosId} ({$targetCode})");
                $remapped++;
            } else {
                $skipped++;
            }
        }

        $this->command->info("  → Remapped: {$remapped}, Removed: {$removed}, Skipped: {$skipped}");
    }

    /**
     * Step 3: Delete old positions that are not in sisfopembda and have no more references
     */
    private function deleteOldPositions(array $codeToId): void
    {
        $this->command->info('');
        $this->command->info('📋 [Step 3] Cleaning up old positions...');

        $validIds = array_values($codeToId);
        $deleted = 0;

        $oldPositions = DB::table('positions')
            ->whereNotIn('id', $validIds)
            ->get();

        foreach ($oldPositions as $old) {
            // Check if still referenced
            $refCount = DB::table('employee_positions')
                ->where('position_id', $old->id)
                ->count();

            if ($refCount > 0) {
                $this->command->warn("  ⚠ ID:{$old->id} {$old->position_name} masih punya {$refCount} referensi, skip.");
                continue;
            }

            DB::table('positions')->where('id', $old->id)->delete();
            $this->command->line("  🗑 Deleted ID:{$old->id} {$old->position_name} ({$old->position_code})");
            $deleted++;
        }

        $this->command->info("  → Deleted: {$deleted} old positions");
    }
}
