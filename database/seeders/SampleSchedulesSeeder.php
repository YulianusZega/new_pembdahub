<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\School;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TimeSlot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleSchedulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating sample teaching schedules...');

        // Get SMK school
        $smk = School::where('name', 'LIKE', '%SMK%')->first();
        if (!$smk) {
            $this->command->error('SMK school not found!');
            return;
        }

        // Get active academic year
        $academicYear = AcademicYear::where('is_active', true)->first();
        if (!$academicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        // Get active semester
        $semester = \App\Models\Semester::where('academic_year_id', $academicYear->id)
            ->where('is_active', true)
            ->first();
        if (!$semester) {
            $this->command->error('No active semester found!');
            return;
        }

        // Get SMK teachers (take first 3 for sample)
        $teachers = Teacher::where('school_id', $smk->id)
            ->where('is_active', true)
            ->take(3)
            ->get();

        if ($teachers->isEmpty()) {
            $this->command->error('No teachers found for SMK!');
            return;
        }

        // Get SMK classrooms
        $classrooms = Classroom::where('school_id', $smk->id)
            ->where('is_active', true)
            ->get();

        if ($classrooms->isEmpty()) {
            $this->command->error('No classrooms found for SMK!');
            return;
        }

        // Get SMK subjects
        $subjects = Subject::where('school_id', $smk->id)
            ->where('is_active', true)
            ->get();

        if ($subjects->isEmpty()) {
            $this->command->error('No subjects found for SMK!');
            return;
        }

        // Get time slots (only teaching slots)
        $timeSlots = TimeSlot::where('school_id', $smk->id)
            ->where('is_teaching_slot', true)
            ->where('is_active', true)
            ->orderBy('slot_order')
            ->get();

        if ($timeSlots->isEmpty()) {
            $this->command->error('No time slots found for SMK!');
            return;
        }

        $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $semesterName = 'ganjil';

        // Delete existing schedules
        Schedule::where('school_id', $smk->id)
            ->where('academic_year_id', $academicYear->id)
            ->delete();

        $scheduleCount = 0;

        // Create schedules for each teacher
        foreach ($teachers as $index => $teacher) {
            $this->command->info("Creating schedules for {$teacher->full_name}...");

            // Assign random subjects to this teacher
            $teacherSubjects = $subjects->random(min(2, $subjects->count()));

            // Create 8-12 schedules per teacher
            $schedulesPerTeacher = rand(8, 12);
            $createdForTeacher = 0;

            while ($createdForTeacher < $schedulesPerTeacher) {
                $day = $days[array_rand($days)];
                $timeSlot = $timeSlots->random();
                $classroom = $classrooms->random();
                $subject = $teacherSubjects->random();

                // Check if this slot is already taken by this teacher
                $exists = Schedule::where('teacher_id', $teacher->id)
                    ->where('academic_year_id', $academicYear->id)
                    ->where('semester', $semesterName)
                    ->where('day_of_week', $day)
                    ->where('time_slot_id', $timeSlot->id)
                    ->exists();

                if ($exists) {
                    continue; // Skip if conflict
                }

                Schedule::create([
                    'school_id' => $smk->id,
                    'teacher_id' => $teacher->id,
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subject->id,
                    'time_slot_id' => $timeSlot->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'semester' => $semesterName,
                    'day_of_week' => $day,
                ]);

                $createdForTeacher++;
                $scheduleCount++;
            }

            $this->command->info("  ✓ Created {$createdForTeacher} schedules");
        }

        $this->command->info("Successfully created {$scheduleCount} sample schedules for {$teachers->count()} teachers!");
    }
}
