<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Support\Facades\DB;

class ImportSmkData extends Command
{
    protected $signature = 'import:smk-data {--dry-run} {--fresh}';
    protected $description = 'Import data siswa SMK dari CSV file';

    private $school;
    private $academicYear;
    private $csvData = [];
    private $classroomMap = [];
    private $majorMap = [];

    public function handle()
    {
        $this->info('=== IMPORT DATA SMK PEMBDA NIAS ===');
        $this->newLine();

        if (!$this->validateRequirements()) {
            return 1;
        }

        if ($this->option('fresh')) {
            if (!$this->confirm('⚠️  Ini akan menghapus semua data SMK yang ada. Lanjutkan?')) {
                $this->info('Import dibatalkan');
                return 0;
            }
        }

        // Parse CSV
        if (!$this->parseCSV()) {
            return 1;
        }

        // Start transaction
        DB::beginTransaction();

        try {
            if ($this->option('fresh')) {
                $this->freshImport();
            }

            // Import steps
            if (!$this->option('dry-run')) {
                $this->importMajors();
                $this->importClassrooms();
                $this->importStudents();
            } else {
                $this->info('📚 DRY RUN - Preview import:');
                $this->previewImport();
            }

            if ($this->option('dry-run')) {
                DB::rollBack();
                $this->newLine();
                $this->info('✅ DRY RUN selesai - Data tidak diimport');
            } else {
                DB::commit();
                $this->newLine();
                $this->info('✅ IMPORT SELESAI!');
                $this->showSummary();
            }

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
        // Check school
        $this->school = School::where('id', 3)->first(); // SMKS Pembda Nias
        
        if (!$this->school) {
            $this->error('❌ Sekolah SMK tidak ditemukan (ID: 3)');
            return false;
        }

        $this->info("✓ Sekolah: {$this->school->school_name} (ID: {$this->school->id})");

        // Check or create academic year 2025/2026
        $this->academicYear = AcademicYear::where('year', '2025/2026')->first();
        
        if (!$this->academicYear) {
            $this->academicYear = AcademicYear::create([
                'school_id' => $this->school->id,
                'year' => '2025/2026',
                'start_date' => '2025-07-14',
                'end_date' => '2026-06-30',
                'is_active' => 1,
            ]);
            $this->info("✓ Tahun Ajaran dibuat: 2025/2026 (ID: {$this->academicYear->id})");
        } else {
            $this->info("✓ Tahun Ajaran: 2025/2026 (ID: {$this->academicYear->id})");
        }

        return true;
    }

    private function parseCSV()
    {
        $csvFile = base_path('Data Siswa SMK2.csv');
        
        if (!file_exists($csvFile)) {
            $this->error("❌ File tidak ditemukan: {$csvFile}");
            return false;
        }

        $this->info('📄 Parsing Data Siswa SMK2.csv...');
        
        $handle = fopen($csvFile, 'r');
        $header = fgetcsv($handle, 1000, ';');
        
        $students = [];
        $classes = [];
        
        while (($row = fgetcsv($handle, 1000, ';')) !== false) {
            // Skip empty rows
            if (empty($row[0]) || trim($row[0]) === '') {
                continue;
            }
            
            // SMK2 format: No;Nama;JK;NISN;Tempat Lahir;Tanggal Lahir;Agama;Rombel Saat Ini
            $data = [
                'nama' => trim($row[1]),
                'jk' => trim($row[2]),
                'nisn' => trim($row[3]),
                'tempat_lahir' => trim($row[4]),
                'tanggal_lahir' => trim($row[5]),
                'agama' => trim($row[6]),
                'kelas' => trim($row[7]),
            ];
            
            if (empty($data['nama']) || empty($data['nisn'])) {
                continue;
            }
            
            $students[] = $data;
            
            // Collect unique classes
            if (!empty($data['kelas']) && !isset($classes[$data['kelas']])) {
                $classes[$data['kelas']] = 1;
            }
        }
        
        fclose($handle);
        
        $this->csvData = [
            'students' => $students,
            'classes' => array_keys($classes),
        ];
        
        $this->info("✓ Berhasil parsing " . count($students) . " siswa dari CSV file");
        $this->info("✓ Ditemukan " . count($classes) . " rombel");
        
        return true;
    }

    private function freshImport()
    {
        $this->info('🗑️  Menghapus data SMK yang ada...');
        
        // Delete student-class assignments
        DB::table('student_classes')
            ->whereIn('classroom_id', function($q) {
                $q->select('id')->from('classrooms')
                  ->where('school_id', $this->school->id);
            })
            ->delete();
        
        // Delete students
        Student::where('school_id', $this->school->id)->delete();
        
        // Delete classrooms
        Classroom::where('school_id', $this->school->id)
            ->where('academic_year_id', $this->academicYear->id)
            ->delete();
        
        $this->info('✓ Data lama dihapus');
    }

    private function importMajors()
    {
        $this->info('🎓 Step 1: Import Program Keahlian...');
        
        // Define majors based on class names
        $majors = [
            'TSM' => 'Teknik Sepeda Motor',
            'TKR' => 'Teknik Kendaraan Ringan',
            'DPIB' => 'Desain Pemodelan dan Informasi Bangunan',
            'TAV' => 'Teknik Audio Video',
            'TKJ' => 'Teknik Komputer dan Jaringan',
            'TJKT' => 'Teknik Jaringan Komputer dan Telekomunikasi',
            'TE' => 'Teknik Elektronika',
            'TO' => 'Teknik Otomotif',
            'ACP' => 'Asisten Keperawatan',
        ];
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($majors as $code => $name) {
            $existing = Major::where('school_id', $this->school->id)
                ->where('major_code', $code)
                ->first();
            
            if ($existing) {
                $this->majorMap[$code] = $existing->id;
                $this->info("  ⏭️  {$name} → sudah ada (ID: {$existing->id})");
                $skipped++;
            } else {
                $major = Major::create([
                    'school_id' => $this->school->id,
                    'major_code' => $code,
                    'major_name' => $name,
                    'is_active' => 1,
                ]);
                
                $this->majorMap[$code] = $major->id;
                $this->info("  ✓ {$name} → imported (ID: {$major->id})");
                $imported++;
            }
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped");
    }

    private function importClassrooms()
    {
        $this->info('🏫 Step 2: Import Kelas...');
        
        $imported = 0;
        $skipped = 0;
        
        foreach ($this->csvData['classes'] as $className) {
            // Parse class name: "X DPIB" -> grade: 10, major: DPIB
            $gradeLevel = $this->parseGradeLevel($className);
            $majorCode = $this->parseMajorCode($className);
            $majorId = $this->majorMap[$majorCode] ?? null;
            
            // Generate class_code
            $classCode = $this->generateClassCode($className, $imported + 1);
            
            $existing = Classroom::where('school_id', $this->school->id)
                ->where('class_name', $className)
                ->where('academic_year_id', $this->academicYear->id)
                ->first();
            
            if ($existing) {
                $this->classroomMap[$className] = $existing->id;
                $this->info("  ⏭️  {$className} → sudah ada");
                $skipped++;
            } else {
                $classroom = Classroom::create([
                    'school_id' => $this->school->id,
                    'class_code' => $classCode,
                    'class_name' => $className,
                    'class_type' => 'Reguler',
                    'grade_level' => $gradeLevel,
                    'academic_year_id' => $this->academicYear->id,
                    'major_id' => $majorId,
                    'capacity' => 40,
                    'is_active' => 1,
                ]);
                
                $this->classroomMap[$className] = $classroom->id;
                $this->info("  ✓ {$className} (Tingkat {$gradeLevel}) → imported (ID: {$classroom->id})");
                $imported++;
            }
        }
        
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped");
    }

    private function importStudents()
    {
        $this->info('🎓 Step 3: Import Siswa...');
        
        $students = $this->csvData['students'];
        
        $imported = 0;
        $skipped = 0;
        $assigned = 0;
        
        $bar = $this->output->createProgressBar(count($students));
        $bar->start();
        
        foreach ($students as $s) {
            $existing = Student::where('nisn', $s['nisn'])->first();
            
            if ($existing) {
                $skipped++;
            } else {
                // Generate NIS from NISN (last 8 digits)
                $nis = substr($s['nisn'], -8);
                
                // Parse birth date
                $birthDate = $this->parseDate($s['tanggal_lahir']);
                
                // Calculate entry year from grade level
                $entryYear = $this->calculateEntryYear($s['kelas']);
                
                $student = Student::create([
                    'school_id' => $this->school->id,
                    'nisn' => $s['nisn'],
                    'nis' => $nis,
                    'full_name' => $s['nama'],
                    'gender' => $s['jk'] === 'P' ? 'P' : 'L',
                    'birth_place' => $s['tempat_lahir'],
                    'birth_date' => $birthDate,
                    'religion' => $this->mapReligion($s['agama']),
                    'entry_year' => $entryYear,
                ]);
                
                // Assign to classroom
                if (isset($this->classroomMap[$s['kelas']])) {
                    DB::table('student_classes')->insert([
                        'student_id' => $student->id,
                        'classroom_id' => $this->classroomMap[$s['kelas']],
                        'academic_year_id' => $this->academicYear->id,
                    ]);
                    $assigned++;
                }
                
                $imported++;
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);
        $this->info("📊 Total: {$imported} imported, {$skipped} skipped, {$assigned} assigned ke kelas");
    }

    private function previewImport()
    {
        $this->newLine();
        $this->info('📚 Program Keahlian: ' . count($this->majorMap) . ' akan diimport');
        $this->info('🏫 Kelas: ' . count($this->csvData['classes']) . ' akan diimport');
        $this->info('🎓 Siswa: ' . count($this->csvData['students']) . ' akan diimport');
    }

    private function showSummary()
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════');
        $this->info('           SUMMARY IMPORT');
        $this->info('═══════════════════════════════════════');
        
        $table = [];
        
        $majorCount = Major::where('school_id', $this->school->id)->count();
        $table[] = ['Program Keahlian', $majorCount];
        
        $classCount = Classroom::where('school_id', $this->school->id)
            ->where('academic_year_id', $this->academicYear->id)
            ->count();
        $table[] = ['Kelas (SMK)', $classCount];
        
        $studentCount = Student::where('school_id', $this->school->id)->count();
        $table[] = ['Siswa (SMK)', $studentCount];
        
        $this->table(['Item', 'Jumlah'], $table);
    }

    private function parseGradeLevel($className)
    {
        if (strpos($className, 'XII') === 0) return 12;
        if (strpos($className, 'XI') === 0) return 11;
        if (strpos($className, 'X ') === 0) return 10;
        return 10;
    }

    private function parseMajorCode($className)
    {
        // Extract major code from class name
        if (preg_match('/(DPIB|TAV|TKJ|TJKT|TKR|TSM|TE|TO|ACP)/', $className, $m)) {
            return $m[1];
        }
        return null;
    }

    private function generateClassCode($className, $sequence)
    {
        $grade = $this->parseGradeLevel($className);
        $majorCode = $this->parseMajorCode($className);
        return 'CLS-' . $grade . '-' . ($majorCode ?? 'REG') . '-' . sprintf('%02d', $sequence);
    }

    private function calculateEntryYear($className)
    {
        $grade = $this->parseGradeLevel($className);
        $currentYear = 2025; // Academic year 2025/2026
        
        // Grade 10 -> entry 2025, Grade 11 -> entry 2024, Grade 12 -> entry 2023
        return $currentYear - ($grade - 10);
    }

    private function parseDate($dateString)
    {
        // Parse date format: "2010-05-29" or other formats
        if (empty($dateString)) {
            return null;
        }
        
        try {
            $date = \Carbon\Carbon::parse($dateString);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function mapReligion($agama)
    {
        $map = [
            'Kristen' => 'Kristen',
            'Katholik' => 'Katolik',
            'Islam' => 'Islam',
            'Hindu' => 'Hindu',
            'Budha' => 'Buddha',
        ];
        
        return $map[$agama] ?? 'Lainnya';
    }
}
