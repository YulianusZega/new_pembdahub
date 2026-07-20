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

        // 40 Tema Monday Inspiration
        $themes = [
            1  => 'Keep Moving Forward: Bergerak Maju Tanpa Henti',
            2  => 'Karakter: Identitas Sejati yang Tidak Bisa Dicuri',
            3  => 'Integritas: Berani Benar dalam Setiap Keadaan',
            4  => 'Disiplin: Jembatan Menuju Kesuksesan',
            5  => 'One Step Higher: Menjadi Versi Terbaik Diri Sendiri',
            6  => 'Berani Bermimpi, Berani Bertindak',
            7  => 'Belajar Sepanjang Hayat: Investasi yang Tidak Pernah Rugi',
            8  => 'Life Skill: Keterampilan Hidup untuk Generasi Tangguh',
            9  => 'Kreativitas: Menemukan Solusi di Tengah Keterbatasan',
            10 => 'Inovasi Dimulai dari Keberanian Mencoba',
            11 => 'Growth Mindset: Gagal Bukan Akhir, Tapi Awal Pembelajaran',
            12 => 'Komunikasi yang Membangun, Bukan Menjatuhkan',
            13 => 'Empati: Memahami Sebelum Ingin Dipahami',
            14 => 'Kolaborasi: Bersama Kita Lebih Kuat',
            15 => 'Kepemimpinan Dimulai dari Diri Sendiri',
            16 => 'Mengelola Waktu: Kunci Produktivitas dan Keseimbangan Hidup',
            17 => 'STEM: Berpikir Sistematis untuk Menyelesaikan Masalah',
            18 => 'Menguasai Teknologi untuk Membangun Masa Depan',
            19 => 'Artificial Intelligence: Peluang dan Tanggung Jawab di Era Baru',
            20 => 'Literasi Digital: Cerdas, Kritis, dan Beretika di Dunia Maya',
            21 => 'Berpikir Kritis: Memilah Fakta di Era Informasi',
            22 => 'Unity in Diversity: Bersatu dalam Keberagaman',
            23 => 'Toleransi: Menghargai Perbedaan sebagai Kekuatan Bangsa',
            24 => 'Nasionalisme di Era Globalisasi: Bangga Menjadi Indonesia',
            25 => 'Pemuda sebagai Penggerak Kemajuan Daerah',
            26 => 'Menjadi Agen Perubahan di Lingkungan Sekitar',
            27 => 'Peduli Lingkungan: Aksi Nyata untuk Bumi yang Lebih Baik',
            28 => 'Budaya Gotong Royong di Era Modern',
            29 => 'Kesehatan Mental: Berani Bicara, Berani Minta Tolong',
            30 => 'Mental Tangguh: Bangkit dari Setiap Keterpurukan',
            31 => 'Adaptif: Siap Menghadapi Perubahan Zaman',
            32 => 'Membangun Jiwa Wirausaha: Dari Ide Menjadi Aksi',
            33 => 'Financial Literacy: Cerdas Mengelola Uang Sejak Dini',
            34 => 'Siap Memasuki Dunia Kerja dan Dunia Usaha',
            35 => 'Melayani dengan Hati, Memimpin dengan Teladan',
            36 => 'Menjadi Pribadi yang Menginspirasi Orang Lain',
            37 => 'Prestasi Sejati Dibangun oleh Konsistensi',
            38 => 'Etika dan Profesionalisme: Modal Utama Meraih Kepercayaan',
            39 => 'Legacy: Warisan Terbaik adalah Karakter yang Menginspirasi',
            40 => 'Keep Moving Forward: Menjadikan Kemajuan sebagai Budaya Hidup',
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

        $this->command->info("Monday Inspiration seeder selesai: {$seededCount} tema baru ditambahkan, {$skippedCount} tema sudah ada (di-update).");
        $this->command->info("Rentang tanggal: {$firstMonday->format('d M Y')} — {$firstMonday->copy()->addWeeks(39)->format('d M Y')}");
    }
}
