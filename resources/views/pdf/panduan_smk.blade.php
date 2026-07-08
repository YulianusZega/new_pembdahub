<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Panduan Project Akhir - SMK Swasta Pembda Nias</title>
    <style>
        @page {
            margin: 2.2cm 2.2cm 2.2cm 2.2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.5;
            color: #000;
        }
        h1, h2, h3 {
            text-align: center;
            font-family: Arial, Helvetica, sans-serif;
            margin: 0 0 12px 0;
            color: #000;
        }
        h1 {
            font-size: 15pt;
            text-transform: uppercase;
        }
        h2 {
            font-size: 13pt;
            text-transform: uppercase;
            margin-top: 25px;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            page-break-before: always;
        }
        h3 {
            font-size: 11pt;
            text-align: left;
            margin-top: 15px;
            margin-bottom: 8px;
        }
        p {
            text-align: justify;
            text-indent: 1.25cm;
            margin: 0 0 10px 0;
        }
        ol, ul {
            margin: 0 0 12px 0;
            padding-left: 20px;
        }
        li {
            text-align: justify;
            margin-bottom: 4px;
        }
        .cover {
            text-align: center;
            page-break-after: always;
            padding-top: 20px;
            height: 95%;
        }
        .cover h1 {
            font-size: 16pt;
            margin-bottom: 5px;
        }
        .cover h2 {
            border-bottom: none;
            font-size: 13pt;
            margin-top: 5px;
            margin-bottom: 40px;
            page-break-before: avoid;
        }
        .logo-box {
            margin: 40px auto;
            width: 130px;
            height: 130px;
            border: 2px solid #000;
            line-height: 130px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            font-size: 13pt;
            border-radius: 50%;
            background: #fafafa;
        }
        .cover-footer {
            margin-top: 80px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            line-height: 1.6;
        }
        .no-indent {
            text-indent: 0;
        }
        .table-format {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        .table-format th, .table-format td {
            border: 1px solid #000;
            padding: 6px 10px;
            text-align: left;
            font-size: 10pt;
        }
        .table-format th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- COVER PAGE -->
    <div class="cover">
        <h1>Panduan Penulisan &amp; Penyusunan</h1>
        <h2>Laporan Project Akhir Kejuruan Siswa</h2>
        
        <div class="logo-box">
            SMK
        </div>
        
        <div style="margin-top: 80px; font-size: 12pt; font-weight: bold; line-height: 1.8;">
            DISUSUN OLEH:<br>
            TIM AKADEMIK KURIKULUM KEJURUAN<br>
            {{ strtoupper($schoolName) }}
        </div>
        
        <div class="cover-footer">
            {{ strtoupper($schoolName) }}<br>
            KOTA GUNUNGSITOLI - SUMATERA UTARA<br>
            TAHUN AJARAN {{ $year }}
        </div>
    </div>

    <!-- KATA PENGANTAR -->
    <div>
        <h1 style="page-break-before: always; margin-top: 10px;">KATA PENGANTAR</h1>
        <br>
        <p>Puji dan syukur kami panjatkan ke hadirat Tuhan Yang Maha Esa atas penyelesaian Buku Panduan Penyusunan Laporan Project Akhir Kejuruan ini. Buku ini disusun sebagai pedoman resmi bagi seluruh siswa kelas XII SMK Swasta Pembda Nias dalam merancang, membuat, merekayasa, dan mendokumentasikan alat/produk kejuruan sebagai salah satu syarat kelulusan studi.</p>
        <p>Project Akhir merupakan pembuktian kompetensi keahlian siswa sesuai jurusan masing-masing. Melalui pengerjaan project ini, siswa dilatih melakukan rancang bangun alat/sistem, pemrograman hardware/software, troubleshooting kerusakan, serta pengujian kualitas produk secara riil dan sistematis.</p>
        <p>Kami mengucapkan terima kasih kepada kepala sekolah, kepala program keahlian, guru pembimbing, dan tim kurikulum kejuruan. Semoga panduan ini mempermudah siswa dalam merampungkan proyek tugas akhir mereka secara optimal.</p>
        <br><br>
        <div style="float: right; text-align: center; width: 220px;" class="no-indent">
            Gunungsitoli, Juni 2026<br>
            Tim Kurikulum SMK
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- BAB I -->
    <h2>BAB I<br>KETENTUAN UMUM &amp; SISTEM BIMBINGAN</h2>
    <h3>A. Pengertian Umum</h3>
    <p>Project Akhir adalah karya rekayasa teknologi, produk hardware/software, atau instalasi sistem fungsional yang dibuat secara berkelompok oleh siswa kelas XII sebagai integrasi kompetensi jurusan. Project dibimbing langsung oleh satu orang Guru Pembimbing Produktif Kejuruan.</p>
    
    <h3>B. Alur Bimbingan Bertahap Kejuruan</h3>
    <p>Proses bimbingan dilaksanakan bertahap (step-by-step) berbasis sistem sekolah dengan alur sebagai berikut:</p>
    <ol>
        <li>Siswa hanya diperbolehkan mengunggah kemajuan bab yang sedang aktif di sistem.</li>
        <li>Akses pengunggahan bab selanjutnya terkunci sebelum bab saat ini disetujui (ACC) oleh Guru Pembimbing.</li>
        <li>Tanggapan Guru Pembimbing berupa:
            <ul>
                <li><strong>Setujui (ACC):</strong> Bab disetujui dan sistem membuka akses pengunggahan bab berikutnya.</li>
                <li><strong>Revisi:</strong> Berkas dikembalikan dengan catatan perbaikan teknis/analitis. Siswa harus merevisi dan mengunggah ulang di bab yang sama.</li>
            </ul>
        </li>
        <li>Jika seluruh bab (Bab I s.d Bab V) telah di-ACC, status proyek kelompok diubah menjadi "Ready for Exam" agar layak dijadwalkan sidang uji kompetensi project.</li>
    </ol>

    <!-- BAB II -->
    <h2>BAB II<br>SISTEMATIKA LAPORAN PROJECT AKHIR (SMK)</h2>
    <p>Naskah Laporan Project Akhir di {{ $schoolName }} disusun dengan sistematika rancang bangun rekayasa berikut:</p>
    
    <h3>A. Bagian Awal</h3>
    <p class="no-indent">Terdiri dari: Halaman Cover, Halaman Pengesahan (Kepala Sekolah, Pembimbing, Penguji), Abstrak Teknis, Kata Pengantar, Daftar Isi, Daftar Tabel, Daftar Skematik/Gambar.</p>

    <h3>B. Bagian Isi</h3>
    <ul>
        <li><strong>BAB I PENDAHULUAN:</strong> Berisi Latar Belakang Masalah (alasan kegunaan produk dibuat), Tujuan Rancang Bangun (apa yang ingin diciptakan), Manfaat Praktis Produk, dan Spesifikasi Teknis/Fungsionalitas Produk (ukuran, sensor, fitur utama alat).</li>
        <li><strong>BAB II PERENCANAAN DAN LANDASAN TEORI:</strong> Berisi Analisis Kebutuhan Komponen, Blok Diagram Sistem (alur input-proses-output produk), Desain Fisik/Skematik Rangkaian Alat, serta Tinjauan Teori Komponen Utama (penjelasan teori sensor/teknologi yang digunakan).</li>
        <li><strong>BAB III PROSES PEMBUATAN &amp; IMPLEMENTASI:</strong> Berisi Jadwal Pembuatan Alat, Langkah Kerja Perakitan Hardware, Rangkaian Skematik Kelistrikan, Struktur Kode Program Utama/Flowchart Kerja Alat, serta Langkah Pengerjaan Casing/Instalasi.</li>
        <li><strong>BAB IV PENGUJIAN DAN ANALISIS ALAT:</strong> Berisi Prosedur Pengujian Alat, Data Hasil Pengujian Tiap Sensor/Fitur, Analisis Fungsionalitas Keseluruhan Alat, serta Analisis Kelemahan, Kelebihan, dan Troubleshooting yang dilakukan saat alat error.</li>
        <li><strong>BAB V PENUTUP:</strong> Berisi Kesimpulan (apakah alat bekerja sesuai tujuan spesifikasi awal) dan Saran (ide pengembangan alat ke versi berikutnya).</li>
    </ul>

    <h3>C. Bagian Akhir</h3>
    <p class="no-indent">Terdiri dari: Daftar Pustaka (format APA) dan Lampiran (Datasheet komponen utama, kode program lengkap/source code, foto produk jadi, dan foto pengujian lapangan).</p>

    <!-- BAB III -->
    <h2>BAB III<br>FORMAT LAYOUT &amp; TATA TULIS</h2>
    <p>Penulisan naskah wajib memenuhi spesifikasi layout standar berikut:</p>
    
    <table class="table-format">
        <thead>
            <tr>
                <th style="width: 35%">Parameter</th>
                <th>Spesifikasi Resmi</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Ukuran Kertas</td>
                <td>A4 (210 mm x 297 mm), Warna Putih Minimal 80 gram.</td>
            </tr>
            <tr>
                <td>Batas Margin</td>
                <td>Kiri: 4 cm | Atas: 4 cm | Bawah: 3 cm | Kanan: 3 cm.</td>
            </tr>
            <tr>
                <td>Font &amp; Ukuran</td>
                <td>Times New Roman. Judul Bab: 14pt (Bold &amp; Kapital) | Sub-bab: 12pt (Bold) | Isi: 12pt.</td>
            </tr>
            <tr>
                <td>Spasi (Line Spacing)</td>
                <td>1.5 Spasi. Khusus Abstrak dan Kode Program ditulis 1.0 Spasi.</td>
            </tr>
            <tr>
                <td>Nomor Halaman</td>
                <td>Romawi kecil di bawah tengah untuk bagian awal. Angka arab di kanan atas untuk bagian isi (kecuali halaman pertama Judul Bab di bawah tengah).</td>
            </tr>
        </tbody>
    </table>

    <!-- BAB IV -->
    <h2>BAB IV<br>PENILAIAN &amp; SIDANG KOMPETENSI</h2>
    <h3>A. Persyaratan Kelayakan Sidang</h3>
    <p>Pendaftaran sidang akhir dilakukan jika Bab V telah disetujui (ACC) oleh Pembimbing, alat fungsional bekerja baik, dan naskah laporan lengkap diunggah dalam sistem.</p>
    
    <h3>B. Bobot Penilaian Kompetensi</h3>
    <ul>
        <li><strong>Nilai Pembimbingan (40%):</strong> Diambil dari keaktifan, kemandirian pengerjaan alat, kedisiplinan logbook, dan progress perakitan.</li>
        <li><strong>Nilai Uji Kompetensi Sidang (60%):</strong> Dinilai oleh Penguji Internal &amp; Eksternal (Industri), meliputi:
            <ul>
                <li>Fungsionalitas dan kerapian fisik produk/sistem (25%)</li>
                <li>Penguasaan materi rancangan dan pemrograman (20%)</li>
                <li>Tanya jawab dan pertanggungjawaban solusi rancang bangun (15%)</li>
            </ul>
        </li>
    </ul>
    
    <br><br>
    <div style="text-align: center;" class="no-indent">
        Disetujui oleh:<br>
        <strong>Kepala Sekolah {{ $schoolName }}</strong>
        <br><br><br><br>
        <strong><u>{{ $principalName }}</u></strong>
    </div>

</body>
</html>
