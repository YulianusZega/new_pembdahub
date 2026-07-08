@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah Nilai Siswa</h1>
            <p class="text-gray-600">Input nilai akademik siswa</p>
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

    <form method="GET" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-purple-500 to-violet-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-search mr-1"></i> Pilih Kelas & Mata Pelajaran</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Kelas</label>
                <select name="classroom_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" required onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ request('classroom_id', $classroomId ?? '') == $classroom->id ? 'selected' : '' }}>{{ $classroom->class_name ?? '-' }}</option>
                    @endforeach
                </select>
            </div>
            @if($classroomId)
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Mata Pelajaran</label>
                <select name="subject_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" onchange="this.form.submit()">
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id', $subjectId ?? '') == $subject->id ? 'selected' : '' }}>{{ $subject->subject_name ?? '-' }}</option>
                    @endforeach
                </select>
            </div>
            @endif
        </div>
    </form>

    @if($classroomId && $subjectId && $students->count())
    <form action="{{ route('admin.grades.store') }}" method="POST" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @csrf
        <input type="hidden" name="classroom_id" value="{{ $classroomId }}">
        <input type="hidden" name="subject_id" value="{{ $subjectId }}">
        <div class="bg-gradient-to-r from-purple-500 to-violet-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-clipboard mr-1"></i> Form Input Nilai</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chalkboard-teacher mr-1"></i> Guru</label>
                <select name="teacher_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}">{{ $teacher->full_name ?? '-' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user-graduate mr-1"></i> Siswa</label>
                <select name="student_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" required>
                    <option value="">-- Pilih Siswa --</option>
                    @foreach($students as $student)
                    <option value="{{ $student->id }}">{{ $student->full_name ?? '-' }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chart-bar mr-1"></i> Jenis Nilai</label>
                <select name="grade_type" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" required>
                    <option value="">-- Pilih Jenis Nilai --</option>
                    <option value="tugas">Tugas</option>
                    <option value="uts">UTS</option>
                    <option value="uas">UAS</option>
                    <option value="sikap">Sikap</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-list-ol mr-1"></i> Nilai (0-100)</label>
                <input type="number" name="score" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition" min="0" max="100" required>
            </div>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-purple-500 to-violet-600 hover:from-purple-600 hover:to-violet-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-save mr-1"></i> Simpan Nilai
            </button>
            <a href="{{ route('admin.grades.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
                Batal
            </a>
        </div>
    </form>
    @elseif($classroomId && $subjectId)
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 rounded-lg shadow">
        <div class="flex items-center">
            <svg class="w-6 h-6 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"/>
            </svg>
            <span class="text-yellow-700 font-medium">Tidak ada siswa di kelas ini.</span>
        </div>
    </div>
    @endif
</div>
@endsection