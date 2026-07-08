<?php

namespace Database\Seeders;

use App\Models\Schedule;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all data needed
        $classrooms = Classroom::all();
        $subjects = Subject::all();
        $teachers = Teacher::all();

        if ($classrooms->isEmpty() || $subjects->isEmpty() || $teachers->isEmpty()) {
            $this->command->info('Tidak cukup data untuk membuat jadwal. Pastikan classrooms, subjects, dan teachers sudah di-seed.');
            return;
        }

        // Get first semester for reference (if exists)
        $semesterId = DB::table('semesters')->first()?->id ?? 1;

        // Define time slots
        $timeSlots = [
            ['start' => '07:00', 'end' => '08:30'],
            ['start' => '08:30', 'end' => '10:00'],
            ['start' => '10:15', 'end' => '11:45'],
            ['start' => '11:45', 'end' => '13:15'],
            ['start' => '13:30', 'end' => '15:00'],
        ];

        $scheduleCount = 0;

        foreach ($classrooms as $classroom) {
            // Get subjects for this school
            $schoolSubjects = $subjects->where('school_id', $classroom->school_id)->values();

            if ($schoolSubjects->isEmpty()) {
                continue;
            }

            $subjectIndex = 0;
            for ($dayOfWeek = 1; $dayOfWeek <= 5; $dayOfWeek++) { // 1-5 = Senin-Jumat
                foreach ($timeSlots as $slotIndex => $timeSlot) {
                    // Get a subject (cycle through available subjects)
                    $subject = $schoolSubjects[$subjectIndex % count($schoolSubjects)];

                    // Get a random teacher
                    $teacher = $teachers->random();

                    // Create schedule
                    DB::table('schedules')->insert([
                        'classroom_id' => $classroom->id,
                        'subject_id' => $subject->id,
                        'teacher_id' => $teacher->id,
                        'semester_id' => $semesterId,
                        'day_of_week' => $dayOfWeek,
                        'start_time' => $timeSlot['start'],
                        'end_time' => $timeSlot['end'],
                        'room' => 'Ruang ' . $classroom->grade_level . ' - ' . chr(65 + ($slotIndex % 5)),
                    ]);

                    $scheduleCount++;
                    $subjectIndex++;
                }
            }
        }

        $this->command->info("Total $scheduleCount jadwal pelajaran berhasil dibuat!");
    }
}
