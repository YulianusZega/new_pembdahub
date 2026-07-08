<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use Illuminate\Foundation\Application;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Classroom;
use App\Models\TeachingAssignment;
use App\Models\StudentClass;
use App\Models\Schedule;
use App\Models\TimeSlot;
use App\Models\LmsCourse;
use App\Models\LmsClass;
use App\Models\LmsEnrollment;
use App\Models\LmsModule;
use App\Models\LmsMaterial;
use App\Models\LmsAssignment;
use App\Models\LmsQuiz;
use App\Models\LmsQuizQuestion;
use App\Models\LmsSubmission;
use App\Models\LmsQuizAttempt;
use App\Models\LmsQuizAnswer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

define('LARAVEL_START', microtime(true));

// Register Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (php_sapi_name() === 'cli') {
    $_GET['key'] = 'pembda2026';
}

if (($_GET['key'] ?? '') !== 'pembda2026') {
    die('Akses ditolak.');
}

try {
    echo "<h2>Memulai Inisialisasi Data Simulasi LMS untuk SMA Pembda 1 Gunungsitoli</h2>";

    $school = School::where('type', 'SMA')->first() ?? School::where('name', 'like', '%SMA%')->first();
    if (!$school) {
        die("Error: Sekolah SMA tidak ditemukan.");
    }

    $activeYear = AcademicYear::where('year', 'like', '%2026/2027%')->first() ?? AcademicYear::where('is_active', true)->first();
    if (!$activeYear) {
        die("Error: Tahun ajaran tidak ditemukan.");
    }

    // Find the correct semester under the target academic year
    $activeSemester = Semester::where('academic_year_id', $activeYear->id)
        ->where('is_active', true)
        ->first();
        
    if (!$activeSemester) {
        $activeSemester = Semester::where('academic_year_id', $activeYear->id)
            ->where('semester_name', 'like', '%ganjil%')
            ->first();
    }
    
    if (!$activeSemester) {
        $activeSemester = Semester::where('academic_year_id', $activeYear->id)->first();
    }

    if (!$activeSemester) {
        die("Error: Semester untuk tahun ajaran aktif tidak ditemukan.");
    }

    // Mengaktifkan tahun ajaran secara otomatis jika belum aktif di database
    if (!$activeYear->is_active) {
        echo "Mengaktifkan Tahun Ajaran " . $activeYear->year . " di database secara otomatis...<br>";
        AcademicYear::query()->update(['is_active' => false]);
        $activeYear->is_active = true;
        $activeYear->save();
    }

    // Mengaktifkan semester secara otomatis jika belum aktif di database
    if (!$activeSemester->is_active) {
        echo "Mengaktifkan Semester " . $activeSemester->semester_name . " di database secara otomatis...<br>";
        Semester::query()->update(['is_active' => false]);
        $activeSemester->is_active = true;
        $activeSemester->save();
    }

    echo "<b>Detail Informasi Akademik:</b><br>";
    echo "- Sekolah: " . $school->name . " (ID: " . $school->id . ")<br>";
    echo "- Tahun Ajaran: " . $activeYear->year . " (ID: " . $activeYear->id . ")<br>";
    echo "- Semester: " . $activeSemester->semester_name . " (ID: " . $activeSemester->id . ")<br><br>";

    // 1. Pembersihan Data Lama (Cascade Delete)
    echo "<b>Pembersihan data simulasi lama...</b><br>";
    $oldCourses = LmsCourse::where('school_id', $school->id)
        ->where('code', 'like', 'SIM-LMS-%')
        ->get();

    $deletedCount = 0;
    foreach ($oldCourses as $c) {
        // Delete enrollments & classes
        $lmsClassIds = LmsClass::where('course_id', $c->id)->pluck('id');
        LmsEnrollment::whereIn('lms_class_id', $lmsClassIds)->delete();
        LmsClass::where('course_id', $c->id)->delete();

        // Delete submissions & assignments
        $assignmentIds = LmsAssignment::where('course_id', $c->id)->pluck('id');
        LmsSubmission::whereIn('assignment_id', $assignmentIds)->delete();
        LmsAssignment::where('course_id', $c->id)->delete();

        // Delete quizzes, questions, attempts, answers
        $quizIds = LmsQuiz::where('course_id', $c->id)->pluck('id');
        $attemptIds = LmsQuizAttempt::whereIn('quiz_id', $quizIds)->pluck('id');
        LmsQuizAnswer::whereIn('attempt_id', $attemptIds)->delete();
        LmsQuizAttempt::whereIn('quiz_id', $quizIds)->delete();
        LmsQuizQuestion::whereIn('quiz_id', $quizIds)->delete();
        LmsQuiz::where('course_id', $c->id)->delete();

        // Delete materials & physical files
        $materials = LmsMaterial::where('course_id', $c->id)->get();
        foreach ($materials as $m) {
            if ($m->file_path && Storage::disk('public')->exists($m->file_path)) {
                Storage::disk('public')->delete($m->file_path);
            }
            $m->delete();
        }

        // Delete modules
        LmsModule::where('course_id', $c->id)->delete();

        // Delete course
        $c->delete();
        $deletedCount++;
    }
    echo "Berhasil menghapus {$deletedCount} LMS Courses lama.<br><br>";

    // 2. Mencari Kelas Aktif
    $classroom = Classroom::where('school_id', $school->id)
        ->where('academic_year_id', $activeYear->id)
        ->where('is_active', true)
        ->first();

    if (!$classroom) {
        // Fallback
        $classroom = Classroom::where('school_id', $school->id)
            ->where('is_active', true)
            ->first();
    }

    if (!$classroom) {
        die("Error: Tidak ada kelas aktif di sekolah ini.");
    }
    echo "<b>Kelas Terpilih untuk Simulasi:</b> " . $classroom->class_name . " (ID: " . $classroom->id . ")<br>";

    // Dapatkan ID siswa aktif di kelas tersebut untuk di-enroll
    $studentIds = StudentClass::where('classroom_id', $classroom->id)
        ->where('academic_year_id', $activeYear->id)
        ->where('status', 'aktif')
        ->pluck('student_id');

    echo "- Jumlah siswa aktif yang akan terdaftar: " . $studentIds->count() . " siswa.<br><br>";

    // 3. Template Data LMS
    $dataTemplates = [
        'Matematika' => [
            [
                'module_title' => 'Aljabar: Persamaan dan Pertidaksamaan Linear',
                'module_desc' => 'Mempelajari sistem persamaan linear dua variabel (SPLDV) dan metode penyelesaiannya.',
                'material_title' => 'Bahan Ajar - Sistem Persamaan Linear Dua Variabel.pdf',
                'material_html' => "
                    <h1>Aljabar: Sistem Persamaan Linear Dua Variabel (SPLDV)</h1>
                    <p>Sistem Persamaan Linear Dua Variabel (SPLDV) adalah sekumpulan dua atau lebih persamaan linear yang memiliki dua variabel yang sama. Bentuk umum SPLDV adalah:</p>
                    <pre>
                    ax + by = c
                    px + qy = r
                    </pre>
                    <h2>Metode Penyelesaian SPLDV:</h2>
                    <ol>
                        <li><strong>Metode Substitusi:</strong> Mengganti salah satu variabel dengan variabel lain dari persamaan pertama ke persamaan kedua.</li>
                        <li><strong>Metode Eliminasi:</strong> Menghilangkan salah satu variabel dengan menjumlahkan atau mengurangkan persamaan setelah menyamakan koefisiennya.</li>
                        <li><strong>Metode Campuran:</strong> Menggabungkan metode eliminasi dan substitusi.</li>
                    </ol>
                    <div class='highlight'>
                        <strong>Contoh Soal:</strong><br>
                        Tentukan himpunan penyelesaian dari:<br>
                        x + y = 5<br>
                        x - y = 1<br><br>
                        <strong>Penyelesaian (Eliminasi):</strong><br>
                        Jumlahkan kedua persamaan:<br>
                        (x + y) + (x - y) = 5 + 1<br>
                        2x = 6 => x = 3<br>
                        Substitusi x = 3 ke persamaan pertama:<br>
                        3 + y = 5 => y = 2.<br>
                        Jadi, HP = {(3, 2)}.
                    </div>
                ",
                'assignment_title' => 'Tugas Mandiri: Menyelesaikan SPLDV',
                'assignment_desc' => "Selesaikan sistem persamaan linear dua variabel berikut dengan menggunakan metode eliminasi atau substitusi secara lengkap:\n\n1) 2x + 3y = 12 dan x - y = 1\n2) 3x - y = 5 dan 2x + 2y = 14\n\nTuliskan langkah penyelesaian lengkap Anda pada selembar kertas atau dokumen digital, foto/scan, lalu kirimkan file jawaban Anda dalam format PDF atau gambar (JPEG/PNG) di sini.",
                'quiz_title' => 'Kuis Mandiri: Sistem Persamaan Linear Dua Variabel',
                'quiz_questions' => [
                    [
                        'q' => 'Himpunan penyelesaian dari sistem persamaan x + y = 5 dan x - y = 1 adalah...',
                        'options' => ['(3, 2)', '(2, 3)', '(4, 1)', '(1, 4)'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Jika 2x + y = 8 dan x - y = 1, maka nilai x adalah...',
                        'options' => ['2', '3', '4', '5'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Metode penyelesaian SPLDV dengan cara menghilangkan salah satu variabel disebut...',
                        'options' => ['Substitusi', 'Eliminasi', 'Grafik', 'Campuran'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Nilai y dari persamaan 3x + 2y = 12 jika diketahui nilai x = 2 adalah...',
                        'options' => ['2', '3', '4', '5'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Sistem persamaan linear dua variabel memiliki paling banyak ... solusi unik.',
                        'options' => ['Satu', 'Dua', 'Tidak terhingga', 'Nol'],
                        'answer' => '0',
                        'score' => 20
                    ]
                ]
            ],
            [
                'module_title' => 'Fungsi Kuadrat dan Grafiknya',
                'module_desc' => 'Memahami karakteristik grafik fungsi kuadrat, titik puncak, dan sumbu simetri.',
                'material_title' => 'Bahan Ajar - Fungsi Kuadrat dan Grafiknya.pdf',
                'material_html' => "
                    <h1>Fungsi Kuadrat dan Karakteristik Grafiknya</h1>
                    <p>Fungsi kuadrat adalah fungsi yang memiliki pangkat tertinggi dua pada variabelnya. Bentuk umum fungsi kuadrat adalah:</p>
                    <pre>y = f(x) = ax^2 + bx + c, dengan a &ne; 0</pre>
                    <h2>Karakteristik Grafik Fungsi Kuadrat (Parabola):</h2>
                    <ul>
                        <li>Jika <strong>a &gt; 0</strong>, parabola terbuka ke atas (memiliki titik balik minimum).</li>
                        <li>Jika <strong>a &lt; 0</strong>, parabola terbuka ke bawah (memiliki titik balik maksimum).</li>
                        <li><strong>Diskriminan (D = b^2 - 4ac)</strong> menentukan perpotongan dengan sumbu X:
                            <ul>
                                <li>D &gt; 0: memotong sumbu X di dua titik berbeda.</li>
                                <li>D = 0: menyinggung sumbu X di satu titik.</li>
                                <li>D &lt; 0: tidak memotong maupun menyinggung sumbu X.</li>
                            </ul>
                        </li>
                    </ul>
                    <h2>Rumus Penting:</h2>
                    <p>Sumbu simetri: <strong>x = -b / 2a</strong></p>
                    <p>Nilai optimum: <strong>y = -D / 4a</strong></p>
                    <p>Titik Puncak: <strong>P( -b/2a, -D/4a )</strong></p>
                ",
                'assignment_title' => 'Tugas Menggambar: Grafik Fungsi Kuadrat',
                'assignment_desc' => "Gambarlah grafik fungsi kuadrat y = x^2 - 4x + 3 pada bidang koordinat Kartesius.\n\nTentukan:\n1. Titik potong dengan sumbu X (y = 0)\n2. Titik potong dengan sumbu Y (x = 0)\n3. Koordinat titik puncak (titik balik)\n\nFoto/scan gambar tangan Anda pada kertas milimeter block atau kertas biasa, atau gunakan aplikasi grafik lalu kirimkan file gambarnya di sini.",
                'quiz_title' => 'Kuis Mandiri: Karakteristik Fungsi Kuadrat',
                'quiz_questions' => [
                    [
                        'q' => 'Bentuk umum fungsi kuadrat adalah...',
                        'options' => ['y = ax + b', 'y = ax^2 + bx + c', 'y = ax^3 + b', 'y = a/x'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Jika nilai a > 0 pada fungsi kuadrat, maka grafiknya akan...',
                        'options' => ['Terbuka ke atas', 'Terbuka ke bawah', 'Berupa garis lurus', 'Berupa lingkaran'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Rumus untuk mencari sumbu simetri x_p pada fungsi kuadrat adalah...',
                        'options' => ['-b / 2a', 'b / 2a', '-b / a', 'D / -4a'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Diskriminan dari fungsi kuadrat y = x^2 - 2x + 1 adalah...',
                        'options' => ['0', '1', '4', '-4'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Titik puncak dari grafik fungsi y = x^2 adalah...',
                        'options' => ['(0, 0)', '(1, 1)', '(0, 1)', '(1, 0)'],
                        'answer' => '0',
                        'score' => 20
                    ]
                ]
            ],
            [
                'module_title' => 'Trigonometri Dasar',
                'module_desc' => 'Mempelajari perbandingan trigonometri pada segitiga siku-siku (Sinus, Cosinus, Tangen).',
                'material_title' => 'Bahan Ajar - Pengenalan Trigonometri Segitiga.pdf',
                'material_html' => "
                    <h1>Trigonometri: Perbandingan Sisi Segitiga Siku-Siku</h1>
                    <p>Trigonometri mempelajari hubungan antara sudut dan sisi-sisi pada segitiga. Untuk sudut &theta; pada segitiga siku-siku, perbandingan dasarnya adalah:</p>
                    <div class='highlight'>
                        <ul>
                            <li><strong>Sinus (&theta;)</strong> = depan / miring (Demi)</li>
                            <li><strong>Cosinus (&theta;)</strong> = samping / miring (Kosi)</li>
                            <li><strong>Tangen (&theta;)</strong> = depan / samping (Desa)</li>
                        </ul>
                    </div>
                    <h2>Nilai Sudut Istimewa:</h2>
                    <table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%; text-align: center;'>
                        <tr style='background-color: #edf2f7;'>
                            <th>Rasio</th>
                            <th>0&deg;</th>
                            <th>30&deg;</th>
                            <th>45&deg;</th>
                            <th>60&deg;</th>
                            <th>90&deg;</th>
                        </tr>
                        <tr>
                            <td><strong>Sin</strong></td>
                            <td>0</td>
                            <td>1/2</td>
                            <td>&frac12;&radic;2</td>
                            <td>&frac12;&radic;3</td>
                            <td>1</td>
                        </tr>
                        <tr>
                            <td><strong>Cos</strong></td>
                            <td>1</td>
                            <td>&frac12;&radic;3</td>
                            <td>&frac12;&radic;2</td>
                            <td>1/2</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td><strong>Tan</strong></td>
                            <td>0</td>
                            <td>1/&radic;3</td>
                            <td>1</td>
                            <td>&radic;3</td>
                            <td>&infin;</td>
                        </tr>
                    </table>
                ",
                'assignment_title' => 'Tugas Praktis: Penerapan Trigonometri',
                'assignment_desc' => "Sebuah tangga sepanjang 5 meter disandarkan pada dinding rumah dengan sudut kemiringan 60 derajat terhadap lantai/tanah.\n\nHitunglah:\n1. Jarak kaki tangga ke dinding\n2. Tinggi ujung tangga yang menempel pada dinding\n\nJawablah dengan menuliskan rumus dan langkah perhitungan lengkap di kertas, foto, dan unggah file jawaban Anda di sini.",
                'quiz_title' => 'Kuis Mandiri: Perbandingan Trigonometri',
                'quiz_questions' => [
                    [
                        'q' => 'Nilai dari sin 30° adalah...',
                        'options' => ['0', '1/2', '1/2 √2', '1/2 √3'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Perbandingan sisi depan dengan sisi miring dalam segitiga siku-siku disebut...',
                        'options' => ['Sinus', 'Cosinus', 'Tangen', 'Cotangen'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Nilai dari cos 60° adalah...',
                        'options' => ['0', '1/2', '1/2 √2', '1'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Jika tan A = 3/4 pada sebuah segitiga siku-siku, maka panjang sisi depan dan samping berturut-turut adalah...',
                        'options' => ['3 dan 4', '4 dan 3', '3 dan 5', '4 dan 5'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Nilai dari sin 90° adalah...',
                        'options' => ['0', '1/2', '1', 'Tidak terdefinisi'],
                        'answer' => '2',
                        'score' => 20
                    ]
                ]
            ]
        ],
        'Bahasa Indonesia' => [
            [
                'module_title' => 'Teks Laporan Hasil Observasi (LHO)',
                'module_desc' => 'Menganalisis struktur dan ciri kebahasaan teks laporan hasil observasi.',
                'material_title' => 'Bahan Ajar - Struktur Teks LHO.pdf',
                'material_html' => "
                    <h1>Teks Laporan Hasil Observasi (LHO)</h1>
                    <p>Teks Laporan Hasil Observasi adalah teks yang berfungsi untuk memberikan informasi tentang suatu objek atau situasi setelah diadakannya penelitian atau pengamatan secara sistematis.</p>
                    <h2>Struktur Teks LHO:</h2>
                    <ol>
                        <li><strong>Pernyataan Umum (Klasifikasi):</strong> Berisi pembuka atau pengantar hal yang akan dilaporkan.</li>
                        <li><strong>Deskripsi Bagian:</strong> Berisi penjelasan detail mengenai objek yang diamati (ciri fisik, habitat, perilaku).</li>
                        <li><strong>Deskripsi Manfaat:</strong> Menjelaskan manfaat atau fungsi dari objek yang dilaporkan bagi kehidupan manusia.</li>
                    </ol>
                    <h2>Ciri Kebahasaan Teks LHO:</h2>
                    <ul>
                        <li>Menggunakan kata benda (Nomina) sebagai subjek utama.</li>
                        <li>Menggunakan kata kerja kopula (seperti: adalah, merupakan, yaitu).</li>
                        <li>Menggunakan kalimat deskriptif untuk memperjelas keadaan objek.</li>
                        <li>Ditulis secara objektif tanpa prasangka.</li>
                    </ul>
                ",
                'assignment_title' => 'Tugas Praktik: Menulis Teks LHO',
                'assignment_desc' => "Lakukan pengamatan singkat terhadap salah satu objek di rumah atau sekolah Anda (misalnya: tanaman hias, hewan peliharaan, atau ruang kelas).\n\nTulislah laporan hasil observasi sepanjang minimal 300 kata yang terbagi secara jelas dalam 3 struktur (Pernyataan Umum, Deskripsi Bagian, dan Deskripsi Manfaat).\n\nTulis laporan di Microsoft Word atau tulis tangan, simpan sebagai file PDF/DOCX, lalu kirimkan file tersebut di sini.",
                'quiz_title' => 'Kuis Mandiri: Memahami Teks LHO',
                'quiz_questions' => [
                    [
                        'q' => 'Struktur pertama dari teks LHO adalah...',
                        'options' => ['Pernyataan umum', 'Deskripsi bagian', 'Deskripsi manfaat', 'Kesimpulan'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Teks laporan hasil observasi harus ditulis berdasarkan...',
                        'options' => ['Opini penulis', 'Fakta hasil pengamatan', 'Cerita fiksi', 'Desas-desus masyarakat'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Kalimat yang mendeskripsikan ciri-ciri fisik objek secara rinci disebut...',
                        'options' => ['Pernyataan umum', 'Deskripsi bagian', 'Deskripsi manfaat', 'Deskripsi umum'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Salah satu ciri kebahasaan teks LHO adalah penggunaan verba relasional seperti...',
                        'options' => ['Makan', 'Adalah', 'Pergi', 'Menulis'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Manakah di bawah ini yang merupakan kalimat objektif?',
                        'options' => ['Mawar itu sangat indah sekali.', 'Bunga mawar memiliki kelopak berwarna merah.', 'Mungkin mawar itu berbau harum.', 'Semua orang menyukai bunga mawar.'],
                        'answer' => '1',
                        'score' => 20
                    ]
                ]
            ],
            [
                'module_title' => 'Teks Eksposisi: Menyusun Argumen Logis',
                'module_desc' => 'Memahami struktur teks eksposisi (tesis, argumen, penegasan ulang) dan menyusun argumen yang kuat.',
                'material_title' => 'Bahan Ajar - Menulis Teks Eksposisi.pdf',
                'material_html' => "
                    <h1>Teks Eksposisi: Menyusun Gagasan Secara Logis</h1>
                    <p>Teks eksposisi adalah teks nonfiksi yang memuat penjelasan tentang suatu informasi, usulan, atau pendapat penulis yang didukung oleh fakta-fakta kuat.</p>
                    <h2>Struktur Teks Eksposisi:</h2>
                    <ol>
                        <li><strong>Tesis (Pernyataan Pendapat):</strong> Pengenalan isu, masalah, atau pandangan umum penulis tentang topik.</li>
                        <li><strong>Argumentasi:</strong> Alasan atau bukti yang digunakan untuk mendukung pendapat atau tesis penulis (fakta, statistik, opini ahli).</li>
                        <li><strong>Penegasan Ulang:</strong> Perumusan kembali pendapat penulis secara ringkas dan sering kali disertai rekomendasi/solusi.</li>
                    </ol>
                    <h2>Ciri Kebahasaan:</h2>
                    <ul>
                        <li>Menggunakan kata-kata persuasif (harus, sebaiknya, penting).</li>
                        <li>Menggunakan konjungsi kausalitas (karena, sebab, oleh karena itu).</li>
                        <li>Menggunakan istilah teknis terkait topik.</li>
                    </ul>
                ",
                'assignment_title' => 'Tugas Menulis: Paragraf Eksposisi Persuasif',
                'assignment_desc' => "Pilihlah salah satu tema berikut:\n- Pentingnya Literasi Digital bagi Pelajar\n- Bahaya Sampah Plastik terhadap Lingkungan\n\nTulislah sebuah teks eksposisi singkat sepanjang 200-250 kata. Pastikan teks memuat tesis yang jelas, minimal dua argumen logis yang disertai fakta pendukung, serta satu penegasan ulang di akhir teks.\n\nUnggah tulisan Anda dalam format file dokumen (PDF/DOCX) di sini.",
                'quiz_title' => 'Kuis Mandiri: Struktur Eksposisi',
                'quiz_questions' => [
                    [
                        'q' => 'Bagian pembuka teks eksposisi yang berisi sudut pandang penulis disebut...',
                        'options' => ['Argumentasi', 'Tesis', 'Penegasan ulang', 'Orientasi'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Tujuan utama dari teks eksposisi adalah...',
                        'options' => ['Menghibur pembaca', 'Menjelaskan informasi atau pengetahuan', 'Menceritakan kejadian fiksi', 'Membujuk untuk membeli produk'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Unsur penjelas untuk mendukung tesis penulis dalam teks eksposisi disebut...',
                        'options' => ['Orientasi', 'Argumentasi', 'Rekomendasi', 'Penutup'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Penegasan ulang dalam teks eksposisi biasanya diletakkan di...',
                        'options' => ['Awal paragraf', 'Akhir paragraf', 'Tengah paragraf', 'Judul teks'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Manakah kata hubung (konjungsi) yang menyatakan sebab-akibat?',
                        'options' => ['Dan', 'Karena', 'Tetapi', 'Serta'],
                        'answer' => '1',
                        'score' => 20
                    ]
                ]
            ],
            [
                'module_title' => 'Teks Anekdot: Kritik Sosial Bernada Humor',
                'module_desc' => 'Meringkas materi teks anekdot dan membedakannya dari humor biasa.',
                'material_title' => 'Bahan Ajar - Anekdot vs Humor.pdf',
                'material_html' => "
                    <h1>Teks Anekdot: Menyampaikan Kritik Melalui Humor</h1>
                    <p>Teks anekdot adalah cerita singkat yang menarik karena lucu dan mengesankan, biasanya mengenai orang penting atau terkenal dan berdasarkan kejadian yang sebenarnya. Namun, fungsi utamanya adalah untuk menyampaikan sindiran atau kritik sosial.</p>
                    <h2>Struktur Teks Anekdot:</h2>
                    <ol>
                        <li><strong>Abstraksi:</strong> Isyarat awal tentang apa yang akan diceritakan.</li>
                        <li><strong>Orientasi:</strong> Bagian yang menceritakan latar belakang terjadinya peristiwa.</li>
                        <li><strong>Krisis:</strong> Bagian terjadinya hal unik, janggal, atau masalah yang lucu.</li>
                        <li><strong>Reaksi:</strong> Bagaimana tokoh menyelesaikan krisis/masalah.</li>
                        <li><strong>Koda:</strong> Bagian akhir cerita yang berisi kesimpulan atau makna cerita.</li>
                    </ol>
                    <h2>Perbedaan Anekdot vs Humor:</h2>
                    <table border='1' cellpadding='5' style='border-collapse: collapse; width: 100%;'>
                        <tr style='background-color: #edf2f7;'>
                            <th>Aspek</th>
                            <th>Teks Anekdot</th>
                            <th>Humor Biasa</th>
                        </tr>
                        <tr>
                            <td><strong>Tujuan</strong></td>
                            <td>Kritik/sindiran sosial</td>
                            <td>Hanya menghibur</td>
                        </tr>
                        <tr>
                            <td><strong>Tokoh</strong></td>
                            <td>Faktual/nyata/pejabat</td>
                            <td>Fiktif/umum</td>
                        </tr>
                        <tr>
                            <td><strong>Struktur</strong></td>
                            <td>Sangat terstruktur</td>
                            <td>Bebas</td>
                        </tr>
                    </table>
                ",
                'assignment_title' => 'Tugas Kreatif: Menulis Teks Anekdot',
                'assignment_desc' => "Buatlah sebuah teks anekdot yang bertema \"Layanan Publik\" atau \"Kehidupan Sekolah\".\n\nPastikan teks Anda memiliki kelucuan, mengandung kritik atau pesan moral, serta mengikuti struktur anekdot yang lengkap (Abstraksi, Orientasi, Krisis, Reaksi, Koda).\n\nTulis karya Anda pada lembar dokumen, simpan sebagai PDF atau file Word, lalu kirimkan file tersebut di sini.",
                'quiz_title' => 'Kuis Mandiri: Memahami Teks Anekdot',
                'quiz_questions' => [
                    [
                        'q' => 'Bagian teks anekdot yang menunjukkan puncak konflik atau kelucuan disebut...',
                        'options' => ['Abstraksi', 'Orientasi', 'Krisis', 'Reaksi'],
                        'answer' => '2',
                        'score' => 20
                    ],
                    [
                        'q' => 'Perbedaan utama antara anekdot dengan humor biasa adalah anekdot mengandung...',
                        'options' => ['Tokoh terkenal', 'Kritik sosial atau pesan moral', 'Cerita yang sangat panjang', 'Gambar komik'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Bagian akhir cerita yang berisi kesimpulan atau penutup cerita anekdot disebut...',
                        'options' => ['Koda', 'Reaksi', 'Krisis', 'Orientasi'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Bagian reaksi dalam teks anekdot berisi...',
                        'options' => ['Pengenalan tokoh utama', 'Penyelesaian masalah atau krisis', 'Isyarat awal cerita', 'Pesan terselubung'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => '"Seorang pejabat sedang berpidato di depan rakyat..." Kalimat tersebut biasanya berada pada bagian...',
                        'options' => ['Abstraksi / Orientasi', 'Krisis', 'Koda', 'Reaksi'],
                        'answer' => '0',
                        'score' => 20
                    ]
                ]
            ]
        ],
        'Bahasa Inggris' => [
            [
                'module_title' => 'Descriptive Text: Describing People and Places',
                'module_desc' => 'Understanding the social function, text structure, and language features of descriptive texts.',
                'material_title' => 'Learning Material - Descriptive Text Guide.pdf',
                'material_html' => "
                    <h1>Descriptive Text: How to Describe Something</h1>
                    <p>Descriptive text is a text which says what a person or a thing is like. Its purpose is to describe and reveal a particular person, place, or thing.</p>
                    <h2>Generic Structure:</h2>
                    <ol>
                        <li><strong>Identification:</strong> Introducing where the object is or what it is.</li>
                        <li><strong>Description:</strong> Describing its properties, features, appearance, or qualities.</li>
                    </ol>
                    <h2>Language Features:</h2>
                    <ul>
                        <li>Specific participant (e.g. Borobudur Temple, My Cat, Uncle Jim).</li>
                        <li>Use of adjectives to describe qualities (e.g. beautiful, tall, historic).</li>
                        <li>Use of Simple Present Tense (e.g. It has 4 legs, Borobudur is located in Magelang).</li>
                    </ul>
                ",
                'assignment_title' => 'Descriptive Writing Assignment',
                'assignment_desc' => "Write a descriptive text about your favorite tourist destination in Indonesia.\n\nRequirements:\n- Length: 150-200 words.\n- Structure: Must contain clear Identification and Description paragraphs.\n- Grammar: Use the Simple Present Tense and at least 5 different adjectives.\n\nType your writing in a document file (PDF/Word) and upload the file as your submission.",
                'quiz_title' => 'Quiz 1: Descriptive Text Concepts',
                'quiz_questions' => [
                    [
                        'q' => 'What is the main purpose of a descriptive text?',
                        'options' => ['To tell a story', 'To describe a specific person, place, or thing', 'To persuade readers to buy something', 'To explain steps to make something'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'The two main generic structures of descriptive text are...',
                        'options' => ['Orientation and Resolution', 'Identification and Description', 'Thesis and Arguments', 'Goal and Materials'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Which tense is mostly used in descriptive texts?',
                        'options' => ['Simple Past Tense', 'Simple Present Tense', 'Present Continuous Tense', 'Past Perfect Tense'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => '\"Borobudur is a magnificent temple.\" The word \"magnificent\" is an example of...',
                        'options' => ['Noun', 'Verb', 'Adjective', 'Adverb'],
                        'answer' => '2',
                        'score' => 20
                    ],
                    [
                        'q' => 'The identification paragraph usually answers which question?',
                        'options' => ['What the object looks like', 'Where or what the object is', 'Why the writer likes it', 'How to reach the location'],
                        'answer' => '1',
                        'score' => 20
                    ]
                ]
            ],
            [
                'module_title' => 'Narrative Text: Fables and Folklores',
                'module_desc' => 'Learning how stories are structured (Orientation, Complication, Resolution) and analyzed for moral values.',
                'material_title' => 'Learning Material - Narrative Text and Fables.pdf',
                'material_html' => "
                    <h1>Narrative Text: Storytelling and Folklores</h1>
                    <p>Narrative text is an imaginative story to entertain people. It deals with problematic events which lead to a crisis or turning point of some kind, which in turn finds a resolution.</p>
                    <h2>Generic Structure:</h2>
                    <ol>
                        <li><strong>Orientation:</strong> Introduces the participants, time, and place (Who, When, Where).</li>
                        <li><strong>Complication:</strong> The story develops, and a problem arises.</li>
                        <li><strong>Resolution:</strong> The problem is solved, either happily or sadly.</li>
                        <li><strong>Reorientation (Optional):</strong> A closing remark or moral lesson from the writer.</li>
                    </ol>
                    <h2>Language Features:</h2>
                    <ul>
                        <li>Use of Simple Past Tense (e.g., lived, walked, ate).</li>
                        <li>Time connectives (e.g., Once upon a time, suddenly, then).</li>
                        <li>Action verbs.</li>
                    </ul>
                ",
                'assignment_title' => 'Narrative Analysis Assignment',
                'assignment_desc' => "Choose a well-known folklore from Indonesia (for example: Malin Kundang, Sangkuriang, or Keong Mas).\n\nAnalyze the story by writing:\n1. A brief summary of the plot (100 words).\n2. The main Complication (problem) and Resolution.\n3. The moral lesson of the story.\n\nWrite your analysis in a document, save it as a PDF or Word file, and submit it here.",
                'quiz_title' => 'Quiz 2: Narrative Text Elements',
                'quiz_questions' => [
                    [
                        'q' => 'The part of the story that introduces the characters, setting, and time is called...',
                        'options' => ['Complication', 'Orientation', 'Resolution', 'Reorientation'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'The climax or the main problem in a narrative text is called...',
                        'options' => ['Orientation', 'Complication', 'Resolution', 'Reorientation'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Narrative texts mostly use which tense?',
                        'options' => ['Simple Present Tense', 'Simple Past Tense', 'Future Tense', 'Present Perfect Tense'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'What is the resolution in a story?',
                        'options' => ['The introduction of characters', 'The part where the problem is solved', 'The moral value statement', 'The list of characters'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'Which of the following is a time connective word?',
                        'options' => ['Beautiful', 'Suddenly', 'Because', 'Walked'],
                        'answer' => '1',
                        'score' => 20
                    ]
                ]
            ],
            [
                'module_title' => 'Procedure Text: How to Make or Do Something',
                'module_desc' => 'Mastering the structure (Goal, Materials/Equipment, Steps) and imperative sentences in instructions.',
                'material_title' => 'Learning Material - Writing Procedure Texts.pdf',
                'material_html' => "
                    <h1>Procedure Text: Giving Instructions</h1>
                    <p>Procedure text is a text that explains how to make or do something through a sequence of actions or steps.</p>
                    <h2>Generic Structure:</h2>
                    <ol>
                        <li><strong>Goal/Aim:</strong> What you intend to make or achieve (e.g. How to Make Fried Rice).</li>
                        <li><strong>Materials/Ingredients:</strong> List of things needed to complete the task.</li>
                        <li><strong>Steps/Method:</strong> The sequential actions to be taken in order.</li>
                    </ol>
                    <h2>Language Features:</h2>
                    <ul>
                        <li>Use of imperative sentences (e.g. Cut the onions, Boil the water).</li>
                        <li>Use of sequence connectives (e.g. First, Second, Next, Finally).</li>
                        <li>Use of Action Verbs (e.g. stir, pour, heat).</li>
                    </ul>
                ",
                'assignment_title' => 'Writing a Recipe Procedure',
                'assignment_desc' => "Write a procedure text explaining how to make your favorite traditional Indonesian drink or food.\n\nYour recipe must include:\n1. Goal/Title\n2. Materials & Ingredients (with measurements)\n3. Clear, numbered steps using imperative verbs.\n\nSave your recipe in a document file (PDF or Word) and upload it here.",
                'quiz_title' => 'Quiz 3: Procedure Text Rules',
                'quiz_questions' => [
                    [
                        'q' => 'What is the social function of a procedure text?',
                        'options' => ['To describe a person', 'To explain steps to make or do something', 'To retell a past event', 'To criticize a policy'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => 'The three main components of a procedure text are...',
                        'options' => ['Orientation, Event, Reorientation', 'Goal, Materials, Steps', 'Thesis, Argument, Reiteration', 'Identification, Description, Conclusion'],
                        'answer' => '1',
                        'score' => 20
                    ],
                    [
                        'q' => '\"Cut the onions into small pieces.\" The word \"Cut\" is an example of...',
                        'options' => ['Imperative verb', 'Adjective', 'Noun', 'Conjunction'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'Which of the following words is a sequence adverb?',
                        'options' => ['First', 'Carefully', 'Good', 'Water'],
                        'answer' => '0',
                        'score' => 20
                    ],
                    [
                        'q' => 'The materials section contains...',
                        'options' => ['What is needed to complete the task', 'The final result of the activity', 'The steps to follow', 'The location of the kitchen'],
                        'answer' => '0',
                        'score' => 20
                    ]
                ]
            ]
        ]
    ];

    // 4. Proses Pembuatan Data LMS
    echo "<b>Proses pembuatan Course, Modul, Materi PDF, Tugas, dan Kuis:</b><br>";
    $colors = ['indigo', 'emerald', 'rose'];
    $colorIndex = 0;

    // Pre-select 3 distinct teachers for the 3 subjects
    $assignedTeacherIds = [];
    $subjectTeachers = [];
    
    foreach (array_keys($dataTemplates) as $subjName) {
        $subject = Subject::where('school_id', $school->id)
            ->where('subject_name', 'like', '%' . $subjName . '%')
            ->where('is_active', true)
            ->first() ?? Subject::where('school_id', $school->id)->where('is_active', true)->first();
            
        if (!$subject) continue;
        
        $teacher = null;
        
        // 1. Try to find a competent teacher who isn't assigned yet and has a login account
        $teacher = Teacher::where('school_id', $school->id)
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->whereHas('user')
            ->whereNotIn('id', $assignedTeacherIds)
            ->whereHas('competentSubjects', function($q) use ($subject) {
                $q->where('subject_id', $subject->id);
            })
            ->first();
            
        // 2. Fallback to any teacher assigned to teach this subject who isn't assigned yet and has account
        if (!$teacher) {
            $teacher = Teacher::where('school_id', $school->id)
                ->where('is_active', true)
                ->whereNotNull('user_id')
                ->whereHas('user')
                ->whereNotIn('id', $assignedTeacherIds)
                ->where(function($query) use ($subject) {
                    $query->whereHas('teachingAssignments', function($q) use ($subject) {
                        $q->where('subject_id', $subject->id);
                    })->orWhereHas('schedules', function($q) use ($subject) {
                        $q->where('subject_id', $subject->id);
                    });
                })
                ->first();
        }
        
        // 3. Fallback to any active teacher with user account who isn't assigned yet
        if (!$teacher) {
            $teacher = Teacher::where('school_id', $school->id)
                ->where('is_active', true)
                ->whereNotNull('user_id')
                ->whereHas('user')
                ->whereNotIn('id', $assignedTeacherIds)
                ->first();
        }
        
        if ($teacher) {
            $assignedTeacherIds[] = $teacher->id;
            $subjectTeachers[$subjName] = [
                'subject' => $subject,
                'teacher' => $teacher
            ];
        }
    }

    foreach ($dataTemplates as $subjectName => $modulesData) {
        if (!isset($subjectTeachers[$subjectName])) {
            echo "Peringatan: Guru atau Mapel untuk '{$subjectName}' tidak dapat diinisialisasi. Dilewati.<br>";
            continue;
        }
        
        $subject = $subjectTeachers[$subjectName]['subject'];
        $teacher = $subjectTeachers[$subjectName]['teacher'];

        // a. Buat/Update Penugasan Mengajar (Teaching Assignment) agar kelas terhubung ke Guru di dashboard "Kelas Saya"
        TeachingAssignment::updateOrCreate(
            [
                'classroom_id' => $classroom->id,
                'subject_id' => $subject->id,
                'academic_year_id' => $activeYear->id,
            ],
            [
                'teacher_id' => $teacher->id,
                'semester_id' => $activeSemester->id,
                'hours_per_week' => 4,
                'is_main_teacher' => true,
                'is_active' => true,
            ]
        );

        // Jika Mapel adalah Matematika, atur guru ini sebagai Wali Kelas (homeroom_teacher_id) untuk menghindari error 403 pada Rapor
        if ($subjectName === 'Matematika') {
            $classroom->homeroom_teacher_id = $teacher->id;
            $classroom->save();
            echo "    + Mengatur Guru {$teacher->full_name} sebagai Wali Kelas untuk {$classroom->class_name}<br>";
        }

        // b. Buat/Update Jadwal Pelajaran (Schedule) agar muncul di "Jadwal Mengajar" di hari Sabtu
        $saturdaySlots = TimeSlot::where('school_id', $school->id)
            ->where('day_of_week', 'saturday')
            ->where('is_teaching_slot', true)
            ->orderBy('slot_order')
            ->get();

        $subjIndex = array_search($subjectName, array_keys($dataTemplates));
        $timeSlot = $saturdaySlots->get($subjIndex) ?? $saturdaySlots->first();
        
        if (!$timeSlot) {
            $timeSlot = TimeSlot::where('school_id', $school->id)
                ->where('is_teaching_slot', true)
                ->first();
        }
            
        if ($timeSlot) {
            Schedule::updateOrCreate(
                [
                    'classroom_id' => $classroom->id,
                    'subject_id' => $subject->id,
                    'academic_year_id' => $activeYear->id,
                    'semester_id' => $activeSemester->id,
                    'day_of_week' => 'saturday',
                    'time_slot_id' => $timeSlot->id,
                ],
                [
                    'school_id' => $school->id,
                    'teacher_id' => $teacher->id,
                    'start_time' => $timeSlot->start_time,
                    'end_time' => $timeSlot->end_time,
                    'duration_slots' => 1,
                    'semester' => 'ganjil',
                ]
            );
            echo "    + Jadwal Mengajar Dibuat di Hari Sabtu: " . $timeSlot->slot_name . " (" . substr($timeSlot->start_time, 0, 5) . " - " . substr($timeSlot->end_time, 0, 5) . ")<br>";
        } else {
            echo "    + Peringatan: Slot waktu hari Sabtu untuk mapel '{$subjectName}' tidak ditemukan.<br>";
        }

        $courseCode = 'SIM-LMS-' . strtoupper(Str::random(6));

        // c. Buat LmsCourse
        $course = LmsCourse::create([
            'school_id' => $school->id,
            'teacher_id' => $teacher->id,
            'subject_id' => $subject->id,
            'classroom_id' => $classroom->id,
            'semester_id' => $activeSemester->id,
            'code' => $courseCode,
            'course_name' => "[Simulasi] {$subject->subject_name} ({$classroom->class_name})",
            'description' => "Mata pelajaran simulasi {$subject->subject_name} untuk kelas {$classroom->class_name} TP. {$activeYear->year}",
            'status' => 'active',
            'is_published' => true,
            'is_active' => true,
        ]);

        $teacherEmail = $teacher->user->email ?? 'tidak ada email';
        echo "* <b>Course Dibuat</b>: [{$course->code}] {$course->course_name} (Guru: {$teacher->full_name} - Email Login: <b>{$teacherEmail}</b>)<br>";

        // b. Buat LmsClass & Enroll Students
        $lmsClass = LmsClass::create([
            'course_id' => $course->id,
            'classroom_id' => $classroom->id,
            'school_id' => $school->id,
            'status' => 'active',
        ]);

        foreach ($studentIds as $studentId) {
            LmsEnrollment::firstOrCreate(
                ['lms_class_id' => $lmsClass->id, 'student_id' => $studentId],
                ['status' => 'enrolled', 'enrolled_at' => now()]
            );
        }

        // c. Buat Modul-modul
        foreach ($modulesData as $idx => $modDef) {
            $seq = $idx + 1;
            $module = LmsModule::create([
                'course_id' => $course->id,
                'title' => "Modul {$seq}: {$modDef['module_title']}",
                'description' => $modDef['module_desc'],
                'sequence' => $seq,
                'is_active' => true,
            ]);

            echo "  - Modul {$seq}: {$module->title}<br>";

            // d. Buat Materi Berupa File PDF
            $htmlContent = "
            <html>
            <head>
                <style>
                    body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; line-height: 1.6; color: #333; padding: 25px; }
                    h1 { color: #1a365d; border-bottom: 2px solid #2b6cb0; padding-bottom: 8px; font-size: 22px; font-weight: bold; margin-bottom: 20px; }
                    h2 { color: #2c5282; margin-top: 25px; margin-bottom: 12px; font-size: 16px; font-weight: bold; }
                    p { margin-bottom: 12px; text-align: justify; font-size: 13px; }
                    ul, ol { margin-bottom: 15px; padding-left: 20px; font-size: 13px; }
                    li { margin-bottom: 6px; }
                    pre { background: #f7fafc; border: 1px solid #edf2f7; padding: 10px; border-radius: 4px; font-family: monospace; font-size: 12px; margin: 15px 0; }
                    table { border-collapse: collapse; width: 100%; margin: 15px 0; font-size: 12px; }
                    table, th, td { border: 1px solid #cbd5e0; }
                    th, td { padding: 8px; text-align: center; }
                    th { background-color: #ebf8ff; color: #2b6cb0; font-weight: bold; }
                    .footer { margin-top: 50px; border-top: 1px solid #e2e8f0; padding-top: 12px; font-size: 10px; color: #a0aec0; text-align: center; }
                    .highlight { background-color: #ebf8ff; border-left: 4px solid #3182ce; padding: 12px 15px; margin: 20px 0; border-radius: 0 4px 4px 0; font-size: 13px; }
                </style>
            </head>
            <body>
                <h1>Materi: {$modDef['module_title']}</h1>
                <div class='highlight'>
                    <strong>Mata Pelajaran:</strong> {$subject->subject_name}<br>
                    <strong>Sekolah:</strong> SMA Swasta Pembda 1 Gunungsitoli<br>
                    <strong>Tahun Ajaran:</strong> TP. {$activeYear->year} (Semester Ganjil)
                </div>
                {$modDef['material_html']}
                <div class='footer'>
                    Dokumen Pembelajaran LMS Perguruan Pembda Nias &copy; 2026. Hak Cipta Dilindungi Undang-Undang.
                </div>
            </body>
            </html>
            ";

            // Generate PDF using Barryvdh\DomPDF\Facade\Pdf
            $pdf = Pdf::loadHTML($htmlContent);
            $pdfContent = $pdf->output();

            $relativeDirectory = 'materials';
            if (!Storage::disk('public')->exists($relativeDirectory)) {
                Storage::disk('public')->makeDirectory($relativeDirectory);
            }

            $safeFilename = Str::slug($subject->subject_name) . '_modul_' . $seq . '_' . time() . '.pdf';
            $filePath = $relativeDirectory . '/' . $safeFilename;
            Storage::disk('public')->put($filePath, $pdfContent);
            $fileSize = strlen($pdfContent);

            LmsMaterial::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'module_id' => $module->id,
                    'title' => "Bahan Ajar - {$modDef['module_title']}.pdf",
                ],
                [
                'title' => "Bahan Ajar - {$modDef['module_title']}.pdf",
                'content' => $modDef['module_desc'],
                'material_type' => 'pdf',
                'file_path' => $filePath,
                'file_url' => Storage::disk('public')->url($filePath),
                'file_size' => $fileSize,
                'order_number' => 1,
                'is_published' => true,
            ]);

            echo "    + Materi PDF disimpan di: {$filePath} ({$fileSize} bytes)<br>";

            // e. Buat Tugas (Assignment) tipe 'file_text' (Isian + Kirim File)
            LmsAssignment::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'module_id' => $module->id,
                    'title' => "{$modDef['assignment_title']}",
                ],
                [
                'description' => $modDef['assignment_desc'],
                'assignment_type' => 'file_text',
                'deadline' => now()->addDays(7),
                'max_score' => 100,
                'is_published' => true,
                'allow_resubmit' => true,
                'max_resubmissions' => 3,
            ]);
            echo "    + Tugas Dibuat (Tipe: File + Teks)<br>";

            // f. Buat Kuis (Quiz) dengan 5 Soal
            $quiz = LmsQuiz::create([
                'course_id' => $course->id,
                'module_id' => $module->id,
                'title' => "{$modDef['quiz_title']}",
                'description' => "Kerjakan kuis ini untuk mengukur tingkat pemahaman Anda pada materi Modul {$seq}.",
                'time_limit' => 20,
                'passing_score' => 70,
                'is_published' => true,
            ]);

            $totalScore = 0;
            foreach ($modDef['quiz_questions'] as $qIdx => $qDef) {
                LmsQuizQuestion::create([
                    'quiz_id' => $quiz->id,
                    'question' => $qDef['q'],
                    'question_type' => 'multiple_choice',
                    'options' => $qDef['options'],
                    'correct_answer' => $qDef['answer'], // index string
                    'order_number' => $qIdx + 1,
                    'score' => $qDef['score'],
                ]);
                $totalScore += $qDef['score'];
            }

            $quiz->update(['total_score' => $totalScore]);
            echo "    + Kuis Dibuat (5 Soal, Total Skor: {$totalScore})<br>";
        }
        echo "<br>";
    }

    echo "<h3>Selesai!</h3> Semua data simulasi LMS untuk 3 Mata Pelajaran, 3 Modul per Pelajaran, Tugas tipe Isian+Kirim File, Materi PDF, dan Kuis 5 Soal berhasil diinisialisasi untuk SMA Pembda 1 Gunungsitoli.";
} catch (\Exception $e) {
    echo "<h3>Error Terjadi:</h3> " . $e->getMessage() . "<br>";
    echo "File: " . $e->getFile() . " (Baris: " . $e->getLine() . ")<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
