@extends('layouts.app')

@section('content')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 1.5rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0,0,0,0.03);
        transition: transform 0.3s ease;
    }
    .gradient-header {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 800;
    }
    .form-control, .form-select {
        border-radius: 0.75rem;
        padding: 0.75rem 1.25rem;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        background-color: #fff;
    }
    .alert-custom {
        border-radius: 1rem;
        border-left: 5px solid;
    }
    .alert-kejuruan {
        background-color: #fff1f2;
        border-left-color: #e11d48;
        color: #9f1239;
    }
    .alert-umum {
        background-color: #eff6ff;
        border-left-color: #3b82f6;
        color: #1e3a8a;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);
        color: white;
        border: none;
        border-radius: 0.75rem;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        color: white;
    }
    .check-card {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }
    .check-card:hover {
        border-color: #6366f1;
        background: #eff6ff;
    }
    .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            
            <div class="d-flex align-items-center mb-4 gap-3">
                <div class="bg-white p-3 rounded-circle shadow-sm">
                    <i class="fas fa-file-signature fs-4 text-primary"></i>
                </div>
                <div>
                    <h2 class="mb-0 gradient-header">Formulir Kontrak Kinerja</h2>
                    <p class="text-muted mb-0">Tahun Ajaran Aktif: <strong>{{ $currentYear->year }} (Semester {{ $currentYear->semester }})</strong></p>
                </div>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-custom mb-4">{{ session('error') }}</div>
            @endif

            <div class="card glass-card">
                <div class="card-body p-4 p-md-5">
                    <form action="{{ route('guru.performance_contracts.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-5">
                            <label class="form-label fw-bold text-dark mb-3"><i class="fas fa-layer-group text-primary me-2"></i>Pilih Tipe Instrumen Kontrak</label>
                            <select class="form-select form-select-lg fs-6" name="contract_type" id="contractType" required onchange="toggleForm()">
                                <option value="">-- Silakan Pilih Tipe Kontrak --</option>
                                <option value="pkg_kejuruan">Form 2A - PKG Guru Kejuruan / Produktif (Fokus TEFA)</option>
                                <option value="pkg_umum">Form 2B - PKG Guru Mapel Umum (Kolaborasi & Logika)</option>
                                <option value="jabatan_tambahan">Form 4 - Kontrak Kinerja Jabatan Tambahan</option>
                            </select>
                        </div>

                        <!-- Form untuk Jabatan Tambahan -->
                        <div class="mb-5 fade-in" id="positionSelectGroup" style="display: none;">
                            <label class="form-label fw-bold text-dark mb-3"><i class="fas fa-user-tie text-primary me-2"></i>Pilih Jabatan yang Diemban</label>
                            <select class="form-select form-select-lg fs-6" name="position_id" id="positionId">
                                <option value="">-- Pilih Jabatan --</option>
                                @foreach($positions as $pos)
                                    <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-2 d-block"><i class="fas fa-info-circle me-1"></i>Pilih jabatan yang target kinerjanya akan Anda pertanggungjawabkan.</small>
                        </div>

                        <!-- Bagian Input Komitmen/Target -->
                        <div class="mb-4" id="targetSection" style="display: none;">
                            
                            <div class="d-flex align-items-center mb-4">
                                <h5 class="fw-bold mb-0 text-dark" id="targetTitle">Target & Komitmen Kinerja</h5>
                                <div class="ms-auto">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">Wajib Diisi</span>
                                </div>
                            </div>
                            
                            <div id="targetKejuruan" style="display: none;">
                                <div class="alert alert-custom alert-kejuruan p-3 mb-4">
                                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Sesuai Evaluasi:</strong> Jangan takut alat rusak. Siswa SMK <strong>wajib</strong> praktik sesuai standar industri (TEFA).
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary">Target Nilai Omzet / Jumlah Layanan TEFA Semester Ini</label>
                                    <input type="text" class="form-control form-control-lg fs-6" name="target_data[tefa_target]" placeholder="Misal: Rp 5.000.000 atau 50 Layanan Servis">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary">SOP & Budaya Industri (5R) yang akan ditegakkan di Bengkel</label>
                                    <textarea class="form-control form-control-lg fs-6" name="target_data[sop_commitment]" rows="3" placeholder="Sebutkan langkah tegas untuk menegakkan kedisiplinan dan keamanan praktik..."></textarea>
                                </div>
                            </div>

                            <div id="targetUmum" style="display: none;">
                                <div class="alert alert-custom alert-umum p-3 mb-4">
                                    <i class="fas fa-info-circle me-2"></i> <strong>Fokus:</strong> Menghilangkan dikotomi mapel. Selalu kaitkan materi pembelajaran Anda dengan nalar kejuruan/industri.
                                </div>
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary">Rencana Materi Pembelajaran Berbasis Proyek (PBL) Terkait Kejuruan</label>
                                    <textarea class="form-control form-control-lg fs-6" name="target_data[pbl_plan]" rows="4" placeholder="Deskripsikan bagaimana mapel umum Anda melatih logika untuk memecahkan masalah produktif kejuruan..."></textarea>
                                </div>
                            </div>

                            <div id="targetJabatan" style="display: none;">
                                <div class="mb-4">
                                    <label class="form-label fw-semibold text-secondary">Tuliskan 3 Target Output Riil Jabatan Anda Semester Ini</label>
                                    <textarea class="form-control form-control-lg fs-6" name="target_data[jabatan_targets]" rows="5" placeholder="1. ...&#10;2. ...&#10;3. ..."></textarea>
                                </div>
                            </div>

                            <!-- Pakta Integritas Checkbox -->
                            <div class="check-card p-4 mt-5">
                                <div class="form-check d-flex gap-3 align-items-center">
                                    <input class="form-check-input" type="checkbox" value="1" id="agreeCheck" required style="width: 1.5rem; height: 1.5rem;">
                                    <label class="form-check-label text-dark" for="agreeCheck" style="line-height: 1.6;">
                                        <strong>Saya bersedia memenuhi target di atas.</strong><br>
                                        <span class="text-muted">Apabila gagal mencapai komitmen ini, saya siap menerima evaluasi hingga pencabutan penugasan mengajar/jabatan sesuai kebijakan Kepala Sekolah dan Yayasan.</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-5 pt-3 border-top" id="actionButtons" style="display: none !important;">
                            <a href="{{ route('guru.performance_contracts.index') }}" class="btn btn-light px-4 py-2 me-3 rounded-3 text-muted fw-semibold">Batal</a>
                            <button type="submit" class="btn-gradient" id="btnSubmit">
                                <i class="fas fa-paper-plane me-2"></i> Ajukan Kontrak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleForm() {
    const type = document.getElementById('contractType').value;
    const targetSection = document.getElementById('targetSection');
    const posGroup = document.getElementById('positionSelectGroup');
    const actionBtns = document.getElementById('actionButtons');

    document.getElementById('targetKejuruan').style.display = 'none';
    document.getElementById('targetUmum').style.display = 'none';
    document.getElementById('targetJabatan').style.display = 'none';
    
    posGroup.style.display = 'none';
    document.getElementById('positionId').required = false;

    if (type) {
        // Simple animation logic
        targetSection.style.opacity = 0;
        targetSection.style.display = 'block';
        actionBtns.style.setProperty('display', 'flex', 'important');
        
        setTimeout(() => {
            targetSection.style.transition = 'opacity 0.4s ease';
            targetSection.style.opacity = 1;
        }, 50);

        if (type === 'pkg_kejuruan') {
            document.getElementById('targetTitle').innerHTML = '<i class="fas fa-bullseye text-danger me-2"></i> Target Kinerja Kejuruan (TEFA)';
            document.getElementById('targetKejuruan').style.display = 'block';
        } else if (type === 'pkg_umum') {
            document.getElementById('targetTitle').innerHTML = '<i class="fas fa-lightbulb text-primary me-2"></i> Target Kinerja Mapel Umum';
            document.getElementById('targetUmum').style.display = 'block';
        } else if (type === 'jabatan_tambahan') {
            document.getElementById('targetTitle').innerHTML = '<i class="fas fa-award text-warning me-2"></i> Target Kinerja Jabatan';
            document.getElementById('targetJabatan').style.display = 'block';
            posGroup.style.display = 'block';
            document.getElementById('positionId').required = true;
        }
    } else {
        targetSection.style.display = 'none';
        actionBtns.style.setProperty('display', 'none', 'important');
    }
}
</script>
@endsection
