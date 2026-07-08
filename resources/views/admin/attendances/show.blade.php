@extends('layouts.admin')

@section('title', 'Detail Absensi - Admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail Absensi</h1>
            <p class="text-gray-600">Informasi lengkap kehadiran</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-chart-bar mr-1"></i> Data Absensi</h2>
        </div>
        <div class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-calendar-alt mr-1"></i> Tanggal</label>
                    <p class="text-gray-800 font-medium">{{ $attendance->date ? $attendance->date->format('d M Y') : '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-user-graduate mr-1"></i> Siswa</label>
                    <p class="text-gray-800 font-medium">{{ $attendance->student->full_name ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-school mr-1"></i> Kelas</label>
                    <p class="text-gray-800 font-medium">{{ $attendance->classroom->class_name ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-chart-bar mr-1"></i> Status</label>
                    <p>
                        @if($attendance->status == 'hadir')
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium"><i class="fas fa-check-circle text-green-500 mr-1"></i> Hadir</span>
                        @elseif($attendance->status == 'terlambat')
                        <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm font-medium"><i class="fas fa-clock text-orange-500 mr-1"></i> Terlambat</span>
                        @elseif($attendance->status == 'izin')
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium"><i class="fas fa-edit mr-1"></i> Izin</span>
                        @elseif($attendance->status == 'sakit')
                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-sm font-medium"><i class="fas fa-thermometer mr-1"></i> Sakit</span>
                        @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium"><i class="fas fa-times-circle text-red-500 mr-1"></i> Alpa</span>
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-clock mr-1"></i> Waktu Masuk</label>
                    <p class="text-gray-800 font-medium">{{ $attendance->time_in ?? '--:--' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-sign-out-alt mr-1"></i> Waktu Keluar</label>
                    <p class="text-gray-800 font-medium">{{ $attendance->time_out ?? '--:--' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-wifi mr-1"></i> Metode</label>
                    <p>
                        @if($attendance->recorded_via == 'rfid')
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 rounded-md text-xs font-bold"><i class="fas fa-id-card mr-1"></i> RFID</span>
                        @elseif($attendance->recorded_via == 'qr_gps')
                        <span class="px-2 py-0.5 bg-cyan-100 text-cyan-700 rounded-md text-xs font-bold"><i class="fas fa-map-marker-alt mr-1"></i> GPS</span>
                        @else
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded-md text-xs font-bold"><i class="fas fa-pen mr-1"></i> Manual</span>
                        @endif
                    </p>
                </div>
                @if($attendance->notes)
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-sticky-note mr-1"></i> Catatan</label>
                    <p class="text-gray-800 font-medium">{{ $attendance->notes }}</p>
                </div>
                @endif
            </div>

            @if($attendance->attachment)
            <div class="mt-4 border-t border-gray-100 pt-4">
                <label class="block text-sm font-semibold text-gray-500 mb-2"><i class="fas fa-paperclip mr-1"></i> Lampiran</label>
                <a href="{{ asset('storage/' . $attendance->attachment) }}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1 text-sm">
                    <i class="fas fa-download"></i> {{ $attendance->attachment_name ?? 'Lihat Dokumen' }}
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('admin.attendances.edit', $attendance) }}" class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
            <i class="fas fa-pencil-alt mr-1"></i> Edit
        </a>
        <a href="{{ route('admin.attendances.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
            ← Kembali
        </a>
    </div>
</div>
@endsection