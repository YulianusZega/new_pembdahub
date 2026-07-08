@extends('layouts.admin')

@section('title', 'Detail Orang Tua/Wali')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail Orang Tua/Wali</h1>
                <p class="text-gray-600 mt-1">Informasi lengkap orang tua/wali siswa</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Data Orang Tua/Wali -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-bold text-sm">1</div>
                    <h2 class="text-xl font-bold text-gray-900">Data Orang Tua/Wali</h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Nama Lengkap</label>
                        <p class="mt-1 text-gray-900 font-medium">{{ $parent->full_name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Hubungan</label>
                        <div class="mt-1">
                            @if($parent->relation_type == 'ayah')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full"><i class="fas fa-user mr-1"></i> Ayah</span>
                            @elseif($parent->relation_type == 'ibu')
                            <span class="px-3 py-1 bg-pink-100 text-pink-800 text-xs font-semibold rounded-full"><i class="fas fa-user mr-1"></i> Ibu</span>
                            @else
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full"><i class="fas fa-user mr-1"></i> Wali</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Telepon</label>
                        <p class="mt-1 text-gray-900">{{ $parent->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Email</label>
                        <p class="mt-1 text-gray-900">{{ $parent->email ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Pekerjaan</label>
                        <p class="mt-1 text-gray-900">{{ $parent->occupation ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Alamat</label>
                        <p class="mt-1 text-gray-900">{{ $parent->address ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Data Siswa -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                    <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-bold text-sm">2</div>
                    <h2 class="text-xl font-bold text-gray-900">Data Siswa</h2>
                </div>

                @if($parent->student)
                <div class="flex items-center gap-4 p-4 bg-gradient-to-r from-orange-50 to-amber-50 rounded-xl border-2 border-orange-200">
                    <img src="{{ $parent->student->photo_url }}" alt="{{ $parent->student->full_name }}" 
                        class="w-16 h-16 rounded-full object-cover border-2 border-orange-300">
                    <div class="flex-1">
                        <h3 class="text-lg font-bold text-gray-900">{{ $parent->student->full_name }}</h3>
                        <p class="text-sm text-gray-600">NISN: {{ $parent->student->nisn }}</p>
                        @if($parent->student->classroom)
                        <p class="text-sm text-gray-600">Kelas: {{ $parent->student->classroom->class_name }}</p>
                        @endif
                    </div>
                    <a href="{{ route('admin.students.show', $parent->student) }}" 
                        class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-orange-600 to-amber-700 text-white rounded-xl font-medium hover:from-orange-700 hover:to-amber-800 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Detail
                    </a>
                </div>
                @else
                <p class="text-gray-500 text-center py-4">Data siswa tidak tersedia</p>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- User Account -->
            @if($parent->user)
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Akun Portal</h3>
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Email</label>
                        <p class="mt-1 text-gray-900">{{ $parent->user->email }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Role</label>
                        <p class="mt-1">
                            <span class="px-3 py-1 bg-orange-100 text-orange-800 text-xs font-semibold rounded-full">
                                {{ ucfirst($parent->user->role) }}
                            </span>
                        </p>
                    </div>
                    <div>
                        <label class="text-sm font-semibold text-gray-600">Status Akun</label>
                        <p class="mt-1">
                            @if($parent->user->is_active)
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">Aktif</span>
                            @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-800 text-xs font-semibold rounded-full">Non-Aktif</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-xl p-4">
                <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i> Belum memiliki akun portal</p>
            </div>
            @endif

            <!-- Actions -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aksi</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.parents.edit', $parent) }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-700 text-white rounded-xl font-medium hover:from-green-700 hover:to-emerald-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Data
                    </a>
                    <a href="{{ route('admin.parents.index') }}" 
                        class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Kembali
                    </a>
                    <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST"
                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data orang tua/wali ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" 
                            class="flex items-center justify-center gap-2 w-full px-4 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-xl font-medium hover:from-red-700 hover:to-red-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Hapus Data
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
