@extends('layouts.app')

@section('content')
<style>
    .document-card {
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e2e8f0;
    }
    .doc-header {
        border-bottom: 3px solid #1e293b;
        padding-bottom: 1rem;
        margin-bottom: 2rem;
    }
    .doc-title {
        font-weight: 800;
        color: #1e293b;
        letter-spacing: 0.5px;
        line-height: 1.4;
    }
    .doc-subtitle {
        color: #64748b;
        font-size: 0.9rem;
        letter-spacing: 1px;
    }
    .alert-doc {
        background: #f8fafc;
        border-left: 5px solid;
        padding: 1.25rem;
        border-radius: 0.25rem;
        margin-bottom: 2rem;
    }
    .alert-kejuruan { border-left-color: #f59e0b; }
    .alert-umum { border-left-color: #3b82f6; }
    .alert-jabatan { border-left-color: #10b981; }
    
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
    .table-doc td textarea {
        width: 100%;
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        padding: 0.75rem;
        resize: vertical;
        min-height: 80px;
    }
    .table-doc td textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
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
            
            <div class="mb-4">
                <label class="form-label fw-bold text-dark mb-2">Pilih Tipe Instrumen Kontrak yang Akan Diisi:</label>
                <select class="form-select form-select-lg shadow-sm" name="contract_type" id="contractType" onchange="toggleForm()">
                    <option value="">-- Silakan Pilih Tipe Kontrak --</option>
                    <option value="pkg_kejuruan">Form 2A - PKG Guru Kejuruan / Produktif</option>
                    <option value="pkg_umum">Form 2B - PKG Guru Mapel Umum</option>
                    <option value="jabatan_tambahan">Form 4 - Kontrak Kinerja Jabatan</option>
                </select>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('guru.performance_contracts.store') }}" method="POST" id="contractForm" style="display: none;">
                @csrf
                <input type="hidden" name="contract_type" id="hiddenContractType">
                
                <div class="document-card p-4 p-md-5">
                    
                    <!-- Header Dokumen -->
                    <div class="text-center doc-header">
                        <h3 class="doc-title text-uppercase" id="docTitle">INSTRUMEN</h3>
                        <p class="doc-subtitle text-uppercase mt-2">SMKS PEMBDA NIAS - TAHUN PELAJARAN {{ $currentYear->year }}</p>
                    </div>

                    <!-- Alert Aturan -->
                    <div id="alertKejuruan" class="alert-doc alert-kejuruan" style="display:none;">
                        <h6 class="text-warning fw-bold mb-2" style="color: #d97706 !important;">Aturan Ketat SK {{ $currentYear->year }}</h6>
                        <p class="mb-0">Syarat Guru dapat dipertahankan/diusulkan SK Yayasan adalah meraih SKOR RATA-RATA MINIMAL > 3.5 dari 4 Pilar di bawah ini.</p>
                    </div>
                    
                    <div id="alertUmum" class="alert-doc alert-umum" style="display:none;">
                        <h6 class="text-primary fw-bold mb-2">Aturan Khusus Mapel Umum</h6>
                        <p class="mb-0">Guru Agama, PPKn, Bahasa, Matematika, dll <strong>TIDAK BOLEH</strong> beralasan mapelnya hanya teori. Mereka dinilai dari seberapa relevan mapel mereka diaplikasikan ke praktik kejuruan siswa.</p>
                    </div>

                    <!-- Info Personal -->
                    <div class="mb-4">
                        <div class="info-row" id="rowNama">
                            <div class="info-label" id="labelNama">Nama Guru Dinilai</div>
                            <div class="info-value text-primary fw-semibold">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="info-row" id="rowJabatan" style="display:none;">
                            <div class="info-label">Jabatan Tambahan</div>
                            <div class="info-value">
                                <select name="position_id" id="positionId" class="form-select form-select-sm border-0 bg-transparent text-primary fw-semibold p-0" style="box-shadow: none;">
                                    <option value="">-- Pilih Jabatan Anda --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}">{{ $pos->position_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Form 2A & 2B -->
                    <div id="tablePkg" style="display:none;">
                        <table class="table-doc">
                            <thead>
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="35%">Pilar Penilaian Kinerja</th>
                                    <th width="45%">Rencana Bukti Fisik Nyata (Target)</th>
                                    <th width="15%">Skor (1-5)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-center">1</td>
                                    <td class="fw-bold" id="pilar1_title">Kompetensi Praktik (30%)</td>
                                    <td><textarea name="target_data[pilar_1]" placeholder="Tuliskan target bukti fisik..." required id="pilar1_input"></textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="fw-bold" id="pilar2_title">Kontribusi Program (30%)</td>
                                    <td><textarea name="target_data[pilar_2]" placeholder="Tuliskan target bukti fisik..." required id="pilar2_input"></textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="fw-bold">Kolaborasi (20%)</td>
                                    <td><textarea name="target_data[pilar_3]" placeholder="Tuliskan target kolaborasi..." required id="pilar3_input"></textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="fw-bold">Budaya Industri 5R (20%)</td>
                                    <td><textarea name="target_data[pilar_4]" placeholder="Tuliskan komitmen budaya 5R..." required id="pilar4_input"></textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Form 4 -->
                    <div id="tableJabatan" style="display:none;">
                        <p class="fw-bold mb-2">Target Output Riil Semester Ganjil:</p>
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
                                    <td><textarea name="target_data[target_1]" placeholder="Target terukur pertama..." required id="jabatan1_input"></textarea></td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td><textarea name="target_data[target_2]" placeholder="Target terukur kedua..." required id="jabatan2_input"></textarea></td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td><textarea name="target_data[target_3]" placeholder="Target terukur ketiga..." id="jabatan3_input"></textarea></td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-end mt-5 pt-4 border-top">
                        <a href="{{ route('guru.performance_contracts.index') }}" class="btn btn-light px-4 py-2 me-3 rounded-3 text-muted fw-semibold">Batal</a>
                        <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-bold shadow-sm">
                            <i class="fas fa-check-circle me-2"></i> Ajukan Target Kinerja
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
function toggleForm() {
    const type = document.getElementById('contractType').value;
    const form = document.getElementById('contractForm');
    
    // Hide all
    document.getElementById('alertKejuruan').style.display = 'none';
    document.getElementById('alertUmum').style.display = 'none';
    document.getElementById('tablePkg').style.display = 'none';
    document.getElementById('tableJabatan').style.display = 'none';
    document.getElementById('rowJabatan').style.display = 'none';
    
    // Reset required attrs
    document.getElementById('positionId').required = false;
    for(let i=1; i<=4; i++) {
        let el = document.getElementById('pilar'+i+'_input');
        if(el) { el.required = false; el.disabled = true; }
    }
    for(let i=1; i<=3; i++) {
        let el = document.getElementById('jabatan'+i+'_input');
        if(el) { el.required = false; el.disabled = true; }
    }

    if (type) {
        form.style.display = 'block';
        document.getElementById('hiddenContractType').value = type;

        if (type === 'pkg_kejuruan' || type === 'pkg_umum') {
            document.getElementById('tablePkg').style.display = 'block';
            document.getElementById('labelNama').innerText = 'Nama Guru Dinilai';
            
            // Enable inputs
            for(let i=1; i<=4; i++) {
                document.getElementById('pilar'+i+'_input').disabled = false;
                document.getElementById('pilar'+i+'_input').required = true;
            }

            if (type === 'pkg_kejuruan') {
                document.getElementById('docTitle').innerHTML = 'INSTRUMEN #2A: PENILAIAN KINERJA GURU<br>(PRODUKTIF/KEJURUAN)';
                document.getElementById('alertKejuruan').style.display = 'block';
                document.getElementById('pilar1_title').innerText = 'Kompetensi Praktik (30%)';
                document.getElementById('pilar2_title').innerText = 'Kontribusi Program (30%)';
            } else {
                document.getElementById('docTitle').innerHTML = 'INSTRUMEN #2B: PENILAIAN KINERJA GURU<br>(MAPEL UMUM)';
                document.getElementById('alertUmum').style.display = 'block';
                document.getElementById('pilar1_title').innerText = 'Kompetensi Relevansi Praktik (30%)';
                document.getElementById('pilar2_title').innerText = 'Kontribusi Program/TEFA (30%)';
            }
        } 
        else if (type === 'jabatan_tambahan') {
            document.getElementById('docTitle').innerHTML = 'INSTRUMEN #4: KONTRAK KINERJA JABATAN';
            document.getElementById('tableJabatan').style.display = 'block';
            document.getElementById('rowJabatan').style.display = 'flex';
            document.getElementById('labelNama').innerText = 'Nama Pejabat';
            document.getElementById('positionId').required = true;
            
            document.getElementById('jabatan1_input').disabled = false;
            document.getElementById('jabatan1_input').required = true;
            document.getElementById('jabatan2_input').disabled = false;
            document.getElementById('jabatan2_input').required = true;
            document.getElementById('jabatan3_input').disabled = false;
        }
    } else {
        form.style.display = 'none';
    }
}
</script>
@endsection
