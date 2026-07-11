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
                    <option value="pkg_kejuruan" {{ $contract->contract_type == 'pkg_kejuruan' ? 'selected' : '' }}>Form 2A - PKG Guru Kejuruan / Produktif</option>
                    <option value="pkg_umum" {{ $contract->contract_type == 'pkg_umum' ? 'selected' : '' }}>Form 2B - PKG Guru Mapel Umum</option>
                    <option value="jabatan_tambahan" {{ $contract->contract_type == 'jabatan_tambahan' ? 'selected' : '' }}>Form 4 - Perjanjian Kinerja Jabatan</option>
                </select>
            </div>

            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($contract->status == 'rejected' && $contract->notes)
                <div class="alert alert-danger mb-4 shadow-sm">
                    <strong><i class="fas fa-exclamation-circle me-1"></i> Catatan Penolakan Kepala Sekolah:</strong><br>
                    {{ $contract->notes }}
                    <hr>
                    <small>Silakan revisi form di bawah ini sesuai arahan di atas, lalu ajukan ulang.</small>
                </div>
            @endif

            <form action="{{ route('guru.performance_contracts.update', $contract->id) }}" method="POST" id="contractForm" style="display: none;">
                @csrf
                @method('PUT')
                <input type="hidden" name="contract_type" id="hiddenContractType">
                
                <div class="document-card p-4 p-md-5">
                    
                    <!-- Header Dokumen -->
                    <div class="text-center doc-header">
                        <h3 class="doc-title text-uppercase" id="docTitle">INSTRUMEN</h3>
                        <p class="doc-subtitle text-uppercase mt-2">SMKS PEMBDA NIAS - TAHUN PELAJARAN {{ $currentYear->year }}</p>
                    </div>

                    <!-- Alert Aturan -->
                    <div id="alertKejuruan" class="alert-doc alert-kejuruan" style="display:none;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-exclamation-triangle fs-3 text-warning" style="color: #d97706 !important;"></i>
                            <div>
                                <h6 class="text-warning fw-bold mb-1" style="color: #d97706 !important;">Aturan Ketat SK {{ $currentYear->year }}</h6>
                                <p class="mb-0 text-dark">Syarat Guru dapat dipertahankan/diusulkan SK Yayasan adalah meraih SKOR RATA-RATA MINIMAL > 3.5 dari 4 Pilar di bawah ini.</p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="alertUmum" class="alert-doc alert-umum" style="display:none;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="fas fa-info-circle fs-3 text-primary"></i>
                            <div>
                                <h6 class="text-primary fw-bold mb-1">Aturan Khusus Mapel Umum</h6>
                                <p class="mb-0 text-dark">Guru Agama, PPKn, Bahasa, Matematika, dll <strong>TIDAK BOLEH</strong> beralasan mapelnya hanya teori. Mereka dinilai dari seberapa relevan mapel mereka diaplikasikan ke praktik kejuruan siswa.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pernyataan Resmi & Info Personal -->
                    <div class="mb-5 pb-3 border-bottom">
                        <p class="mb-3 text-dark text-justify" style="line-height: 1.6; font-size: 1.05rem;">
                            Yang bertanda tangan di bawah ini, saya selaku <strong>Pihak yang menyatakan berjanji</strong>:
                        </p>
                        
                        <div class="info-row" id="rowNama">
                            <div class="info-label" id="labelNama">Nama Lengkap</div>
                            <div class="info-value text-dark fw-bold">{{ Auth::user()->name }}</div>
                        </div>
                        <div class="info-row" id="rowJabatan" style="display:none;">
                            <div class="info-label">Jabatan Tambahan</div>
                            <div class="info-value">
                                <select name="position_id" id="positionId" class="form-select form-select-sm border-0 bg-transparent text-dark fw-bold p-0" style="box-shadow: none;">
                                    <option value="">-- Silakan Pilih Jabatan Anda --</option>
                                    @foreach($positions as $pos)
                                        <option value="{{ $pos->id }}" {{ $contract->position_id == $pos->id ? 'selected' : '' }}>{{ $pos->position_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <p class="mt-4 text-dark text-justify" style="line-height: 1.6; font-size: 1.05rem;" id="statementTeks">
                            Dengan ini menyatakan <strong>KOMITMEN DAN KESANGGUPAN PENUH</strong> untuk melaksanakan serta mencapai target kinerja riil pada Tahun Pelajaran {{ $currentYear->year }}, sebagaimana tertuang secara rinci pada tabel bukti fisik nyata berikut ini:
                        </p>
                    </div>

                    <!-- Tabel Form 2A & 2B -->
                    <div id="tablePkg" style="display:none;" class="table-responsive">
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
                                    <td class="fw-bold" id="pilar1_title">Kompetensi Praktik (30%)</td>
                                    <td><textarea name="target_data[pilar_1]" placeholder="Uraikan rencana konkret / pencapaian bukti fisik nyata..." required id="pilar1_input">{{ old('target_data.pilar_1', $contract->target_data['pilar_1'] ?? '') }}</textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td class="fw-bold" id="pilar2_title">Kontribusi Program (30%)</td>
                                    <td><textarea name="target_data[pilar_2]" placeholder="Uraikan target kontribusi secara spesifik..." required id="pilar2_input">{{ old('target_data.pilar_2', $contract->target_data['pilar_2'] ?? '') }}</textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td class="fw-bold">Kolaborasi (20%)</td>
                                    <td><textarea name="target_data[pilar_3]" placeholder="Jelaskan bentuk kolaborasi lintas mata pelajaran/unit yang akan dilakukan..." required id="pilar3_input">{{ old('target_data.pilar_3', $contract->target_data['pilar_3'] ?? '') }}</textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                                <tr>
                                    <td class="text-center">4</td>
                                    <td class="fw-bold">Budaya Industri 5R (20%)</td>
                                    <td><textarea name="target_data[pilar_4]" placeholder="Sebutkan langkah tegas penegakan SOP K3 dan Budaya 5R..." required id="pilar4_input">{{ old('target_data.pilar_4', $contract->target_data['pilar_4'] ?? '') }}</textarea></td>
                                    <td class="eval-col">Dievaluasi<br>Akhir Sem.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tabel Form 4 -->
                    <div id="tableJabatan" style="display:none;" class="table-responsive">
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
                                    <td><textarea name="target_data[target_1]" placeholder="Sebutkan sasaran output pekerjaan riil pertama (contoh: Mengadakan 1x Job Fair)..." required id="jabatan1_input">{{ old('target_data.target_1', $contract->target_data['target_1'] ?? '') }}</textarea></td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                                <tr>
                                    <td class="text-center">2</td>
                                    <td><textarea name="target_data[target_2]" placeholder="Sebutkan sasaran output pekerjaan riil kedua (contoh: Memastikan 15 Alumni terserap)..." required id="jabatan2_input">{{ old('target_data.target_2', $contract->target_data['target_2'] ?? '') }}</textarea></td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                                <tr>
                                    <td class="text-center">3</td>
                                    <td><textarea name="target_data[target_3]" placeholder="Sebutkan sasaran output pekerjaan riil ketiga (opsional)..." id="jabatan3_input">{{ old('target_data.target_3', $contract->target_data['target_3'] ?? '') }}</textarea></td>
                                    <td class="eval-col">Menunggu Evaluasi</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pakta Integritas / Agreement -->
                    <div class="mt-5 p-4 bg-light border border-secondary rounded" style="border-width: 2px !important; border-style: dashed !important;">
                        <h6 class="fw-bold mb-3 text-dark text-center"><i class="fas fa-file-signature text-secondary me-2"></i> PERNYATAAN & PAKTA INTEGRITAS</h6>
                        <div class="form-check d-flex align-items-start gap-3">
                            <input class="form-check-input mt-1 border-secondary" type="checkbox" value="1" id="agreeCheck" required style="width: 1.5rem; height: 1.5rem; flex-shrink: 0;">
                            <label class="form-check-label text-dark text-justify" for="agreeCheck" style="line-height: 1.6;">
                                Demikian Perjanjian Kinerja ini saya buat dengan sadar dan penuh rasa tanggung jawab sebagai komitmen tugas utama saya. Apabila di akhir semester sasaran kinerja dan bukti fisik ini <strong>TIDAK TERCAPAI / TIDAK TERBUKTI</strong>, saya bersedia menerima sanksi administratif berupa peninjauan ulang hingga pencabutan jam mengajar/penonaktifan tugas tambahan oleh Kepala Sekolah dan Yayasan.
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <a href="{{ route('guru.performance_contracts.index') }}" class="btn btn-light px-4 py-2 me-3 rounded-3 text-muted fw-semibold">Batalkan</a>
                        <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-bold shadow-sm">
                            <i class="fas fa-save me-2"></i> Simpan Perubahan & Ajukan Ulang
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', (event) => {
    toggleForm();
});

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
                document.getElementById('docTitle').innerHTML = 'PERJANJIAN KINERJA GURU<br><span class="text-primary">(PRODUKTIF/KEJURUAN)</span>';
                document.getElementById('alertKejuruan').style.display = 'block';
                document.getElementById('pilar1_title').innerText = 'Kompetensi Praktik (30%)';
                document.getElementById('pilar2_title').innerText = 'Kontribusi Program (30%)';
            } else {
                document.getElementById('docTitle').innerHTML = 'PERJANJIAN KINERJA GURU<br><span class="text-primary">(MAPEL UMUM)</span>';
                document.getElementById('alertUmum').style.display = 'block';
                document.getElementById('pilar1_title').innerText = 'Kompetensi Relevansi Praktik (30%)';
                document.getElementById('pilar2_title').innerText = 'Kontribusi Program/TEFA (30%)';
            }
        } 
        else if (type === 'jabatan_tambahan') {
            document.getElementById('docTitle').innerHTML = 'PERJANJIAN KINERJA JABATAN';
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
