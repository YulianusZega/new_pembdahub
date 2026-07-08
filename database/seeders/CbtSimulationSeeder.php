<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Seeder untuk soal simulasi CBT kelas X TSM 1 (SMK, school_id=3)
 * 8 mata pelajaran × 5 soal = 40 soal
 */
class CbtSimulationSeeder extends Seeder
{
    public function run(): void
    {
        $schoolId       = 3;
        $classroomId    = 170; // X TSM 1
        $academicYearId = 1;
        $semesterId     = 1;
        $teacherId      = 185;
        $createdBy      = 4; // Admin SMK Pembda
        $now            = Carbon::now();

        // Start time 1 hour from now, end time 7 days later
        $startTime = $now->copy()->addHour();
        $endTime   = $now->copy()->addDays(7);

        $subjectsQuestions = $this->getSubjectsQuestions();

        foreach ($subjectsQuestions as $subjectData) {
            $subjectId   = $subjectData['subject_id'];
            $subjectName = $subjectData['subject_name'];
            $questions   = $subjectData['questions'];

            // 1. Create Question Bank
            $bankId = DB::table('cbt_question_banks')->insertGetId([
                'school_id'        => $schoolId,
                'subject_id'       => $subjectId,
                'teacher_id'       => $teacherId,
                'academic_year_id' => $academicYearId,
                'bank_name'        => "Bank Soal Simulasi {$subjectName} Kelas X",
                'description'      => "Soal simulasi untuk latihan CBT mata pelajaran {$subjectName} kelas X TSM.",
                'grade_level'      => '10',
                'total_questions'   => count($questions),
                'is_active'        => true,
                'is_shared'        => false,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);

            // 2. Create Questions & Options
            $questionIds = [];
            foreach ($questions as $qIdx => $q) {
                $questionId = DB::table('cbt_questions')->insertGetId([
                    'question_bank_id' => $bankId,
                    'question_type'    => 'multiple_choice',
                    'question_text'    => $q['text'],
                    'explanation'      => $q['explanation'] ?? null,
                    'points'           => 1,
                    'difficulty'       => $q['difficulty'] ?? 'sedang',
                    'topic'            => $q['topic'] ?? null,
                    'is_active'        => true,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
                $questionIds[] = $questionId;

                // Insert options
                foreach ($q['options'] as $oIdx => $opt) {
                    DB::table('cbt_question_options')->insert([
                        'question_id'  => $questionId,
                        'option_label' => chr(65 + $oIdx), // A, B, C, D
                        'option_text'  => $opt['text'],
                        'is_correct'   => $opt['correct'] ?? false,
                        'sort_order'   => $oIdx,
                    ]);
                }
            }

            // 3. Create Exam
            $examId = DB::table('cbt_exams')->insertGetId([
                'school_id'            => $schoolId,
                'subject_id'           => $subjectId,
                'teacher_id'           => $teacherId,
                'academic_year_id'     => $academicYearId,
                'semester_id'          => $semesterId,
                'exam_title'           => "Simulasi CBT {$subjectName}",
                'exam_description'     => "Ujian simulasi CBT untuk latihan siswa kelas X TSM 1 mata pelajaran {$subjectName}.",
                'exam_type'            => 'quiz',
                'exam_scope'           => 'class',
                'status'               => 'published',
                'start_time'           => $startTime,
                'end_time'             => $endTime,
                'duration_minutes'     => 30,
                'total_questions_shown' => count($questions),
                'randomize_questions'  => true,
                'randomize_options'    => true,
                'show_result'          => true,
                'show_answer_key'      => true,
                'allow_review'         => true,
                'passing_score'        => 60,
                'max_attempts'         => 3,
                'prevent_tab_switch'   => false,
                'prevent_copy_paste'   => false,
                'auto_sync_grade'      => false,
                'created_by'           => $createdBy,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);

            // 4. Link exam to question bank
            DB::table('cbt_exam_question_bank')->insert([
                'exam_id'           => $examId,
                'question_bank_id'  => $bankId,
                'questions_to_pick' => count($questions),
            ]);

            // 5. Link exam to classroom (X TSM 1)
            DB::table('cbt_exam_participants')->insert([
                'exam_id'      => $examId,
                'classroom_id' => $classroomId,
            ]);

            // 6. Link all questions to the exam
            foreach ($questionIds as $sortIdx => $qId) {
                DB::table('cbt_exam_questions')->insert([
                    'exam_id'     => $examId,
                    'question_id' => $qId,
                    'sort_order'  => $sortIdx + 1,
                ]);
            }

            $this->command->info("✓ {$subjectName}: {$bankId} bank, {$examId} exam, " . count($questions) . " soal");
        }

        $this->command->info("\n=== Selesai! " . count($subjectsQuestions) . " mata pelajaran, total " .
            array_sum(array_map(fn($s) => count($s['questions']), $subjectsQuestions)) . " soal ===");
    }

    private function getSubjectsQuestions(): array
    {
        return [
            // ===== 1. MATEMATIKA (id=207) =====
            [
                'subject_id'   => 207,
                'subject_name' => 'Matematika',
                'questions'    => [
                    [
                        'text'        => 'Hasil dari 3x² + 5x - 2 jika x = 2 adalah …',
                        'topic'       => 'Aljabar',
                        'difficulty'  => 'mudah',
                        'explanation' => '3(2²) + 5(2) - 2 = 12 + 10 - 2 = 20',
                        'options'     => [
                            ['text' => '18', 'correct' => false],
                            ['text' => '20', 'correct' => true],
                            ['text' => '22', 'correct' => false],
                            ['text' => '24', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Jika f(x) = 2x + 3 dan g(x) = x² - 1, maka (f ∘ g)(2) = …',
                        'topic'       => 'Fungsi Komposisi',
                        'difficulty'  => 'sedang',
                        'explanation' => 'g(2) = 4-1 = 3, f(3) = 2(3)+3 = 9',
                        'options'     => [
                            ['text' => '7', 'correct' => false],
                            ['text' => '9', 'correct' => true],
                            ['text' => '11', 'correct' => false],
                            ['text' => '13', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Himpunan penyelesaian dari pertidaksamaan 2x - 5 > 3 adalah …',
                        'topic'       => 'Pertidaksamaan Linear',
                        'difficulty'  => 'mudah',
                        'explanation' => '2x > 8, x > 4',
                        'options'     => [
                            ['text' => 'x > 4', 'correct' => true],
                            ['text' => 'x > 3', 'correct' => false],
                            ['text' => 'x < 4', 'correct' => false],
                            ['text' => 'x < 3', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Diketahui barisan aritmetika: 5, 9, 13, 17, … Suku ke-20 adalah …',
                        'topic'       => 'Barisan dan Deret',
                        'difficulty'  => 'sedang',
                        'explanation' => 'a=5, b=4, U20 = 5 + (20-1)×4 = 5 + 76 = 81',
                        'options'     => [
                            ['text' => '77', 'correct' => false],
                            ['text' => '79', 'correct' => false],
                            ['text' => '81', 'correct' => true],
                            ['text' => '85', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Nilai dari log₂ 32 adalah …',
                        'topic'       => 'Logaritma',
                        'difficulty'  => 'mudah',
                        'explanation' => '2⁵ = 32, jadi log₂ 32 = 5',
                        'options'     => [
                            ['text' => '4', 'correct' => false],
                            ['text' => '5', 'correct' => true],
                            ['text' => '6', 'correct' => false],
                            ['text' => '8', 'correct' => false],
                        ],
                    ],
                ],
            ],

            // ===== 2. BAHASA INDONESIA (id=208) =====
            [
                'subject_id'   => 208,
                'subject_name' => 'Bahasa Indonesia',
                'questions'    => [
                    [
                        'text'        => 'Teks yang berisi langkah-langkah atau tahapan untuk melakukan sesuatu disebut teks …',
                        'topic'       => 'Jenis Teks',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Teks prosedur berisi langkah-langkah untuk melakukan sesuatu.',
                        'options'     => [
                            ['text' => 'Deskripsi', 'correct' => false],
                            ['text' => 'Prosedur', 'correct' => true],
                            ['text' => 'Eksposisi', 'correct' => false],
                            ['text' => 'Narasi', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Berikut ini yang termasuk kata baku adalah …',
                        'topic'       => 'Ejaan dan Tata Bahasa',
                        'difficulty'  => 'mudah',
                        'explanation' => '"Analisis" adalah kata baku, sedangkan "analisa" adalah tidak baku.',
                        'options'     => [
                            ['text' => 'Analisa', 'correct' => false],
                            ['text' => 'Analisis', 'correct' => true],
                            ['text' => 'Apotik', 'correct' => false],
                            ['text' => 'Nasehat', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Unsur intrinsik dalam cerita yang menggambarkan tempat, waktu, dan suasana disebut …',
                        'topic'       => 'Unsur Intrinsik',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Latar (setting) meliputi tempat, waktu, dan suasana.',
                        'options'     => [
                            ['text' => 'Alur', 'correct' => false],
                            ['text' => 'Tema', 'correct' => false],
                            ['text' => 'Latar', 'correct' => true],
                            ['text' => 'Amanat', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Kalimat efektif harus memenuhi syarat berikut, KECUALI …',
                        'topic'       => 'Kalimat Efektif',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Kalimat efektif harus logis, hemat, padu, dan sejajar. "Panjang" bukan syarat.',
                        'options'     => [
                            ['text' => 'Logis', 'correct' => false],
                            ['text' => 'Hemat', 'correct' => false],
                            ['text' => 'Panjang', 'correct' => true],
                            ['text' => 'Padu', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Teks yang bertujuan untuk meyakinkan pembaca agar mengikuti pandangan penulis disebut …',
                        'topic'       => 'Jenis Teks',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Teks persuasi bertujuan membujuk/meyakinkan pembaca.',
                        'options'     => [
                            ['text' => 'Argumentasi', 'correct' => false],
                            ['text' => 'Persuasi', 'correct' => true],
                            ['text' => 'Eksposisi', 'correct' => false],
                            ['text' => 'Deskripsi', 'correct' => false],
                        ],
                    ],
                ],
            ],

            // ===== 3. PENDIDIKAN PANCASILA (id=209) =====
            [
                'subject_id'   => 209,
                'subject_name' => 'Pendidikan Pancasila',
                'questions'    => [
                    [
                        'text'        => 'Sila pertama Pancasila berbunyi …',
                        'topic'       => 'Pancasila',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Sila 1: Ketuhanan Yang Maha Esa.',
                        'options'     => [
                            ['text' => 'Kemanusiaan yang adil dan beradab', 'correct' => false],
                            ['text' => 'Ketuhanan Yang Maha Esa', 'correct' => true],
                            ['text' => 'Persatuan Indonesia', 'correct' => false],
                            ['text' => 'Keadilan sosial bagi seluruh rakyat Indonesia', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Lembaga negara yang bertugas mengawasi pengelolaan keuangan negara adalah …',
                        'topic'       => 'Lembaga Negara',
                        'difficulty'  => 'sedang',
                        'explanation' => 'BPK (Badan Pemeriksa Keuangan) bertugas memeriksa pengelolaan keuangan negara.',
                        'options'     => [
                            ['text' => 'DPR', 'correct' => false],
                            ['text' => 'MPR', 'correct' => false],
                            ['text' => 'BPK', 'correct' => true],
                            ['text' => 'MA', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Bentuk negara Indonesia menurut UUD 1945 adalah …',
                        'topic'       => 'Konstitusi',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Pasal 1 ayat 1 UUD 1945: Negara Indonesia ialah Negara Kesatuan yang berbentuk Republik.',
                        'options'     => [
                            ['text' => 'Monarki', 'correct' => false],
                            ['text' => 'Federal', 'correct' => false],
                            ['text' => 'Kesatuan Republik', 'correct' => true],
                            ['text' => 'Serikat', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Hak dan kewajiban warga negara diatur dalam UUD 1945 pasal …',
                        'topic'       => 'Hak dan Kewajiban',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Pasal 27-34 UUD 1945 mengatur hak dan kewajiban warga negara. Pasal 27 paling utama.',
                        'options'     => [
                            ['text' => 'Pasal 1-5', 'correct' => false],
                            ['text' => 'Pasal 18-22', 'correct' => false],
                            ['text' => 'Pasal 27-34', 'correct' => true],
                            ['text' => 'Pasal 35-37', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Nilai Pancasila yang mencerminkan gotong royong terdapat dalam sila ke- …',
                        'topic'       => 'Pancasila',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Sila ke-3 (Persatuan Indonesia) dan sila ke-5 (Keadilan Sosial) mencerminkan gotong royong, namun secara filosofis, gotong royong paling erat dengan sila ke-3.',
                        'options'     => [
                            ['text' => '1', 'correct' => false],
                            ['text' => '2', 'correct' => false],
                            ['text' => '3', 'correct' => true],
                            ['text' => '4', 'correct' => false],
                        ],
                    ],
                ],
            ],

            // ===== 4. PENDIDIKAN AGAMA ISLAM (id=210) =====
            [
                'subject_id'   => 210,
                'subject_name' => 'Pendidikan Agama Islam',
                'questions'    => [
                    [
                        'text'        => 'Rukun Islam yang kelima adalah …',
                        'topic'       => 'Aqidah',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Rukun Islam ke-5 adalah menunaikan ibadah haji bagi yang mampu.',
                        'options'     => [
                            ['text' => 'Puasa Ramadhan', 'correct' => false],
                            ['text' => 'Membayar zakat', 'correct' => false],
                            ['text' => 'Melaksanakan shalat', 'correct' => false],
                            ['text' => 'Menunaikan haji', 'correct' => true],
                        ],
                    ],
                    [
                        'text'        => 'Surat Al-Fatihah terdiri dari … ayat.',
                        'topic'       => 'Al-Quran',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Surat Al-Fatihah terdiri dari 7 ayat.',
                        'options'     => [
                            ['text' => '5', 'correct' => false],
                            ['text' => '6', 'correct' => false],
                            ['text' => '7', 'correct' => true],
                            ['text' => '8', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Hukum bacaan tajwid ketika nun mati atau tanwin bertemu huruf ب disebut …',
                        'topic'       => 'Tajwid',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Nun mati/tanwin bertemu huruf Ba (ب) hukumnya Iqlab.',
                        'options'     => [
                            ['text' => 'Idzhar', 'correct' => false],
                            ['text' => 'Idgham', 'correct' => false],
                            ['text' => 'Iqlab', 'correct' => true],
                            ['text' => 'Ikhfa', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Asmaul Husna "Ar-Rahman" artinya …',
                        'topic'       => 'Aqidah',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Ar-Rahman berarti Yang Maha Pengasih.',
                        'options'     => [
                            ['text' => 'Yang Maha Penyayang', 'correct' => false],
                            ['text' => 'Yang Maha Pengasih', 'correct' => true],
                            ['text' => 'Yang Maha Kuasa', 'correct' => false],
                            ['text' => 'Yang Maha Mengetahui', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Shalat wajib yang dikerjakan menjelang tengah malam adalah shalat …',
                        'topic'       => 'Ibadah',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Shalat Isya dikerjakan setelah hilangnya mega merah hingga menjelang tengah malam.',
                        'options'     => [
                            ['text' => 'Maghrib', 'correct' => false],
                            ['text' => 'Isya', 'correct' => true],
                            ['text' => 'Subuh', 'correct' => false],
                            ['text' => 'Dzuhur', 'correct' => false],
                        ],
                    ],
                ],
            ],

            // ===== 5. INFORMATIKA (id=215) =====
            [
                'subject_id'   => 215,
                'subject_name' => 'Informatika',
                'questions'    => [
                    [
                        'text'        => 'Satuan terkecil data dalam komputer yang bernilai 0 atau 1 disebut …',
                        'topic'       => 'Sistem Komputer',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Bit (binary digit) adalah satuan terkecil data.',
                        'options'     => [
                            ['text' => 'Byte', 'correct' => false],
                            ['text' => 'Bit', 'correct' => true],
                            ['text' => 'Pixel', 'correct' => false],
                            ['text' => 'Hertz', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Perangkat lunak yang berfungsi mengelola seluruh sumber daya komputer disebut …',
                        'topic'       => 'Perangkat Lunak',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Sistem operasi mengelola seluruh sumber daya komputer.',
                        'options'     => [
                            ['text' => 'Aplikasi', 'correct' => false],
                            ['text' => 'Driver', 'correct' => false],
                            ['text' => 'Sistem Operasi', 'correct' => true],
                            ['text' => 'Compiler', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Bahasa pemrograman yang langsung dapat dimengerti oleh komputer tanpa proses kompilasi disebut …',
                        'topic'       => 'Pemrograman',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Bahasa mesin (machine language) langsung dipahami komputer.',
                        'options'     => [
                            ['text' => 'Bahasa tingkat tinggi', 'correct' => false],
                            ['text' => 'Bahasa assembly', 'correct' => false],
                            ['text' => 'Bahasa mesin', 'correct' => true],
                            ['text' => 'Bahasa script', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Algoritma yang mengurutkan data dengan membandingkan elemen berdekatan dan menukarnya disebut …',
                        'topic'       => 'Algoritma',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Bubble Sort bekerja dengan membandingkan dan menukar elemen berdekatan.',
                        'options'     => [
                            ['text' => 'Selection Sort', 'correct' => false],
                            ['text' => 'Bubble Sort', 'correct' => true],
                            ['text' => 'Quick Sort', 'correct' => false],
                            ['text' => 'Merge Sort', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Konversi bilangan biner 1010 ke desimal adalah …',
                        'topic'       => 'Sistem Bilangan',
                        'difficulty'  => 'mudah',
                        'explanation' => '1×2³ + 0×2² + 1×2¹ + 0×2⁰ = 8 + 0 + 2 + 0 = 10',
                        'options'     => [
                            ['text' => '8', 'correct' => false],
                            ['text' => '10', 'correct' => true],
                            ['text' => '12', 'correct' => false],
                            ['text' => '14', 'correct' => false],
                        ],
                    ],
                ],
            ],

            // ===== 6. DASAR-DASAR PROGRAM KEAHLIAN (id=218) =====
            [
                'subject_id'   => 218,
                'subject_name' => 'Dasar-Dasar Program Keahlian',
                'questions'    => [
                    [
                        'text'        => 'K3 dalam dunia kerja bengkel merupakan singkatan dari …',
                        'topic'       => 'K3',
                        'difficulty'  => 'mudah',
                        'explanation' => 'K3 = Keselamatan dan Kesehatan Kerja.',
                        'options'     => [
                            ['text' => 'Keamanan dan Keterampilan Kerja', 'correct' => false],
                            ['text' => 'Keselamatan dan Kesehatan Kerja', 'correct' => true],
                            ['text' => 'Keselamatan dan Kebersihan Kerja', 'correct' => false],
                            ['text' => 'Kerapian dan Kedisiplinan Kerja', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Alat pelindung diri (APD) yang digunakan untuk melindungi tangan saat bekerja di bengkel adalah …',
                        'topic'       => 'K3',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Sarung tangan (gloves) melindungi tangan dari benda tajam, panas, dan bahan kimia.',
                        'options'     => [
                            ['text' => 'Helm', 'correct' => false],
                            ['text' => 'Kacamata safety', 'correct' => false],
                            ['text' => 'Sarung tangan', 'correct' => true],
                            ['text' => 'Sepatu safety', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Alat ukur yang digunakan untuk mengukur tegangan listrik adalah …',
                        'topic'       => 'Alat Ukur',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Voltmeter digunakan untuk mengukur tegangan/beda potensial listrik.',
                        'options'     => [
                            ['text' => 'Amperemeter', 'correct' => false],
                            ['text' => 'Ohmmeter', 'correct' => false],
                            ['text' => 'Voltmeter', 'correct' => true],
                            ['text' => 'Wattmeter', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Dalam gambar teknik, garis tebal kontinu digunakan untuk menggambar …',
                        'topic'       => 'Gambar Teknik',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Garis tebal kontinu digunakan untuk garis tepi / garis benda yang terlihat.',
                        'options'     => [
                            ['text' => 'Garis sumbu', 'correct' => false],
                            ['text' => 'Garis tepi benda', 'correct' => true],
                            ['text' => 'Garis ukuran', 'correct' => false],
                            ['text' => 'Garis arsiran', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Jenis logam yang paling banyak digunakan sebagai bahan dasar rangka sepeda motor adalah …',
                        'topic'       => 'Material Teknik',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Baja (campuran besi dan karbon) paling umum digunakan untuk rangka motor.',
                        'options'     => [
                            ['text' => 'Aluminium', 'correct' => false],
                            ['text' => 'Baja', 'correct' => true],
                            ['text' => 'Tembaga', 'correct' => false],
                            ['text' => 'Kuningan', 'correct' => false],
                        ],
                    ],
                ],
            ],

            // ===== 7. KONSENTRASI KEAHLIAN TSM (id=221) =====
            [
                'subject_id'   => 221,
                'subject_name' => 'Konsentrasi Keahlian TSM',
                'questions'    => [
                    [
                        'text'        => 'Komponen mesin sepeda motor yang berfungsi mengubah energi panas menjadi energi gerak adalah …',
                        'topic'       => 'Mesin Motor',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Piston bergerak naik-turun akibat pembakaran, mengubah energi panas menjadi gerak.',
                        'options'     => [
                            ['text' => 'Karburator', 'correct' => false],
                            ['text' => 'Busi', 'correct' => false],
                            ['text' => 'Piston', 'correct' => true],
                            ['text' => 'Klep', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Sistem pengapian pada sepeda motor yang menggunakan CDI bekerja berdasarkan prinsip …',
                        'topic'       => 'Kelistrikan Motor',
                        'difficulty'  => 'sedang',
                        'explanation' => 'CDI (Capacitor Discharge Ignition) bekerja berdasarkan prinsip pengosongan muatan kapasitor.',
                        'options'     => [
                            ['text' => 'Induksi elektromagnetik', 'correct' => false],
                            ['text' => 'Pengosongan muatan kapasitor', 'correct' => true],
                            ['text' => 'Resistansi listrik', 'correct' => false],
                            ['text' => 'Arus bolak-balik', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Langkah kerja mesin 4-tak secara berurutan adalah …',
                        'topic'       => 'Mesin Motor',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Siklus 4-tak: hisap → kompresi → usaha → buang.',
                        'options'     => [
                            ['text' => 'Hisap - Usaha - Kompresi - Buang', 'correct' => false],
                            ['text' => 'Hisap - Kompresi - Usaha - Buang', 'correct' => true],
                            ['text' => 'Kompresi - Hisap - Buang - Usaha', 'correct' => false],
                            ['text' => 'Buang - Hisap - Kompresi - Usaha', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Fungsi kopling pada sepeda motor adalah …',
                        'topic'       => 'Sistem Transmisi',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Kopling memutus dan menghubungkan tenaga dari mesin ke transmisi.',
                        'options'     => [
                            ['text' => 'Meredam getaran mesin', 'correct' => false],
                            ['text' => 'Mendinginkan mesin', 'correct' => false],
                            ['text' => 'Memutus dan menghubungkan tenaga mesin ke transmisi', 'correct' => true],
                            ['text' => 'Mengatur campuran bahan bakar', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Oli mesin harus diganti secara berkala karena …',
                        'topic'       => 'Perawatan Motor',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Kualitas oli menurun karena kontaminasi dan panas, sehingga fungsi pelumasannya berkurang.',
                        'options'     => [
                            ['text' => 'Agar warna oli tetap bening', 'correct' => false],
                            ['text' => 'Kualitas pelumasan menurun seiring pemakaian', 'correct' => true],
                            ['text' => 'Supaya mesin bertambah kencang', 'correct' => false],
                            ['text' => 'Untuk menambah bahan bakar', 'correct' => false],
                        ],
                    ],
                ],
            ],

            // ===== 8. PJOK (id=82) =====
            [
                'subject_id'   => 82,
                'subject_name' => 'PJOK',
                'questions'    => [
                    [
                        'text'        => 'Dalam permainan bola voli, jumlah pemain dalam satu tim di lapangan adalah …',
                        'topic'       => 'Permainan Bola Besar',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Bola voli dimainkan oleh 6 pemain per tim di lapangan.',
                        'options'     => [
                            ['text' => '5 orang', 'correct' => false],
                            ['text' => '6 orang', 'correct' => true],
                            ['text' => '7 orang', 'correct' => false],
                            ['text' => '11 orang', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Teknik dasar dalam permainan sepak bola yang digunakan untuk mengoper bola jarak pendek menggunakan kaki bagian dalam disebut …',
                        'topic'       => 'Permainan Bola Besar',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Short pass menggunakan kaki bagian dalam untuk operan jarak pendek.',
                        'options'     => [
                            ['text' => 'Shooting', 'correct' => false],
                            ['text' => 'Heading', 'correct' => false],
                            ['text' => 'Short pass', 'correct' => true],
                            ['text' => 'Dribbling', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Komponen kebugaran jasmani yang berkaitan dengan kemampuan otot melawan beban disebut …',
                        'topic'       => 'Kebugaran Jasmani',
                        'difficulty'  => 'sedang',
                        'explanation' => 'Kekuatan (strength) adalah kemampuan otot melawan beban/tahanan.',
                        'options'     => [
                            ['text' => 'Kelincahan', 'correct' => false],
                            ['text' => 'Kekuatan', 'correct' => true],
                            ['text' => 'Kelenturan', 'correct' => false],
                            ['text' => 'Kecepatan', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Lari jarak pendek (sprint) 100 meter memerlukan komponen kebugaran utama berupa …',
                        'topic'       => 'Atletik',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Sprint 100m membutuhkan kecepatan (speed) sebagai komponen utama.',
                        'options'     => [
                            ['text' => 'Daya tahan', 'correct' => false],
                            ['text' => 'Kelenturan', 'correct' => false],
                            ['text' => 'Kecepatan', 'correct' => true],
                            ['text' => 'Koordinasi', 'correct' => false],
                        ],
                    ],
                    [
                        'text'        => 'Gerakan senam lantai yang dilakukan dengan menggulingkan badan ke depan disebut …',
                        'topic'       => 'Senam',
                        'difficulty'  => 'mudah',
                        'explanation' => 'Forward roll (guling depan) adalah gerakan menggulingkan badan ke depan.',
                        'options'     => [
                            ['text' => 'Kayang', 'correct' => false],
                            ['text' => 'Guling depan', 'correct' => true],
                            ['text' => 'Handstand', 'correct' => false],
                            ['text' => 'Meroda', 'correct' => false],
                        ],
                    ],
                ],
            ],
        ];
    }
}
