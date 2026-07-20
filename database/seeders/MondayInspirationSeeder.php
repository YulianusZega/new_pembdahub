<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\EducationalCalendar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MondayInspirationSeeder extends Seeder
{
    /**
     * Seed Monday Inspiration themes for TP 2026/2027.
     * 
     * Program Yayasan Perguruan Pembda Nias
     * MONDAY INSPIRATION — "KEEP MOVING FORWARD"
     * Tema Pembinaan Upacara Bendera Hari Senin
     * Tahun Pelajaran 2026/2027
     */
    public function run(): void
    {
        // Cari TP 2026/2027 (mendukung format "2026/2027" atau "TP. 2026/2027")
        $academicYear = AcademicYear::where('year', '2026/2027')
            ->orWhere('year', 'TP. 2026/2027')
            ->orWhere('year', 'LIKE', '%2026/2027%')
            ->first();
        if (!$academicYear) {
            $this->command->warn('TP 2026/2027 tidak ditemukan. Monday Inspiration seeder dilewati.');
            return;
        }

        // Cari user Yayasan untuk created_by (cari user dengan role yayasan, atau fallback ke user pertama)
        $creator = User::where('role', 'yayasan')->first() ?? User::first();
        if (!$creator) {
            $this->command->warn('Tidak ada user ditemukan. Monday Inspiration seeder dilewati.');
            return;
        }

        // 51 Tema Monday Inspiration (13 Juli 2026 — 28 Juni 2027)
        $themes = [
            // === FASE 1: Fondasi & Karakter (Minggu 1-5) ===
            1  => 'Keep Moving Forward: Bergerak Maju Tanpa Henti',
            2  => 'Karakter: Identitas Sejati yang Tidak Bisa Dicuri',
            3  => 'Integritas: Berani Benar dalam Setiap Keadaan',
            4  => 'Disiplin: Jembatan Menuju Kesuksesan',
            5  => 'One Step Higher: Menjadi Versi Terbaik Diri Sendiri',

            // === FASE 2: Pola Pikir & Belajar (Minggu 6-11) ===
            6  => 'Berani Bermimpi, Berani Bertindak',
            7  => 'Belajar Sepanjang Hayat: Investasi yang Tidak Pernah Rugi',
            8  => 'Life Skill: Keterampilan Hidup untuk Generasi Tangguh',
            9  => 'Kreativitas: Menemukan Solusi di Tengah Keterbatasan',
            10 => 'Inovasi Dimulai dari Keberanian Mencoba',
            11 => 'Growth Mindset: Gagal Bukan Akhir, Tapi Awal Pembelajaran',

            // === FASE 3: Komunikasi & Sosial (Minggu 12-16) ===
            12 => 'Komunikasi yang Membangun, Bukan Menjatuhkan',
            13 => 'Empati: Memahami Sebelum Ingin Dipahami',
            14 => 'Kolaborasi: Bersama Kita Lebih Kuat',
            15 => 'Kepemimpinan Dimulai dari Diri Sendiri',
            16 => 'Mengelola Waktu: Kunci Produktivitas dan Keseimbangan Hidup',

            // === FASE 4: Teknologi & Digital (Minggu 17-21) ===
            17 => 'STEM: Berpikir Sistematis untuk Menyelesaikan Masalah',
            18 => 'Menguasai Teknologi untuk Membangun Masa Depan',
            19 => 'Artificial Intelligence: Peluang dan Tanggung Jawab di Era Baru',
            20 => 'Literasi Digital: Cerdas, Kritis, dan Beretika di Dunia Maya',
            21 => 'Berpikir Kritis: Memilah Fakta di Era Informasi',

            // === FASE 5: Kebangsaan & Diversitas (Minggu 22-26) ===
            22 => 'Unity in Diversity: Bersatu dalam Keberagaman',
            23 => 'Toleransi: Menghargai Perbedaan sebagai Kekuatan Bangsa',
            24 => 'Nasionalisme di Era Globalisasi: Bangga Menjadi Indonesia',
            25 => 'Pemuda sebagai Penggerak Kemajuan Daerah',
            26 => 'Menjadi Agen Perubahan di Lingkungan Sekitar',

            // === FASE 6: Lingkungan & Kesehatan (Minggu 27-31) ===
            27 => 'Peduli Lingkungan: Aksi Nyata untuk Bumi yang Lebih Baik',
            28 => 'Budaya Gotong Royong di Era Modern',
            29 => 'Kesehatan Mental: Berani Bicara, Berani Minta Tolong',
            30 => 'Mental Tangguh: Bangkit dari Setiap Keterpurukan',
            31 => 'Adaptif: Siap Menghadapi Perubahan Zaman',

            // === FASE 7: Kesiapan Masa Depan (Minggu 32-38) ===
            32 => 'Membangun Jiwa Wirausaha: Dari Ide Menjadi Aksi',
            33 => 'Financial Literacy: Cerdas Mengelola Uang Sejak Dini',
            34 => 'Siap Memasuki Dunia Kerja dan Dunia Usaha',
            35 => 'Melayani dengan Hati, Memimpin dengan Teladan',
            36 => 'Menjadi Pribadi yang Menginspirasi Orang Lain',
            37 => 'Prestasi Sejati Dibangun oleh Konsistensi',
            38 => 'Etika dan Profesionalisme: Modal Utama Meraih Kepercayaan',

            // === FASE 8: Penguatan & Evaluasi (Minggu 39-45) ===
            39 => 'Membangun Budaya Sekolah yang Positif dan Inklusif',
            40 => 'Pendidikan Karakter: Pondasi Generasi Emas Indonesia',
            41 => 'Bersyukur: Menghargai Setiap Proses dan Pencapaian',
            42 => 'Digital Wellbeing: Keseimbangan Hidup di Era Digital',
            43 => 'Tanggung Jawab Sosial: Berbuat Baik Tanpa Diminta',
            44 => 'Persiapan Ujian: Belajar Cerdas, Bukan Hanya Keras',
            45 => 'Manajemen Stres: Tetap Tenang di Bawah Tekanan',

            // === FASE 9: Refleksi & Penutup (Minggu 46-51) ===
            46 => 'Merayakan Proses, Bukan Hanya Hasil',
            47 => 'Refleksi Diri: Belajar dari Perjalanan Setahun',
            48 => 'Mempersiapkan Diri untuk Babak Baru Kehidupan',
            49 => 'Profil Pelajar Pembda: Berkarakter, Berprestasi, Berpengaruh',
            50 => 'Legacy: Warisan Terbaik adalah Karakter yang Menginspirasi',
            51 => 'Keep Moving Forward: Menjadikan Kemajuan sebagai Budaya Hidup',
        ];

        // Senin pertama upacara: 13 Juli 2026
        $firstMonday = Carbon::parse('2026-07-13');
        $seededCount = 0;
        $skippedCount = 0;

        foreach ($themes as $weekNumber => $theme) {
            $mondayDate = $firstMonday->copy()->addWeeks($weekNumber - 1);

            // Gunakan updateOrCreate agar aman dijalankan berulang kali (idempotent)
            $calendar = EducationalCalendar::updateOrCreate(
                [
                    'academic_year_id' => $academicYear->id,
                    'type' => 'monday_inspiration',
                    'start_date' => $mondayDate->format('Y-m-d'),
                ],
                [
                    'school_id' => null,
                    'title' => $theme,
                    'end_date' => $mondayDate->format('Y-m-d'),
                    'is_holiday' => false,
                    'level' => 'yayasan',
                    'description' => "Monday Inspiration Minggu ke-{$weekNumber} | Program: Keep Moving Forward",
                    'created_by' => $creator->id,
                ]
            );

            if ($calendar->wasRecentlyCreated) {
                $seededCount++;
            } else {
                $skippedCount++;
            }
        }

        $lastMonday = $firstMonday->copy()->addWeeks(count($themes) - 1);
        $this->command->info("Monday Inspiration seeder selesai: {$seededCount} tema baru ditambahkan, {$skippedCount} tema sudah ada (di-update).");
        $this->command->info("Total: " . count($themes) . " tema | Rentang: {$firstMonday->format('d M Y')} — {$lastMonday->format('d M Y')}");
    }
}
