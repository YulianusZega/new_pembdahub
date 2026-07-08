@extends('layouts.guru')

@section('title', 'Tambah Modul - ' . $course->name)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-4">
        <a href="{{ route('guru.lms.show', $course->id) }}" class="w-10 h-10 bg-white shadow-sm border border-gray-100 rounded-xl flex items-center justify-center text-gray-400 hover:text-emerald-600 transition-all hover:shadow-md">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="text-xl font-bold text-gray-800">Tambah Modul Baru</h2>
            <p class="text-xs text-gray-500 mt-0.5">{{ $course->name }}</p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-8 py-6">
            <h3 class="text-white font-bold tracking-wide flex items-center gap-2">
                <i class="fas fa-layer-group"></i> Detail Modul
            </h3>
            <p class="text-white/80 text-xs mt-1">Gunakan modul untuk mengelompokkan materi pembelajaran secara sistematis.</p>
        </div>

        <form action="{{ route('guru.lms.modules.store', $course->id) }}" method="POST" class="p-8 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">Judul Modul</label>
                    <input type="text" name="title" required placeholder="Contoh: Modul 1 - Pengenalan Dasar" 
                           class="w-full border border-gray-200 rounded-xl px-5 py-3.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm">
                    @error('title') <p class="text-rose-500 text-[10px] mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">Deskripsi (Opsional)</label>
                    <textarea name="description" rows="4" placeholder="Jelaskan apa yang akan dipelajari di modul ini..." 
                              class="w-full border border-gray-200 rounded-xl px-5 py-3.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all shadow-sm"></textarea>
                    @error('description') <p class="text-rose-500 text-[10px] mt-1 ml-1 font-bold">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5 ml-1">Warna Tema Modul</label>
                    <div class="grid grid-cols-4 sm:grid-cols-8 gap-3 mt-2">
                        @foreach(['indigo', 'emerald', 'rose', 'amber', 'blue', 'purple', 'cyan', 'orange'] as $color)
                        <label class="relative cursor-pointer group">
                            <input type="radio" name="color" value="{{ $color }}" class="peer sr-only" {{ $loop->first ? 'checked' : '' }}>
                            <div class="w-full aspect-square rounded-xl border-2 border-transparent peer-checked:border-emerald-500 peer-checked:ring-2 peer-checked:ring-emerald-100 transition-all flex items-center justify-center bg-{{ $color }}-500 shadow-sm group-hover:scale-105">
                                <i class="fas fa-check text-white opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                            </div>
                            <span class="block text-[8px] text-center mt-1 font-bold text-gray-400 uppercase tracking-tighter">{{ $color }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-50 flex gap-3">
                <a href="{{ route('guru.lms.show', $course->id) }}" 
                   class="flex-1 px-6 py-3.5 rounded-xl font-bold bg-gray-50 text-gray-400 hover:text-gray-600 transition-all uppercase tracking-widest text-xs text-center">
                    Batal
                </a>
                <button type="submit" 
                        class="flex-[2] px-6 py-3.5 rounded-xl font-bold bg-emerald-600 text-white hover:bg-emerald-700 transition-all shadow-lg hover:shadow-emerald-200 uppercase tracking-widest text-xs">
                    Simpan Modul Baru
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
