<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Survey;
use App\Models\School;
use App\Models\SurveyQuestion;

class SurveySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the SMK Swasta Pembda Nias
        $smk = School::where('type', 'SMK')->first();
        
        if (!$smk) {
            $this->command->warn('Sekolah SMK tidak ditemukan. Seeding survei kejuruan dibatalkan.');
            return;
        }

        // 1. Create Survey for Guru (Self-Evaluation & Spirit)
        $surveyGuru = Survey::updateOrCreate(
            [
                'title' => 'Survei Kesiapan dan Penerapan Jiwa Kejuruan (Vocational Spirit) Guru SMKS Swasta Pembda Nias',
                'school_id' => $smk->id,
            ],
            [
                'description' => 'Latar Belakang & Tujuan: Survei ini diselenggarakan untuk memotret kesiapan, filosofi pengajaran, serta komitmen nyata Bapak/Ibu Guru dalam mewujudkan Visi Pendidikan Kejuruan di SMKS Swasta Pembda Nias. Hasil potret kuesioner ini akan digunakan secara objektif sebagai salah satu instrumen utama dalam melakukan evaluasi kinerja, pemetaan kompetensi, serta penentuan kebijakan Pembagian Tugas Mengajar & Penugasan Struktural untuk Tahun Pelajaran (TP) 2026/2027. Harap berikan jawaban yang jujur, objektif, dan analisis yang kritis demi kemajuan bersama.',
                'target_respondent' => 'guru',
                'status' => 'active',
            ]
        );

        $questionsGuru = [
            // Bagian 1: Visi & Peran Guru (Untuk Semua Guru)
            [
                'question_text' => 'Dalam menyusun metode pembelajaran, seberapa sering Anda menitikberatkan pada penyelesaian seluruh materi kurikulum teoretis dibanding melatih keterampilan praktis yang dibutuhkan dunia kerja nyata?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 1
            ],
            [
                'question_text' => 'Bagaimana Anda menilai keberhasilan lulusan SMK: Apakah siswa yang langsung membuka usaha mandiri berskala mikro/kecil memiliki tingkat kesuksesan yang setara dengan mereka yang diterima bekerja di industri besar?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 2
            ],
            [
                'question_text' => 'Tanggung jawab profesional dan moral seorang pendidik SMK dianggap selesai sepenuhnya ketika siswa lulus dengan nilai rapor di atas KKM. Seberapa besar Anda menyetujui pernyataan tersebut?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 3
            ],
            [
                'question_text' => 'Apakah pembelajaran yang Anda berikan saat ini sudah secara konkret membekali siswa dengan mentalitas tangguh untuk berani mengambil risiko usaha, ataukah masih sebatas teori kewirausahaan di atas kertas?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 4
            ],

            // Bagian 2: Evaluasi Fasilitas, Manajemen, & Kebijakan Sekolah (Untuk Semua Guru)
            [
                'question_text' => 'Bagaimana Anda menilai keberpihakan kebijakan manajemen sekolah saat ini terhadap pengembangan Unit Produksi dan Teaching Factory: Apakah sekolah sudah memberikan fleksibilitas operasional yang cukup, ataukah masih terhambat oleh birokrasi internal?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 5
            ],
            [
                'question_text' => 'Sejauh mana kualitas dan kuantitas fasilitas serta bahan praktik di bengkel/lab sekolah kita saat ini dinilai memadai untuk melatih siswa menghasilkan produk berstandar industri nyata?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 6
            ],
            [
                'question_text' => 'Dalam pengambilan keputusan terkait kurikulum dan inovasi pembelajaran, seberapa sering Anda merasa manajemen sekolah memberikan ruang/dukungan bagi guru untuk berimprovisasi di luar prosedur standar?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 7
            ],

            // Bagian 3: Evaluasi Khusus Akreditasi, Hasil Pelatihan, & Turning Vision to Action (Untuk Semua Guru)
            [
                'question_text' => 'Menurut Anda pribadi, seberapa sering penyusunan perangkat administrasi pembelajaran di sekolah kita didasarkan pada kebutuhan riil siswa saat kerja/magang, dibanding sekadar tuntutan formalitas agar sekolah mendapat skor akreditasi tinggi?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 8
            ],
            [
                'question_text' => 'Setelah mengikuti program pelatihan atau magang industri yang dibiayai sekolah/pemerintah, seberapa konsisten Anda diwajibkan oleh sistem sekolah untuk menerapkan ilmu baru tersebut secara konkret ke dalam materi praktik siswa?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 9
            ],
            [
                'question_text' => 'Di tengah banyaknya visi-misi besar sekolah kita (seperti mewujudkan sekolah wirausaha dan TEFA), seberapa sering rencana-rencana tersebut berhenti di tingkat rapat atau proposal saja karena kendala "Turning Vision into Action" (kesulitan memulai aksi nyata)?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 10
            ],

            [
                'question_text' => 'Seberapa aktif Anda memanfaatkan atau memperkenalkan teknologi digital terkini (seperti AI, IoT, software simulasi, atau platform digital) dalam proses pembelajaran untuk menyesuaikan dengan tren dunia industri saat ini?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => null,
                'order' => 11
            ],

            // Bagian 4: Khusus Guru Kejuruan (Produktif)
            [
                'question_text' => 'Ketika siswa menggunakan peralatan praktik sekolah untuk mencoba inovasi baru yang berisiko merusak alat tersebut, seberapa sering Anda memilih membatasi eksplorasi mereka demi menjaga keamanan inventaris?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'kejuruan',
                'order' => 12
            ],
            [
                'question_text' => 'Menurut pandangan jujur Anda, apakah pelaksanaan Teaching Factory (TeFa) di unit sekolah kita sudah benar-benar mencerminkan alur kerja industri nyata, ataukah baru sebatas formalitas perubahan nama jadwal praktik kelas?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'kejuruan',
                'order' => 13
            ],
            [
                'question_text' => 'Seberapa sering Anda melibatkan Unit Produksi sekolah sebagai wadah nyata bagi siswa untuk belajar mengelola keuangan usaha, melayani pelanggan, dan menanggung risiko rugi secara mandiri?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'kejuruan',
                'order' => 14
            ],
            [
                'question_text' => 'Dalam menegakkan aturan budaya kerja industri (seperti SOP dan kedisiplinan), seberapa sering Anda memberikan toleransi atas pelanggaran siswa dengan alasan "mereka masih dalam tahap belajar"?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'kejuruan',
                'order' => 15
            ],
            [
                'question_text' => 'Dalam mengeksekusi proyek Teaching Factory atau kerja sama industri yang menuntut tenggat waktu ketat, manakah yang paling menggambarkan respon kerja Anda terhadap tuntutan kecepatan dunia usaha?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'kejuruan',
                'order' => 16
            ],

            // Bagian 5: Khusus Guru Umum (Non-Kejuruan)
            [
                'question_text' => 'Saat mengajarkan mata pelajaran umum (Matematika/Bahasa/dll), seberapa sering Anda mengalami kesulitan untuk merelasikan konsep teori yang Anda ampu dengan keahlian produktif siswa di bengkel/lab?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'umum',
                'order' => 17
            ],
            [
                'question_text' => 'Apakah Anda secara aktif berkolaborasi dengan guru kejuruan untuk menyelaraskan materi ajar umum agar dapat langsung membantu memecahkan masalah praktis yang dihadapi siswa saat praktik kejuruan?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'umum',
                'order' => 18
            ],
            [
                'question_text' => 'Saya meyakini bahwa mata pelajaran umum yang saya ampu memiliki kontribusi langsung dalam melatih logika berpikir logis siswa untuk menganalisis peluang usaha di bidang kejuruan mereka.',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'umum',
                'order' => 19
            ],
            [
                'question_text' => 'Dalam mengajar mata pelajaran umum, seberapa sering Anda memberikan penugasan berbasis proyek (Project-Based Learning) yang meminta siswa memecahkan masalah nyata atau menghasilkan karya terkait bidang kejuruan?',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'target_guru' => 'umum',
                'order' => 20
            ],

            // Bagian 6: Pertanyaan Terbuka / Esai
            [
                'question_text' => 'Sebutkan satu produk, jasa, atau inovasi konkret dari bidang keahlian Anda yang menurut Anda paling layak diproduksi secara massal oleh siswa untuk dipasarkan ke masyarakat melalui unit usaha sekolah.',
                'type' => 'text',
                'scale_type' => null,
                'target_guru' => null,
                'order' => 21
            ],
            [
                'question_text' => 'Berikan pandangan kritis dan jujur Anda mengenai hambatan terbesar (baik dari diri sendiri maupun sistem) yang membuat sebagian guru SMK terjebak dalam rutinitas mengajar formalitas saja, serta usulkan solusi konkretnya.',
                'type' => 'text',
                'scale_type' => null,
                'target_guru' => null,
                'order' => 22
            ]
        ];

        // Sync questions for Guru Survey
        foreach ($questionsGuru as $q) {
            $surveyGuru->questions()->updateOrCreate(
                [
                    'survey_id' => $surveyGuru->id,
                    'order' => $q['order']
                ],
                [
                    'question_text' => $q['question_text'],
                    'type' => $q['type'],
                    'scale_type' => $q['scale_type'],
                    'target_guru' => $q['target_guru']
                ]
            );
        }

        // 2. Create Survey for Siswa (Evaluating Teachers' Vocational Spirit)
        $surveySiswa = Survey::updateOrCreate(
            [
                'title' => 'Survei Evaluasi Penerapan Budaya & Pembelajaran Praktis SMKS Swasta Pembda Nias (Sudut Pandang Siswa)',
                'school_id' => $smk->id,
            ],
            [
                'description' => 'Kuisioner ini ditujukan bagi siswa SMKS Swasta Pembda Nias untuk mengevaluasi penerapan pembelajaran kejuruan, Project-Based Learning, Budaya Kerja Industri, dan kewirausahaan yang diajarkan oleh Bapak/Ibu guru di unit sekolah Anda.',
                'target_respondent' => 'siswa',
                'status' => 'active',
            ]
        );

        $questionsSiswa = [
            [
                'question_text' => 'Bapak/Ibu Guru produktif (kejuruan) menguasai materi praktik secara mendalam and mampu mencontohkan keterampilan kerja dengan baik di bengkel/lab.',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'order' => 1
            ],
            [
                'question_text' => 'Bapak/Ibu Guru mata pelajaran umum (seperti Matematika, Bahasa Indonesia, dll.) sering memberikan contoh atau tugas yang dihubungkan dengan bidang kejuruan jurusan saya.',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'order' => 2
            ],
            [
                'question_text' => 'Dalam proses belajar, kami sering diberikan tugas berupa proyek nyata untuk menghasilkan produk atau jasa yang bermanfaat (Project-Based Learning).',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'order' => 3
            ],
            [
                'question_text' => 'Kegiatan belajar praktik kami sudah menggunakan sistem seperti industri nyata (Teaching Factory), di mana kami dilatih melayani pelanggan atau memproduksi barang standar pasar.',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'order' => 4
            ],
            [
                'question_text' => 'Bapak/Ibu Guru sangat tegas menerapkan Budaya Kerja Industri (seperti kebersihan bengkel 5R/5S, keselamatan kerja, kedisiplinan waktu, kerapihan pakaian, dan kesopanan sopan santun).',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'order' => 5
            ],
            [
                'question_text' => 'Bapak/Ibu Guru sering memotivasi dan melatih kami untuk berinovasi serta menciptakan peluang usaha mandiri (entrepreneurship).',
                'type' => 'scale',
                'scale_type' => 'likert_5',
                'order' => 6
            ],
            [
                'question_text' => 'Tuliskan tanggapan, saran, atau usulan Anda agar pembelajaran praktik dan suasana industri di SMKS Swasta Pembda Nias bisa semakin baik lagi.',
                'type' => 'text',
                'scale_type' => null,
                'order' => 7
            ]
        ];

        // Sync questions for Siswa Survey
        foreach ($questionsSiswa as $q) {
            $surveySiswa->questions()->updateOrCreate(
                [
                    'survey_id' => $surveySiswa->id,
                    'order' => $q['order']
                ],
                [
                    'question_text' => $q['question_text'],
                    'type' => $q['type'],
                    'scale_type' => $q['scale_type']
                ]
            );
        }

        $this->command->info('Seeding survei khusus kejuruan SMK berhasil diselesaikan.');
    }
}
