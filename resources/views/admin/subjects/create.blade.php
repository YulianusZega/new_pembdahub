@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-pink-500 to-pink-600 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Mata Pelajaran</h1>
                <p class="text-gray-600 mt-1">Lengkapi informasi mata pelajaran baru</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm animate-fade-in">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-1">Terdapat beberapa kesalahan:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.subjects.store') }}" method="POST">
        @csrf
        
        <!-- Informasi Mata Pelajaran -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-pink-500 to-pink-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Dasar</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-school mr-1"></i> Sekolah <span class="text-red-500">*</span>
                        </span>
                    </label>
                    @if(auth()->user()->isSuperAdmin())
                        <select name="school_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all">
                            <option value="">-- Pilih Sekolah --</option>
                            @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                            @endforeach
                        </select>
                    @else
                        <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
                        <div class="w-full px-4 py-3 border-2 border-pink-200 rounded-xl bg-pink-50 text-gray-800 font-semibold">
                            {{ auth()->user()->school->name }}
                        </div>
                    @endif
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-list-ol mr-1"></i> Kode Mata Pelajaran
                        </span>
                    </label>
                    <input type="text" name="subject_code" value="{{ old('subject_code') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                        placeholder="Contoh: MAT-001">
                    <p class="text-xs text-gray-500 mt-1">Kode unik untuk identifikasi mata pelajaran</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-graduation-cap mr-1"></i> Nama Mata Pelajaran <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input type="text" name="subject_name" value="{{ old('subject_name') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                        placeholder="Contoh: Matematika" required>
                </div>
            </div>
        </div>

        <!-- Deskripsi -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 text-white font-bold text-sm">
                    2
                </div>
                <h2 class="text-xl font-bold text-gray-900">Deskripsi & Detail</h2>
            </div>
            
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-edit mr-1"></i> Deskripsi
                        </span>
                    </label>
                    <textarea name="description" rows="4" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                        placeholder="Jelaskan tentang mata pelajaran ini...">{{ old('description') }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <span class="flex items-center gap-2">
                            <i class="fas fa-bullseye mr-1"></i> KKM (Kriteria Ketuntasan Minimal)
                        </span>
                    </label>
                    <div class="relative">
                        <input type="number" name="kkm" value="{{ old('kkm', 75) }}" 
                            min="0" max="100" step="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent transition-all" 
                            placeholder="75">
                        <span class="absolute right-4 top-3 text-gray-500 text-sm">/ 100</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Nilai minimal untuk dinyatakan tuntas (0-100)</p>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 justify-end bg-white rounded-2xl shadow-lg p-6">
            <a href="{{ route('admin.subjects.index') }}" 
                class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-all">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </span>
            </a>
            <button type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-pink-600 to-pink-700 text-white rounded-xl font-medium hover:from-pink-700 hover:to-pink-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Mata Pelajaran
                </span>
            </button>
        </div>
    </form>
</div>
@endsection