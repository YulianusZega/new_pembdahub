<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Seeder untuk membuat penugasan jabatan (employee_positions) bagi semua
 * Guru & Pegawai SMPS Pembda 2 yang belum memiliki record di employee_positions.
 * 
 * Sumber data:
 * - survey_db.penugasan     → mapping guru → kelas → mata pelajaran
 * - classrooms.homeroom_teacher_id → wali kelas
 * - subject_teacher         → kompetensi guru (guru mengajar mapel apa)
 * - sisfopembda             → jabatan struktural (sudah ada: Kepsek, Wakasek, KTU)
 * 
 * Yang dilakukan seeder ini:
 * 1. Jabatan "Guru Mata Pelajaran" (position_id=6) untuk semua guru yang mengajar
 * 2. Jabatan "Guru BK/Konseling" (position_id=8) untuk guru BK (Kristiani)
 * 3. Jabatan "Wali Kelas" (position_id=47) untuk guru yang menjadi wali kelas
 * 4. Update classroom_id pada record wali kelas yang sudah ada tapi belum ada classroom_id
 */
class AssignSMPEmployeePositionsSeeder extends Seeder
{
    // Position IDs (dari tabel positions)
    const POS_GURU_MAPEL   = 6;  // "Guru Mata Pelajaran" (global, functional)
    const POS_WAKEL_SMP    = 47; // "Wali Kelas" (school_id=1, SMP-specific)
    const POS_WAKEL_GLOBAL = 44; // "Wali Kelas" (global) — dipakai oleh existing records
    const POS_GURU_BK      = 8;  // "Guru BK/Konseling" (global, functional)
    
    // Academic Year
    const ACADEMIC_YEAR_ID = 1;  // TP. 2025/2026
    
    // School
    const SCHOOL_ID = 1; // SMPS Pembda 2
    
    /**
     * Mapping employee_id => data guru
     * Berdasarkan employees + survey_db.penugasan + classrooms.homeroom_teacher_id
     */
    private function getGuruData(): array
    {
        return [
            // employee_id => [nama, is_bk, classroom_id wali kelas (null jika bukan)]
            124 => ['name' => 'YONATA TELAUMBANUA, S.PD',          'is_bk' => false, 'wali_classroom_id' => 203], // IX-Albert Einstein (sudah ada ep, perlu update classroom_id)
            125 => ['name' => 'DEDI PUTRA TELAUMBANUA, S.PD',      'is_bk' => false, 'wali_classroom_id' => 204], // IX-Aristoteles
            126 => ['name' => 'MARSELINA MASARIA NDRURU, S.AG',    'is_bk' => false, 'wali_classroom_id' => null],
            127 => ['name' => 'DRA. KRISTIANI ZEBUA',              'is_bk' => true,  'wali_classroom_id' => 200], // VIII-Isaac Newton (juga Guru BK)
            128 => ['name' => 'BEATUS NDRURU, S.PD',               'is_bk' => false, 'wali_classroom_id' => null], // (sudah ada ep WALIKELAS)
            129 => ['name' => 'ELIAMAN ZAI, S.PD',                 'is_bk' => false, 'wali_classroom_id' => 198], // VII-Gregor Mendel
            130 => ['name' => 'NURIATI ZEGA, SH',                  'is_bk' => false, 'wali_classroom_id' => null],
            131 => ['name' => 'DEWI JULI SULASTRI ZEGA, S.E',      'is_bk' => false, 'wali_classroom_id' => null],
            132 => ['name' => 'YARNIWATI SARUMAHA, S.PD.K',        'is_bk' => false, 'wali_classroom_id' => 205], // IX-Pythagoras
            133 => ['name' => 'SOLIDARMAN JAYA MENDROFA, S.PD',    'is_bk' => false, 'wali_classroom_id' => 201], // VIII-Thomas Alva Edison
            134 => ['name' => 'CLARA NOVITA SABRINA, S.PD',        'is_bk' => false, 'wali_classroom_id' => null],
            135 => ['name' => 'ERWIN JHOSEP CLARK ZEBUA, A.MD.T',  'is_bk' => false, 'wali_classroom_id' => null],
            136 => ['name' => 'BERTHA TELAUMBANUA, S.PD',          'is_bk' => false, 'wali_classroom_id' => null],
            137 => ['name' => 'SRI RAHAYU TANJUNG, S.PD',          'is_bk' => false, 'wali_classroom_id' => null],
            138 => ['name' => 'HENY APRILIA TELAUMBANUA, S.PD',    'is_bk' => false, 'wali_classroom_id' => 199], // VIII-A.G. Bell
            139 => ['name' => 'NIGUENTS FALDES HULU, S.PD',        'is_bk' => false, 'wali_classroom_id' => null],
        ];
    }

    public function run(): void
    {
        $this->command->info('');
        $this->command->info('================================================================');
        $this->command->info('  Assign Employee Positions - SMPS Pembda 2 Gunungsitoli');
        $this->command->info('================================================================');
        
        DB::beginTransaction();
        
        try {
            $stats = [
                'guru_mapel_created' => 0,
                'guru_bk_created' => 0,
                'wali_kelas_created' => 0,
                'wali_kelas_updated' => 0,
                'skipped' => 0,
            ];

            $this->assignGuruMapel($stats);
            $this->assignGuruBK($stats);
            $this->assignWaliKelas($stats);
            $this->updateExistingWaliKelasClassroom($stats);
            
            DB::commit();
            
            $this->command->info('');
            $this->command->info('=== RINGKASAN ===');
            $this->command->info("  Guru Mata Pelajaran dibuat : {$stats['guru_mapel_created']}");
            $this->command->info("  Guru BK/Konseling dibuat   : {$stats['guru_bk_created']}");
            $this->command->info("  Wali Kelas dibuat           : {$stats['wali_kelas_created']}");
            $this->command->info("  Wali Kelas di-update        : {$stats['wali_kelas_updated']}");
            $this->command->info("  Dilewati (sudah ada)        : {$stats['skipped']}");
            $this->command->info('✅ Semua penugasan jabatan SMPS Pembda 2 berhasil dibuat!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            $this->command->error($e->getTraceAsString());
            Log::error('AssignSMPEmployeePositions failed: ' . $e->getMessage());
        }
    }

    /**
     * 1. Assign jabatan "Guru Mata Pelajaran" untuk semua guru yang mengajar
     *    tapi belum punya jabatan GURU di employee_positions
     */
    private function assignGuruMapel(array &$stats): void
    {
        $this->command->info('');
        $this->command->info('📋 [1/4] Assigning jabatan "Guru Mata Pelajaran"...');
        
        $guruData = $this->getGuruData();
        $now = now();
        $startDate = '2025-07-01'; // Awal tahun ajaran 2025/2026
        
        foreach ($guruData as $employeeId => $data) {
            // Skip jika sudah ada jabatan GURU aktif
            $exists = DB::table('employee_positions')
                ->where('employee_id', $employeeId)
                ->where('position_id', self::POS_GURU_MAPEL)
                ->whereNull('end_date')
                ->exists();
            
            if ($exists) {
                $this->command->line("  ✓ {$data['name']} — sudah ada jabatan Guru Mapel");
                $stats['skipped']++;
                continue;
            }
            
            // Verify employee exists and is active
            $employee = DB::table('employees')
                ->where('id', $employeeId)
                ->where('school_id', self::SCHOOL_ID)
                ->where('is_active', 1)
                ->first();
            
            if (!$employee) {
                $this->command->warn("  ⚠ Employee #{$employeeId} ({$data['name']}) tidak ditemukan/tidak aktif, skip.");
                $stats['skipped']++;
                continue;
            }
            
            // Hitung jam mengajar dari subject_teacher (jumlah mapel x default hours)
            $teachingSubjects = DB::table('subject_teacher as st')
                ->join('teachers as t', 'st.teacher_id', '=', 't.id')
                ->join('subjects as s', 'st.subject_id', '=', 's.id')
                ->where('t.employee_id', $employeeId)
                ->where('s.school_id', self::SCHOOL_ID)
                ->count();
            
            DB::table('employee_positions')->insert([
                'employee_id'     => $employeeId,
                'position_id'     => self::POS_GURU_MAPEL,
                'academic_year_id'=> self::ACADEMIC_YEAR_ID,
                'semester'        => 'full_year',
                'start_date'      => $startDate,
                'end_date'        => null,
                'sk_number'       => 'SK/SMP/GURU/' . date('Y') . '/' . str_pad($employeeId, 3, '0', STR_PAD_LEFT),
                'sk_date'         => $startDate,
                'notes'           => "Penugasan sebagai Guru Mata Pelajaran ({$teachingSubjects} mapel) - SMPS Pembda 2 TP 2025/2026",
                'is_primary'      => true,
                'workload_hours'  => 0,
                'position_allowance' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            
            $this->command->info("  ✅ {$data['name']} → Guru Mata Pelajaran ({$teachingSubjects} mapel)");
            $stats['guru_mapel_created']++;
        }
    }

    /**
     * 2. Assign jabatan "Guru BK/Konseling" untuk guru yang mengajar Bimbingan Konseling
     *    (DRA. KRISTIANI ZEBUA, employee_id=127)
     */
    private function assignGuruBK(array &$stats): void
    {
        $this->command->info('');
        $this->command->info('📋 [2/4] Assigning jabatan "Guru BK/Konseling"...');
        
        $guruData = $this->getGuruData();
        $now = now();
        $startDate = '2025-07-01';
        
        foreach ($guruData as $employeeId => $data) {
            if (!$data['is_bk']) continue;
            
            // Skip jika sudah ada
            $exists = DB::table('employee_positions')
                ->where('employee_id', $employeeId)
                ->where('position_id', self::POS_GURU_BK)
                ->whereNull('end_date')
                ->exists();
            
            if ($exists) {
                $this->command->line("  ✓ {$data['name']} — sudah ada jabatan Guru BK");
                $stats['skipped']++;
                continue;
            }
            
            DB::table('employee_positions')->insert([
                'employee_id'     => $employeeId,
                'position_id'     => self::POS_GURU_BK,
                'academic_year_id'=> self::ACADEMIC_YEAR_ID,
                'semester'        => 'full_year',
                'start_date'      => $startDate,
                'end_date'        => null,
                'sk_number'       => 'SK/SMP/BK/' . date('Y') . '/' . str_pad($employeeId, 3, '0', STR_PAD_LEFT),
                'sk_date'         => $startDate,
                'notes'           => 'Penugasan sebagai Guru BK/Konseling - SMPS Pembda 2 TP 2025/2026',
                'is_primary'      => false, // Primary = Guru Mapel
                'workload_hours'  => 0,
                'position_allowance' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            
            $this->command->info("  ✅ {$data['name']} → Guru BK/Konseling");
            $stats['guru_bk_created']++;
        }
    }

    /**
     * 3. Assign jabatan "Wali Kelas" untuk guru yang menjadi wali kelas
     *    tapi BELUM punya record WALIKELAS aktif di employee_positions
     */
    private function assignWaliKelas(array &$stats): void
    {
        $this->command->info('');
        $this->command->info('📋 [3/4] Assigning jabatan "Wali Kelas"...');
        
        $guruData = $this->getGuruData();
        $now = now();
        $startDate = '2025-07-01';
        
        foreach ($guruData as $employeeId => $data) {
            if (!$data['wali_classroom_id']) continue;
            
            // Skip jika sudah ada WALIKELAS aktif (cek semua variant position_id)
            $exists = DB::table('employee_positions')
                ->where('employee_id', $employeeId)
                ->whereIn('position_id', [self::POS_WAKEL_SMP, self::POS_WAKEL_GLOBAL, 7, 45, 46])
                ->whereNull('end_date')
                ->exists();
            
            if ($exists) {
                $this->command->line("  ✓ {$data['name']} — sudah ada jabatan Wali Kelas");
                $stats['skipped']++;
                continue;
            }
            
            // Get classroom name for notes
            $classroom = DB::table('classrooms')->where('id', $data['wali_classroom_id'])->first();
            $className = $classroom ? $classroom->class_name : "Classroom #{$data['wali_classroom_id']}";
            
            DB::table('employee_positions')->insert([
                'employee_id'     => $employeeId,
                'position_id'     => self::POS_WAKEL_SMP,
                'academic_year_id'=> self::ACADEMIC_YEAR_ID,
                'semester'        => 'full_year',
                'classroom_id'    => $data['wali_classroom_id'],
                'start_date'      => $startDate,
                'end_date'        => null,
                'sk_number'       => 'SK/SMP/WK/' . date('Y') . '/' . str_pad($employeeId, 3, '0', STR_PAD_LEFT),
                'sk_date'         => $startDate,
                'notes'           => "Penugasan sebagai Wali Kelas {$className} - SMPS Pembda 2 TP 2025/2026",
                'is_primary'      => false, // Primary = Guru Mapel
                'workload_hours'  => 0,
                'position_allowance' => 0,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);
            
            $this->command->info("  ✅ {$data['name']} → Wali Kelas {$className}");
            $stats['wali_kelas_created']++;
        }
    }

    /**
     * 4. Update classroom_id pada record wali kelas yang sudah ada
     *    tapi belum punya classroom_id (YONATA=124, BEATUS=128)
     */
    private function updateExistingWaliKelasClassroom(array &$stats): void
    {
        $this->command->info('');
        $this->command->info('📋 [4/4] Updating classroom_id pada Wali Kelas existing...');
        
        // Hanya update YONATA — BEATUS tidak punya classroom saat ini
        // (VII-Alessandro Volta id=195 sudah tidak ada di classrooms)
        $updates = [
            // employee_id => [classroom_id, name, class_name]
            124 => ['classroom_id' => 203, 'name' => 'YONATA TELAUMBANUA, S.PD',  'class' => 'IX-Albert Einstein'],
        ];
        
        foreach ($updates as $employeeId => $data) {
            // Find existing active WALIKELAS record without classroom_id
            $record = DB::table('employee_positions')
                ->where('employee_id', $employeeId)
                ->whereIn('position_id', [self::POS_WAKEL_SMP, self::POS_WAKEL_GLOBAL, 7, 44, 45, 46, 47])
                ->whereNull('end_date')
                ->whereNull('classroom_id')
                ->first();
            
            if (!$record) {
                // Check if already has classroom_id
                $hasClassroom = DB::table('employee_positions')
                    ->where('employee_id', $employeeId)
                    ->whereIn('position_id', [self::POS_WAKEL_SMP, self::POS_WAKEL_GLOBAL, 7, 44, 45, 46, 47])
                    ->whereNull('end_date')
                    ->whereNotNull('classroom_id')
                    ->exists();
                    
                if ($hasClassroom) {
                    $this->command->line("  ✓ {$data['name']} — classroom_id sudah ada");
                    $stats['skipped']++;
                } else {
                    $this->command->warn("  ⚠ {$data['name']} — record WALIKELAS aktif tidak ditemukan");
                    $stats['skipped']++;
                }
                continue;
            }
            
            DB::table('employee_positions')
                ->where('id', $record->id)
                ->update([
                    'classroom_id' => $data['classroom_id'],
                    'notes'        => "Penugasan sebagai Wali Kelas {$data['class']} - SMPS Pembda 2 TP 2025/2026",
                    'updated_at'   => now(),
                ]);
            
            $this->command->info("  ✅ {$data['name']} → classroom_id updated ke {$data['class']}");
            $stats['wali_kelas_updated']++;
        }
    }
}
