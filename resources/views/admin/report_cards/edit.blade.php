@extends('layouts.admin')

@section('title', 'Edit Rapor - ' . $reportCard->student->full_name)

@section('content')
<style>
.rapor-edit-wrapper { background: #f7fafc; min-height: 100vh; padding: 30px; }
.rapor-edit-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 25px 30px; border-radius: 12px; box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3); margin-bottom: 30px; }
.edit-card { background: white; padding: 35px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07); }
.form-group-modern { margin-bottom: 28px; }
.form-label-modern { display: block; font-weight: 700; color: #2d3748; margin-bottom: 10px; font-size: 1rem; }
.form-control-modern { width: 100%; padding: 12px 15px; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.95rem; transition: all 0.3s; font-family: inherit; }
.form-control-modern:focus { outline: none; border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1); }
.form-control-modern.is-invalid { border-color: #e53e3e; }
.invalid-feedback { color: #e53e3e; font-size: 0.875rem; margin-top: 5px; display: block; }
.form-text { color: #718096; font-size: 0.875rem; margin-top: 5px; display: block; }
.btn-action-edit { padding: 12px 30px; border-radius: 8px; font-weight: 700; transition: all 0.3s; border: none; cursor: pointer; font-size: 1rem; }
.btn-action-edit:hover { transform: translateY(-2px); box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15); }
.btn-primary-modern { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
.btn-secondary-modern { background: #e2e8f0; color: #4a5568; }
.info-box { background: #edf2f7; border: 2px solid #cbd5e0; border-radius: 8px; padding: 20px; margin-bottom: 25px; }
.info-row { display: flex; margin-bottom: 10px; }
.info-label { width: 40%; font-weight: 600; color: #4a5568; }
.info-value { width: 60%; color: #2d3748; }
</style>

<div class="rapor-edit-wrapper">
    <!-- Header -->
    <div class="rapor-edit-header">
        <a href="{{ route('admin.report_cards.show', $reportCard) }}" class="btn-action-edit btn-secondary-modern" style="display: inline-block; margin-bottom: 15px;">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h1 style="font-size: 2rem; font-weight: 800; margin: 0;"><i class="fas fa-pencil-alt mr-1"></i> Edit Catatan Rapor</h1>
        <p style="margin: 5px 0 0 0; opacity: 0.9;">{{ $reportCard->student->full_name }} - {{ $reportCard->semester->semester_name }}</p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
        <!-- Left: Student Info -->
        <div>
            <div class="edit-card">
                <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 20px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                    <i class="fas fa-user" style="color: #667eea;"></i> Informasi Siswa
                </h5>
                <div class="info-box">
                    <div class="info-row">
                        <div class="info-label">Nama:</div>
                        <div class="info-value">{{ $reportCard->student->full_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">NISN:</div>
                        <div class="info-value"><code style="background: #edf2f7; padding: 2px 6px; border-radius: 4px;">{{ $reportCard->student->nisn }}</code></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Kelas:</div>
                        <div class="info-value">{{ $reportCard->classroom->class_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Semester:</div>
                        <div class="info-value">{{ $reportCard->semester->semester_name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Rata-rata:</div>
                        <div class="info-value"><strong style="font-size: 1.2rem; color: {{ $reportCard->average_score >= 80 ? '#48bb78' : ($reportCard->average_score >= 70 ? '#ecc94b' : '#e53e3e') }};">{{ number_format($reportCard->average_score, 1) }}</strong></div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">Status:</div>
                        <div class="info-value"><span style="padding: 4px 12px; border-radius: 15px; font-size: 0.85rem; font-weight: 700; background: {{ $reportCard->status == 'draft' ? '#e2e8f0' : ($reportCard->status == 'finalized' ? '#cfe2ff' : '#d4edda') }}; color: {{ $reportCard->status == 'draft' ? '#4a5568' : ($reportCard->status == 'finalized' ? '#084298' : '#155724') }};">{{ ucfirst($reportCard->status) }}</span></div>
                    </div>
                </div>
                
                <div style="background: #fff3cd; border: 2px solid #ecc94b; border-radius: 8px; padding: 15px; margin-top: 20px;">
                    <div style="font-weight: 700; color: #856404; margin-bottom: 8px;"><i class="fas fa-info-circle"></i> Catatan Penting</div>
                    <ul style="margin: 0; padding-left: 20px; color: #856404; font-size: 0.9rem;">
                        <li>Edit hanya untuk catatan & rekomendasi</li>
                        <li>Nilai tidak dapat diubah di sini</li>
                        <li>Simpan perubahan sebelum finalize</li>
                    </ul>
                </div>
                
                <div class="edit-card" style="margin-top: 25px; background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);">
                    <h6 style="color: #2d3748; font-weight: 700; margin-bottom: 15px;">
                        <i class="fas fa-lightbulb" style="color: #ecc94b;"></i> Tips Menulis Catatan
                    </h6>
                    <ul style="margin: 0; padding-left: 20px; color: #4a5568; font-size: 0.9rem; line-height: 1.8;">
                        <li>Fokus pada perkembangan & kemajuan siswa</li>
                        <li>Gunakan bahasa positif dan membangun</li>
                        <li>Berikan contoh konkret bila memungkinkan</li>
                        <li>Saran yang spesifik dan mudah diterapkan</li>
                        <li>Hindari kata-kata yang menjatuhkan</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right: Form -->
        <div>
            <div class="edit-card">
                <h5 style="color: #2d3748; font-weight: 700; margin-bottom: 25px; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
                    <i class="fas fa-edit" style="color: #667eea;"></i> Edit Catatan & Rekomendasi
                </h5>
                
                @php
                    $isHomeroomTeacher = auth()->user()->isGuru() && auth()->user()->isHomeroomTeacher() && !auth()->user()->hasAnyRole(['superadmin', 'admin_sekolah']);
                @endphp

                @if($isHomeroomTeacher)
                <div style="background: #d1ecf1; border: 2px solid #3498db; border-radius: 8px; padding: 18px; margin-bottom: 25px;">
                    <div style="font-weight: 700; color: #0c5460; margin-bottom: 8px;">
                        <i class="fas fa-user-check" style="color: #3498db;"></i> Sebagai Wali Kelas
                    </div>
                    <p style="margin: 0; color: #0c5460; font-size: 0.95rem;">
                        Anda hanya dapat mengedit <strong>Catatan Wali Kelas</strong>. Field lainnya (Catatan Kepala Sekolah & Saran) hanya bisa diedit oleh Admin Sekolah.
                    </p>
                </div>
                @endif

                <form method="POST" action="{{ route('admin.report_cards.update', $reportCard) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-chalkboard-teacher" style="color: #3182ce;"></i> Catatan Wali Kelas
                        </label>
                        <textarea name="teacher_notes" class="form-control-modern @error('teacher_notes') is-invalid @enderror" 
                                  rows="5" maxlength="1000" placeholder="Tulis catatan wali kelas tentang perkembangan siswa...">{{ old('teacher_notes', $reportCard->teacher_notes) }}</textarea>
                        @error('teacher_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text"><i class="fas fa-info-circle"></i> Maksimal 1000 karakter - Sisa: <span id="teacher-count">{{ 1000 - strlen($reportCard->teacher_notes ?? '') }}</span></small>
                    </div>

                    @if(!$isHomeroomTeacher)
                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-user-tie" style="color: #667eea;"></i> Catatan Kepala Sekolah
                        </label>
                        <textarea name="principal_notes" class="form-control-modern @error('principal_notes') is-invalid @enderror" 
                                  rows="5" maxlength="1000" placeholder="Tulis catatan kepala sekolah...">{{ old('principal_notes', $reportCard->principal_notes) }}</textarea>
                        @error('principal_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text"><i class="fas fa-info-circle"></i> Maksimal 1000 karakter - Sisa: <span id="principal-count">{{ 1000 - strlen($reportCard->principal_notes ?? '') }}</span></small>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern">
                            <i class="fas fa-lightbulb" style="color: #ecc94b;"></i> Saran Pengembangan
                        </label>
                        <textarea name="recommendations" class="form-control-modern @error('recommendations') is-invalid @enderror" 
                                  rows="5" maxlength="1000" placeholder="Tulis saran untuk pengembangan siswa...">{{ old('recommendations', $reportCard->recommendations) }}</textarea>
                        @error('recommendations')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text"><i class="fas fa-info-circle"></i> Maksimal 1000 karakter - Sisa: <span id="recommendations-count">{{ 1000 - strlen($reportCard->recommendations ?? '') }}</span></small>
                    </div>
                    @else
                    <!-- Tampilkan read-only untuk wali kelas -->
                    <div class="form-group-modern">
                        <label class="form-label-modern" style="opacity: 0.6;">
                            <i class="fas fa-user-tie" style="color: #667eea;"></i> Catatan Kepala Sekolah
                            <span style="font-size: 0.85rem; font-weight: 400; color: #718096;">(Hanya bisa diedit oleh Admin)</span>
                        </label>
                        <div style="background: #f7fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 12px; color: #4a5568; min-height: 80px;">
                            {{ $reportCard->principal_notes ?: 'Belum diisi oleh Admin Sekolah' }}
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label-modern" style="opacity: 0.6;">
                            <i class="fas fa-lightbulb" style="color: #ecc94b;"></i> Saran Pengembangan
                            <span style="font-size: 0.85rem; font-weight: 400; color: #718096;">(Hanya bisa diedit oleh Admin)</span>
                        </label>
                        <div style="background: #f7fafc; border: 2px solid #e2e8f0; border-radius: 8px; padding: 12px; color: #4a5568; min-height: 80px;">
                            {{ $reportCard->recommendations ?: 'Belum diisi oleh Admin Sekolah' }}
                        </div>
                    </div>
                    @endif

                    <div style="display: flex; gap: 15px; margin-top: 35px;">
                        <button type="submit" class="btn-action-edit btn-primary-modern" style="flex: 1;">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.report_cards.show', $reportCard) }}" class="btn-action-edit btn-secondary-modern" style="flex: 1; text-align: center; text-decoration: none; display: inline-block;">
                            <i class="fas fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Character counter
document.addEventListener('DOMContentLoaded', function() {
    const textareas = [
        { element: document.querySelector('textarea[name="teacher_notes"]'), counter: document.getElementById('teacher-count') },
        { element: document.querySelector('textarea[name="principal_notes"]'), counter: document.getElementById('principal-count') },
        { element: document.querySelector('textarea[name="recommendations"]'), counter: document.getElementById('recommendations-count') }
    ];
    
    textareas.forEach(({ element, counter }) => {
        if (element && counter) {
            element.addEventListener('input', function() {
                const remaining = 1000 - this.value.length;
                counter.textContent = remaining;
                counter.style.color = remaining < 100 ? '#e53e3e' : '#718096';
            });
        }
    });
});
</script>
@endsection
