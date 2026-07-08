@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Jadwal Pelajaran</h1>
        <a href="{{ route('admin.schedules.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center gap-2 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Tambah Jadwal
        </a>
    </div>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded">
        <div class="flex items-center">
            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            {{ session('success') }}
        </div>
    </div>
    @endif

    <!-- Filter Section -->
    <form method="GET" class="mb-6 bg-white p-4 rounded-lg shadow">
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <!-- Filter Unit Sekolah -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Unit Sekolah</label>
                @if(auth()->user()->isSuperAdmin())
                    <select name="school_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                        <option value="">-- Semua Sekolah --</option>
                        @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full border-2 border-indigo-200 rounded-lg px-4 py-2 bg-indigo-50 text-gray-800 font-semibold">
                        <i class="fas fa-school mr-1"></i> {{ auth()->user()->school->name }}
                    </div>
                @endif
            </div>

            <!-- Filter Tahun Pelajaran -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tahun Pelajaran</label>
                <select name="academic_year_id" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                        {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cari</label>
                <input type="text" name="q" value="{{ request('q') }}"
                    placeholder="Cari kelas, mapel, guru..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Filter Hari</label>
                <select name="day" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Semua Hari --</option>
                    @foreach($days as $val => $label)
                    <option value="{{ $val }}" {{ request('day') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
                    Filter
                </button>
            </div>
        </div>
    </form>

    <!-- Jadwal Table (Matrix Format) -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="p-3 text-left font-semibold border border-indigo-500" style="width: 80px;">Hari</th>
                        <th class="p-3 text-left font-semibold border border-indigo-500" style="width: 100px;">Waktu</th>
                        @foreach($classrooms as $classroom)
                        <th class="p-1 text-center font-semibold border border-indigo-500 text-xs" style="width: 100px; min-width: 100px; max-width: 100px;">
                            {{ $classroom->class_name }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @php
                    $dayMap = [
                    1 => 'Senin',
                    2 => 'Selasa',
                    3 => 'Rabu',
                    4 => 'Kamis',
                    5 => 'Jumat',
                    6 => 'Sabtu'
                    ];
                    $currentDay = null;
                    @endphp
                    @forelse($scheduleMatrix as $key => $schedules)
                    @php
                    list($dayNum, $timeRange) = explode('|', $key);
                    $showDay = ($currentDay !== $dayNum);
                    $currentDay = $dayNum;
                    $rowspan = $scheduleMatrix->filter(function($item, $k) use ($dayNum) {
                    return str_starts_with($k, $dayNum . '|');
                    })->count();
                    @endphp
                    <tr class="hover:bg-gray-50 transition">
                        @if($showDay)
                        <td class="p-3 border border-gray-300 bg-gray-50 font-semibold text-gray-800 align-top" rowspan="{{ $rowspan }}">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium @if($dayNum == 1) bg-blue-100 text-blue-800 @elseif($dayNum == 2) bg-green-100 text-green-800 @elseif($dayNum == 3) bg-yellow-100 text-yellow-800 @elseif($dayNum == 4) bg-purple-100 text-purple-800 @elseif($dayNum == 5) bg-pink-100 text-pink-800 @else bg-gray-100 text-gray-800 @endif">
                                {{ $dayMap[$dayNum] ?? '-' }}
                            </span>
                        </td>
                        @endif
                        <td class="p-3 border border-gray-300 text-gray-800 font-medium whitespace-nowrap">
                            {{ date('H:i', strtotime(explode('-', $timeRange)[0])) }} -
                            {{ date('H:i', strtotime(explode('-', $timeRange)[1])) }}
                        </td>
                        @foreach($classrooms as $classroom)
                        @php
                        $schedule = $schedules->where('classroom_id', $classroom->id)->first();
                        @endphp
                        <td class="p-1 border border-gray-300 text-center text-xs relative group" style="width: 100px; min-width: 100px; max-width: 100px;">
                            @if($schedule)
                            <div class="bg-gradient-to-br from-indigo-50 to-purple-50 p-1 rounded hover:shadow-md transition cursor-pointer">
                                <div class="font-semibold text-gray-800 text-xs">
                                    {{ $schedule->subject->subject_name ?? '-' }}
                                </div>
                                <div class="text-xs text-gray-600 mt-1">
                                    @php
                                    $teacherName = $schedule->teacher->full_name ?? '';
                                    $words = explode(' ', $teacherName);
                                    $initials = '';
                                    foreach($words as $word) {
                                    $initials .= strtoupper(substr($word, 0, 1));
                                    }
                                    @endphp
                                    ({{ $initials }})
                                </div>
                                <!-- Action Menu on Hover -->
                                <div class="absolute top-1 right-1 hidden group-hover:flex gap-1">
                                    <a href="{{ route('admin.schedules.show', $schedule) }}"
                                        class="p-1 bg-blue-500 hover:bg-blue-600 text-white rounded shadow-lg"
                                        title="Lihat">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.schedules.edit', $schedule) }}"
                                        class="p-1 bg-green-500 hover:bg-green-600 text-white rounded shadow-lg"
                                        title="Edit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Yakin hapus jadwal {{ $schedule->subject->subject_name ?? '' }} - {{ $schedule->teacher->full_name ?? '' }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1 bg-red-500 hover:bg-red-600 text-white rounded shadow-lg"
                                            title="Hapus">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @else
                            <span class="text-gray-300">-</span>
                            @endif
                        </td>
                        @endforeach

                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($classrooms) + 2 }}" class="p-8 text-center text-gray-500">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-16 h-16 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-lg font-medium">Belum ada jadwal pelajaran</p>
                                <p class="text-sm">Pilih sekolah atau klik tombol "Tambah Jadwal" untuk membuat jadwal baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Info -->
    <div class="mt-6">
        <p class="text-sm text-gray-600">Menampilkan {{ count($scheduleMatrix) }} slot jadwal dengan {{ count($classrooms) }} kelas</p>
    </div>
</div>
@endsection