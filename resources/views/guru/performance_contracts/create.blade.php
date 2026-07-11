@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Buat Kontrak Kinerja Baru</h2>
            <p class="text-muted">Tahun Ajaran Aktif: {{ $currentYear->year }} (Semester {{ $currentYear->semester }})</p>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{ route('guru.performance_contracts.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-bold">Pilih Tipe Instrumen Kontrak</label>
                    <select class="form-select" name="contract_type" id="contractType" required onchange="toggleForm()">
                        <option value="">-- Pilih Tipe --</option>
                        <option value="pkg_kejuruan">Form 2A - PKG Guru Kejuruan / Produktif (TEFA)</option>
                        <option value="pkg_umum">Form 2B - PKG Guru Mapel Umum (Kolaborasi Logika)</option>
                        <option value="jabatan_tambahan">Form 4 - Kontrak Kinerja Jabatan Tambahan (Kajur, Kepala Bengkel, dll)</option>
                    </select>
                </div>

                <!-- Form untuk Jabatan Tambahan -->
                <div class="mb-4" id="positionSelectGroup" style="display: none;">
                    <label class="form-label fw-bold">Pilih Jabatan yang Diemban</label>
                    <select class="form-select" name="position_id" id="positionId">
                        <option value="">-- Pilih Jabatan --</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilih jabatan yang akan Anda setujui target kinerjanya.</small>
                </div>

                <!-- Bagian Input Komitmen/Target -->
                <div class="mb-4" id="targetSection" style="display: none;">
                    <h5 class="fw-bold border-bottom pb-2" id="targetTitle">Target & Komitmen Kinerja</h5>
                    
                    <div id="targetKejuruan" style="display: none;">
                        <p class="text-danger fw-bold"><i class="fas fa-exclamation-circle"></i> Sesuai Evaluasi: Jangan takut alat rusak. Siswa wajib praktik standar industri.</p>
                        <div class="mb-3">
                            <label class="form-label">Target Nilai Omzet / Jumlah Layanan TEFA Semester Ini</label>
                            <input type="text" class="form-control" name="target_data[tefa_target]" placeholder="Misal: Rp 5.000.000 atau 50 Layanan Servis">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SOP & Budaya Industri (5R) yang akan ditegakkan di Bengkel</label>
                            <textarea class="form-control" name="target_data[sop_commitment]" rows="3" placeholder="Sebutkan langkah tegas untuk menegakkan kedisiplinan..."></textarea>
                        </div>
                    </div>

                    <div id="targetUmum" style="display: none;">
                        <p class="text-primary fw-bold"><i class="fas fa-info-circle"></i> Fokus: Menghilangkan Dikotomi Mapel. Kaitkan materi dengan nalar kejuruan.</p>
                        <div class="mb-3">
                            <label class="form-label">Rencana Materi Pembelajaran Berbasis Proyek (PBL) yang Berkaitan dengan Kejuruan</label>
                            <textarea class="form-control" name="target_data[pbl_plan]" rows="3" placeholder="Deskripsikan bagaimana mapel umum Anda melatih logika untuk memecahkan masalah kejuruan..."></textarea>
                        </div>
                    </div>

                    <div id="targetJabatan" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Tuliskan 3 Target Output Riil Jabatan Anda Semester Ini</label>
                            <textarea class="form-control" name="target_data[jabatan_targets]" rows="4" placeholder="1. ...&#10;2. ...&#10;3. ..."></textarea>
                        </div>
                    </div>

                    <!-- Pakta Integritas Checkbox -->
                    <div class="form-check mt-4 border p-3 rounded bg-light">
                        <input class="form-check-input" type="checkbox" value="1" id="agreeCheck" required>
                        <label class="form-check-label fw-bold" for="agreeCheck">
                            Saya bersedia memenuhi target di atas dan siap dicabut penugasan mengajarnya/jabatannya jika gagal mencapai komitmen ini sesuai kebijakan Kepala Sekolah dan Yayasan.
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <a href="{{ route('guru.performance_contracts.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button type="submit" class="btn btn-primary" id="btnSubmit" style="display: none;">Ajukan Kontrak ke Kepala Sekolah</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleForm() {
    const type = document.getElementById('contractType').value;
    const targetSection = document.getElementById('targetSection');
    const posGroup = document.getElementById('positionSelectGroup');
    const btnSubmit = document.getElementById('btnSubmit');

    document.getElementById('targetKejuruan').style.display = 'none';
    document.getElementById('targetUmum').style.display = 'none';
    document.getElementById('targetJabatan').style.display = 'none';
    
    posGroup.style.display = 'none';
    document.getElementById('positionId').required = false;

    if (type) {
        targetSection.style.display = 'block';
        btnSubmit.style.display = 'inline-block';

        if (type === 'pkg_kejuruan') {
            document.getElementById('targetTitle').innerText = 'Target Kinerja Guru Kejuruan (TEFA & Praktik)';
            document.getElementById('targetKejuruan').style.display = 'block';
        } else if (type === 'pkg_umum') {
            document.getElementById('targetTitle').innerText = 'Target Kinerja Guru Umum (Kolaborasi Logika)';
            document.getElementById('targetUmum').style.display = 'block';
        } else if (type === 'jabatan_tambahan') {
            document.getElementById('targetTitle').innerText = 'Target Kinerja Jabatan (Form 4)';
            document.getElementById('targetJabatan').style.display = 'block';
            posGroup.style.display = 'block';
            document.getElementById('positionId').required = true;
        }
    } else {
        targetSection.style.display = 'none';
        btnSubmit.style.display = 'none';
    }
}
</script>
@endsection
