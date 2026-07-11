@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>{{ $viewTitle }}</h2>
            <p class="text-muted">Tahun Ajaran Aktif: {{ $currentYear->year ?? '-' }}</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal Pengajuan</th>
                            <th>Nama Guru</th>
                            <th>Unit Sekolah</th>
                            <th>Tipe Kontrak</th>
                            <th>Status Persetujuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contract->created_at->format('d M Y H:i') }}</td>
                            <td><strong>{{ $contract->employee->full_name ?? '-' }}</strong></td>
                            <td>{{ $contract->school->name ?? 'SMK' }}</td>
                            <td>
                                @if($contract->contract_type == 'pkg_kejuruan')
                                    <span class="badge bg-info">Form 2A (Kejuruan)</span>
                                @elseif($contract->contract_type == 'pkg_umum')
                                    <span class="badge bg-info">Form 2B (Umum)</span>
                                @else
                                    <span class="badge bg-warning text-dark">Form 4 (Jabatan: {{ $contract->position->position_name ?? '' }})</span>
                                @endif
                            </td>
                            <td>
                                @if($contract->status == 'submitted_to_kepsek')
                                    <span class="badge bg-warning text-dark">Menunggu Kepsek</span>
                                @elseif($contract->status == 'approved_by_kepsek')
                                    <span class="badge bg-primary">Menunggu Yayasan</span>
                                @elseif($contract->status == 'approved_by_yayasan')
                                    <span class="badge bg-success">ACC Yayasan</span>
                                @elseif($contract->status == 'rejected')
                                    <span class="badge bg-danger">Ditolak</span>
                                @else
                                    <span class="badge bg-secondary">{{ $contract->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.performance_contracts.show', $contract->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-search"></i> Periksa
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada data pengajuan kontrak kinerja.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $contracts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
