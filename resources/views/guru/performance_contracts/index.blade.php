@extends('layouts.guru')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Kontrak Kinerja Saya</h2>
            <a href="{{ route('guru.performance_contracts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Kontrak Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Tahun Ajaran</th>
                            <th>Tipe Kontrak</th>
                            <th>Jabatan (Jika Form 4)</th>
                            <th>Status Persetujuan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                        <tr>
                            <td>{{ $contract->academicYear->year }} (Smt {{ $contract->academicYear->semester }})</td>
                            <td>
                                @if($contract->contract_type == 'pkg_kejuruan')
                                    <span class="badge bg-info">Form 2A (Kejuruan)</span>
                                @elseif($contract->contract_type == 'pkg_umum')
                                    <span class="badge bg-info">Form 2B (Umum)</span>
                                @else
                                    <span class="badge bg-warning text-dark">Form 4 (Jabatan)</span>
                                @endif
                            </td>
                            <td>
                                {{ $contract->position ? $contract->position->position_name : '-' }}
                            </td>
                            <td>
                                @if($contract->status == 'draft')
                                    <span class="badge bg-secondary">Draft</span>
                                @elseif($contract->status == 'submitted_to_kepsek')
                                    <div class="d-flex align-items-center" style="gap: 5px; font-size: 0.85rem;">
                                        <span class="badge bg-primary">1. Diajukan</span> <i class="fas fa-arrow-right text-muted" style="font-size: 10px;"></i>
                                        <span class="badge bg-light text-secondary border">2. Kepsek</span> <i class="fas fa-arrow-right text-muted" style="font-size: 10px;"></i>
                                        <span class="badge bg-light text-secondary border">3. Yayasan</span>
                                    </div>
                                @elseif($contract->status == 'approved_by_kepsek')
                                    <div class="d-flex align-items-center" style="gap: 5px; font-size: 0.85rem;">
                                        <span class="badge bg-success">1. Diajukan</span> <i class="fas fa-arrow-right text-muted" style="font-size: 10px;"></i>
                                        <span class="badge bg-primary">2. Kepsek</span> <i class="fas fa-arrow-right text-muted" style="font-size: 10px;"></i>
                                        <span class="badge bg-light text-secondary border">3. Yayasan</span>
                                    </div>
                                @elseif($contract->status == 'approved_by_yayasan')
                                    <div class="d-flex align-items-center" style="gap: 5px; font-size: 0.85rem;">
                                        <span class="badge bg-success">1. Diajukan</span> <i class="fas fa-arrow-right text-success" style="font-size: 10px;"></i>
                                        <span class="badge bg-success">2. Kepsek</span> <i class="fas fa-arrow-right text-success" style="font-size: 10px;"></i>
                                        <span class="badge bg-success">3. Yayasan</span>
                                    </div>
                                @elseif($contract->status == 'rejected')
                                    <span class="badge bg-danger">Ditolak / Dikembalikan</span>
                                    <small class="d-block text-danger mt-1">Catatan: {{ $contract->notes }}</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('guru.performance_contracts.show', $contract->id) }}" class="btn btn-sm btn-outline-secondary mb-1">Lihat Detail</a>
                                
                                @if(in_array($contract->status, ['draft', 'submitted_to_kepsek', 'rejected']))
                                    <a href="{{ route('guru.performance_contracts.edit', $contract->id) }}" class="btn btn-sm btn-outline-primary mb-1">Edit</a>
                                    <form action="{{ route('guru.performance_contracts.destroy', $contract->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger mb-1">Hapus</button>
                                    </form>
                                @endif

                                @if($contract->status == 'approved_by_yayasan')
                                    <!-- Bar Cetak Kontrak Muncul di Sini -->
                                    <a href="{{ route('guru.performance_contracts.print', $contract->id) }}" target="_blank" class="btn btn-sm btn-success fw-bold mb-1">
                                        <i class="fas fa-print"></i> Cetak Pakta Integritas
                                    </a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Anda belum membuat Kontrak Kinerja untuk Tahun Ajaran ini.<br>
                                <a href="{{ route('guru.performance_contracts.create') }}" class="btn btn-sm btn-primary mt-2">Buat Sekarang</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
