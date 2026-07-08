<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Buku Panduan Penelitian Ilmiah - SMA Swasta Pembda 1 Gunungsitoli</title>
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
        <h2>Laporan Penelitian Ilmiah Akhir Siswa</h2>
        
        <div class="logo-box">
            SMA
        </div>
        
        <div style="margin-top: 80px; font-size: 12pt; font-weight: bold; line-height: 1.8;">
            DISUSUN OLEH:<br>
            TIM AKADEMIK KURIKULUM<br>
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
        <p>Puji dan syukur kami panjatkan ke hadirat Tuhan Yang Maha Esa atas penyelesaian Buku Panduan Penyusunan Laporan Penelitian Ilmiah Akhir ini. Buku ini disusun sebagai pedoman resmi bagi seluruh siswa kelas XII SMA Swasta Pembda 1 Gunungsitoli dalam menyusun karya tulis ilmiah sebagai salah satu syarat kelulusan akademik.</p>
        <p>Kegiatan penelitian ilmiah ini bertujuan untuk melatih kepekaan ilmiah siswa terhadap masalah di lingkungan sekitar, melatih cara berpikir kritis, analitis, logis, serta sistematis. Melalui bimbingan bertahap berbasis sistem informasi sekolah, kami berharap proses penelitian berjalan secara terencana, terstruktur, dan akuntabel.</p>
        <p>Kami mengucapkan terima kasih kepada kepala sekolah, dewan guru, komite kurikulum, dan semua pihak yang membantu penerbitan panduan ini. Semoga buku pedoman ini dapat membantu siswa melaksanakan penelitian dengan hasil yang berkualitas.</p>
        <br><br>
        <div style="float: right; text-align: center; width: 220px;" class="no-indent">
            Gunungsitoli, Juni 2026<br>
            Tim Kurikulum SMA
        </div>
        <div style="clear: both;"></div>
    </div>

    <!-- BAB I -->
    <h2>BAB I<br>KETENTUAN UMUM &amp; SISTEM BIMBINGAN</h2>
    <h3>A. Pengertian Umum</h3>
    <p>Penelitian Ilmiah Akhir adalah karya ilmiah tertulis yang disusun oleh siswa kelas XII baik secara mandiri maupun berkelompok berdasarkan hasil penelitian empiris atau teoritis. Setiap kelompok dibimbing oleh satu orang Guru Pembimbing Akademik yang ditetapkan melalui SK Kepala Sekolah.</p>
    
    <h3>B. Alur Bimbingan Bertahap Secara Sistemis</h3>
    <p>Demi menjaga mutu keilmiahan, proses bimbingan di {{ $schoolName }} diatur secara bertahap (step-by-step) sebagai berikut:</p>
    <ol>
        <li>Siswa mengunggah draf bab aktif kelompoknya ke dalam sistem bimbingan sekolah.</li>
        <li>Siswa tidak dapat mengunggah bab berikutnya sebelum bab saat ini disetujui (ACC) oleh Guru Pembimbing.</li>
        <li>Keputusan Guru Pembimbing terdiri dari:
            <ul>
                <li><strong>Setujui (ACC):</strong> Bab disetujui dan sistem secara otomatis membuka akses pengunggahan bab berikutnya.</li>
                <li><strong>Revisi:</strong> Berkas dikembalikan dengan catatan koreksi. Siswa wajib merevisi dan mengunggah ulang perbaikan di bab yang sama.</li>
            </ul>
        </li>
        <li>Kelompok siswa yang telah menyelesaikan seluruh tahapan (Bab I s.d Bab V) akan dinyatakan layak maju ke sidang ujian akhir oleh Guru Pembimbing.</li>
    </ol>

    <!-- BAB II -->
    <h2>BAB II<br>SISTEMATIKA PENULISAN PENELITIAN ILMIAH</h2>
    <p>Struktur penulisan Laporan Penelitian Ilmiah di {{ $schoolName }} diatur dengan sistematika baku mirip naskah skripsi akademis berikut:</p>
    
    <h3>A. Bagian Awal</h3>
    <p class="no-indent">Terdiri dari: Halaman Cover, Halaman Pengesahan (Persetujuan Kepala Sekolah dan Pembimbing), Abstrak Penelitian, Kata Pengantar, Daftar Isi, Daftar Tabel, dan Daftar Gambar.</p>

    <h3>B. Bagian Isi</h3>
    <ul>
        <li><strong>BAB I PENDAHULUAN:</strong> Berisi Latar Belakang Masalah (alasan krusial topik dipilih), Rumusan Masalah (pertanyaan penelitian), Batasan Masalah, Tujuan Penelitian, dan Manfaat Penelitian (teoritis &amp; praktis).</li>
        <li><strong>BAB II KAJIAN PUSTAKA:</strong> Berisi Tinjauan Teori (landasan ilmiah teori), Penelitian Terdahulu (perbandingan riset sejenis), Kerangka Berpikir (alur logika penelitian), dan Hipotesis Penelitian (dugaan sementara jika riset kuantitatif).</li>
        <li><strong>BAB III METODOLOGI PENELITIAN:</strong> Berisi Tempat dan Waktu Penelitian, Desain Penelitian, Populasi dan Sampel (subjek penelitian), Teknik Pengumpulan Data (kuesioner, observasi, wawancara), serta Teknik Analisis Data (kualitatif atau statistik kuantitatif).</li>
        <li><strong>BAB IV HASIL DAN PEMBAHASAN:</strong> Berisi Deskripsi Data (penyajian temuan lapangan), Analisis Data (pembuktian hipotesis/korelasi), dan Pembahasan (penafsiran mendalam terhadap temuan riset dibandingkan teori).</li>
        <li><strong>BAB V PENUTUP:</strong> Berisi Kesimpulan (jawaban singkat atas rumusan masalah) dan Saran (rekomendasi untuk perbaikan/peneliti selanjutnya).</li>
    </ul>

    <h3>C. Bagian Akhir</h3>
    <p class="no-indent">Terdiri dari: Daftar Pustaka (format APA Style) dan Lampiran (instrumen penelitian, data mentah/raw data, foto aktivitas penelitian, dll.).</p>

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
                <td>1.5 Spasi. Khusus Abstrak dan Kutipan Langsung ditulis 1.0 Spasi.</td>
            </tr>
            <tr>
                <td>Nomor Halaman</td>
                <td>Romawi kecil di bawah tengah untuk bagian awal. Angka arab di kanan atas untuk bagian isi (kecuali halaman pertama Judul Bab di bawah tengah).</td>
            </tr>
        </tbody>
    </table>

    <!-- BAB IV -->
    <h2>BAB IV<br>PENILAIAN DAN KELULUSAN</h2>
    <h3>A. Syarat Ujian Sidang</h3>
    <p>Pendaftaran sidang akhir dilakukan setelah Bab V disetujui oleh Pembimbing dan berkas naskah lengkap telah diunggah dalam sistem.</p>
    
    <h3>B. Bobot Penilaian</h3>
    <ul>
        <li><strong>Nilai Pembimbingan (40%):</strong> Diambil dari keaktifan, kemandirian, dan ketepatan waktu revisi naskah selama masa bimbingan bertahap.</li>
        <li><strong>Nilai Sidang Ujian (60%):</strong> Dinilai secara objektif oleh Dewan Penguji yang ditunjuk sekolah, meliputi: penguasaan materi penelitian, metode analisis, kegunaan praktis temuan, dan kemampuan mempertahankan argumentasi ilmiah.</li>
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
