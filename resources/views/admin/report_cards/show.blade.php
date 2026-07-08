@extends('layouts.admin')

@section('title', 'Detail Rapor - ' . $reportCard->student->full_name)

@section('content')
<style>
.rapor-detail-wrapper { background: #f7fafc; min-height: 100vh; padding: 30px; }
.rapor-detail-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); margin-bottom: 30px; }
.info-card, .grades-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07); margin-bottom: 25px; }
.btn-action-detail { padding: 10px 20px; border-radius: 8px; font-weight: 700; transition: all 0.3s; border: none; cursor: pointer; }
.btn-action-detail:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); }
.btn-back { background: #e2e8f0; color: #4a5568; }
.btn-pdf { background: #e53e3e; color: white; }
.btn-edit { background: #ecc94b; color: white; }
.btn-finalize { background: #3182ce; color: white; }
.btn-publish { background: #48bb78; color: white; }
.info-table { width: 100%; margin-bottom: 0; }
.info-table tr { border-bottom: 1px solid #e2e8f0; }
.info-table td { padding: 12px 8px; vertical-align: top; }
.score-display { font-size: 3rem; font-weight: 800; margin: 20px 0; }
.predicate-badge { font-size: 1.3rem; padding: 12px 24px; border-radius: 25px; font-weight: 700; display: inline-block; }
.status-badge { padding: 10px 20px; border-radius: 20px; font-weight: 700; display: inline-block; }
.grades-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.grades-table th, .grades-table td { border: 2px solid #e2e8f0; padding: 12px; text-align: center; }
.grades-table thead th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 700; }
.grades-table tbody tr:hover { background: #f7fafc; }
.achievement-badge { padding: 6px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 700; }
.notes-box { background: #f7fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 20px; min-height: 80px; }
</style>

<div class="rapor-detail-wrapper">
    <!-- Header -->
    <div class="rapor-detail-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
            <div>
                <a href="{{ route('admin.report_cards.index') }}" class="btn-action-detail btn-back" style="display: inline-block; margin-bottom: 10px;">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
                <h1 style="font-size: 2rem; font-weight: 800; margin: 0;"><i class="fas fa-file-alt mr-1"></i> Detail Rapor Siswa</h1>
                <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $reportCard->student->full_name }}</p>
            </div>
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('admin.report_cards.print', $reportCard) }}" class="btn-action-detail btn-pdf" target="_blank">
                    <i class="fas fa-file-pdf"></i> Download PDF
                </a>
                @if($reportCard->isEditable())
                    <a href="{{ route('admin.report_cards.edit', $reportCard) }}" class="btn-action-detail btn-edit">
                        <i class="fas fa-edit"></i> Edit Catatan
                    </a>
                @endif
                @if($reportCard->status === 'draft')
                    <form method="POST" action="{{ route('admin.report_cards.finalize', $reportCard) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-action-detail btn-finalize" onclick="return confirm('Finalize rapor ini? Setelah di-finalize tidak bisa diedit lagi.')">
                            <i class="fas fa-check"></i> Finalize
                        </button>
                    </form>
                @endif
                @if($reportCard->status === 'finalized')
                    <form method="POST" action="{{ route('admin.report_cards.publish', $reportCard) }}" style="display: inline;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-action-detail btn-publish" onclick="return confirm('Publish rapor ini? Siswa akan bisa mengakses rapor.')">
                            <i class="fas fa-paper-plane"></i> Publish
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
        <!-- Left Column: Student Info -->
        <div>
            <!-- Student Info Card -->
            <div class="info-card">
                <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 20px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                    <i class="fas fa-user" style="color: #667eea;"></i> Informasi Siswa
                </h5>
                <table class="info-table">
                    <tr>
                        <td style="width: 40%; font-weight: 700; color: #4a5568;">NISN</td>
                        <td><code style="background: #edf2f7; padding: 4px 8px; border-radius: 4px;">{{ $reportCard->student->nisn }}</code></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #4a5568;">Nama</td>
                        <td><strong>{{ $reportCard->student->full_name }}</strong></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #4a5568;">Kelas</td>
                        <td><span class="achievement-badge" style="background: #edf2f7; color: #4a5568;">{{ $reportCard->classroom->class_name }}</span></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #4a5568;">Semester</td>
                        <td>{{ $reportCard->semester->semester_name }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #4a5568;">Tahun Ajaran</td>
                        <td>{{ $reportCard->academicYear->year }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #4a5568;">Sekolah</td>
                        <td>{{ $reportCard->student->school->school_name }}</td>
                    </tr>
                </table>

                <div style="text-align: center; margin: 30px 0; padding: 25px; background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%); border-radius: 10px;">
                    <div class="score-display" style="color: {{ $reportCard->average_score >= 80 ? '#48bb78' : ($reportCard->average_score >= 70 ? '#ecc94b' : '#e53e3e') }};">
                        {{ number_format($reportCard->average_score, 1) }}
                    </div>
                    <span class="predicate-badge" style="background: {{ $reportCard->predicate == 'A' ? '#d4edda' : ($reportCard->predicate == 'B' ? '#cfe2ff' : ($reportCard->predicate == 'C' ? '#fff3cd' : '#f8d7da')) }}; color: {{ $reportCard->predicate == 'A' ? '#155724' : ($reportCard->predicate == 'B' ? '#084298' : ($reportCard->predicate == 'C' ? '#856404' : '#721c24')) }};">
                        PREDIKAT {{ $reportCard->predicate }}
                    </span>
                    <p style="margin: 15px 0 0 0; font-size: 1.1rem; color: #4a5568;">
                        <strong>Peringkat {{ $reportCard->rank }}</strong> dari {{ $reportCard->total_students }} siswa
                    </p>
                </div>

                <div style="margin: 20px 0;">
                    <span class="status-badge" style="background: {{ $reportCard->status == 'draft' ? '#e2e8f0' : ($reportCard->status == 'finalized' ? '#cfe2ff' : '#d4edda') }}; color: {{ $reportCard->status == 'draft' ? '#4a5568' : ($reportCard->status == 'finalized' ? '#084298' : '#155724') }};">
                        <i class="fas fa-{{ $reportCard->status == 'draft' ? 'file-alt' : ($reportCard->status == 'finalized' ? 'check-circle' : 'paper-plane') }}"></i> {{ ucfirst($reportCard->status) }}
                    </span>
                </div>

                @if($reportCard->finalized_at)
                    <small style="color: #718096; display: block; margin-top: 10px;">
                        <i class="fas fa-check-circle" style="color: #3182ce;"></i> Finalized: {{ $reportCard->finalized_at->format('d M Y H:i') }}<br>
                        oleh {{ $reportCard->finalizedBy->name ?? '-' }}
                    </small>
                @endif
                @if($reportCard->published_at)
                    <small style="color: #718096; display: block; margin-top: 8px;">
                        <i class="fas fa-paper-plane" style="color: #48bb78;"></i> Published: {{ $reportCard->published_at->format('d M Y H:i') }}<br>
                        oleh {{ $reportCard->publishedBy->name ?? '-' }}
                    </small>
                @endif
            </div>

            <!-- Attendance Card -->
            <div class="info-card">
                <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 20px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                    <i class="fas fa-calendar-check" style="color: #667eea;"></i> Kehadiran
                </h5>
                <table style="width: 100%;">
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; font-weight: 600; color: #4a5568;">Total Hari Efektif</td>
                        <td style="padding: 12px 8px; text-align: right;"><strong style="font-size: 1.1rem;">{{ $reportCard->total_days }}</strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #48bb78; font-weight: 600;"><i class="fas fa-check-circle"></i> Hadir</td>
                        <td style="padding: 12px 8px; text-align: right; color: #48bb78;"><strong style="font-size: 1.1rem;">{{ $reportCard->days_present }}</strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #ecc94b; font-weight: 600;"><i class="fas fa-notes-medical"></i> Sakit</td>
                        <td style="padding: 12px 8px; text-align: right; color: #ecc94b;"><strong style="font-size: 1.1rem;">{{ $reportCard->days_sick }}</strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #3182ce; font-weight: 600;"><i class="fas fa-hand-paper"></i> Izin</td>
                        <td style="padding: 12px 8px; text-align: right; color: #3182ce;"><strong style="font-size: 1.1rem;">{{ $reportCard->days_permission }}</strong></td>
                    </tr>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 12px 8px; color: #e53e3e; font-weight: 600;"><i class="fas fa-times-circle"></i> Alpa</td>
                        <td style="padding: 12px 8px; text-align: right; color: #e53e3e;"><strong style="font-size: 1.1rem;">{{ $reportCard->days_absent }}</strong></td>
                    </tr>
                    <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                        <td style="padding: 15px 8px; font-weight: 700;">Persentase Kehadiran</td>
                        <td style="padding: 15px 8px; text-align: right; font-weight: 700; font-size: 1.3rem;">{{ number_format($reportCard->attendance_percentage, 1) }}%</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Right Column: Grades & Notes -->
        <div>
            <!-- Grades Table -->
            <div class="grades-card">
                <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 20px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                    <i class="fas fa-chart-line" style="color: #667eea;"></i> Nilai Per Mata Pelajaran
                </h5>
                <div style="overflow-x: auto;">
                    <table class="grades-table">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th rowspan="2" style="width: 5%;">No</th>
                                <th rowspan="2" style="width: 25%;">Mata Pelajaran</th>
                                <th colspan="4">Komponen Nilai</th>
                                <th rowspan="2" style="width: 10%;">Akhir</th>
                                <th rowspan="2" style="width: 10%;">Predikat</th>
                            </tr>
                            <tr>
                                <th style="width: 10%;">Tugas<br><small>(20%)</small></th>
                                <th style="width: 10%;">UTS<br><small>(30%)</small></th>
                                <th style="width: 10%;">UAS<br><small>(40%)</small></th>
                                <th style="width: 10%;">Sikap<br><small>(10%)</small></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subjectScores as $index => $score)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td style="text-align: left; font-weight: 600;">{{ $score['subject'] }}</td>
                                    <td>{{ $score['tugas'] }}</td>
                                    <td>{{ $score['uts'] }}</td>
                                    <td>{{ $score['uas'] }}</td>
                                    <td>{{ $score['sikap'] }}</td>
                                    <td><strong style="font-size: 1.1rem; color: #2d3748;">{{ $score['final'] }}</strong></td>
                                    <td>
                                        <span class="achievement-badge" style="background: {{ $score['predicate'] === 'A' ? '#d4edda' : ($score['predicate'] === 'B' ? '#cfe2ff' : ($score['predicate'] === 'C' ? '#fff3cd' : '#f8d7da')) }}; color: {{ $score['predicate'] === 'A' ? '#155724' : ($score['predicate'] === 'B' ? '#084298' : ($score['predicate'] === 'C' ? '#856404' : '#721c24')) }};">
                                            {{ $score['predicate'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #a0aec0; padding: 30px;">
                                        <i class="fas fa-inbox" style="font-size: 2rem; display: block; margin-bottom: 10px;"></i>
                                        Belum ada data nilai
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        @if($subjectScores)
                            <tfoot>
                                <tr style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                    <th colspan="6" style="text-align: right; padding: 15px; font-size: 1.1rem;">RATA-RATA:</th>
                                    <th style="font-size: 1.3rem;">{{ number_format($reportCard->average_score, 1) }}</th>
                                    <th style="font-size: 1.2rem;">{{ $reportCard->predicate }}</th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Achievements -->
            @if($achievements->isNotEmpty())
                <div class="grades-card">
                    <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 20px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                        <i class="fas fa-trophy" style="color: #ecc94b;"></i> Prestasi & Penghargaan
                    </h5>
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr style="background: #f7fafc;">
                                <th style="padding: 12px; border: 1px solid #e2e8f0; text-align: left; width: 5%;">No</th>
                                <th style="padding: 12px; border: 1px solid #e2e8f0; text-align: left; width: 35%;">Prestasi</th>
                                <th style="padding: 12px; border: 1px solid #e2e8f0; text-align: center; width: 15%;">Jenis</th>
                                <th style="padding: 12px; border: 1px solid #e2e8f0; text-align: center; width: 15%;">Tingkat</th>
                                <th style="padding: 12px; border: 1px solid #e2e8f0; text-align: center; width: 15%;">Peringkat</th>
                                <th style="padding: 12px; border: 1px solid #e2e8f0; text-align: center; width: 15%;">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($achievements as $index => $achievement)
                                <tr style="border-bottom: 1px solid #e2e8f0;">
                                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: center;">{{ $index + 1 }}</td>
                                    <td style="padding: 12px; border: 1px solid #e2e8f0; font-weight: 600;">{{ $achievement->title }}</td>
                                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: center;">
                                        <span class="achievement-badge" style="background: #edf2f7; color: #4a5568;">{{ $achievement->type_label }}</span>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: center;">
                                        <span class="achievement-badge" style="background: {{ $achievement->level == 'international' ? '#d4edda' : ($achievement->level == 'national' ? '#cfe2ff' : '#fff3cd') }}; color: {{ $achievement->level == 'international' ? '#155724' : ($achievement->level == 'national' ? '#084298' : '#856404') }};">
                                            {{ $achievement->level_label }}
                                        </span>
                                    </td>
                                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: center; font-weight: 700;">{{ $achievement->rank_label }}</td>
                                    <td style="padding: 12px; border: 1px solid #e2e8f0; text-align: center;">{{ $achievement->achievement_date?->format('d M Y') ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            <!-- Notes -->
            <div class="grades-card">
                <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 20px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                    <i class="fas fa-sticky-note" style="color: #667eea;"></i> Catatan & Rekomendasi
                </h5>
                
                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 10px;">
                        <i class="fas fa-chalkboard-teacher" style="color: #3182ce;"></i> Catatan Wali Kelas:
                    </label>
                    <div class="notes-box">
                        {{ $reportCard->teacher_notes ?? 'Belum ada catatan dari wali kelas.' }}
                    </div>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 10px;">
                        <i class="fas fa-user-tie" style="color: #667eea;"></i> Catatan Kepala Sekolah:
                    </label>
                    <div class="notes-box">
                        {{ $reportCard->principal_notes ?? 'Belum ada catatan dari kepala sekolah.' }}
                    </div>
                </div>

                <div style="margin-bottom: 0;">
                    <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 10px;">
                        <i class="fas fa-lightbulb" style="color: #ecc94b;"></i> Saran Pengembangan:
                    </label>
                    <div class="notes-box">
                        {{ $reportCard->recommendations ?? 'Belum ada rekomendasi.' }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
