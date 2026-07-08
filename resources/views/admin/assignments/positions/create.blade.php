@extends('layouts.admin')

@section('title', 'Tambah Penugasan Jabatan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Tambah Penugasan Jabatan</h1>
                    <p class="text-gray-600 mt-1">Tetapkan jabatan untuk guru pada tahun ajaran tertentu</p>
                </div>
            </div>
            <a href="{{ route('admin.assignments.positions.index') }}" 
                class="flex items-center gap-2 px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 hover:border-gray-400 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <p class="text-red-800 font-bold mb-2">Terjadi Kesalahan!</p>
                <ul class="list-disc list-inside space-y-1 text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.assignments.positions.store') }}" method="POST" id="assignmentForm">
        @csrf
        
        <!-- Section 1: Pilih Guru & Tahun Ajaran -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-600">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold">1</div>
                    <h2 class="text-xl font-bold text-white">Pilih Guru & Tahun Ajaran</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-2">
                        <label for="employee_id" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-chalkboard-teacher mr-1"></i> Pilih Guru <span class="text-red-500">*</span>
                        </label>
                        <select name="employee_id" id="employee_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('employee_id') border-red-500 @enderror" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" 
                                        data-school="{{ $teacher->school->name ?? '' }}"
                                        {{ old('employee_id', $selectedEmployee->id ?? '') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->employee_code }} - {{ $teacher->full_name }}
                                    @if($teacher->school) ({{ $teacher->school->name }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('employee_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="academic_year_id" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select name="academic_year_id" id="academic_year_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('academic_year_id') border-red-500 @enderror" required>
                            <option value="">-- Pilih --</option>
                            @foreach($academicYears as $year)
                                <option value="{{ $year->id }}" {{ old('academic_year_id', $currentYear->id ?? '') == $year->id ? 'selected' : '' }}>
                                    {{ $year->year }}{{ $year->is_active ? ' (Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('academic_year_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>
        </div>

        <!-- Section 2: Pilih Jabatan -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-600">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold">2</div>
                    <h2 class="text-xl font-bold text-white">Pilih Jabatan</h2>
                </div>
            </div>
            <div class="p-6">
                <p class="text-gray-600 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="font-semibold">Pilih satu atau lebih jabatan untuk guru ini</span>
                </p>

                @foreach($positions as $category => $categoryPositions)
                <div class="mb-6 last:mb-0">
                    <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <span class="px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 rounded-lg">
                            {{ ucfirst($category) }}
                        </span>
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($categoryPositions as $position)
                        <label class="relative flex items-start p-4 border-2 border-gray-200 rounded-xl hover:border-purple-400 hover:bg-purple-50 transition-all cursor-pointer group">
                            <div class="flex items-center h-5">
                                <input type="checkbox" 
                                       name="positions[]" 
                                       value="{{ $position->id }}" 
                                       class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500 focus:ring-2"
                                       {{ in_array($position->id, old('positions', [])) ? 'checked' : '' }}>
                            </div>
                            <div class="ml-3 flex-1">
                                <span class="font-semibold text-gray-900 group-hover:text-purple-700">
                                    {{ $position->display_name }}
                                </span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-bold rounded">
                                        {{ $position->position_code }}
                                    </span>
                                    @if(!auth()->user()->isAdminSekolah())
                                    <span class="text-sm font-bold text-green-600">
                                        Rp {{ number_format($position->allowance_amount, 0, ',', '.') }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endforeach

                @error('positions')
                    <p class="mt-3 text-sm text-red-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Section 3: Jabatan Utama & Detail -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-500 to-pink-600">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white font-bold">3</div>
                    <h2 class="text-xl font-bold text-white">Jabatan Utama & Detail</h2>
                </div>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="primary_position_id" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-star text-yellow-400 mr-1"></i> Jabatan Utama <span class="text-red-500">*</span>
                        </label>
                        <select name="primary_position_id" id="primary_position_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('primary_position_id') border-red-500 @enderror" required>
                            <option value="">-- Pilih Jabatan Utama --</option>
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Jabatan utama akan ditampilkan sebagai jabatan primer</p>
                        @error('primary_position_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="classroomSection" class="hidden">
                        <label for="classroom_id" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-school mr-1"></i> Kelas (untuk Wali Kelas)
                        </label>
                        <select name="classroom_id" id="classroom_id" class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <option value="">-- Pilih Kelas --</option>
                            @if(isset($classrooms))
                                @foreach($classrooms as $classroom)
                                    <option value="{{ $classroom->id }}" {{ old('classroom_id') == $classroom->id ? 'selected' : '' }}>
                                        {{ $classroom->class_name }} ({{ $classroom->grade_level }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Wajib diisi jika jabatan Wali Kelas dipilih</p>
                    </div>

                    <div>
                        <label for="position_start_date" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i> Tanggal Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="position_start_date" 
                               id="position_start_date" 
                               value="{{ old('position_start_date', date('Y-m-d')) }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('position_start_date') border-red-500 @enderror" 
                               required>
                        @error('position_start_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sk_number" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-file-alt mr-1"></i> Nomor SK
                        </label>
                        <input type="text" 
                               name="sk_number" 
                               id="sk_number" 
                               value="{{ old('sk_number') }}"
                               placeholder="Contoh: SK-001/YPP/2024"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('sk_number') border-red-500 @enderror">
                        @error('sk_number')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="sk_date" class="block text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-calendar-alt mr-1"></i> Tanggal SK
                        </label>
                        <input type="date" 
                               name="sk_date" 
                               id="sk_date" 
                               value="{{ old('sk_date') }}"
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('sk_date') border-red-500 @enderror">
                        @error('sk_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('admin.assignments.positions.index') }}" 
                class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-bold hover:bg-gray-50 transition-all">
                Batal
            </a>
            <button type="submit" 
                    class="flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-purple-600 to-pink-700 text-white rounded-xl font-bold hover:from-purple-700 hover:to-pink-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Penugasan
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const positionCheckboxes = document.querySelectorAll('input[name="positions[]"]');
    const primarySelect = document.getElementById('primary_position_id');
    const classroomSection = document.getElementById('classroomSection');
    const classroomSelect = document.getElementById('classroom_id');
    const employeeSelect = document.getElementById('employee_id');
    const academicYearSelect = document.getElementById('academic_year_id');

    // Reload page when employee changes
    if (employeeSelect) {
        employeeSelect.addEventListener('change', function() {
            const empId = this.value;
            const yearId = academicYearSelect ? academicYearSelect.value : '';
            if (empId) {
                window.location.href = "{{ route('admin.assignments.positions.create') }}?employee_id=" + empId + "&academic_year_id=" + yearId;
            }
        });
    }
    
    // Update primary position options when checkboxes change
    function updatePrimaryOptions() {
        const selectedPositions = Array.from(positionCheckboxes)
            .filter(cb => cb.checked)
            .map(cb => ({
                id: cb.value,
                name: cb.closest('label').querySelector('.font-semibold').textContent.trim()
            }));
        
        primarySelect.innerHTML = '<option value="">-- Pilih Jabatan Utama --</option>';
        
        selectedPositions.forEach(pos => {
            const option = document.createElement('option');
            option.value = pos.id;
            option.textContent = pos.name;
            primarySelect.appendChild(option);
        });

        // Show/hide classroom section if "Wali Kelas" is selected
        const waliKelasSelected = selectedPositions.some(pos => 
            pos.name.toLowerCase().includes('wali kelas')
        );
        if (classroomSection) {
            if (waliKelasSelected) {
                classroomSection.classList.remove('hidden');
            } else {
                classroomSection.classList.add('hidden');
                if (classroomSelect) classroomSelect.value = '';
            }
        }
    }
    
    positionCheckboxes.forEach(cb => {
        cb.addEventListener('change', updatePrimaryOptions);
    });
    
    // Initial update
    updatePrimaryOptions();
});
</script>
@endsection
