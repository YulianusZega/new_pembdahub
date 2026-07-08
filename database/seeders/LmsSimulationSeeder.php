<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LmsCourse;
use App\Models\LmsModule;
use App\Models\LmsMaterial;
use App\Models\LmsAssignment;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\LmsClass;
use App\Models\TeachingAssignment;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Support\Str;

class LmsSimulationSeeder extends Seeder
{
    public function run()
    {
        $schoolId = 1; // SMPS Pembda 2 Gunungsitoli
        $activeYear = AcademicYear::where('is_active', true)->first() ?? AcademicYear::first();
        $activeSemester = Semester::where('is_active', true)->first() ?? Semester::first();

        // Standardized topics based on Kurikulum Merdeka SMP
        $subjectTemplates = [
            'Bahasa Indonesia' => [
                [
                    'title' => 'Teks Deskripsi: Keindahan Alam Indonesia',
                    'desc' => 'Mempelajari cara menggambarkan objek secara rinci dan panca indra.',
                    'material' => 'Teks deskripsi adalah teks yang melukiskan sesuatu sesuai dengan keadaan sebenarnya sehingga pembaca dapat melihat, mendengar, mencium, dan merasakan apa yang dilukiskan itu sesuai dengan citra penulisnya.',
                    'assignment' => 'Buatlah satu paragraf teks deskripsi tentang sekolah SMPS Pembda 2 Gunungsitoli.',
                    'quiz' => [
                        ['q' => 'Tujuan utama teks deskripsi adalah...', 'options' => ['Memberitahu berita', 'Menggambarkan objek secara rinci', 'Membujuk pembaca', 'Menjelaskan langkah-langkah'], 'correct' => 1]
                    ]
                ],
                [
                    'title' => 'Teks Berita: Menyaring Informasi',
                    'desc' => 'Cara mengidentifikasi unsur 5W+1H dalam sebuah berita.',
                    'material' => 'Informasi yang disampaikan dalam teks berita haruslah mengandung unsur ADIKSIMBA (Apa, Di mana, Kapan, Siapa, Mengapa, Bagaimana).',
                    'assignment' => 'Bacalah berita di koran hari ini dan tentukan unsur 5W+1H nya.',
                    'quiz' => [
                        ['q' => 'Unsur ADIKSIMBA dalam berita diawali dengan kata tanya...', 'options' => ['Bagaimana', 'Mengapa', 'Apa', 'Kapan'], 'correct' => 2]
                    ]
                ]
            ],
            'Matematika' => [
                [
                    'title' => 'Bilangan Bulat dan Operasi Hitung',
                    'desc' => 'Memahami bilangan positif, negatif, dan nol serta sifat operasinya.',
                    'material' => 'Bilangan bulat terdiri dari bilangan bulat positif, nol, dan bilangan bulat negatif. Sifat perkalian: (+) x (-) = (-).',
                    'assignment' => 'Selesaikan soal berikut: (-15) + 20 x (-2) = ...',
                    'quiz' => [
                        ['q' => 'Hasil dari -10 + 25 adalah...', 'options' => ['-15', '15', '35', '-35'], 'correct' => 1]
                    ]
                ],
                [
                    'title' => 'Pengenalan Aljabar',
                    'desc' => 'Mengenal variabel, suku, dan konstanta dalam matematika.',
                    'material' => 'Bentuk aljabar adalah teknik menyajikan masalah matematika dengan simbol atau huruf sebagai peubah (variabel).',
                    'assignment' => 'Sederhanakan bentuk aljabar 3x + 5y - 2x + y.',
                    'quiz' => [
                        ['q' => 'Dalam bentuk 5x + 3, angka 3 disebut...', 'options' => ['Variabel', 'Koefisien', 'Konstanta', 'Suku'], 'correct' => 2]
                    ]
                ]
            ],
            'IPA' => [
                [
                    'title' => 'Hakikat Ilmu Sains dan Metode Ilmiah',
                    'desc' => 'Mempelajari bagaimana ilmuwan bekerja dan mengenal alat laboratorium.',
                    'material' => 'Sains adalah ilmu pengetahuan sistematis tentang alam dan dunia fisik. Langkah metode ilmiah: Observasi, Hipotesis, Eksperimen, Kesimpulan.',
                    'assignment' => 'Sebutkan 5 alat laboratorium beserta fungsinya dalam bentuk tabel.',
                    'quiz' => [
                        ['q' => 'Langkah pertama dalam metode ilmiah adalah...', 'options' => ['Eksperimen', 'Hipotesis', 'Observasi', 'Kesimpulan'], 'correct' => 2]
                    ]
                ],
                [
                    'title' => 'Sel: Unit Terkecil Kehidupan',
                    'desc' => 'Mengenal bagian-bagian sel hewan dan tumbuhan serta fungsinya.',
                    'material' => 'Sel adalah unit struktural dan fungsional terkecil dari makhluk hidup. Organel sel seperti Nukleus berfungsi sebagai pusat kendali.',
                    'assignment' => 'Gambarlah struktur sel hewan dan beri keterangan bagian-bagiannya.',
                    'quiz' => [
                        ['q' => 'Pusat kendali seluruh kegiatan sel berada di...', 'options' => ['Mitokondria', 'Nukleus', 'Sitoplasma', 'Ribosom'], 'correct' => 1]
                    ]
                ]
            ],
            'English' => [
                [
                    'title' => 'Greeting and Leave Takings',
                    'desc' => 'How to greet people in formal and informal situations.',
                    'material' => 'Common greetings: Hello, Good Morning, Good Afternoon. Leave takings: Goodbye, See you later, Good night.',
                    'assignment' => 'Write a short dialogue about two students meeting in the morning.',
                    'quiz' => [
                        ['q' => 'What is the response for "How are you?"', 'options' => ['I am fine, thank you', 'Goodbye', 'Nice to meet you', 'Good Morning'], 'correct' => 0]
                    ]
                ],
                [
                    'title' => 'This is Me: Self Introduction',
                    'desc' => 'Introducing yourself and your family to others.',
                    'material' => 'Using pronouns (I, You, My) to describe personal identity like name, age, address, and hobbies.',
                    'assignment' => 'Record a short video introducing yourself in English (max 1 minute).',
                    'quiz' => [
                        ['q' => 'To introduce yourself, you can say...', 'options' => ['He is my friend', 'Let me introduce myself', 'How do you do', 'I am sorry'], 'correct' => 1]
                    ]
                ]
            ]
        ];

        // Generic template for other subjects
        $genericModules = [
            [
                'title' => 'Pendahuluan Materi Pembelajaran',
                'desc' => 'Pengenalan kompetensi dasar dan tujuan pembelajaran semester ini.',
                'material' => 'Selamat datang di kursus ini. Kita akan mempelajari dasar-dasar mata pelajaran ini sesuai Kurikulum Merdeka.',
                'assignment' => 'Tuliskan harapanmu mengikuti pelajaran ini dalam 3 kalimat.',
                'quiz' => [['q' => 'Apakah anda siap belajar?', 'options' => ['Siap', 'Sangat Siap', 'Ragu', 'Tidak'], 'correct' => 1]]
            ],
            [
                'title' => 'Penerapan Konsep Dasar dalam Keseharian',
                'desc' => 'Melihat keterkaitan ilmu dengan kehidupan sehari-hari di lingkungan sekolah.',
                'material' => 'Ilmu yang kita pelajari sangat bermanfaat bagi kemajuan teknologi dan tatanan sosial masyarakat.',
                'assignment' => 'Berikan satu contoh penerapan materi ini di rumah anda.',
                'quiz' => [['q' => 'Ilmu pengetahuan sangat penting bagi...', 'options' => ['Masa Depan', 'Hiburan', 'Traveling', 'Tidur'], 'correct' => 0]]
            ]
        ];

        $assignments = TeachingAssignment::with(['teacher', 'subject', 'classroom'])
            ->whereHas('teacher', function($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            })->get();

        $count = 0;
        foreach ($assignments as $ta) {
            // 1. Create Course
            $course = LmsCourse::updateOrCreate(
                [
                    'subject_id' => $ta->subject_id,
                    'classroom_id' => $ta->classroom_id,
                    'teacher_id' => $ta->teacher_id,
                ],
                [
                    'school_id' => $schoolId,
                    'semester_id' => $activeSemester->id,
                    'code' => strtoupper(Str::slug($ta->subject->subject_name)) . '-' . $ta->classroom_id,
                    'course_name' => $ta->subject->subject_name . " (" . $ta->classroom->class_name . ")",
                    'description' => "Kursus interaktif Kurikulum Merdeka untuk mata pelajaran " . $ta->subject->subject_name . " di kelas " . $ta->classroom->class_name,
                    'is_published' => true,
                    'is_active' => true,
                    'status' => 'active'
                ]
            );

            // 2. Also ensure an LmsClass exists (mapping to real classroom)
            LmsClass::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'classroom_id' => $ta->classroom_id,
                ],
                [
                    'school_id' => $schoolId,
                    'status' => 'active',
                    'start_date' => now(),
                ]
            );

            // 3. Determine module templates
            $templates = $subjectTemplates[$ta->subject->subject_name] ?? $genericModules;

            foreach ($templates as $idx => $tmp) {
                // 4. Create Module
                $module = LmsModule::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $tmp['title'],
                    ],
                    [
                        'description' => $tmp['desc'],
                        'sequence' => $idx + 1,
                        'is_active' => true,
                    ]
                );

                // 5. Create Material
                LmsMaterial::updateOrCreate(
                    [
                        'module_id' => $module->id,
                        'course_id' => $course->id,
                        'title' => 'Materi: ' . $tmp['title'],
                    ],
                    [
                        'content' => $tmp['material'],
                        'material_type' => 'text',
                        'is_published' => true,
                        'order_number' => 1
                    ]
                );

                // 6. Create Assignment
                LmsAssignment::updateOrCreate(
                    [
                        'module_id' => $module->id,
                        'course_id' => $course->id,
                        'title' => 'Tugas: ' . $tmp['title'],
                    ],
                    [
                        'description' => $tmp['assignment'],
                        'assignment_type' => 'text',
                        'deadline' => now()->addDays(7),
                        'max_score' => 100,
                        'is_published' => true,
                    ]
                );

                // 7. Create Quiz (if data exists)
                if (isset($tmp['quiz'])) {
                    $quiz = LmsQuiz::updateOrCreate(
                        [
                            'module_id' => $module->id,
                            'course_id' => $course->id,
                        ],
                        [
                            'title' => 'Kuis: ' . $tmp['title'],
                            'description' => 'Kerjakan kuis ini untuk menguji pemahaman anda.',
                            'time_limit' => 30,
                            'is_published' => true,
                        ]
                    );

                    foreach ($tmp['quiz'] as $qIdx => $qData) {
                        LmsQuizQuestion::updateOrCreate(
                            [
                                'quiz_id' => $quiz->id,
                                'question' => $qData['q'],
                            ],
                            [
                                'question_type' => 'multiple_choice',
                                'options' => $qData['options'],
                                'correct_answer' => $qData['correct'],
                                'score' => 10,
                                'order_number' => $qIdx + 1
                            ]
                        );
                    }
                }
            }
            $count++;
        }
        echo "Successfully populated {$count} courses with 2 modules each for SMPS Pembda 2 Gunungsitoli.\n";
    }
}
