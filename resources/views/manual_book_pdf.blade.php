<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manual Book PembdaHUB</title>
    <style>
        @page {
            size: A4;
            margin: 2.2cm 2cm 2.2cm 2cm;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #334155;
            line-height: 1.6;
            font-size: 11pt;
            background-color: #ffffff;
        }

        /* Cover Page Styling (Borderless Stretch Fix - Bright & Premium) */
        .cover-container {
            position: absolute;
            top: -2.2cm;
            left: -2cm;
            right: -2cm;
            bottom: -2.2cm;
            background: #f8fafc;
            color: #1e293b;
        }
        
        /* Safe printable content area inside the cover page */
        .cover-content {
            position: absolute;
            top: 2.2cm;
            left: 2cm;
            right: 2cm;
            bottom: 2.2cm;
            padding: 1cm 0.5cm;
        }
        
        .cover-accent-bar {
            position: absolute;
            left: -2cm;
            top: -2.2cm;
            bottom: -2.2cm;
            width: 18px;
            background: #3b82f6;
            background: linear-gradient(180deg, #3b82f6 0%, #10b981 100%);
        }
        
        .cover-header {
            margin-top: 1cm;
        }
        
        .cover-logo-text {
            font-size: 16pt;
            letter-spacing: 5px;
            color: #2563eb;
            font-weight: 800;
            text-transform: uppercase;
        }
        
        .cover-title {
            font-size: 38pt;
            font-weight: 900;
            line-height: 1.1;
            margin-top: 1.2cm;
            color: #0f172a;
            letter-spacing: -1px;
        }
        
        .cover-subtitle {
            font-size: 18pt;
            font-weight: 400;
            margin-top: 0.5cm;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 1.5cm;
        }
        
        .cover-institution {
            font-size: 13pt;
            font-weight: 600;
            color: #1e293b;
            margin-top: 1.5cm;
            line-height: 1.5;
        }

        .cover-meta {
            position: absolute;
            bottom: 1.5cm;
            left: 0.5cm;
            right: 0.5cm;
            border-top: 1px solid #e2e8f0;
            padding-top: 1cm;
        }
        
        .cover-meta-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .cover-meta-table td {
            padding: 5px 0;
            font-size: 10.5pt;
            color: #475569;
            border: none !important;
        }
        
        .cover-meta-table td.label {
            width: 30%;
            font-weight: bold;
            color: #2563eb;
        }
        
        /* Page Break */
        .page-break {
            page-break-after: always;
            clear: both;
        }
        
        /* Header & Footer */
        .header {
            position: fixed;
            top: -1.4cm;
            left: 2cm;
            right: 2cm;
            height: 30px;
            font-size: 8.5pt;
            text-align: right;
            color: #64748b;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .footer {
            position: fixed;
            bottom: -1.4cm;
            left: 2cm;
            right: 2cm;
            height: 30px;
            font-size: 8.5pt;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        
        .page-number:after {
            content: counter(page);
        }
        
        /* Typography */
        h1, h2, h3, h4 {
            color: #0f172a;
            font-weight: bold;
            page-break-inside: avoid;
            page-break-after: avoid;
        }
        
        h1 {
            font-size: 20pt;
            border-bottom: 3px solid #3b82f6;
            padding-bottom: 8px;
            margin-top: 0;
            margin-bottom: 1cm;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1e3b8b;
        }
        
        h2 {
            font-size: 14pt;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 6px;
            margin-top: 1.2cm;
            margin-bottom: 0.6cm;
            color: #2563eb;
        }
        
        h3 {
            font-size: 12pt;
            margin-top: 0.8cm;
            margin-bottom: 0.4cm;
            color: #0d9488;
        }
        
        p {
            margin-top: 0;
            margin-bottom: 15px;
            text-align: justify;
            color: #334155;
        }
        
        ul, ol {
            margin-top: 0;
            margin-bottom: 20px;
            padding-left: 20px;
        }
        
        li {
            margin-bottom: 6px;
            text-align: justify;
            color: #334155;
        }

        /* CSS Numbered Step list */
        ol.step-list {
            list-style-type: none;
            padding-left: 0;
            margin-bottom: 20px;
            counter-reset: step-counter;
        }
        
        ol.step-list li {
            position: relative;
            padding-left: 32px;
            margin-bottom: 12px;
            text-align: justify;
        }
        
        ol.step-list li::before {
            content: counter(step-counter);
            counter-increment: step-counter;
            position: absolute;
            left: 0;
            top: 2px;
            width: 20px;
            height: 20px;
            background-color: #2563eb;
            color: #ffffff;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 8.5pt;
            font-weight: bold;
        }

        /* Custom Elements */
        .badge {
            background-color: #eff6ff;
            color: #1e40af;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8.5pt;
            font-family: Courier, monospace;
            border: 1px solid #bfdbfe;
        }
        
        .alert-box {
            background-color: #eff6ff;
            border-left: 4px solid #3b82f6;
            padding: 15px;
            margin-top: 20px;
            margin-bottom: 20px;
            border-radius: 0 8px 8px 0;
            page-break-inside: avoid;
        }
        
        .alert-box.warning {
            border-left-color: #f59e0b;
            background-color: #fffbeb;
        }
        
        .alert-box.success {
            border-left-color: #10b981;
            background-color: #f0fdf4;
        }
        
        .alert-box.caution {
            border-left-color: #ef4444;
            background-color: #fef2f2;
        }
        
        .alert-title {
            font-weight: bold;
            font-size: 10.5pt;
            margin-bottom: 6px;
            color: #0f172a;
        }
        
        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 25px;
            font-size: 9.5pt;
            page-break-inside: auto;
        }
        
        th, td {
            padding: 10px 12px;
            text-align: left;
            vertical-align: top;
            border: 1px solid #e2e8f0;
        }
        
        th {
            background-color: #eff6ff;
            color: #1e40af;
            font-weight: bold;
            border-bottom: 2px solid #bfdbfe;
        }
        
        tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        
        .table-title {
            font-weight: bold;
            font-size: 10pt;
            color: #2563eb;
            margin-top: 15px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
 
        /* Flow diagram placeholder */
        .diagram-container {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin: 25px 0;
            page-break-inside: avoid;
        }
        
        .diagram-title {
            font-weight: bold;
            color: #1e40af;
            margin-bottom: 12px;
            font-size: 10.5pt;
        }
        
        .diagram-step {
            display: inline-block;
            background: #ffffff;
            border: 1px solid #bfdbfe;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 9pt;
            font-weight: bold;
            color: #1e40af;
            margin: 5px;
        }
        
        .diagram-arrow {
            display: inline-block;
            color: #3b82f6;
            font-weight: bold;
            margin: 0 5px;
        }

        /* 2-Column Grid Card Layout for Premium Manual Book */
        .grid-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 12px;
            margin-top: 10px;
            margin-bottom: 15px;
            border: none !important;
            page-break-inside: auto !important;
        }
        
        .grid-table tr {
            border: none !important;
            background: none !important;
            page-break-inside: avoid;
        }
        
        .grid-table td {
            width: 50%;
            padding: 0px !important;
            border: none !important;
            background: none !important;
            vertical-align: top;
        }
        
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 4px solid #2563eb;
            border-radius: 8px;
            padding: 15px;
        }
        
        .card-title {
            font-weight: bold;
            font-size: 10.5pt;
            color: #2563eb;
            margin-bottom: 8px;
            border-bottom: 1px solid #eff6ff;
            padding-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .card-body {
            font-size: 9pt;
            line-height: 1.5;
            color: #334155;
            text-align: left;
        }

        .card-body ul, .card-body ol {
            margin-top: 4px;
            margin-bottom: 0px;
            padding-left: 15px;
        }

        .card-body li {
            margin-bottom: 4px;
            font-size: 9pt;
            text-align: left;
        }
    </style>
</head>
<body>

    <!-- Cover Page -->
    <div class="cover-container">
        <div class="cover-content">
            <div class="cover-accent-bar"></div>
            <div class="cover-header">
                <span class="cover-logo-text">PembdaHUB</span>
            </div>
            <h1 class="cover-title" style="border:none; margin:0; text-transform:none; letter-spacing:normal;">MANUAL BOOK<br>PANDUAN PENGGUNA</h1>
            <div class="cover-subtitle">Sistem Informasi Manajemen Sekolah Terpadu</div>
            <div class="cover-institution">
                <strong>Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA)</strong><br>
                Jl. Pelita No.9 Kelurahan Ilir Kota Gunungsitoli Propinsi Sumatera Utara (22815)
            </div>
            
            <div class="cover-meta">
                <table class="cover-meta-table">
                    <tr>
                        <td class="label">Versi Dokumen</td>
                        <td>3.0.0</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Rilis</td>
                        <td>11 Juni 2026</td>
                    </tr>
                    <tr>
                        <td class="label">Status</td>
                        <td>Production Ready (Overhauled)</td>
                    </tr>
                    <tr>
                        <td class="label">Penulis</td>
                        <td>Tim Development PembdaHUB</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Header & Footer (Repeating starting page 2) -->
    <div class="header">
        PembdaHUB - Manual Book Panduan Pengguna
    </div>
    
    <div class="footer">
        <table style="width:100%; border:none; margin:0; padding:0; border-collapse:collapse;">
            <tr style="background:none;">
                <td style="border:none; padding:0; text-align:left; color:#64748b; font-size:8.5pt;">Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA)</td>
                <td style="border:none; padding:0; text-align:right; color:#64748b; font-size:8.5pt;">Halaman <span class="page-number"></span></td>
            </tr>
        </table>
    </div>

    <!-- Halaman Daftar Isi -->
    <div>
        <h1>Daftar Isi</h1>
        <table style="border: none; margin-top: 0.5cm; font-size: 11pt;">
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold; width: 85%;">1. PENDAHULUAN</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">3</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">1.1 Visi &amp; Misi Sistem | 1.2 Persyaratan Sistem</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">3</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">2. MEMULAI APLIKASI (GETTING STARTED)</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">4</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">2.1 Akses &amp; Login Pertama | 2.2 Ganti Password | 2.3 Peran Ganda | 2.4 Quick Start</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">4</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">3. LEVEL &amp; HAK AKSES PENGGUNA (ROLES)</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">6</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">3.1 Perincian Tugas 9 Level Pengguna (Termasuk Kepala Sekolah &amp; Yayasan)</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">6</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">4. PETA MENU UTAMA APLIKASI</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">8</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">4.1 Panel Admin | 4.2 Keuangan | 4.3 Guru &amp; Kepsek | 4.4 Siswa/Ortu | 4.5 Yayasan | 4.6 Elite</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">8</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">5. INTEGRASI ALUR KERJA UTAMA SISTEM</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">10</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">5.1 PSB | 5.2 Jadwal | 5.3 LMS &amp; CBT | 5.4 Rapor | 5.5 P5 | 5.6 Keuangan | 5.7 Payroll | 5.8 PKL | 5.9 Proyek</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">10</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">6. PANDUAN OPERASIONAL LANGKAH DEMI LANGKAH</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">12</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">6.1 Admin &amp; Super Admin (A s.d. Q)</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">12</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">6.2 Bendahara (A s.d. C)</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">17</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">6.3 Guru &amp; Wali Kelas / Kepala Sekolah (A s.d. J)</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">18</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">6.4 Siswa &amp; Orang Tua (A s.d. G)</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">21</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">6.5 Yayasan (A s.d. C) | 6.6 Fitur Lintas-Peran (A s.d. C)</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">24</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">7. PENYELESAIAN MASALAH (TROUBLESHOOTING)</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">26</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; padding-left: 20px; color: #475569;">7.1 s.d. 7.10 Solusi Masalah Teknis Harian Terperinci</td>
                <td style="border: none; padding: 6px 0; text-align: right; color: #475569;">26</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">8. DATA SIMULASI &amp; PENGUJIAN PLATFORM (SEEDER)</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">28</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">9. SISTEM REPUTASI &amp; POIN KEAKTIFAN TERINTEGRASI</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">29</td>
            </tr>
            <tr style="background: none;">
                <td style="border: none; padding: 6px 0; font-weight: bold;">10. GLOSARIUM &amp; DAFTAR ISTILAH (GLOSSARY)</td>
                <td style="border: none; padding: 6px 0; text-align: right; font-weight: bold;">30</td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Bab 1 -->
    <div>
        <h1>1. Pendahuluan</h1>
        <p>
            <strong>PembdaHUB</strong> adalah sistem manajemen sekolah terintegrasi berbasis web yang dirancang khusus untuk mengelola operasional akademik, keuangan, kepegawaian, pembelajaran, dan ujian di bawah naungan <strong>Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA)</strong>.
        </p>
        <p>
            Sistem ini mendukung pengelolaan terintegrasi secara dinamis bagi 3 unit sekolah di bawah yayasan:
        </p>
        <ol>
            <li><strong>SMPS Pembda 2 Gunungsitoli</strong> (Sekolah Menengah Pertama)</li>
            <li><strong>SMA Swasta Pembda 1 Gunungsitoli</strong> (Sekolah Menengah Atas)</li>
            <li><strong>SMKS Swasta Pembda Nias</strong> (Sekolah Menengah Kejuruan)</li>
        </ol>

        <h2>1.1 Visi &amp; Misi Sistem</h2>
        <ul>
            <li><strong>Sentralisasi Data:</strong> Mengintegrasikan data master siswa, guru, kelas, dan alumni dalam satu database tunggal untuk menghindari duplikasi data.</li>
            <li><strong>Transparansi Keuangan:</strong> Mempermudah pelacakan pembayaran uang sekolah (SPP/UPP/DPP), denda keterlambatan secara otomatis, dan pelaporan keuangan real-time.</li>
            <li><strong>Digitalisasi Akademik:</strong> Menyediakan media e-learning (LMS), ujian berbasis komputer (CBT) anti-curang, absensi waktu-nyata, dan penerbitan Rapor Digital (PDF) otomatis.</li>
            <li><strong>Efisiensi SDM:</strong> Menghitung beban mengajar guru secara riil untuk dasar penggajian (slip gaji) bulanan yang akurat berdasarkan status kepegawaian.</li>
        </ul>

        <h2>1.2 Persyaratan Sistem (System Requirements)</h2>
        <p>
            Untuk memastikan aplikasi PembdaHUB berjalan dengan lancar, pastikan perangkat keras dan perangkat lunak Anda memenuhi syarat berikut:
        </p>
        <ul>
            <li><strong>Peramban (Browser):</strong> Google Chrome, Mozilla Firefox, Microsoft Edge, atau Safari versi terbaru (direkomendasikan Chrome v100+).</li>
            <li><strong>Resolusi Layar:</strong> Minimal 1280x720 piksel (resolusi laptop standar); direkomendasikan 1366x768 piksel ke atas untuk kenyamanan melihat Schedule Grid dan panel keuangan.</li>
            <li><strong>Koneksi Internet:</strong> Stabil dengan kecepatan minimal 2 Mbps untuk penggunaan harian, dan minimal 10 Mbps untuk pelaksanaan Ujian Online (CBT).</li>
        </ul>

        <div class="alert-box">
            <div class="alert-title">ℹ️ Catatan Sistem Terintegrasi</div>
            PembdaHUB mengadopsi Single Sign-On (SSO) internal, di mana akun pengguna dapat bertransisi dengan mulus bergantung kepada peranan (role) yang telah diatur oleh administrator.
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Bab 2 -->
    <div>
        <h1>2. Memulai Aplikasi (Getting Started)</h1>
        <p>
            Selamat datang di PembdaHUB! Bagian ini dirancang untuk mempermudah pengguna baru dalam mengakses sistem, melakukan login, serta memahami fungsionalitas dasar pertama kali.
        </p>

        <h2>2.1 Cara Akses &amp; Login Pertama Kali</h2>
        <ol class="step-list">
            <li>Buka browser Anda dan masukkan alamat website sekolah: <code>http://perguruanpembda.com</code> (atau <code>http://localhost/PembdaHUB</code> di server lokal).</li>
            <li>Klik tombol <strong>Login</strong> pada menu kanan atas halaman utama.</li>
            <li>Masukkan <strong>Username</strong> (atau email) dan <strong>Kata Sandi</strong> default yang diberikan oleh pihak sekolah.</li>
            <li>Tekan tombol <strong>Masuk</strong>.</li>
        </ol>

        <h2>2.2 Mengganti Kata Sandi (Change Password)</h2>
        <div class="alert-box warning">
            <div class="alert-title">⚠️ Wajib Ganti Kata Sandi Pertama Kali</div>
            Demi keamanan data Anda, sistem akan secara otomatis memaksa pengguna baru untuk mengganti kata sandi bawaan pada saat login pertama kali sebelum dapat mengakses dashboard utama.
        </div>
        <p>Prosedur mengganti kata sandi setelah masuk:</p>
        <ol class="step-list">
            <li>Sistem akan mengalihkan Anda ke halaman <strong>Ubah Password</strong> secara otomatis jika ini adalah login pertama Anda.</li>
            <li>Masukkan kata sandi lama Anda di kolom pertama.</li>
            <li>Masukkan kata sandi baru Anda di kolom kedua (minimal 8 karakter, kombinasi huruf dan angka).</li>
            <li>Masukkan ulang kata sandi baru Anda di kolom verifikasi, lalu klik <strong>Update Password</strong>.</li>
            <li>Setelah berhasil, Anda akan dialihkan secara aman ke dashboard utama sesuai peranan Anda.</li>
        </ol>

        <h2>2.3 Memahami Peran Ganda (Switch Role)</h2>
        <p>
            PembdaHUB mendukung peran ganda tanpa perlu logout. Misalnya, seorang <strong>Guru</strong> yang juga bertindak sebagai <strong>Wali Kelas</strong> atau <strong>Kepala Sekolah</strong> dapat beralih peran dengan cepat menggunakan menu <strong>Switch Role</strong>.
        </p>
        <ol class="step-list">
            <li>Temukan tombol beralih peran pada bagian kiri menu navigasi / sidebar.</li>
            <li>Klik tombol peran yang dituju (misal: "Beralih ke Wali Kelas" atau "Beralih ke Kepala Sekolah").</li>
            <li>Sistem akan memperbarui menu sidebar secara instan sesuai peran baru tanpa perlu memasukkan password lagi.</li>
        </ol>

        <h2>2.4 Quick Start: Langkah Pertama Setiap Peran</h2>
        <table style="font-size: 9pt;">
            <thead>
                <tr>
                    <th style="width: 25%;">Peran Pengguna</th>
                    <th>Hal Pertama yang Harus Dilakukan (Langkah Cepat)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold; color: #1e3b8b;">Admin Sekolah</td>
                    <td>Perbarui <strong>Profil Sekolah &amp; KKM</strong>, atur <strong>Tahun Ajaran &amp; Semester</strong> aktif, lalu import data <strong>Guru &amp; Siswa</strong>.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #1e3b8b;">Bendahara</td>
                    <td>Periksa data rombel, lalu gunakan menu <strong>Tagihan Massal</strong> untuk menerbitkan SPP bulan berjalan bagi siswa.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #1e3b8b;">Guru</td>
                    <td>Buka menu <strong>Jadwal Mengajar</strong> untuk melihat kelas Anda, buat <strong>Course LMS</strong> baru, dan unggah materi ajar.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #1e3b8b;">Siswa</td>
                    <td>Lengkapi data profil Anda, periksa <strong>Jadwal Pelajaran</strong> harian, masuk ke LMS untuk membaca materi pelajaran aktif.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold; color: #1e3b8b;">Orang Tua</td>
                    <td>Pilih profil anak Anda di drop-down, periksa status <strong>Presensi Hari Ini</strong>, dan lihat rincian tagihan uang sekolah yang aktif.</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Bab 3 -->
    <div>
        <h1>3. Level &amp; Hak Akses Pengguna</h1>
        <p>
            PembdaHUB menerapkan kontrol akses berbasis peran (<em>Role-Based Access Control / RBAC</em>) yang ketat. Terdapat <strong>9 level pengguna</strong> dengan rincian tugas dan fungsi masing-masing:
        </p>

        <h2>3.1 Perincian Tugas 9 Level Pengguna</h2>
        
        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">1. Super Admin</div>
                        <div class="card-body">
                            Administrator utama sistem di bawah Yayasan yang memegang kontrol penuh atas konfigurasi dan seluruh unit sekolah. Tugas utama meliputi pengelolaan akun pengguna, penugasan peranan, reset kata sandi massal atau individual, konfigurasi profil sekolah global, dan memantau dashboard analitik gabungan seluruh unit sekolah.
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">2. Admin Sekolah</div>
                        <div class="card-body">
                            Pengelola administrasi spesifik untuk satu unit sekolah (SMP/SMA/SMK). Tugas utama meliputi pengelolaan data master siswa, guru, rombel/kelas, konfigurasi kurikulum sekolah, KKM, penyusunan jadwal pelajaran visual (<em>Schedule Grid</em>), verifikasi Penerimaan Siswa Baru (PSB), pengelolaan penempatan PKL (khusus SMK) &amp; Alumni, serta konfigurasi format &amp; plotting pembimbing/penguji Tugas Akhir Kelas XII.
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">3. Bendahara (Treasurer)</div>
                        <div class="card-body">
                            Pengelola administrasi keuangan sekolah (SPP, DPP, uang seragam, buku, dan biaya penunjang lainnya). Tugas utama meliputi pembuatan tagihan bulanan secara massal maupun individual, pencatatan pembayaran cicilan atau lunas, pemutihan (<em>waive</em>) denda keterlambatan siswa, dan pencetakan kuitansi pembayaran resmi (PDF).
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">4. Guru</div>
                        <div class="card-body">
                            Pendidik yang mengelola pembelajaran di kelas masing-masing. Tugas utama meliputi pengisian absensi harian bulk, penginputan nilai, pengelolaan materi &amp; forum LMS, CBT online exam, sinkronisasi nilai ke rapor, monitoring jurnal magang siswa PKL (khusus SMK), serta bertindak sebagai Pembimbing (feedback logbook &amp; kelayakan) atau Penguji sidang akhir Tugas Akhir Kelas XII.
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">5. Wali Kelas</div>
                        <div class="card-body">
                            Guru yang ditugaskan memimpin satu rombongan belajar (kelas). Wali Kelas mendapatkan semua menu Guru ditambah menu administrasi kelas dan rapor. Tugas utama meliputi pemantauan keaktifan dan absensi siswa kelas bimbingannya, menulis catatan perkembangan dan bimbingan konseling (BK), memproses generate Rapor Digital, mengisi deskripsi catatan rapor, mempublikasikan rapor agar bisa diunduh siswa/orang tua, dan mengunduh rapor kelas secara kolektif (ZIP).
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">6. Kepala Sekolah</div>
                        <div class="card-body">
                            Memegang hak akses pemantauan makro atas kinerja akademik unit sekolah yang dipimpinnya. Kepala Sekolah dapat melihat absensi guru/pegawai, memantau sebaran nilai siswa, meninjau rancangan pembelajaran di LMS/CBT, dan menyetujui cetak Rapor kelas. Dapat beralih peran kembali ke guru aktif.
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">7. Siswa</div>
                        <div class="card-body">
                            Pengguna akhir untuk pembelajaran &amp; evaluasi mandiri. Tugas utama meliputi melihat jadwal, mengakses LMS (materi, tugas, kuis), mengikuti ujian online CBT, mengisi jurnal harian magang PKL (khusus SMK), mengajukan judul Tugas Akhir/Proyek Kelas XII, memperbarui logbook bimbingan, mengunggah draf laporan, dan monitoring jadwal sidang akhir.
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">8. Orang Tua (Wali)</div>
                        <div class="card-body">
                            Memantau perkembangan akademik dan kewajiban administrasi anak mereka secara real-time. Orang tua dapat melihat absensi harian, nilai tugas dan ujian anak, mengunduh Rapor Digital anak secara mandiri (PDF) setelah dirilis oleh Wali Kelas, memantau status tagihan SPP dan denda berjalan, serta melihat catatan konseling anak.
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="border-left-color: #0d9488;">
                        <div class="card-title" style="color: #0d9488;">9. Yayasan (Ketua Yayasan)</div>
                        <div class="card-body">
                            Pengawas dan pemegang kebijakan makro untuk memantau perkembangan sekolah dan sirkulasi keuangan. Tugas utama meliputi memantau statistik pendaftar PSB di seluruh unit, melihat neraca kas keuangan bulanan (pendapatan vs tunggakan) per unit sekolah, serta memantau total pengeluaran penggajian (payroll) bulanan dan beban kerja riil guru di bawah naungan Yayasan.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Bab 4 -->
    <div>
        <h1>4. Peta Menu Utama Aplikasi</h1>
        <p>
            Berikut adalah peta menu sidebar utama yang disajikan kepada pengguna berdasarkan peranan masing-masing untuk memberikan navigasi yang cepat dan efisien.
        </p>

        <h2>4.1 Panel Administrator &amp; Admin Sekolah</h2>
        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Dashboard &amp; Manajemen User</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Dashboard:</strong> Statistik siswa aktif, guru, kelas, grafik bulanan penerimaan kas, dan jumlah pendaftar PSB.</li>
                                <li><strong>Manajemen User:</strong> Tambah pengguna, edit profil, ubah peranan, dan reset password.</li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Sekolah &amp; Kelas</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Unit Sekolah:</strong> Pengaturan profil sekolah dan KKM masing-masing unit.</li>
                                <li><strong>Periode Akademik:</strong> Konfigurasi Tahun Ajaran &amp; Semester aktif.</li>
                                <li><strong>Rombel / Kelas:</strong> Daftar kelas, kuota daya tampung, pengelompokan jurusan (SMK), dan penugasan Wali Kelas.</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Data Master &amp; Jam Pelajaran</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Siswa:</strong> Database siswa aktif, impor Excel/CSV, foto, dan set RFID.</li>
                                <li><strong>Guru &amp; Pegawai:</strong> Database guru, kompetensi mengajar, dan jabatan.</li>
                                <li><strong>Mata Pelajaran:</strong> Database mata pelajaran dan impor massal.</li>
                                <li><strong>Jam Pelajaran (Slots):</strong> Pengaturan slot mengajar (SMK s.d. 15 slots).</li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Akademik &amp; Jadwal Grid</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Penugasan:</strong> Menghubungkan Guru, Mapel, dan Kelas.</li>
                                <li><strong>Jadwal Pelajaran (Grid):</strong> Penyusunan jadwal pelajaran visual dengan deteksi bentrok mengajar/ruang kelas.</li>
                                <li><strong>Monitoring &amp; BK:</strong> Rekap absensi harian dan rekam pembinaan konseling.</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Kepegawaian &amp; Payroll</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>SDM &amp; Absensi:</strong> Statistik pegawai (PNS/Yayasan/Honorer/Kontrak), rekap kehadiran, dan cuti.</li>
                                <li><strong>Beban Kerja (Workload):</strong> Hitung total jam mengajar guru rill vs wajib, tunjangan, dan kunci data beban kerja.</li>
                                <li><strong>Laporan Gaji:</strong> Gaji Pokok, tunjangan Wali Kelas, slip gaji bulanan, ekspor rekap ke CSV, dan cetak slip PDF.</li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">PSB &amp; Fitur Tambahan</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>PSB (Penerimaan Baru):</strong> Verifikasi biaya formulir, tes masuk, kelulusan, dan migrasi siswa baru.</li>
                                <li><strong>Notifikasi WhatsApp:</strong> Monitoring WhatsApp Fonnte API untuk pesan otomatis.</li>
                                <li><strong>CBT, LMS &amp; Publik:</strong> Monitoring CBT/LMS, Badges siswa berprestasi, dan web berita/galeri.</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Kelola PKL &amp; Alumni</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Penempatan PKL:</strong> Mitra industri, pembimbing internal, mentor industri, dan periode magang siswa SMK.</li>
                                <li><strong>Tracer Study:</strong> Survey keterserapan lulusan (BMW) dari seluruh unit sekolah.</li>
                                <li><strong>Lowongan Kerja:</strong> Papan pengumuman lowongan kerja (Job Board) terintegrasi.</li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Pengendalian Proyek / Tugas Akhir</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Format Tugas Akhir:</strong> Upload draf template format panduan penulisan per unit.</li>
                                <li><strong>Pengajuan &amp; Plotting:</strong> Review pengajuan judul kelas XII dan plotting Guru Pembimbing.</li>
                                <li><strong>Jadwal Ujian:</strong> Plotting Guru Penguji, tanggal, waktu, dan ruang sidang akhir.</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <h2>4.2 Panel Keuangan &amp; Operasional Pengguna</h2>
        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Treasurer (Bendahara)</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Dashboard:</strong> Grafik tren kas masuk bulanan dan rasio pelunasan tagihan kelas.</li>
                                <li><strong>Tagihan (Bills):</strong> Pembuatan tagihan massal per tingkat kelas (SPP/Seragam/Buku), filter tagihan, dan denda terlambat.</li>
                                <li><strong>Pembayaran (Payments):</strong> Formulir terima pembayaran lunas/cicilan berdasarkan NISN/Nama siswa, log kas masuk, pemutihan denda, dan kuitansi PDF.</li>
                                <li><strong>Laporan Keuangan:</strong> Rekap penerimaan kas bulanan, semester, dan tunggakan tagihan per rombel.</li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Guru &amp; Wali Kelas / Kepala Sekolah</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Akademik:</strong> Jadwal mengajar mingguan dan input absensi harian bulk (Set All Hadir/Sakit/Izin/Alpha).</li>
                                <li><strong>Nilai &amp; Sikap:</strong> Input nilai tugas, UTS, UAS, dan sikap secara bulk.</li>
                                <li><strong>Asisten AI:</strong> AI RPP Generator dan AI Pembuat Soal CBT otomatis.</li>
                                <li><strong>LMS (E-Learning):</strong> Membuat course, unggah file/video, kelola tugas (assignments) dengan deadline, kuis, dan forum diskusi.</li>
                                <li><strong>CBT (Ujian):</strong> Bank Soal, token ujian, monitoring siswa, force submit, penilaian manual esai, dan sinkronisasi nilai ke rapor.</li>
                                <li><strong>Rapor (Wali Kelas):</strong> Generate rapor (kalkulasi otomatis nilai akhir dan absensi), Rapor Projek P5, rilis rapor PDF, dan unduh ZIP.</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Siswa &amp; Orang Tua</div>
                        <div class="card-body">
                            <strong>Portal Siswa:</strong>
                            <ul>
                                <li>Melihat jadwal harian, nilai, rekap absensi, bimbingan konseling (BK), dan rincian tagihan berjalan.</li>
                                <li>Mengakses LMS (materi, upload tugas, kuis) dan mengikuti ujian online CBT dengan sistem anti-cheat (tab switches limit).</li>
                                <li><strong>Magang/PKL:</strong> Mengisi jurnal logbook aktivitas PKL harian di industri (khusus SMK).</li>
                                <li><strong>Proyek &amp; Tugas Akhir:</strong> Pengajuan judul proyek, download format panduan, pengisian logbook bimbingan, dan monitoring jadwal sidang.</li>
                            </ul>
                            <strong>Portal Orang Tua (Wali):</strong>
                            <ul>
                                <li>Memilih anak, memantau kehadiran, nilai akademik, status pembayaran tagihan &amp; denda berjalan.</li>
                                <li>Melihat catatan bimbingan konseling dan mengunduh Rapor Digital PDF anak secara mandiri.</li>
                            </ul>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card" style="background-color: #f0f9ff; border-left-color: #0d9488;">
                        <div class="card-title" style="color: #0d9488;">Pembda Elite (Fitur Lintas Peran)</div>
                        <div class="card-body">
                            <ul>
                                <li><strong>Hub Forum &amp; Kolaborasi:</strong> Forum diskusi global, partisipasi kepanitiaan/proyek, dan donasi sosial.</li>
                                <li><strong>Papan Peringkat:</strong> Monitoring Hall of Fame perolehan poin keaktifan siswa &amp; guru.</li>
                                <li><strong>Pelatihan PembdaHUB:</strong> Modul pelatihan belajar mandiri bagi semua peran.</li>
                                <li><strong>Yayasan:</strong> Executive Dashboard, kirim Undangan Pelatihan, monitoring unit pendidikan, sirkulasi keuangan, payroll, &amp; beban kerja.</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Bab 5 -->
    <div>
        <h1>5. Integrasi Alur Kerja Utama Sistem</h1>
        <p>
            PembdaHUB menyatukan berbagai modul operasional sekolah untuk menciptakan ekosistem manajemen pendidikan tanpa kertas (<em>paperless</em>) yang terpadu.
        </p>

        <div class="diagram-container">
            <div class="diagram-title">ALUR UTAMA OPERASIONAL PEMBDAHUB</div>
            <div class="diagram-step">Formulir Online PSB</div>
            <div class="diagram-arrow">&gt;</div>
            <div class="diagram-step">Seleksi &amp; Ujian Masuk</div>
            <div class="diagram-arrow">&gt;</div>
            <div class="diagram-step">Migrasi Ke Siswa Aktif</div>
            <div class="diagram-arrow">&gt;</div>
            <div class="diagram-step">Rombel &amp; Jadwal Grid</div>
            <br><br>
            <div class="diagram-arrow">v</div>
            <br>
            <div class="diagram-step">LMS &amp; CBT Ujian</div>
            <div class="diagram-arrow">&gt;</div>
            <div class="diagram-step">Penilaian &amp; Absensi</div>
            <div class="diagram-arrow">&gt;</div>
            <div class="diagram-step">Rapor Digital PDF</div>
            <br><br>
            <div class="diagram-arrow">v</div>
            <br>
            <div class="diagram-step">Beban Kerja Guru</div>
            <div class="diagram-arrow">&gt;</div>
            <div class="diagram-step">Kalkulasi Gaji (Payroll)</div>
            <div class="diagram-arrow">&gt;</div>
            <div class="diagram-step">Slip Gaji PDF Guru</div>
        </div>

        <h2>5.1 Rincian Alur Kerja Terintegrasi</h2>
        <p>
            Berikut adalah detail alur operasional beserta bagan alir (flowchart) antar modul yang saling terhubung di dalam sistem PembdaHUB:
        </p>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Alur PSB ke Siswa Aktif</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Pendaftaran:</strong> Calon siswa mendaftar online via situs publik, memilih unit sekolah (dan program/konsentrasi keahlian untuk SMK).</li>
                                <li><strong>Notifikasi:</strong> Sistem mengirimkan nomor registrasi (misal: <code>PSB-SMKS-2026-0001</code>) via WhatsApp (Fonnte API) ke nomor orang tua.</li>
                                <li><strong>Verifikasi:</strong> Admin memeriksa pembayaran formulir dan dokumen pendukung. Status naik ke <span class="badge">document_verified</span>.</li>
                                <li><strong>Seleksi:</strong> Tes masuk dilaksanakan, skor diinput, status diubah menjadi <span class="badge">scored</span> lalu diputuskan <span class="badge">accepted</span>.</li>
                                <li><strong>Migrasi:</strong> Siswa membayar daftar ulang, Admin menekan tombol <strong>Migrate</strong>. Sistem otomatis membuat data siswa, akun user, dan mengirimkan kredensial login.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Daftar Online</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Verifikasi Berkas</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Tes Masuk</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Bayar</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Migrasi Aktif</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Alur Penjadwalan &amp; Kegiatan Belajar</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Time Slots:</strong> Admin membagi jam pelajaran harian di menu *Time Slots* (SMP 8 slots, SMK 15 slots).</li>
                                <li><strong>Jadwal Grid:</strong> Admin menyusun jadwal pada Grid visual drag-and-drop. Sistem memvalidasi waktu-nyata:
                                    <ul>
                                        <li><strong>Bentrok Guru:</strong> Mencegah guru mengajar di dua kelas berbeda pada jam yang sama.</li>
                                        <li><strong>Bentrok Ruang:</strong> Mencegah ruang kelas digunakan dua kelas pada jam yang sama.</li>
                                        <li><strong>Kompetensi:</strong> Memastikan kompetensi mengajar guru cocok.</li>
                                    </ul>
                                </li>
                                <li><strong>Penyebaran:</strong> Jadwal otomatis terbit di portal Guru dan Siswa.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Time Slots</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Penugasan Guru</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Jadwal Grid</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Deteksi Bentrok</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Jadwal Rilis</span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Alur Pembelajaran LMS &amp; CBT Ujian</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>LMS &amp; Kelas Virtual:</strong> Guru membuat *course*, mengunggah materi, dan dapat memulai kelas tatap muka virtual (Jitsi Meet). Siswa dapat bergabung dengan satu klik.</li>
                                <li><strong>Notifikasi Otomatis WA:</strong> Setiap materi/tugas baru, kuis dirilis, atau kelas dimulai, sistem mengirimkan notifikasi instan ke WhatsApp seluruh siswa secara bulk.</li>
                                <li><strong>Kuis Pengacakan Seeded:</strong> Soal kuis dan pilihan jawaban diacak secara seeded. Urutan tetap konsisten jika siswa me-refresh halaman guna mencegah kecurangan.</li>
                                <li><strong>CBT Premium:</strong> Lembar ujian CBT mendukung pengetikan rumus KaTeX (LaTeX). Jawaban siswa disimpan offline-first di <code>localStorage</code> jika koneksi putus, lalu di-sync otomatis ketika online kembali.</li>
                                <li><strong>Kontrol Jeda (Pause/Resume):</strong> Guru/Admin dapat menjeda ujian global. Layar siswa otomatis memunculkan overlay kunci, dan deadline waktu disesuaikan otomatis pasca ujian dilanjutkan kembali.</li>
                                <li><strong>Analisis Psikometrik:</strong> Nilai CBT ter-kalkulasi otomatis (PG) dan esai (manual). Guru dapat meninjau tab Analisis Butir Soal sebelum mensinkronisasikan nilai ke Rapor.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Buat Course</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Materi &amp; Tugas</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Ujian CBT</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Jeda/Resume</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Sync Nilai</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Alur Penilaian &amp; Rapor Digital</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Kalkulasi Nilai:</strong> Wali kelas menekan <strong>Generate Raport</strong>. Sistem mengambil nilai tugas (20%), UTS (30%), UAS (40%), dan sikap (10%) dari tabel <code>grades</code>.</li>
                                <li><strong>Absensi Rill:</strong> Data kehadiran siswa (hadir, sakit, izin, alpha) ditarik otomatis dari database absensi selama semester.</li>
                                <li><strong>Persetujuan:</strong> Wali kelas mengisi deskripsi sikap/catatan perkembangan, lalu mengunci status rapor (<strong>Finalize</strong> &amp; <strong>Publish</strong>).</li>
                                <li><strong>Unduh Rapor:</strong> Orang tua/siswa mengunduh file PDF Rapor resmi berstandar nasional langsung dari portal mereka.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Input Grades</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Generate Rapor</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Catatan Sikap</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Lock &amp; Finalize</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Publish PDF</span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Alur Keuangan, SPP &amp; Denda</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Penerbitan:</strong> Bendahara menerbitkan tagihan SPP bulanan otomatis pada tanggal 1 setiap bulannya.</li>
                                <li><strong>Denda Otomatis:</strong> Pembayaran melewati jatuh tempo (tanggal 10) otomatis dikenakan denda bulanan (misal Rp 10.000) yang terakumulasi di portal siswa/ortu.</li>
                                <li><strong>Terima Pembayaran:</strong> Bendahara menginput pembayaran masuk (lunas atau cicilan/parsial) berdasarkan pencarian NISN/Nama siswa.</li>
                                <li><strong>WhatsApp Kuitansi:</strong> Setelah disimpan, sistem menerbitkan kuitansi PDF dan otomatis mengirimkan tanda terima pembayaran ke nomor WhatsApp orang tua.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Tagihan SPP</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Jatuh Tempo</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Kalkulasi Denda</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Terima Uang</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Kuitansi WA</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Alur SDM &amp; Penggajian (Payroll)</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Beban Mengajar:</strong> Sistem menghitung total jam pelajaran mengajar riil guru berdasarkan jadwal pelajaran aktif yang diampunya.</li>
                                <li><strong>Jam Honor:</strong> Kelebihan jam mengajar riil di atas jam wajib (misal: 24 jam untuk PNS) otomatis dihitung sebagai jam honor tambahan.</li>
                                <li><strong>Payroll Bulanan:</strong> Gaji pokok, tunjangan struktural/wali kelas, tunjangan keluarga/anak/beras, dan honor jam mengajar tambahan dikalkulasikan otomatis.</li>
                                <li><strong>Kunci &amp; Slip PDF:</strong> Gaji divalidasi dan dikunci (*lock*) oleh Admin SDM. Slip Gaji PDF resmi diterbitkan langsung ke akun guru masing-masing.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Workload Jam</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Kalkulasi Honor</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Tunjangan</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Lock Payroll</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Slip Gaji PDF</span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">Alur Kerja PKL &amp; Magang Terintegrasi</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Penempatan:</strong> Admin mem-plot siswa PKL ke perusahaan mitra, pembimbing internal, dan mentor industri.</li>
                                <li><strong>Signed Link:</strong> Sistem otomatis mengirimkan tautan akses pintar (*Signed URL*) ke email/WA Mentor Industri.</li>
                                <li><strong>Logbook Jurnal:</strong> Siswa PKL mengisi jurnal harian magang dan mengunggah foto bukti fisik di portal Siswa.</li>
                                <li><strong>Verifikasi Mentor:</strong> Mentor me-review, menyetujui, atau menolak jurnal langsung via Signed Link tanpa login.</li>
                                <li><strong>Nilai Industri:</strong> Mentor mengisi nilai kompetensi akhir magang siswa langsung dari portal mentor di browser.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Plot Penempatan</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Signed URL WA</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Jurnal Siswa</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Approve Mentor</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Input Nilai</span>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">Alur Pengendalian Proyek &amp; Tugas Akhir</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Pengajuan Judul:</strong> Siswa kelas XII mengajukan judul proyek/karya tulis ilmiah beserta abstrak di portal Siswa.</li>
                                <li><strong>Plotting Guru:</strong> Admin menunjuk Guru Pembimbing yang sesuai bidang keilmuan.</li>
                                <li><strong>Konsultasi &amp; Logbook:</strong> Siswa mengisi logbook kemajuan bimbingan. Guru me-review, memberi feedback, dan menyetujui logbook (siswa +10 poin, guru +15 poin).</li>
                                <li><strong>Kelayakan Ujian:</strong> Guru Pembimbing menandai status kelayakan sidang (<code>Ready for Exam</code>). Siswa memperoleh bonus +50 poin.</li>
                                <li><strong>Sidang &amp; Nilai:</strong> Admin menjadwalkan tanggal/ruang sidang dan Guru Penguji. Guru Penguji memasukkan nilai akhir presentasi (kelulusan memberikan siswa +100 poin, penguji +30 poin).</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Usulan Judul</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Plot Pembimbing</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Logbook &amp; Ready</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Jadwal Sidang</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Sidang &amp; Nilai</span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="border-left-color: #0d9488;">
                        <div class="card-title" style="color: #0d9488;">Alur P5 (Projek Penguatan Profil Pelajar Pancasila) [NEW!]</div>
                        <div class="card-body">
                            <ol>
                                <li><strong>Inisiasi Projek:</strong> Guru membuat projek P5 baru, menentukan tema (Gaya Hidup Berkelanjutan, Bhinneka Tunggal Ika, dll.), judul, dan deskripsi projek.</li>
                                <li><strong>Target Dimensi:</strong> Guru mengaitkan projek dengan dimensi profil pelajar pancasila (Beriman, Mandiri, Gotong Royong, Bernalar Kritis, dll.) beserta sub-elemen kompetensi.</li>
                                <li><strong>Penilaian Sikap:</strong> Guru menginput nilai perkembangan siswa per sub-elemen menggunakan kategori: Belum Berkembang (BB), Mulai Berkembang (MB), Berkembang Sesuai Harapan (BSH), Sangat Berkembang (SB).</li>
                                <li><strong>Catatan Projek:</strong> Guru menulis catatan proses perkembangan projek siswa secara individual.</li>
                                <li><strong>Rapor P5:</strong> Sistem merender nilai menjadi Rapor P5 Digital (PDF) berformat nasional yang siap dicetak untuk siswa dan orang tua.</li>
                            </ol>
                            <div style="margin-top: 12px; padding: 8px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; text-align: center; font-size: 7.5pt;">
                                <span style="font-weight: bold; color: #1e3b8b;">Buat Projek P5</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Target Dimensi</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Input Nilai (BB-SB)</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #1e3b8b;">Catatan Sikap</span>
                                <span style="color: #3b82f6; font-weight: bold; margin: 0 2px;">&rarr;</span>
                                <span style="font-weight: bold; color: #10b981;">Cetak Rapor P5 PDF</span>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Bab 6: Panduan Operasional Langkah Demi Langkah -->
    <div>
        <h1>6. Panduan Operasional Langkah Demi Langkah</h1>
        <p>
            Bagian ini memberikan panduan operasional klik-demi-klik untuk melakukan tugas-tugas utama harian di aplikasi PembdaHUB berdasarkan masing-masing peran pengguna.
        </p>

        <h2>6.1 Panduan untuk Administrator &amp; Admin Sekolah</h2>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">A. Konfigurasi Profil Sekolah &amp; KKM Awal</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Data Master</strong> &rarr; <strong>Kelola Sekolah</strong>.</li>
                                <li>Pilih unit sekolah (SMP/SMA/SMK) lalu klik <strong>Edit</strong>.</li>
                                <li>Isi data profil sekolah: NPSN, Nama Sekolah, Nama Kepala Sekolah, NIP Kepala Sekolah, Alamat, dan Kriteria Ketuntasan Minimal (KKM).</li>
                                <li>Klik <strong>Simpan Perubahan</strong>. Profil ini akan menjadi kop surat resmi Rapor PDF dan Kuitansi SPP.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">B. Setup Tahun Ajaran &amp; Semester</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Data Master</strong> &rarr; <strong>Tahun Ajaran &amp; Semester</strong>.</li>
                                <li>Untuk membuat baru, klik <strong>Tambah Tahun Ajaran</strong> (misal: "2026/2027").</li>
                                <li>Untuk mengaktifkan periode akademik berjalan, klik tombol <strong>Aktifkan</strong> di samping tahun ajaran dan pilih semester aktif (Ganjil / Genap).</li>
                                <li>Sistem akan otomatis menutup periode lama dan membuka periode belajar baru.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">C. Setup Kompetensi, Program, &amp; Konsentrasi Keahlian (SMK)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Data Master</strong> &rarr; <strong>Kompetensi Keahlian</strong>.</li>
                                <li>Input bidang keahlian, program keahlian (misal: "Teknik Otomotif"), dan konsentrasi keahlian (misal: "Teknik Kendaraan Ringan Otomotif").</li>
                                <li>Klik <strong>Simpan</strong>. Data ini akan terhubung pada rombel kelas dan pilihan pendaftaran PSB SMK.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">D. Setup Jam Pelajaran (Time Slots)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Data Master</strong> &rarr; <strong>Jam Pelajaran (Time Slots)</strong>.</li>
                                <li>Pilih unit sekolah. Klik <strong>Tambah Slot Jam</strong>.</li>
                                <li>Masukkan nomor jam (misal: Jam ke-1), waktu mulai (07:30), dan waktu selesai (08:15).</li>
                                <li>Centang pilihan "Jam Istirahat" jika jam tersebut digunakan untuk istirahat, lalu simpan.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">E. Manajemen Akun Pengguna (CRUD &amp; Reset Password)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Manajemen User</strong> &rarr; <strong>Kelola Akun</strong>.</li>
                                <li>Untuk membuat akun: klik <strong>Tambah User</strong>, isi Nama Lengkap, Username, Email, Password default, dan pilih Peran (Role).</li>
                                <li>Untuk edit/hapus: klik tombol aksi di samping baris user terkait.</li>
                                <li>Untuk reset password: klik tombol <strong>Reset Password</strong>, sistem akan mengembalikan password ke default bawaan per peran atau mengirimkan link reset.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">F. Setup Kelas &amp; Rombel (Assign Siswa &amp; Wali Kelas)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Sekolah &amp; Kelas</strong> &rarr; <strong>Rombel / Kelas</strong>.</li>
                                <li>Klik <strong>Tambah Kelas</strong>. Tentukan nama kelas (misal: "Kelas X-TKRO 1"), tingkat kelas, jurusan, dan pilih <strong>Wali Kelas</strong> dari daftar guru.</li>
                                <li>Klik <strong>Simpan</strong>.</li>
                                <li>Buka detail kelas baru tersebut, klik <strong>Assign Siswa</strong>. Centang nama siswa aktif yang ingin dimasukkan ke kelas ini, lalu klik <strong>Tugaskan</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">G. Import Data Siswa &amp; Mapel via Excel/CSV</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Data Master</strong> &rarr; <strong>Siswa</strong> (atau <strong>Mata Pelajaran</strong>).</li>
                                <li>Klik tombol <strong>Impor CSV/Excel</strong>.</li>
                                <li>Klik tautan <strong>Download Template Format</strong> untuk mengunduh berkas contoh CSV yang benar.</li>
                                <li>Isi data Anda ke template (NISN, Nama, Email, Alamat, dll.) tanpa mengubah header kolom.</li>
                                <li>Unggah file CSV baru Anda dan klik <strong>Proses Impor</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">H. Penugasan Mengajar Guru</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Akademik</strong> &rarr; <strong>Penugasan Mengajar</strong>.</li>
                                <li>Klik <strong>Tambah Penugasan</strong>.</li>
                                <li>Pilih nama Guru, pilih mata pelajaran yang diampu, kelas/rombel target, dan jumlah jam pelajaran wajib per minggu.</li>
                                <li>Klik <strong>Simpan</strong>. Penugasan ini akan muncul di sidebar kanan pada halaman Schedule Grid.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">I. Bobot Nilai (Grade Weights)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Akademik</strong> &rarr; <strong>Bobot Nilai</strong>.</li>
                                <li>Pilih unit sekolah yang akan dikonfigurasi.</li>
                                <li>Tentukan persentase bobot untuk nilai Tugas, UTS, UAS, dan Sikap (misal: Tugas 20%, UTS 30%, UAS 40%, Sikap 10% - total harus 100%).</li>
                                <li>Klik <strong>Simpan Bobot</strong>. Aturan ini akan langsung digunakan saat Wali Kelas melakukan Generate Rapor.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">J. Menyusun Jadwal Pelajaran Visual (Schedule Grid)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke panel admin, buka menu <strong>Akademik</strong> &rarr; <strong>Jadwal Pelajaran (Grid)</strong>.</li>
                                <li>Pilih <strong>Unit Sekolah</strong>, <strong>Tahun Ajaran</strong>, <strong>Semester</strong>, dan <strong>Kelas</strong> yang ingin diatur.</li>
                                <li>Klik dan tarik (<em>drag-and-drop</em>) mata pelajaran dari panel samping kanan ke dalam slot hari dan jam pelajaran kosong pada Grid.</li>
                                <li><strong>Menangani Bentrok:</strong> Jika muncul blok merah bertuliskan &ldquo;Bentrok Guru&rdquo; atau &ldquo;Bentrok Ruangan&rdquo;, letakkan kembali elemen tersebut, lalu cari slot waktu lain.</li>
                                <li>Tekan tombol <strong>Simpan Jadwal</strong> di bagian bawah halaman.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">K. Monitoring Kehadiran Siswa</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Akademik</strong> &rarr; <strong>Monitoring Absensi</strong>.</li>
                                <li>Pilih Tanggal, Unit Sekolah, dan Kelas.</li>
                                <li>Sistem menampilkan tabel kehadiran hari ini (Hadir, Sakit, Izin, Alpha) beserta statistik persentase kehadiran rombel.</li>
                                <li>Klik <strong>Rekap Bulanan</strong> untuk mengekspor rekap absensi kelas ke format Excel/CSV.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">L. Memproses Migrasi Siswa Baru (PSB) &amp; RFID Card Setup</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>PSB</strong> &rarr; <strong>Pendaftar Baru</strong>.</li>
                                <li>Cari calon siswa, periksa pembayaran biaya formulir. Ubah status menjadi <span class="badge">document_verified</span>.</li>
                                <li>Masukkan nilai tes masuk di kolom <strong>Skor Tes</strong>, lalu klik <strong>Update Status</strong> ke <span class="badge">scored</span>. Klik <strong>Terima (Accept)</strong>.</li>
                                <li>Klik tombol <strong>Migrate</strong> pada pendaftar yang telah melunasi daftar ulang. Siswa resmi dipindahkan ke tabel siswa aktif.</li>
                                <li>Buka profil siswa aktif baru tersebut, dekatkan kartu RFID pada mesin pembaca kiosk, lalu input kode ID kartu di kolom <strong>RFID UID</strong> dan klik <strong>Simpan RFID</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">M. Employee Leave &amp; Attendance Management (Pegawai)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Kepegawaian</strong> &rarr; <strong>Absensi Pegawai</strong> untuk memantau rekap kehadiran staf &amp; guru harian.</li>
                                <li>Untuk cuti: Masuk ke menu <strong>Cuti &amp; Izin</strong>.</li>
                                <li>Tinjau pengajuan cuti pegawai yang masuk (meliputi tanggal cuti, jenis cuti, alasan, dan file surat rekomendasi dokter/instansi).</li>
                                <li>Klik <strong>Approve</strong> untuk memberikan izin cuti (hari kerja wajib guru dikurangi otomatis pada rekap payroll) atau <strong>Reject</strong> untuk menolak.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">N. Manajemen Konten Website (Berita, Galeri, Konten Umum)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Konten Website</strong> &rarr; <strong>Kelola Berita</strong>. Klik <strong>Tambah Berita</strong>, isi judul, isi artikel, upload thumbnail, lalu klik <strong>Publish</strong>.</li>
                                <li>Masuk ke <strong>Kelola Galeri</strong>. Unggah foto kegiatan sekolah terbaru beserta takarir (caption).</li>
                                <li>Masuk ke <strong>Konten Umum</strong>. Di sini Anda dapat mengedit informasi teks sambutan Kepala Sekolah, Visi &amp; Misi, dan Kontak Sekolah yang muncul pada landing page utama.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">O. Penempatan PKL, Loker, &amp; Tracer Study (Alumni)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li><strong>Penempatan PKL:</strong> Masuk ke **Kelola PKL &amp; Alumni** &rarr; **Penempatan PKL**, klik **Tambah Penempatan**. Pilih siswa SMK, input nama industri, nama mentor beserta email/WhatsApp, serta pilih pembimbing internal. Klik **Simpan**.</li>
                                <li><strong>Salin Tautan Mentor:</strong> Pada halaman detail penempatan PKL (Admin &amp; Guru), Anda dapat menyalin tautan khusus (*Signed URL*) untuk mentor industri secara langsung guna dibagikan dengan mudah via WhatsApp.</li>
                                <li><strong>Tracer Study:</strong> Pantau status penelusuran karir (Bekerja, Kuliah, Wirausaha) dan keterserapan alumni. Akses menu pengisian Tracer Study ini diproteksi secara ketat agar hanya dapat diakses oleh alumni yang berstatus lulus (siswa aktif akan diblokir/403).</li>
                                <li><strong>Lowongan Kerja:</strong> Buka menu **Lowongan Pekerjaan**, klik **Tambah Lowongan** untuk menerbitkan info loker DUDI mitra sekolah bagi alumni.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">P. Plotting &amp; Jadwal Tugas Akhir / Proyek</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li><strong>Format Panduan:</strong> Masuk ke **Format Tugas Akhir**, klik **Tambah Format** untuk upload dokumen template format panduan penulisan per unit sekolah.</li>
                                <li><strong>Plotting Pembimbing:</strong> Buka menu **Pengajuan Judul (Proposals)**, pilih judul siswa berstatus `Pending`, klik **Plot Pembimbing** dan pilih Guru Pembimbing aktif. Klik **Setujui**.</li>
                                <li><strong>Jadwal Ujian:</strong> Buka menu **Jadwal Ujian (Exams)** untuk mem-plot Guru Penguji, ruang, and tanggal sidang akhir bagi siswa berstatus `Ready for Exam`.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="border-left-color: #2563eb;">
                        <div class="card-title">Q. Admin CBT Monitoring &amp; Ujian Sekolah</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>CBT</strong> &rarr; <strong>Monitoring Ujian</strong>.</li>
                                <li>Di sini Admin dapat melihat seluruh ujian yang sedang berlangsung di sekolah: jumlah siswa aktif di ruang ujian, rata-rata waktu pengerjaan, dan status koneksi siswa.</li>
                                <li>Jika terjadi kecurangan massal atau keadaan darurat (mati listrik), Admin dapat menggunakan tombol <strong>Jeda Ujian Massal</strong> untuk mengunci layar CBT seluruh siswa secara instan di unit tersebut.</li>
                                <li>Setelah kondisi kondusif, klik <strong>Lanjutkan Ujian Massal</strong> untuk membuka kembali lembar ujian siswa dengan penambahan durasi waktu cadangan otomatis.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        
        <h2>6.2 Panduan untuk Bendahara (Treasurer)</h2>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">A. Menerbitkan Tagihan SPP Bulanan Secara Massal</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Keuangan</strong> &rarr; <strong>Manajemen Tagihan (Bills)</strong>.</li>
                                <li>Klik tombol <strong>Buat Tagihan Massal</strong> di pojok kanan atas.</li>
                                <li>Pilih <strong>Unit Sekolah</strong>, <strong>Tingkat Kelas</strong> (misal Kelas VII), <strong>Tahun Ajaran</strong>, <strong>Semester</strong>, dan pilih jenis biaya (misal <strong>SPP Bulanan</strong>).</li>
                                <li>Masukkan nominal tagihan (misal Rp 150.000) dan tanggal jatuh tempo (default tanggal 10).</li>
                                <li>Klik <strong>Terbitkan Tagihan</strong>. Sistem akan membuat baris tagihan baru untuk seluruh siswa aktif di kelas tersebut secara otomatis.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">B. Mencatat Penerimaan Pembayaran SPP &amp; Pemutihan Denda</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Keuangan</strong> &rarr; <strong>Terima Pembayaran</strong>.</li>
                                <li>Masukkan <strong>NISN</strong> atau <strong>Nama Siswa</strong> pada kolom pencarian, lalu tekan Enter.</li>
                                <li>Daftar tagihan aktif siswa akan muncul beserta denda keterlambatannya (jika melewati jatuh tempo).</li>
                                <li><strong>Pembayaran:</strong> Input jumlah uang yang dibayarkan &mdash; jika bayar penuh, status otomatis <span class="badge">Lunas</span>; jika mencicil, status <span class="badge">Cicilan</span>.</li>
                                <li><strong>Pemutihan Denda (Waive):</strong> Jika perlu keringanan, klik tombol <strong>Waive Denda</strong> sebelum menyimpan pembayaran.</li>
                                <li>Klik <strong>Simpan Transaksi</strong>. Klik <strong>Cetak Kuitansi</strong> (PDF) atau kirim otomatis ke WA orang tua.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="border-left-color: #2563eb;">
                        <div class="card-title">C. Laporan Rekap Keuangan &amp; Ekspor CSV</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Keuangan</strong> &rarr; <strong>Laporan Rekap</strong>.</li>
                                <li>Tentukan rentang tanggal, unit sekolah, dan jenis kas yang ingin dievaluasi (SPP / DPP / Uang Buku / Pendaftaran).</li>
                                <li>Sistem menampilkan tabel neraca pemasukan kas riil, akumulasi piutang tunggakan, dan grafik tren kas bulanan.</li>
                                <li>Klik tombol <strong>Ekspor ke CSV/Excel</strong> untuk mengunduh laporan keuangan guna keperluan audit Yayasan.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <h2>6.3 Panduan untuk Guru &amp; Wali Kelas / Kepala Sekolah</h2>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">A. Membuat Kelas Virtual &amp; Mengunggah Materi LMS</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>LMS</strong> &rarr; <strong>Courses</strong>. Klik <strong>Tambah Course</strong>, tentukan nama mapel dan kelas.</li>
                                <li>Masuk ke course baru tersebut, klik <strong>Tambah Materi</strong>. Masukkan judul materi, uraian deskripsi, dan upload file bahan ajar (PDF/Powerpoint/Video). Klik <strong>Publish</strong>.</li>
                                <li><strong>Kelas Tatap Muka Virtual:</strong> Klik tombol <strong>Start Jitsi Meeting</strong>. Link meeting live terbuat otomatis dan seluruh siswa di kelas tersebut menerima notifikasi WA untuk segera bergabung. Klik <strong>Stop Meeting</strong> untuk mengakhiri.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">B. Membuat Tugas (Assignment) &amp; Penilaian</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke <strong>Portal Guru</strong> &rarr; <strong>LMS</strong> &rarr; Pilih kelas, lalu klik <strong>Tambah Modul</strong> &rarr; <strong>Tugas (Assignment)</strong>.</li>
                                <li>Isi Judul Tugas, deskripsi instruksi, batas waktu (deadline), dan centang "Allow Resubmission" jika siswa diperkenankan mengunggah ulang tugas.</li>
                                <li>Siswa mengunggah tugas. Masuk ke tab <strong>Submissions</strong>, klik nama siswa, tinjau file jawaban mereka, masukkan nilai angka (0-100), ketik feedback evaluasi, lalu klik <strong>Submit Grade</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">C. CBT Exam: Membuat Bank Soal, Token, Jeda Ujian, &amp; Sync</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>CBT</strong> &rarr; <strong>Bank Soal</strong>, isi soal dan opsi. Anda dapat menggunakan format LaTeX <code>\( ... \)</code> atau <code>$$ ... $$</code> dengan bantuan petunjuk cheat-sheet tersemat untuk menyisipkan rumus matematika/sains yang indah.</li>
                                <li>Buka menu <strong>CBT</strong> &rarr; <strong>Jadwal Ujian (Exams)</strong>, klik <strong>Buat Ujian Baru</strong>. Hubungkan ke Bank Soal, tentukan durasi, token, dan klik <strong>Aktifkan</strong>.</li>
                                <li><strong>Kontrol Jeda:</strong> Pada detail ujian aktif, Anda dapat menekan tombol <strong>Jeda Ujian</strong> untuk menghentikan sementara waktu pengerjaan seluruh siswa, dan menekan <strong>Lanjutkan Ujian</strong> untuk mengaktifkan kembali dengan penyesuaian durasi otomatis.</li>
                                <li>Nilai PG diperiksa otomatis oleh sistem, sedangkan esai dapat Anda koreksi secara manual di tab <strong>Grade Essays</strong>.</li>
                                <li>Klik tombol <strong>Sync Grades</strong> untuk mengirimkan seluruh nilai CBT langsung ke rapor siswa.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">D. Asisten AI: AI Lesson Plan (RPP) &amp; AI CBT Question Generator</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li><strong>AI Lesson Plan (RPP) Generator:</strong> Masuk ke <strong>Asisten AI</strong> &rarr; <strong>RPP Generator</strong>. Isi Tingkat Kelas, Mata Pelajaran, Topik Bahasan (misal: "Sistem Transmisi Manual"), dan Alokasi Waktu. Klik <strong>Generate Lesson Plan</strong>. Sistem berbasis AI akan memformulasikan draf RPP lengkap. Klik <strong>Download RPP PDF</strong> untuk menyimpannya.</li>
                                <li><strong>AI CBT Question Generator:</strong> Masuk ke <strong>Asisten AI</strong> &rarr; <strong>Pembuat Soal CBT</strong>. Isi mata pelajaran, topik bahasan materi, jumlah soal, dan tingkat kesulitan (Mudah/Sedang/Sukar). Klik <strong>Generate Questions</strong>. AI akan merancang daftar soal pilihan ganda beserta kunci jawabannya. Klik <strong>Save to Question Bank</strong> untuk memindahkan soal secara instan ke Bank Soal CBT Anda.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">E. P5 Projek Pancasila: Setup Projek, Target, Penilaian, &amp; Rapor P5</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Raport Projek (P5)</strong> &rarr; <strong>Kelola Projek</strong>. Klik <strong>Buat Projek Baru</strong>, isi Judul Projek, Tema (misal: "Gaya Hidup Berkelanjutan"), dan deskripsi.</li>
                                <li>Buka detail projek baru tersebut, klik <strong>Tambah Target Dimensi</strong>. Pilih dimensi profil pelajar pancasila dan sub-elemen kompetensi target.</li>
                                <li>Klik <strong>Penilaian Projek (Assess)</strong>. Di samping nama siswa, pilih nilai kualitatif perkembangan sikap mereka (BB / MB / BSH / SB) pada sub-elemen target menggunakan tombol radio.</li>
                                <li>Ketik catatan proses perkembangan projek siswa di kolom <strong>Catatan Projek</strong>.</li>
                                <li>Klik <strong>Cetak Rapor P5 PDF</strong> untuk mencetak rapor projek kelas tersebut.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">F. Forum Diskusi LMS, Pengumuman, &amp; Pin/Lock Thread, Best Answer</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke <strong>Portal Guru</strong> &rarr; <strong>LMS</strong> &rarr; Pilih kelas &rarr; Klik tab <strong>Diskusi Forum</strong>. Klik <strong>Buat Topik Baru</strong>.</li>
                                <li>Pilih tipe topik menggunakan tombol opsi berbentuk pil: <strong>Diskusi</strong> (Biru), <strong>Pertanyaan</strong> (Oranye), atau <strong>Pengumuman</strong> (Merah).</li>
                                <li>Isi judul dan konten. Centang <strong>Pin Thread</strong> jika ingin menyematkannya di bagian teratas forum, lalu simpan.</li>
                                <li>Guru dapat memberikan lencana <strong>"Jawaban Terbaik" (Best Answer)</strong> pada kiriman siswa yang paling tepat (terdapat sorotan bingkai emas bersinar), serta melakukan moderasi: Pin/Unpin, Lock/Unlock diskusi, atau hapus kiriman.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">G. Input Nilai Sikap &amp; Absensi Harian Bulk</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li><strong>Input Absensi Harian:</strong> Masuk ke <strong>Absensi Siswa</strong> &rarr; <strong>Input Absensi</strong>. Pilih Kelas dan Tanggal. Sistem menampilkan daftar nama siswa. Klik tombol <strong>Set All Hadir</strong> untuk menandai semua siswa hadir secara instan. Ubah status per anak (Sakit/Izin/Alpha) jika ada yang tidak masuk, lalu klik <strong>Simpan Absensi</strong>.</li>
                                <li><strong>Input Nilai Sikap:</strong> Masuk ke <strong>Input Nilai</strong> &rarr; Pilih Kelas dan Mapel, pilih kategori <strong>Nilai Sikap / Karakter</strong>. Masukkan skor angka sikap (A / B / C / D) beserta catatan perkembangan karakter siswa secara massal, lalu klik <strong>Simpan</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">H. Monitoring Jurnal PKL Siswa</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke **Monitoring PKL** &rarr; **Jurnal Magang**.</li>
                                <li>Pilih siswa bimbingan Anda untuk memantau logbook harian mereka.</li>
                                <li>Tinjau deskripsi kegiatan, foto lampiran, lokasi koordinat GPS, dan status persetujuan dari mentor industri.</li>
                                <li>Berikan catatan pembinaan guru pada kolom umpan balik (feedback) jika diperlukan untuk pemantauan berkala.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">I. Pembimbingan &amp; Penilaian Tugas Akhir/Proyek Kelas XII</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li><strong>Review Logbook:</strong> Masuk ke **Bimbingan Tugas Akhir**, klik **Detail** pada entri logbook bimbingan mingguan siswa, tulis ulasan revisi dan klik **Approve Logbook** (Siswa +10 poin, Guru +15 poin).</li>
                                <li><strong>Kesiapan Sidang:</strong> Klik tombol **Nyatakan Siap Sidang (Mark Ready for Exam)** jika bimbingan dinilai tuntas dan draf laporan sudah lengkap (Siswa +50 poin).</li>
                                <li><strong>Menilai Sidang (Sebagai Penguji):</strong> Masuk ke **Ujian Tugas Akhir**, pilih siswa yang Anda uji, masukkan nilai numerik (skala 0 - 100) dan catatan sidang. Klik **Simpan Nilai Ujian** (Siswa Lulus +100 poin, Penguji +30 poin).</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">J. Rapor Digital: Generate Rapor, Deskripsi Catatan, Rilis, Bulk Download</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke <strong>Portal Wali Kelas</strong> &rarr; <strong>Raport Digital</strong> &rarr; <strong>Generate</strong>.</li>
                                <li>Klik tombol <strong>Kalkulasi Nilai Rapor</strong> untuk menghitung rerata nilai tugas, UTS, UAS, sikap, dan total kehadiran siswa secara otomatis.</li>
                                <li>Masuk ke <strong>Form Deskripsi</strong>, ketik catatan perkembangan kepribadian/sikap untuk masing-masing siswa.</li>
                                <li>Klik tombol <strong>Lock &amp; Finalize</strong> untuk mengunci seluruh nilai agar tidak dapat diedit lagi oleh guru mata pelajaran.</li>
                                <li>Klik <strong>Publish Rapor</strong>. Tombol unduhan rapor otomatis tampil di akun portal siswa dan orang tua mereka.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <h2>6.4 Panduan untuk Siswa &amp; Orang Tua</h2>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">A. E-Learning LMS: Baca Materi, Kerjakan Kuis, Kumpul Tugas</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li><strong>Membaca Materi &amp; Progress:</strong> Buka <strong>LMS</strong> &rarr; Pilih course mapel. Klik materi ajar, baca isinya, lalu klik tombol <strong>Tandai Selesai</strong> di bagian bawah untuk memperbarui kemajuan membaca Anda (+10 poin).</li>
                                <li><strong>Mengerjakan Kuis LMS:</strong> Klik kuis aktif &rarr; klik <strong>Mulai Quiz</strong>. Halaman kuis interaktif akan memuat <em>Floating Timer Bar</em> (hijau/kuning/merah), panel <em>Question Navigator</em> di samping (hijau=dijawab, kuning=ragu, abu=belum), dan opsi kartu interaktif. Klik <strong>Kirim Jawaban</strong> untuk melihat nilai instan beserta visualisasi circular progress ring.</li>
                                <li><strong>Mengumpulkan Tugas:</strong> Pilih Tugas &rarr; klik <strong>Unggah Jawaban</strong>. Seret file jawaban (maksimal 5MB) ke area upload, lalu klik <strong>Kirim Tugas</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">B. Ujian Online CBT: Token, LaTeX, Offline Buffer Sync, Anti-Cheat</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke <strong>Ujian Online (CBT)</strong>, pilih ujian dan masukkan token akses dari pengawas.</li>
                                <li><strong>Pembacaan Rumus:</strong> Rumus-rumus matematika/sains dalam bentuk LaTeX otomatis ter-render rapi dan jelas berkat engine KaTeX bawaan.</li>
                                <li><strong>Widget &amp; Ketahanan Offline:</strong> Pantau widget indikator koneksi. Jika internet terputus, sistem akan menyimpan jawaban Anda secara aman ke buffer lokal browser. Begitu internet terhubung kembali, jawaban di-sync otomatis ke server.</li>
                                <li><strong>Peringatan Anti-Cheat:</strong> Jangan meninggalkan tab ujian. Jika pindah tab/blur layar, sistem mencatat peringatan pelanggaran. Klik <strong>Selesai &amp; Kumpulkan</strong> untuk submit ujian secara sah.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">C. Bimbingan Konseling (BK): Lihat Catatan &amp; Rekomendasi</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke <strong>Portal Siswa</strong> &rarr; Buka menu <strong>Konseling &amp; Perkembangan</strong>.</li>
                                <li>Tinjau catatan pembinaan sikap dari guru BK/Wali Kelas.</li>
                                <li>Baca rekomendasi tindak lanjut atau saran bimbingan yang harus dilaksanakan demi meningkatkan poin karakter Anda.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">D. Mengisi Jurnal Harian PKL (Siswa SMK)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu **Magang/PKL** &rarr; **Jurnal Magang**, klik **Tambah Jurnal**.</li>
                                <li>Isi tanggal, uraian pekerjaan, upload foto bukti fisik, lalu klik **Kirim**.</li>
                                <li>Jurnal terkirim ke Mentor Industri untuk disetujui.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">E. Pengajuan Judul, Logbook Proyek/Tugas Akhir Kelompok</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu **Proyek &amp; Tugas Akhir**, klik **Ajukan Judul** untuk mengusulkan judul TA Anda.</li>
                                <li>Setelah disetujui, baik ketua maupun seluruh anggota kelompok dapat menginput entri bimbingan di tab **Logbook** secara berkala (menulis progres, mengunggah draf PDF, dan mengajukan konsultasi). Setiap pengisian logbook progress yang valid akan memberikan poin reputasi (+10 poin) ke seluruh anggota kelompok secara otomatis.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">F. Portal Alumni &amp; Tracer Study (BMW)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Bagi alumni yang sudah lulus: login ke portal PembdaHUB (akses dibatasi hanya untuk alumni, siswa aktif akan menerima 403).</li>
                                <li>Buka menu <strong>Alumni</strong> &rarr; <strong>Tracer Study</strong>.</li>
                                <li>Isi kuesioner pelacakan karir (Bekerja / Melanjutkan Kuliah / Wirausaha / Mencari Kerja) beserta detail instansi kerja, rentang gaji, dan relevansi kurikulum sekolah. Klik <strong>Simpan</strong>.</li>
                                <li>Buka menu <strong>Lowongan Kerja</strong> untuk melamar info lowongan mitra DUDI sekolah.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="border-left-color: #2563eb;">
                        <div class="card-title">G. Monitoring Orang Tua: Absensi, Tagihan, Denda, Download Rapor PDF</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke <strong>Portal Orang Tua</strong>, pilih anak yang ingin dipantau dari menu drop-down di pojok kanan atas.</li>
                                <li>Buka menu <strong>Presensi &amp; Jadwal</strong> untuk memantau status kehadiran harian anak secara real-time.</li>
                                <li>Buka menu <strong>Keuangan &amp; SPP</strong> untuk melihat rincian pembayaran SPP bulanan yang sudah lunas, cicilan, tunggakan berjalan, serta denda keterlambatan.</li>
                                <li>Di akhir semester, klik menu <strong>Rapor Digital</strong>, lalu tekan tombol <strong>Download Rapor (PDF)</strong> untuk mengunduh laporan hasil belajar anak Anda secara mandiri.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <h2>6.5 Panduan untuk Yayasan</h2>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">A. Executive Dashboard &amp; Keuangan Gabungan</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke <strong>Portal Yayasan</strong> &rarr; Buka halaman <strong>Dashboard Eksekutif</strong>.</li>
                                <li>Tinjau grafik penerimaan kas bulanan gabungan serta perbandingan pendapatan kas real-time antar 3 unit sekolah.</li>
                                <li>Tinjau rasio pelunasan SPP per rombel/kelas di seluruh unit sekolah.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">B. Mengirim Undangan Pelatihan (Single &amp; Bulk)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Undangan Pelatihan</strong> pada sidebar Yayasan.</li>
                                <li>Untuk mengirim undangan perorangan: Klik <strong>Kirim Undangan</strong>, isi nama guru, pilih modul pelatihan, dan klik <strong>Kirim</strong>.</li>
                                <li>Untuk mengirim undangan massal: Klik <strong>Kirim Undangan Massal</strong>, pilih unit sekolah (SMP/SMA/SMK), pilih modul pelatihan, lalu klik <strong>Kirim Massal</strong>. Seluruh guru di unit tersebut akan menerima notifikasi undangan di portal mereka.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="border-left-color: #2563eb;">
                        <div class="card-title">C. Monitoring SDM, Payroll, &amp; Beban Kerja Guru</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Kepegawaian (SDM)</strong> &rarr; <strong>Beban Kerja Guru</strong> untuk melihat sebaran beban mengajar riil guru di seluruh unit sekolah yayasan.</li>
                                <li>Buka menu <strong>Laporan Penggajian (Payroll)</strong>.</li>
                                <li>Tinjau total pengeluaran kas bulanan Yayasan untuk membayarkan gaji pokok dan tunjangan bagi guru berstatus PNS, Honorer, maupun Pegawai Tetap Yayasan.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <h2>6.6 Fitur Lintas-Peran (Pembda Elite)</h2>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">A. Hub Forum &amp; Kolaborasi (Thread, Like, Join Project, Kepanitiaan, Donasi)</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Forum &amp; Kolaborasi</strong> di sidebar.</li>
                                <li><strong>Membuat Thread:</strong> Klik <strong>Buat Utas Baru</strong>, isi Judul, kategori (Diskusi/Proyek/Kepanitiaan/Charity), dan deskripsi. Klik <strong>Kirim</strong>.</li>
                                <li><strong>Gabung Kepanitiaan/Proyek:</strong> Pada thread bertipe Proyek/Kepanitiaan, klik tombol <strong>Join Project</strong>. Pembuat thread dapat menyetujui (Approve) atau menolak (Reject) permintaan gabung Anda di tab Members.</li>
                                <li><strong>Donasi Sosial (Charity):</strong> Pada thread bertipe Donasi/Charity, klik tombol <strong>Donate</strong>, isi nominal uang, dan lakukan pembayaran online untuk berpartisipasi dalam bakti sosial.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">B. Hall of Fame &amp; Leaderboard Reputasi</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Buka menu <strong>Hall of Fame</strong> di sidebar.</li>
                                <li>Di sini ditampilkan papan peringkat (Leaderboard) interaktif perolehan poin reputasi keaktifan siswa dan guru di sekolah.</li>
                                <li>Peringkat teratas akan dipajang di halaman depan (Hall of Fame) sebagai bentuk penghargaan prestasi karakter.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="border-left-color: #2563eb;">
                        <div class="card-title">C. Pelatihan PembdaHUB: Modul Belajar &amp; Progres Belajar</div>
                        <div class="card-body">
                            <ol class="step-list">
                                <li>Masuk ke menu <strong>Pelatihan PembdaHUB</strong>.</li>
                                <li>Pilih modul pelatihan yang ingin dipelajari (misal: "Panduan Kurikulum Merdeka").</li>
                                <li>Baca materi pelajaran online, atau klik <strong>Download PDF Materi</strong> untuk belajar secara offline.</li>
                                <li>Setiap penyelesaian modul akan terekam di sistem sebagai bagian dari progres peningkatan kompetensi SDM guru dan pegawai yayasan.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Bab 7: Penyelesaian Masalah (Troubleshooting) -->
    <div>
        <h1>7. Penyelesaian Masalah (Troubleshooting)</h1>
        <p>
            Untuk kelancaran operasional harian, berikut adalah panduan cepat penyelesaian masalah teknis yang sering ditemui oleh pengguna:
        </p>

        <table class="grid-table">
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">7.1 WA Notifikasi Tidak Terkirim</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> API Token Fonnte kedaluwarsa, paket habis, atau nomor pengirim di HP terputus.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Masuk ke Admin &rarr; PSB &rarr; Notifications &rarr; Test Connection.</li>
                                <li>Periksa variabel <span class="badge">WHATSAPP_API_TOKEN</span> di file <span class="badge">.env</span>.</li>
                                <li>Pastikan status device Fonnte berstatus <em>Connected</em>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">7.2 Bentrok Jadwal Pelajaran (Grid)</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Jadwal mengajar guru atau ruang kelas bertumpukan di jam yang sama.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Perhatikan tanda warna merah pada slot grid beserta alasannya.</li>
                                <li>Tekan tombol <strong>Clear Cache</strong> di kanan atas halaman *Schedule Grid* jika data tidak ter-refresh.</li>
                                <li>Pindahkan slot pelajaran ke jam atau ruangan lain yang kosong.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">7.3 Nilai CBT Tidak Muncul di Rapor</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Ujian CBT selesai tetapi guru belum melakukan sinkronisasi nilai ke rapor utama.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Masuk ke Portal Guru &rarr; CBT &rarr; Exams &rarr; Pilih Ujian.</li>
                                <li>Klik tombol <strong>Sync Grades</strong> untuk mengirimkan data ke rapor.</li>
                                <li>Pastikan ID mapel dan semester di CBT cocok dengan konfigurasi rapor kelas.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">7.4 Ortu Tidak Bisa Unduh Rapor</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Status Rapor kelas siswa masih berupa <code>draft</code> atau <code>finalized</code>, belum di-publish.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Wali kelas masuk ke Portal Wali Kelas &rarr; Raport &rarr; Pilih Siswa.</li>
                                <li>Klik tombol <strong>Publish</strong>.</li>
                                <li>Status berubah menjadi <code>published</code>, tombol unduh otomatis muncul di portal siswa/ortu.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">7.5 Perhitungan Denda SPP Tidak Sesuai</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Pengaturan tanggal jatuh tempo atau denda belum diperbarui.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Bendahara masuk ke Pengaturan Keuangan &rarr; Settings &rarr; Late Fees.</li>
                                <li>Perbarui *grace period* (toleransi) dan nominal denda bulanan, lalu simpan.</li>
                                <li>Sistem merevisi denda tagihan berjalan seluruh siswa otomatis.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">7.6 Lupa Kata Sandi / Tidak Bisa Login</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Salah memasukkan kata sandi, akun dinonaktifkan, atau lupa kredensial.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Klik <strong>Lupa Password</strong> di bawah form login untuk mengirim link reset password ke email.</li>
                                <li>Hubungi Wali Kelas atau Admin Sekolah untuk mereset password Anda secara manual menjadi password default bawaan sekolah.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">7.7 Import Data CSV Gagal / Error Format</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Format file salah, kolom terbalik, atau ada karakter aneh di CSV.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Unduh ulang <strong>Template CSV Resmi</strong> dari halaman impor.</li>
                                <li>Buka di Excel dan simpan kembali dengan pilihan format <strong>CSV (Comma Delimited)</strong>.</li>
                                <li>Pastikan NISN siswa berupa angka murni dan tidak duplikat dengan siswa lain.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">7.8 Siswa Tidak Muncul di Rombel Kelas</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Siswa baru dimigrasi dari PSB tetapi belum di-assign ke kelas rombel manapun.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Admin masuk ke menu <strong>Rombel / Kelas</strong> &rarr; Pilih Kelas &rarr; klik <strong>Assign Siswa</strong>.</li>
                                <li>Cari nama siswa terkait pada daftar siswa yang belum memiliki kelas, lalu klik <strong>Tugaskan</strong>.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="card">
                        <div class="card-title">7.9 Tombol Switch Peran Tidak Muncul</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Akun Anda belum didaftarkan memiliki peran ganda oleh Super Admin.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Hubungi Super Admin sekolah Anda.</li>
                                <li>Minta untuk menautkan profil Guru Anda dengan peran struktural tambahan (misal: Wali Kelas atau Kepala Sekolah) di database.</li>
                                <li>Setelah ditautkan, tombol beralih peran akan muncul otomatis.</li>
                            </ol>
                        </div>
                    </div>
                </td>
                <td>
                    <div class="card">
                        <div class="card-title">7.10 RFID Card Tidak Terbaca di Kiosk</div>
                        <div class="card-body">
                            <strong>Penyebab:</strong> Kode UID kartu RFID belum didaftarkan ke profil siswa, atau koneksi mesin kiosk terputus.
                            <br><strong>Solusi:</strong>
                            <ol>
                                <li>Admin harus memeriksa apakah nomor kartu sudah diinput pada field <strong>RFID UID</strong> di edit profil siswa.</li>
                                <li>Periksa kabel koneksi USB mesin pembaca kartu RFID pada komputer kiosk.</li>
                                <li>Restart aplikasi kiosk pembaca kartu absensi.</li>
                            </ol>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div class="card" style="background-color: #fffbeb; border-color: #f59e0b;">
                        <div class="card-title" style="color: #b45309; border-bottom-color: #fde68a;">Bantuan &amp; Layanan IT Support</div>
                        <div class="card-body" style="color: #78350f;">
                            Jika Anda mengalami kendala sistem lainnya yang tidak tercakup dalam panduan ini, silakan hubungi tim dukungan teknis Yayasan:
                            <ul style="padding-left:15px; margin-top: 5px;">
                                <li><strong>Email Support:</strong> it.support@perguruanpembda.com</li>
                                <li><strong>Helpdesk WA:</strong> +62 823-7777-xxxx</li>
                                <li><strong>Jam Operasional:</strong> Senin - Sabtu (08:00 - 16:00 WIB)</li>
                            </ul>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Bab 8: Data Simulasi & Pengujian Platform (Seeder) -->
    <div class="page-break"></div>
    <div>
        <h1>8. Data Simulasi &amp; Pengujian Platform (Seeder)</h1>
        <p>
            Untuk mempermudah proses evaluasi dan presentasi sistem tanpa harus menginput data dari nol, PembdaHUB dilengkapi dengan generator data simulasi otomatis (<strong>Comprehensive Simulation Seeder</strong>).
        </p>

        <h2>8.1 Tujuan Data Simulasi</h2>
        <p>
            Seeder ini dirancang untuk mensimulasikan seluruh alur operasional sekolah secara instan. Data yang dihasilkan saling terhubung satu sama lain untuk menguji skenario:
        </p>
        <ul>
            <li><strong>Absensi:</strong> Kehadiran harian 14 hari ke belakang dengan status dan jam presensi bervariasi.</li>
            <li><strong>BK &amp; Konseling:</strong> Kasus pelanggaran siswa, sesi konseling, hingga piagam penghargaan prestasi siswa beserta rekomendasi tindak lanjutnya.</li>
            <li><strong>Reputasi:</strong> Riwayat penambahan poin prestasi dan denda pelanggaran serta lencana penghargaan (*badging system*).</li>
            <li><strong>LMS E-Learning:</strong> Pengumuman kelas virtual, materi pelajaran aktif, tugas terkumpul yang sudah dinilai guru, serta kuis yang telah dikerjakan siswa.</li>
            <li><strong>CBT Online Exam:</strong> Bank soal pilihan ganda, sesi ujian anti-curang, jawaban siswa, dan rekapitulasi nilai ujian CBT.</li>
            <li><strong>Rapor Digital:</strong> Perhitungan otomatis nilai akhir semester berbasis bobot sekolah, peringkat siswa di kelasnya, serta status rilis rapor digital.</li>
        </ul>

        <h2>8.2 Cara Menjalankan Simulasi</h2>
        <p>
            Data simulasi dapat dijalankan melalui dua metode:
        </p>
        <h3>Metode A: Melalui Web Browser (Sangat Direkomendasikan)</h3>
        <p>
            Metode ini adalah cara termudah dan tercepat, terutama di server hosting yang tidak memiliki akses SSH/Terminal.
        </p>
        <ol>
            <li>Buka browser dan akses alamat berikut:
                <ul>
                    <li>Localhost: <code>http://localhost/seed-simulasi</code> (sesuaikan port jika XAMPP menggunakan port selain 80)</li>
                    <li>Production/Hosting: <code>http://perguruanpembda.com/seed-simulasi</code></li>
                </ul>
            </li>
            <li>Halaman browser akan menampilkan log eksekusi seeder secara real-time. Proses pemutakhiran puluhan ribu baris data simulasi ini memakan waktu kurang dari 15 detik.</li>
        </ol>

        <h3>Metode B: Melalui Terminal (CLI)</h3>
        <p> Jikalau Anda memiliki akses terminal pada local server XAMPP Anda, jalankan perintah berikut: </p>
        <pre style="background: #f1f5f9; padding: 10px; border-radius: 4px; font-family: monospace;">php artisan db:seed --class=ComprehensiveSimulationSeeder</pre>

        <h2>8.3 Daftar Akun Demo Pengujian</h2>
        <table style="font-size: 8.5pt;">
            <thead>
                <tr>
                    <th style="width: 20%;">Peran Portal</th>
                    <th style="width: 35%;">Username / Email</th>
                    <th style="width: 15%;">Password</th>
                    <th>Keterangan Halaman Uji</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">Super Admin</td>
                    <td><code>superadmin@PembdaHUB.com</code></td>
                    <td><code>Superadmin@2026!</code></td>
                    <td>Dashboard yayasan, pengaturan user global, &amp; tahun ajaran.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Admin SMP</td>
                    <td><code>admin@smp2pembda.sch.id</code></td>
                    <td><code>AdminSMP@2026!</code></td>
                    <td>Portal Admin SMPS Pembda 2 Gunungsitoli.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Admin SMA</td>
                    <td><code>admin@sma1pembda.sch.id</code></td>
                    <td><code>AdminSMA@2026!</code></td>
                    <td>Portal Admin SMA Swasta Pembda 1 Gunungsitoli.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Admin SMK</td>
                    <td><code>admin@smkpembda.sch.id</code></td>
                    <td><code>AdminSMK@2026!</code></td>
                    <td>Portal Admin SMKS Swasta Pembda Nias.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Guru (SMA)</td>
                    <td><code>ama.zega@sma1pembda.sch.id</code></td>
                    <td><code>Guru@2026!</code></td>
                    <td>Membuat kuis, kelas virtual Jitsi, koreksi tugas, &amp; rekap absen.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Siswa (SMP)</td>
                    <td><code>ferdinan@student.smp2pembda.sch.id</code></td>
                    <td><code>Siswa@2026!</code></td>
                    <td>Mengunduh Rapor PDF, cek leaderboard Hall of Fame, &amp; materi LMS.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Orang Tua</td>
                    <td><code>ama.ferdinan@parent.sch.id</code></td>
                    <td><code>OrangTua@2026!</code></td>
                    <td>Memantau absensi anak, status tagihan keuangan, denda, &amp; nilai Rapor.</td>
                </tr>
            </tbody>
        </table>

        <h2>8.4 Cara Mengosongkan Data Simulasi (Go-Live)</h2>
        <p>
            Jika sistem PembdaHUB sudah siap digunakan secara resmi dengan data ril sekolah, Anda dapat menghapus semua data simulasi ini secara instan dan mengembalikan database ke kondisi bersih dengan menjalankan perintah di terminal:
        </p>
        <pre style="background: #f1f5f9; padding: 10px; border-radius: 4px; font-family: monospace;">php artisan migrate:fresh --seed</pre>
        <p>
            Perintah ini akan membersihkan seluruh tabel data transaksi simulasi dan menyisakan data master bawaan (seperti data sekolah, akun SuperAdmin, tahun ajaran aktif, dan data kurikulum utama).
        </p>
    </div>

    <!-- Bab 9: Sistem Reputasi & Poin Keaktifan Terintegrasi -->
    <div class="page-break"></div>
    <div>
        <h1>9. Sistem Reputasi &amp; Poin Keaktifan Terintegrasi</h1>
        <p>
            PembdaHUB menerapkan <strong>Sistem Reputasi</strong> berbasis gamifikasi untuk meningkatkan kepatuhan dan keaktifan akademik bagi Siswa dan Guru di bawah naungan Yayasan Perguruan Pembangunan Daerah Nias (PEMBDA). Setiap aktivitas produktif di dalam sistem akan memberikan penghargaan berupa <strong>Poin Reputasi</strong>, sedangkan pelanggaran presensi atau pembatalan data akan memicu denda pengurangan poin.
        </p>

        <h2>9.1 Sumber Perolehan Poin Siswa &amp; Guru</h2>
        <p>
            Poin reputasi siswa dan guru teralokasi secara otomatis di database berdasarkan pemicu (trigger) aktivitas:
        </p>
        
        <table style="font-size: 8pt; margin-top: 10px; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 15%; background-color: #eff6ff; color: #1e40af; font-weight: bold; border: 1px solid #e2e8f0; padding: 5px;">Peran</th>
                    <th style="width: 55%; background-color: #eff6ff; color: #1e40af; font-weight: bold; border: 1px solid #e2e8f0; padding: 5px;">Aktivitas Kegiatan / Pemicu Sistem</th>
                    <th style="width: 15%; background-color: #eff6ff; color: #1e40af; font-weight: bold; border: 1px solid #e2e8f0; padding: 5px;">Poin</th>
                    <th style="width: 15%; background-color: #eff6ff; color: #1e40af; font-weight: bold; border: 1px solid #e2e8f0; padding: 5px;">Kategori</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td rowspan="14" style="font-weight: bold; color: #1e3b8b; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 5px; text-align: center; vertical-align: middle;">Siswa</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Absensi kelas dicatat dengan status <strong>Hadir (Present)</strong></td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+10 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">attendance</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Membayar tagihan sekolah (SPP/dll) tepat waktu (s/d tanggal 10)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+40 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">payment</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Mengikuti sesi bimbingan konseling pembinaan oleh Guru BK</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+10 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">counseling</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Mengikuti kunjungan rumah (Home Visit) pembinaan sekolah</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+20 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">home_visit</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Menyelesaikan membaca/mempelajari materi di modul LMS (100% Progres)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+10 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">lms_material</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Menyelesaikan ujian CBT dengan hasil lulus (skor &ge; KKM)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+50 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">cbt_exam</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Meraih nilai sempurna/istimewa pada ujian CBT (skor &ge; 90)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+50 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">cbt_exam_bonus</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Mengisi logbook bimbingan Tugas Akhir kelompok secara berkala</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+10 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">final_project</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Tugas Akhir dinyatakan layak ujian/sidang oleh Pembimbing</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+50 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">final_project</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Lulus ujian sidang Tugas Akhir kelompok yang dinilai oleh Penguji</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+100 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">final_project</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Logbook harian PKL disetujui oleh Mentor Industri DUDI</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+10 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">pkl</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Menyelesaikan program PKL (nilai akhir telah terbit)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+100 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">pkl_completed</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Mengisi dan mengirimkan survei Tracer Study Lulusan (Alumni)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+50 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">alumni_tracer</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Aktivitas Hub Forum (Thread: +15, Reply: +5, Upvote: +2/+10, Best Answer: +15)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">Variatif</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">forum</td>
                </tr>
                <tr>
                    <td rowspan="8" style="font-weight: bold; color: #0d9488; background-color: #f8fafc; border: 1px solid #e2e8f0; padding: 5px; text-align: center; vertical-align: middle;">Guru</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Melakukan pengisian absensi harian kelas yang diajarnya</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+20 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">attendance</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Menginput dokumentasi catatan konseling pembinaan siswa</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+10 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">counseling_action</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Melaksanakan kunjungan rumah (Home Visit) siswa bimbingan</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+50 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">home_visit</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Membuat dan mempublikasikan materi pembelajaran baru di LMS</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+30 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">lms</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Melakukan input penilaian projek P5 siswa kelas bimbingan</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+30 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">p5</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Mereview logbook bimbingan Tugas Akhir siswa (memberi feedback)</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+15 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">mentoring</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Menguji dan menginput nilai ujian sidang Tugas Akhir siswa</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+30 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">examination</td>
                </tr>
                <tr>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">Siswa bimbingan PKL yang ditugaskan telah selesai dinilai oleh Industri</td>
                    <td style="font-weight: bold; color: #10b981; border: 1px solid #e2e8f0; padding: 5px;">+50 Poin</td>
                    <td style="border: 1px solid #e2e8f0; padding: 5px;">pkl_monitoring</td>
                </tr>
            </tbody>
        </table>

        <h2>9.2 Pengurangan Poin &amp; Mekanisme Rollback Otomatis</h2>
        <div class="alert-box warning" style="margin-bottom: 10px;">
            <div class="alert-title">Mekanisme Pengurangan Poin (Deductions)</div>
            <p style="margin: 0; padding: 0;">Sistem menerapkan denda pengurangan poin otomatis untuk ketidakpatuhan atau pelanggaran perilaku:</p>
            <ul style="margin: 5px 0 0 0; padding-left: 20px;">
                <li><strong>Siswa Alfa:</strong> Dikurangi <strong>-10 Poin</strong> otomatis saat absensi di-set Alpha oleh guru atau admin.</li>
                <li><strong>Pelanggaran Sikap (BK):</strong> Dikurangi berdasarkan tingkat keseriusan pelanggaran: Ringan (<strong>-20 Poin</strong>), Sedang (<strong>-50 Poin</strong>), Berat (<strong>-100 Poin</strong>).</li>
            </ul>
        </div>

        <p>
            Untuk menjaga integritas data dan mencegah manipulasi perolehan poin, PembdaHUB dilengkapi dengan fitur <strong>Rollback Poin Otomatis</strong> pada level database:
        </p>
        <ul>
            <li><strong>Pengubahan Presensi:</strong> Jika status kehadiran siswa diubah dari <em>Hadir</em> menjadi <em>Alpha</em>, sistem akan membatalkan perolehan +10 poin sebelumnya dan menerapkan denda pengurangan -10 poin secara real-time.</li>
            <li><strong>Penghapusan Absensi Manual:</strong> Jika Admin/Guru menghapus record absensi, sistem akan membatalkan poin terkait (mengembalikan poin jika denda, atau menarik kembali jika bonus).</li>
            <li><strong>Penghapusan Catatan Konseling:</strong> Menghapus catatan penghargaan/pelanggaran akan membatalkan poin/denda siswa, serta poin input bagi Guru BK (+10).</li>
            <li><strong>Penolakan Logbook PKL:</strong> Jika Mentor Industri menolak logbook harian siswa yang sempat disetujui, perolehan <strong>+10 Poin</strong> PKL siswa akan otomatis ditarik kembali.</li>
            <li><strong>Penghapusan Diskusi Forum:</strong> Menghapus thread atau komentar di forum akan otomatis membatalkan poin yang didapatkan dari forum tersebut beserta upvote terkait.</li>
        </ul>
        <p>
            Mekanisme ini memastikan papan peringkat prestasi (<em>Leaderboard / Hall of Fame</em>) sekolah berjalan secara objektif, bersih dari manipulasi, dan terpercaya.
        </p>
    </div>

    <!-- Bab 10: Glosarium & Daftar Istilah -->
    <div class="page-break"></div>
    <div>
        <h1>10. Glosarium &amp; Daftar Istilah</h1>
        <p>
            Berikut adalah penjelasan singkat untuk istilah, singkatan, dan akronim teknis maupun akademis yang digunakan di dalam aplikasi PembdaHUB:
        </p>
        <table style="font-size: 9pt; margin-top: 10px;">
            <thead>
                <tr>
                    <th style="width: 25%;">Istilah / Akronim</th>
                    <th>Penjelasan Singkat &amp; Konteks Penggunaan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-weight: bold;">NISN</td>
                    <td><strong>Nomor Induk Siswa Nasional:</strong> Kode pengenal identitas siswa yang bersifat unik, standar, dan berlaku sepanjang masa untuk seluruh siswa se-Indonesia. Digunakan sebagai username login default portal Siswa.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">NPSN</td>
                    <td><strong>Nomor Pokok Sekolah Nasional:</strong> Kode pengenal unik untuk satuan pendidikan (sekolah) di seluruh Indonesia. Digunakan pada pengaturan profil unit sekolah.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">KKM</td>
                    <td><strong>Kriteria Ketuntasan Minimal:</strong> Nilai batas terendah pencapaian kompetensi siswa untuk dinyatakan lulus/tuntas pada suatu mata pelajaran.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">RFID</td>
                    <td><strong>Radio Frequency Identification:</strong> Teknologi nirkabel pengenal berbasis gelombang radio. Di PembdaHUB, kartu RFID digunakan oleh siswa/pegawai untuk melakukan absen tempel secara instan pada mesin kiosk sekolah.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">CBT</td>
                    <td><strong>Computer Based Test:</strong> Modul ujian sekolah yang dikerjakan secara online menggunakan komputer atau handphone melalui aplikasi peramban (browser).</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">LMS</td>
                    <td><strong>Learning Management System:</strong> Modul media e-learning pembelajaran virtual terpadu (modul, tugas, kuis, forum diskusi) untuk mempermudah KBM tanpa kertas.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">P5</td>
                    <td><strong>Projek Penguatan Profil Pelajar Pancasila:</strong> Modul pembelajaran kokurikuler dalam Kurikulum Merdeka yang dirancang untuk menguatkan upaya pencapaian kompetensi dan karakter pancasila.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">BK</td>
                    <td><strong>Bimbingan Konseling:</strong> Pelayanan bantuan konseling untuk memantau, mendampingi, dan membina perkembangan sikap, mental, serta karakter perilaku siswa di sekolah.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">SPP &amp; DPP</td>
                    <td><strong>Sumbangan Pembinaan Pendidikan &amp; Dana Pembangunan Pendidikan:</strong> Jenis tagihan iuran pembiayaan wajib bulanan/tahunan siswa.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">DUDI</td>
                    <td><strong>Dunia Usaha Dunia Industri:</strong> Mitra eksternal sekolah (perusahaan/mitra kerja) tempat siswa melaksanakan Praktik Kerja Lapangan (PKL).</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">RPP</td>
                    <td><strong>Rencana Pelaksanaan Pembelajaran:</strong> Dokumen rancangan detail skenario mengajar guru per tatap muka yang sekarang dapat dibuat secara instan oleh fitur Asisten AI.</td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Rombel</td>
                    <td><strong>Rombongan Belajar:</strong> Kelompok siswa yang terdaftar aktif dalam satu kelas/jurusan untuk mengikuti kegiatan belajar mengajar yang sama.</td>
                </tr>
            </tbody>
        </table>
    </div>
 
</body>
</html>
