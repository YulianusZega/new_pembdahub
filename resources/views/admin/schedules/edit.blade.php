@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Jadwal Pelajaran</h1>
        <p class="text-gray-600">Perbarui informasi jadwal kelas</p>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
            </svg>
            <div>
                <h3 class="font-semibold text-red-800 mb-2">Terjadi Kesalahan</h3>
                <ul class="list-disc pl-5 space-y-1 text-sm text-red-700">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.schedules.update', $schedule) }}" method="POST" class="bg-white rounded-lg shadow-lg">
        @csrf
        @method('PUT')

        <div class="p-6 space-y-5">
            <!-- Hari -->
            <div>
                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs mr-2">1</span>
                    Pilih Hari
                </label>
                <select name="day_of_week" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">-- Pilih Hari --</option>
                    @foreach($days as $val => $label)
                    <option value="{{ $val }}" {{ old('day_of_week', $schedule->day_of_week) == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Jam Pelajaran -->
            <div>
                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs mr-2">2</span>
                    Jam Pelajaran
                </label>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Jam Mulai</label>
                        <input type="time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-600 mb-1">Jam Selesai</label>
                        <input type="time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    </div>
                </div>
            </div>

            <!-- Kelas -->
            <div>
                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs mr-2">3</span>
                    Kelas
                </label>
                <select name="classroom_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ old('classroom_id', $schedule->classroom_id) == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->class_name }} @if($classroom->school)- {{ $classroom->school->name }}@endif
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Mata Pelajaran -->
            <div>
                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs mr-2">4</span>
                    Mata Pelajaran
                </label>
                <select name="subject_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">-- Pilih Mata Pelajaran --</option>
                    @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ old('subject_id', $schedule->subject_id) == $subject->id ? 'selected' : '' }}>
                        {{ $subject->subject_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Guru -->
            <div>
                <label class="flex items-center text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center justify-center w-6 h-6 bg-indigo-600 text-white rounded-full text-xs mr-2">5</span>
                    Guru Pengajar
                </label>
                <select name="teacher_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                    <option value="">-- Pilih Guru --</option>
                    @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ old('teacher_id', $schedule->teacher_id) == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Ruangan -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ruangan / Lokasi (Opsional)</label>
                <input type="text" name="room" value="{{ old('room', $schedule->room) }}" 
                    placeholder="Contoh: Lab Komputer, Ruang 301, Lapangan" 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                <p class="text-xs text-gray-500 mt-1">Ruangan adalah lokasi fisik tempat pembelajaran, berbeda dengan Kelas yang merupakan kelompok siswa</p>
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-lg">
            <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Batal
            </a>
            <button type="submit" class="inline-flex items-center px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-lg hover:shadow-xl">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                Perbarui Jadwal
            </button>
        </div>
    </form>
</div>
@endsection