@extends('layouts.admin')

@section('title', 'Assign Siswa ke Kelas - Admin')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Assign Siswa ke Kelas</h1>
                <p class="text-gray-600 mt-1">Kelola siswa dalam kelas {{ $classroom->class_name }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.classrooms.assignStudents', $classroom->id) }}" method="POST">
        @csrf

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Kelas</h2>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Kelas</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-700" value="{{ $classroom->class_name }}" readonly>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-200 rounded-xl bg-gray-50 text-gray-700" value="{{ $classroom->academicYear->year ?? '-' }}" readonly>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-600 text-white font-bold text-sm">
                    2
                </div>
                <h2 class="text-xl font-bold text-gray-900">Pilih Siswa</h2>
            </div>

            <!-- Input Pencarian -->
            <div class="mb-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </span>
                    <input type="text" id="studentSearch" 
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 text-sm transition-all bg-white" 
                        placeholder="Cari nama atau NISN siswa...">
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-xl p-4 bg-gray-50">
                @forelse($students as $student)
                <div class="student-item flex items-center p-3 mb-2 bg-white rounded-lg hover:bg-cyan-50 transition-colors">
                    <input type="checkbox" name="student_ids[]" value="{{ $student->id }}" id="student_{{ $student->id }}"
                        {{ in_array($student->id, $assignedStudentIds) ? 'checked' : '' }}
                        class="w-5 h-5 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                    <label for="student_{{ $student->id }}" class="ml-3 flex-1 cursor-pointer">
                        <span class="student-name font-medium text-gray-900">{{ $student->full_name }}</span>
                        <span class="student-nisn text-gray-500 text-sm ml-2">({{ $student->nisn }})</span>
                    </label>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p>Tidak ada siswa tersedia</p>
                </div>
                @endforelse
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-600 to-blue-700 text-white rounded-xl font-medium hover:from-cyan-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Assignment
            </button>
            <a href="{{ route('admin.classrooms.index') }}" 
                class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('studentSearch');
    const studentItems = document.querySelectorAll('.student-item');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();

            studentItems.forEach(item => {
                const name = item.querySelector('.student-name').textContent.toLowerCase();
                const nisnElement = item.querySelector('.student-nisn');
                const nisn = nisnElement ? nisnElement.textContent.toLowerCase() : '';

                if (name.includes(query) || nisn.includes(query)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection