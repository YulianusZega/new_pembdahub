<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\Teacher;
use App\Models\Employee;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\AcademicYear;
use Carbon\Carbon;

class ImportSmpData extends Command
{
    protected $signature = 'import:smp-data {--dry-run : Run without actually importing} {--fresh : Delete existing data first}';
    protected $description = 'Import data SMP dari Data SMP.sql';

    private $dryRun = false;
    private $fresh = false;
    private $school;
    private $academicYear;
    private $sqlFile;
    
    // Mapping ID lama -> ID baru
    private $subjectMap = [];
    private $teacherMap = [];
    private $classroomMap = [];
    private $studentMap = [];
    
    // Data dari SQL
    private $sqlData = [
        'subjects' => [],
        'teachers' => [],
        'classrooms' => [],
        'students' => [],
        'assignments' => []
    ];
    
    public function handle()
    {
        $this->dryRun = $this->option('dry-run');
        $this->fresh = $this->option('fresh');
        $this->sqlFile = base_path('Data SMP.sql');
        
        if (!file_exists($this->sqlFile)) {
            $this->error("File Data SMP.sql tidak ditemukan di: {$this->sqlFile}");
            return 1;
        }
        
        if ($this->dryRun) {
            $this->warn('🔍 DRY RUN MODE - Tidak ada data yang akan diimport');
        }
        
        if ($this->fresh && !$this->dryRun) {
            if (!$this->confirm('⚠️  Ini akan menghapus semua data SMP yang ada. Lanjutkan?', false)) {
                $this->info('Import dibatalkan.');
                return 0;
            }
        }
        
        $this->info('=== IMPORT DATA SMP PEMBDA 2 GUNUNGSITOLI ===');
        $this->newLine();
        
        // Validasi school dan academic year
        if (!$this->validateRequirements()) {
            return 1;
        }
        
        // Parse SQL file
        $this->info('📄 Parsing Data SMP.sql...');
        if (!$this->parseSqlFile()) {
            return 1;
        }
        
        DB::beginTransaction();
        
        try {
            if ($this->fresh && !$this->dryRun) {
                $this->cleanExistingData();
            }
            
            // Step 1: Import Mata Pelajaran
            $this->info('📚 Step 1: Import Mata Pelajaran...');
            $this->importSubjects();
            $this->newLine();
            
            // Step 2: Import Guru
            $this->info('👨‍🏫 Step 2: Import Guru...');
            $this->importTeachers();
            $this->newLine();
            
            // Step 3: Import Kelas
            $this->info('🏫 Step 3: Import Kelas...');
            $this->importClassrooms();
            $this->newLine();
            
            // Step 4: Import Siswa
            $this->info('🎓 Step 4: Import Siswa...');
            $this->importStudents();
            $this->newLine();
            
            if ($this->dryRun) {
                DB::rollBack();
                $this->warn('✅ DRY RUN selesai - Rollback semua perubahan');
            } else {
                DB::commit();
                $this->info('✅ IMPORT SELESAI!');
            }
            
            $this->showSummary();
            
            return 0;
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
    
    private function validateRequirements()
    {
        // Cek sekolah SMP
        $this->school = School::where('name', 'SMPS Pembda 2 Gunungsitoli')->first();
        if (!$this->school) {
            $this->error('❌ Sekolah "SMPS Pembda 2 Gunungsitoli" tidak ditemukan!');
            return false;
        }
        $this->info('✓ Sekolah: ' . $this->school->name . ' (ID: ' . $this->school->id . ')');
        
        // Cek tahun ajaran
        $this->academicYear = AcademicYear::where('year', '2025/2026')->first();
        if (!$this->academicYear) {
            $this->error('❌ Tahun Ajaran 2025/2026 tidak ditemukan!');
            $this->info('Membuat tahun ajaran baru...');
            
            if (!$this->dryRun) {
                $this->academicYear = AcademicYear::create([
                    'year' => '2025/2026',
                    'start_date' => '2025-07-14',
                    'end_date' => '2026-06-30',
                    'is_active' => 1,
                ]);
            }
        }
        if ($this->academicYear) {
            $this->info('✓ Tahun Ajaran: ' . $this->academicYear->year . ' (ID: ' . $this->academicYear->id . ')');
        }
        
        $this->newLine();
        return true;
    }
    
    private function importSubjects()
    {
        $subjects = [
            ['code' => 'PAK', 'name' => 'Pend. Agama Katolik', 'old_id' => 1],
            ['code' => 'BIND', 'name' => 'Bahasa Indonesia', 'old_id' => 2],
            ['code' => 'BING', 'name' => 'Bahasa Inggris', 'old_id' => 3],
            ['code' => 'IPA', 'name' => 'Ilmu Pengetahuan Alam', 'old_id' => 4],
            ['code' => 'IPS', 'name' => 'Ilmu Pengetahuan Sosial', 'old_id' => 5],
            ['code' => 'MTK', 'name' => 'Matematika', 'old_id' => 6],
            ['code' => 'PJOK', 'name' => 'PJOK', 'old_id' => 7],
            ['code' => 'PKN', 'name' => 'PKn', 'old_id' => 8],
            ['code' => 'PAI', 'name' => 'Pend. Agama Islam', 'old_id' => 9],
            ['code' => 'PAK2', 'name' => 'Pend. Agama Kristen', 'old_id' => 10],
            ['code' => 'TIK', 'name' => 'TIK', 'old_id' => 11],
            ['code' => 'SBD', 'name' => 'Senibudaya', 'old_id' => 13],
            ['code' => 'BK', 'name' => 'Bimbingan Konseling', 'old_id' => 14],
            ['code' => 'MULOK', 'name' => 'Mulok', 'old_id' => 15],
            ['code' => 'PKY', 'name' => 'Prakarya', 'old_id' => 16],
        ];
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($subjects as $sub) {
            $existing = Subject::where('subject_code', $sub['code'])->first();
            
            if ($existing) {
                $this->subjectMap[$sub['old_id']] = $existing->id;
                $skipped++;
                $this->line("  ⏭️  {$sub['name']} → sudah ada (ID: {$existing->id})");
            } else {
                if (!$this->dryRun) {
                    $subject = Subject::create([
                        'school_id' => $this->school->id,
                        'subject_code' => $sub['code'],
                        'subject_name' => $sub['name'],
                        'is_active' => 1,
                    ]);
                    $this->subjectMap[$sub['old_id']] = $subject->id;
                    $this->info("  ✓ {$sub['name']} → imported (ID: {$subject->id})");
                } else {
                    $this->info("  ✓ {$sub['name']} → akan diimport");
                }
                $imported++;
            }
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped");
    }
    
    private function importTeachers()
    {
        $teachers = $this->getTeachersData();
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($teachers as $t) {
            $existing = Teacher::where('teacher_code', $t['nip'])->first();
            
            if ($existing) {
                $this->teacherMap[$t['old_id']] = $existing->id;
                $skipped++;
                $this->line("  ⏭️  {$t['nama_lengkap']} → sudah ada");
            } else {
                if (!$this->dryRun) {
                    // Create employee first
                    $employee = Employee::create([
                        'school_id' => $this->school->id,
                        'employee_code' => $t['nip'],
                        'full_name' => $t['nama_lengkap'],
                        'gender' => 'L', // default, bisa diupdate manual
                        'employee_type' => 'guru',
                        'employment_status' => 'yayasan',
                        'tmt_date' => now()->format('Y-m-d'),
                        'is_active' => 1,
                    ]);
                    
                    // Create teacher
                    $teacher = Teacher::create([
                        'employee_id' => $employee->id,
                        'school_id' => $this->school->id,
                        'teacher_code' => $t['nip'],
                        'full_name' => $t['nama_lengkap'],
                        'gender' => 'L',
                        'is_active' => 1,
                    ]);
                    
                    $this->teacherMap[$t['old_id']] = $teacher->id;
                    $this->info("  ✓ {$t['nama_lengkap']} → imported (ID: {$teacher->id})");
                } else {
                    $this->info("  ✓ {$t['nama_lengkap']} → akan diimport");
                }
                $imported++;
            }
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped");
    }
    
    private function importClassrooms()
    {
        $classrooms = $this->getClassroomsData();
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($classrooms as $c) {
            $existing = Classroom::where('school_id', $this->school->id)
                ->where('class_name', $c['nama_kelas'])
                ->first();
            
            if ($existing) {
                $this->classroomMap[$c['old_id']] = $existing->id;
                $skipped++;
                $this->line("  ⏭️  {$c['nama_kelas']} → sudah ada");
            } else {
                if (!$this->dryRun) {
                    // Generate class_code otomatis
                    $classCode = 'CLS-' . $c['tingkat'] . '-' . sprintf('%02d', $imported + 1);
                    
                    $classroom = Classroom::create([
                        'school_id' => $this->school->id,
                        'class_code' => $classCode,
                        'class_name' => $c['nama_kelas'],
                        'class_type' => 'Reguler',
                        'grade_level' => $c['tingkat'],
                        'academic_year_id' => $this->academicYear->id,
                        'capacity' => 35,
                        'is_active' => 1,
                    ]);
                    
                    $this->classroomMap[$c['old_id']] = $classroom->id;
                    $this->info("  ✓ {$c['nama_kelas']} (Tingkat {$c['tingkat']}) → imported (ID: {$classroom->id})");
                } else {
                    $this->info("  ✓ {$c['nama_kelas']} → akan diimport");
                }
                $imported++;
            }
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped");
    }
    
    private function importStudents()
    {
        $students = $this->getStudentsData();
        
        $imported = 0;
        $skipped = 0;
        $assigned = 0;
        
        $bar = $this->output->createProgressBar(count($students));
        $bar->start();
        
        foreach ($students as $s) {
            $existing = Student::where('nis', $s['nis'])->first();
            
            if ($existing) {
                $skipped++;
            } else {
                if (!$this->dryRun) {
                    // Generate NISN dummy dari NIS
                    $nisn = '00' . $s['nis'];
                    
                    $student = Student::create([
                        'school_id' => $this->school->id,
                        'nisn' => (string)$nisn,
                        'nis' => (string)$s['nis'],
                        'full_name' => $s['nama_lengkap'],
                        'gender' => 'L', // default
                        'entry_year' => 2024, // default
                    ]);
                    
                    // Assign ke kelas jika ada mapping
                    if (isset($this->classroomMap[$s['kelas_id']])) {
                        DB::table('student_classes')->insert([
                            'student_id' => $student->id,
                            'classroom_id' => $this->classroomMap[$s['kelas_id']],
                            'academic_year_id' => $this->academicYear->id,
                        ]);
                        $assigned++;
                    }
                    
                    $imported++;
                }
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped, {$assigned} assigned ke kelas");
    }
    
    private function showSummary()
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info('           SUMMARY IMPORT');
        $this->info('═══════════════════════════════════════');
        
        if (!$this->dryRun) {
            $totalSubjects = Subject::count();
            $totalTeachers = Teacher::where('school_id', $this->school->id)->count();
            $totalClassrooms = Classroom::where('school_id', $this->school->id)->count();
            $totalStudents = Student::where('school_id', $this->school->id)->count();
            
            $this->table(
                ['Item', 'Jumlah'],
                [
                    ['Mata Pelajaran', $totalSubjects],
                    ['Guru (SMP Pembda 2)', $totalTeachers],
                    ['Kelas (SMP Pembda 2)', $totalClassrooms],
                    ['Siswa (SMP Pembda 2)', $totalStudents],
                ]
            );
        }
    }
    
    // Data sources
    private function getTeachersData()
    {
        return [
            ['old_id' => 1, 'nip' => '1451768669130080', 'nama_lengkap' => 'YONATA TELAUMBANUA, S.PD'],
            ['old_id' => 2, 'nip' => '0056769670130083', 'nama_lengkap' => 'DEDI PUTRA TELAUMBANUA, S.PD'],
            ['old_id' => 3, 'nip' => '1534746650300020', 'nama_lengkap' => 'MARSELINA MASARIA NDRURU, S.AG'],
            ['old_id' => 4, 'nip' => '0549745647300052', 'nama_lengkap' => 'DRA. KRISTIANI ZEBUA'],
            ['old_id' => 5, 'nip' => '1834757663200000', 'nama_lengkap' => 'BEATUS NDRURU, S.PD'],
            ['old_id' => 6, 'nip' => '7247764666130150', 'nama_lengkap' => 'ELIAMAN ZAI, S.PD'],
            ['old_id' => 7, 'nip' => 'GTK001', 'nama_lengkap' => 'NURIATI ZEGA, SH'],
            ['old_id' => 8, 'nip' => '4042772673130070', 'nama_lengkap' => 'DEWI JULI SULASTRI ZEGA, S.E'],
            ['old_id' => 9, 'nip' => '2447764665230240', 'nama_lengkap' => 'YARNIWATI SARUMAHA, S.PD.K'],
            ['old_id' => 10, 'nip' => '7647767668130260', 'nama_lengkap' => 'SOLIDARMAN JAYA MENDROFA, S.PD'],
            ['old_id' => 11, 'nip' => '0447774675230053', 'nama_lengkap' => 'CLARA NOVITA SABRINA, S.PD'],
            ['old_id' => 12, 'nip' => 'GTK002', 'nama_lengkap' => 'ERWIN JHOSEP CLARK ZEBUA, A.MD.T'],
            ['old_id' => 13, 'nip' => '9735775676230130', 'nama_lengkap' => 'BERTHA TELAUMBANUA, S.PD'],
            ['old_id' => 14, 'nip' => '2734777678230050', 'nama_lengkap' => 'SRI RAHAYU TANJUNG, S.PD'],
            ['old_id' => 15, 'nip' => '5762777678230050', 'nama_lengkap' => 'HENY APRILIA TELAUMBANUA, S.PD'],
            ['old_id' => 16, 'nip' => 'GTK003', 'nama_lengkap' => 'NIGUENTS FALDES HULU, S.PD'],
        ];
    }
    
    private function getClassroomsData()
    {
        return [
            ['old_id' => 4, 'nama_kelas' => 'VII-Alessandro Volta', 'tingkat' => 7],
            ['old_id' => 5, 'nama_kelas' => 'VII-Archimedes', 'tingkat' => 7],
            ['old_id' => 6, 'nama_kelas' => 'VII-Blaise Pascal', 'tingkat' => 7],
            ['old_id' => 7, 'nama_kelas' => 'VII-Gregor Mendel', 'tingkat' => 7],
            ['old_id' => 8, 'nama_kelas' => 'VIII-Alexsander Graham Bell', 'tingkat' => 8],
            ['old_id' => 9, 'nama_kelas' => 'VIII-Isaac Newton', 'tingkat' => 8],
            ['old_id' => 10, 'nama_kelas' => 'VIII-Thomas Alva Edison', 'tingkat' => 8],
            ['old_id' => 1, 'nama_kelas' => 'IX-Albert Einstein', 'tingkat' => 9],
            ['old_id' => 2, 'nama_kelas' => 'IX-Aristoteles', 'tingkat' => 9],
            ['old_id' => 3, 'nama_kelas' => 'IX-Pythagoras', 'tingkat' => 9],
        ];
    }
    
    private function parseSqlFile()
    {
        $content = file_get_contents($this->sqlFile);
        
        // Extract students data
        if (preg_match_all("/INSERT INTO `siswa`.*?VALUES\s*(.*?);/s", $content, $matches)) {
            foreach ($matches[1] as $values) {
                // Parse each INSERT statement
                if (preg_match_all("/\((\d+),\s*'([^']+)',\s*'([^']+)',\s*(\d+),\s*(?:(\d+)|NULL),/", $values, $studentMatches, PREG_SET_ORDER)) {
                    foreach ($studentMatches as $sm) {
                        $this->sqlData['students'][] = [
                            'old_id' => $sm[1],
                            'nis' => $sm[2],
                            'nama_lengkap' => $sm[3],
                            'kelas_id' => $sm[4],
                        ];
                    }
                }
            }
        }
        
        $studentCount = count($this->sqlData['students']);
        if ($studentCount == 0) {
            $this->error('Tidak bisa parsing data siswa dari SQL file!');
            return false;
        }
        
        $this->info("✓ Berhasil parsing {$studentCount} siswa dari SQL file");
        return true;
    }
    
    private function cleanExistingData()
    {
        $this->warn('🗑️  Menghapus data SMP yang ada...');
        
        // Delete student_classes for this school
        DB::table('student_classes')
            ->whereIn('student_id', function($query) {
                $query->select('id')
                    ->from('students')
                    ->where('school_id', $this->school->id);
            })
            ->delete();
        
        // Delete students
        Student::where('school_id', $this->school->id)->delete();
        
        // Delete classrooms
        Classroom::where('school_id', $this->school->id)->delete();
        
        $this->info('✓ Data lama dihapus');
    }
    
    private function getStudentsData()
    {
        return $this->sqlData['students'];
    }
}
