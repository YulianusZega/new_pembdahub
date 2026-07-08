<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\GalleryItem;
use App\Models\User;

class LandingPageContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Memulai seeding berita dan galeri halaman beranda...');

        // Get an author id (preferably superadmin or admin)
        $author = User::where('role', 'superadmin')->first() 
               ?? User::where('role', 'admin_sekolah')->first()
               ?? User::first();

        $authorId = $author ? $author->id : 1;

        // Clean existing records to avoid duplicates
        News::truncate();
        GalleryItem::truncate();

        // 1. Seed News
        $newsItems = [
            [
                'title' => 'Penerimaan Murid Baru TP 2026/2027 Gelombang II Resmi Opened',
                'excerpt' => 'Yayasan Perguruan PEMBDA Nias secara resmi membuka pendaftaran Penerimaan Peserta Didik Baru (PPDB) TP 2026/2027 Gelombang II mulai 1 Mei hingga 30 Juni 2026.',
                'content' => 'Yayasan Perguruan PEMBDA Nias secara resmi membuka pendaftaran Penerimaan Peserta Didik Baru (PPDB) Tahun Pelajaran 2026/2027 untuk Gelombang II. Pendaftaran dibuka untuk unit SMPS Swasta Pembda 2, SMA Swasta Pembda 1, dan SMKS Swasta Pembda Nias. Calon siswa dan orang tua dapat mendaftarkan diri secara langsung melalui platform digital PembdaHUB di perguruanpembda.com.',
                'category' => 'pengumuman',
                'icon' => 'fa-solid fa-bullhorn',
                'gradient_from' => '#4f2ed1',
                'gradient_to' => '#6366f1',
                'is_published' => true,
                'published_at' => '2026-05-01 08:00:00',
                'author_id' => $authorId,
            ],
            [
                'title' => 'Pemilihan Duta Sekolah SMA Swasta Pembda 1 Gunungsitoli Sukses Digelar',
                'excerpt' => 'Pemilihan Duta Sekolah SMA Swasta Pembda 1 Gunungsitoli sukses dilaksanakan sebagai bagian dari implementasi P5 Kurikulum Merdeka.',
                'content' => 'Dalam rangka implementasi Projek Penguatan Profil Pelajar Pancasila (P5) Kurikulum Merdeka, SMA Swasta Pembda 1 Gunungsitoli menyelenggarakan pemilihan Duta Sekolah. Kegiatan ini mengangkat tema-tema penting penanggulangan kekerasan seksual, pencegahan perundungan (bullying), dan kampanye anti narkoba di kalangan pelajar. Kepala Sekolah, Berliance Zamira Zebua, S.Pd., menyampaikan apresiasi atas kreativitas siswa dalam mengampanyekan pesan-pesan positif ini.',
                'category' => 'kegiatan',
                'icon' => 'fa-solid fa-users',
                'gradient_from' => '#8b5cf6',
                'gradient_to' => '#a78bfa',
                'is_published' => true,
                'published_at' => '2026-05-18 09:00:00',
                'author_id' => $authorId,
            ],
            [
                'title' => 'Kelas Industri Axioo & Daihatsu SMK Swasta Pembda Tingkatkan Link & Match',
                'excerpt' => 'SMK Swasta Pembda Nias memperkuat program penyelarasan industri dengan menghadirkan Kelas Industri Axioo Class Program dan Daihatsu.',
                'content' => 'SMK Swasta Pembda Nias terus memperkuat program link and match dengan dunia usaha dan dunia industri (DUDI). Melalui kerjasama resmi dengan Axioo Class Program dan Daihatsu, sekolah menyediakan kelas industri khusus yang dibekali kurikulum industri dan laboratorium modern. Program ini bertujuan memastikan lulusan SMK Pembda memiliki keahlian yang langsung sesuai dengan kebutuhan kerja modern.',
                'category' => 'kerjasama',
                'icon' => 'fa-solid fa-handshake',
                'gradient_from' => '#059669',
                'gradient_to' => '#34d399',
                'is_published' => true,
                'published_at' => '2026-04-20 10:00:00',
                'author_id' => $authorId,
            ],
            [
                'title' => 'Yayasan PEMBDA Nias Dukung Penuh Pembentukan Ikatan Alumni (IKASPEN)',
                'excerpt' => 'Ketua Yayasan Perguruan PEMBDA Nias, Yulianus Zega, S.Kom., memberikan dukungan penuh bagi pembentukan Ikatan Alumni STM-SMK Pembda (IKASPEN).',
                'content' => 'Ketua Yayasan Perguruan PEMBDA Nias, Yulianus Zega, S.Kom., memberikan dukungan penuh bagi berdirinya Ikatan Alumni STM-SMK Pembda Nias (IKASPEN). Pihak yayasan memfasilitasi pembentukan ikatan ini dan menyediakan kantor sekretariat bersama. Keberadaan IKASPEN diharapkan dapat mempererat silaturahmi alumni sekaligus mendukung program Tracer Study sekolah di masa mendatang.',
                'category' => 'prestasi',
                'icon' => 'fa-solid fa-trophy',
                'gradient_from' => '#d97706',
                'gradient_to' => '#fbbf24',
                'is_published' => true,
                'published_at' => '2026-05-24 11:00:00',
                'author_id' => $authorId,
            ]
        ];

        foreach ($newsItems as $news) {
            News::create($news);
        }
        $this->command->info('Berhasil men-seed ' . count($newsItems) . ' berita.');

        // 2. Seed Gallery Items (using fallbacks for design, no actual image uploads required)
        $galleryItems = [
            [
                'title' => 'Upacara Bendera Senin Pagi',
                'caption' => 'Pelaksanaan Upacara Bendera Senin Pagi secara rutin guna melatih kedisiplinan dan jiwa nasionalisme siswa.',
                'image' => '',
                'category' => 'upacara',
                'sort_order' => 1,
                'is_featured' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Praktikum Fisika Lab IPA',
                'caption' => 'Siswa melakukan eksperimen sains secara langsung di laboratorium IPA terpadu.',
                'image' => '',
                'category' => 'praktikum',
                'sort_order' => 2,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Pekan Olahraga Antar Kelas',
                'caption' => 'Keseruan turnamen futsal internal dalam rangka Pekan Olahraga Sekolah.',
                'image' => '',
                'category' => 'olahraga',
                'sort_order' => 3,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Latihan Paduan Suara Seni',
                'caption' => 'Siswa mengasah minat bakat seni melalui kelompok paduan suara sekolah.',
                'image' => '',
                'category' => 'seni',
                'sort_order' => 4,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Praktik Tune-Up Bengkel Motor',
                'caption' => 'Siswa SMK Swasta Pembda melakukan praktik tune-up kendaraan di bengkel otomotif sekolah.',
                'image' => '',
                'category' => 'bengkel',
                'sort_order' => 5,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Juara Cerdas Cermat Kota',
                'caption' => 'Penyerahan piala penghargaan kepada siswa peraih juara lomba cerdas cermat tingkat kota.',
                'image' => '',
                'category' => 'prestasi',
                'sort_order' => 6,
                'is_featured' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Axioo Class Lab Komputer',
                'caption' => 'Aktivitas belajar pemrograman siswa di Laboratorium Komputer Axioo Class Program.',
                'image' => '',
                'category' => 'komputer',
                'sort_order' => 7,
                'is_featured' => false,
                'is_active' => true,
            ]
        ];

        foreach ($galleryItems as $item) {
            GalleryItem::create($item);
        }
        $this->command->info('Berhasil men-seed ' . count($galleryItems) . ' galeri.');
    }
}
