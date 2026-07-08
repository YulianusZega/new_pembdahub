# [UPDATE 2026-02-01] CATATAN PERUBAHAN

- Menu "Absensi" (biasa) dihapus, hanya "Absensi Kelas (Bulk)" yang tampil di sidebar (grup Akademik)
- Admin hanya dapat melihat data nilai, tidak dapat menambah/mengedit/menghapus nilai siswa
- Tampilan nilai: satu baris per siswa, kolom nilai per jenis (Tugas, UTS, UAS, Sikap), kolom NISN, dan rata-rata nilai

# BUSINESS REQUIREMENTS DOCUMENT (BRD)

## Sistem Informasi Yayasan Perguruan Pembangunan Daerah (Pembda) Nias

**Versi:** 1.0 FINAL  
**Tanggal:** 28 Januari 2026  
**Status:** 🔒 FROZEN - Tidak boleh diubah setelah approved

---

## 📋 EXECUTIVE SUMMARY

### Tentang Yayasan

**Nama:** Yayasan Perguruan Pembangunan Daerah (Pembda) Nias  
**Lokasi:** Gunungsitoli, Nias, Sumatera Utara

### Unit Sekolah Yang Dikelola

| Kode | Nama Sekolah                     | Jenjang | Estimasi Siswa          |
| ---- | -------------------------------- | ------- | ----------------------- |
| 001  | SMP Swasta Pembda 2 Gunungsitoli | SMP     | ~500 siswa              |
| 002  | SMA Swasta Pembda 1 Gunungsitoli | SMA     | ~490 siswa              |
| 003  | SMK Swasta Pembda Nias           | SMK     | 710 siswa (data actual) |

**Total Siswa:** ~1,700 siswa  
**Total Guru & Staff:** ~150 orang  
**Total Pengguna Sistem:** ~3,500+ (siswa + guru + orang tua)

### Tujuan Sistem

Membangun sistem informasi terintegrasi berbasis web yang mencakup:

1. Website publik yayasan & sekolah
2. Sistem Informasi Akademik (SIAKAD)
3. Sistem keuangan dengan pembayaran QRIS
4. Learning Management System (LMS)
5. Portal monitoring untuk orang tua

---

## 🎯 RUANG LINGKUP SISTEM

### Fase 1 (Prioritas Utama)

✅ Website Publik  
✅ SIAKAD (Data Master, Kelas, Jadwal, Absensi, Nilai)  
✅ Sistem Keuangan + QRIS  
✅ LMS (Materi, Tugas, Quiz)  
✅ Portal Orang Tua

### Fase 2 (Ditunda - Future Development)

⏳ Video Conference Integration  
⏳ Advanced Analytics & Business Intelligence

---

## 📅 KALENDER AKADEMIK & TAHUN AJARAN

### Tahun Ajaran

- **Dimulai:** Bulan **Juli** setiap tahun
- **Berakhir:** Bulan **Juni** tahun berikutnya
- **Format Penamaan:** "2024/2025", "2025/2026", dst.

### Semester

- **Semester Ganjil:** Juli - Desember (6 bulan)
- **Semester Genap:** Januari - Juni (6 bulan)

### Kenaikan Kelas

- **Waktu:** Bulan **Juni** setiap tahun
- **Proses:** Otomatis oleh sistem berdasarkan kriteria kelulusan
- **Contoh:**
    - Juni 2025: Kelas VII → naik ke VIII
    - Juni 2025: Kelas IX/XII → lulus → pindah ke data **Alumni**

### Status Siswa

| Status     | Keterangan                                                  |
| ---------- | ----------------------------------------------------------- |
| **Aktif**  | Siswa sedang bersekolah                                     |
| **Lulus**  | Siswa sudah lulus (otomatis pindah ke alumni di bulan Juni) |
| **Keluar** | Siswa keluar sebelum lulus (pindah sekolah, DO, dll)        |
| **Pindah** | Siswa pindah ke sekolah lain                                |

**Aturan:**

- Siswa dengan status "Lulus" **otomatis dipindahkan** ke tabel alumni saat proses kenaikan kelas (Juni)
- Siswa yang "Lulus" tidak bisa login lagi ke sistem (akun di-nonaktifkan)
- Data alumni tersimpan untuk keperluan dokumentasi & reuni

---

## 💰 SISTEM KEUANGAN & PEMBAYARAN

### Jenis Pembayaran

| Jenis          | Kode    | Deskripsi                    | Sifat                 | Status Cicilan |
| -------------- | ------- | ---------------------------- | --------------------- | -------------- |
| SPP            | SPP     | SPP Bulanan                  | Recurring (12x/tahun) | ✅ Boleh Cicil |
| Seragam        | SERAGAM | Seragam Sekolah              | Sekali (siswa baru)   | ✅ Boleh Cicil |
| Pendaftaran    | DAFTAR  | Biaya Pendaftaran Siswa Baru | Sekali                | ✅ Boleh Cicil |
| Uang Alat      | ALAT    | Uang Alat Praktik/Lab        | Per Semester/Tahun    | ✅ Boleh Cicil |
| Sumbangan Duku | DUKU    | Sumbangan Duku               | Per Tahun             | ✅ Boleh Cicil |
| Perayaan Natal | NATAL   | Dana Perayaan Natal          | Per Tahun             | ✅ Boleh Cicil |
| Dana Kelas     | KELAS   | Dana Operasional Kelas       | Per Tahun             | ✅ Boleh Cicil |
| OSIS           | OSIS    | Iuran OSIS                   | Per Tahun             | ✅ Boleh Cicil |

### Aturan Pembayaran SPP

**Jatuh Tempo:**

- Setiap tanggal **10** di bulan berjalan
- Contoh: SPP Januari jatuh tempo 10 Januari 2025

**Denda Keterlambatan:**

- ❌ **TIDAK ADA DENDA**
- Sistem hanya memberi notifikasi/reminder

**Cicilan:**

- ✅ **BOLEH CICIL**
- Tidak ada minimal cicilan
- Contoh: SPP Rp 250.000 bisa dicicil:
    - Bayar Rp 100.000 (5 Jan)
    - Bayar Rp 150.000 (20 Jan)
    - Status: Lunas

**Status Tagihan:**
| Status | Kondisi |
|--------|---------|
| Belum Bayar | Total dibayar = Rp 0 |
| Cicilan | Total dibayar < Total tagihan |
| Lunas | Total dibayar = Total tagihan |
| Lebih Bayar | Total dibayar > Total tagihan (jarang, tapi bisa terjadi) |

**Refund (Pengembalian Dana):**

- ❌ **TIDAK ADA REFUND**
- Jika siswa keluar/pindah di tengah semester/tahun, uang SPP **tidak dikembalikan**
- Uang pembayaran lain (Seragam, Pendaftaran) juga **tidak dikembalikan**

### Metode Pembayaran

| Metode            | Keterangan                           | Real-time Update          |
| ----------------- | ------------------------------------ | ------------------------- |
| **QRIS**          | Scan QR Code (via Midtrans)          | ✅ Ya (via webhook)       |
| **Transfer Bank** | Manual (upload bukti, admin approve) | ❌ Tidak (perlu approval) |
| **Cash/Tunai**    | Di sekolah (admin input manual)      | ❌ Tidak (input manual)   |

**Prioritas:** QRIS (untuk mengurangi beban admin & meningkatkan akurasi)

### Auto-Generate Tagihan SPP

- **Trigger:** Awal bulan (tanggal 1)
- **Proses:** Sistem otomatis membuat 1 tagihan SPP untuk setiap siswa aktif
- **Contoh:**
    - 1 Juli 2025 → Generate tagihan SPP Juli untuk 1,700 siswa
    - 1 Agustus 2025 → Generate tagihan SPP Agustus untuk 1,700 siswa

---

## 📊 SISTEM AKADEMIK

### A. Absensi

**Kategori Absensi:**
| Kode | Status | Keterangan |
|------|--------|------------|
| H | Hadir | Siswa masuk sekolah |
| S | Sakit | Siswa sakit (perlu surat keterangan dokter - opsional) |
| I | Izin | Siswa izin (perlu surat izin - opsional) |
| A | Alpha | Siswa tidak hadir tanpa keterangan |

**Surat Keterangan:**

- **TIDAK WAJIB** upload surat (untuk fleksibilitas)
- Guru/admin bisa input manual berdasarkan surat fisik
- Jika ingin strict: bisa tambahkan upload di fase 2

**Notifikasi ke Orang Tua:**

- **Trigger:** Siswa alpha **3 kali** dalam 1 bulan
- **Action:** Kirim notifikasi otomatis ke orang tua via:
    - Email
    - Push notification (PWA)
- **Isi Notifikasi:**

    ```
    [NOTIFIKASI ABSENSI]

    Yth. Bapak/Ibu Orang Tua dari [Nama Siswa]

    Kami informasikan bahwa anak Bapak/Ibu telah alpha (tidak hadir tanpa keterangan) sebanyak 3 kali dalam bulan [Bulan]:
    - [Tanggal 1]: [Mata Pelajaran]
    - [Tanggal 2]: [Mata Pelajaran]
    - [Tanggal 3]: [Mata Pelajaran]

    Mohon perhatian dan tindak lanjut dari Bapak/Ibu.

    Hormat kami,
    [Nama Sekolah]
    ```

**Input Absensi:**

- **Bulk Input:** Guru bisa input absensi untuk seluruh kelas sekaligus
- **Per Siswa:** Bisa juga input per siswa (untuk fleksibilitas)

### B. Nilai & Penilaian

**Kriteria Ketuntasan Minimal (KKM):**

- **KKM Standar:** **75**
- Berlaku untuk semua mata pelajaran (kecuali sekolah ingin set berbeda per mapel)

**Jenis Nilai:**
| Jenis | Bobot | Keterangan |
|-------|-------|------------|
| Tugas | 20% | Tugas harian, PR, dll |
| UTS (Ujian Tengah Semester) | 30% | Ujian mid-semester |
| UAS (Ujian Akhir Semester) | 40% | Ujian akhir semester |
| Sikap/Praktik | 10% | Penilaian sikap/praktik |

**Formula Nilai Akhir:**

```
Nilai Akhir = (Tugas × 20%) + (UTS × 30%) + (UAS × 40%) + (Sikap × 10%)
```

**Contoh Perhitungan:**

```
Tugas: 80
UTS: 75
UAS: 85
Sikap: 90

Nilai Akhir = (80 × 0.2) + (75 × 0.3) + (85 × 0.4) + (90 × 0.1)
            = 16 + 22.5 + 34 + 9
            = 81.5

Status: TUNTAS (≥75)
```

**Status Ketuntasan:**
| Nilai Akhir | Status | Keterangan |
|-------------|--------|------------|
| ≥ 75 | **TUNTAS** | Lulus mata pelajaran |
| < 75 | **BELUM TUNTAS** | Harus remedial |

**Remedial:**

- ✅ **DIPERBOLEHKAN**
- ❌ **TIDAK ADA BATASAN** jumlah remedial
- Siswa boleh remedial sampai nilai ≥ 75
- Nilai remedial menggantikan nilai UTS/UAS yang rendah
- Guru input nilai remedial sebagai nilai pengganti

**Finalisasi Nilai:**

- Admin/Guru bisa **finalisasi** nilai di akhir semester
- Setelah finalisasi, nilai **masih bisa diedit** oleh guru (untuk fleksibilitas)
- Tapi ada log/history perubahan (untuk audit)

### C. Kelas & Rombongan Belajar

**Jumlah Siswa per Kelas:**

- ❌ **TIDAK ADA BATASAN MAKSIMAL**
- Sekolah bisa mengatur sendiri sesuai kapasitas ruangan
- Rekomendasi: 30-45 siswa per kelas (tapi tidak di-enforce sistem)

**Kriteria Naik Kelas:**

- Semua mata pelajaran harus **TUNTAS** (nilai ≥ 75)
- Jika ada mapel yang belum tuntas → siswa **TINGGAL KELAS**

**Tinggal Kelas:**

- ✅ **DIPERBOLEHKAN**
- Siswa yang tidak memenuhi kriteria naik kelas → mengulang di kelas yang sama
- Contoh:
    - Siswa kelas VII tahun ini, nilai Matematika 70 (tidak tuntas)
    - Tahun depan tetap di kelas VII (tidak naik ke VIII)

**Kenaikan Kelas Otomatis (Juni):**

```
IF (semua_mapel >= 75) THEN
  Naik kelas
ELSE
  Tinggal kelas
END IF

IF (kelas == IX OR kelas == XII) AND (semua_mapel >= 75) THEN
  Status = "Lulus"
  Pindah ke tabel Alumni
END IF
```

### D. Mata Pelajaran

**Kategori Mata Pelajaran:**

1. **Umum:** Semua siswa wajib (Matematika, B.Indonesia, B.Inggris, dll)
2. **Jurusan:** Khusus siswa jurusan tertentu
    - SMA: IPA (Fisika, Kimia, Biologi), IPS (Ekonomi, Sosiologi, Geografi)
    - SMK: Per jurusan (misal: TKJ → Jaringan Komputer, Pemrograman)

**Jurusan per Sekolah:**

**SMP Pembda 2:**

- Tidak ada jurusan (semua umum)

**SMA Pembda 1:**

- IPA (Ilmu Pengetahuan Alam)
- IPS (Ilmu Pengetahuan Sosial)

**SMK Pembda Nias:**

- DPIB (Desain Pemodelan dan Informasi Bangunan)
- TJKT (Teknik Jaringan Komputer dan Telekomunikasi)
- ACP (Animasi dan Crafting Pemrograman)
- TKR (Teknik Kendaraan Ringan)
- TSM (Teknik Sepeda Motor)
- TAV (Teknik Audio Video)
- TE (Teknik Elektronika)
- TO (Teknik Otomotif)

---

## 👥 ROLE & PERMISSION

### 1. Super Admin (Admin Yayasan)

**Akses:**

- ✅ Kelola **SEMUA** sekolah (SMP, SMA, SMK)
- ✅ Kelola admin sekolah
- ✅ Lihat laporan keuangan gabungan (3 sekolah)
- ✅ Kelola website yayasan
- ✅ Settings global sistem
- ✅ CRUD tahun ajaran, semester

**Batasan:**

- Tidak ada batasan

### 2. Admin Sekolah (Kepala Sekolah / Wakasek / TU)

**Akses:**

- ✅ Kelola guru & siswa **di sekolahnya**
- ✅ Kelola kelas & jadwal **di sekolahnya**
- ✅ Input & cetak raport **di sekolahnya**
- ✅ Kelola pembayaran **di sekolahnya**
- ✅ Lihat laporan **di sekolahnya**
- ✅ Kelola berita/galeri **di sekolahnya**

**Batasan:**

- ❌ **TIDAK BISA** akses data sekolah lain
- ❌ **TIDAK BISA** edit settings global
- ❌ **TIDAK BISA** kelola admin yayasan

**Contoh:**

- Admin SMP **tidak bisa** lihat/edit data siswa SMA/SMK
- Admin SMA **tidak bisa** approve pembayaran siswa SMK

### 3. Guru

**Akses:**

- ✅ Lihat jadwal mengajar **dirinya sendiri**
- ✅ Input absensi kelas **yang diajar**
- ✅ Input nilai siswa **di kelas yang diajar**
- ✅ Lihat nilai siswa **di kelas yang diajar** (semua mapel)
- ✅ Edit nilai **yang sudah diinput** (dengan log perubahan)
- ✅ Upload materi LMS **di mata pelajaran yang diajar**
- ✅ Buat tugas & quiz **di mata pelajaran yang diajar**
- ✅ Lihat data siswa **di kelas yang diajar**
- ✅ Lihat tagihan/pembayaran siswa **di kelas yang diajar**

**Batasan:**

- ❌ **TIDAK BISA** lihat/edit data siswa di kelas lain
- ❌ **TIDAK BISA** lihat nilai siswa di kelas yang tidak diajar
- ❌ **TIDAK BISA** kelola master data (sekolah, tahun ajaran, dll)
- ❌ **TIDAK BISA** approve/input pembayaran
- ❌ **TIDAK BISA** kelola website

**Klarifikasi:**

> **"Guru hanya bisa lihat nilai siswa-nya"** = Guru bisa lihat semua nilai (semua mapel) dari siswa di kelas yang diajar, tapi hanya bisa **INPUT/EDIT** nilai di mapel yang diajar.

**Contoh:**

- Pak Budi mengajar Matematika di kelas X-TKJ
- Pak Budi bisa lihat nilai siswa X-TKJ untuk semua mapel (Matematika, B.Indonesia, Fisika, dll)
- Pak Budi hanya bisa input/edit nilai Matematika
- Pak Budi tidak bisa lihat/edit nilai kelas XI-TKJ (tidak mengajar di sana)

### 4. Siswa

**Akses:**

- ✅ Lihat jadwal kelas **dirinya sendiri**
- ✅ Lihat nilai & raport **dirinya sendiri**
- ✅ Akses materi LMS
- ✅ Kerjakan tugas & quiz online
- ✅ Upload jawaban tugas
- ✅ Lihat tagihan & riwayat pembayaran **dirinya sendiri**
- ✅ Forum diskusi di LMS
- ✅ Download materi pembelajaran

**Batasan:**

- ❌ **TIDAK BISA** input data apapun (absensi, nilai, dll)
- ❌ **TIDAK BISA** lihat data siswa lain
- ❌ **TIDAK BISA** bayar tagihan (hanya bisa lihat)

### 5. Orang Tua

**Akses:**

- ✅ Lihat data anak (biodata, kelas)
- ✅ Lihat nilai & raport anak **SEMUA MAPEL**
- ✅ **Download raport PDF** (untuk disimpan/print)
- ✅ Lihat absensi anak (detail per hari)
- ✅ Bayar tagihan via **QRIS**
- ✅ Lihat riwayat pembayaran & download kwitansi
- ✅ Terima notifikasi (tagihan, nilai, absensi)
- ✅ **Chat dengan guru** (via sistem messaging)

**Batasan:**

- ❌ **TIDAK BISA** akses LMS (bukan untuk pembelajaran)
- ❌ **TIDAK BISA** lihat data siswa lain
- ❌ **TIDAK BISA** input/edit data apapun

**Multi-Child:**

- Jika orang tua punya 2+ anak di sekolah yang sama → 1 akun bisa switch/lihat semua anak

---

## 🔧 TECHNICAL REQUIREMENTS

### Platform & Teknologi

**Backend:**

- Framework: **Laravel 11**
- Database: **MySQL 8.0**
- PHP Version: **8.2+**
- Queue: Laravel Queue (database driver)

**Frontend:**

- Template Engine: **Blade**
- CSS Framework: **Tailwind CSS 3.x**
- JavaScript: **Alpine.js** (untuk interactivity ringan)
- Charts: **Chart.js**

**Progressive Web App (PWA):**

- ✅ Responsive design (desktop, tablet, mobile)
- ✅ Installable di smartphone
- ✅ Offline mode (cache data terakhir)
- ✅ Push notification

**Payment Gateway:**

- Provider: **Midtrans**
- Method: QRIS, Virtual Account (opsional), E-Wallet (opsional)

### Email & Notifikasi

**Email SMTP:**

- Provider: **Gmail SMTP**
- Untuk: Reset password, notifikasi penting, kwitansi

**Notifikasi:**

- ✅ Email
- ✅ Push Notification (PWA)
- ❌ SMS (tidak digunakan - biaya mahal)

**Trigger Notifikasi:**

1. **Tagihan Jatuh Tempo:**
    - H-3: Reminder tagihan akan jatuh tempo
    - H+0: Tagihan sudah jatuh tempo (jika belum bayar)
2. **Pembayaran Berhasil:**
    - Langsung setelah payment sukses (QRIS)
    - Kirim kwitansi digital (PDF)

3. **Nilai Baru:**
    - Saat guru input nilai baru
    - Notif ke siswa & orang tua

4. **Absensi Alpha:**
    - Saat alpha mencapai 3x dalam sebulan
    - Notif ke orang tua

5. **Pengumuman Penting:**
    - Dari admin sekolah
    - Broadcast ke semua siswa/ortu

### File Storage & Upload

**Lokasi Penyimpanan:**

- **Server Hostinger** (bukan cloud external)
- Path: `/storage/app/public/`
- Public access via symlink

**Kategori File:**
| Kategori | Max Size | Format |
|----------|----------|--------|
| Foto Profil (Siswa/Guru) | 2 MB | JPG, PNG |
| Dokumen (Tugas, Materi) | 2 MB | PDF, DOC, DOCX, PPT, PPTX |
| Gambar (Galeri, Berita) | 2 MB | JPG, PNG, GIF |

**Validasi Upload:**

- ✅ Cek extension file
- ✅ Cek MIME type
- ✅ Cek ukuran file
- ✅ Sanitize filename (remove special chars)
- ✅ Rename file (untuk security): `timestamp_randomstring.ext`

**Contoh:**

```
Original: Tugas Matematika.pdf
Renamed: 1706427230_a8b3c9d2e1f4.pdf
```

### Backup & Disaster Recovery

**Database Backup:**

- **Frekuensi:** Otomatis **HARIAN** (setiap jam 2 pagi)
- **Retention:** Simpan backup selama **30 hari**
- **Lokasi:** Server Hostinger + Download manual ke lokal (recommended)
- **Format:** SQL dump (compressed .sql.gz)

**File Backup:**

- Backup files (foto, dokumen) **mingguan**
- Retention: 30 hari

**Restore Procedure:**

- Dokumentasi lengkap cara restore dari backup
- Testing restore setiap bulan (untuk ensure backup valid)

### Security

**Authentication:**

- Password hashing: **bcrypt** (Laravel default)
- Session timeout: 120 menit (2 jam)
- Remember me: 30 hari (secure cookie)

**Authorization:**

- Role-based access control (RBAC)
- Middleware per role
- Permission check di setiap action

**Protection:**

- ✅ CSRF protection (Laravel default)
- ✅ SQL Injection prevention (Eloquent ORM)
- ✅ XSS protection (Blade auto-escape)
- ✅ File upload validation
- ✅ Rate limiting (login, API)
- ✅ SSL/HTTPS (Hostinger support)

**Audit Log:**

- Log semua aktivitas penting:
    - Login/logout
    - CRUD data master
    - Input/edit nilai
    - Pembayaran
    - Perubahan settings
- Retention: 1 tahun

### Performance

**Target:**

- Page load: < 2 detik
- Uptime: 99.5%
- Concurrent users: 500+ (peak hour)

**Optimization:**

- Database indexing
- Query optimization (N+1 prevention)
- Image compression
- Lazy loading
- Route/config/view caching
- CDN untuk assets (opsional)

---

## 🎨 UI/UX REQUIREMENTS

### Design Principles

- **Clean & Simple:** Tidak ramai, mudah dipahami
- **Responsive:** Optimal di desktop, tablet, smartphone
- **Accessible:** Font readable (min 14px), contrast ratio bagus
- **Consistent:** Color scheme & typography konsisten

### Color Scheme (Rekomendasi)

- **Primary:** Blue (#2563EB) - profesional, trust
- **Secondary:** Green (#10B981) - success, growth
- **Accent:** Orange (#F59E0B) - call-to-action
- **Neutral:** Gray scale untuk text & background
- **Error:** Red (#EF4444)

### Typography

- **Heading:** Inter/Poppins (Bold)
- **Body:** Inter/Roboto (Regular)
- **Size:** 14px-16px (body), 24px-32px (heading)

### Navigation

- **Website Publik:** Top navbar + mega menu
- **Dashboard:** Sidebar (collapsible di mobile)
- **Breadcrumb:** Untuk navigasi dalam dashboard

---

## 📱 MOBILE (PWA) REQUIREMENTS

### Install Prompt

- Show "Add to Home Screen" prompt setelah 3x visit
- Icon: Logo sekolah (512x512 PNG)
- Splash screen saat buka app

### Offline Mode

- Cache halaman penting:
    - Dashboard
    - Jadwal
    - Nilai (last loaded)
- Show "Offline Mode" banner saat no internet
- Sync data saat online kembali

### Push Notification

- Ask permission saat pertama login
- Types:
    - Tagihan jatuh tempo
    - Nilai baru
    - Pengumuman
    - Tugas baru (LMS)

---

## 📊 REPORTING REQUIREMENTS

### Laporan untuk Admin/Kepala Sekolah

**Akademik:**

- Rekap nilai per kelas (Excel/PDF)
- Daftar siswa tidak tuntas
- Statistik kelulusan per tahun
- Ranking siswa per kelas

**Absensi:**

- Rekap absensi per kelas (harian, bulanan)
- Daftar siswa dengan alpha tinggi (>3x)
- Grafik trend kehadiran

**Keuangan:**

- Laporan penerimaan (harian, bulanan, tahunan)
- Daftar tunggakan per kelas
- Rekap per jenis pembayaran
- Grafik cash flow

**Siswa:**

- Data siswa aktif/lulus/keluar (statistik)
- Pertumbuhan siswa per tahun (grafik)

### Export Format

- ✅ PDF (untuk print)
- ✅ Excel (untuk olah data lebih lanjut)

---

## ✅ ACCEPTANCE CRITERIA

Sistem dianggap **SUKSES** jika memenuhi kriteria:

### Functional

- ✅ Semua fitur di Fase 1 berjalan sesuai spesifikasi
- ✅ Multi-role login berfungsi dengan permission yang benar
- ✅ QRIS payment terintegrasi & real-time update
- ✅ Auto-generate tagihan SPP berjalan setiap bulan
- ✅ Notifikasi terkirim sesuai trigger
- ✅ Raport digital bisa di-generate & download
- ✅ LMS bisa upload materi, buat tugas, quiz

### Non-Functional

- ✅ Page load < 2 detik (80% pages)
- ✅ Responsive di mobile (tested di 3+ devices)
- ✅ Zero critical bugs after UAT
- ✅ Backup otomatis berjalan harian
- ✅ Dokumentasi lengkap (user manual + technical doc)

### User Adoption (3 bulan setelah launch)

- ✅ 80% guru input nilai via sistem
- ✅ 60% pembayaran via QRIS (reduce cash)
- ✅ 70% siswa akses LMS min 1x/minggu
- ✅ 60% orang tua aktif monitoring anak

---

## 📞 SUPPORT & MAINTENANCE

### Support Channel

- Email: support@pembdanias.sch.id
- WhatsApp: [nomor admin]
- In-app help center

### Maintenance Window

- Daily: Auto backup (2:00 AM - no downtime)
- Monthly: Security updates (Sunday, 2:00-4:00 AM)
- Quarterly: Feature updates (koordinasi dengan sekolah)

### SLA (Service Level Agreement)

- **Uptime:** 99.5% (max downtime 3.6 jam/bulan)
- **Response Time:** < 24 jam (untuk inquiry)
- **Bug Fix:**
    - Critical: < 24 jam
    - High: < 3 hari
    - Medium: < 1 minggu
    - Low: backlog

---

## 🚫 OUT OF SCOPE (Tidak Termasuk)

Hal-hal berikut **TIDAK TERMASUK** dalam sistem:

❌ Video Conference (ditunda ke Fase 2)  
❌ Advanced Analytics/BI Dashboard (ditunda ke Fase 2)  
❌ Mobile App Native (iOS/Android) - pakai PWA dulu  
❌ Biometric attendance (fingerprint/face recognition)  
❌ AI-powered features (auto-grading essay, chatbot, dll)  
❌ Integration dengan sistem pemerintah (Dapodik, EMIS)  
❌ E-learning konten (video pembelajaran) - hanya upload file  
❌ Live streaming kelas  
❌ Gamification (badges, points, leaderboard)  
❌ Alumni portal (data alumni ada, tapi no special portal)

---

## 📝 ASSUMPTIONS & CONSTRAINTS

### Assumptions (Asumsi)

- Hostinger Premium sudah ada & aktif
- Domain sudah/akan didaftarkan (.sch.id atau .com)
- Data siswa & guru bisa di-export ke Excel untuk import
- Gmail SMTP bisa digunakan (atau Hostinger email)
- Midtrans account bisa dibuat (verifikasi dokumen yayasan)
- Admin/guru ada yang bisa dilatih untuk mengelola sistem
- Internet di sekolah cukup stabil untuk akses sistem

### Constraints (Batasan)

- Budget terbatas → pilih stack open-source & free tools
- No dedicated server → pakai shared hosting (Hostinger)
- No dedicated IT staff → sistem harus mudah maintenance
- Timeline: 4 bulan (17 minggu)
- File storage limit: sesuai kapasitas Hostinger Premium

---

## 📄 GLOSSARY (Istilah)

| Istilah     | Definisi                                                                     |
| ----------- | ---------------------------------------------------------------------------- |
| **SIAKAD**  | Sistem Informasi Akademik                                                    |
| **LMS**     | Learning Management System (Sistem Pembelajaran Online)                      |
| **PWA**     | Progressive Web App (Aplikasi web yang bisa di-install seperti app)          |
| **QRIS**    | Quick Response Code Indonesian Standard (Standar QR pembayaran Indonesia)    |
| **KKM**     | Kriteria Ketuntasan Minimal (Nilai minimal untuk lulus mata pelajaran)       |
| **Rombel**  | Rombongan Belajar (Kelas)                                                    |
| **NISN**    | Nomor Induk Siswa Nasional                                                   |
| **NIS**     | Nomor Induk Sekolah (internal)                                               |
| **UTS**     | Ujian Tengah Semester                                                        |
| **UAS**     | Ujian Akhir Semester                                                         |
| **Alpha**   | Tidak hadir tanpa keterangan                                                 |
| **Webhook** | Callback URL untuk terima notifikasi dari sistem eksternal (misal: Midtrans) |

---

## ✍️ SIGN-OFF

Dokumen ini telah direview dan disetujui oleh:

**Yayasan Pembda Nias:**

- Nama: ********\_\_\_********
- Jabatan: ********\_\_\_********
- Tanggal: ********\_\_\_********
- Tanda Tangan: ********\_\_\_********

**Developer:**

- Claude (AI Assistant)
- Tanggal: 28 Januari 2026

---

**STATUS:** 🔴 AWAITING APPROVAL

Setelah Bapak/Ibu **REVIEW** dan **APPROVE** dokumen ini, status akan berubah menjadi:
**🟢 APPROVED & FROZEN**

Dan kita akan lanjut ke:
**Dokumen #2: DATABASE_SCHEMA_FINAL.sql**

---

**END OF DOCUMENT**
