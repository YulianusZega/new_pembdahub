<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Position;
use App\Models\TimeSlot;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\TeachingAssignment;

class ImportSmkTeachersCommand extends Command
{
    protected $signature = 'import:smk-teachers {--dry-run} {--fresh}';
    protected $description = 'Import SMK teachers, subjects, positions, and time slots from CSV';

    private $school_id = 3; // SMK Pembda Nias
    private $academic_year_id = 1; // TP. 2025/2026

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $fresh = $this->option('fresh');

        $this->info("\n=== IMPORT DATA GURU & PEMBAGIAN TUGAS SMK PEMBDA NIAS ===");
        
        // Verify school and academic year
        $school = School::find($this->school_id);
        $academicYear = AcademicYear::find($this->academic_year_id);
        
        if (!$school || !$academicYear) {
            $this->error('School or Academic Year not found!');
            return 1;
        }
        
        $this->info("✓ Sekolah: {$school->name} (ID: {$this->school_id})");
        $this->info("✓ Tahun Ajaran: {$academicYear->year} (ID: {$this->academic_year_id})\n");

        if ($fresh && !$dryRun) {
            if (!$this->confirm('⚠️  Ini akan menghapus semua data guru SMK yang ada. Lanjutkan?', false)) {
                $this->info('Import dibatalkan.');
                return 0;
            }
            $this->deleteExistingData();
        }

        DB::beginTransaction();
        
        try {
            // Step 1: Import Subjects
            $this->importSubjects($dryRun);
            
            // Step 2: Import Teachers
            $this->importTeachers($dryRun);
            
            // Step 3: Import Positions & Assignments
            $this->importPositions($dryRun);
            
            // Step 4: Import Time Slots
            $this->importTimeSlots($dryRun);
            
            // Step 5: Import Teaching Assignments
            $this->importTeachingAssignments($dryRun);
            
            // Step 6: Populate Subject Competencies
            $this->populateSubjectCompetencies($dryRun);
            
            if ($dryRun) {
                DB::rollBack();
                $this->info("\n✓ DRY RUN selesai - tidak ada data yang disimpan");
            } else {
                DB::commit();
                $this->info("\n✅ IMPORT SELESAI!");
            }
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("\n❌ ERROR: " . $e->getMessage());
            $this->error("File: " . $e->getFile() . " Line: " . $e->getLine());
            return 1;
        }
    }

    private function deleteExistingData()
    {
        $this->info("🗑️  Menghapus data SMK yang ada...");
        
        // Delete subject_teacher (teacher competencies)
        try {
            DB::table('subject_teacher')
                ->whereIn('teacher_id', function($q) {
                    $q->select('id')->from('teachers')->where('school_id', $this->school_id);
                })
                ->delete();
        } catch (\Exception $e) {
            // Table doesn't exist yet, skip
        }
        
        // Delete teaching assignments (skip if table doesn't exist)
        try {
            DB::table('teaching_assignments')
                ->whereIn('teacher_id', function($q) {
                    $q->select('id')->from('teachers')->where('school_id', $this->school_id);
                })
                ->delete();
        } catch (\Exception $e) {
            // Table doesn't exist yet, skip
        }
        
        // Delete employee positions
        DB::table('employee_positions')
            ->whereIn('employee_id', function($q) {
                $q->select('id')->from('employees')->where('school_id', $this->school_id);
            })
            ->delete();
        
        // Delete teachers (cascade will handle employees)
        DB::table('teachers')->where('school_id', $this->school_id)->delete();
        
        // Delete employees
        DB::table('employees')->where('school_id', $this->school_id)->delete();
        
        // Delete users (guru role only for this school)
        DB::table('users')
            ->where('school_id', $this->school_id)
            ->where('role', 'guru')
            ->delete();
        
        // Delete time slots
        DB::table('time_slots')->where('school_id', $this->school_id)->delete();
        
        $this->info("✓ Data lama dihapus\n");
    }

    private function importSubjects($dryRun)
    {
        $this->info("📚 Step 1: Import Mata Pelajaran...");
        
        $csvFile = base_path('mapel_smk.csv');
        if (!file_exists($csvFile)) {
            $this->warn("  ⚠️  File mapel_smk.csv tidak ditemukan, skip");
            return;
        }

        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($csvFile));
        
        array_shift($data); // Remove header
        
        $imported = 0;
        $skipped = 0;

        foreach ($data as $row) {
            if (count($row) < 3) continue;
            
            [$code, $name, $category] = $row;
            
            // Check if exists
            $existing = Subject::where('school_id', $this->school_id)
                ->where('subject_code', $code)
                ->first();
            
            if ($existing) {
                $this->line("  ⏭️  {$name} → sudah ada");
                $skipped++;
                continue;
            }
            
            if (!$dryRun) {
                Subject::create([
                    'school_id' => $this->school_id,
                    'subject_code' => $code,
                    'subject_name' => $name,
                    'category' => $category,
                    'is_active' => true,
                ]);
            }
            
            $this->line("  ✓ {$name} → imported");
            $imported++;
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped\n");
    }

    private function importTeachers($dryRun)
    {
        $this->info("👨‍🏫 Step 2: Import Guru...");
        
        $csvFile = base_path('guru_smk.csv');
        if (!file_exists($csvFile)) {
            $this->error("  ❌ File guru_smk.csv tidak ditemukan!");
            throw new \Exception("guru_smk.csv not found");
        }

        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($csvFile));
        
        array_shift($data); // Remove header
        
        $imported = 0;
        $skipped = 0;

        foreach ($data as $row) {
            if (count($row) < 7) continue;
            
            [$no, $nama, $nuptk, $gender, $golongan, $jenis_guru, $total_jp] = $row;
            
            // Use NUPTK as teacher code if available, otherwise generate code
            $teacherCode = ($nuptk !== '0' && !empty($nuptk)) 
                ? $nuptk 
                : 'SMK-' . str_pad($no, 3, '0', STR_PAD_LEFT);
            
            // Check if teacher already exists
            $existing = Teacher::where('school_id', $this->school_id)
                ->where('teacher_code', $teacherCode)
                ->first();
            
            if ($existing) {
                $this->line("  ⏭️  {$nama} → sudah ada");
                $skipped++;
                continue;
            }
            
            if (!$dryRun) {
                // Generate username and email
                $username = strtolower(str_replace([' ', ',', '.'], '', explode(',', $nama)[0]));
                $email = $username . '@smkpembda.sch.id';
                
                // Unique check for username
                $counter = 1;
                $originalUsername = $username;
                while (User::where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }
                
                // Create user first
                $user = User::create([
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'role' => 'guru',
                    'school_id' => $this->school_id,
                    'is_active' => true,
                ]);
                
                // Create employee first (karena teachers membutuhkan employee_id)
                $employee = Employee::create([
                    'school_id' => $this->school_id,
                    'user_id' => $user->id,
                    'employee_code' => $teacherCode, // Use same code as teacher
                    'full_name' => $nama,
                    'gender' => $gender,
                    'employee_type' => $jenis_guru === 'PTY' || $jenis_guru === 'PTT' ? 'staff_tu' : 'guru',
                    'employment_status' => $jenis_guru === 'PNS' ? 'pns' : ($jenis_guru === 'GTY' || $jenis_guru === 'PTY' ? 'yayasan' : 'kontrak'),
                    'tmt_date' => '2025-07-01',
                    'is_active' => true,
                ]);
                
                // Then create teacher (dengan employee_id)
                $teacher = Teacher::create([
                    'employee_id' => $employee->id,
                    'user_id' => $user->id,
                    'school_id' => $this->school_id,
                    'teacher_code' => $teacherCode,
                    'full_name' => $nama,
                    'gender' => $gender,
                    'phone' => null,
                    'position' => $jenis_guru === 'PTY' || $jenis_guru === 'PTT' ? 'Staff' : 'Guru',
                    'is_active' => true,
                ]);
            }
            
            $this->line("  ✓ {$nama} → imported");
            $imported++;
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped\n");
    }

    private function importPositions($dryRun)
    {
        $this->info("💼 Step 3: Import Jabatan Struktural...");
        
        $csvFile = base_path('jabatan_smk.csv');
        if (!file_exists($csvFile)) {
            $this->warn("  ⚠️  File jabatan_smk.csv tidak ditemukan, skip");
            return;
        }

        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($csvFile));
        
        array_shift($data); // Remove header
        
        $imported = 0;
        $skipped = 0;

        foreach ($data as $row) {
            if (count($row) < 3) continue;
            
            [$nama_guru, $jabatan, $kode] = $row;
            
            // Normalize nama guru for better matching
            // Remove gelar akademik dan ambil nama inti
            $nama_parts = explode(',', $nama_guru);
            $nama_inti = trim($nama_parts[0]);
            
            // Try exact match first
            $teacher = Teacher::where('school_id', $this->school_id)
                ->where('full_name', 'LIKE', '%' . $nama_inti . '%')
                ->first();
            
            if (!$teacher) {
                $this->line("  ⚠️  {$nama_guru} → guru tidak ditemukan");
                $skipped++;
                continue;
            }
            
            // Find or create position
            if (!$dryRun) {
                $position = Position::firstOrCreate(
                    [
                        'school_id' => $this->school_id,
                        'position_code' => $kode,
                    ],
                    [
                        'position_name' => $jabatan,
                        'level' => $this->getPositionLevel($jabatan),
                    ]
                );
                
                $employee = $teacher->employee;
                if (!$employee) {
                    $this->line("  ⚠️  {$nama_guru} → employee tidak ada");
                    $skipped++;
                    continue;
                }
                
                // Check if already assigned
                $existing = DB::table('employee_positions')
                    ->where('employee_id', $employee->id)
                    ->where('position_id', $position->id)
                    ->whereNull('end_date')
                    ->first();
                
                if ($existing) {
                    $this->line("  ⏭️  {$nama_guru} - {$jabatan} → sudah ada");
                    $skipped++;
                    continue;
                }
                
                DB::table('employee_positions')->insert([
                    'employee_id' => $employee->id,
                    'position_id' => $position->id,
                    'start_date' => '2025-07-01',
                    'end_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            $this->line("  ✓ {$nama_guru} - {$jabatan}");
            $imported++;
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped\n");
    }

    private function importTimeSlots($dryRun)
    {
        $this->info("⏰ Step 4: Import Time Slots...");
        
        $csvFile = base_path('time_slots_smk.csv');
        if (!file_exists($csvFile)) {
            $this->warn("  ⚠️  File time_slots_smk.csv tidak ditemukan, skip");
            return;
        }

        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($csvFile));
        
        array_shift($data); // Remove header
        
        $imported = 0;
        $skipped = 0;

        foreach ($data as $row) {
            if (count($row) < 5) continue;
            
            [$day, $slot, $start_time, $end_time, $duration] = $row;
            
            // Determine slot type and name
            $slot_type = 'lesson';
            $slot_name = "Pelajaran {$slot}";
            $is_teaching_slot = true;
            
            if (strpos($start_time, '10:07') !== false) {
                $slot_type = 'break';
                $slot_name = 'Istirahat 1';
                $is_teaching_slot = false;
            } elseif (strpos($start_time, '12:36') !== false) {
                $slot_type = 'break';
                $slot_name = 'Istirahat 2';
                $is_teaching_slot = false;
            }
            
            // Check if exists
            $existing = TimeSlot::where('school_id', $this->school_id)
                ->where('day_of_week', $this->mapDayToString($day))
                ->where('slot_order', $slot)
                ->first();
            
            if ($existing) {
                $skipped++;
                continue;
            }
            
            if (!$dryRun) {
                TimeSlot::create([
                    'school_id' => $this->school_id,
                    'day_of_week' => $this->mapDayToString($day),
                    'slot_name' => $slot_name,
                    'slot_type' => $slot_type,
                    'slot_order' => $slot,
                    'start_time' => $start_time,
                    'end_time' => $end_time,
                    'duration_minutes' => $duration,
                    'is_teaching_slot' => $is_teaching_slot,
                    'is_active' => true,
                ]);
            }
            
            $imported++;
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped\n");
    }

    private function importTeachingAssignments($dryRun)
    {
        $this->info("📖 Step 5: Import Tugas Mengajar...");
        
        $csvFile = base_path('teaching_assignments_smk.csv');
        if (!file_exists($csvFile)) {
            $this->warn("  ⚠️  File teaching_assignments_smk.csv tidak ditemukan, skip");
            return;
        }

        $data = array_map(function($line) {
            return str_getcsv($line, ';');
        }, file($csvFile));
        
        array_shift($data); // Remove header
        
        $imported = 0;
        $skipped = 0;

        foreach ($data as $row) {
            if (count($row) < 4) continue;
            
            [$nama_guru, $nama_mapel, $nama_kelas, $jp] = $row;
            
            // Find teacher
            $teacher = Teacher::where('school_id', $this->school_id)
                ->where('full_name', 'LIKE', '%' . explode(',', $nama_guru)[0] . '%')
                ->first();
            
            if (!$teacher) {
                $this->line("  ⚠️  Guru: {$nama_guru} → tidak ditemukan");
                $skipped++;
                continue;
            }
            
            // Find subject
            $subject = Subject::where('school_id', $this->school_id)
                ->where('subject_name', 'LIKE', '%' . $nama_mapel . '%')
                ->first();
            
            if (!$subject) {
                $this->line("  ⚠️  Mapel: {$nama_mapel} → tidak ditemukan");
                $skipped++;
                continue;
            }
            
            // Find classroom - match format like "X TKR1" atau "XI TKR2"
            // Normalize nama kelas: hapus spasi, uppercase
            $kelasNormalized = strtoupper(str_replace(' ', '', $nama_kelas));
            
            $classroom = Classroom::where('school_id', $this->school_id)
                ->where(function($q) use ($kelasNormalized, $nama_kelas) {
                    // Try exact match first
                    $q->whereRaw('UPPER(REPLACE(class_name, " ", "")) LIKE ?', ['%' . $kelasNormalized . '%'])
                      // Or match by components (tingkat + jurusan)
                      ->orWhere(function($q2) use ($nama_kelas) {
                          // Extract level (X, XI, XII) and major
                          if (preg_match('/^(X{1,3})\s+(.+)$/i', $nama_kelas, $matches)) {
                              $level = $matches[1];
                              $major = $matches[2];
                              $q2->whereRaw('class_name LIKE ?', [$level . ' %' . $major . '%']);
                          }
                      });
                })
                ->first();
            
            if (!$classroom) {
                $this->line("  ⚠️  Kelas: {$nama_kelas} → tidak ditemukan");
                $skipped++;
                continue;
            }
            
            // Check if already exists
            $existing = TeachingAssignment::where('teacher_id', $teacher->id)
                ->where('subject_id', $subject->id)
                ->where('classroom_id', $classroom->id)
                ->where('academic_year_id', $this->academic_year_id)
                ->first();
            
            if ($existing) {
                $skipped++;
                continue;
            }
            
            if (!$dryRun) {
                TeachingAssignment::create([
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'classroom_id' => $classroom->id,
                    'academic_year_id' => $this->academic_year_id,
                    'hours_per_week' => $jp,
                    'is_main_teacher' => true,
                    'is_active' => true,
                ]);
            }
            
            $this->line("  ✓ {$teacher->full_name} → {$subject->subject_name} → {$classroom->class_name}");
            $imported++;
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped\n");
    }

    private function getPositionLevel($jabatan)
    {
        if (str_contains($jabatan, 'Kepala Sekolah')) return 1;
        if (str_contains($jabatan, 'Wakil')) return 2;
        if (str_contains($jabatan, 'Pembantu Kepala') || str_contains($jabatan, 'PKS')) return 3;
        if (str_contains($jabatan, 'Kapro') || str_contains($jabatan, 'Koordinator')) return 4;
        if (str_contains($jabatan, 'Kepala Lab') || str_contains($jabatan, 'Kepala Bengkel')) return 5;
        if (str_contains($jabatan, 'Seksi')) return 6;
        return 7;
    }

    private function mapDayToString($day)
    {
        $days = [
            'Senin' => 'monday',
            'Selasa' => 'tuesday',
            'Rabu' => 'wednesday',
            'Kamis' => 'thursday',
            'Jumat' => 'friday',
            'Sabtu' => 'saturday',
            'Minggu' => 'sunday',
        ];
        return $days[$day] ?? 'monday';
    }
    
    private function populateSubjectCompetencies($dryRun = false)
    {
        $this->info("\n📖 Step 6: Populate Kompetensi Mata Pelajaran...");
        
        // Get distinct teacher-subject pairs from teaching_assignments
        $data = DB::table('teaching_assignments')
            ->join('teachers', 'teaching_assignments.teacher_id', '=', 'teachers.id')
            ->where('teachers.school_id', $this->school_id)
            ->select('teaching_assignments.teacher_id', 'teaching_assignments.subject_id')
            ->distinct()
            ->get();
        
        $inserted = 0;
        $skipped = 0;
        
        foreach ($data as $item) {
            $exists = DB::table('subject_teacher')
                ->where('teacher_id', $item->teacher_id)
                ->where('subject_id', $item->subject_id)
                ->exists();
            
            if (!$exists) {
                if (!$dryRun) {
                    DB::table('subject_teacher')->insert([
                        'teacher_id' => $item->teacher_id,
                        'subject_id' => $item->subject_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $inserted++;
            } else {
                $skipped++;
            }
        }
        
        $this->info("  📊 Total: {$inserted} imported, {$skipped} skipped");
    }
}
