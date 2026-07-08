<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\LmsAssignment;
use App\Models\LmsClass;
use App\Models\LmsCourse;
use App\Models\LmsEnrollment;
use App\Models\LmsMaterial;
use App\Models\LmsModule;
use App\Models\LmsQuiz;
use App\Models\LmsQuizAnswer;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizQuestion;
use App\Models\LmsSubmission;
use App\Models\School;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentClass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LmsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('📚 Seeding LMS data...');

        $academicYear = AcademicYear::where('is_active', true)->first();
        $semester = Semester::where('is_active', true)->first();

        if (!$academicYear || !$semester) {
            $this->command->error('❌ No active academic year/semester found. Run main seeder first.');
            return;
        }

        $schools = School::all();
        $courseCount = 0;

        foreach ($schools as $school) {
            $teachers = Teacher::where('school_id', $school->id)->where('is_active', true)->get();
            $subjects = Subject::where('school_id', $school->id)->where('is_active', true)->get();
            $classrooms = Classroom::where('school_id', $school->id)
                ->where('academic_year_id', $academicYear->id)
                ->where('is_active', true)
                ->get();

            if ($teachers->isEmpty() || $subjects->isEmpty() || $classrooms->isEmpty()) {
                $this->command->warn("⚠️  Skipping {$school->name}: missing teachers/subjects/classrooms.");
                continue;
            }

            // ─── Determine courses based on school type ───
            $courseDefs = $this->getCourseDefinitions($school->type);

            foreach ($courseDefs as $courseDef) {
                // Find matching subject
                $subject = $subjects->first(fn($s) => Str::contains(
                    Str::lower($s->subject_name),
                    Str::lower($courseDef['subject_match'])
                ));
                if (!$subject) continue;

                // Assign a teacher (round-robin)
                $teacher = $teachers[$courseCount % $teachers->count()];

                // Pick 1-2 classrooms for grade level
                $eligibleClassrooms = $classrooms->filter(
                    fn($c) => $c->grade_level === $courseDef['grade']
                )->take(2);
                if ($eligibleClassrooms->isEmpty()) continue;

                // Create course
                $course = LmsCourse::create([
                    'school_id' => $school->id,
                    'teacher_id' => $teacher->id,
                    'subject_id' => $subject->id,
                    'semester_id' => $semester->id,
                    'code' => 'LMS-' . strtoupper(Str::random(8)),
                    'course_name' => $courseDef['name'],
                    'description' => $courseDef['description'],
                    'status' => 'active',
                    'is_published' => true,
                    'is_active' => true,
                ]);

                // Assign classrooms & enroll students
                foreach ($eligibleClassrooms as $classroom) {
                    $lmsClass = LmsClass::create([
                        'course_id' => $course->id,
                        'classroom_id' => $classroom->id,
                        'school_id' => $school->id,
                        'status' => 'active',
                    ]);

                    $studentIds = StudentClass::where('classroom_id', $classroom->id)
                        ->where('academic_year_id', $academicYear->id)
                        ->where('status', 'aktif')
                        ->pluck('student_id');

                    foreach ($studentIds as $studentId) {
                        LmsEnrollment::firstOrCreate(
                            ['lms_class_id' => $lmsClass->id, 'student_id' => $studentId],
                            ['status' => 'enrolled', 'enrolled_at' => now()]
                        );
                    }
                }

                // Seed modules, materials, assignments, quizzes
                $this->seedModules($course, $courseDef['modules']);
                $this->seedAssignments($course, $courseDef['assignments']);
                $this->seedQuiz($course, $courseDef['quiz']);

                // Create some sample student submissions & quiz attempts
                $this->seedStudentActivity($course);

                $courseCount++;
            }

            $this->command->info("  ✅ {$school->name}: created courses for LMS");
        }

        $this->command->info("📚 LMS seeding complete! {$courseCount} courses created.");
    }

    // ================================================================
    //  COURSE DEFINITIONS PER SCHOOL TYPE
    // ================================================================

    private function getCourseDefinitions(string $schoolType): array
    {
        return match ($schoolType) {
            'SMP' => $this->getSMPCourses(),
            'SMA' => $this->getSMACourses(),
            'SMK' => $this->getSMKCourses(),
            default => [],
        };
    }

    private function getSMPCourses(): array
    {
        return [
            // ─── Matematika Kelas VII ───
            [
                'name' => 'Matematika Kelas VII - Bilangan & Aljabar',
                'description' => 'Mempelajari bilangan bulat, pecahan, bentuk aljabar, dan persamaan linear satu variabel sesuai Kurikulum Merdeka.',
                'subject_match' => 'matematika',
                'grade' => 7,
                'modules' => [
                    [
                        'title' => 'Bab 1: Bilangan Bulat dan Pecahan',
                        'description' => 'Operasi hitung pada bilangan bulat dan pecahan',
                        'materials' => [
                            ['title' => 'Operasi Penjumlahan & Pengurangan Bilangan Bulat', 'type' => 'text', 'content' => "Bilangan bulat terdiri dari bilangan bulat positif, nol, dan bilangan bulat negatif.\n\nContoh:\n• 5 + (-3) = 2\n• -7 + 4 = -3\n• -2 - (-6) = -2 + 6 = 4\n\nSifat-sifat:\n1. Komutatif: a + b = b + a\n2. Asosiatif: (a + b) + c = a + (b + c)\n3. Identitas: a + 0 = a"],
                            ['title' => 'Operasi Perkalian & Pembagian Bilangan Bulat', 'type' => 'text', 'content' => "Aturan tanda:\n• (+) × (+) = (+)\n• (-) × (-) = (+)\n• (+) × (-) = (-)\n• (-) × (+) = (-)\n\nContoh:\n• 4 × (-3) = -12\n• (-5) × (-2) = 10\n• (-12) ÷ 3 = -4"],
                            ['title' => 'Video: Operasi Bilangan Bulat', 'type' => 'link', 'url' => 'https://www.youtube.com/watch?v=example_bilangan'],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Bentuk Aljabar',
                        'description' => 'Pengenalan variabel, koefisien, konstanta, dan operasi bentuk aljabar',
                        'materials' => [
                            ['title' => 'Pengertian Variabel, Koefisien, dan Konstanta', 'type' => 'text', 'content' => "Bentuk aljabar: 3x² + 5x - 7\n\nKomponen:\n• Variabel: x (huruf yang mewakili bilangan)\n• Koefisien: 3 dan 5 (bilangan di depan variabel)\n• Konstanta: -7 (bilangan tanpa variabel)\n• Suku: 3x², 5x, -7\n\nSuku sejenis: suku yang memiliki variabel dan pangkat sama.\nContoh: 2x dan 5x adalah suku sejenis"],
                            ['title' => 'Penjumlahan dan Pengurangan Bentuk Aljabar', 'type' => 'text', 'content' => "Langkah:\n1. Kelompokkan suku-suku sejenis\n2. Jumlahkan koefisien suku sejenis\n\nContoh:\n(3x + 5) + (2x - 3) = 3x + 2x + 5 - 3 = 5x + 2\n(4x² - 2x + 1) - (x² + 3x - 5) = 3x² - 5x + 6"],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Persamaan Linear Satu Variabel (PLSV)',
                        'description' => 'Menyelesaikan persamaan dan pertidaksamaan linear satu variabel',
                        'materials' => [
                            ['title' => 'Konsep PLSV', 'type' => 'text', 'content' => "Persamaan linear satu variabel (PLSV) adalah kalimat terbuka yang memiliki satu variabel berpangkat 1.\n\nBentuk umum: ax + b = c\n\nCara menyelesaikan:\n1. Pindah ruas (ubah tanda)\n2. Sederhanakan\n\nContoh:\n2x + 5 = 13\n2x = 13 - 5\n2x = 8\nx = 4"],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Latihan Soal Bilangan Bulat', 'description' => "Kerjakan soal-soal berikut:\n1. Hitung: (-15) + 8 - (-3)\n2. Hitung: (-4) × 6 ÷ (-2)\n3. Urutkan dari terkecil: -5, 3, -1, 0, -8, 2\n4. Suhu di puncak gunung -3°C, kemudian turun 5°C. Berapa suhu sekarang?\n5. Buat 3 soal cerita yang melibatkan bilangan negatif.", 'max_score' => 100, 'days_from_now' => 7],
                    ['title' => 'Tugas Bentuk Aljabar', 'description' => "Kerjakan soal berikut:\n1. Tentukan variabel, koefisien, dan konstanta dari: 4x² - 7x + 12\n2. Sederhanakan: (5a + 3b - 2) + (2a - b + 7)\n3. Sederhanakan: (6x² + 3x) - (2x² - x + 4)\n4. Jika x = 3, hitung nilai 2x² - 5x + 1\n5. Buat contoh bentuk aljabar dari situasi nyata.", 'max_score' => 100, 'days_from_now' => 14],
                ],
                'quiz' => [
                    'title' => 'Quiz: Bilangan Bulat & Aljabar',
                    'description' => 'Quiz untuk menguji pemahaman tentang bilangan bulat dan dasar aljabar.',
                    'time_limit' => 30,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'Hasil dari (-8) + 5 adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'-13'],['key'=>'B','text'=>'-3'],['key'=>'C','text'=>'3'],['key'=>'D','text'=>'13']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Hasil dari (-3) × (-4) adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'-12'],['key'=>'B','text'=>'-7'],['key'=>'C','text'=>'7'],['key'=>'D','text'=>'12']], 'answer' => 'D', 'score' => 10],
                        ['q' => 'Koefisien x pada bentuk aljabar 5x² - 3x + 7 adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'5'],['key'=>'B','text'=>'-3'],['key'=>'C','text'=>'7'],['key'=>'D','text'=>'2']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Hasil penjumlahan (2x + 3) + (4x - 1) = ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'6x + 2'],['key'=>'B','text'=>'6x + 4'],['key'=>'C','text'=>'6x - 2'],['key'=>'D','text'=>'8x + 2']], 'answer' => 'A', 'score' => 10],
                        ['q' => 'Bilangan bulat negatif selalu lebih kecil dari nol.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Hasil kali dua bilangan bulat negatif adalah bilangan negatif.', 'type' => 'true_false', 'answer' => 'false', 'score' => 10],
                        ['q' => 'Penyelesaian dari 3x - 6 = 9 adalah x = ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'3'],['key'=>'B','text'=>'5'],['key'=>'C','text'=>'1'],['key'=>'D','text'=>'15']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Suku konstanta pada 2a³ - 4a + 9 adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'2'],['key'=>'B','text'=>'-4'],['key'=>'C','text'=>'9'],['key'=>'D','text'=>'3']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Jelaskan perbedaan antara variabel dan konstanta dalam bentuk aljabar!', 'type' => 'essay', 'answer' => null, 'score' => 10],
                        ['q' => 'Sebutkan sifat komutatif pada operasi penjumlahan bilangan bulat!', 'type' => 'short_answer', 'answer' => null, 'score' => 10],
                    ],
                ],
            ],

            // ─── IPA Kelas VIII ───
            [
                'name' => 'IPA Kelas VIII - Gerak & Gaya',
                'description' => 'Mempelajari konsep gerak lurus, hukum Newton, dan penerapannya dalam kehidupan sehari-hari.',
                'subject_match' => 'ilmu pengetahuan alam',
                'grade' => 8,
                'modules' => [
                    [
                        'title' => 'Bab 1: Gerak Lurus',
                        'description' => 'Gerak lurus beraturan (GLB) dan gerak lurus berubah beraturan (GLBB)',
                        'materials' => [
                            ['title' => 'Pengertian Gerak dan Jarak vs Perpindahan', 'type' => 'text', 'content' => "Gerak adalah perubahan posisi benda terhadap titik acuan.\n\nJarak: panjang lintasan yang ditempuh (skalar, selalu positif)\nPerpindahan: perubahan posisi dari titik awal ke titik akhir (vektor, bisa negatif)\n\nContoh:\nAndi berjalan 3 m ke utara, lalu 4 m ke timur.\n• Jarak = 3 + 4 = 7 m\n• Perpindahan = √(3² + 4²) = 5 m"],
                            ['title' => 'Kecepatan dan Percepatan', 'type' => 'text', 'content' => "Kecepatan (v) = perpindahan / waktu = Δs / Δt\nSatuan: m/s\n\nPercepatan (a) = perubahan kecepatan / waktu = Δv / Δt\nSatuan: m/s²\n\nGLB: v tetap, a = 0, s = v × t\nGLBB: a tetap, v = v₀ + at, s = v₀t + ½at²"],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Hukum Newton',
                        'description' => 'Tiga hukum Newton tentang gerak',
                        'materials' => [
                            ['title' => 'Hukum I Newton (Inersia)', 'type' => 'text', 'content' => "Hukum I Newton: Setiap benda akan tetap diam atau bergerak lurus beraturan kecuali ada gaya yang bekerja padanya.\n\nContoh kehidupan sehari-hari:\n• Penumpang terdorong ke depan saat bus mengerem mendadak\n• Koin di atas kertas tetap diam saat kertas ditarik cepat\n• Penggunaan sabuk pengaman di mobil"],
                            ['title' => 'Hukum II Newton (F = ma)', 'type' => 'text', 'content' => "Hukum II Newton: Percepatan benda sebanding dengan gaya total dan berbanding terbalik dengan massanya.\n\nRumus: F = m × a\n\nDimana:\n• F = gaya (Newton)\n• m = massa (kg)\n• a = percepatan (m/s²)\n\nContoh:\nGaya 10 N bekerja pada benda 2 kg.\na = F/m = 10/2 = 5 m/s²"],
                            ['title' => 'Hukum III Newton (Aksi-Reaksi)', 'type' => 'text', 'content' => "Hukum III Newton: Setiap aksi akan menimbulkan reaksi yang sama besar dan berlawanan arah.\n\nContoh:\n• Roket: gas terdorong ke bawah, roket terdorong ke atas\n• Berenang: tangan mendorong air ke belakang, tubuh bergerak ke depan\n• Berjalan: kaki mendorong tanah ke belakang, tubuh bergerak ke depan"],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Praktikum: Mengukur Kecepatan Benda', 'description' => "Lakukan percobaan sederhana di rumah:\n1. Gunakan bola kecil dan bidang miring (papan/meja)\n2. Ukur jarak lintasan (cm) menggunakan penggaris\n3. Ukur waktu (detik) menggunakan stopwatch HP\n4. Lakukan 5 kali pengukuran\n5. Hitung kecepatan rata-rata\n6. Buat tabel dan grafik hasilnya\n7. Kesimpulan: apakah gerak bola termasuk GLB atau GLBB?", 'max_score' => 100, 'days_from_now' => 10],
                ],
                'quiz' => [
                    'title' => 'Quiz: Gerak Lurus & Hukum Newton',
                    'description' => 'Uji pemahaman tentang konsep gerak dan hukum Newton.',
                    'time_limit' => 25,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'Sebuah mobil bergerak dengan kecepatan tetap 60 km/jam selama 2 jam. Jarak yang ditempuh adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'30 km'],['key'=>'B','text'=>'60 km'],['key'=>'C','text'=>'120 km'],['key'=>'D','text'=>'180 km']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Gerak lurus beraturan (GLB) memiliki percepatan ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'positif'],['key'=>'B','text'=>'negatif'],['key'=>'C','text'=>'nol'],['key'=>'D','text'=>'berubah-ubah']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Hukum I Newton disebut juga hukum ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Aksi-Reaksi'],['key'=>'B','text'=>'Inersia'],['key'=>'C','text'=>'Gravitasi'],['key'=>'D','text'=>'Kekekalan Energi']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Gaya 20 N bekerja pada benda bermassa 4 kg. Percepatan benda adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'4 m/s²'],['key'=>'B','text'=>'5 m/s²'],['key'=>'C','text'=>'80 m/s²'],['key'=>'D','text'=>'0,2 m/s²']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Perpindahan dan jarak selalu memiliki nilai yang sama.', 'type' => 'true_false', 'answer' => 'false', 'score' => 10],
                        ['q' => 'Hukum III Newton menyatakan bahwa gaya aksi dan reaksi bekerja pada dua benda berbeda.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Satuan percepatan dalam SI adalah ...', 'type' => 'short_answer', 'answer' => null, 'score' => 10],
                        ['q' => 'Jelaskan perbedaan antara jarak dan perpindahan beserta contohnya!', 'type' => 'essay', 'answer' => null, 'score' => 20],
                    ],
                ],
            ],

            // ─── Bahasa Indonesia Kelas IX ───
            [
                'name' => 'Bahasa Indonesia Kelas IX - Teks Laporan & Pidato',
                'description' => 'Mempelajari cara menyusun teks laporan percobaan, teks pidato persuasif, dan cerita pendek.',
                'subject_match' => 'bahasa indonesia',
                'grade' => 9,
                'modules' => [
                    [
                        'title' => 'Bab 1: Teks Laporan Percobaan',
                        'description' => 'Struktur, ciri kebahasaan, dan cara menyusun teks laporan percobaan',
                        'materials' => [
                            ['title' => 'Struktur Teks Laporan Percobaan', 'type' => 'text', 'content' => "Struktur Teks Laporan Percobaan:\n\n1. Judul\n2. Tujuan percobaan\n3. Alat dan Bahan\n4. Langkah-langkah percobaan\n5. Hasil percobaan (data/tabel)\n6. Pembahasan\n7. Kesimpulan\n\nCiri Kebahasaan:\n• Menggunakan kalimat pasif (diukur, dicampur, diamati)\n• Kata kerja tindakan (mengamati, mencatat, mengukur)\n• Kata bilangan (2 ml, 100°C, 5 menit)\n• Konjungsi kronologis (pertama, kemudian, selanjutnya, akhirnya)"],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Pidato Persuasif',
                        'description' => 'Menyusun dan menyampaikan pidato persuasif yang efektif',
                        'materials' => [
                            ['title' => 'Struktur Pidato Persuasif', 'type' => 'text', 'content' => "Pidato persuasif bertujuan mengajak, membujuk, atau mempengaruhi pendengar.\n\nStruktur:\n1. Pembukaan: salam, sapaan, pengantar topik\n2. Isi: argumen, fakta, data pendukung\n3. Penutup: ajakan/imbauan, salam penutup\n\nTeknik persuasi:\n• Ethos: kredibilitas pembicara\n• Pathos: emosi pendengar\n• Logos: logika dan data\n\nTips menyampaikan pidato:\n• Kontak mata dengan audiens\n• Variasi intonasi\n• Bahasa tubuh yang mendukung\n• Gunakan contoh konkret"],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Menulis Teks Laporan Percobaan', 'description' => "Buatlah teks laporan percobaan sederhana (boleh percobaan IPA atau percobaan sederhana di rumah).\n\nKetentuan:\n1. Gunakan struktur yang benar (judul, tujuan, alat bahan, langkah, hasil, pembahasan, kesimpulan)\n2. Minimal 300 kata\n3. Sertakan tabel data hasil percobaan\n4. Gunakan bahasa baku dan ciri kebahasaan teks laporan\n5. Submit dalam format PDF atau ketik langsung.", 'max_score' => 100, 'days_from_now' => 14],
                    ['title' => 'Praktik Pidato Persuasif', 'description' => "Susun naskah pidato persuasif dengan tema: \"Pentingnya Menjaga Lingkungan Sekolah\".\n\nKetentuan:\n1. Minimal 250 kata\n2. Gunakan struktur pidato yang benar\n3. Gunakan minimal 2 teknik persuasi\n4. Sertakan fakta/data pendukung\n5. BONUS: Rekam video pidato (2-3 menit) dan upload.", 'max_score' => 100, 'days_from_now' => 21],
                ],
                'quiz' => [
                    'title' => 'Quiz: Teks Laporan & Pidato Persuasif',
                    'description' => 'Quiz untuk menguji pemahaman tentang teks laporan percobaan dan pidato persuasif.',
                    'time_limit' => 20,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'Bagian yang berisi langkah-langkah dalam teks laporan percobaan disebut ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Tujuan'],['key'=>'B','text'=>'Prosedur'],['key'=>'C','text'=>'Kesimpulan'],['key'=>'D','text'=>'Pembahasan']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Teks laporan percobaan banyak menggunakan kalimat pasif.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Teknik persuasi yang mengandalkan emosi pendengar disebut ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Ethos'],['key'=>'B','text'=>'Pathos'],['key'=>'C','text'=>'Logos'],['key'=>'D','text'=>'Retorika']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Pidato persuasif bertujuan untuk menghibur pendengar.', 'type' => 'true_false', 'answer' => 'false', 'score' => 10],
                        ['q' => 'Konjungsi kronologis yang tepat untuk teks laporan percobaan adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'tetapi, namun'],['key'=>'B','text'=>'karena, sebab'],['key'=>'C','text'=>'pertama, kemudian, selanjutnya'],['key'=>'D','text'=>'jika, maka']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Jelaskan perbedaan antara pidato persuasif dan pidato informatif!', 'type' => 'essay', 'answer' => null, 'score' => 25],
                        ['q' => 'Sebutkan 3 teknik persuasi dan berikan contoh masing-masing!', 'type' => 'essay', 'answer' => null, 'score' => 25],
                    ],
                ],
            ],
        ];
    }

    private function getSMACourses(): array
    {
        return [
            // ─── Fisika Kelas X ───
            [
                'name' => 'Fisika Kelas X - Kinematika & Dinamika',
                'description' => 'Mempelajari gerak lurus, gerak parabola, hukum Newton, dan penerapannya dalam permasalahan fisika.',
                'subject_match' => 'fisika',
                'grade' => 10,
                'modules' => [
                    [
                        'title' => 'Bab 1: Besaran dan Satuan',
                        'description' => 'Besaran pokok, besaran turunan, dimensi, dan angka penting',
                        'materials' => [
                            ['title' => 'Besaran Pokok dan Turunan', 'type' => 'text', 'content' => "Besaran Pokok (7 besaran SI):\n1. Panjang → meter (m)\n2. Massa → kilogram (kg)\n3. Waktu → sekon (s)\n4. Suhu → kelvin (K)\n5. Kuat arus → ampere (A)\n6. Jumlah zat → mol (mol)\n7. Intensitas cahaya → kandela (cd)\n\nBesaran Turunan (diturunkan dari besaran pokok):\n• Kecepatan = panjang/waktu → m/s\n• Gaya = massa × percepatan → kg⋅m/s² = Newton\n• Energi = gaya × jarak → kg⋅m²/s² = Joule"],
                            ['title' => 'Analisis Dimensi', 'type' => 'text', 'content' => "Dimensi adalah cara menyatakan besaran dalam bentuk besaran pokok.\n\nNotasi: [M] massa, [L] panjang, [T] waktu\n\nContoh:\n• Kecepatan: [L][T]⁻¹\n• Gaya: [M][L][T]⁻²\n• Energi: [M][L]²[T]⁻²\n\nKegunaan:\n1. Menguji kebenaran rumus\n2. Menemukan hubungan antar besaran"],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Kinematika Gerak Lurus',
                        'description' => 'GLB, GLBB, gerak vertikal, dan grafik gerak',
                        'materials' => [
                            ['title' => 'Rumus GLB dan GLBB', 'type' => 'text', 'content' => "GLB (Gerak Lurus Beraturan):\n• v = konstan, a = 0\n• s = v × t\n\nGLBB (Gerak Lurus Berubah Beraturan):\n• a = konstan ≠ 0\n• v = v₀ + at\n• s = v₀t + ½at²\n• v² = v₀² + 2as\n\nGerak Vertikal:\n• Jatuh bebas: v₀ = 0, a = g = 9,8 m/s²\n• Dilempar ke atas: a = -g\n• Dilempar ke bawah: a = +g"],
                            ['title' => 'Grafik Gerak', 'type' => 'text', 'content' => "Grafik s-t (posisi-waktu):\n• GLB: garis lurus miring\n• GLBB: parabola\n• Gradien = kecepatan\n\nGrafik v-t (kecepatan-waktu):\n• GLB: garis horizontal\n• GLBB: garis lurus miring\n• Gradien = percepatan\n• Luas daerah = perpindahan\n\nGrafik a-t (percepatan-waktu):\n• GLB: a = 0 (di sumbu t)\n• GLBB: garis horizontal"],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Problem Set: Kinematika', 'description' => "Kerjakan soal-soal berikut dengan langkah penyelesaian lengkap:\n\n1. Sebuah mobil bergerak dari keadaan diam dan mencapai kecepatan 72 km/jam dalam 10 sekon. Tentukan percepatan dan jarak yang ditempuh!\n2. Sebuah bola dilempar vertikal ke atas dengan v₀ = 20 m/s (g = 10 m/s²). Tentukan: a) tinggi maksimum, b) waktu di udara, c) kecepatan saat kembali ke tanah.\n3. Gambarkan grafik v-t dan s-t untuk soal nomor 1.\n4. Buat analisis dimensi untuk membuktikan bahwa rumus s = v₀t + ½at² benar secara dimensi.", 'max_score' => 100, 'days_from_now' => 7],
                ],
                'quiz' => [
                    'title' => 'Quiz: Besaran, Satuan & Kinematika',
                    'description' => 'Quiz mencakup besaran pokok/turunan, analisis dimensi, dan kinematika gerak lurus.',
                    'time_limit' => 35,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'Yang BUKAN termasuk besaran pokok adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Massa'],['key'=>'B','text'=>'Kecepatan'],['key'=>'C','text'=>'Suhu'],['key'=>'D','text'=>'Waktu']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Dimensi gaya (F = ma) adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'[M][L][T]⁻¹'],['key'=>'B','text'=>'[M][L][T]⁻²'],['key'=>'C','text'=>'[M][L]²[T]⁻²'],['key'=>'D','text'=>'[M][L]⁻¹[T]⁻²']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Sebuah benda jatuh bebas dari ketinggian 45 m (g=10 m/s²). Waktu yang diperlukan untuk sampai ke tanah adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'2 s'],['key'=>'B','text'=>'3 s'],['key'=>'C','text'=>'4 s'],['key'=>'D','text'=>'4,5 s']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Pada GLB, grafik v-t berupa garis horizontal.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Pada gerak jatuh bebas, kecepatan awal benda adalah nol.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Luas daerah di bawah grafik v-t menyatakan ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Kecepatan'],['key'=>'B','text'=>'Percepatan'],['key'=>'C','text'=>'Perpindahan'],['key'=>'D','text'=>'Gaya']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Mobil bergerak dari diam dengan a = 2 m/s². Kecepatan setelah 5 s adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'5 m/s'],['key'=>'B','text'=>'10 m/s'],['key'=>'C','text'=>'20 m/s'],['key'=>'D','text'=>'25 m/s']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Turunkan rumus v² = v₀² + 2as dari rumus-rumus GLBB yang Anda ketahui!', 'type' => 'essay', 'answer' => null, 'score' => 20],
                    ],
                ],
            ],

            // ─── Biologi Kelas XI ───
            [
                'name' => 'Biologi Kelas XI - Sel & Sistem Organ',
                'description' => 'Mempelajari struktur dan fungsi sel, sistem pencernaan, sistem pernapasan, dan sistem peredaran darah manusia.',
                'subject_match' => 'biologi',
                'grade' => 11,
                'modules' => [
                    [
                        'title' => 'Bab 1: Struktur dan Fungsi Sel',
                        'description' => 'Organel sel, perbedaan sel hewan dan tumbuhan, transpor membran',
                        'materials' => [
                            ['title' => 'Organel Sel dan Fungsinya', 'type' => 'text', 'content' => "Organel Sel:\n\n1. Nukleus (inti sel): mengatur seluruh aktivitas sel, menyimpan DNA\n2. Mitokondria: respirasi sel, menghasilkan ATP (\"powerhouse of the cell\")\n3. Ribosom: sintesis protein\n4. Retikulum Endoplasma:\n   • RE Kasar (ada ribosom): sintesis protein\n   • RE Halus: sintesis lipid, detoksifikasi\n5. Badan Golgi: modifikasi & pengemasan protein\n6. Lisosom: pencernaan intraseluler\n7. Vakuola: penyimpanan (besar pada sel tumbuhan)\n8. Kloroplas: fotosintesis (hanya sel tumbuhan)\n9. Dinding sel: pelindung & penyokong (hanya sel tumbuhan)"],
                            ['title' => 'Perbedaan Sel Hewan dan Sel Tumbuhan', 'type' => 'text', 'content' => "| Komponen | Sel Hewan | Sel Tumbuhan |\n|----------|-----------|---------------|\n| Dinding sel | Tidak ada | Ada (selulosa) |\n| Kloroplas | Tidak ada | Ada |\n| Vakuola | Kecil/tidak ada | Besar (vakuola sentral) |\n| Sentriol | Ada | Tidak ada |\n| Lisosom | Ada | Jarang ada |\n| Bentuk | Bulat/tidak beraturan | Kotak/persegi |\n| Plastida | Tidak ada | Ada |"],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Sistem Pencernaan Manusia',
                        'description' => 'Organ pencernaan, enzim, dan gangguan sistem pencernaan',
                        'materials' => [
                            ['title' => 'Saluran Pencernaan Manusia', 'type' => 'text', 'content' => "Saluran Pencernaan:\n\n1. Mulut: pencernaan mekanik (gigi) & kimiawi (amilase)\n2. Kerongkongan (esofagus): peristaltik\n3. Lambung: HCl + pepsin → pencernaan protein\n4. Usus halus:\n   • Duodenum: menerima enzim pankreas & empedu\n   • Jejunum & Ileum: penyerapan nutrisi\n5. Usus besar: penyerapan air, pembentukan feses\n6. Rektum → Anus: pengeluaran feses\n\nKelenjar pencernaan:\n• Kelenjar ludah: amilase (karbohidrat)\n• Lambung: pepsin (protein), HCl\n• Pankreas: lipase (lemak), tripsin (protein), amilase\n• Hati: empedu (mengemulsi lemak)"],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Laporan Praktikum: Pengamatan Sel', 'description' => "Lakukan pengamatan sel menggunakan mikroskop (atau gambar referensi jika tidak tersedia).\n\n1. Amati preparat sel bawang merah (sel tumbuhan)\n2. Amati sel epitel pipi (sel hewan) jika memungkinkan\n3. Gambar hasil pengamatan dengan label organel\n4. Buat tabel perbandingan sel tumbuhan vs sel hewan\n5. Minimal 400 kata dengan format laporan ilmiah\n\nJika tidak ada mikroskop: gunakan gambar dari internet, analisis dan bandingkan.", 'max_score' => 100, 'days_from_now' => 10],
                ],
                'quiz' => [
                    'title' => 'Quiz: Sel & Sistem Pencernaan',
                    'description' => 'Quiz tentang organel sel dan sistem pencernaan manusia.',
                    'time_limit' => 25,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'Organel yang disebut "powerhouse of the cell" adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Nukleus'],['key'=>'B','text'=>'Ribosom'],['key'=>'C','text'=>'Mitokondria'],['key'=>'D','text'=>'Badan Golgi']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Kloroplas hanya terdapat pada sel tumbuhan.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Enzim yang memecah protein di lambung adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Amilase'],['key'=>'B','text'=>'Lipase'],['key'=>'C','text'=>'Pepsin'],['key'=>'D','text'=>'Tripsin']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Penyerapan nutrisi terjadi terutama di ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Lambung'],['key'=>'B','text'=>'Usus halus'],['key'=>'C','text'=>'Usus besar'],['key'=>'D','text'=>'Kerongkongan']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Sel hewan memiliki dinding sel.', 'type' => 'true_false', 'answer' => 'false', 'score' => 10],
                        ['q' => 'Empedu diproduksi oleh ... dan disimpan di ...', 'type' => 'short_answer', 'answer' => null, 'score' => 10],
                        ['q' => 'Jelaskan proses pencernaan karbohidrat dari mulut hingga usus halus!', 'type' => 'essay', 'answer' => null, 'score' => 20],
                        ['q' => 'Fungsi utama Retikulum Endoplasma Kasar adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Sintesis lipid'],['key'=>'B','text'=>'Sintesis protein'],['key'=>'C','text'=>'Pencernaan intraseluler'],['key'=>'D','text'=>'Fotosintesis']], 'answer' => 'B', 'score' => 10],
                    ],
                ],
            ],
        ];
    }

    private function getSMKCourses(): array
    {
        return [
            // ─── Matematika SMK Kelas X ───
            [
                'name' => 'Matematika SMK Kelas X - Logika & Himpunan',
                'description' => 'Mempelajari logika matematika, himpunan, dan penerapannya dalam pemrograman dan kehidupan sehari-hari.',
                'subject_match' => 'matematika',
                'grade' => 10,
                'modules' => [
                    [
                        'title' => 'Bab 1: Logika Matematika',
                        'description' => 'Pernyataan, negasi, konjungsi, disjungsi, implikasi, dan biimplikasi',
                        'materials' => [
                            ['title' => 'Pernyataan dan Tabel Kebenaran', 'type' => 'text', 'content' => "Pernyataan (proposisi): kalimat yang bernilai benar (B) atau salah (S), tidak kedua-duanya.\n\nContoh pernyataan:\n• \"2 + 3 = 5\" → B (Benar)\n• \"Jakarta adalah ibu kota Jepang\" → S (Salah)\n\nBUKAN pernyataan:\n• \"Berapa umurmu?\" (kalimat tanya)\n• \"Tutup pintunya!\" (kalimat perintah)\n\nOperasi Logika:\n• Negasi (~p): kebalikan nilai\n• Konjungsi (p ∧ q): B hanya jika keduanya B\n• Disjungsi (p ∨ q): S hanya jika keduanya S\n• Implikasi (p → q): S hanya jika p B dan q S\n• Biimplikasi (p ↔ q): B jika keduanya sama"],
                            ['title' => 'Penerapan Logika dalam Pemrograman', 'type' => 'text', 'content' => "Logika matematika sangat penting dalam pemrograman:\n\n1. IF-ELSE (Implikasi):\n   if (x > 0) → \"positif\"\n   Jika x > 0 MAKA output \"positif\"\n\n2. AND (Konjungsi):\n   if (umur >= 17 AND punya_KTP)\n   → boleh_memilih\n\n3. OR (Disjungsi):\n   if (nilai >= 75 OR ada_remedial)\n   → lulus\n\n4. NOT (Negasi):\n   if (!is_logged_in)\n   → redirect ke login\n\nTabel kebenaran = truth table dalam programming."],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Himpunan',
                        'description' => 'Operasi himpunan, diagram Venn, dan penerapannya',
                        'materials' => [
                            ['title' => 'Operasi Himpunan', 'type' => 'text', 'content' => "A = {1, 2, 3, 4, 5}, B = {3, 4, 5, 6, 7}\n\nOperasi:\n• Irisan (A ∩ B): {3, 4, 5}\n  Anggota yang ada di A DAN B\n\n• Gabungan (A ∪ B): {1, 2, 3, 4, 5, 6, 7}\n  Anggota yang ada di A ATAU B\n\n• Selisih (A - B): {1, 2}\n  Anggota A yang TIDAK ada di B\n\n• Komplemen (A'): anggota semesta yang TIDAK ada di A\n\nDiagram Venn:\nGunakan lingkaran berpotongan untuk visualisasi.\n\nPenerapan: database query (SQL JOIN = irisan), filter data, analisis data."],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Tugas: Tabel Kebenaran & Logika Programming', 'description' => "Kerjakan soal berikut:\n\n1. Buat tabel kebenaran untuk: (p ∧ q) → ~r\n2. Tentukan nilai kebenaran: \"Jika 2+2=4 maka Jakarta ibu kota Indonesia\"\n3. Tulis pseudocode program penjualan yang menggunakan:\n   • Konjungsi (AND): diskon jika member DAN belanja > 100rb\n   • Disjungsi (OR): gratis ongkir jika berat < 1kg ATAU lokasi Jakarta\n   • Negasi (NOT): tampilkan warning jika stok TIDAK cukup\n4. Gambar diagram Venn untuk: siswa yang suka Matematika dan siswa yang suka Programming dari data 40 siswa.", 'max_score' => 100, 'days_from_now' => 7],
                ],
                'quiz' => [
                    'title' => 'Quiz: Logika Matematika & Himpunan',
                    'description' => 'Quiz tentang logika matematika dan operasi himpunan.',
                    'time_limit' => 25,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'Negasi dari "Semua siswa rajin belajar" adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Semua siswa tidak rajin belajar'],['key'=>'B','text'=>'Ada siswa yang tidak rajin belajar'],['key'=>'C','text'=>'Tidak ada siswa yang rajin belajar'],['key'=>'D','text'=>'Sebagian siswa rajin belajar']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Konjungsi (p ∧ q) bernilai benar hanya jika p dan q keduanya benar.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Implikasi p → q bernilai salah jika ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'p benar, q benar'],['key'=>'B','text'=>'p benar, q salah'],['key'=>'C','text'=>'p salah, q benar'],['key'=>'D','text'=>'p salah, q salah']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'A = {1,2,3}, B = {2,3,4}. A ∩ B = ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'{1,2,3,4}'],['key'=>'B','text'=>'{2,3}'],['key'=>'C','text'=>'{1,4}'],['key'=>'D','text'=>'{1}']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Dalam pemrograman, operator AND setara dengan operasi logika ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Negasi'],['key'=>'B','text'=>'Konjungsi'],['key'=>'C','text'=>'Disjungsi'],['key'=>'D','text'=>'Implikasi']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Disjungsi bernilai salah hanya jika kedua pernyataan bernilai salah.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'A ∪ B artinya gabungan himpunan A dan B.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Jelaskan perbedaan antara irisan dan gabungan himpunan beserta contohnya!', 'type' => 'essay', 'answer' => null, 'score' => 20],
                    ],
                ],
            ],

            // ─── Bahasa Inggris SMK Kelas XI ───
            [
                'name' => 'English for SMK XI - Business Communication',
                'description' => 'Learn professional English for business correspondence, presentations, and workplace communication.',
                'subject_match' => 'bahasa inggris',
                'grade' => 11,
                'modules' => [
                    [
                        'title' => 'Unit 1: Business Letters & Emails',
                        'description' => 'Writing formal business letters and professional emails',
                        'materials' => [
                            ['title' => 'Parts of a Business Letter', 'type' => 'text', 'content' => "Structure of a Business Letter:\n\n1. Letterhead (company name, address, logo)\n2. Date: February 10, 2026\n3. Inside Address (recipient's name & address)\n4. Salutation: Dear Mr./Ms. [Name],\n5. Body:\n   • Opening paragraph: purpose of letter\n   • Middle paragraph(s): details\n   • Closing paragraph: call to action\n6. Complimentary Close: Sincerely, / Best regards,\n7. Signature\n\nCommon Types:\n• Inquiry Letter (asking for information)\n• Order Letter (placing an order)\n• Complaint Letter (expressing dissatisfaction)\n• Application Letter (applying for a job)"],
                            ['title' => 'Professional Email Writing', 'type' => 'text', 'content' => "Email Etiquette:\n\n1. Subject Line: Clear and specific\n   ✅ \"Meeting Rescheduled to March 5\"\n   ❌ \"Important!!!\"\n\n2. Greeting: \"Dear Ms. Johnson,\" or \"Hi Team,\"\n\n3. Body:\n   • Get to the point quickly\n   • Use short paragraphs\n   • Use bullet points for lists\n   • Be polite but concise\n\n4. Closing: \"Best regards,\" \"Thank you,\"\n\n5. Signature: Name, position, contact info\n\nCommon Phrases:\n• \"I am writing to inquire about...\"\n• \"Please find attached...\"\n• \"I look forward to hearing from you.\"\n• \"Thank you for your prompt response.\""],
                        ],
                    ],
                    [
                        'title' => 'Unit 2: Job Application',
                        'description' => 'Writing CV/Resume and cover letters, job interview skills',
                        'materials' => [
                            ['title' => 'How to Write a CV/Resume', 'type' => 'text', 'content' => "CV/Resume Sections:\n\n1. Personal Information\n   • Full name, email, phone, address\n\n2. Objective/Summary\n   \"A motivated SMK graduate seeking a position as...\"\n\n3. Education\n   • School name, year, major, GPA\n\n4. Skills\n   • Technical: MS Office, programming, accounting software\n   • Soft skills: teamwork, communication, leadership\n   • Languages: Indonesian (native), English (intermediate)\n\n5. Experience\n   • Internship, part-time jobs, school projects\n\n6. Achievements\n   • Competitions, certifications, awards\n\nTips:\n• Keep it 1 page\n• Use action verbs: managed, created, organized\n• Tailor for each job application\n• No typos!"],
                            ['title' => 'Job Interview Tips', 'type' => 'text', 'content' => "Common Interview Questions & How to Answer:\n\n1. \"Tell me about yourself.\"\n   → Brief summary: education + skills + goals\n\n2. \"Why do you want this job?\"\n   → Show knowledge of company + match your skills\n\n3. \"What are your strengths?\"\n   → Give examples with evidence\n   \"I'm good at problem-solving. In my school project, I...\"\n\n4. \"What are your weaknesses?\"\n   → Be honest but show improvement\n   \"I used to be shy, but I've been practicing public speaking...\"\n\n5. \"Where do you see yourself in 5 years?\"\n   → Show ambition and growth\n\nDo's:\n✅ Arrive early, dress formally\n✅ Make eye contact, smile\n✅ Bring extra copies of CV\n\nDon'ts:\n❌ Use slang or informal language\n❌ Speak negatively about previous employers\n❌ Check your phone"],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Writing Task: Application Letter', 'description' => "Write a job application letter for one of the following positions:\n\na) IT Support Staff at PT Telkom Indonesia\nb) Accounting Staff at Bank BRI\nc) Administrative Assistant at a hotel\n\nRequirements:\n1. Use correct business letter format\n2. Minimum 200 words\n3. Include: why you're interested, your qualifications, and call to action\n4. Use formal English\n5. Also attach a simple CV/Resume (1 page)\n\nGrading criteria: Format (25%), Content (30%), Grammar (25%), Vocabulary (20%)", 'max_score' => 100, 'days_from_now' => 14],
                ],
                'quiz' => [
                    'title' => 'Quiz: Business Letters & Job Application',
                    'description' => 'Test your knowledge on business communication and job application skills.',
                    'time_limit' => 20,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'The correct salutation in a business letter is ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'Hey Mr. Smith!'],['key'=>'B','text'=>'Dear Mr. Smith,'],['key'=>'C','text'=>'Hi there Smith,'],['key'=>'D','text'=>'Yo Mr. Smith']], 'answer' => 'B', 'score' => 10],
                        ['q' => '"Please find attached" is commonly used in professional emails.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'A CV should be ... pages long for fresh graduates.', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'1'],['key'=>'B','text'=>'3'],['key'=>'C','text'=>'5'],['key'=>'D','text'=>'10']], 'answer' => 'A', 'score' => 10],
                        ['q' => 'Which is the correct closing for a formal letter?', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'See ya!'],['key'=>'B','text'=>'Cheers,'],['key'=>'C','text'=>'Best regards,'],['key'=>'D','text'=>'Bye!']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'In a job interview, you should speak negatively about your previous employer to show honesty.', 'type' => 'true_false', 'answer' => 'false', 'score' => 10],
                        ['q' => 'The purpose of a cover letter is to ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'List your hobbies'],['key'=>'B','text'=>'Introduce yourself and express interest in the position'],['key'=>'C','text'=>'Complain about the company'],['key'=>'D','text'=>'Ask about the salary']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Write a short email to a company asking about available internship positions. Use proper format.', 'type' => 'essay', 'answer' => null, 'score' => 20],
                        ['q' => 'What does "I look forward to hearing from you" mean in Indonesian?', 'type' => 'short_answer', 'answer' => null, 'score' => 10],
                    ],
                ],
            ],

            // ─── Pemrograman Web Kelas X ───
            [
                'name' => 'Pemrograman Web Kelas X - HTML, CSS & JavaScript',
                'description' => 'Mempelajari dasar-dasar pembuatan website menggunakan HTML5, CSS3, dan pengenalan JavaScript.',
                'subject_match' => 'pemrograman web',
                'grade' => 10,
                'modules' => [
                    [
                        'title' => 'Bab 1: Pengenalan HTML5',
                        'description' => 'Struktur dasar HTML, tag, atribut, dan elemen semantik',
                        'materials' => [
                            ['title' => 'Struktur Dasar HTML', 'type' => 'text', 'content' => "HTML (HyperText Markup Language) adalah bahasa markup untuk membuat halaman web.\n\nStruktur dasar:\n<!DOCTYPE html>\n<html lang=\"id\">\n<head>\n    <meta charset=\"UTF-8\">\n    <title>Judul Halaman</title>\n</head>\n<body>\n    <h1>Halo Dunia!</h1>\n    <p>Ini adalah paragraf pertama saya.</p>\n</body>\n</html>\n\nTag penting:\n• <h1> - <h6>: Heading\n• <p>: Paragraf\n• <a href=\"\">: Link\n• <img src=\"\" alt=\"\">: Gambar\n• <ul>, <ol>, <li>: List\n• <table>, <tr>, <td>: Tabel\n• <div>: Container\n• <span>: Inline container"],
                            ['title' => 'Elemen Semantik HTML5', 'type' => 'text', 'content' => "Elemen semantik memberi makna pada konten:\n\n<header> - Bagian atas halaman/section\n<nav> - Navigasi\n<main> - Konten utama\n<article> - Konten independen (berita, blog)\n<section> - Bagian/seksi dari halaman\n<aside> - Konten sampingan (sidebar)\n<footer> - Bagian bawah halaman\n<figure> - Ilustrasi/gambar dengan caption\n<figcaption> - Caption untuk figure\n\nContoh:\n<header>\n    <nav>Menu navigasi</nav>\n</header>\n<main>\n    <article>\n        <h2>Judul Artikel</h2>\n        <p>Isi artikel...</p>\n    </article>\n    <aside>Sidebar</aside>\n</main>\n<footer>Copyright 2026</footer>"],
                            ['title' => 'Form dan Input HTML', 'type' => 'text', 'content' => "Form digunakan untuk mengumpulkan input dari pengguna:\n\n<form action=\"/submit\" method=\"POST\">\n    <label for=\"nama\">Nama:</label>\n    <input type=\"text\" id=\"nama\" name=\"nama\" required>\n\n    <label for=\"email\">Email:</label>\n    <input type=\"email\" id=\"email\" name=\"email\">\n\n    <label for=\"password\">Password:</label>\n    <input type=\"password\" id=\"password\" name=\"password\">\n\n    <label>Gender:</label>\n    <input type=\"radio\" name=\"gender\" value=\"L\"> Laki-laki\n    <input type=\"radio\" name=\"gender\" value=\"P\"> Perempuan\n\n    <label for=\"kota\">Kota:</label>\n    <select id=\"kota\" name=\"kota\">\n        <option value=\"gst\">Gunungsitoli</option>\n        <option value=\"mdn\">Medan</option>\n    </select>\n\n    <textarea name=\"pesan\"></textarea>\n    <button type=\"submit\">Kirim</button>\n</form>"],
                        ],
                    ],
                    [
                        'title' => 'Bab 2: Styling dengan CSS3',
                        'description' => 'Selector, properti, box model, flexbox, dan responsive design',
                        'materials' => [
                            ['title' => 'Dasar CSS dan Selector', 'type' => 'text', 'content' => "CSS (Cascading Style Sheets) mengatur tampilan halaman web.\n\n3 cara menambahkan CSS:\n1. Inline: <p style=\"color: red;\">Teks merah</p>\n2. Internal: <style> di <head>\n3. External: <link rel=\"stylesheet\" href=\"style.css\">\n\nSelector:\n• Element: p { color: blue; }\n• Class: .judul { font-size: 24px; }\n• ID: #header { background: #333; }\n• Descendant: .card p { margin: 10px; }\n• Pseudo: a:hover { color: red; }\n\nBox Model:\n• content → padding → border → margin\n\nContoh:\n.card {\n    width: 300px;\n    padding: 20px;\n    border: 1px solid #ddd;\n    margin: 10px;\n    border-radius: 8px;\n    box-shadow: 0 2px 4px rgba(0,0,0,0.1);\n}"],
                            ['title' => 'Flexbox dan Responsive Design', 'type' => 'text', 'content' => "Flexbox: layout 1 dimensi (baris/kolom)\n\n.container {\n    display: flex;\n    justify-content: space-between; /* horizontal */\n    align-items: center; /* vertical */\n    gap: 20px;\n}\n\nProperti flex item:\n• flex-grow: 1 (mengisi ruang)\n• flex-shrink: 0 (tidak menyusut)\n• flex-basis: 300px (ukuran awal)\n\nResponsive Design dengan Media Query:\n\n/* Mobile First */\n.card { width: 100%; }\n\n/* Tablet */\n@media (min-width: 768px) {\n    .card { width: 48%; }\n}\n\n/* Desktop */\n@media (min-width: 1024px) {\n    .card { width: 30%; }\n}\n\nTips responsive:\n• Gunakan unit relatif (%, em, rem, vw, vh)\n• Hindari width/height fixed\n• Gunakan max-width bukan width"],
                        ],
                    ],
                    [
                        'title' => 'Bab 3: Pengenalan JavaScript',
                        'description' => 'Variabel, tipe data, fungsi, DOM manipulation',
                        'materials' => [
                            ['title' => 'Variabel dan Tipe Data JavaScript', 'type' => 'text', 'content' => "Deklarasi variabel:\nlet nama = \"Budi\";      // bisa diubah\nconst PI = 3.14;         // tidak bisa diubah\nvar umur = 17;           // cara lama (hindari)\n\nTipe Data:\n• String: \"Halo\", 'Dunia'\n• Number: 42, 3.14\n• Boolean: true, false\n• Array: [1, 2, 3]\n• Object: { nama: \"Budi\", umur: 17 }\n• null, undefined\n\nOperator:\n• Aritmatika: +, -, *, /, %\n• Perbandingan: ==, ===, !=, !==, >, <\n• Logika: &&, ||, !\n\nKondisi:\nif (nilai >= 75) {\n    console.log(\"Lulus\");\n} else {\n    console.log(\"Tidak lulus\");\n}"],
                            ['title' => 'DOM Manipulation', 'type' => 'text', 'content' => "DOM (Document Object Model) memungkinkan JavaScript mengubah halaman web.\n\nMengambil elemen:\nlet judul = document.getElementById('judul');\nlet cards = document.querySelectorAll('.card');\nlet btn = document.querySelector('#submit-btn');\n\nMengubah konten:\njudul.textContent = 'Judul Baru';\njudul.innerHTML = '<em>Judul Tebal</em>';\n\nMengubah style:\njudul.style.color = 'blue';\njudul.style.fontSize = '24px';\n\nMenambah/hapus class:\njudul.classList.add('active');\njudul.classList.remove('hidden');\njudul.classList.toggle('dark-mode');\n\nEvent Listener:\nbtn.addEventListener('click', function() {\n    alert('Tombol diklik!');\n});\n\nform.addEventListener('submit', function(e) {\n    e.preventDefault();\n    // validasi form\n});"],
                        ],
                    ],
                ],
                'assignments' => [
                    ['title' => 'Proyek: Membuat Halaman Profil Pribadi', 'description' => "Buatlah halaman web profil pribadi menggunakan HTML & CSS.\n\nKetentuan:\n1. Gunakan elemen semantik HTML5 (header, nav, main, section, footer)\n2. Minimal 3 section: Tentang Saya, Hobi/Keahlian, Kontak\n3. Tambahkan foto (boleh placeholder)\n4. Gunakan CSS external (file terpisah)\n5. Terapkan flexbox untuk layout\n6. Responsive (tampil baik di HP dan desktop)\n7. Gunakan minimal 3 Google Fonts\n8. Tambahkan hover effect pada link dan button\n\nBonus: Tambahkan animasi CSS sederhana (transition/keyframe)\n\nKumpulkan dalam format ZIP berisi file HTML + CSS + gambar.", 'max_score' => 100, 'days_from_now' => 14],
                    ['title' => 'Tugas: Validasi Form dengan JavaScript', 'description' => "Buatlah form pendaftaran siswa baru dengan validasi JavaScript.\n\nField yang harus ada:\n1. Nama lengkap (wajib, minimal 3 karakter)\n2. Email (wajib, format email valid)\n3. No HP (wajib, hanya angka, 10-13 digit)\n4. Tanggal lahir (wajib, umur minimal 12 tahun)\n5. Jenis kelamin (wajib, radio button)\n6. Alamat (wajib, textarea minimal 20 karakter)\n\nKetentuan:\n• Validasi real-time (saat mengetik)\n• Tampilkan pesan error di bawah setiap field\n• Tombol submit disabled jika ada error\n• Tampilkan ringkasan data setelah submit berhasil\n• Style yang rapi menggunakan CSS", 'max_score' => 100, 'days_from_now' => 21],
                ],
                'quiz' => [
                    'title' => 'Quiz: HTML, CSS & JavaScript Dasar',
                    'description' => 'Quiz untuk menguji pemahaman dasar pemrograman web.',
                    'time_limit' => 30,
                    'passing_score' => 70,
                    'questions' => [
                        ['q' => 'Tag HTML yang benar untuk membuat heading terbesar adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'<heading>'],['key'=>'B','text'=>'<h1>'],['key'=>'C','text'=>'<h6>'],['key'=>'D','text'=>'<head>']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'CSS adalah singkatan dari Cascading Style Sheets.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Properti CSS untuk mengubah warna teks adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'font-color'],['key'=>'B','text'=>'text-color'],['key'=>'C','text'=>'color'],['key'=>'D','text'=>'background-color']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Cara mendeklarasikan variabel yang tidak bisa diubah nilainya di JavaScript adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'var'],['key'=>'B','text'=>'let'],['key'=>'C','text'=>'const'],['key'=>'D','text'=>'static']], 'answer' => 'C', 'score' => 10],
                        ['q' => 'Elemen <nav> termasuk elemen semantik HTML5.', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Method JavaScript untuk mengambil elemen berdasarkan ID adalah ...', 'type' => 'multiple_choice', 'options' => [['key'=>'A','text'=>'document.getElement(id)'],['key'=>'B','text'=>'document.getElementById(id)'],['key'=>'C','text'=>'document.findById(id)'],['key'=>'D','text'=>'document.query(id)']], 'answer' => 'B', 'score' => 10],
                        ['q' => 'Flexbox hanya bisa mengatur layout secara horizontal.', 'type' => 'true_false', 'answer' => 'false', 'score' => 10],
                        ['q' => 'CSS selector .card p akan memilih semua elemen <p> di dalam elemen dengan class "card".', 'type' => 'true_false', 'answer' => 'true', 'score' => 10],
                        ['q' => 'Jelaskan perbedaan antara inline, internal, dan external CSS beserta kelebihan dan kekurangan masing-masing!', 'type' => 'essay', 'answer' => null, 'score' => 20],
                    ],
                ],
            ],
        ];
    }

    // ================================================================
    //  SEED HELPERS
    // ================================================================

    private function seedModules(LmsCourse $course, array $moduleDefs): void
    {
        foreach ($moduleDefs as $seq => $modDef) {
            $module = LmsModule::create([
                'course_id' => $course->id,
                'title' => $modDef['title'],
                'description' => $modDef['description'] ?? null,
                'sequence' => $seq + 1,
                'is_active' => true,
            ]);

            foreach (($modDef['materials'] ?? []) as $matSeq => $matDef) {
                LmsMaterial::create([
                    'course_id' => $course->id,
                    'module_id' => $module->id,
                    'title' => $matDef['title'],
                    'material_type' => $matDef['type'],
                    'content' => $matDef['content'] ?? null,
                    'file_url' => $matDef['url'] ?? null,
                    'order_number' => $matSeq + 1,
                    'is_published' => true,
                ]);
            }
        }
    }

    private function seedAssignments(LmsCourse $course, array $assignmentDefs): void
    {
        foreach ($assignmentDefs as $asgDef) {
            LmsAssignment::create([
                'course_id' => $course->id,
                'title' => $asgDef['title'],
                'description' => $asgDef['description'],
                'max_score' => $asgDef['max_score'],
                'deadline' => now()->addDays($asgDef['days_from_now']),
                'is_published' => true,
            ]);
        }
    }

    private function seedQuiz(LmsCourse $course, array $quizDef): void
    {
        $quiz = LmsQuiz::create([
            'course_id' => $course->id,
            'title' => $quizDef['title'],
            'description' => $quizDef['description'],
            'time_limit' => $quizDef['time_limit'],
            'total_score' => 0,
            'passing_score' => $quizDef['passing_score'],
            'is_published' => true,
        ]);

        $totalScore = 0;
        foreach ($quizDef['questions'] as $order => $qDef) {
            LmsQuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => $qDef['q'],
                'question_type' => $qDef['type'],
                'options' => $qDef['options'] ?? null,
                'correct_answer' => $qDef['answer'],
                'order_number' => $order + 1,
                'score' => $qDef['score'],
            ]);
            $totalScore += $qDef['score'];
        }

        $quiz->update(['total_score' => $totalScore]);
    }

    private function seedStudentActivity(LmsCourse $course): void
    {
        // Get enrolled students (max 3 for simulation)
        $enrolledStudentIds = LmsEnrollment::whereIn(
            'lms_class_id',
            $course->lmsClasses()->pluck('id')
        )->pluck('student_id')->take(3);

        if ($enrolledStudentIds->isEmpty()) return;

        $assignments = $course->assignments;
        $quizzes = $course->quizzes()->with('questions')->get();

        foreach ($enrolledStudentIds as $i => $studentId) {
            // ─── Submit some assignments ───
            foreach ($assignments->take($i === 0 ? $assignments->count() : 1) as $assignment) {
                $isGraded = $i === 0; // First student gets graded
                $score = $isGraded ? rand(70, 95) : null;

                LmsSubmission::create([
                    'assignment_id' => $assignment->id,
                    'student_id' => $studentId,
                    'submission_text' => $this->getSampleSubmission($assignment->title),
                    'status' => $isGraded ? 'graded' : 'submitted',
                    'score' => $score,
                    'feedback' => $isGraded ? $this->getSampleFeedback($score) : null,
                    'submitted_at' => now()->subDays(rand(1, 5)),
                    'graded_at' => $isGraded ? now()->subDays(rand(0, 2)) : null,
                    'graded_by' => $isGraded ? $course->teacher->user_id : null,
                ]);
            }

            // ─── Take quizzes (first student completes, others may not) ───
            if ($i < 2) {
                foreach ($quizzes as $quiz) {
                    $attempt = LmsQuizAttempt::create([
                        'quiz_id' => $quiz->id,
                        'student_id' => $studentId,
                        'started_at' => now()->subDays(rand(1, 3))->subMinutes(rand(10, 30)),
                        'finished_at' => $i === 0 ? now()->subDays(rand(1, 3)) : null,
                        'score' => null,
                        'is_passed' => null,
                    ]);

                    if ($i === 0 && $quiz->questions->isNotEmpty()) {
                        // First student answers all questions
                        $totalEarned = 0;
                        $maxTotal = $quiz->questions->sum('score');

                        foreach ($quiz->questions as $question) {
                            $isCorrect = rand(0, 100) > 30; // 70% chance correct
                            $earnedScore = 0;

                            if ($question->isAutoGradable()) {
                                $earnedScore = $isCorrect ? $question->score : 0;
                                $totalEarned += $earnedScore;
                            }

                            LmsQuizAnswer::create([
                                'attempt_id' => $attempt->id,
                                'question_id' => $question->id,
                                'answer' => $isCorrect
                                    ? ($question->correct_answer ?? 'Jawaban siswa untuk soal ini.')
                                    : 'Jawaban salah siswa.',
                                'is_correct' => $question->isAutoGradable() ? $isCorrect : null,
                                'score' => $question->isAutoGradable() ? $earnedScore : null,
                            ]);
                        }

                        $pct = $maxTotal > 0 ? ($totalEarned / $maxTotal) * 100 : 0;
                        $attempt->update([
                            'score' => round($pct, 1),
                            'is_passed' => $pct >= $quiz->passing_score,
                        ]);
                    }
                }
            }
        }
    }

    private function getSampleSubmission(string $title): string
    {
        $samples = [
            'Berikut adalah jawaban saya untuk tugas ini. Saya telah mengerjakan semua soal sesuai instruksi yang diberikan.',
            'Tugas ini saya kerjakan berdasarkan materi yang telah dipelajari. Mohon koreksi jika ada kesalahan.',
            'Saya telah menyelesaikan tugas ini. Terlampir jawaban lengkap beserta langkah pengerjaannya.',
        ];
        return $samples[array_rand($samples)];
    }

    private function getSampleFeedback(int $score): string
    {
        if ($score >= 90) return 'Sangat baik! Jawaban lengkap dan tepat. Pertahankan!';
        if ($score >= 80) return 'Bagus! Sebagian besar jawaban benar. Perhatikan kembali detail penulisan rumus.';
        if ($score >= 70) return 'Cukup baik. Beberapa jawaban masih perlu diperbaiki. Pelajari kembali materi di modul.';
        return 'Perlu perbaikan. Silakan pelajari ulang materi dan coba kerjakan kembali.';
    }
}
