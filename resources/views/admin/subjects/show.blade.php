@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-pink-500 to-purple-600 shadow-lg">
                    <span class="text-white text-3xl"><i class="fas fa-graduation-cap mr-1"></i></span>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $subject->subject_name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        @if($subject->subject_code)
                        <span class="px-3 py-1 bg-gradient-to-r from-pink-100 to-purple-100 text-pink-800 rounded-full text-sm font-semibold">
                            {{ $subject->subject_code }}
                        </span>
                        @endif
                        @if($subject->is_active)
                        <span class="flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                            Aktif
                        </span>
                        @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-sm font-semibold">Nonaktif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Informasi Dasar -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Informasi Mata Pelajaran</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-pink-50 text-pink-600">
                            <i class="fas fa-list-ol mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Kode Mata Pelajaran</p>
                            <p class="text-base font-semibold text-gray-900">{{ $subject->subject_code ?: '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                            <i class="fas fa-graduation-cap mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Nama Mata Pelajaran</p>
                            <p class="text-base font-semibold text-gray-900">{{ $subject->subject_name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <i class="fas fa-school mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Sekolah</p>
                            <p class="text-base font-semibold text-gray-900">{{ $subject->school ? $subject->school->name : '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <i class="fas fa-bullseye mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">KKM</p>
                            <p class="text-base font-semibold text-gray-900">{{ $subject->kkm ?? 75 }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Deskripsi -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Deskripsi</h2>
                </div>
                
                <div class="prose max-w-none">
                    @if($subject->description)
                    <p class="text-gray-700 leading-relaxed">{{ $subject->description }}</p>
                    @else
                    <p class="text-gray-400 italic">Tidak ada deskripsi</p>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Stats -->
            <div class="bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Statistik</h3>
                <div class="space-y-4">
                    <div class="bg-white rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">KKM</span>
                            <span class="text-2xl font-bold text-pink-600">{{ $subject->kkm ?? 75 }}</span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-pink-500 to-purple-600 h-2 rounded-full" style="width: {{ ($subject->kkm ?? 75) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.subjects.edit', $subject) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Mata Pelajaran
                    </a>
                    
                    <a href="{{ route('admin.subjects.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                    
                    <form action="{{ route('admin.subjects.destroy', $subject) }}" method="POST" 
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus mata pelajaran ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-medium hover:from-red-700 hover:to-red-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Mata Pelajaran
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection