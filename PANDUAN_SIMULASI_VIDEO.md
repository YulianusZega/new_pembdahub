# PANDUAN REKAMAN VIDEO SIMULASI APLIKASI
## PEMBDA HUB - Sistem Informasi Manajemen Sekolah Terpadu

Panduan ini berisi skenario, naskah narasi (voiceover), dan panduan klik-demi-klik untuk merekam video demo/simulasi operasional aplikasi **Pembda Hub**. Video ini dapat direkam menggunakan screen recorder seperti **OBS Studio**, **Loom**, **Camtasia**, atau **Windows Game Bar (Win + G)**.

---

## 📋 PERSAPAN REKAMAN

1. **Jalankan Seeder Simulasi**: Pastikan database Anda telah terisi data simulasi terbaru dengan mengakses `http://localhost/seed-simulasi` (pada localhost) atau `http://perguruanpembda.com/seed-simulasi` (pada hosting) agar seluruh grafik, data siswa, nilai, dan tagihan muncul dengan lengkap dan indah.
2. **Siapkan Browser Tabs**: Buka beberapa tab browser atau gunakan mode Incognito agar Anda bisa berpindah peran dengan cepat (misalnya tab 1 login Admin/Guru, tab 2 login Siswa/Orang Tua).
3. **Resolusi Rekaman**: Gunakan resolusi layar minimal **1080p (Full HD)** untuk memastikan teks dan visual sistem terlihat sangat tajam dan profesional.

---

## 🎬 SKENARIO & NASKAH VIDEO (SCENE-BY-SCENE)

### SCENE 1: Pembukaan & Overview Platform (Durasi: ~1 Menit)
* **Visual**: Tampilan Halaman Depan (Landing Page/Portal Publik) Pembda Hub. Arahkan kursor ke logo Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA) dan daftar unit sekolah.
* **Tindakan**: Gulir halaman beranda ke bawah secara perlahan, tunjukkan fitur-fitur unggulan yang tertera di landing page.
* **Narasi (Voiceover)**:
  > "Halo semuanya! Selamat datang dalam video demo Pembda Hub, Sistem Informasi Manajemen Sekolah Terpadu yang dirancang khusus untuk Yayasan Perguruan Pembangunan Daerah Nias atau PEMBDA. Sistem ini mengintegrasikan seluruh operasional di tiga unit sekolah: SMPS Pembda 2, SMA Swasta Pembda 1, dan SMKS Swasta Pembda Nias. Hari ini kita akan melihat bagaimana Pembda Hub mendigitalisasi seluruh alur kerja sekolah secara paperless, mulai dari pendaftaran siswa baru, e-learning, ujian CBT, keuangan, bimbingan konseling, penilaian rapor, hingga penggajian guru secara otomatis."

---

### SCENE 2: Penerimaan Siswa Baru (PSB) & Migrasi Otomatis (Durasi: ~2 Menit)
* **Visual**: Halaman Form Pendaftaran Online Calon Siswa (PSB), kemudian berpindah ke Dashboard Admin Sekolah.
* **Tindakan**:
  1. Tunjukkan halaman pendaftaran siswa baru secara singkat.
  2. Buka tab baru, login sebagai **Admin SMK** (`admin@smkpembda.sch.id` / `AdminSMK@2026!`).
  3. Buka menu **PSB** -> **Pendaftar Baru**.
  4. Perlihatkan data calon siswa, ubah status ke `document_verified`, lalu input **Skor Tes** masuk dan ubah status ke `scored` lalu klik **Terima (Accept)**.
  5. Klik tombol **Migrate** pada siswa yang diterima. Tunjukkan notifikasi sukses migrasi.
* **Narasi (Voiceover)**:
  > "Kita mulai dari modul Penerimaan Siswa Baru atau PSB. Calon siswa dapat mendaftar secara mandiri melalui formulir online. Di sisi Admin Sekolah, kita dapat memverifikasi berkas pendaftaran dengan cepat. Setelah calon siswa mengikuti ujian masuk, kita tinggal menginput skor tesnya. Jika lulus seleksi dan menyelesaikan daftar ulang, Admin cukup menekan satu tombol 'Migrate'. Secara instan, sistem akan membuat akun siswa aktif di database, menyusun rombongan belajar, dan mengirimkan kredensial login secara otomatis ke nomor WhatsApp orang tua melalui integrasi API Fonnte."

---

### SCENE 3: Penyusunan Jadwal Pelajaran dengan Schedule Grid (Durasi: ~1.5 Menit)
* **Visual**: Menu **Akademik** -> **Jadwal Pelajaran (Grid)** di Portal Admin.
* **Tindakan**:
  1. Pilih Kelas, Tahun Ajaran, dan Semester aktif.
  2. Tunjukkan visualisasi Grid Jadwal Pelajaran yang interaktif.
  3. Lakukan *drag-and-drop* satu mata pelajaran dari daftar kanan ke dalam slot Grid kosong.
  4. Tunjukkan sistem pendeteksi bentrok (*conflict detection*) dengan mencoba meletakkan guru yang sama di jam yang sama (maka grid akan menampilkan warna merah/warning).
* **Narasi (Voiceover)**:
  > "Selanjutnya, kita masuk ke modul Akademik. Menyusun jadwal pelajaran sering kali menjadi tugas yang rumit bagi sekolah. Namun, dengan fitur Schedule Grid di Pembda Hub, Admin dapat menyusun jadwal pelajaran secara visual dengan drag-and-drop. Sistem kami dilengkapi algoritma pendeteksi bentrok otomatis. Jika ada guru atau ruang kelas yang terjadwal ganda di jam yang sama, sistem akan langsung memberikan peringatan warna merah, menjamin penyusunan jadwal yang 100% akurat tanpa konflik."

---

### SCENE 4: E-Learning LMS & Ujian Online CBT Premium (Durasi: ~3 Menit)
* **Visual**: Berpindah antara Portal Guru (`ama.zega@sma1pembda.sch.id` / `Guru@2026!`) dan Portal Siswa (`ferdinan@student.smp2pembda.sch.id` / `Siswa@2026!`).
* **Tindakan**:
  1. **Sisi Guru**: Tunjukkan forum diskusi di LMS, perlihatkan label tipe utas (Diskusi/Pertanyaan/Pengumuman) dan fitur pemberian lencana "Jawaban Terbaik" (Best Answer) dengan bingkai emas.
  2. Tunjukkan menu Bank Soal CBT dengan penulisan rumus matematika/sains menggunakan LaTeX ($KaTeX$).
  3. **Sisi Siswa**: Login sebagai siswa, buka LMS Quiz. Tunjukkan **Floating Timer Bar** (yang berdenyut merah jika waktu kritis) dan **Circular Progress Ring SVG** setelah kuis selesai.
  4. Tunjukkan halaman Ujian CBT. Matikan koneksi internet (atau demonstrasikan simulasi offline-first) untuk memperlihatkan pesan ketahanan offline. Pindah tab untuk memperlihatkan deteksi anti-curang (*tab switch tracking*).
  5. **Sisi Guru**: Kembali ke Portal Guru, klik **Sync Grades** pada hasil ujian CBT untuk memasukkan nilai ke Rapor secara instan.
* **Narasi (Voiceover)**:
  > "Mari kita lihat media pembelajaran online. Pembda Hub dilengkapi modul LMS dan CBT premium. Guru dapat membuat forum diskusi kelas interaktif, menandai balasan siswa paling cerdas sebagai 'Jawaban Terbaik' yang bersinar emas, serta menyematkan pengumuman penting. Untuk ujian sains dan matematika, bank soal kami mendukung penulisan rumus LaTeX yang ter-render rapi dan cepat.
  > Di sisi siswa, pengerjaan kuis LMS dilengkapi bilah waktu melayang dinamis dan indikator cincin progres sirkular. Pada ujian CBT, sistem kami memiliki perlindungan offline-first; jika internet terputus, jawaban siswa disimpan aman di memori lokal browser dan disinkronkan kembali saat online. Selain itu, fitur anti-cheat akan mencatat peringatan jika siswa mencoba berpindah tab browser. Terakhir, Guru cukup menekan 'Sync Grades' untuk mengirimkan nilai ujian CBT langsung ke nilai Rapor siswa tanpa input manual."

---

### SCENE 5: Pembinaan Siswa & Bimbingan Konseling (BK) (Durasi: ~1.5 Menit)
* **Visual**: Halaman **Bimbingan Konseling** dan **Dashboard Reputasi** Siswa.
* **Tindakan**:
  1. Tunjukkan pencatatan kasus konseling/pelanggaran siswa dan piagam prestasi (*Scientist Badges*) oleh Guru BK/Wali Kelas.
  2. Masuk ke Portal Siswa, tunjukkan tab **Konseling** dan grafik poin reputasi serta lencana reputasi yang diperoleh siswa di Hall of Fame.
* **Narasi (Voiceover)**:
  > "Pembda Hub tidak hanya fokus pada nilai akademik, tetapi juga karakter siswa. Melalui Modul Bimbingan Konseling, Guru BK dapat mencatat sesi pembinaan siswa secara privat. Setiap perilaku siswa dicatat dalam sistem reputasi poin. Siswa yang berprestasi akan mendapatkan poin reputasi tambahan dan lencana penghargaan visual yang tampil di portal mereka. Sebaliknya, poin akan berkurang otomatis jika siswa melakukan pelanggaran, sehingga orang tua dapat memantau kedisiplinan anak secara real-time."

---

### SCENE 6: Keuangan SPP & Denda Keterlambatan Otomatis (Durasi: ~2 Menit)
* **Visual**: Portal Bendahara dan Portal Orang Tua (`ama.ferdinan@parent.sch.id` / `OrangTua@2026!`).
* **Tindakan**:
  1. **Sisi Bendahara**: Tunjukkan menu **Manajemen Tagihan**, buat tagihan massal untuk satu angkatan kelas.
  2. **Sisi Orang Tua**: Perlihatkan halaman Keuangan Anak. Tunjukkan rincian nominal tagihan SPP beserta akumulasi denda keterlambatan berjalan yang dihitung otomatis oleh sistem sejak melewati tanggal jatuh tempo.
  3. **Sisi Bendahara**: Terima pembayaran parsial (cicilan) dari siswa, lakukan pemutihan denda dengan mengklik tombol **Waive Denda**, lalu simpan transaksi dan tunjukkan cetakan **Kuitansi Resmi (PDF)** yang bersih berlogo sekolah.
* **Narasi (Voiceover)**:
  > "Mari kita beralih ke modul Keuangan. Bendahara Sekolah dapat membuat tagihan bulanan seperti SPP secara massal untuk seluruh kelas. Sistem Pembda Hub akan menghitung denda keterlambatan secara otomatis dan real-time jika pembayaran melewati tanggal jatuh tempo. Orang tua dapat memantau total tunggakan dan denda anak langsung dari portal mereka. 
  > Saat membayar, Bendahara dapat menerima cicilan atau pembayaran lunas. Jika ada kondisi khusus, Bendahara dapat melakukan pemutihan denda dengan tombol 'Waive'. Setelah transaksi disimpan, kuitansi resmi PDF siap dicetak dan dikirimkan instan ke WhatsApp orang tua."

---

### SCENE 7: Pengolahan Rapor Digital (Durasi: ~1.5 Menit)
* **Visual**: Portal Wali Kelas -> Menu **Raport Digital** dan Portal Orang Tua -> Unduh Rapor.
* **Tindakan**:
  1. **Sisi Wali Kelas**: Tunjukkan proses **Generate Raport**. Jelaskan bahwa nilai akhir dihitung otomatis berdasarkan persentase bobot tugas, UTS, UAS, dan absensi riil siswa yang ditarik dari modul presensi.
  2. Klik tombol **Finalize** dan **Publish**.
  3. **Sisi Orang Tua**: Tunjukkan tombol **Unduh Rapor** yang kini aktif, lalu klik dan perlihatkan file PDF Rapor hasil belajar siswa yang rapi dan terstandarisasi.
* **Narasi (Voiceover)**:
  > "Di akhir semester, Wali Kelas tidak perlu lagi menghitung nilai rapor secara manual. Cukup dengan menekan tombol 'Generate', sistem akan menarik seluruh nilai tugas, UTS, UAS, absensi kehadiran, dan catatan sikap siswa, lalu mengalikannya dengan bobot nilai sekolah secara otomatis. Setelah Wali Kelas memasukkan catatan wali kelas dan mengklik 'Publish', orang tua dapat langsung mengunduh Rapor Digital anak dalam bentuk file PDF resmi kapan saja dan di mana saja."

---

### SCENE 8: SDM, Beban Kerja, & Laporan Penggajian Guru (Durasi: ~1.5 Menit)
* **Visual**: Menu **Kepegawaian** -> **Workload** (Beban Kerja) & **Payroll** (Laporan Gaji).
* **Tindakan**:
  1. Tunjukkan halaman beban kerja guru yang memuat perbandingan jam mengajar riil (dari jadwal pelajaran) vs jam mengajar wajib. Tunjukkan perhitungan jam honor mengajar tambahan otomatis.
  2. Buka menu Laporan Gaji, tunjukkan slip gaji bulanan lengkap dengan tunjangan fungsional/wali kelas, lalu klik **Cetak Slip Gaji (PDF)**.
* **Narasi (Voiceover)**:
  > "Terakhir, Pembda Hub membantu yayasan dalam manajemen SDM dan penggajian guru. Sistem membandingkan beban mengajar riil guru dari jadwal pelajaran aktif terhadap kewajiban jam mengajar mereka. Kelebihan jam mengajar akan dikonversi menjadi jam honor secara otomatis. Di menu Payroll, Bendahara Yayasan dapat memverifikasi tunjangan jabatan, tunjangan keluarga, dan mencetak slip gaji PDF resmi yang dikirimkan secara privat ke akun guru masing-masing."

---

### SCENE 9: Penutup & Call to Action (Durasi: ~30 Detik)
* **Visual**: Halaman Landing Page Utama Pembda Hub dengan teks penutup.
* **Narasi (Voiceover)**:
  > "Dengan Pembda Hub, Yayasan Perguruan Pembangunan Daerah Nias melangkah maju menuju digitalisasi sekolah yang modern, efisien, transparan, dan ramah lingkungan. Semua proses kini terintegrasi dalam satu platform terpadu. Terima kasih telah menyaksikan video simulasi ini, sampai jumpa!"

---

## 💡 TIPS REKAMAN YANG BAGUS

* **Gunakan Microphone Eksternal**: Rekam suara Anda di ruangan yang sunyi menggunakan mikrofon berkualitas agar suara narasi terdengar jelas tanpa noise bising.
* **Gunakan Kursor yang Jelas**: Aktifkan fitur *pointer highlight* atau efek lingkaran kuning pada kursor mouse di aplikasi perekam layar Anda agar penonton mudah mengikuti ke mana kursor Anda bergerak.
* **Lakukan Latihan Terlebih Dahulu**: Cobalah alur klik-demi-klik sebanyak satu kali sebelum Anda mulai merekam video final untuk memastikan transisi antar tab berjalan lancar.
