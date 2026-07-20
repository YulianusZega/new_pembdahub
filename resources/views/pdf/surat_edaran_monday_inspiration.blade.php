<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Surat Edaran Monday Inspiration - Yayasan Perguruan Pembda Nias</title>
    <style>
        @page {
            margin: 2cm 2.5cm 2cm 2.5cm;
            size: A4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #000;
        }

        /* === KOP SURAT === */
        .kop-surat {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
            position: relative;
        }
        .kop-surat .logo {
            position: absolute;
            left: 0;
            top: 5px;
            width: 75px;
            height: auto;
        }
        .kop-surat .yayasan-name {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop-surat .school-name {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #000;
        }
        .kop-surat .address {
            font-size: 10pt;
            margin-top: 2px;
        }
        .kop-surat .website {
            font-size: 10pt;
            font-style: italic;
        }

        /* === ISI SURAT === */
        .surat-info {
            margin-bottom: 15px;
        }
        .surat-info table {
            width: auto;
        }
        .surat-info td {
            padding: 1px 5px 1px 0;
            vertical-align: top;
            font-size: 12pt;
        }
        .surat-info td.label { width: 90px; }
        .surat-info td.sep { width: 15px; text-align: center; }

        .perihal-line { margin: 10px 0; }
        .kepada-block { margin: 10px 0 5px 0; }
        .kepada-block .di-tempat { margin-left: 120px; font-style: italic; }

        .salam-pembuka { margin: 10px 0; }
        .isi-surat { margin: 10px 0; text-align: justify; }
        .isi-surat p { margin-bottom: 8px; text-indent: 40px; }
        .isi-surat .no-indent { text-indent: 0; }

        /* === PROGRAM BOX === */
        .program-box {
            border: 2px solid #1a237e;
            padding: 15px 20px;
            margin: 15px 0;
            text-align: center;
            background: #f5f5ff;
            page-break-inside: avoid;
        }
        .program-box .program-label {
            font-size: 11pt;
            font-weight: bold;
            color: #1a237e;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .program-box .program-title {
            font-size: 18pt;
            font-weight: bold;
            color: #1a237e;
            margin: 5px 0;
            letter-spacing: 2px;
        }
        .program-box .program-tagline {
            font-size: 14pt;
            font-weight: bold;
            font-style: italic;
            color: #303f9f;
        }
        .program-box .program-subtitle {
            font-size: 10pt;
            margin-top: 5px;
            color: #555;
        }

        /* === TABEL TEMA === */
        .tema-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 10pt;
        }
        .tema-table th {
            background: #1a237e;
            color: #fff;
            padding: 6px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #1a237e;
        }
        .tema-table td {
            padding: 4px 8px;
            border: 1px solid #ccc;
            vertical-align: top;
        }
        .tema-table tr:nth-child(even) {
            background: #f8f9ff;
        }
        .tema-table .col-mg { width: 35px; text-align: center; }
        .tema-table .col-tgl { width: 95px; text-align: center; }
        .tema-table .col-program { width: 120px; text-align: center; font-style: italic; }
        .tema-table .col-tema { }

        /* === FASE HEADER === */
        .fase-row td {
            background: #e8eaf6 !important;
            font-weight: bold;
            font-size: 10pt;
            color: #1a237e;
            text-align: center;
            padding: 5px;
        }

        /* === PETUNJUK === */
        .petunjuk-list {
            margin: 10px 0 10px 20px;
        }
        .petunjuk-list li {
            margin-bottom: 5px;
            text-align: justify;
        }

        /* === TTD === */
        .ttd-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }
        .ttd-right {
            float: right;
            text-align: center;
            width: 250px;
        }
        .ttd-right .jabatan { margin-bottom: 60px; }
        .ttd-right .nama {
            font-weight: bold;
            text-decoration: underline;
        }

        /* === TEMBUSAN === */
        .tembusan {
            clear: both;
            margin-top: 100px;
            font-size: 11pt;
        }
        .tembusan ul {
            margin-left: 20px;
            list-style-type: decimal;
        }
        .tembusan ul li { margin-bottom: 2px; }

        /* Page break */
        .page-break { page-break-before: always; }

        /* Clearfix */
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>

    {{-- === HALAMAN 1: SURAT EDARAN === --}}

    {{-- Kop Surat --}}
    <div class="kop-surat">
        @php
            $logoPath = public_path('images/logo-pembda.png');
        @endphp
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo Pembda">
        @endif
        <div class="yayasan-name">Yayasan Perguruan Pembda Nias</div>
        <div class="school-name">Surat Edaran</div>
        <div class="address">Jl. Pelita No.9 Kelurahan Ilir, Kota Gunungsitoli, Sumatera Utara 22815</div>
        <div class="website">Website: perguruanpembda.com | Email: yayasan@perguruanpembda.com</div>
    </div>

    {{-- Info Surat --}}
    <div class="surat-info">
        <table>
            <tr>
                <td class="label">Nomor</td>
                <td class="sep">:</td>
                <td>___/SE/YP-PEMBDA/VII/2026</td>
            </tr>
            <tr>
                <td class="label">Lampiran</td>
                <td class="sep">:</td>
                <td>1 (satu) berkas</td>
            </tr>
            <tr>
                <td class="label">Perihal</td>
                <td class="sep">:</td>
                <td><b>Program <i>Monday Inspiration</i> — "Keep Moving Forward"<br>Tahun Pelajaran 2026/2027</b></td>
            </tr>
        </table>
    </div>

    <div style="text-align: right; margin-bottom: 15px;">
        Gunungsitoli, {{ \Carbon\Carbon::now()->locale('id')->isoFormat('D MMMM Y') }}
    </div>

    {{-- Kepada --}}
    <div class="kepada-block">
        Kepada Yth.<br>
        <b>Kepala Sekolah & Seluruh Guru</b><br>
        di Lingkungan Yayasan Perguruan Pembda Nias<br>
        <div class="di-tempat">di tempat</div>
    </div>

    {{-- Salam --}}
    <div class="salam-pembuka"><i>Assalamu'alaikum Wr. Wb.</i></div>
    <div class="salam-pembuka">Salam dan Bahagia.</div>

    {{-- Isi Surat --}}
    <div class="isi-surat">
        <p>
            Puji syukur kita panjatkan ke hadirat Tuhan Yang Maha Esa atas berkat dan rahmat-Nya yang senantiasa menyertai 
            kita semua. Semoga seluruh sivitas akademika Yayasan Perguruan Pembda Nias dalam keadaan sehat dan penuh semangat 
            dalam menjalankan tugas pendidikan.
        </p>

        <p>
            Dalam rangka memperkuat pembinaan karakter, semangat belajar, dan wawasan seluruh peserta didik, 
            Yayasan Perguruan Pembda Nias dengan ini menerbitkan program:
        </p>
    </div>

    {{-- Box Program --}}
    <div class="program-box">
        <div class="program-label">Program Yayasan Perguruan Pembda Nias</div>
        <div class="program-title">MONDAY INSPIRATION</div>
        <div class="program-tagline">"KEEP MOVING FORWARD"</div>
        <div class="program-subtitle">Tema Pembinaan Upacara Bendera Hari Senin — Tahun Pelajaran 2026/2027</div>
    </div>

    <div class="isi-surat">
        <p>
            Program <i>Monday Inspiration</i> merupakan program pembinaan mingguan yang dilaksanakan setiap hari Senin 
            melalui upacara bendera. Setiap minggu, seluruh unit sekolah di bawah Yayasan Perguruan Pembda Nias 
            akan menyampaikan tema pembinaan yang telah ditetapkan oleh Yayasan sebagai bahan amanat Pembina Upacara.
        </p>

        <p>
            Program ini bertujuan untuk:
        </p>

        <ol class="petunjuk-list">
            <li>Menanamkan nilai-nilai karakter, integritas, dan kepemimpinan kepada seluruh peserta didik;</li>
            <li>Membangun semangat dan motivasi belajar yang konsisten sepanjang tahun pelajaran;</li>
            <li>Memberikan wawasan tentang perkembangan teknologi, literasi digital, dan tantangan zaman;</li>
            <li>Memperkuat rasa nasionalisme, toleransi, dan kepedulian sosial;</li>
            <li>Mempersiapkan peserta didik menjadi generasi yang adaptif, kreatif, dan berkarakter.</li>
        </ol>
    </div>

    <div class="isi-surat">
        <p class="no-indent"><b>Petunjuk Pelaksanaan:</b></p>
        <ol class="petunjuk-list">
            <li>Setiap unit sekolah <b>wajib</b> melaksanakan upacara bendera setiap hari Senin sesuai jadwal tema yang terlampir;</li>
            <li>Pembina Upacara menyampaikan amanat berdasarkan <b>tema yang telah ditetapkan</b> pada minggu tersebut;</li>
            <li>Pembina Upacara diharapkan <b>mengembangkan dan mengontekstualisasikan</b> tema sesuai dengan kondisi dan kebutuhan peserta didik di masing-masing unit;</li>
            <li>Jika hari Senin bertepatan dengan hari libur nasional atau libur sekolah, tema tersebut dapat disampaikan pada kesempatan terdekat berikutnya;</li>
            <li>Dokumentasi pelaksanaan upacara (foto/video) dapat dikirimkan ke Yayasan sebagai laporan;</li>
            <li>Daftar tema lengkap <b>51 minggu</b> terlampir pada halaman berikutnya.</li>
        </ol>
    </div>

    <div class="isi-surat">
        <p>
            Demikian surat edaran ini disampaikan. Atas perhatian dan kerja sama seluruh Kepala Sekolah dan Guru 
            dalam menyukseskan program ini, kami mengucapkan terima kasih.
        </p>
    </div>

    <div class="salam-pembuka"><i>Wassalamu'alaikum Wr. Wb.</i></div>

    {{-- Tanda Tangan --}}
    <div class="ttd-section clearfix">
        <div class="ttd-right">
            <div class="jabatan">Ketua Yayasan Perguruan Pembda Nias,</div>
            <div class="nama">___________________________</div>
            <div>Ketua Yayasan</div>
        </div>
    </div>

    {{-- Tembusan --}}
    <div class="tembusan">
        <b>Tembusan:</b>
        <ul>
            <li>Kepala SMPS Pembda 2 Gunungsitoli</li>
            <li>Kepala SMAS Pembda 1 Gunungsitoli</li>
            <li>Kepala SMKS Pembda Gunungsitoli</li>
            <li>Arsip</li>
        </ul>
    </div>

    {{-- === HALAMAN 2-3: LAMPIRAN TABEL TEMA === --}}
    <div class="page-break"></div>

    <div class="kop-surat">
        @if(file_exists($logoPath))
            <img src="{{ $logoPath }}" class="logo" alt="Logo Pembda">
        @endif
        <div class="yayasan-name">Yayasan Perguruan Pembda Nias</div>
        <div class="school-name" style="font-size: 14pt;">Lampiran Surat Edaran</div>
        <div class="address">Program Monday Inspiration — "Keep Moving Forward" — TP 2026/2027</div>
    </div>

    <div style="text-align: center; margin-bottom: 15px;">
        <b style="font-size: 13pt;">JADWAL TEMA PEMBINAAN UPACARA BENDERA HARI SENIN</b><br>
        <span style="font-size: 11pt;">Tahun Pelajaran 2026/2027 | 51 Minggu (13 Juli 2026 — 28 Juni 2027)</span>
    </div>

    <table class="tema-table">
        <thead>
            <tr>
                <th class="col-mg">Mg</th>
                <th class="col-tgl">Tanggal</th>
                <th class="col-program">Program</th>
                <th class="col-tema">Tema Pembinaan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $themes = [
                    // Fase 1: Fondasi & Karakter
                    ['fase' => 'FASE 1: Fondasi & Karakter', 'start' => 1],
                    1  => 'Keep Moving Forward: Bergerak Maju Tanpa Henti',
                    2  => 'Karakter: Identitas Sejati yang Tidak Bisa Dicuri',
                    3  => 'Integritas: Berani Benar dalam Setiap Keadaan',
                    4  => 'Disiplin: Jembatan Menuju Kesuksesan',
                    5  => 'One Step Higher: Menjadi Versi Terbaik Diri Sendiri',

                    // Fase 2: Pola Pikir & Belajar
                    ['fase' => 'FASE 2: Pola Pikir & Belajar', 'start' => 6],
                    6  => 'Berani Bermimpi, Berani Bertindak',
                    7  => 'Belajar Sepanjang Hayat: Investasi yang Tidak Pernah Rugi',
                    8  => 'Life Skill: Keterampilan Hidup untuk Generasi Tangguh',
                    9  => 'Kreativitas: Menemukan Solusi di Tengah Keterbatasan',
                    10 => 'Inovasi Dimulai dari Keberanian Mencoba',
                    11 => 'Growth Mindset: Gagal Bukan Akhir, Tapi Awal Pembelajaran',

                    // Fase 3: Komunikasi & Sosial
                    ['fase' => 'FASE 3: Komunikasi & Sosial', 'start' => 12],
                    12 => 'Komunikasi yang Membangun, Bukan Menjatuhkan',
                    13 => 'Empati: Memahami Sebelum Ingin Dipahami',
                    14 => 'Kolaborasi: Bersama Kita Lebih Kuat',
                    15 => 'Kepemimpinan Dimulai dari Diri Sendiri',
                    16 => 'Mengelola Waktu: Kunci Produktivitas dan Keseimbangan Hidup',

                    // Fase 4: Teknologi & Digital
                    ['fase' => 'FASE 4: Teknologi & Digital', 'start' => 17],
                    17 => 'STEM: Berpikir Sistematis untuk Menyelesaikan Masalah',
                    18 => 'Menguasai Teknologi untuk Membangun Masa Depan',
                    19 => 'Artificial Intelligence: Peluang dan Tanggung Jawab di Era Baru',
                    20 => 'Literasi Digital: Cerdas, Kritis, dan Beretika di Dunia Maya',
                    21 => 'Berpikir Kritis: Memilah Fakta di Era Informasi',

                    // Fase 5: Kebangsaan & Diversitas
                    ['fase' => 'FASE 5: Kebangsaan & Diversitas', 'start' => 22],
                    22 => 'Unity in Diversity: Bersatu dalam Keberagaman',
                    23 => 'Toleransi: Menghargai Perbedaan sebagai Kekuatan Bangsa',
                    24 => 'Nasionalisme di Era Globalisasi: Bangga Menjadi Indonesia',
                    25 => 'Pemuda sebagai Penggerak Kemajuan Daerah',
                    26 => 'Menjadi Agen Perubahan di Lingkungan Sekitar',

                    // Fase 6: Lingkungan & Kesehatan
                    ['fase' => 'FASE 6: Lingkungan & Kesehatan', 'start' => 27],
                    27 => 'Peduli Lingkungan: Aksi Nyata untuk Bumi yang Lebih Baik',
                    28 => 'Budaya Gotong Royong di Era Modern',
                    29 => 'Kesehatan Mental: Berani Bicara, Berani Minta Tolong',
                    30 => 'Mental Tangguh: Bangkit dari Setiap Keterpurukan',
                    31 => 'Adaptif: Siap Menghadapi Perubahan Zaman',

                    // Fase 7: Kesiapan Masa Depan
                    ['fase' => 'FASE 7: Kesiapan Masa Depan', 'start' => 32],
                    32 => 'Membangun Jiwa Wirausaha: Dari Ide Menjadi Aksi',
                    33 => 'Financial Literacy: Cerdas Mengelola Uang Sejak Dini',
                    34 => 'Siap Memasuki Dunia Kerja dan Dunia Usaha',
                    35 => 'Melayani dengan Hati, Memimpin dengan Teladan',
                    36 => 'Menjadi Pribadi yang Menginspirasi Orang Lain',
                    37 => 'Prestasi Sejati Dibangun oleh Konsistensi',
                    38 => 'Etika dan Profesionalisme: Modal Utama Meraih Kepercayaan',

                    // Fase 8: Penguatan & Evaluasi
                    ['fase' => 'FASE 8: Penguatan & Evaluasi', 'start' => 39],
                    39 => 'Membangun Budaya Sekolah yang Positif dan Inklusif',
                    40 => 'Pendidikan Karakter: Pondasi Generasi Emas Indonesia',
                    41 => 'Bersyukur: Menghargai Setiap Proses dan Pencapaian',
                    42 => 'Digital Wellbeing: Keseimbangan Hidup di Era Digital',
                    43 => 'Tanggung Jawab Sosial: Berbuat Baik Tanpa Diminta',
                    44 => 'Persiapan Ujian: Belajar Cerdas, Bukan Hanya Keras',
                    45 => 'Manajemen Stres: Tetap Tenang di Bawah Tekanan',

                    // Fase 9: Refleksi & Penutup
                    ['fase' => 'FASE 9: Refleksi & Penutup', 'start' => 46],
                    46 => 'Merayakan Proses, Bukan Hanya Hasil',
                    47 => 'Refleksi Diri: Belajar dari Perjalanan Setahun',
                    48 => 'Mempersiapkan Diri untuk Babak Baru Kehidupan',
                    49 => 'Profil Pelajar Pembda: Berkarakter, Berprestasi, Berpengaruh',
                    50 => 'Legacy: Warisan Terbaik adalah Karakter yang Menginspirasi',
                    51 => 'Keep Moving Forward: Menjadikan Kemajuan sebagai Budaya Hidup',
                ];

                $firstMonday = \Carbon\Carbon::parse('2026-07-13');
            @endphp

            @foreach($themes as $key => $value)
                @if(is_array($value) && isset($value['fase']))
                    <tr class="fase-row">
                        <td colspan="4">{{ $value['fase'] }}</td>
                    </tr>
                @elseif(is_string($value))
                    <tr>
                        <td class="col-mg">{{ $key }}</td>
                        <td class="col-tgl">{{ $firstMonday->copy()->addWeeks($key - 1)->locale('id')->isoFormat('D MMM Y') }}</td>
                        <td class="col-program"><i>Keep Moving Forward</i></td>
                        <td class="col-tema">{{ $value }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; text-align: center; font-size: 10pt; color: #555;">
        <i>"Keep Moving Forward — Bergerak Maju Tanpa Henti"</i><br>
        <b>Yayasan Perguruan Pembda Nias</b> | Tahun Pelajaran 2026/2027
    </div>

</body>
</html>
