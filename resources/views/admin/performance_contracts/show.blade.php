@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Pemeriksaan Kontrak Kinerja</h2>
            <a href="{{ route('admin.performance_contracts.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Pegawai -->
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Data Pegawai</div>
                <div class="card-body">
                    <p class="mb-1"><strong>Nama:</strong> {{ $contract->employee->full_name }}</p>
                    <p class="mb-1"><strong>NIP:</strong> {{ $contract->employee->nip ?? '-' }}</p>
                    <p class="mb-1"><strong>Unit:</strong> {{ $contract->school->name ?? '-' }}</p>
                    <hr>
                    <p class="mb-1"><strong>Tipe Kontrak:</strong> 
                        @if($contract->contract_type == 'pkg_kejuruan') Form 2A (Kejuruan)
                        @elseif($contract->contract_type == 'pkg_umum') Form 2B (Mapel Umum)
                        @else Form 4 (Jabatan)
                        @endif
                    </p>
                    @if($contract->contract_type == 'jabatan_tambahan')
                        <p class="mb-1"><strong>Jabatan:</strong> {{ $contract->position->position_name ?? '-' }}</p>
                    @endif
                    <p class="mb-0 mt-3">
                        <strong>Status Saat Ini:</strong>
                        @if($contract->status == 'submitted_to_kepsek') <span class="badge bg-warning text-dark">Menunggu Kepsek</span>
                        @elseif($contract->status == 'approved_by_kepsek') <span class="badge bg-primary">Menunggu Yayasan</span>
                        @elseif($contract->status == 'approved_by_yayasan') <span class="badge bg-success">ACC Final</span>
                        @else <span class="badge bg-secondary">{{ $contract->status }}</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Detail Target -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-bold">Rincian Komitmen / Target Kinerja</div>
                <div class="card-body">
                    
                    @if($contract->contract_type == 'pkg_kejuruan')
                        <div class="mb-3 border-bottom pb-2">
                            <h6>1. Target Omzet / Layanan TEFA</h6>
                            <p class="text-muted fs-5">{{ $contract->target_data['tefa_target'] ?? '-' }}</p>
                        </div>
                        <div class="mb-3 border-bottom pb-2">
                            <h6>2. Komitmen Penegakan Budaya Industri & SOP (5R)</h6>
                            <p class="text-muted">{{ $contract->target_data['sop_commitment'] ?? '-' }}</p>
                        </div>
                    @elseif($contract->contract_type == 'pkg_umum')
                        <div class="mb-3 border-bottom pb-2">
                            <h6>Rencana Pembelajaran Berbasis Proyek (PBL) Kejuruan</h6>
                            <p class="text-muted">{{ $contract->target_data['pbl_plan'] ?? '-' }}</p>
                        </div>
                    @else
                        <div class="mb-3 border-bottom pb-2">
                            <h6>Target Output Riil Jabatan</h6>
                            <p class="text-muted" style="white-space: pre-line;">{{ $contract->target_data['jabatan_targets'] ?? '-' }}</p>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Area Approval (Hanya muncul jika belum di-ACC oleh pihak terkait) -->
            @php
                $user = auth()->user();
                $canApprove = false;
                
                if ($user->isSuperAdmin() && $contract->status == 'approved_by_kepsek') {
                    $canApprove = true; // Yayasan memproses setelah Kepsek
                } elseif (!$user->isSuperAdmin() && $contract->status == 'submitted_to_kepsek') {
                    $canApprove = true; // Kepsek memproses yang baru masuk
                }
            @endphp

            @if($canApprove)
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white fw-bold">
                    Tindakan Persetujuan
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.performance_contracts.process', $contract->id) }}" method="POST">
                        @csrf
                        <div class="mb-3" id="rejectNotesContainer" style="display: none;">
                            <label class="form-label text-danger fw-bold">Catatan Penolakan (Wajib jika ditolak)</label>
                            <textarea name="notes" id="rejectNotes" class="form-control" rows="3" placeholder="Sebutkan alasan penolakan agar guru memperbaiki komitmennya..."></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-danger" onclick="showReject()">Tolak & Kembalikan</button>
                            <button type="submit" name="action" value="approve" class="btn btn-success fw-bold px-4">
                                <i class="fas fa-check-circle"></i> Setujui Kontrak Ini
                            </button>
                        </div>
                        
                        <!-- Hidden submit for reject -->
                        <button type="submit" name="action" value="reject" id="btnRealReject" style="display: none;">Proses Tolak</button>
                    </form>
                </div>
            </div>
            @endif

        </div>
    </div>
</div>

<script>
    function showReject() {
        document.getElementById('rejectNotesContainer').style.display = 'block';
        document.getElementById('rejectNotes').required = true;
        
        // Ganti fungsi tombol merah
        const rejectBtn = document.querySelector('.btn-outline-danger');
        rejectBtn.innerText = "Konfirmasi Tolak";
        rejectBtn.classList.remove('btn-outline-danger');
        rejectBtn.classList.add('btn-danger');
        rejectBtn.onclick = function() {
            document.getElementById('btnRealReject').click();
        };
    }
</script>
@endsection
