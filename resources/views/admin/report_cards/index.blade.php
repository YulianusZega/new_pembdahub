@extends('layouts.admin')

@section('title', 'Rapor Digital')

@section('content')
<style>
.rapor-wrapper { background: #f7fafc; min-height: 100vh; padding: 30px; }
.rapor-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); margin-bottom: 30px; }
.filter-card, .table-card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07); margin-bottom: 30px; }
.btn-generate { background: white; color: #667eea; border: none; padding: 12px 30px; border-radius: 8px; font-weight: 700; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); transition: all 0.3s; }
.btn-generate:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2); }
.form-select-custom { border: 2px solid #e2e8f0; border-radius: 8px; padding: 12px 15px; font-size: 0.95rem; transition: all 0.3s; background: white; width: 100%; }
.form-select-custom:focus { border-color: #667eea; outline: none; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
.table-modern { width: 100%; border-collapse: separate; border-spacing: 0 8px; }
.table-modern thead th { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px; font-weight: 700; text-align: center; border: none; font-size: 0.9rem; }
.table-modern thead th:first-child { border-radius: 10px 0 0 10px; }
.table-modern thead th:last-child { border-radius: 0 10px 10px 0; }
.table-modern tbody tr { background: white; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); transition: all 0.3s; }
.table-modern tbody tr:hover { box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15); transform: translateY(-2px); }
.table-modern tbody td { padding: 18px; border: none; vertical-align: middle; }
.table-modern tbody tr td:first-child { border-radius: 10px 0 0 10px; }
.table-modern tbody tr td:last-child { border-radius: 0 10px 10px 0; }
.badge-modern { padding: 8px 16px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; }
.nilai-display { font-size: 1.5rem; font-weight: 800; padding: 10px 20px; border-radius: 10px; display: inline-block; }
.btn-action { 
    width: 38px; 
    height: 38px; 
    border-radius: 8px; 
    color: white; 
    text-decoration: none; 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    transition: all 0.3s; 
    font-size: 1rem;
    border: none;
    cursor: pointer;
}
.btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0, 0, 0, 0.25); color: white; text-decoration: none; }
.btn-action i { font-size: 1rem; }
</style>

<div class="rapor-wrapper">
    <div class="rapor-header">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 2.2rem; font-weight: 800; margin: 0;"><i class="fas fa-chart-bar mr-1"></i> Rapor Digital</h1>
                <p style="margin: 10px 0 0 0; opacity: 0.9; font-size: 1.05rem;">Kelola dan generate rapor siswa otomatis dari data nilai</p>
                
                @php
                    $user = auth()->user();
                    $isHomeroomTeacher = $user->isGuru() && $user->isHomeroomTeacher() && !$user->hasAnyRole(['superadmin', 'admin_sekolah']);
                @endphp
                
                @if($isHomeroomTeacher)
                    @php
                        $homeroomClasses = $user->homeroomClassrooms()->pluck('class_name')->toArray();
                    @endphp
                    <div style="margin-top: 12px; background: rgba(255, 255, 255, 0.2); padding: 10px 18px; border-radius: 8px; display: inline-block;">
                        <i class="fas fa-user-check"></i> <strong>Wali Kelas:</strong> {{ implode(', ', $homeroomClasses) }}
                    </div>
                @endif
            </div>
            <button type="button" class="btn-generate" data-toggle="modal" data-target="#generateModal">
                <i class="fas fa-file-invoice"></i> Generate Rapor
            </button>
            <button type="button" class="btn-generate" style="margin-left: 10px; background: #48bb78; color: white;" data-toggle="modal" data-target="#bulkDownloadModal">
                <i class="fas fa-download"></i> Bulk Download PDF
            </button>
            @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminSekolah())
            <a href="{{ route('admin.settings.report-cards') }}" class="btn-generate" style="margin-left: 10px; background: #4f46e5; color: white; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                <i class="fas fa-cog" style="margin-right: 6px;"></i> Pengaturan Rapor
            </a>
            @endif
        </div>
    </div>

    <div class="filter-card">
        <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 25px; font-size: 1.2rem;">
            <i class="fas fa-filter" style="color: #667eea;"></i> Filter Rapor
        </h5>
        <form method="GET" action="{{ route('admin.report_cards.index') }}" id="filterForm">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
                <div>
                    <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 10px; font-size: 0.95rem;">
                        <i class="fas fa-calendar-alt" style="color: #667eea;"></i> Semester
                    </label>
                    <select name="semester_id" class="form-select-custom" onchange="document.getElementById('filterForm').submit()">
                        @foreach($semesters->unique('id') as $semester)
                            <option value="{{ $semester->id }}" {{ $semesterId == $semester->id ? 'selected' : '' }}>
                                {{ $semester->semester_name }} - {{ $semester->academicYear->year }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 10px; font-size: 0.95rem;">
                        <i class="fas fa-school" style="color: #667eea;"></i> Kelas
                    </label>
                    <select name="classroom_id" class="form-select-custom" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Semua Kelas --</option>
                        @foreach($classrooms as $classroom)
                            <option value="{{ $classroom->id }}" {{ $classroomId == $classroom->id ? 'selected' : '' }}>
                                {{ $classroom->class_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 10px; font-size: 0.95rem;">
                        <i class="fas fa-flag" style="color: #667eea;"></i> Status
                    </label>
                    <select name="status" class="form-select-custom" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- Semua Status --</option>
                        <option value="draft" {{ $status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="finalized" {{ $status == 'finalized' ? 'selected' : '' }}>Finalized</option>
                        <option value="published" {{ $status == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

    <div class="table-card">
        <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 25px; font-size: 1.2rem;">
            <i class="fas fa-clipboard mr-1"></i> Daftar Rapor - {{ $semesters->where('id', $semesterId)->first()->semester_name ?? '' }} {{ $semesters->where('id', $semesterId)->first()->academicYear->year ?? '' }}
        </h5>

        @if($reportCards->isEmpty())
            <div style="background: linear-gradient(135deg, #fff3cd 0%, #ffe5a1 100%); border-left: 6px solid #ffc107; padding: 25px; border-radius: 10px;">
                <strong style="color: #856404; font-size: 1.1rem;"><i class="fas fa-exclamation-triangle"></i> Belum ada rapor untuk semester ini.</strong>
                <p style="color: #856404; margin: 12px 0 0 0; font-size: 0.95rem;">Klik tombol <strong>"Generate Rapor"</strong> di atas untuk membuat rapor otomatis.</p>
            </div>
        @else
            @php $hasEmptyData = $reportCards->where('average_score', 0)->count() > 0; @endphp
            @if($hasEmptyData)
                <div style="background: linear-gradient(135deg, #f8d7da 0%, #f5c2c7 100%); border-left: 6px solid #dc3545; padding: 25px; border-radius: 10px; margin-bottom: 25px;">
                    <strong style="color: #721c24; font-size: 1.1rem;"><i class="fas fa-exclamation-circle"></i> Peringatan!</strong>
                    <p style="color: #721c24; margin: 12px 0 0 0;">Beberapa rapor memiliki nilai 0.0 karena belum ada data nilai untuk semester ini. Pilih semester <strong>"Genap - 2025/2026"</strong>.</p>
                </div>
            @endif

            <div style="overflow-x: auto;">
                <table class="table-modern">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th style="width: 3%;">No</th>
                            <th style="width: 10%;">NISN</th>
                            <th style="width: 18%;">Nama Siswa</th>
                            <th style="width: 8%;">Kelas</th>
                            <th style="width: 10%;">Nilai</th>
                            <th style="width: 10%;">Predikat</th>
                            <th style="width: 8%;">Ranking</th>
                            <th style="width: 13%;">Kehadiran</th>
                            <th style="width: 8%;">Status</th>
                            <th style="width: 12%;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reportCards as $index => $reportCard)
                            <tr>
                                <td style="text-align: center; font-weight: 700;">{{ $reportCards->firstItem() + $index }}</td>
                                <td><code style="background: #edf2f7; padding: 6px 12px; border-radius: 6px; font-weight: 600;">{{ $reportCard->student->nisn }}</code></td>
                                <td><strong style="color: #2d3748;">{{ $reportCard->student->full_name }}</strong></td>
                                <td style="text-align: center;"><span class="badge-modern" style="background: #edf2f7; color: #4a5568;">{{ $reportCard->classroom->class_name }}</span></td>
                                <td style="text-align: center;">
                                    @if($reportCard->average_score > 0)
                                        <span class="nilai-display" style="background: {{ $reportCard->average_score >= 80 ? '#d4edda' : ($reportCard->average_score >= 70 ? '#fff3cd' : '#f8d7da') }}; color: {{ $reportCard->average_score >= 80 ? '#155724' : ($reportCard->average_score >= 70 ? '#856404' : '#721c24') }};">
                                            {{ number_format($reportCard->average_score, 1) }}
                                        </span>
                                    @else
                                        <span style="color: #cbd5e0; font-size: 1.3rem; font-weight: 700;">0.0</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    @if($reportCard->average_score > 0)
                                        <span class="badge-modern" style="background: {{ $reportCard->predicate == 'A' ? '#d4edda' : ($reportCard->predicate == 'B' ? '#cfe2ff' : ($reportCard->predicate == 'C' ? '#fff3cd' : '#f8d7da')) }}; color: {{ $reportCard->predicate == 'A' ? '#155724' : ($reportCard->predicate == 'B' ? '#084298' : ($reportCard->predicate == 'C' ? '#856404' : '#721c24')) }}; font-size: 1.1rem;">
                                            {{ $reportCard->predicate }}
                                        </span>
                                    @else
                                        <small style="color: #e53e3e; font-weight: 600;"><i class="fas fa-times-circle"></i> N/A</small>
                                    @endif
                                </td>
                                <td style="text-align: center;"><strong style="font-size: 1.3rem;">{{ $reportCard->rank }}</strong><small style="color: #a0aec0;"> / {{ $reportCard->total_students }}</small></td>
                                <td style="text-align: center;">
                                    @if($reportCard->total_days > 0)
                                        <div style="margin-bottom: 6px; font-weight: 600;"><strong>{{ $reportCard->days_present }}</strong> / {{ $reportCard->total_days }}</div>
                                        <span class="badge-modern" style="background: {{ $reportCard->attendance_percentage >= 80 ? '#d4edda' : ($reportCard->attendance_percentage >= 60 ? '#fff3cd' : '#f8d7da') }}; color: {{ $reportCard->attendance_percentage >= 80 ? '#155724' : ($reportCard->attendance_percentage >= 60 ? '#856404' : '#721c24') }};">{{ number_format($reportCard->attendance_percentage, 0) }}%</span>
                                    @else
                                        <span style="color: #cbd5e0;">-</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="badge-modern" style="background: {{ $reportCard->status == 'draft' ? '#e2e8f0' : ($reportCard->status == 'finalized' ? '#cfe2ff' : '#d4edda') }}; color: {{ $reportCard->status == 'draft' ? '#4a5568' : ($reportCard->status == 'finalized' ? '#084298' : '#155724') }};">{{ ucfirst($reportCard->status) }}</span>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: inline-flex; gap: 8px; align-items: center;">
                                        <a href="{{ route('admin.report_cards.show', $reportCard) }}" class="btn-action" style="background: #3182ce;" title="Lihat Detail Rapor">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                        <a href="{{ route('admin.report_cards.print', $reportCard) }}" class="btn-action" style="background: #e53e3e;" title="Download PDF" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        @if($reportCard->isEditable())
                                            <a href="{{ route('admin.report_cards.edit', $reportCard) }}" class="btn-action" style="background: #48bb78;" title="Edit Catatan">
                                                <i class="fas fa-pen-to-square"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div style="margin-top: 25px; display: flex; justify-content: center;">{{ $reportCards->links() }}</div>
        @endif
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="generateModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form method="POST" action="{{ route('admin.report_cards.generate') }}">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-file-invoice"></i> Generate Rapor</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #2d3748; margin-bottom: 8px; display: block;">Semester *</label>
                        <select name="semester_id" class="form-select-custom" required>
                            @foreach($semesters->unique('id') as $semester)
                                <option value="{{ $semester->id }}" {{ $semesterId == $semester->id ? 'selected' : '' }}>{{ $semester->semester_name }} - {{ $semester->academicYear->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #2d3748; margin-bottom: 8px; display: block;">Kelas</label>
                        <select name="classroom_id" class="form-select-custom">
                            <option value="">-- Semua Kelas --</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="background: #e6f7ff; padding: 20px; border-radius: 10px; border-left: 5px solid #3182ce;">
                        <strong style="color: #2c5282;"><i class="fas fa-info-circle"></i> Info:</strong>
                        <p style="color: #2c5282; margin: 10px 0 0 0;">Generate akan membuat rapor baru untuk siswa yang belum punya rapor, dan update rapor yang masih berstatus <strong>draft</strong>.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-weight: 700;"><i class="fas fa-cogs"></i> Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Download Modal -->
<div class="modal fade" id="bulkDownloadModal" tabindex="-1" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px;">
            <form method="POST" action="{{ route('admin.report_cards.bulk_download') }}">
                @csrf
                <div class="modal-header" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title"><i class="fas fa-download"></i> Bulk Download Rapor PDF</h5>
                    <button type="button" class="close" data-dismiss="modal" style="color: white;"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #2d3748; margin-bottom: 8px; display: block;">Semester *</label>
                        <select name="semester_id" class="form-select-custom" required>
                            @foreach($semesters->unique('id') as $semester)
                                <option value="{{ $semester->id }}" {{ $semesterId == $semester->id ? 'selected' : '' }}>{{ $semester->semester_name }} - {{ $semester->academicYear->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="margin-bottom: 20px;">
                        <label style="font-weight: 700; color: #2d3748; margin-bottom: 8px; display: block;">Kelas *</label>
                        <select name="classroom_id" class="form-select-custom" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($classrooms as $classroom)
                                <option value="{{ $classroom->id }}">{{ $classroom->class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="background: #f0fff4; padding: 20px; border-radius: 10px; border-left: 5px solid #48bb78;">
                        <strong style="color: #276749;"><i class="fas fa-info-circle"></i> Info:</strong>
                        <p style="color: #276749; margin: 10px 0 0 0;">Semua rapor di kelas yang dipilih akan di-download sebagai file <strong>ZIP</strong> berisi PDF per siswa. Proses ini mungkin memerlukan waktu beberapa detik.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn" style="background: linear-gradient(135deg, #48bb78 0%, #38a169 100%); color: white; font-weight: 700;"><i class="fas fa-download"></i> Download ZIP</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
