@extends('layouts.admin')

@section('title', 'Tambah Absensi - Admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Absensi</h1>
            <p class="text-gray-600">Catat kehadiran siswa</p>
        </div>
    </div>

    <form action="{{ route('admin.attendances.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @csrf
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-clipboard mr-1"></i> Form Absensi</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="w-full md:w-1/3">
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Absensi</label>
                <input type="date" name="date" id="attendance_date" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" value="{{ old('date', date('Y-m-d')) }}" required>
            </div>
            
            <div id="existence_alert" class="hidden bg-blue-50 border-l-4 border-blue-500 p-4 rounded-xl mb-4 text-blue-800 text-sm italic">
                <i class="fas fa-info-circle mr-1"></i> Siswa sudah memiliki data absen di tanggal ini. Jam Masuk/Keluar telah otomatis dimuat. <b>(Mode Perbarui)</b>
            </div>

            @if($isSuperAdmin)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Unit Sekolah (Superadmin)</label>
                <select id="school_filter" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}">{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Kelas</label>
                <select name="classroom_id" id="classroom_select" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                    <option value="">Pilih Kelas</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" data-school-id="{{ $classroom->school_id ?? '' }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>{{ $classroom->class_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user-graduate mr-1"></i> Siswa</label>
                <select name="student_id" id="student_select" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                    <option value="">Pilih Siswa</option>
                    @foreach($students as $student)
                    <option value="{{ $student->id }}" data-school-id="{{ $student->school_id ?? '' }}" data-class-ids="{{ $student->classrooms->pluck('id')->implode(',') }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->full_name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bagian Jam (Muncul Dinamis) -->
            <div id="time_fields_section" class="bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200 space-y-4 opacity-50 pointer-events-none transition-all">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-bold text-gray-600 uppercase tracking-wider">Konfigurasi Waktu</h3>
                    <span id="lock_badge" class="hidden px-2 py-1 bg-gray-200 text-gray-500 text-[10px] rounded uppercase font-bold"><i class="fas fa-lock mr-1"></i> Terkunci</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">JAM MASUK</label>
                        <input type="time" name="time_in" id="time_in" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition bg-white">
                        <input type="hidden" name="hidden_time_in" id="hidden_time_in">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 mb-2">JAM KELUAR</label>
                        <input type="time" name="time_out" id="time_out" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition bg-white">
                        <input type="hidden" name="hidden_time_out" id="hidden_time_out">
                    </div>
                </div>
                <p id="time_info" class="text-[11px] text-gray-400 italic">Pilih siswa terlebih dahulu untuk mengisi jam.</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chart-bar mr-1"></i> Status Kehadiran</label>
                <select name="status" id="status" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                    <option value="">Pilih Status</option>
                    <option value="hadir" {{ old('status') == 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ old('status') == 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ old('status') == 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ old('status') == 'alpha' ? 'selected' : '' }}>Alpha (Tanpe Keterangan)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-file-alt mr-1"></i> Catatan (Opsional)</label>
                <textarea name="notes" id="notes" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" rows="2">{{ old('notes') }}</textarea>
            </div>

            <!-- Bagian Upload Dokumen (Muncul hanya jika Izin/Sakit) -->
            <div id="document_upload_section" class="hidden animate-fade-in bg-yellow-50 p-4 rounded-xl border border-yellow-200">
                <label class="block text-sm font-bold text-yellow-800 mb-2"><i class="fas fa-paperclip mr-1"></i> Lampiran Dokumen (Surat Izin/Dokter)</label>
                <input type="file" name="attachment" id="attachment" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-100 file:text-yellow-700 hover:file:bg-yellow-200 cursor-pointer">
                <p class="text-[10px] text-yellow-600 mt-2">* Format: JPG, PNG, PDF (Max 2MB). Opsional.</p>
            </div>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="{{ route('admin.attendances.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const schoolFilter = document.getElementById('school_filter');
    const classSelect = document.getElementById('classroom_select');
    const studentSelect = document.getElementById('student_select');
    
    // Simpan option asli
    const originalClasses = classSelect ? Array.from(classSelect.options) : [];
    const originalStudents = studentSelect ? Array.from(studentSelect.options) : [];

    function filterStudents() {
        if(!studentSelect) return;
        const schoolId = schoolFilter ? schoolFilter.value : null;
        const classId = classSelect ? classSelect.value : null;
        
        studentSelect.innerHTML = '';
        originalStudents.forEach(opt => {
            let schoolMatch = true;
            let classMatch = true;
            
            if(schoolId && opt.value !== "") {
                schoolMatch = opt.getAttribute('data-school-id') === schoolId;
            }
            if(classId && opt.value !== "") {
                const classIds = opt.getAttribute('data-class-ids');
                classMatch = classIds && classIds.split(',').includes(classId.toString());
            }

            if(opt.value === "" || (schoolMatch && classMatch)) {
                studentSelect.appendChild(opt.cloneNode(true));
            }
        });
    }

    if(schoolFilter) {
        schoolFilter.addEventListener('change', function() {
            const schoolId = this.value;
            
            // Filter Kelas
            if(classSelect) {
                classSelect.innerHTML = '';
                originalClasses.forEach(opt => {
                    if(opt.value === "" || opt.getAttribute('data-school-id') === schoolId || schoolId === "") {
                        classSelect.appendChild(opt.cloneNode(true));
                    }
                });
            }

            filterStudents();
        });
    }

    if(classSelect) {
        classSelect.addEventListener('change', filterStudents);
    }

    // Modern AJAX Existence Check
    const dateInput = document.getElementById('attendance_date');
    const existenceAlert = document.getElementById('existence_alert');
    const timeFieldsSection = document.getElementById('time_fields_section');
    const timeInfo = document.getElementById('time_info');
    const lockBadge = document.getElementById('lock_badge');
    const timeIn = document.getElementById('time_in');
    const timeOut = document.getElementById('time_out');
    const hiddenTimeIn = document.getElementById('hidden_time_in');
    const hiddenTimeOut = document.getElementById('hidden_time_out');
    const statusInput = document.getElementById('status');
    const notesInput = document.getElementById('notes');

    function checkExistingAttendance() {
        const studentId = studentSelect.value;
        const date = dateInput.value;

        if(!studentId || !date) {
            timeFieldsSection.classList.add('opacity-50', 'pointer-events-none');
            timeInfo.textContent = 'Pilih siswa terlebih dahulu untuk mengisi jam.';
            return;
        }

        fetch(`{{ url('admin/attendances/check') }}?student_id=${studentId}&date=${date}`)
            .then(res => res.json())
            .then(res => {
                // Aktifkan section
                timeFieldsSection.classList.remove('opacity-50', 'pointer-events-none');
                timeInfo.textContent = 'Silakan isi jam jika diperlukan.';

                // Reset state
                timeIn.readOnly = false;
                timeIn.classList.remove('bg-gray-100', 'cursor-not-allowed', 'border-gray-100');
                timeIn.classList.add('bg-white', 'border-gray-200');

                timeOut.readOnly = false;
                timeOut.classList.remove('bg-gray-100', 'cursor-not-allowed', 'border-gray-100');
                timeOut.classList.add('bg-white', 'border-gray-200');
                
                lockBadge.classList.add('hidden');

                if(res.exists) {
                    existenceAlert.classList.remove('hidden');
                    statusInput.value = res.data.status || 'hadir';
                    notesInput.value = res.data.notes || '';

                    // Logic Jam Masuk
                    if(res.data.time_in) {
                        timeIn.value = res.data.time_in;
                        timeIn.readOnly = true;
                        timeIn.classList.replace('bg-white', 'bg-gray-100');
                        timeIn.classList.add('cursor-not-allowed', 'border-gray-100');
                        hiddenTimeIn.value = res.data.time_in;
                        lockBadge.classList.remove('hidden');
                    } else {
                        timeIn.value = '';
                        hiddenTimeIn.value = '';
                    }

                    // Logic Jam Keluar
                    if(res.data.time_out) {
                        timeOut.value = res.data.time_out;
                        timeOut.readOnly = true;
                        timeOut.classList.replace('bg-white', 'bg-gray-100');
                        timeOut.classList.add('cursor-not-allowed', 'border-gray-100');
                        hiddenTimeOut.value = res.data.time_out;
                        lockBadge.classList.remove('hidden');
                    } else {
                        timeOut.value = '';
                        hiddenTimeOut.value = '';
                    }

                } else {
                    existenceAlert.classList.add('hidden');
                    
                    // AUTO-FILL JAM SEKARANG (Ide 1)
                    const now = new Date();
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    timeIn.value = `${hours}:${minutes}`;
                    
                    timeOut.value = '';
                    hiddenTimeIn.value = '';
                    hiddenTimeOut.value = '';
                    statusInput.value = 'hadir';
                    notesInput.value = '';
                    
                    // Update visibility sections
                    toggleDocUpload();
                }
            });
    }

    const docSection = document.getElementById('document_upload_section');
    function toggleDocUpload() {
        const status = statusInput.value;
        if(status === 'izin' || status === 'sakit') {
            docSection.classList.remove('hidden');
        } else {
            docSection.classList.add('hidden');
        }

        // Ide Barusan: Jika bukan Hadir, sembunyikan jam
        if(status !== 'hadir' && status !== '') {
            timeFieldsSection.classList.add('hidden');
            timeIn.value = '';
            timeOut.value = '';
        } else if(studentSelect.value) {
            timeFieldsSection.classList.remove('hidden');
        }
    }

    if(statusInput) statusInput.addEventListener('change', toggleDocUpload);
    if(studentSelect) studentSelect.addEventListener('change', checkExistingAttendance);
    if(dateInput) dateInput.addEventListener('change', checkExistingAttendance);
});
</script>
@endpush