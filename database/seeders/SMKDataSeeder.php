<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\ProgramKeahlian;
use App\Models\KonsentrasiKeahlian;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\AcademicYear;
use App\Models\TimeSlot;
use App\Models\Schedule;

class SMKDataSeeder extends Seeder
{
    private $smk;
    private $academicYear;
    private $timeSlots;
    private $programs;
    private $teachers = [];
    private $subjects = [];
    private $classrooms = [];

    public function run(): void
    {
        $this->command->info('🔄 Memulai proses seeding data SMK...');
        
        // Get SMK school
        $this->smk = School::where('type', 'SMK')->first();
        if (!$this->smk) {
            $this->command->error('❌ Sekolah SMK tidak ditemukan!');
            return;
        }
        
        $this->academicYear = AcademicYear::where('is_active', true)->first();
        if (!$this->academicYear) {
            $this->command->error('❌ Tahun ajaran aktif tidak ditemukan!');
            return;
        }

        $this->programs = ProgramKeahlian::with('konsentrasiKeahlians')->where('school_id', $this->smk->id)->get();
        if ($this->programs->isEmpty()) {
            $this->command->error('❌ Program Keahlian tidak ditemukan!');
            return;
        }

        // Step 1: Delete old data
        $this->deleteOldData();

        // Step 2: Create subjects for each program
        $this->createSubjects();

        // Step 3: Create/assign teachers
        $this->assignExistingTeachers();

        // Step 4: Assign teacher competencies
        $this->assignTeacherCompetencies();

        // Step 5: Create classrooms
        $this->createClassrooms();

        // Step 6: Get time slots
        $this->timeSlots = TimeSlot::where('school_id', $this->smk->id)->orderBy('start_time')->get();

        // Step 7: Create schedules
        $this->createSchedules();

        $this->command->info('✅ Seeding data SMK selesai!');
    }

    private function deleteOldData(): void
    {
        $this->command->info('🗑️  Menghapus data lama...');
        
        $classroomIds = Classroom::where('school_id', $this->smk->id)->pluck('id');
        
        // Delete schedules
        $deletedSchedules = Schedule::whereIn('classroom_id', $classroomIds)->delete();
        $this->command->info("   Jadwal dihapus: {$deletedSchedules}");

        // Delete classrooms
        $deletedClassrooms = Classroom::where('school_id', $this->smk->id)->delete();
        $this->command->info("   Kelas dihapus: {$deletedClassrooms}");

        // Delete subjects (only for SMK - subjects with program_keahlian_id)
        $deletedSubjects = Subject::where('school_id', $this->smk->id)
            ->whereNotNull('program_keahlian_id')
            ->delete();
        $this->command->info("   Mata pelajaran SMK dihapus: {$deletedSubjects}");

        // Delete teacher competencies for deleted subjects
        DB::table('subject_teacher')
            ->whereIn('subject_id', function($query) {
                $query->select('id')
                    ->from('subjects')
                    ->where('school_id', $this->smk->id)
                    ->whereNotNull('program_keahlian_id');
            })
            ->delete();
    }

    private function createSubjects(): void
    {
        $this->command->info('📚 Membuat mata pelajaran...');

        // Mata pelajaran umum (wajib untuk semua program)
        $commonSubjects = [
            ['kode' => 'PAI', 'nama' => 'Pendidikan Agama Islam', 'kategori' => 'Umum'],
            ['kode' => 'PKN', 'nama' => 'Pendidikan Kewarganegaraan', 'kategori' => 'Umum'],
            ['kode' => 'BIND', 'nama' => 'Bahasa Indonesia', 'kategori' => 'Umum'],
            ['kode' => 'MTK', 'nama' => 'Matematika', 'kategori' => 'Umum'],
            ['kode' => 'BING', 'nama' => 'Bahasa Inggris', 'kategori' => 'Umum'],
            ['kode' => 'PJOK', 'nama' => 'Pendidikan Jasmani, Olahraga dan Kesehatan', 'kategori' => 'Umum'],
            ['kode' => 'SBD', 'nama' => 'Seni Budaya', 'kategori' => 'Umum'],
            ['kode' => 'PKWU', 'nama' => 'Prakarya dan Kewirausahaan', 'kategori' => 'Umum'],
        ];

        foreach ($commonSubjects as $subject) {
            $this->subjects[] = Subject::create([
                'school_id' => $this->smk->id,
                'code' => $subject['kode'],
                'name' => $subject['nama'],
                'category' => 'Wajib',
                'hours_per_week' => 2,
                'is_active' => true,
            ]);
        }

        // Mata pelajaran produktif per program
        foreach ($this->programs as $program) {
            $this->command->info("   Program: {$program->nama}");

            $productiveSubjects = $this->getProductiveSubjects($program->kode);
            
            foreach ($productiveSubjects as $subject) {
                $this->subjects[] = Subject::create([
                    'school_id' => $this->smk->id,
                    'program_keahlian_id' => $program->id,
                    'code' => $subject['kode'],
                    'name' => $subject['nama'],
                    'category' => 'Produktif',
                    'hours_per_week' => $subject['jam'] ?? 4,
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info("   Total mata pelajaran: " . count($this->subjects));
    }

    private function getProductiveSubjects(string $programCode): array
    {
        $subjects = [
            'TO' => [ // Teknik Otomotif
                ['kode' => 'TMO', 'nama' => 'Teknologi Mekanik Otomotif', 'jam' => 4],
                ['kode' => 'PKKR', 'nama' => 'Pemeliharaan Kelistrikan Kendaraan Ringan', 'jam' => 6],
                ['kode' => 'PCKR', 'nama' => 'Pemeliharaan Chasis dan Transmisi Kendaraan Ringan', 'jam' => 6],
                ['kode' => 'PEMKR', 'nama' => 'Pemeliharaan Engine Kendaraan Ringan', 'jam' => 6],
                ['kode' => 'PPKR', 'nama' => 'Pemeliharaan dan Perbaikan Kendaraan Ringan', 'jam' => 6],
            ],
            'TE' => [ // Teknik Elektronika
                ['kode' => 'DE', 'nama' => 'Dasar Elektronika', 'jam' => 4],
                ['kode' => 'DL', 'nama' => 'Dasar Listrik', 'jam' => 4],
                ['kode' => 'TEA', 'nama' => 'Teknik Elektronika Analog', 'jam' => 6],
                ['kode' => 'TED', 'nama' => 'Teknik Elektronika Digital', 'jam' => 6],
                ['kode' => 'TAV', 'nama' => 'Teknik Audio Video', 'jam' => 8],
            ],
            'TKP' => [ // Teknik Konstruksi dan Properti
                ['kode' => 'GB', 'nama' => 'Gambar Bangunan', 'jam' => 6],
                ['kode' => 'MKB', 'nama' => 'Mekanika Konstruksi Bangunan', 'jam' => 4],
                ['kode' => 'DPIB', 'nama' => 'Desain Pemodelan dan Informasi Bangunan', 'jam' => 8],
                ['kode' => 'KB', 'nama' => 'Konstruksi Bangunan', 'jam' => 6],
                ['kode' => 'UKB', 'nama' => 'Ukur Tanah dan Pemetaan', 'jam' => 4],
            ],
            'TJKT' => [ // Teknik Jaringan Komputer dan Telekomunikasi
                ['kode' => 'SKJ', 'nama' => 'Sistem Komputer dan Jaringan', 'jam' => 6],
                ['kode' => 'PJKL', 'nama' => 'Perakitan dan Jaringan Komputer Lokal', 'jam' => 6],
                ['kode' => 'AWJ', 'nama' => 'Administrasi dan Web Jaringan', 'jam' => 6],
                ['kode' => 'TKJ', 'nama' => 'Teknologi Jaringan', 'jam' => 8],
                ['kode' => 'KPJ', 'nama' => 'Keamanan dan Perawatan Jaringan', 'jam' => 4],
            ],
        ];

        return $subjects[$programCode] ?? [];
    }

    private function assignExistingTeachers(): void
    {
        $this->command->info('👨‍🏫 Menggunakan guru yang sudah ada...');

        // Get existing teachers from SMK
        $allTeachers = Teacher::where('school_id', $this->smk->id)->get();
        
        if ($allTeachers->isEmpty()) {
            $this->command->warn('   ⚠️ Tidak ada guru di SMK, buat guru terlebih dahulu');
            return;
        }

        // Distribute teachers to subjects
        // Guru umum - ambil semua guru untuk mata pelajaran umum
        $commonSubjectCodes = ['PAI', 'PKN', 'BIND', 'MTK', 'BING', 'PJOK', 'SBD', 'PKWU'];
        foreach ($commonSubjectCodes as $code) {
            $this->teachers[$code] = $allTeachers->take(2)->values()->all(); // 2 guru per mapel umum
        }

        // Guru produktif - distribute evenly to programs
        $programCodes = ['TO', 'TE', 'TKP', 'TJKT'];
        $teachersPerProgram = max(1, intval($allTeachers->count() / count($programCodes)));
        
        foreach ($programCodes as $index => $code) {
            $this->teachers[$code] = $allTeachers
                ->slice($index * $teachersPerProgram, $teachersPerProgram)
                ->values()
                ->all();
        }

        $this->command->info("   Menggunakan " . $allTeachers->count() . " guru yang ada");
    }

    private function assignTeacherCompetencies(): void
    {
        $this->command->info('🎯 Menetapkan kompetensi guru...');

        // Assign kompetensi mata pelajaran umum
        $commonSubjectCodes = ['PAI', 'PKN', 'BIND', 'MTK', 'BING', 'PJOK', 'SBD', 'PKWU'];
        foreach ($commonSubjectCodes as $code) {
            $subject = collect($this->subjects)->firstWhere('code', $code);
            if ($subject && isset($this->teachers[$code])) {
                foreach ($this->teachers[$code] as $teacher) {
                    DB::table('subject_teacher')->insert([
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'is_primary' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Assign kompetensi mata pelajaran produktif
        foreach ($this->programs as $program) {
            $programSubjects = collect($this->subjects)->where('program_keahlian_id', $program->id);
            $programTeachers = $this->teachers[$program->kode] ?? [];

            foreach ($programSubjects as $subject) {
                foreach ($programTeachers as $teacher) {
                    DB::table('subject_teacher')->insert([
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'is_primary' => rand(0, 1) == 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        $this->command->info("   Kompetensi guru ditetapkan");
    }

    private function createClassrooms(): void
    {
        $this->command->info('🏫 Membuat kelas...');

        foreach ($this->programs as $program) {
            foreach ($program->konsentrasiKeahlians as $konsentrasi) {
                // Buat 3 tingkat: X, XI, XII
                for ($grade = 10; $grade <= 12; $grade++) {
                    $romanGrade = $this->toRoman($grade);
                    $className = "{$romanGrade} {$konsentrasi->kode}";
                    $classCode = strtoupper(str_replace(' ', '-', $className));
                    
                    $this->classrooms[] = Classroom::create([
                        'school_id' => $this->smk->id,
                        'academic_year_id' => $this->academicYear->id,
                        'program_keahlian_id' => $program->id,
                        'konsentrasi_keahlian_id' => $konsentrasi->id,
                        'class_code' => $classCode,
                        'class_name' => $className,
                        'grade_level' => $grade,
                        'capacity' => 32,
                        'is_active' => true,
                    ]);

                    $this->command->info("   Kelas dibuat: {$className}");
                }
            }
        }

        $this->command->info("   Total kelas: " . count($this->classrooms));
    }

    private function createSchedules(): void
    {
        $this->command->info('📅 Membuat jadwal...');

        if ($this->timeSlots->isEmpty()) {
            $this->command->warn('   ⚠️ Time slot tidak ditemukan, lewati pembuatan jadwal');
            return;
        }

        $this->command->info("   Time slots tersedia: {$this->timeSlots->count()}");

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $scheduleCount = 0;

        foreach ($this->classrooms as $classroom) {
            // Get subjects for this classroom
            $classroomSubjects = collect($this->subjects)->filter(function($subject) use ($classroom) {
                // Common subjects (no program_keahlian_id) OR subjects matching classroom's program
                return is_null($subject->program_keahlian_id) || 
                       $subject->program_keahlian_id == $classroom->program_keahlian_id;
            });

            $this->command->info("   Kelas {$classroom->class_name}: {$classroomSubjects->count()} mata pelajaran");

            if ($classroomSubjects->isEmpty()) {
                $this->command->warn("      ⚠️ Tidak ada mata pelajaran untuk kelas ini!");
                continue;
            }

            $subjectIndex = 0;
            $totalSubjects = $classroomSubjects->count();

            foreach ($days as $day) {
                $timeSlotsForDay = $this->timeSlots->take(4); // 4 slot per hari
                
                foreach ($timeSlotsForDay as $timeSlot) {
                    if ($subjectIndex >= $totalSubjects) {
                        $subjectIndex = 0; // Mulai dari awal jika sudah habis
                    }

                    $subject = $classroomSubjects->values()[$subjectIndex];
                    
                    // Get random competent teacher for this subject
                    $teachers = DB::table('subject_teacher')
                        ->where('subject_id', $subject->id)
                        ->pluck('teacher_id');
                    
                    if ($teachers->isEmpty()) {
                        $this->command->warn("      ⚠️ Tidak ada guru kompeten untuk mata pelajaran: {$subject->name}");
                        $subjectIndex++;
                        continue;
                    }

                    $teacherId = $teachers->random();

                    try {
                        Schedule::create([
                            'academic_year_id' => $this->academicYear->id,
                            'classroom_id' => $classroom->id,
                            'subject_id' => $subject->id,
                            'teacher_id' => $teacherId,
                            'time_slot_id' => $timeSlot->id,
                            'day' => $day,
                        ]);
                        $scheduleCount++;
                    } catch (\Exception $e) {
                        // Skip jika ada konflik
                        $this->command->warn("      ⚠️ Konflik jadwal: " . $e->getMessage());
                        continue;
                    }

                    $subjectIndex++;
                }
            }
        }

        $this->command->info("   Total jadwal dibuat: {$scheduleCount}");
    }

    private function toRoman(int $number): string
    {
        $map = [10 => 'X', 11 => 'XI', 12 => 'XII'];
        return $map[$number] ?? $number;
    }
}
