@extends('layouts.guru')

@section('content')
<style>
    .document-card {
        background: #ffffff;
        border-radius: 1rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.05);
    }
    .doc-header {
        border-bottom: 4px solid #4f46e5;
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
    }
    .doc-title {
        font-weight: 900;
        font-size: 2.25rem;
        color: #1e293b;
        letter-spacing: 1px;
        line-height: 1.3;
    }
    .doc-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        letter-spacing: 1px;
    }
    
    .info-row {
        display: flex;
        margin-bottom: 0.75rem;
    }
    .info-label {
        width: 200px;
        font-weight: 600;
        color: #334155;
    }
    .info-value {
        flex: 1;
        border-bottom: 1px dotted #94a3b8;
        color: #0f172a;
        font-weight: 600;
    }
    
    .table-doc {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 2rem;
    }
    .table-doc th, .table-doc td {
        border: 1px solid #cbd5e1;
        padding: 1rem;
        vertical-align: top;
    }
    .table-doc th {
        background-color: #f8fafc;
        font-weight: 700;
        color: #334155;
        text-align: center;
    }
    .eval-col {
        color: #94a3b8;
        font-style: italic;
        text-align: center;
        vertical-align: middle !important;
        background: #f8fafc;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0 text-dark">Detail Perjanjian Kinerja</h4>
                <a href="{{ route('guru.performance_contracts.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <div class="document-card p-4 p-md-5">
                <!-- Header Dokumen -->
                <div class="text-center doc-header">
                    <h3 class="doc-title text-uppercase">
                        @if($contract->contract_type == 'pkg_kejuruan')
                            PERJANJIAN KINERJA GURU<br><span class="text-primary">(PRODUKTIF/KEJURUAN)</span>
                        @elseif($contract->contract_type == 'pkg_umum')
                            PERJANJIAN KINERJA GURU<br><span class="text-primary">(MAPEL UMUM)</span>
                        @else
                            PERJANJIAN KINERJA JABATAN
                        @endif
                    </h3>
                    <p class="doc-subtitle text-uppercase mt-2">SMKS PEMBDA NIAS - TAHUN PELAJARAN {{ $contract->academicYear->year }}</p>
                </div>

                <div class="mb-4">
                    <span class="badge bg-{{ $contract->status == 'draft' ? 'secondary' : ($contract->status == 'approved_by_yayasan' ? 'success' : 'primary') }}">
                        Status: {{ strtoupper(str_replace('_', ' ', $contract->status)) }}
                    </span>
                </div>

                <!-- Pernyataan Resmi & Info Personal -->
                <div class="mb-5 pb-3 border-bottom">
                    <p class="mb-3 text-dark text-justify" style="line-height: 1.6; font-size: 1.05rem;">
                        Yang bertanda tangan di bawah ini, saya selaku <strong>Pihak yang menyatakan berjanji</strong>:
                    </p>
                    
                    <div class="info-row">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value text-dark">{{ auth()->user()->name }}</div>
                    </div>
                    @if($contract->contract_type == 'jabatan_tambahan')
                    <div class="info-row">
                        <div class="info-label">Jabatan Tambahan</div>
                        <div class="info-value">{{ $contract->position->position_name ?? '-' }}</div>
                    </div>
                    @endif
                    
                    <p class="mt-4 text-dark text-justify" style="line-height: 1.6; font-size: 1.05rem;">
                        Dengan ini menyatakan <strong>KOMITMEN DAN KESANGGUPAN PENUH</strong> untuk melaksanakan serta mencapai target kinerja riil pada Tahun Pelajaran {{ $contract->academicYear->year }}, sebagaimana tertuang secara rinci pada tabel bukti fisik nyata berikut ini:
                    </p>
                </div>

                <!-- Data Tabel -->
                <div class="table-responsive">
                    @if(in_array($contract->contract_type, ['pkg_kejuruan', 'pkg_umum']))
                        <table class="table-doc">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Pilar Perjanjian Kinerja</th>
                                    <th width="45%">Rencana Bukti Fisik Nyata (Target)</th>
                                    <th width="15%">Skor (1-5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="fw-bold">{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kompetensi Praktik (30%)' : 'Kompetensi Relevansi Praktik (30%)' }}</td>
                                    <td>{{ $contract->target_data['pilar_1'] ?? '-' }}</td>
                                    <td class="eval-col">Menunggu Evaluasi Akhir</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="fw-bold">{{ $contract->contract_type == 'pkg_kejuruan' ? 'Kontribusi Program (30%)' : 'Kontribusi Program/TEFA (30%)' }}</td>
                                    <td>{{ $contract->target_data['pilar_2'] ?? '-' }}</td>
                                    <td class="eval-col">Menunggu Evaluasi Akhir</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="fw-bold">Kolaborasi (20%)</td>
                                    <td>{{ $contract->target_data['pilar_3'] ?? '-' }}</td>
                                    <td class="eval-col">Menunggu Evaluasi Akhir</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="fw-bold">Budaya Industri 5R (20%)</td>
                                    <td>{{ $contract->target_data['pilar_4'] ?? '-' }}</td>
                                    <td class="eval-col">Menunggu Evaluasi Akhir</td>
                                </tr>
                            </tbody>
                        </table>
                    @elseif($contract->contract_type == 'jabatan_tambahan')
                        <table class="table-doc">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="65%">Deskripsi Target Pekerjaan (Harus Bisa Diukur)</th>
                                    <th width="30%">Status Evaluasi Akhir Semester</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td>{{ $contract->target_data['target_1'] ?? '-' }}</td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td>{{ $contract->target_data['target_2'] ?? '-' }}</td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td>{{ $contract->target_data['target_3'] ?? '-' }}</td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>
        </div>
        
        <!-- Hasil Evaluasi -->
        @if($contract->evaluations && $contract->evaluations->count() > 0)
        <div class="mt-8">
            <h3 class="text-xl font-bold text-gray-900 mb-4">Hasil Evaluasi Akhir Semester</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($contract->evaluations as $eval)
                    @if($eval->status === 'approved_by_yayasan')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-blue-600 px-4 py-3">
                            <h4 class="text-white font-bold">{{ $eval->semester->name ?? 'Semester' }}</h4>
                            <p class="text-indigo-100 text-xs">{{ $eval->semester->academicYear->name ?? '' }}</p>
                        </div>
                        <div class="p-5">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-sm font-semibold text-gray-500">Nilai Akhir:</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-3xl font-black text-indigo-700">{{ number_format($eval->score, 2) }}</span>
                                    <div class="flex text-yellow-400 text-xs">
                                        @for($i=1; $i<=5; $i++)
                                            <i class="fas fa-star {{ $i <= round($eval->score) ? '' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            
                            <div class="space-y-3 mt-4 border-t border-gray-100 pt-4">
                                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Rincian Penilaian</h5>
                                @foreach($eval->evaluation_data as $key => $score)
                                    @php $displayKey = ucwords(str_replace('_', ' ', $key)); @endphp
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-700 truncate pr-4" title="{{ $displayKey }}">{{ $displayKey }}</span>
                                        <span class="font-bold text-gray-900 bg-gray-100 px-2 py-0.5 rounded">{{ $score }} / 5</span>
                                    </div>
                                @endforeach
                            </div>

                            @if($eval->notes)
                            <div class="mt-4 bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                <p class="text-xs font-bold text-yellow-800 mb-1"><i class="fas fa-comment-dots"></i> Catatan Evaluasi:</p>
                                <p class="text-sm text-yellow-900 italic">"{{ $eval->notes }}"</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
            
            @if($contract->evaluations->where('status', 'approved_by_yayasan')->count() === 0)
                <div class="bg-gray-50 p-4 rounded-xl text-center border border-gray-200">
                    <p class="text-gray-500 text-sm">Evaluasi semester sedang diproses atau belum di-ACC oleh Yayasan.</p>
                </div>
            @endif
        </div>
        @endif
        
    </div>
</div>
@endsection
