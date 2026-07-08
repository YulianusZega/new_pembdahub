<?php

namespace Database\Seeders;

use App\Models\TrainingModule;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrainingModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first superadmin user as the creator
        $creator = User::where('role', 'superadmin')->first();
        $creatorId = $creator ? $creator->id : 1;

        $modules = [
            [
                'title' => 'Panduan Umum Pengenalan Sistem PembdaHub',
                'description' => 'Panduan pengenalan dasar platform PembdaHub, cara masuk (login), navigasi antarmuka, dan keamanan akun untuk seluruh civitas akademika.',
                'category' => 'panduan_umum',
                'target_roles' => ['superadmin', 'admin_sekolah', 'guru', 'siswa', 'orang_tua', 'bendahara', 'ketua_yayasan', 'kepala_sekolah'],
                'sort_order' => 1,
                'is_published' => true,
                'difficulty' => 'Pemula',
                'reading_time' => 10,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Umum Pengenalan Sistem PembdaHub

Selamat datang di **PembdaHub**, Sistem Manajemen Sekolah Terpadu yang dirancang khusus untuk lingkungan **Yayasan Perguruan Pembda Nias**. Platform ini menyatukan administrasi sekolah, proses belajar mengajar (LMS), ujian online (CBT), pemantauan orang tua, manajemen SDM, hingga keuangan sekolah dalam satu pintu.

---

## 1. Persyaratan Sistem (System Requirements)

Untuk menjamin kenyamanan dan kelancaran saat menggunakan PembdaHub, pastikan perangkat keras dan perangkat lunak Anda memenuhi spesifikasi berikut:
* **Peramban (Browser)**: Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari versi terbaru (sangat direkomendasikan Google Chrome v100+).
* **Resolusi Layar**: Minimal 1280x720 piksel (resolusi laptop standar); direkomendasikan 1366x768 piksel ke atas.
* **Koneksi Internet**: Koneksi stabil minimal 2 Mbps untuk navigasi dashboard harian, dan minimal 10 Mbps pada saat pelaksanaan Ujian Online (CBT).

---

## 2. Cara Mengakses PembdaHub & Login Pertama Kali

Anda dapat mengakses PembdaHub melalui browser komputer maupun smartphone dengan alamat:
**[https://www.perguruanpembda.com](https://www.perguruanpembda.com)**

### Langkah-langkah Login:
1. Klik tombol **Login** pada halaman utama atau langsung kunjungi **[https://www.perguruanpembda.com/login](https://www.perguruanpembda.com/login)**.
2. Masukkan alamat **Email** atau **Username** resmi Anda yang telah didaftarkan oleh Administrator Sekolah.
3. Masukkan **Password** default Anda.
4. Klik **Masuk**.
5. Jika ini adalah pertama kalinya Anda masuk, sistem akan meminta Anda untuk segera memperbarui password demi keamanan akun Anda.

---

## 3. Keamanan Akun & Ganti Password

Sebagai pengguna PembdaHub, sangat penting untuk menjaga kerahasiaan informasi akun Anda:
* **Gunakan Password yang Kuat**: Kombinasikan huruf besar, huruf kecil, angka, dan karakter spesial (misal: `@`, `#`, `!`).
* **Jangan Bagikan Akun**: Satu akun PembdaHub mewakili satu identitas diri di dalam sekolah. Segala tindakan di dalam sistem akan tercatat sebagai aktivitas akun bersangkutan.
* **Log Out Setelah Digunakan**: Pastikan Anda selalu menekan tombol **Keluar** (Logout) jika menggunakan komputer bersama di laboratorium sekolah atau warnet.
* **Ubah Password Berkala**: Anda dapat mengubah password sewaktu-waktu melalui halaman Pengaturan Profil Anda.

---

## 4. Memahami Peran Ganda & Switch Role

Aplikasi PembdaHub mendukung Single Sign-On (SSO) internal yang canggih. Pengguna yang memiliki tugas ganda (seperti seorang **Guru** yang juga menjabat sebagai **Wali Kelas** atau **Kepala Sekolah**) dapat beralih panel kerja secara instan tanpa perlu logout terlebih dahulu:
1. Tombol beralih peran terletak di bagian atas panel navigasi samping (sidebar).
2. Klik tombol peran yang ingin dituju (misal: "Beralih ke Wali Kelas" atau "Beralih ke Kepala Sekolah").
3. Sidebar navigasi akan otomatis berubah menyesuaikan hak akses peran terpilih secara real-time.

---

## 5. Hub Forum & Kolaborasi Lintas Peran

PembdaHub dilengkapi dengan fitur **Hub Forum & Kolaborasi**, sebuah media komunikasi sosial internal bagi seluruh civitas akademika:
* **Berbagi Informasi**: Tulis pengumuman, bagikan artikel, atau ajukan pertanyaan terbuka.
* **Kepanitiaan & Proyek**: Daftarkan diri dalam kepanitiaan kegiatan sekolah (misal: Panitia MPLS, Classmeeting).
* **Donasi & Charity**: Ikuti aksi sosial penggalangan dana kemanusiaan untuk sesama anggota sekolah yang membutuhkan.
* **Leaderboard Keaktifan**: Kumpulkan poin reputasi dari setiap postingan dan interaksi positif Anda di Forum Hub.',
            ],
            [
                'title' => 'Panduan Administrator: Pengelolaan Data Master & Pengguna',
                'description' => 'Langkah-langkah lengkap untuk admin sekolah dan superadmin dalam mengelola data master institusi, struktur akademik, mata pelajaran, serta hak akses pengguna.',
                'category' => 'fitur_admin',
                'target_roles' => ['superadmin', 'admin_sekolah'],
                'sort_order' => 2,
                'is_published' => true,
                'difficulty' => 'Menengah',
                'reading_time' => 25,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Administrator: Pengelolaan Data Master & Pengguna

Modul ini ditujukan khusus untuk **Super Admin** dan **Admin Sekolah** dalam mengelola infrastruktur data dasar pada platform PembdaHub.

---

## 1. Pengelolaan Data Master & Unit Sekolah

Data master adalah fondasi operasional sebelum sistem dapat digunakan untuk transaksi akademik. Pastikan data diisi secara berurutan:
* **Pengelolaan Unit Sekolah (Khusus Super Admin)**: Akses menu **Data Master > Kelola Sekolah**. Digunakan untuk menambah unit baru, mengubah identitas sekolah (NPSN, KKM, Alamat, Nama Kepala Sekolah), dan mengunggah logo resmi sekolah.
* **Tahun Ajaran & Semester**: Sebelum memulai tahun ajaran baru, pastikan Tahun Ajaran aktif (misal: 2026/2027) dan Semester aktif (Ganjil/Genap) telah dikonfigurasi melalui menu **Tahun Ajaran** dan **Semester**.
* **Jurusan & Program Keahlian (Khusus SMK)**: Menghubungkan program studi yang dibuka agar memudahkan pengelompokan kelas dan kurikulum siswa melalui menu **Jurusan** dan **Program Keahlian**.
* **Jam Pelajaran (Time Slots)**: Buat daftar slot jam belajar harian. Tentukan waktu mulai dan selesai, serta centang "Jam Istirahat" jika jam tersebut digunakan untuk istirahat.

---

## 2. Pengelolaan Akun & Impor CSV Massal

PembdaHub membagi pengguna ke dalam beberapa peran yang dikelola melalui grup menu **Data Pengguna**:
* **Kelola Akun**: Digunakan untuk menambah pengguna baru, mereset password, mengaktifkan/menonaktifkan akun, atau mengganti role.
* **Data Siswa & Orang Tua**: Pendaftaran siswa dikaitkan dengan NIS/NISN. Setelah akun siswa dibuat, kaitkan dengan akun Orang Tua agar fitur pemantauan wali dapat berjalan.
* **Impor Data via CSV**:
  1. Masuk ke menu **Siswa** atau **Mata Pelajaran**.
  2. Klik tombol **Impor CSV**.
  3. Unduh **Template Format CSV** resmi.
  4. Isi data sesuai template (tanpa mengubah header kolom), lalu unggah berkas CSV baru tersebut.

---

## 3. Penjadwalan Akademik & Conflict Detection

Setelah data kelas, guru, dan mata pelajaran siap, admin harus melakukan pemetaan jadwal pelajaran:
* **Penugasan Mengajar**: Menghubungkan Guru dengan Mata Pelajaran dan Kelas serta menentukan jumlah jam pelajaran wajib per minggu.
* **Jadwal Pelajaran (Grid)**:
  1. Masuk ke menu **Akademik > Jadwal Pelajaran (Grid)**.
  2. Tarik (*drag-and-drop*) mata pelajaran ke slot hari dan jam pelajaran kosong pada Grid.
  3. **Deteksi Bentrok**: Jika sistem mendeteksi guru yang bersangkutan sedang mengajar di kelas lain pada jam yang sama, atau ruang kelas digunakan kelas lain, sistem akan memunculkan blok merah peringatan bentrok.
  4. Klik **Simpan Jadwal** setelah grid bersih dari bentrok.

---

## 4. Pengelolaan Praktik Kerja Lapangan (PKL) & Alumni

Bagi unit sekolah SMK, pengelolaan hubungan kerja industri dikelola penuh di sistem:
* **Penempatan PKL**: Daftarkan penempatan siswa, tentukan guru pembimbing sekolah, dan masukkan email/WhatsApp mentor industri DUDI.
* **Tautan Mentor DUDI (Signed URL)**: Admin dapat menyalin tautan akses langsung (signed URL) untuk mentor industri di halaman detail penempatan. Mentor tidak perlu login ke aplikasi sekolah, cukup klik tautan tersebut untuk memantau absensi GPS/jurnal harian siswa dan memberikan penilaian akhir.
* **Tracer Study & Lowongan Kerja**: Kelola data lulusan, pantau kuesioner tracer study (BMW: Bekerja, Melanjutkan, Wirausaha), dan kelola lowongan pekerjaan (job board) pada menu **PKL & Alumni > Tracer Study / Lowongan Kerja**.

---

## 5. Administrasi Tugas Akhir & CBT Monitoring

* **Format Tugas Akhir**: Unggah template format proposal/laporan akhir per unit sekolah.
* **Persetujuan Judul**: Tinjau usulan judul Tugas Akhir (Penelitian Ilmiah untuk SMA / Project Akhir untuk SMK) siswa kelas XII. Setujui dan tetapkan guru pembimbing untuk usulan yang diterima.
* **Penjadwalan Ujian**: Bila guru pembimbing menyatakan usulan layak uji, jadwalkan tanggal, lokasi, dan tetapkan guru penguji melalui menu **Tugas Ujian/Sidang**.
* **Admin CBT Monitoring**: Pantau aktivitas ujian siswa secara realtime. Gunakan tombol **Jeda Ujian Massal** jika terjadi keadaan darurat, dan **Lanjutkan Ujian Massal** untuk melanjutkan kembali dengan penambahan durasi waktu otomatis.',
            ],
            [
                'title' => 'Panduan Guru: Mengelola Pembelajaran & Penilaian Digital',
                'description' => 'Panduan lengkap bagi para guru untuk memanfaatkan fitur Asisten AI (RPP & Bank Soal), mengelola kelas online di LMS, serta melakukan penilaian digital.',
                'category' => 'fitur_guru',
                'target_roles' => ['guru'],
                'sort_order' => 3,
                'is_published' => true,
                'difficulty' => 'Menengah',
                'reading_time' => 18,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Guru: Mengelola Pembelajaran & Penilaian Digital

Selamat datang, Bapak/Ibu Guru! Modul ini dirancang untuk membantu Anda menguasai seluruh fitur akademik dan asisten digital pada Portal Guru PembdaHub.

---

## 1. Pemanfaatan Asisten AI (RPP & Soal CBT)

PembdaHub dilengkapi dengan fitur kecerdasan buatan berbasis **Gemini AI** untuk meringankan beban administrasi mengajar Anda:
* **RPP Generator (Modul Ajar AI)**: Akses menu **Asisten AI > RPP Generator**. Masukkan mata pelajaran, topik bahasan, kelas, tingkat kognitif, dan model pembelajaran yang diinginkan. Klik generate, AI akan menyusun rencana pelaksanaan pembelajaran lengkap mulai dari tujuan belajar, langkah kegiatan (pendahuluan, inti, penutup), hingga rubrik asesmen. Anda dapat menyalin atau mengunduhnya sebagai PDF.
* **Pembuat Soal CBT AI**: Akses menu **Asisten AI > Pembuat Soal CBT**. Anda dapat membuat soal pilihan ganda maupun esai secara otomatis berdasarkan dokumen materi pembelajaran atau deskripsi topik. Soal yang dihasilkan AI dapat langsung ditambahkan ke Bank Soal Anda untuk ujian online.

---

## 2. Mengelola LMS & Jitsi Live Meetings

LMS digunakan untuk interaksi belajar daring dengan siswa:
* **Membuat Topik**: Bagi kelas menjadi beberapa topik utama sesuai silabus.
* **Mengunggah Materi**: Unggah bahan ajar berupa PDF, slide presentasi, tautan video, atau artikel bacaan.
* **Membuat Tugas**: Berikan instruksi tugas, batas pengumpulan (deadline), dan kriteria penilaian. Siswa dapat mengunggah file jawaban mereka langsung di sistem.
* **Start Jitsi Live Meeting**: Klik tombol **Start Jitsi Meeting** di dalam ruang belajar LMS Anda. Link meeting tatap muka virtual terbuat secara instan dan mengirimkan notifikasi live via WhatsApp ke seluruh siswa di kelas tersebut.

---

## 3. CBT Ujian Online, Jeda & Sinkronisasi Nilai

Untuk menyelenggarakan evaluasi harian, ujian tengah semester (UTS), maupun ujian akhir (UAS):
* **Bank Soal**: Susun kumpulan soal per mata pelajaran. Anda dapat mengetikkan rumus matematika/sains berstandar KaTeX/LaTeX.
* **Kelola & Monitoring Ujian**: Jadwalkan ujian. Tentukan kelas sasaran, waktu pengerjaan (durasi), tanggal mulai dan berakhir, serta opsi acak soal.
* **Jeda/Resume Ujian**: Selama ujian berlangsung, Anda dapat menjeda ujian secara global untuk mengunci layar CBT siswa sementara waktu jika terindikasi kecurangan, lalu melanjutkannya dengan penambahan durasi waktu otomatis.
* **Koreksi Esai & Sync Grades**: Koreksi jawaban esai siswa secara manual, lalu klik tombol **Sync Grades** untuk mengirim seluruh nilai ujian CBT langsung ke Rapor Digital.

---

## 4. Projek Karakter P5 (Khusus Wali Kelas)

Sebagai wali kelas, Anda dapat mengelola projek P5 siswa:
* **Setup Projek P5**: Buat projek baru, tentukan tema (misal: "Gaya Hidup Berkelanjutan"), judul, deskripsi, serta kaitkan dengan dimensi/sub-elemen target sikap Pancasila.
* **Penilaian Kualitatif**: Input capaian perkembangan sikap siswa (BB: Belum Berkembang, MB: Mulai Berkembang, BSH: Berkembang Sesuai Harapan, SB: Sangat Berkembang) menggunakan tombol radio.
* **Catatan & Rapor P5**: Masukkan deskripsi catatan projek untuk setiap siswa dan unduh berkas Rapor P5 Digital (PDF) berformat nasional.

---

## 5. Bimbingan Tugas Akhir & Monitoring PKL

* **Bimbingan & Review Logbook**: Pantau progress pengerjaan tugas akhir siswa bimbingan Anda. Tinjau logbook harian mereka dan berikan catatan/feedback secara online.
* **Nyatakan Siap Sidang**: Tandai tugas akhir siswa bimbingan sebagai "Siap Sidang" jika bimbingan telah selesai untuk memicu penjadwalan ujian oleh admin.
* **Penguji Sidang**: Jika ditugaskan sebagai penguji, input nilai akhir ujian beserta catatan revisi sidang melalui menu **Tugas Ujian/Sidang**.
* **Pembimbing Monitoring PKL**: Bagi guru SMK, lihat kemajuan harian siswa bimbingan PKL Anda. Salin tautan akses portal mentor industri DUDI dari halaman detail monitoring untuk memudahkan koordinasi via WhatsApp. Anda juga mendapatkan **+50 Poin** reputasi ketika siswa bimbingan PKL Anda telah selesai dinilai oleh Industri.',
            ],
            [
                'title' => 'Panduan Siswa: Belajar Mandiri & Mengikuti Ujian Online',
                'description' => 'Panduan untuk siswa dalam mengakses materi pelajaran di LMS, mengumpulkan tugas secara tepat waktu, mengikuti ujian CBT, dan melihat pencapaian prestasi.',
                'category' => 'fitur_siswa',
                'target_roles' => ['siswa'],
                'sort_order' => 4,
                'is_published' => true,
                'difficulty' => 'Pemula',
                'reading_time' => 15,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Siswa: Belajar Mandiri & Mengikuti Ujian Online

Halo para siswa Perguruan Pembda! Portal Siswa PembdaHub dirancang khusus untuk mempermudah aktivitas belajarmu sehari-hari secara digital dan menyenangkan.

---

## 1. Belajar Interaktif Lewat LMS & Jitsi Live

LMS adalah ruang kelas digitalmu. Di sini kamu bisa:
* **Melihat Materi**: Baca bahan ajar yang diunggah oleh bapak/ibu guru berupa modul PDF, presentasi, atau tonton video pembelajaran yang disematkan.
* **Mengerjakan Tugas**: Perhatikan instruksi tugas dan tenggat waktu pengumpulan (deadline). 
* **Mengunggah Jawaban**: Kamu bisa langsung mengunggah file foto tugas tulisan tangan, PDF laporan, atau ketikan jawabanmu.
* **Jitsi Live Meeting**: Ketika guru memulai kelas virtual, kamu akan menerima notifikasi di WhatsApp. Klik link tersebut atau tombol **Gabung Live Class** di portal LMS untuk masuk ke tatap muka online secara instan.

---

## 2. Mengikuti Ujian CBT Anti-Cheat & Rumus KaTeX

Saat bapak/ibu guru menjadwalkan ulangan harian atau ujian semester:
* Akses menu **CBT / Ujian > Ujian Tersedia**.
* Pilih ujian yang sedang aktif, masukkan token ujian, dan klik **Mulai Ujian**.
* **Anti-Cheat (Deteksi Tab Switch)**: Jangan keluar dari jendela ujian atau membuka tab browser baru. Sistem mendeteksi aktivitas pindah tab dan akan memberikan peringatan otomatis. Jika melewati batas maksimal, lembar ujianmu akan terkunci otomatis!
* **Rumus KaTeX**: Untuk mata pelajaran matematika/sains, kamu akan melihat soal berformat rumus ilmiah yang indah, dan kamu juga bisa menjawab dengan simbol matematika standar.
* **Koneksi Terputus**: Jika koneksi terputus di tengah jalan, jangan panik. Jawabanmu sudah tersimpan otomatis di `localStorage`. Cukup refresh browser atau login kembali untuk melanjutkan ujian selama durasi masih tersedia.

---

## 3. Sistem Reputasi & Pembda Elite

PembdaHub memiliki fitur gamifikasi untuk mengapresiasi keaktifanmu belajar:
* **Hall of Fame**: Papan peringkat keaktifan siswa berdasarkan perolehan poin prestasi. Kamu bisa melihat posisimu dibanding teman-teman se-sekolah.
* **Lencana Kehormatan**: Dapatkan lencana khusus (seperti *Siswa Rajin*, *LMS Explorer*, atau *CBT Champion*) yang diberikan secara otomatis saat kamu menyelesaikan tugas tepat waktu atau meraih nilai sempurna.
* **Alumni Tracer Study**: Lulusan yang mengisi kuesioner Tracer Study akan memperoleh **+50 Poin** reputasi.

---

## 4. Jurnal Harian PKL (Khusus SMK)

Bagi siswa SMK yang sedang melaksanakan Prakerin/Magang di industri:
* **Pengisian Jurnal Magang**: Setiap hari kerja, masuk ke menu **Jurnal Magang**.
* **Input Laporan Harian**: Tuliskan aktivitas pekerjaan yang kamu lakukan, unggah foto bukti kegiatan, serta kirim koordinat GPS lokasi magang Anda untuk divalidasi oleh mentor industri DUDI.
* **Validasi Mentor**: Laporan yang disetujui mentor akan ditandai dengan centang hijau dan memberikan poin reputasi (+10 poin). Ketika program PKL Anda selesai dan dinilai, Anda mendapatkan tambahan **+100 Poin** reputasi.

---

## 5. Portofolio Tugas Akhir (Kelas XII)

Siswa kelas XII wajib menyelesaikan tugas akhir/proyek kelulusan:
* **Pengajuan Judul**: Usulkan judul tugas akhir/penelitian ilmiah Anda, tulis abstrak, dan pilih anggota kelompok Anda (jika kelompok). Anda yang mengajukan otomatis ditunjuk sebagai ketua.
* **Pengisian Logbook**: Ketua maupun anggota kelompok dapat menginput kemajuan harian (logbook) beserta berkas dokumentasi penunjang. Setiap pengisian logbook bimbingan akan memberikan poin reputasi (+10 poin) ke seluruh anggota kelompok setelah disetujui pembimbing.',
            ],
            [
                'title' => 'Panduan Orang Tua: Memantau Perkembangan & Administrasi Anak',
                'description' => 'Panduan bagi orang tua untuk memantau kemajuan belajar anak, melihat absensi kehadiran harian, dan melunasi tagihan sekolah anak.',
                'category' => 'fitur_orangtua',
                'target_roles' => ['orang_tua'],
                'sort_order' => 5,
                'is_published' => true,
                'difficulty' => 'Pemula',
                'reading_time' => 8,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Orang Tua: Memantau Perkembangan & Administrasi Anak

Bapak/Ibu Orang Tua yang kami hormati, PembdaHub menyediakan portal khusus agar Bapak/Ibu dapat aktif terlibat dalam memantau proses pendidikan putra-putri Bapak/Ibu.

---

## 1. Mengelola Banyak Anak (Multi-Child View)

Jika Bapak/Ibu memiliki lebih dari satu anak yang bersekolah di Perguruan Pembda:
* Halaman utama Portal Orang Tua akan menampilkan pilihan nama anak-anak Bapak/Ibu.
* Bapak/Ibu cukup memilih salah satu nama anak untuk beralih dan melihat data akademis serta administrasi anak tersebut secara real-time.

---

## 2. Fitur Pemantauan Absensi & Rapor Digital

Setelah memilih profil anak, Bapak/Ibu dapat mengakses menu pemantauan berikut:
* **Absensi Kehadiran Realtime**: Pantau kehadiran anak di sekolah setiap hari. Bapak/Ibu akan melihat status kehadiran apakah anak masuk (hadir), izin, sakit, atau alfa (tanpa keterangan) secara langsung setelah guru kelas melakukan absensi pagi.
* **Nilai & Hasil Belajar**: Lihat perkembangan nilai tugas, ulangan harian, UTS, hingga hasil akhir Rapor Semester anak.
* **Unduh Rapor Digital PDF**: Setelah wali kelas mempublikasikan rapor semester, Bapak/Ibu dapat mengunduh berkas Rapor Digital anak dalam bentuk PDF resmi langsung dari portal.

---

## 3. Pemantauan Administrasi Keuangan (Tagihan SPP)

* Akses menu **Tagihan** untuk melihat riwayat pembayaran SPP anak.
* Sistem akan menampilkan daftar tagihan yang belum dibayar, jatuh tempo, nominal tagihan, serta **Denda Keterlambatan Otomatis** jika melewati batas jatuh tempo (biasanya tanggal 10 setiap bulan).
* Lakukan pembayaran melalui loket bendahara sekolah atau transfer bank resmi. Setelah dikonfirmasi oleh Bendahara, status tagihan di portal Bapak/Ibu akan berubah menjadi **Lunas**.',
            ],
            [
                'title' => 'Panduan Keuangan: Manajemen Tagihan & Pembayaran Siswa',
                'description' => 'Instruksi langkah demi langkah bagi Bendahara Sekolah dalam pembuatan tagihan massal, validasi bukti transfer, dan penyusunan laporan keuangan.',
                'category' => 'fitur_keuangan',
                'target_roles' => ['superadmin', 'admin_sekolah', 'bendahara'],
                'sort_order' => 6,
                'is_published' => true,
                'difficulty' => 'Menengah',
                'reading_time' => 15,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Keuangan: Manajemen Tagihan & Pembayaran Siswa

Modul ini ditujukan bagi **Bendahara Sekolah** untuk mengelola sirkulasi keuangan sekolah secara transparan dan efisien.

---

## 1. Pembuatan Tagihan Massal & Individu

PembdaHub membagi pengelolaan tagihan menjadi dua metode utama:
* **Buat Tagihan Individu**: Digunakan untuk mengenakan tagihan khusus pada satu siswa tertentu (misal: denda kehilangan buku perpustakaan atau uang seragam susulan). Masukkan nama siswa, jenis tagihan, nominal, dan batas waktu pembayaran (jatuh tempo).
* **Pembuatan Tagihan Massal (Bulk-Create)**: Digunakan untuk membuat tagihan SPP rutin bulanan atau iuran pembangunan untuk satu angkatan, satu jurusan, atau seluruh kelas sekaligus. Masukkan nominal, tentukan kelas sasaran, pilih bulan penagihan, lalu klik simpan. Sistem akan otomatis memposting tagihan tersebut ke akun seluruh siswa yang berada di kelas target secara bersamaan.

---

## 2. Pencatatan Pembayaran & Pemutihan Denda

Ketika siswa atau orang tua membayar uang sekolah:
* **Input Pembayaran Manual (Loket Sekolah)**: Jika pembayaran dilakukan secara tunai di loket bendahara sekolah, cari nama atau NIS siswa di menu **Input Pembayaran**, pilih jenis tagihan yang ingin dibayar, masukkan nominal uang yang diterima, dan klik **Simpan Transaksi**.
* **Kalkulasi Denda & Pemutihan (Waive)**: Sistem secara otomatis mengkalkulasi denda jika pembayaran terlambat. Bendahara dapat menekan tombol **Waive Denda** untuk menghapus denda tersebut berdasarkan kebijakan sekolah sebelum menyimpan transaksi.
* **Kuitansi PDF & WhatsApp**: Sistem otomatis mencetak kuitansi digital (PDF) dan mengirimkan tanda terima pembayaran resmi ke nomor WhatsApp orang tua terdaftar.',
            ],
            [
                'title' => 'Panduan Ketua Yayasan: Monitoring Kinerja Unit & Penggajian',
                'description' => 'Panduan eksekutif bagi Ketua Yayasan untuk memantau aktivitas operasional seluruh sekolah, rekapitulasi beban kerja guru/staf, dan persetujuan penggajian bulanan.',
                'category' => 'fitur_yayasan',
                'target_roles' => ['ketua_yayasan'],
                'sort_order' => 7,
                'is_published' => true,
                'difficulty' => 'Mahir',
                'reading_time' => 12,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Ketua Yayasan: Monitoring Kinerja Unit & Penggajian

Modul ini memandu **Ketua Yayasan Perguruan Pembda Nias** dalam menggunakan panel eksekutif PembdaHub untuk fungsi pengawasan institusional dan keuangan makro.

---

## 1. Pemantauan Operasional Unit Pendidikan (Sekolah)

* Melalui halaman utama **Dashboard Yayasan**, Ketua Yayasan dapat memantau profil singkat, jumlah total siswa aktif, total guru, serta tenaga kependidikan di setiap jenjang unit (SMP Swasta Pembda 2, SMA Swasta Pembda 1, dan SMK Swasta Pembda Nias).
* Menu ini mempermudah pengambilan kebijakan strategis berbasis data riil di lapangan.

---

## 2. Monitoring SDM & Rekap Beban Kerja Guru

* Akses menu **Kepegawaian > Rekap Beban Kerja**.
* Yayasan dapat melihat detail jam mengajar guru (kumulatif jam pelajaran yang diampu per minggu) dan tugas tambahan yang diemban (misal: Wali Kelas, Kepala Laboratorium).
* Rekap ini menjadi tolok ukur penentuan efisiensi tenaga pengajar serta menjadi salah satu instrumen penilaian kinerja.

---

## 3. Pengelolaan & Persetujuan Penggajian (Payroll)

* Akses menu **Kepegawaian > Penggajian**.
* Sistem PembdaHub otomatis menghitung gaji pokok, tunjangan jabatan, tunjangan kehadiran (berdasarkan integrasi absensi sidik jari/digital), serta potongan pinjaman/koperasi guru dan staf.
* Ketua Yayasan dapat memeriksa rincian slip gaji bulanan seluruh pegawai sebelum dana ditransfer atau dibagikan.
* Slip Gaji PDF resmi diterbitkan dan dapat diunduh secara privat oleh masing-masing guru di portal mereka setelah dikonfirmasi yayasan.

---

## 4. Mengirim Undangan Pelatihan Guru (Single & Bulk)

* Yayasan memiliki menu **Undangan Pelatihan** untuk mengirim undangan pelatihan kompetensi guru.
* **Kirim Undangan Single**: Pilih nama guru, tentukan modul pelatihan PembdaHub, dan klik kirim.
* **Kirim Undangan Bulk**: Kirim undangan ke seluruh guru di unit sekolah (SMP/SMA/SMK) sekaligus untuk mengkoordinasikan program peningkatan kompetensi guru secara terintegrasi.',
            ],
            [
                'title' => 'Panduan Kepala Sekolah: Pemantauan Kinerja Akademik & Pengesahan Rapor',
                'description' => 'Langkah-langkah bagi Kepala Sekolah untuk memantau kehadiran guru/siswa, meninjau rancangan LMS/CBT, dan menyetujui penerbitan rapor digital.',
                'category' => 'fitur_admin',
                'target_roles' => ['superadmin', 'kepala_sekolah'],
                'sort_order' => 8,
                'is_published' => true,
                'difficulty' => 'Menengah',
                'reading_time' => 12,
                'pdf_file' => 'MANUAL_BOOK_PEMBDAHUB.pdf',
                'content' => '# Panduan Kepala Sekolah: Pemantauan Kinerja Akademik & Pengesahan Rapor

Modul ini memandu **Kepala Sekolah** dalam menjalankan peran pengawasan akademik dan operasional di tingkat unit sekolah masing-masing.

---

## 1. Pemantauan Kehadiran Guru & Staf (Pegawai)

* Melalui menu **Dashboard Kepala Sekolah**, pantau rekapitulasi tingkat kehadiran guru dan staf di sekolah Anda hari ini secara realtime.
* Lihat statistik guru yang hadir, terlambat, izin, sakit, atau tidak hadir (alfa) sebagai tolok ukur penegakan disiplin dan penilaian kinerja guru.

---

## 2. Monitoring KBM di LMS & CBT Ujian

Kepala Sekolah dapat memantau jalannya Kegiatan Belajar Mengajar (KBM) digital:
* **LMS Course Monitoring**: Lihat materi-materi ajar yang diunggah oleh guru di kelas-kelas online LMS, serta keaktifan interaksi siswa di forum diskusi.
* **CBT Exam Monitoring**: Pantau jalannya ujian sekolah online (CBT) yang sedang aktif, persentase keikutsertaan siswa, dan rata-rata skor perolehan siswa.

---

## 3. Meninjau Sebaran Nilai Akademik Siswa

* Akses menu **Analitik Akademik** untuk melihat grafik sebaran nilai rata-rata tugas, UTS, dan UAS siswa per kelas.
* Fitur ini membantu Kepala Sekolah memetakan pemahaman belajar siswa, efektivitas metode mengajar guru, serta mengambil tindakan pembinaan akademik yang tepat.

---

## 4. Pengesahan & Tanda Tangan Digital Rapor Semester

Di akhir semester, setelah wali kelas melakukan finalisasi nilai rapor siswa:
* Masuk ke menu **Persetujuan Rapor (Rapor Approvals)**.
* Tinjau draft rapor kelas, lalu bubuhkan **Tanda Tangan & Cap Digital** Kepala Sekolah secara sistem.
* Setelah disetujui Kepala Sekolah, Rapor Digital (PDF) berstatus resmi dirilis dan siap didistribusikan ke portal siswa dan orang tua.',
            ],
        ];

        foreach ($modules as $module) {
            TrainingModule::updateOrCreate(
                ['title' => $module['title']],
                [
                    'slug' => \Illuminate\Support\Str::slug($module['title']),
                    'description' => $module['description'],
                    'content' => $module['content'],
                    'category' => $module['category'],
                    'target_roles' => $module['target_roles'],
                    'sort_order' => $module['sort_order'],
                    'is_published' => $module['is_published'],
                    'difficulty' => $module['difficulty'],
                    'reading_time' => $module['reading_time'],
                    'pdf_file' => $module['pdf_file'],
                    'created_by' => $creatorId,
                ]
            );
        }

        // Copy Manual Book PDF to public storage so that it's downloadable
        $pdfSource = base_path('MANUAL_BOOK_PEMBDAHUB.pdf');
        $pdfDestDir = storage_path('app/public');
        if (!file_exists($pdfDestDir)) {
            mkdir($pdfDestDir, 0755, true);
        }
        $pdfDest = $pdfDestDir . '/MANUAL_BOOK_PEMBDAHUB.pdf';
        if (file_exists($pdfSource)) {
            copy($pdfSource, $pdfDest);
            $this->command->info('Manual Book PDF copied to public storage: ' . $pdfDest);
        } else {
            $this->command->warn('Manual Book PDF not found in base path: ' . $pdfSource);
        }

        $this->command->info('Training modules seeded successfully!');
    }
}
