@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Jadwal Pelajaran</h1>
            <p class="text-gray-600">Buat jadwal mengajar baru</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.schedules.store') }}" method="POST" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @csrf

        <!-- Step 1: Hari -->
        <div class="border-b border-gray-200">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 text-white rounded-xl font-bold shadow">1</span>
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-calendar-alt mr-1"></i> Pilih Hari</h3>
                </div>
                <select name="day_of_week" id="day_of_week" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required>
                    <option value="">-- Pilih Hari --</option>
                    @foreach($days as $val => $label)
                    <option value="{{ $val }}" {{ old('day_of_week') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Step 2: Jam Pelajaran -->
        <div class="border-b border-gray-200">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 text-white rounded-xl font-bold shadow">2</span>
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-clock mr-1"></i> Tentukan Jam Pelajaran</h3>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Mulai</label>
                        <input type="time" name="start_time" id="start_time" value="{{ old('start_time') }}" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required>
                        <p class="text-xs text-gray-500 mt-1">Contoh: 07:00</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Jam Selesai</label>
                        <input type="time" name="end_time" id="end_time" value="{{ old('end_time') }}" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required>
                        <p class="text-xs text-gray-500 mt-1">Contoh: 08:30</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Kelas -->
        <div class="border-b border-gray-200">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 text-white rounded-xl font-bold shadow">3</span>
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-school mr-1"></i> Pilih Kelas</h3>
                </div>
                <select name="classroom_id" id="classroom_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->class_name }} @if($classroom->school) - {{ $classroom->school->name }}@endif
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 mt-2">Contoh: X-A, XI IPA 1, XII TKJ 2</p>
            </div>
        </div>

        <!-- Step 4: Mata Pelajaran -->
        <div class="border-b border-gray-200">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 text-white rounded-xl font-bold shadow">4</span>
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-graduation-cap mr-1"></i> Pilih Mata Pelajaran</h3>
                </div>
                <select name="subject_id" id="subject_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->subject_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Step 5: Guru (filtered by subject) -->
        <div class="border-b border-gray-200">
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <span class="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-orange-500 to-red-600 text-white rounded-xl font-bold shadow">5</span>
                    <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-chalkboard-teacher mr-1"></i> Pilih Guru</h3>
                </div>
                <select name="teacher_id" id="teacher_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition" required disabled>
                    <option value="">-- Pilih Mata Pelajaran Dulu --</option>
                </select>
                <p class="text-xs text-gray-500 mt-2">Guru akan difilter berdasarkan mata pelajaran yang mengajar</p>
            </div>
        </div>

        <!-- Optional: Ruangan -->
        <div class="p-6">
            <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-door-open mr-1"></i> Ruangan / Lokasi (Opsional)</label>
            <input type="text" name="room" value="{{ old('room') }}" placeholder="Contoh: Lab Komputer, Ruang 301, Lapangan" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent transition">
            <p class="text-xs text-gray-500 mt-2">Ruangan adalah lokasi fisik tempat pembelajaran, berbeda dengan Kelas yang merupakan kelompok siswa</p>
        </div>

        <div class="px-6 pb-6 flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-save mr-1"></i> Simpan Jadwal
            </button>
            <a href="{{ route('admin.schedules.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
                Batal
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const classroomSelect = document.getElementById('classroom_id');
        const subjectSelect = document.getElementById('subject_id');
        const teacherSelect = document.getElementById('teacher_id');
        
        // Dynamic filtering berdasarkan classroom (untuk superadmin)
        @if(auth()->user()->isSuperAdmin())
        classroomSelect.addEventListener('change', async function() {
            const classroomId = this.value;
            
            // Reset dropdowns
            subjectSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
            teacherSelect.innerHTML = '<option value="">-- Pilih Mata Pelajaran Dulu --</option>';
            subjectSelect.disabled = !classroomId;
            teacherSelect.disabled = true;
            
            if (!classroomId) return;
            
            try {
                const response = await fetch('{{ route("admin.api.schedule.by-classroom") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ classroom_id: classroomId })
                });
                
                if (!response.ok) throw new Error('Network error');
                const data = await response.json();
                
                // Populate subjects
                if (data.subjects && data.subjects.length > 0) {
                    data.subjects.forEach(subject => {
                        const option = document.createElement('option');
                        option.value = subject.id;
                        option.textContent = subject.subject_name;
                        subjectSelect.appendChild(option);
                    });
                } else {
                    subjectSelect.innerHTML = '<option value="">-- Tidak ada mata pelajaran di sekolah ini --</option>';
                }
                
                // Store teachers data for later filtering
                window.teachersData = data.teachers || [];
                
            } catch (error) {
                showFlashMessage('Gagal memuat data mata pelajaran dan guru.', 'error');
            }
        });
        @endif
        
        // Filter teachers by subject (menggunakan kompetensi)
        subjectSelect.addEventListener('change', async function() {
            const subjectId = this.value;
            
            teacherSelect.innerHTML = '<option value="">-- Pilih Guru --</option>';
            teacherSelect.disabled = !subjectId;
            
            if (!subjectId) return;
            
            try {
                const response = await fetch('{{ route("admin.api.teachers-by-subject") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ subject_id: subjectId })
                });
                
                if (!response.ok) throw new Error('Network error');
                const data = await response.json();
                
                if (data.teachers && data.teachers.length > 0) {
                    data.teachers.forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.full_name;
                        teacherSelect.appendChild(option);
                    });
                    teacherSelect.disabled = false;
                } else {
                    teacherSelect.innerHTML = '<option value="">Tidak ada guru berkompeten untuk mata pelajaran ini</option>';
                    teacherSelect.disabled = true;
                }
            } catch (error) {
                showFlashMessage('Gagal memuat data guru.', 'error');
                // Fallback: show all teachers from school if competency API fails
                @if(!auth()->user()->isSuperAdmin())
                const allTeachers = @json($teachers);
                allTeachers.forEach(teacher => {
                    const option = document.createElement('option');
                    option.value = teacher.id;
                    option.textContent = teacher.full_name;
                    teacherSelect.appendChild(option);
                });
                teacherSelect.disabled = false;
                @else
                if (window.teachersData && window.teachersData.length > 0) {
                    window.teachersData.forEach(teacher => {
                        const option = document.createElement('option');
                        option.value = teacher.id;
                        option.textContent = teacher.full_name;
                        teacherSelect.appendChild(option);
                    });
                    teacherSelect.disabled = false;
                }
                @endif
            }
        });
    });
</script>
@endpush
@endsection