@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Detail Jadwal Pelajaran</h1>
        <p class="text-gray-600">Informasi lengkap jadwal kelas</p>
    </div>

    <!-- Card Detail -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Header Card with Gradient -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold">{{ $schedule->subject->subject_name ?? '-' }}</h2>
                    <p class="text-indigo-100 mt-1">{{ $schedule->classroom->class_name ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <div class="bg-white/20 rounded-lg px-4 py-2">
                        <div class="text-sm text-indigo-100">{{ $days[$schedule->day_of_week] ?? '-' }}</div>
                        <div class="text-lg font-bold">{{ $schedule->start_time }} - {{ $schedule->end_time }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Body Card -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Guru Pengajar -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Guru Pengajar</h3>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $schedule->teacher->full_name ?? '-' }}</p>
                    </div>
                </div>

                <!-- Kelas -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Kelas</h3>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $schedule->classroom->class_name ?? '-' }}</p>
                        <p class="text-sm text-gray-600">{{ $schedule->classroom->school->name ?? '-' }}</p>
                    </div>
                </div>

                <!-- Hari -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Hari</h3>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $days[$schedule->day_of_week] ?? '-' }}</p>
                    </div>
                </div>

                <!-- Waktu -->
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Waktu</h3>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>
                    </div>
                </div>

                <!-- Ruangan -->
                @if($schedule->room)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-400 to-red-600 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-500">Ruangan</h3>
                        <p class="text-lg font-semibold text-gray-900 mt-1">{{ $schedule->room }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Footer Actions -->
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center">
            <a href="{{ route('admin.schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Kembali
            </a>
            <div class="flex space-x-3">
                <a href="{{ route('admin.schedules.edit', $schedule) }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit Jadwal
                </a>
                <form action="{{ route('admin.schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus jadwal ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection