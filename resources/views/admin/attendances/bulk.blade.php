@extends('layouts.admin')

@section('title', 'Absensi Kelas - Admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Input Absensi Kelas</h1>
            <p class="text-gray-600">Catat kehadiran seluruh siswa sekaligus</p>
        </div>
    </div>

    <form method="GET" class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-search mr-1"></i> Pilih Kelas & Tanggal</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tanggal</label>
                    <input type="date" name="date" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" value="{{ request('date', date('Y-m-d')) }}" required>
                </div>
                @if($isSuperAdmin)
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-university mr-1"></i> Filter Sekolah</label>
                    <select name="school_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" onchange="this.form.submit()">
                        <option value="">Semua Sekolah</option>
                        @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $selectedSchoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Kelas</label>
                <select name="classroom_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition" required onchange="this.form.submit()">
                    <option value="">Pilih Kelas</option>
                    @foreach($classrooms as $classroom)
                    <option value="{{ $classroom->id }}" {{ request('classroom_id', $selectedClassroom ?? '') == $classroom->id ? 'selected' : '' }}>
                        {{ $classroom->class_name }} {{ $isSuperAdmin ? '('. ($classroom->school->name ?? '-') .')' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </form>

    @if(isset($students) && count($students) > 0)
    <form action="{{ route('admin.attendances.bulkStore') }}" method="POST">
        @csrf
        <input type="hidden" name="date" value="{{ old('date', request('date')) }}">
        <input type="hidden" name="classroom_id" value="{{ $selectedClassroom }}">
        
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <h2 class="text-xl font-bold text-white"><i class="fas fa-clipboard mr-1"></i> Daftar Siswa ({{ count($students) }} orang)</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="bg-gray-100 border-b">
                            <th class="p-4 text-left font-semibold text-gray-700">No</th>
                            <th class="p-4 text-left font-semibold text-gray-700">Nama Siswa</th>
                            <th class="p-4 text-left font-semibold text-gray-700">Status</th>
                            <th class="p-4 text-left font-semibold text-gray-700">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $index => $student)
                        <tr class="border-b border-gray-100 hover:bg-gray-50 transition">
                            <td class="p-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 bg-gray-100 text-gray-600 rounded-lg font-semibold">{{ $index + 1 }}</span>
                            </td>
                            <td class="p-4 font-medium text-gray-800">{{ $student->full_name }}</td>
                            <td class="p-4">
                                <select name="statuses[{{ $student->id }}]" class="border-2 border-gray-200 p-2 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition">
                                    <option value="hadir">Hadir</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpha">Alpha</option>
                                </select>
                            </td>
                            <td class="p-4">
                                <input type="text" name="notes[{{ $student->id }}]" class="border-2 border-gray-200 p-2 rounded-xl w-full focus:ring-2 focus:ring-green-500 focus:border-transparent transition" placeholder="Opsional">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-6 flex justify-end">
                <button type="submit" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                    <i class="fas fa-save mr-1"></i> Simpan Semua Absensi
                </button>
            </div>
        </div>
    </form>
    @elseif(isset($selectedClassroom))
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