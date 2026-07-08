<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\TeachingAssignment;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;

class SimulateSchedulesSMK extends Command
{
    protected $signature = 'simulate:schedules-smk {--target-year= : Specific target year e.g. 2026/2027}';
    protected $description = 'Generate simulated teaching assignments and schedules for SMKS Pembda Nias, cloned from previous year odd semester (excluding Saturday).';

    public function handle()
    {
        $this->info('Memulai Proses Simulasi Jadwal & Penugasan SMK...');

        // 1. Cari Sekolah SMK
        $school = School::where('name', 'like', '%SMK%')->first();
        if (!$school) {
            $this->error('Sekolah SMK tidak ditemukan!');
            return 1;
        }
        $this->info("Ditemukan Sekolah: {$school->name}");

        // 2. Setup Tahun Ajaran Target
        $targetYearName = $this->option('target-year') ?: '2026/2027';
        // Cari berdasarkan nama yang mirip atau cari yang aktif saat ini
        $targetYear = AcademicYear::where('year', 'like', '%' . $targetYearName . '%')->first();
        
        if (!$targetYear) {
            // Jika tidak ketemu berdasarkan nama, ambil yang sedang aktif saja!
            $targetYear = AcademicYear::where('is_active', true)->first();
        }
        
        if (!$targetYear) {
            $this->error("Tidak ada Tahun Ajaran yang aktif atau cocok dengan {$targetYearName}. Silakan buat manual di menu Tahun Pelajaran.");
            return 1;
        }
        
        // Ambil Semester Ganjil dari Tahun Ajaran Target
        $targetSemester = Semester::where('academic_year_id', $targetYear->id)
                                ->where('semester_number', 1)
                                ->first();
        
        if (!$targetSemester) {
            $this->error("Semester Ganjil untuk TP {$targetYear->year} tidak ditemukan! Silakan buat manual di menu Semester.");
            return 1;
        }

        $this->info("Target: Tahun {$targetYear->year} - Semester {$targetSemester->semester_name}");

        // 3. Cari Data Semester Ganjil Tahun Sebelumnya (Atau yang ada datanya)
        $previousSemester = Semester::where('semester_number', 1)
                                    ->where('id', '!=', $targetSemester->id)
                                    ->whereHas('academicYear', function($q) use ($targetYear) {
                                        $q->where('start_date', '<', $targetYear->start_date ?? '2026-07-01');
                                    })
                                    ->orderBy('id', 'desc')
                                    ->first();
                                    
        if (!$previousSemester) {
            // Fallback cari sembarang ganjil
            $previousSemester = Semester::where('semester_number', 1)->where('id', '!=', $targetSemester->id)->first();
        }

        if (!$previousSemester) {
            $this->error("Tidak ada data semester ganjil sebelumnya untuk di-cloning!");
            return 1;
        }

        $this->info("Sumber Kloning: Tahun {$previousSemester->academicYear->year} - Semester {$previousSemester->semester_name}");

        // 4. Proses Pemetaan Classrooms
        // Hanya memetakan kelas lama ke kelas baru JIKA KELAS BARU SUDAH DIBUAT secara manual (berdasarkan nama)
        $oldClassrooms = Classroom::where('academic_year_id', $previousSemester->academic_year_id)
                                  ->where('school_id', $school->id)
                                  ->get();
        
        $classroomMap = []; // old_id => new_id
        $skippedClasses = 0;
        
        foreach ($oldClassrooms as $oldClass) {
            $newClass = Classroom::where('school_id', $school->id)
                                 ->where('academic_year_id', $targetYear->id)
                                 ->where('class_name', $oldClass->class_name)
                                 ->first();
            
            if ($newClass) {
                $classroomMap[$oldClass->id] = $newClass->id;
            } else {
                $skippedClasses++;
            }
        }
        
        $this->info("Berhasil memetakan " . count($classroomMap) . " kelas ke tahun ajaran baru. (Skipped/Belum dibuat: {$skippedClasses} kelas)");

        DB::beginTransaction();
        try {
            // 4.5 Cloning Time Slots
            $oldTimeSlots = \App\Models\TimeSlot::where('school_id', $school->id)
                                    ->where('academic_year_id', $previousSemester->academic_year_id)
                                    ->get();
                                    
            // Fallback for first time migration where academic_year_id might be null
            if ($oldTimeSlots->isEmpty()) {
                $oldTimeSlots = \App\Models\TimeSlot::where('school_id', $school->id)
                                        ->whereNull('academic_year_id')
                                        ->get();
            }

            $timeSlotMap = [];
            $countTimeSlots = 0;
            foreach ($oldTimeSlots as $oldTs) {
                $newTs = \App\Models\TimeSlot::firstOrCreate([
                    'school_id' => $school->id,
                    'academic_year_id' => $targetYear->id,
                    'day_of_week' => $oldTs->day_of_week,
                    'slot_order' => $oldTs->slot_order,
                ], [
                    'slot_name' => $oldTs->slot_name,
                    'slot_type' => $oldTs->slot_type,
                    'start_time' => $oldTs->start_time,
                    'end_time' => $oldTs->end_time,
                    'duration_minutes' => $oldTs->duration_minutes,
                    'is_teaching_slot' => $oldTs->is_teaching_slot,
                    'is_active' => $oldTs->is_active,
                ]);
                $timeSlotMap[$oldTs->id] = $newTs->id;
                $countTimeSlots++;
            }
            $this->info("Berhasil meng-kloning {$countTimeSlots} Time Slot.");

            // 4.6 Cloning Employee Positions (Penugasan Jabatan)
            $oldPositions = \App\Models\EmployeePosition::where('academic_year_id', $previousSemester->academic_year_id)
                                    ->where('semester', strtolower($previousSemester->semester_name))
                                    ->get();

            $countPositions = 0;
            foreach ($oldPositions as $oldPos) {
                \App\Models\EmployeePosition::firstOrCreate([
                    'employee_id' => $oldPos->employee_id,
                    'academic_year_id' => $targetYear->id,
                    'semester' => strtolower($targetSemester->semester_name),
                    'position_id' => $oldPos->position_id,
                ], [
                    'classroom_id' => $oldPos->classroom_id ? ($classroomMap[$oldPos->classroom_id] ?? $oldPos->classroom_id) : null,
                    'start_date' => $targetSemester->start_date ?? $oldPos->start_date,
                    'end_date' => $targetSemester->end_date ?? $oldPos->end_date,
                    'sk_number' => $oldPos->sk_number,
                    'sk_date' => $oldPos->sk_date,
                    'notes' => $oldPos->notes,
                    'is_primary' => $oldPos->is_primary,
                    'workload_hours' => $oldPos->workload_hours,
                    'position_allowance' => $oldPos->position_allowance,
                ]);
                $countPositions++;
            }
            $this->info("Berhasil meng-kloning {$countPositions} Penugasan Jabatan.");

            // 5. Cloning Teaching Assignments
            $oldAssignments = TeachingAssignment::where('academic_year_id', $previousSemester->academic_year_id)
                                ->where('semester_id', $previousSemester->id)
                                ->whereIn('classroom_id', array_keys($classroomMap))
                                ->get();

            $assignmentMap = []; // old_id => new_id
            $countAssignments = 0;

            foreach ($oldAssignments as $oldAss) {
                // Ensure the teacher still exists/active (just in case)
                $teacher = Teacher::find($oldAss->teacher_id);
                if (!$teacher) continue;

                $newAss = TeachingAssignment::firstOrCreate([
                    'teacher_id' => $oldAss->teacher_id,
                    'subject_id' => $oldAss->subject_id,
                    'classroom_id' => $classroomMap[$oldAss->classroom_id],
                    'academic_year_id' => $targetYear->id,
                    'semester_id' => $targetSemester->id,
                    'is_main_teacher' => $oldAss->is_main_teacher,
                    'group_code' => $oldAss->group_code,
                ], [
                    'hours_per_week' => $oldAss->hours_per_week,
                    'teaching_load_type' => $oldAss->teaching_load_type,
                    'hourly_rate' => $oldAss->hourly_rate,
                    'teaching_allowance' => $oldAss->teaching_allowance,
                    'is_active' => true,
                ]);
                $assignmentMap[$oldAss->id] = $newAss->id;
                $countAssignments++;
            }
            $this->info("Berhasil meng-kloning {$countAssignments} Penugasan Mengajar.");

            // 6. Cloning Schedules (Skip Saturday)
            $oldSchedules = Schedule::where('academic_year_id', $previousSemester->academic_year_id)
                            ->where('semester_id', $previousSemester->id)
                            ->whereIn('classroom_id', array_keys($classroomMap))
                            ->get();

            $countSchedules = 0;
            $skippedSaturday = 0;

            foreach ($oldSchedules as $oldSched) {
                // Skip if saturday
                $dayStr = strtolower((string)$oldSched->day_of_week);
                if ($dayStr === 'saturday' || $dayStr === '6' || $dayStr === 'sabtu') {
                    $skippedSaturday++;
                    continue;
                }

                $newAssId = $oldSched->teaching_assignment_id ? ($assignmentMap[$oldSched->teaching_assignment_id] ?? null) : null;
                $newClassroomId = $classroomMap[$oldSched->classroom_id] ?? null;

                if (!$newClassroomId) continue;
                
                // PENTING: Gunakan time_slot_id yang baru!
                $newTimeSlotId = $oldSched->time_slot_id ? ($timeSlotMap[$oldSched->time_slot_id] ?? $oldSched->time_slot_id) : null;

                Schedule::firstOrCreate([
                    'teaching_assignment_id' => $newAssId,
                    'school_id' => $school->id,
                    'academic_year_id' => $targetYear->id,
                    'semester_id' => $targetSemester->id,
                    'semester' => strtolower($targetSemester->semester_name),
                    'classroom_id' => $newClassroomId,
                    'day_of_week' => $oldSched->day_of_week,
                    'time_slot_id' => $newTimeSlotId,
                ], [
                    'subject_id' => $oldSched->subject_id,
                    'duration_slots' => $oldSched->duration_slots,
                    'teacher_id' => $oldSched->teacher_id,
                    'start_time' => $oldSched->start_time,
                    'end_time' => $oldSched->end_time,
                    'room' => $oldSched->room,
                    'group_code' => $oldSched->group_code,
                ]);
                $countSchedules++;
            }

            DB::commit();
            $this->info("Berhasil meng-kloning {$countSchedules} Jadwal Mengajar (Di-skip hari Sabtu: {$skippedSaturday}).");
            $this->info('Simulasi Selesai dengan SUKSES!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Gagal melakukan simulasi: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
