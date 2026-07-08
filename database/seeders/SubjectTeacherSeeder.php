<?php

namespace Database\Seeders;

use App\Models\Teacher;
use App\Models\Subject;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectTeacherSeeder extends Seeder
{
    /**
     * Seed subject_teacher pivot table with realistic competency assignments
     */
    public function run(): void
    {
        $this->command->info('🎓 Assigning subject competencies to teachers...');
        
        $schools = School::all();
        
        if ($schools->isEmpty()) {
            $this->command->error('❌ No schools found in database!');
            return;
        }

        // Define subject competencies by subject name patterns
        $subjectCompetencies = [
            // SMP subjects
            'Matematika' => ['math', 'general'],
            'Bahasa Indonesia' => ['language'],
            'Bahasa Inggris' => ['language', 'english'],
            'IPA' => ['science'],
            'IPS' => ['social'],
            'Pendidikan Agama' => ['religion', 'general'],
            'PPKN' => ['social', 'general'],
            'Seni Budaya' => ['arts', 'general'],
            // SMA subjects
            'Fisika' => ['science', 'math'],
            'Kimia' => ['science'],
            'Biologi' => ['science'],
            'Sejarah' => ['social'],
            'Geografi' => ['social'],
            'Ekonomi' => ['business', 'economics', 'social'],
            'Sosiologi' => ['social'],
            // SMK subjects
            'Pemrograman Web' => ['programming', 'tech'],
            'Jaringan Komputer' => ['networking', 'tech'],
            'Basis Data' => ['database', 'tech'],
            'Sistem Operasi' => ['system', 'tech'],
            'Akuntansi Dasar' => ['accounting', 'business'],
            'Praktikum Akuntansi' => ['accounting', 'business'],
            'Ekonomi Bisnis' => ['business', 'economics'],
            'Logika Matematika' => ['math', 'general'],
            'English Business Communication' => ['language', 'english', 'business'],
            'Desain Grafis' => ['tech', 'arts'],
            'Pemrograman Dasar' => ['programming', 'tech'],
            'Administrasi Server' => ['networking', 'tech', 'system'],
            'Produk Kreatif' => ['business', 'arts'],
        ];
        
        // Teacher specialization profiles (cycled per school)
        $teacherSpecializations = [
            0 => ['math', 'science', 'general'],           // Math/Science specialist
            1 => ['language', 'english', 'social'],         // Language/Social specialist
            2 => ['science', 'math'],                       // Pure Science
            3 => ['social', 'religion', 'general'],         // Social/General
            4 => ['programming', 'tech', 'database'],       // Tech specialist
            5 => ['accounting', 'business', 'economics'],   // Business specialist
            6 => ['networking', 'tech', 'system'],          // Network specialist
            7 => ['arts', 'general', 'language'],            // Arts/General specialist
        ];
        
        DB::beginTransaction();
        try {
            $totalAssignments = 0;
            
            foreach ($schools as $school) {
                $teachers = Teacher::where('school_id', $school->id)
                    ->where('is_active', 1)
                    ->get();
                
                $subjects = Subject::where('school_id', $school->id)
                    ->where('is_active', 1)
                    ->get();
                
                if ($teachers->isEmpty() || $subjects->isEmpty()) {
                    $this->command->warn("⚠️  Skipping {$school->school_name}: no teachers or subjects.");
                    continue;
                }

                $this->command->info("📚 Processing {$school->school_name} ({$school->type})...");
                $schoolAssignments = 0;
                
                foreach ($teachers as $index => $teacher) {
                    $specialization = $teacherSpecializations[$index % count($teacherSpecializations)];
                    
                    $teacherAssignments = [];
                    $primarySubjectId = null;
                    
                    foreach ($subjects as $subject) {
                        $subjectCategory = $subjectCompetencies[$subject->subject_name] ?? ['general'];
                        
                        $isCompetent = !empty(array_intersect($specialization, $subjectCategory));
                        
                        if ($isCompetent) {
                            $teacherAssignments[] = [
                                'teacher_id' => $teacher->id,
                                'subject_id' => $subject->id,
                                'is_primary' => false,
                                'notes' => 'Assigned by system seeder',
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                            
                            if ($primarySubjectId === null) {
                                $primarySubjectId = $subject->id;
                            }
                        }
                    }
                    
                    // Ensure each teacher gets at least 1 subject
                    if (empty($teacherAssignments) && $subjects->isNotEmpty()) {
                        $fallbackSubject = $subjects->random();
                        $teacherAssignments[] = [
                            'teacher_id' => $teacher->id,
                            'subject_id' => $fallbackSubject->id,
                            'is_primary' => true,
                            'notes' => 'Fallback assignment by seeder',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $primarySubjectId = $fallbackSubject->id;
                    }
                    
                    if (!empty($teacherAssignments)) {
                        if ($primarySubjectId) {
                            foreach ($teacherAssignments as &$assignment) {
                                if ($assignment['subject_id'] == $primarySubjectId) {
                                    $assignment['is_primary'] = true;
                                    break;
                                }
                            }
                        }
                        
                        DB::table('subject_teacher')->insert($teacherAssignments);
                        $schoolAssignments += count($teacherAssignments);
                    }
                }
                
                $this->command->info("   ✓ {$school->school_name}: {$schoolAssignments} competencies for {$teachers->count()} teachers");
                $totalAssignments += $schoolAssignments;
            }
            
            DB::commit();
            $this->command->info("✅ Successfully assigned {$totalAssignments} subject competencies across {$schools->count()} schools!");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Error assigning competencies: ' . $e->getMessage());
        }
    }
}

