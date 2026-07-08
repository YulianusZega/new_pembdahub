<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;
use App\Models\School;
use Illuminate\Support\Facades\DB;

class ImportSisfopembdaEmployeesSeeder extends Seeder
{
    /**
     * Import 12 pegawai dari sisfopembda yang belum ada di PembdaHub.
     * Juga membuat school "Yayasan" jika belum ada.
     */
    public function run(): void
    {
        $this->createYayasanSchool();
        $this->importEmployees();
        $this->assignPositions();
    }

    /**
     * Step 1: Buat school Yayasan jika belum ada (untuk Buala Laoli & staff yayasan)
     */
    private function createYayasanSchool(): void
    {
        $this->command->info('=== Step 1: Cek/Buat School Yayasan ===');

        $yayasan = School::where('type', 'Yayasan')->first();
        if (!$yayasan) {
            $yayasan = School::create([
                'name' => 'Yayasan Perguruan Pembda Nias',
                'type' => 'Yayasan',
                'is_active' => true,
            ]);
            $this->command->info("  CREATED: Yayasan Perguruan Pembda Nias (id={$yayasan->id})");
        } else {
            $this->command->info("  EXISTS: {$yayasan->name} (id={$yayasan->id})");
        }
    }

    /**
     * Step 2: Import 12 pegawai
     */
    private function importEmployees(): void
    {
        $this->command->info('');
        $this->command->info('=== Step 2: Import Pegawai ===');

        // Resolve school IDs
        $smpId = 1;
        $smaId = 2;
        $smkId = 3;
        $yayasanId = School::where('type', 'Yayasan')->first()->id;

        $employees = [
            // ---- SMP (school_id=1) ----
            [
                'school_id'         => $smpId,
                'employee_code'     => 'SMP-001',
                'full_name'         => 'Arman Jaya Harefa, S.E.',
                'gender'            => 'L',
                'employee_type'     => 'staff_tu',
                'employment_status' => 'yayasan', // PTY
                'marital_status'    => 'menikah',
                'children_count'    => 2,
                'basic_salary'      => 2200000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smpId,
                'employee_code'     => 'SMP-002',
                'full_name'         => 'Yarisman Waruwu, A.Md',
                'gender'            => 'L',
                'employee_type'     => 'other',
                'employment_status' => 'yayasan', // PTY
                'marital_status'    => 'menikah',
                'children_count'    => 0,
                'basic_salary'      => 1825000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],

            // ---- SMA (school_id=2) ----
            [
                'school_id'         => $smaId,
                'employee_code'     => 'SMA-001',
                'full_name'         => 'Yuniria Telaumbanua, A.Md',
                'gender'            => 'P',
                'employee_type'     => 'staff_keuangan',
                'employment_status' => 'yayasan', // PTY
                'marital_status'    => 'menikah',
                'children_count'    => 0,
                'basic_salary'      => 3749000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smaId,
                'employee_code'     => 'SMA-002',
                'full_name'         => 'Efaproditus Laoli',
                'gender'            => 'L',
                'employee_type'     => 'staff_tu',
                'employment_status' => 'honorer',
                'marital_status'    => 'menikah',
                'children_count'    => 0,
                'basic_salary'      => 1850000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smaId,
                'employee_code'     => 'SMA-003',
                'full_name'         => 'Operator SMA',
                'gender'            => 'L',
                'employee_type'     => 'other',
                'employment_status' => 'honorer',
                'marital_status'    => 'belum_menikah',
                'children_count'    => 0,
                'basic_salary'      => 2000000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],

            // ---- SMK (school_id=3) ----
            [
                'school_id'         => $smkId,
                'employee_code'     => 'SMK-046',
                'full_name'         => 'Yelfi Deliani, S.Sos',
                'gender'            => 'P',
                'employee_type'     => 'staff_keuangan',
                'employment_status' => 'honorer',
                'marital_status'    => 'menikah',
                'children_count'    => 0,
                'basic_salary'      => 1625000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smkId,
                'employee_code'     => 'SMK-047',
                'full_name'         => 'Sozaro Harefa, A.Md',
                'gender'            => 'L',
                'employee_type'     => 'staff_tu',
                'employment_status' => 'honorer',
                'marital_status'    => 'menikah',
                'children_count'    => 2,
                'basic_salary'      => 2300000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smkId,
                'employee_code'     => 'SMK-048',
                'full_name'         => 'Herdiyana Lahagu, S.Ak',
                'gender'            => 'P',
                'employee_type'     => 'other',
                'employment_status' => 'honorer',
                'marital_status'    => 'belum_menikah',
                'children_count'    => 0,
                'basic_salary'      => 2000000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smkId,
                'employee_code'     => 'SMK-049',
                'full_name'         => 'Herdiyani Lahagu, S.Ap',
                'gender'            => 'P',
                'employee_type'     => 'other',
                'employment_status' => 'honorer',
                'marital_status'    => 'menikah',
                'children_count'    => 0,
                'basic_salary'      => 2000000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smkId,
                'employee_code'     => 'SMK-050',
                'full_name'         => 'Herlinawati Telaumbanua, SE',
                'gender'            => 'P',
                'employee_type'     => 'other',
                'employment_status' => 'honorer',
                'marital_status'    => 'belum_menikah',
                'children_count'    => 0,
                'basic_salary'      => 1500000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
            [
                'school_id'         => $smkId,
                'employee_code'     => 'SMK-051',
                'full_name'         => 'Molirhati Telaumbanua, A.Md',
                'gender'            => 'P',
                'employee_type'     => 'other',
                'employment_status' => 'honorer',
                'marital_status'    => 'menikah',
                'children_count'    => 0,
                'basic_salary'      => 1750000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],

            // ---- Yayasan ----
            [
                'school_id'         => $yayasanId,
                'employee_code'     => 'YYS-001',
                'full_name'         => 'Buala Laoli',
                'gender'            => 'L',
                'employee_type'     => 'other',
                'employment_status' => 'honorer',
                'marital_status'    => 'menikah',
                'children_count'    => 0,
                'basic_salary'      => 3000000,
                'tmt_date'          => '2025-08-01',
                'is_active'         => true,
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($employees as $data) {
            // Cek apakah sudah ada berdasarkan nama (case-insensitive)
            $exists = Employee::whereRaw('LOWER(full_name) LIKE ?', [
                '%' . strtolower(explode(',', $data['full_name'])[0]) . '%'
            ])->where('school_id', $data['school_id'])->first();

            if ($exists) {
                $this->command->warn("  SKIP (sudah ada): {$data['full_name']} → existing: {$exists->full_name} (id={$exists->id})");
                $skipped++;
                continue;
            }

            // Cek employee_code unik
            if (Employee::where('employee_code', $data['employee_code'])->exists()) {
                // Generate alternative code
                $data['employee_code'] = $data['employee_code'] . '-' . time();
                $this->command->warn("  Code conflict, using: {$data['employee_code']}");
            }

            $emp = Employee::create($data);
            $this->command->info("  CREATED: [{$emp->employee_code}] {$emp->full_name} → school_id={$emp->school_id}, salary=Rp " . number_format($emp->basic_salary, 0, ',', '.'));
            $created++;
        }

        $this->command->info("  Total: {$created} created, {$skipped} skipped");
    }

    /**
     * Step 3: Assign jabatan (positions) ke pegawai yang baru diimport
     */
    private function assignPositions(): void
    {
        $this->command->info('');
        $this->command->info('=== Step 3: Assign Jabatan ke Pegawai ===');

        // Mapping: employee name → position assignments
        // Position IDs from database:
        // 55 = KTU SMP (school_id=1, 1jt)
        // 59 = Bendahara SMA (school_id=2, 2jt)
        // 60 = Tata Usaha SMA (school_id=2, 1.65jt)
        // 45 = Wali Kelas SMA (school_id=2, 250rb) — Efaproditus juga Wali Kelas
        // 64 = Pembantu Bendahara SMK (school_id=3, 1jt)
        // 62 = KTU SMK (school_id=3, 1jt)
        // 65 = Operator SMK (school_id=3, 500rb)

        $assignments = [
            // [nama_partial, school_id, position_id, is_primary]
            ['Arman Jaya Harefa',  1, 55, true],   // KTU SMP
            ['Yuniria',       2, 59, true],   // Bendahara SMA
            ['Efaproditus',   2, 60, true],   // Tata Usaha SMA
            ['Yelfi Deliani', 3, 64, true],   // Pembantu Bendahara SMK
            ['Sozaro',        3, 62, true],   // KTU SMK
            ['Sozaro',        3, 65, false],  // Operator SMK (jabatan ke-2)
        ];

        $assigned = 0;
        foreach ($assignments as [$namePartial, $schoolId, $positionId, $isPrimary]) {
            $emp = Employee::where('school_id', $schoolId)
                ->whereRaw('LOWER(full_name) LIKE ?', ['%' . strtolower($namePartial) . '%'])
                ->first();

            if (!$emp) {
                $this->command->warn("  NOT FOUND: {$namePartial} (school_id={$schoolId})");
                continue;
            }

            // Cek apakah sudah ada assignment
            $existsAssignment = DB::table('employee_positions')
                ->where('employee_id', $emp->id)
                ->where('position_id', $positionId)
                ->exists();

            if ($existsAssignment) {
                $this->command->warn("  SKIP (sudah assigned): {$emp->full_name} → position_id={$positionId}");
                continue;
            }

            DB::table('employee_positions')->insert([
                'employee_id' => $emp->id,
                'position_id' => $positionId,
                'start_date'  => '2025-08-01',
                'is_primary'  => $isPrimary,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            $posName = DB::table('positions')->where('id', $positionId)->value('position_name');
            $this->command->info("  ASSIGNED: {$emp->full_name} → {$posName} (position_id={$positionId})");
            $assigned++;
        }

        $this->command->info("  Total: {$assigned} position assignments created");
    }
}
