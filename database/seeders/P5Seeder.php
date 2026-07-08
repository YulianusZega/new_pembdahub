<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\P5Assessment;
use App\Models\P5Project;
use App\Models\P5ProjectNote;
use App\Models\P5ProjectTarget;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class P5Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->first();
        if (!$activeYear) {
            $activeYear = AcademicYear::first();
        }

        if (!$activeYear) {
            return;
        }

        // Find classrooms with a homeroom teacher assigned
        $classrooms = Classroom::whereNotNull('homeroom_teacher_id')->where('is_active', true)->get();

        if ($classrooms->isEmpty()) {
            // Fallback: get any active classrooms
            $classrooms = Classroom::where('is_active', true)->get();
        }

        $faker = Faker::create('id_ID');

        foreach ($classrooms as $classroom) {
            // Find creator user (homeroom teacher's user, or admin, or first user)
            $teacher = $classroom->homeroomTeacher;
            $creatorId = 1; // Default fallback to SuperAdmin
            if ($teacher && $teacher->user_id) {
                $creatorId = $teacher->user_id;
            } else {
                $user = User::where('school_id', $classroom->school_id)->where('role', 'admin_sekolah')->first();
                if ($user) {
                    $creatorId = $user->id;
                }
            }

            // Create Project 1: Kearifan Lokal Nias
            $project = P5Project::create([
                'school_id' => $classroom->school_id,
                'academic_year_id' => $activeYear->id,
                'classroom_id' => $classroom->id,
                'title' => 'Eksplorasi Budaya Tradisional Lompat Batu Nias (Fahombo)',
                'theme' => 'Kearifan Lokal',
                'description' => 'Projek ini mengajak siswa untuk mempelajari sejarah, makna nilai ksatria, dan teknik dasar tradisi lompat batu (Fahombo) di Nias, serta menyajikannya dalam bentuk karya tulis dan dokumentasi video pendek.',
                'created_by' => $creatorId,
            ]);

            // Add P5 Targets for Project 1
            $targets = [
                P5ProjectTarget::create([
                    'p5_project_id' => $project->id,
                    'dimension' => 'Berkebinekaan Global',
                    'sub_element' => 'Mendalami budaya dan identitas budaya lokal Nias serta merefleksikan nilai-nilai luhur tradisi Fahombo.',
                ]),
                P5ProjectTarget::create([
                    'p5_project_id' => $project->id,
                    'dimension' => 'Gotong Royong',
                    'sub_element' => 'Membangun komunikasi dan koordinasi yang baik dengan sesama anggota kelompok dalam pembuatan infografis/video.',
                ]),
                P5ProjectTarget::create([
                    'p5_project_id' => $project->id,
                    'dimension' => 'Kreatif',
                    'sub_element' => 'Menghasilkan gagasan orisinal dalam menyajikan visualisasi sejarah lompat batu melalui media digital.',
                ])
            ];

            // Get students in this class
            $students = $classroom->students()
                ->wherePivot('status', 'aktif')
                ->wherePivot('academic_year_id', $activeYear->id)
                ->get();

            if ($students->isEmpty()) {
                // Fallback: get any students in the school
                $students = Student::where('school_id', $classroom->school_id)->limit(10)->get();
            }

            foreach ($students as $student) {
                // Assessment scores
                foreach ($targets as $target) {
                    $score = $faker->randomElement(['SB', 'BSH', 'SAB']);
                    P5Assessment::create([
                        'p5_project_id' => $project->id,
                        'student_id' => $student->id,
                        'p5_project_target_id' => $target->id,
                        'score' => $score,
                    ]);
                }

                // Process note
                P5ProjectNote::create([
                    'p5_project_id' => $project->id,
                    'student_id' => $student->id,
                    'notes' => $faker->randomElement([
                        'Siswa menunjukkan ketertarikan tinggi saat riset lapangan, berkolaborasi dengan sangat aktif dalam wawancara tokoh budaya lokal Nias.',
                        'Siswa konsisten membantu rekan sekelompok dalam mengumpulkan dokumentasi foto dan berkontribusi penuh pada pembuatan laporan.',
                        'Siswa sangat kreatif dalam menyusun naskah video dokumenter P5 dan menunjukkan kepemimpinan yang baik di kelompoknya.',
                        'Siswa mampu memahami nilai-nilai budaya Nias dengan baik serta menyelesaikan tugas projek tepat waktu dengan hasil memuaskan.'
                    ]),
                ]);
            }
        }
    }
}
