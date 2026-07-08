<?php

namespace Database\Seeders;

use App\Models\Classroom;
use App\Models\School;
use App\Models\AcademicYear;
use Illuminate\Database\Seeder;

class ClassroomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all schools and active academic year
        $schools = School::all();
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        
        if (!$activeAcademicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        // SMA Classrooms
        $smaClassrooms = [
            ['name' => 'X MIPA 1', 'grade' => 10, 'capacity' => 35],
            ['name' => 'X MIPA 2', 'grade' => 10, 'capacity' => 35],
            ['name' => 'X IPS 1', 'grade' => 10, 'capacity' => 32],
            ['name' => 'XI MIPA 1', 'grade' => 11, 'capacity' => 35],
            ['name' => 'XI MIPA 2', 'grade' => 11, 'capacity' => 35],
            ['name' => 'XI IPS 1', 'grade' => 11, 'capacity' => 32],
            ['name' => 'XII MIPA 1', 'grade' => 12, 'capacity' => 35],
            ['name' => 'XII MIPA 2', 'grade' => 12, 'capacity' => 35],
            ['name' => 'XII IPS 1', 'grade' => 12, 'capacity' => 32],
        ];

        // SMP Classrooms
        $smpClassrooms = [
            ['name' => 'VII A', 'grade' => 7, 'capacity' => 35],
            ['name' => 'VII B', 'grade' => 7, 'capacity' => 35],
            ['name' => 'VIII A', 'grade' => 8, 'capacity' => 35],
            ['name' => 'VIII B', 'grade' => 8, 'capacity' => 35],
            ['name' => 'IX A', 'grade' => 9, 'capacity' => 35],
            ['name' => 'IX B', 'grade' => 9, 'capacity' => 35],
        ];

        // SMK Classrooms (dengan jurusan)
        $smkClassrooms = [
            // Kelas X
            ['name' => 'X TKJ 1', 'grade' => 10, 'capacity' => 36],
            ['name' => 'X TKJ 2', 'grade' => 10, 'capacity' => 36],
            ['name' => 'X RPL 1', 'grade' => 10, 'capacity' => 36],
            ['name' => 'X RPL 2', 'grade' => 10, 'capacity' => 36],
            ['name' => 'X AKL 1', 'grade' => 10, 'capacity' => 32],
            ['name' => 'X OTKP 1', 'grade' => 10, 'capacity' => 32],
            
            // Kelas XI
            ['name' => 'XI TKJ 1', 'grade' => 11, 'capacity' => 36],
            ['name' => 'XI TKJ 2', 'grade' => 11, 'capacity' => 36],
            ['name' => 'XI RPL 1', 'grade' => 11, 'capacity' => 36],
            ['name' => 'XI RPL 2', 'grade' => 11, 'capacity' => 36],
            ['name' => 'XI AKL 1', 'grade' => 11, 'capacity' => 32],
            ['name' => 'XI OTKP 1', 'grade' => 11, 'capacity' => 32],
            
            // Kelas XII
            ['name' => 'XII TKJ 1', 'grade' => 12, 'capacity' => 36],
            ['name' => 'XII TKJ 2', 'grade' => 12, 'capacity' => 36],
            ['name' => 'XII RPL 1', 'grade' => 12, 'capacity' => 36],
            ['name' => 'XII RPL 2', 'grade' => 12, 'capacity' => 36],
            ['name' => 'XII AKL 1', 'grade' => 12, 'capacity' => 32],
            ['name' => 'XII OTKP 1', 'grade' => 12, 'capacity' => 32],
        ];

        // SD Classrooms
        $sdClassrooms = [
            ['name' => 'I A', 'grade' => 1, 'capacity' => 30],
            ['name' => 'I B', 'grade' => 1, 'capacity' => 30],
            ['name' => 'II A', 'grade' => 2, 'capacity' => 32],
            ['name' => 'II B', 'grade' => 2, 'capacity' => 32],
            ['name' => 'III A', 'grade' => 3, 'capacity' => 34],
            ['name' => 'III B', 'grade' => 3, 'capacity' => 34],
            ['name' => 'IV A', 'grade' => 4, 'capacity' => 35],
            ['name' => 'IV B', 'grade' => 4, 'capacity' => 35],
            ['name' => 'V A', 'grade' => 5, 'capacity' => 36],
            ['name' => 'V B', 'grade' => 5, 'capacity' => 36],
            ['name' => 'VI A', 'grade' => 6, 'capacity' => 37],
            ['name' => 'VI B', 'grade' => 6, 'capacity' => 37],
        ];

        foreach ($schools as $school) {
            $classrooms = [];
            
            if (str_contains(strtoupper($school->name), 'SMA')) {
                $classrooms = $smaClassrooms;
            } elseif (str_contains(strtoupper($school->name), 'SMK')) {
                $classrooms = $smkClassrooms;
            } elseif (str_contains(strtoupper($school->name), 'SMP')) {
                $classrooms = $smpClassrooms;
            } elseif (str_contains(strtoupper($school->name), 'SD')) {
                $classrooms = $sdClassrooms;
            }

            foreach ($classrooms as $classroom) {
                Classroom::create([
                    'school_id' => $school->id,
                    'academic_year_id' => $activeAcademicYear->id,
                    'class_code' => $classroom['name'],
                    'class_name' => $classroom['name'],
                    'class_type' => 'reguler',
                    'type' => 'reguler',
                    'grade_level' => $classroom['grade'],
                    'capacity' => $classroom['capacity'],
                    'is_active' => true,
                ]);
            }
            
            $this->command->info("Created " . count($classrooms) . " classrooms for {$school->name}");
        }

        $this->command->info('Classrooms seeded successfully!');
    }
}
