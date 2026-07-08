<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Rapor - {{ $reportCard->student->full_name }}</title>
    <style>
        @page {
            margin: 20mm 25mm;
            size: A4 portrait;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'DejaVu Sans', 'Arial', sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #000;
        }
        
        .header {
            text-align: center;
            border-bottom: 3px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .header .yayasan {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 5px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        
        .header .school-name {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        
        .header .rapor-title {
            font-size: 16pt;
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
            text-decoration: underline;
        }
        
        .header .address {
            font-size: 9.5pt;
            margin: 3px 0;
            line-height: 1.5;
            color: #333;
        }
        
        .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin: 18px 0 10px;
            padding: 8px;
            background: #e8e8e8;
            border-left: 4px solid #333;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            font-size: 10pt;
            border: 2px solid #333;
            background: #fafafa;
        }
        
        .info-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        .info-table .label {
            width: 35%;
            font-weight: bold;
            background: #e8e8e8;
        }
        
        .info-table .separator {
            width: 3%;
            font-weight: bold;
        }
        
        .info-table .value {
            font-weight: 600;
        }
        
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 9pt;
            border: 2px solid #000;
        }
        
        .grades-table th,
        .grades-table td {
            border: 1px solid #333;
            padding: 6px 4px;
            text-align: center;
        }
        
        .grades-table thead th {
            background-color: #333;
            color: white;
            font-weight: bold;
            padding: 8px 4px;
        }
        
        .grades-table .subject-col {
            text-align: left;
            padding-left: 10px;
            font-weight: 600;
        }
        
        .grades-table tfoot td {
            background-color: #333;
            color: white;
            font-weight: bold;
            padding: 10px 4px;
            font-size: 11pt;
        }
        
        .attendance-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
            border: 2px solid #333;
        }
        
        .attendance-table td {
            border: 1px solid #333;
            padding: 6px 10px;
        }
        
        .attendance-table .label-col {
            width: 70%;
            font-weight: bold;
            background: #e8e8e8;
        }
        
        .attendance-table .value-col {
            font-weight: 600;
            text-align: center;
        }
        
        .attendance-table .total-row {
            background: #333;
            color: white;
            font-weight: bold;
        }
        
        .achievements-section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        
        .achievements-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
            border: 2px solid #333;
        }
        
        .achievements-table th,
        .achievements-table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
        }
        
        .achievements-table th {
            background-color: #333;
            color: white;
            font-weight: bold;
        }
        
        .achievements-table .title-col {
            text-align: left;
            padding-left: 8px;
        }
        
        .notes-section {
            margin-bottom: 18px;
            page-break-inside: avoid;
        }
        
        .notes-section .section-title {
            font-weight: bold;
            font-size: 11pt;
            margin-bottom: 6px;
            padding: 6px;
            background: #e8e8e8;
            border: 1px solid #333;
        }
        
        .notes-section .content {
            border: 2px solid #333;
            padding: 10px;
            min-height: 70px;
            font-size: 10pt;
            text-align: justify;
            line-height: 1.6;
            background: #fafafa;
        }
        
        .signature-section {
            margin-top: 35px;
            display: table;
            width: 100%;
            page-break-inside: avoid;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            font-size: 10pt;
            padding: 0 15px;
        }
        
        .signature-box .position {
            margin-bottom: 65px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .signature-box .name {
            font-weight: bold;
            text-decoration: underline;
        }
        
        .footer-note {
            margin-top: 25px;
            padding: 10px;
            border: 1px solid #333;
            background: #f0f0f0;
            font-size: 9pt;
            text-align: center;
            font-style: italic;
        }
        
        .score-box {
            display: inline-block;
            padding: 10px 20px;
            border: 2px solid #333;
            background: #f0f0f0;
            font-size: 14pt;
            font-weight: bold;
            margin: 10px 0;
        }
        
        .predicate-box {
            display: inline-block;
            padding: 8px 16px;
            border: 2px solid #000;
            background: #fff;
            font-size: 12pt;
            font-weight: bold;
            margin-left: 15px;
        }
        
        .predicate-badge {
            display: inline-block;
            padding: 3px 10px;
            border: 1.5px solid #000;
            font-weight: bold;
            font-size: 10pt;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        
        .summary-box {
            margin: 15px 0;
            padding: 12px;
            border: 2px solid #333;
            background: #f8f8f8;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header Kop Surat -->
    <div class="header">
        <div class="yayasan">Yayasan Perguruan Pembda Nias</div>
        <div class="school-name">{{ strtoupper($reportCard->student->school->school_name) }}</div>
        <div class="rapor-title">Rapor Digital</div>
        <div class="address">
            {{ $reportCard->student->school->address ?? 'Alamat Sekolah' }}<br>
            Telp: {{ $reportCard->student->school->phone ?? '-' }} | Email: {{ $reportCard->student->school->email ?? '-' }}
        </div>
    </div>

    <!-- Student Information -->
    <table class="info-table">
        <tr>
            <td class="label">Nama Siswa</td>
            <td class="separator">:</td>
            <td class="value">{{ $reportCard->student->full_name }}</td>
            <td class="label">NISN</td>
            <td class="separator">:</td>
            <td class="value">{{ $reportCard->student->nisn }}</td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="separator">:</td>
            <td class="value">{{ $reportCard->classroom->class_name }}</td>
            <td class="label">Semester</td>
            <td class="separator">:</td>
            <td class="value">{{ $reportCard->semester->semester_name }}</td>
        </tr>
        <tr>
            <td class="label">Tahun Ajaran</td>
            <td class="separator">:</td>
            <td class="value">{{ $reportCard->academicYear->year }}</td>
            <td class="label">Ranking Kelas</td>
            <td class="separator">:</td>
            <td class="value">{{ $reportCard->rank }} dari {{ $reportCard->total_students }} siswa</td>
        </tr>
    </table>

    <!-- Summary Box -->
    <div class="summary-box" style="margin: 18px 0;">
        <span style="font-size: 11pt; font-weight: bold;">Rata-rata Nilai:</span>
        <span class="score-box">{{ number_format($reportCard->average_score, 1) }}</span>
        <span class="predicate-box">PREDIKAT {{ $reportCard->predicate }}</span>
    </div>

    <!-- Section Title Nilai -->
    <div style="font-weight: bold; font-size: 11pt; margin: 18px 0 10px; padding: 8px; background: #e8e8e8; border-left: 4px solid #333;">
        <i class="fas fa-chart-bar mr-1"></i> DAFTAR NILAI MATA PELAJARAN
    </div>

    <!-- Grades Table -->
    <table class="grades-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: 5%;">No</th>
                <th rowspan="2" style="width: 30%;">Mata Pelajaran</th>
                <th colspan="4">Komponen Penilaian</th>
                <th rowspan="2" style="width: 8%;">Nilai<br>Akhir</th>
                <th rowspan="2" style="width: 8%;">Predikat</th>
            </tr>
            <tr>
                <th style="width: 10%;">Tugas<br>(20%)</th>
                <th style="width: 10%;">UTS<br>(30%)</th>
                <th style="width: 10%;">UAS<br>(40%)</th>
                <th style="width: 9%;">Sikap<br>(10%)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subjectScores as $index => $score)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="subject-col">{{ $score['subject'] }}</td>
                    <td>{{ $score['tugas'] }}</td>
                    <td>{{ $score['uts'] }}</td>
                    <td>{{ $score['uas'] }}</td>
                    <td>{{ $score['sikap'] }}</td>
                    <td class="font-bold">{{ $score['final'] }}</td>
                    <td class="font-bold">{{ $score['predicate'] }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right">Rata-rata</td>
                <td class="font-bold">{{ number_format($reportCard->average_score, 1) }}</td>
                <td class="font-bold">{{ $reportCard->predicate }}</td>
            </tr>
        </tfoot>
    </table>

    <!-- Keterangan Predikat -->
    <div style="font-size: 9pt; margin: 12px 0 20px; padding: 10px; background: #f8f8f8; border: 2px solid #ddd; border-radius: 4px;">
        <strong>Keterangan Predikat:</strong> A = Sangat Baik (90-100) &nbsp;|&nbsp; B = Baik (80-89) &nbsp;|&nbsp; C = Cukup (70-79) &nbsp;|&nbsp; D = Kurang (&lt;70)
    </div>

    <!-- Section Title Kehadiran -->
    <div style="font-weight: bold; font-size: 11pt; margin: 20px 0 10px; padding: 8px; background: #e8e8e8; border-left: 4px solid #333;">
        <i class="fas fa-chart-bar mr-1"></i> KEHADIRAN SISWA
    </div>

    <!-- Attendance -->
    <div class="attendance-section">
        <table class="attendance-table">
            <tr>
                <td class="label-col">Total Hari Efektif</td>
                <td class="value-col">{{ $reportCard->total_days }} hari</td>
            </tr>
            <tr>
                <td class="label-col">Hadir</td>
                <td class="value-col">{{ $reportCard->days_present }} hari</td>
            </tr>
            <tr>
                <td class="label-col">Sakit</td>
                <td class="value-col">{{ $reportCard->days_sick }} hari</td>
            </tr>
            <tr>
                <td class="label-col">Izin</td>
                <td class="value-col">{{ $reportCard->days_permission }} hari</td>
            </tr>
            <tr>
                <td class="label-col">Tanpa Keterangan (Alpa)</td>
                <td class="value-col">{{ $reportCard->days_absent }} hari</td>
            </tr>
            <tr class="total-row">
                <td class="label-col">Persentase Kehadiran</td>
                <td class="value-col">{{ number_format($reportCard->attendance_percentage, 1) }}%</td>
            </tr>
            </tr>
        </table>
    </div>

    <!-- Achievements -->
    @if($achievements->isNotEmpty())
        <!-- Section Title Prestasi -->
        <div style="font-weight: bold; font-size: 11pt; margin: 20px 0 10px; padding: 8px; background: #e8e8e8; border-left: 4px solid #333; page-break-before: avoid;">
            <i class="fas fa-trophy mr-1"></i> PRESTASI & PENGHARGAAN
        </div>
        
        <div class="achievements-section">
            <table class="achievements-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">No</th>
                        <th style="width: 40%;">Prestasi</th>
                        <th style="width: 15%;">Jenis</th>
                        <th style="width: 20%;">Tingkat</th>
                        <th style="width: 20%;">Peringkat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($achievements as $index => $achievement)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="title-col">{{ $achievement->title }}</td>
                            <td>{{ $achievement->type_label }}</td>
                            <td>{{ $achievement->level_label }}</td>
                            <td>{{ $achievement->rank_label }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <!-- Teacher Notes -->
    <div class="notes-section" style="page-break-inside: avoid; margin-top: 20px;">
        <div style="font-weight: bold; font-size: 11pt; margin-bottom: 8px; padding: 8px; background: #e8e8e8; border-left: 4px solid #333;">
            <i class="fas fa-edit mr-1"></i> CATATAN WALI KELAS
        </div>
        <div class="content">
            {{ $reportCard->teacher_notes ?? 'Siswa menunjukkan perkembangan yang baik selama semester ini. Terus pertahankan semangat belajar dan tingkatkan prestasi di semester berikutnya.' }}
        </div>
    </div>

    <!-- Principal Notes -->
    <div class="notes-section" style="page-break-inside: avoid;">
        <div style="font-weight: bold; font-size: 11pt; margin-bottom: 8px; padding: 8px; background: #e8e8e8; border-left: 4px solid #333;">
            <i class="fas fa-user-tie mr-1"></i> CATATAN KEPALA SEKOLAH
        </div>
        <div class="content">
            {{ $reportCard->principal_notes ?? 'Selamat atas pencapaian yang telah diraih. Terus tingkatkan prestasi dan pertahankan karakter yang baik di semester berikutnya.' }}
        </div>
    </div>

    <!-- Recommendations -->
    @if($reportCard->recommendations)
        <div class="notes-section" style="page-break-inside: avoid;">
            <div style="font-weight: bold; font-size: 11pt; margin-bottom: 8px; padding: 8px; background: #e8e8e8; border-left: 4px solid #333;">
                <i class="fas fa-lightbulb text-yellow-500 mr-1"></i> SARAN PENGEMBANGAN
            </div>
            <div class="content">
                {{ $reportCard->recommendations }}
            </div>
        </div>
    @endif

    <!-- Signatures -->
    <div class="signature-section" style="margin-top: 30px; page-break-inside: avoid;">
        <div class="signature-box" style="text-align: center;">
            <div style="margin-bottom: 5px;">Orang Tua/Wali Siswa</div>
            <div style="margin: 60px 0 5px 0;"></div>
            <div style="font-weight: bold; text-decoration: underline;">(_____________________)</div>
        </div>
        <div class="signature-box" style="text-align: center;">
            <div style="margin-bottom: 5px;">{{ $reportCard->student->school->city ?? 'Gunungsitoli' }}, {{ now()->translatedFormat('d F Y') }}</div>
            <div style="margin-bottom: 5px;">Wali Kelas</div>
            <div style="margin: 60px 0 5px 0;"></div>
            <div style="font-weight: bold; text-decoration: underline;">(_____________________)</div>
            <div style="font-size: 9pt; margin-top: 2px;">NIP. .........................</div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px; page-break-inside: avoid;">
        <div style="font-weight: bold; margin-bottom: 5px;">Mengetahui,</div>
        <div style="font-weight: bold; margin-bottom: 5px;">Kepala Sekolah</div>
        <div style="margin: 60px 0 5px 0;"></div>
        <div style="font-weight: bold; text-decoration: underline;">(_____________________)</div>
        <div style="font-size: 9pt; margin-top: 2px;">NIP. .........................</div>
    </div>

    <!-- Footer -->
    <div class="footer-note" style="margin-top: 25px;">
        <small>Dokumen ini dicetak dari sistem {{ config('app.name') }} pada {{ now()->format('d/m/Y H:i') }} WIB<br>
        Rapor ini sah dan berlaku apabila telah ditandatangani oleh pihak yang berwenang</small>
    </div>
</body>
</html>
