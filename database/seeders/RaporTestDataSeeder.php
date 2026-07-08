<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grade;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Semester;
use App\Models\Teacher;
use App\Models\Schedule;
use Carbon\Carbon;

class RaporTestDataSeeder extends Seeder
{
    /**
     * Seed test data untuk rapor digital
     */
    public function run(): void
    {
        // Get active semester
        $semester = Semester::where('is_active', true)->first() 
                    ?? Semester::first();
        
        if (!$semester) {
            $this->command->error('Tidak ada semester. Jalankan SemesterSeeder dulu.');
            return;
        }

        // Get active students (limit 10 untuk testing)
        $students = Student::where('status', 'aktif')
            ->limit(10)
            ->get();

        if ($students->isEmpty()) {
            $this->command->error('Tidak ada siswa aktif.');
            return;
        }
        
        $this->command->info('Found ' . $students->count() . ' active students');

        // Get subjects (minimal 5 subjects)
        $subjects = Subject::limit(5)->get();
        
        if ($subjects->count() < 3) {
            $this->command->error('Minimal butuh 3 mata pelajaran. Jalankan SubjectSeeder dulu.');
            return;
        }

        // Get or create a teacher
        $teacher = Teacher::first();
        if (!$teacher) {
            $this->command->error('Tidak ada teacher. Buat minimal 1 teacher dulu.');
            return;
        }

        $this->command->info('🎯 Generating test data untuk ' . $students->count() . ' siswa...');
        $this->command->info('   Semester: ' . $semester->semester_name . ' (ID: ' . $semester->id . ')');
        $this->command->info('   Academic Year ID: ' . $semester->academic_year_id);

        $gradesCreated = 0;
        $attendancesCreated = 0;

        foreach ($students as $student) {
            // Get student's classroom
            $studentClass = \DB::table('student_classes')
                ->where('student_id', $student->id)
                ->where('status', 'aktif')
                ->first();
            
            if (!$studentClass) {
                $this->command->warn("Siswa {$student->full_name} tidak punya kelas aktif, skip.");
                continue;
            }
            
            $classroomId = $studentClass->classroom_id;
            
            // Generate GRADES untuk setiap mata pelajaran
            foreach ($subjects as $subject) {
                // Tugas (3 nilai, diambil rata-rata)
                for ($i = 1; $i <= 3; $i++) {
                    Grade::create([
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'semester_id' => $semester->id,
                        'grade_type' => 'tugas',
                        'score' => rand(70, 95),
                        'is_remedial' => false,
                        'notes' => 'Tugas ' . $i,
                        'created_by' => 1,
                    ]);
                    $gradesCreated++;
                }

                // UTS
                Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'semester_id' => $semester->id,
                    'grade_type' => 'uts',
                    'score' => rand(75, 92),
                    'is_remedial' => false,
                    'created_by' => 1,
                ]);
                $gradesCreated++;

                // UAS
                Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'semester_id' => $semester->id,
                    'grade_type' => 'uas',
                    'score' => rand(78, 95),
                    'is_remedial' => false,
                    'created_by' => 1,
                ]);
                $gradesCreated++;

                // Sikap
                Grade::create([
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'teacher_id' => $teacher->id,
                    'semester_id' => $semester->id,
                    'grade_type' => 'sikap',
                    'score' => rand(80, 95),
                    'is_remedial' => false,
                    'created_by' => 1,
                ]);
                $gradesCreated++;
            }

            // Generate ATTENDANCES (30 hari dalam semester)
            $startDate = Carbon::parse($semester->start_date);
            $endDate = Carbon::parse($semester->end_date);
            
            // Buat 30 hari sample
            for ($day = 0; $day < 30; $day++) {
                $date = $startDate->copy()->addDays($day);
                
                if ($date->isWeekend()) {
                    continue; // Skip weekend
                }
                
                if ($date > $endDate) {
                    break;
                }

                // Random attendance status (mayoritas hadir)
                $rand = rand(1, 100);
                if ($rand <= 85) {
                    $status = 'hadir';
                } elseif ($rand <= 93) {
                    $status = 'sakit';
                } elseif ($rand <= 97) {
                    $status = 'izin';
                } else {
                    $status = 'alpha'; // Changed from 'alpa'
                }

                // Get or create schedule for attendance
                $schedule = Schedule::where('semester_id', $semester->id)
                    ->first();

                if (!$schedule) {
                    // Create dummy schedule
                    $schedule = Schedule::create([
                        'classroom_id' => $classroomId,
                        'subject_id' => $subjects->first()->id,
                        'teacher_id' => $teacher->id,
                        'semester_id' => $semester->id,
                        'day_of_week' => 1, // Monday
                        'start_time' => '08:00:00',
                        'end_time' => '09:00:00',
                    ]);
                }

                Attendance::create([
                    'student_id' => $student->id,
                    'classroom_id' => $classroomId,
                    'schedule_id' => $schedule->id,
                    'date' => $date->format('Y-m-d'),
                    'status' => $status,
                    'notes' => $status === 'sakit' ? 'Sakit demam' : null,
                    'created_by' => 1,
                ]);
                $attendancesCreated++;
            }
        }

        $this->command->info("✅ Selesai!");
        $this->command->info("   - Grades created: {$gradesCreated}");
        $this->command->info("   - Attendances created: {$attendancesCreated}");
        $this->command->info("   - Ready untuk generate rapor!");
    }
}
