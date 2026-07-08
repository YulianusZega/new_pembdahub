<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Classroom;
use App\Models\AcademicYear;
use App\Models\StudentBill;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentClassroomAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Hapus semua data tagihan dan pembayaran
        $this->command->info('Deleting bills and payments...');
        DB::table('payments')->delete();
        DB::table('student_bills')->delete();
        $this->command->info('Bills and payments deleted!');

        // 2. Hapus assignment lama
        DB::table('student_classes')->delete();

        // 3. Get active academic year
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        if (!$activeAcademicYear) {
            $this->command->error('No active academic year found!');
            return;
        }

        // 4. Nama-nama Nias yang realistis
        $niasFirstNames = [
            'Yohanes', 'Maria', 'Andreas', 'Elisabeth', 'Samuel', 'Ruth',
            'Daniel', 'Ester', 'Petrus', 'Sara', 'Yosua', 'Debora',
            'Paulus', 'Hana', 'Simon', 'Lea', 'Tomas', 'Rina',
            'Lukas', 'Anna', 'Markus', 'Dina', 'Yakobus', 'Ribka',
            'Mateus', 'Naomi', 'Filipus', 'Lidia', 'Natanael', 'Rut',
            'Timotius', 'Rahel', 'Stefanus', 'Miriam', 'Barnabas', 'Tamar',
            'Silas', 'Zefora', 'Apolos', 'Yulia', 'Titus', 'Priskila',
            'Erastus', 'Dorkas', 'Aleksander', 'Tabita', 'Kornelius', 'Lidia',
            'Akuila', 'Febe', 'Epafras', 'Kloe', 'Timoteus', 'Eunika',
            'Trofimus', 'Lois', 'Aristarkus', 'Persis', 'Urbanus', 'Maria',
        ];

        $niasLastNames = [
            'Zebua', 'Laia', 'Gulo', 'Waruwu', 'Dakhi', 'Telaumbanua',
            'Zega', 'Hia', 'Mendrofa', 'Halawa', 'Ndraha', 'Hulu',
            'Faoma', 'Wau', 'Hadia', 'Lase', 'Logo', 'Zaluchu',
            'Harefa', 'Ziliwu', 'Duha', 'Laoli', 'Bawamenewi', 'Dachi',
            'Fau', 'Zai', 'Lahagu', 'Nazara', 'Sarumaha', 'Bate',
        ];

        // 5. Update existing students dengan nama Nias
        $students = Student::all();
        foreach ($students as $index => $student) {
            $firstName = $niasFirstNames[$index % count($niasFirstNames)];
            $lastName = $niasLastNames[$index % count($niasLastNames)];
            
            $student->update([
                'full_name' => "$firstName $lastName"
            ]);
        }
        $this->command->info('Updated student names with Nias names!');

        // 6. Assign students to classrooms (5 per class)
        $classrooms = Classroom::all();
        $assignedStudentIds = []; // Track assigned students
        $totalAssignments = 0;

        foreach ($classrooms as $classroom) {
            // Get 5 unassigned students from the same school
            $schoolStudents = Student::where('school_id', $classroom->school_id)
                ->whereNotIn('id', $assignedStudentIds)
                ->take(5)
                ->get();

            if ($schoolStudents->count() < 5) {
                // If not enough students in this school, get from any school
                $remaining = 5 - $schoolStudents->count();
                $additionalStudents = Student::whereNotIn('id', array_merge($assignedStudentIds, $schoolStudents->pluck('id')->toArray()))
                    ->take($remaining)
                    ->get();
                $schoolStudents = $schoolStudents->merge($additionalStudents);
            }

            foreach ($schoolStudents as $student) {
                DB::table('student_classes')->insert([
                    'student_id' => $student->id,
                    'classroom_id' => $classroom->id,
                    'academic_year_id' => $activeAcademicYear->id,
                    'status' => 'aktif',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $assignedStudentIds[] = $student->id; // Mark as assigned
                $totalAssignments++;
            }

            $this->command->info("Assigned {$schoolStudents->count()} students to {$classroom->class_name} ({$classroom->school->name})");
        }

        $this->command->info("Total assignments created: $totalAssignments");
        $this->command->info('Student assignments completed successfully!');
    }
}
