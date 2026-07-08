@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg text-white text-2xl font-bold">
                    {{ strtoupper(substr($school->name, 0, 2)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $school->name }}</h1>
                    <div class="flex items-center gap-3 mt-2">
                        <span class="px-3 py-1 rounded-full text-sm font-semibold @if($school->type == 'SMA') bg-blue-100 text-blue-800 @elseif($school->type == 'SMK') bg-purple-100 text-purple-800 @else bg-gray-100 text-gray-800 @endif">
                            {{ $school->type }}
                        </span>
                        @if($school->is_active)
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
            <!-- Identitas -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Identitas Sekolah</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <i class="fas fa-school mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Nama Sekolah</p>
                            <p class="text-base font-semibold text-gray-900 break-words">{{ $school->name }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                            <i class="fas fa-clipboard mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Tipe</p>
                            <p class="text-base font-semibold text-gray-900">{{ $school->type }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <i class="fas fa-list-ol mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">NPSN</p>
                            <p class="text-base font-semibold text-gray-900">{{ $school->npsn ?: '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-yellow-50 text-yellow-600">
                            <i class="fas fa-user mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Kepala Sekolah</p>
                            @if($school->principal)
                                <p class="text-base font-semibold text-gray-900">{{ $school->principal->full_name }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $school->principal->teacher_code }}</p>
                            @else
                                <p class="text-base font-semibold text-gray-400">Belum ditentukan</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Alamat -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-green-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Alamat & Lokasi</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <i class="fas fa-map-marker-alt mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Alamat Lengkap</p>
                            <p class="text-base font-semibold text-gray-900 break-words">{{ $school->address ?: '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                <i class="fas fa-city mr-1"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-500 mb-1">Kota</p>
                                <p class="text-base font-semibold text-gray-900">{{ $school->city ?: '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                                <i class="fas fa-map mr-1"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-500 mb-1">Provinsi</p>
                                <p class="text-base font-semibold text-gray-900">{{ $school->province ?: '-' }}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-pink-50 text-pink-600">
                                <i class="fas fa-mailbox mr-1"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-500 mb-1">Kode Pos</p>
                                <p class="text-base font-semibold text-gray-900">{{ $school->postal_code ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="md:col-span-2 p-4 bg-blue-50 rounded-xl border border-blue-100 mt-4">
                            <p class="text-xs font-bold text-blue-700 uppercase tracking-wider flex items-center gap-2 mb-3">
                                <i class="fas fa-satellite"></i> Koordinat GPS (Untuk Absensi)
                            </p>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <p class="text-xs text-blue-500 font-semibold uppercase">Latitude</p>
                                    <p class="text-sm font-mono font-bold text-blue-900">{{ $school->latitude ?: '0.00000000' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-blue-500 font-semibold uppercase">Longitude</p>
                                    <p class="text-sm font-mono font-bold text-blue-900">{{ $school->longitude ?: '0.00000000' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Kontak -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-purple-600 shadow-md">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">Kontak</h2>
                </div>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                            <i class="fas fa-phone mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Telepon</p>
                            <p class="text-base font-semibold text-gray-900 break-words">{{ $school->phone ?: '-' }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                            <i class="fas fa-envelope mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Email</p>
                            @if($school->email)
                            <a href="mailto:{{ $school->email }}" class="text-base font-semibold text-blue-600 hover:text-blue-800 break-words">{{ $school->email }}</a>
                            @else
                            <p class="text-base font-semibold text-gray-900">-</p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-lg bg-green-50 text-green-600">
                            <i class="fas fa-globe mr-1"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-500 mb-1">Website</p>
                            @if($school->website)
                            <a href="{{ $school->website }}" target="_blank" class="text-base font-semibold text-blue-600 hover:text-blue-800 break-words">{{ $school->website }}</a>
                            @else
                            <p class="text-base font-semibold text-gray-900">-</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.schools.edit', $school) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-green-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Sekolah
                    </a>
                    
                    <a href="{{ route('admin.schools.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali ke Daftar
                    </a>
                    
                    <form action="{{ route('admin.schools.destroy', $school) }}" method="POST" 
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus sekolah ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-medium hover:from-red-700 hover:to-red-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Sekolah
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection