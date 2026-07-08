<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Seeder untuk melengkapi data Wali Kelas dan Kompetensi (subject_teacher)
 * untuk SMPS Pembda 2 Gunungsitoli (school_id = 1)
 * 
 * Sumber data:
 * - Wali Kelas: sisfopembda.penugasan_jabatan (jabatan_id=11 = "Wali Kelas SMP")
 * - Kompetensi: survey_db.penugasan (mapping guru → mapel)
 * 
 * Mapping pegawai sisfopembda → PembdaHub teacher_id:
 *   pegawai 15 (Yonata)      → teacher 124
 *   pegawai 16 (Dedi Putra)   → teacher 125
 *   pegawai 17 (Marselina)    → teacher 126
 *   pegawai 18 (Kristiani)    → teacher 127
 *   pegawai 19 (Beatus)       → teacher 128
 *   pegawai 20 (Eliaman)      → teacher 129
 *   pegawai 21 (Nuriati)      → teacher 130
 *   pegawai 22 (Dewi Juli)    → teacher 131
 *   pegawai 23 (Yarniwati)    → teacher 132
 *   pegawai 24 (Clara Novita) → teacher 134 (note: pegawai skip, Clara = 0447774675230053)
 *   pegawai 25 (Solidarman)   → teacher 133
 *   pegawai 26 (Erwin)        → teacher 135
 *   pegawai 27 (Sri Rahayu)   → teacher 137
 *   pegawai 28 (Bertha)       → teacher 136
 *   pegawai 29 (Heny Aprilia) → teacher 138
 *   pegawai 30 (Niguents)     → teacher 139
 */
class UpdateSMPWaliKelasKompetensiSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('=== Update Wali Kelas & Kompetensi SMPS Pembda 2 ===');
        
        DB::beginTransaction();
        
        try {
            $this->updateWaliKelas();
            $this->createMissingSubjects();
            $this->updateKompetensi();
            
            DB::commit();
            $this->command->info('✅ Semua data berhasil diupdate!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error: ' . $e->getMessage());
            Log::error('UpdateSMPWaliKelasKompetensi failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Update homeroom_teacher_id pada classrooms
     * 
     * Sumber: sisfopembda.penugasan_jabatan (jabatan_id=11 = "Wali Kelas SMP")
     * 10 guru memegang jabatan Wali Kelas SMP:
     *   - Yonata (124), Dedi (125), Kristiani (127), Beatus (128),
     *   - Eliaman (129), Yarniwati (132), Solidarman (133), Clara Novita (134),
     *   - Bertha (136), Heny Aprilia (138)
     * 
     * Penempatan berdasarkan pola penugasan mengajar dari survey_db:
     *   - IX: Guru senior (Wakasek + PKS) → Yonata, Dedi, Yarniwati
     *   - VIII: Kristiani (MTK), Solidarman (PJOK), Heny (B.Indo)
     *   - VII: Beatus (existing), Clara Novita, Bertha, Eliaman
     */
    private function updateWaliKelas(): void
    {
        $this->command->info('');
        $this->command->info('📋 Updating Wali Kelas...');
        
        // Mapping: classroom_id => teacher_id
        // Based on sisfopembda jabatan "Wali Kelas SMP" + survey_db teaching patterns
        $waliKelasAssignments = [
            217 => 128,  // VII-Alessandro Volta  → BEATUS NDRURU, S.PD (sudah ada)
            218 => 134,  // VII-Archimedes         → CLARA NOVITA SABRINA, S.PD
            219 => 136,  // VII-Blaise Pascal      → BERTHA TELAUMBANUA, S.PD
            198 => 129,  // VII-Gregor Mendel      → ELIAMAN ZAI, S.PD
            199 => 138,  // VIII-A.G. Bell         → HENY APRILIA TELAUMBANUA, S.PD
            200 => 127,  // VIII-Isaac Newton      → DRA. KRISTIANI ZEBUA
            201 => 133,  // VIII-Thomas Alva Edison → SOLIDARMAN JAYA MENDROFA, S.PD
            203 => 124,  // IX-Albert Einstein     → YONATA TELAUMBANUA, S.PD (Wakasek)
            204 => 125,  // IX-Aristoteles         → DEDI PUTRA TELAUMBANUA, S.PD (PKS)
            205 => 132,  // IX-Pythagoras          → YARNIWATI SARUMAHA, S.PD.K
        ];
        
        $updated = 0;
        $skipped = 0;
        
        foreach ($waliKelasAssignments as $classroomId => $teacherId) {
            $classroom = DB::table('classrooms')->where('id', $classroomId)->first();
            $teacher = DB::table('teachers')->where('id', $teacherId)->first();
            
            if (!$classroom || !$teacher) {
                $this->command->warn("  ⚠ Classroom #{$classroomId} atau Teacher #{$teacherId} tidak ditemukan, skip.");
                $skipped++;
                continue;
            }
            
            // Check if already assigned correctly
            if ($classroom->homeroom_teacher_id == $teacherId) {
                $this->command->line("  ✓ {$classroom->class_name} → {$teacher->full_name} (sudah ada)");
                $skipped++;
                continue;
            }
            
            DB::table('classrooms')
                ->where('id', $classroomId)
                ->update(['homeroom_teacher_id' => $teacherId]);
            
            $this->command->info("  ✅ {$classroom->class_name} → {$teacher->full_name}");
            $updated++;
        }
        
        $this->command->info("  → Updated: {$updated}, Skipped: {$skipped}");
    }
    
    /**
     * Buat subject "Pend. Agama Islam" untuk school_id=1 jika belum ada
     * Sumber: survey_db.mata_pelajaran id=9 "Pend. Agama Islam"
     */
    private function createMissingSubjects(): void
    {
        $this->command->info('');
        $this->command->info('📋 Checking missing subjects...');
        
        // Check if "Pend. Agama Islam" exists for school_id=1
        $paiExists = DB::table('subjects')
            ->where('school_id', 1)
            ->where(function ($q) {
                $q->where('subject_name', 'like', '%Agama Islam%')
                  ->orWhere('subject_code', 'PAI');
            })
            ->first();
        
        if (!$paiExists) {
            DB::table('subjects')->insert([
                'school_id' => 1,
                'subject_code' => 'PAI',
                'subject_name' => 'Pend. Agama Islam',
                'kkm' => 75,
                'description' => 'Mata pelajaran yang mempelajari nilai-nilai dan ajaran agama Islam untuk membentuk karakter religius, moral, dan spiritual siswa dalam kehidupan sehari-hari.',
                'is_active' => 1,
                'hours_per_week' => 2,
            ]);
            $this->command->info("  ✅ Subject 'Pend. Agama Islam' berhasil dibuat untuk SMPS Pembda 2");
        } else {
            $this->command->line("  ✓ Subject 'Pend. Agama Islam' sudah ada (ID: {$paiExists->id})");
        }
    }
    
    /**
     * Update subject_teacher (kompetensi guru)
     * 
     * Missing berdasarkan survey_db.penugasan:
     * 1. Clara Novita (134) → Prakarya (146), B.Indonesia (2)
     * 2. Sri Rahayu (137)   → Pend. Agama Islam (baru dibuat)
     */
    private function updateKompetensi(): void
    {
        $this->command->info('');
        $this->command->info('📋 Updating Kompetensi (subject_teacher)...');
        
        $now = now();
        $added = 0;
        $skipped = 0;
        
        // Define missing competencies
        $missingKompetensi = [
            // Clara Novita Sabrina (teacher_id=134): teaches IPS, Prakarya, B.Indonesia
            // Currently only has: 5 (IPS)
            // Missing: 146 (Prakarya), 2 (B.Indonesia)
            ['teacher_id' => 134, 'subject_id' => 146, 'label' => 'CLARA NOVITA SABRINA → Prakarya'],
            ['teacher_id' => 134, 'subject_id' => 2,   'label' => 'CLARA NOVITA SABRINA → Bahasa Indonesia'],
            
            // Sri Rahayu Tanjung (teacher_id=137): teaches Pend. Agama Islam
            // Currently only has: 227 (B.Daerah) 
            // Missing: Pend. Agama Islam (need to lookup ID)
            ['teacher_id' => 137, 'subject_id' => 'PAI', 'label' => 'SRI RAHAYU TANJUNG → Pend. Agama Islam'],
        ];
        
        foreach ($missingKompetensi as $item) {
            $subjectId = $item['subject_id'];
            
            // Resolve PAI subject ID if needed
            if ($subjectId === 'PAI') {
                $paiSubject = DB::table('subjects')
                    ->where('school_id', 1)
                    ->where(function ($q) {
                        $q->where('subject_name', 'like', '%Agama Islam%')
                          ->orWhere('subject_code', 'PAI');
                    })
                    ->first();
                
                if (!$paiSubject) {
                    $this->command->warn("  ⚠ Subject PAI tidak ditemukan, skip: {$item['label']}");
                    $skipped++;
                    continue;
                }
                $subjectId = $paiSubject->id;
            }
            
            // Check if already exists
            $exists = DB::table('subject_teacher')
                ->where('teacher_id', $item['teacher_id'])
                ->where('subject_id', $subjectId)
                ->exists();
            
            if ($exists) {
                $this->command->line("  ✓ {$item['label']} (sudah ada)");
                $skipped++;
                continue;
            }
            
            DB::table('subject_teacher')->insert([
                'teacher_id' => $item['teacher_id'],
                'subject_id' => $subjectId,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            
            $this->command->info("  ✅ {$item['label']}");
            $added++;
        }
        
        $this->command->info("  → Added: {$added}, Skipped: {$skipped}");
    }
}
