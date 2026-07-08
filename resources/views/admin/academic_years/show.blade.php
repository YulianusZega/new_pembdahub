@extends('layouts.admin')

@section('title', 'Detail Tahun Ajaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg text-white text-2xl font-bold">
                    <i class="fas fa-calendar-alt mr-1"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $academicYear->year }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        @if($academicYear->is_active)
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Aktif
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold">
                            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                            Tidak Aktif
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Tahun Ajaran -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Informasi Tahun Ajaran</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-calendar-alt mr-1"></i></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Tahun Ajaran</p>
                            <p class="text-base font-semibold text-gray-900">{{ $academicYear->year }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600"><i class="fas fa-calendar mr-1"></i></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Tanggal Mulai</p>
                            <p class="text-base font-semibold text-gray-900">{{ $academicYear->start_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-orange-50 text-orange-600"><i class="fas fa-calendar mr-1"></i></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Tanggal Berakhir</p>
                            <p class="text-base font-semibold text-gray-900">{{ $academicYear->end_date->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="md:col-span-2 flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600"><i class="fas fa-stopwatch"></i></div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-500 mb-1">Durasi Periode</p>
                            <p class="text-base font-semibold text-gray-900">
                                {{ $academicYear->start_date->format('d M Y') }} — {{ $academicYear->end_date->format('d M Y') }}
                                <span class="text-sm text-gray-500">({{ $academicYear->start_date->diffInDays($academicYear->end_date) }} hari)</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-gradient-to-br from-emerald-50 to-green-50 rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Status</h3>
                <div class="flex items-center justify-center">
                    @if($academicYear->is_active)
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-3">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-lg font-bold text-green-800">Tahun Ajaran Aktif</p>
                        <p class="text-sm text-green-600 mt-1">Sedang berjalan</p>
                    </div>
                    @else
                    <div class="text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-3">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <p class="text-lg font-bold text-gray-700">Tidak Aktif</p>
                        <p class="text-sm text-gray-500 mt-1">Belum/Sudah berakhir</p>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.academic-years.edit', $academicYear) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Tahun Ajaran
                    </a>
                    <a href="{{ route('admin.academic-years.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection