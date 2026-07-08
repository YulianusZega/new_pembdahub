@extends('layouts.admin')

@section('title', 'Edit Absensi - Admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Edit Absensi</h1>
            <p class="text-gray-600">Perbarui data kehadiran</p>
        </div>
    </div>

    <form action="{{ route('admin.attendances.update', $attendance) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @csrf
        @method('PUT')
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-clipboard mr-1"></i> Form Absensi</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tanggal</label>
                    <input type="date" disabled class="w-full border-2 border-gray-100 bg-gray-50 text-gray-500 p-3 rounded-xl cursor-not-allowed" value="{{ old('date', $attendance->date ? $attendance->date->format('Y-m-d') : '') }}">
                    <input type="hidden" name="date" value="{{ $attendance->date ? $attendance->date->format('Y-m-d') : '' }}">
                    <p class="text-[10px] text-gray-400 mt-1">* Tanggal tidak dapat diubah.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-clock mr-1"></i> Jam Masuk</label>
                    <input type="time" name="time_in" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" value="{{ old('time_in', $attendance->time_in) }}">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-sign-out-alt mr-1"></i> Jam Keluar</label>
                    <input type="time" name="time_out" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" value="{{ old('time_out', $attendance->time_out) }}">
                </div>
            </div>

            @if($isSuperAdmin)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Unit Sekolah (Superadmin)</label>
                <select id="school_filter" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                    <option value="">-- Pilih Sekolah --</option>
                    @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ $attendance->student && $attendance->student->school_id == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Kelas</label>
                <select disabled class="w-full border-2 border-gray-100 bg-gray-50 text-gray-500 p-3 rounded-xl cursor-not-allowed">
                    @foreach($classrooms as $classroom)
                        @if(old('classroom_id', $attendance->classroom_id) == $classroom->id)
                            <option value="{{ $classroom->id }}" selected>{{ $classroom->class_name }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" name="classroom_id" value="{{ $attendance->classroom_id }}">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user-graduate mr-1"></i> Siswa</label>
                <select disabled class="w-full border-2 border-gray-100 bg-gray-50 text-gray-500 p-3 rounded-xl cursor-not-allowed">
                    @foreach($students as $student)
                        @if(old('student_id', $attendance->student_id) == $student->id)
                            <option value="{{ $student->id }}" selected>{{ $student->full_name }}</option>
                        @endif
                    @endforeach
                </select>
                <input type="hidden" name="student_id" value="{{ $attendance->student_id }}">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chart-bar mr-1"></i> Status Kehadiran</label>
                <select name="status" id="status" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required>
                    <option value="">Pilih Status</option>
                    <option value="hadir" {{ (old('status', $attendance->status) == 'hadir') ? 'selected' : '' }}>Hadir</option>
                    <option value="izin" {{ (old('status', $attendance->status) == 'izin') ? 'selected' : '' }}>Izin</option>
                    <option value="sakit" {{ (old('status', $attendance->status) == 'sakit') ? 'selected' : '' }}>Sakit</option>
                    <option value="alpha" {{ (old('status', $attendance->status) == 'alpha' || old('status', $attendance->status) == 'alpa') ? 'selected' : '' }}>Alpha (Tanpe Keterangan)</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-file-alt mr-1"></i> Catatan (Opsional)</label>
                <textarea name="notes" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" rows="2">{{ old('notes', $attendance->notes) }}</textarea>
            </div>

            <!-- Bagian Upload Dokumen (Izin/Sakit) -->
            <div id="document_upload_section" class="{{ in_array($attendance->status, ['izin', 'sakit']) ? '' : 'hidden' }} animate-fade-in bg-yellow-50 p-4 rounded-xl border border-yellow-200 mt-4">
                <label class="block text-sm font-bold text-yellow-800 mb-2"><i class="fas fa-paperclip mr-1"></i> Lampiran Dokumen</label>
                
                @if($attendance->attachment)
                <div class="mb-3 p-2 bg-white rounded border border-yellow-100 flex items-center justify-between">
                    <span class="text-xs text-gray-600 truncate mr-2"><i class="fas fa-file mr-1"></i> {{ $attendance->attachment_name }}</span>
                    <a href="{{ asset('storage/' . $attendance->attachment) }}" target="_blank" class="text-[10px] bg-yellow-500 text-white px-2 py-1 rounded">Lihat</a>
                </div>
                @endif

                <input type="file" name="attachment" id="attachment" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-yellow-100 file:text-yellow-700 hover:file:bg-yellow-200 cursor-pointer">
                <p class="text-[10px] text-yellow-600 mt-2">* Format: JPG, PNG, PDF (Max 2MB). Kosongkan jika tidak ingin mengubah.</p>
            </div>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-save mr-1"></i> Simpan Perubahan
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
    // Logic Upload Dokumen
    const statusInput = document.getElementById('status');
    const docSection = document.getElementById('document_upload_section');
    
    function toggleDocUpload() {
        const status = statusInput.value;
        if(status === 'izin' || status === 'sakit') {
            docSection.classList.remove('hidden');
        } else {
            docSection.classList.add('hidden');
        }
    }

    if(statusInput) {
        statusInput.addEventListener('change', toggleDocUpload);
    }

    // Logic Filter Siswa/Kelas
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
        
        const currStudent = studentSelect.value;
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

        studentSelect.value = currStudent;
        if(!studentSelect.value && currStudent) studentSelect.value = '';
    }

    if(schoolFilter) {
        schoolFilter.addEventListener('change', function() {
            const schoolId = this.value;
            
            // Filter Kelas
            if(classSelect) {
                const currClass = classSelect.value;
                classSelect.innerHTML = '';
                originalClasses.forEach(opt => {
                    if(opt.value === "" || opt.getAttribute('data-school-id') === schoolId || schoolId === "") {
                        classSelect.appendChild(opt.cloneNode(true));
                    }
                });
                classSelect.value = currClass;
                if(!classSelect.value && currClass) classSelect.value = '';
            }

            filterStudents();
        });
        
        // Trigger on load
        if(schoolFilter.value) {
            schoolFilter.dispatchEvent(new Event('change'));
        }
    }

    if(classSelect) {
        classSelect.addEventListener('change', filterStudents);
        // Trigger on load
        filterStudents();
    }
});
</script>
@endpush