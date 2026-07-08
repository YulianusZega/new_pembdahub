@extends('layouts.admin')

@section('title', 'Tunjuk Wali Kelas')

@section('content')
<div class="max-w-3xl mx-auto">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('admin.classrooms.index') }}" 
               class="flex items-center justify-center w-10 h-10 rounded-lg bg-gray-100 hover:bg-gray-200 transition-colors">
                <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tunjuk Wali Kelas</h1>
                <p class="text-sm text-gray-600 mt-1">Pilih guru untuk menjadi wali kelas {{ $classroom->class_name }}</p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-xl p-4">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <!-- Classroom Info -->
        <div class="bg-gradient-to-r from-cyan-500 to-blue-600 px-6 py-4 text-white">
            <div class="flex items-center gap-4">
                @php
                    $avatar = $classroom->getAvatarConfig();
                @endphp
                @if($avatar['icon'])
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center text-2xl font-bold">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        {!! $avatar['icon'] !!}
                    </svg>
                </div>
                @else
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center text-2xl font-bold text-white">
                    {{ $avatar['initials'] }}
                </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold">{{ $classroom->class_name }}</h2>
                    <p class="text-cyan-100 text-sm mt-1">{{ $classroom->school->name ?? '' }}</p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('admin.classrooms.assignHomeroom.store', $classroom) }}" method="POST" class="p-6">
            @csrf

            <!-- Teacher Selection -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-3">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Pilih Guru Wali Kelas <span class="text-red-500">*</span>
                    </span>
                </label>
                <select name="teacher_id" id="teacher_id" required
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition @error('teacher_id') border-red-500 @enderror">
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" 
                                {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}
                                data-photo="{{ $teacher->photo ? asset('storage/' . $teacher->photo) : '' }}"
                                data-code="{{ $teacher->teacher_code }}"
                                data-phone="{{ $teacher->phone }}">
                            {{ $teacher->full_name }} ({{ $teacher->teacher_code }})
                        </option>
                    @endforeach
                </select>
                @error('teacher_id')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
                
                <!-- Teacher Preview Card -->
                <div id="teacher-preview" class="hidden mt-4 p-4 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border-2 border-indigo-200">
                    <div class="flex items-center gap-4">
                        <img id="preview-photo" src="" alt="" class="w-16 h-16 rounded-full object-cover border-3 border-white shadow-lg">
                        <div id="preview-avatar" class="hidden w-16 h-16 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-xl shadow-lg"></div>
                        <div>
                            <div id="preview-name" class="font-bold text-gray-900 text-lg"></div>
                            <div id="preview-code" class="text-sm text-gray-600 mt-1"></div>
                            <div id="preview-phone" class="text-sm text-gray-500 mt-1"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Year -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-3">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Tahun Ajaran <span class="text-red-500">*</span>
                    </span>
                </label>
                <input type="hidden" name="academic_year_id" value="{{ $currentAcademicYear->id ?? '' }}">
                <div class="px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-xl text-gray-700 font-semibold">
                    {{ $currentAcademicYear->year ?? 'Tidak ada tahun ajaran aktif' }}
                </div>
            </div>

            <!-- Semester -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-3">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Semester <span class="text-red-500">*</span>
                    </span>
                </label>
                <div class="grid grid-cols-3 gap-3">
                    <label class="relative flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-indigo-500 transition group">
                        <input type="radio" name="semester" value="ganjil" {{ old('semester') == 'ganjil' ? 'checked' : '' }} class="sr-only peer" required>
                        <span class="text-gray-700 font-semibold peer-checked:text-indigo-700">Ganjil</span>
                        <div class="absolute inset-0 border-2 border-indigo-600 rounded-xl opacity-0 peer-checked:opacity-100 transition"></div>
                    </label>
                    <label class="relative flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-indigo-500 transition group">
                        <input type="radio" name="semester" value="genap" {{ old('semester') == 'genap' ? 'checked' : '' }} class="sr-only peer" required>
                        <span class="text-gray-700 font-semibold peer-checked:text-indigo-700">Genap</span>
                        <div class="absolute inset-0 border-2 border-indigo-600 rounded-xl opacity-0 peer-checked:opacity-100 transition"></div>
                    </label>
                    <label class="relative flex items-center justify-center px-4 py-3 border-2 border-gray-300 rounded-xl cursor-pointer hover:border-indigo-500 transition group">
                        <input type="radio" name="semester" value="full_year" {{ old('semester') == 'full_year' ? 'checked' : '' }} class="sr-only peer" required>
                        <span class="text-gray-700 font-semibold peer-checked:text-indigo-700">Full Year</span>
                        <div class="absolute inset-0 border-2 border-indigo-600 rounded-xl opacity-0 peer-checked:opacity-100 transition"></div>
                    </label>
                </div>
                @error('semester')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Start Date -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-3">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Tanggal Mulai
                    </span>
                </label>
                <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
            </div>

            <!-- SK Number -->
            <div class="mb-6">
                <label class="block text-sm font-bold text-gray-700 mb-3">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Nomor SK (Opsional)
                    </span>
                </label>
                <input type="text" name="sk_number" value="{{ old('sk_number') }}" placeholder="Contoh: 001/SK/PEMBDA/2026"
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-6 border-t-2 border-gray-100">
                <button type="submit"
                        class="flex-1 flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-bold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Tunjuk Sebagai Wali Kelas
                </button>
                <a href="{{ route('admin.classrooms.index') }}"
                   class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-bold transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('teacher_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const preview = document.getElementById('teacher-preview');
    
    if (this.value) {
        const photo = selectedOption.dataset.photo;
        const code = selectedOption.dataset.code;
        const phone = selectedOption.dataset.phone;
        const name = selectedOption.text.split(' (')[0];
        
        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-code').textContent = '<i class="fas fa-id-badge mr-1"></i> ' + code;
        document.getElementById('preview-phone').textContent = phone ? '<i class="fas fa-phone mr-1"></i> ' + phone : '';
        
        if (photo) {
            document.getElementById('preview-photo').src = photo;
            document.getElementById('preview-photo').classList.remove('hidden');
            document.getElementById('preview-avatar').classList.add('hidden');
        } else {
            const initials = name.substring(0, 2).toUpperCase();
            document.getElementById('preview-avatar').textContent = initials;
            document.getElementById('preview-avatar').classList.remove('hidden');
            document.getElementById('preview-photo').classList.add('hidden');
        }
        
        preview.classList.remove('hidden');
    } else {
        preview.classList.add('hidden');
    }
});
</script>
@endsection
