@extends('layouts.admin')
@php
    $user = auth()->user();
    $schoolType = $user->school ? strtoupper($user->school->type) : 'ALL';
    $isSMA = $schoolType === 'SMA';
    $isSMK = $schoolType === 'SMK';
    
    $pageTitle = 'Panduan & Format ';
    if ($isSMA) $pageTitle .= 'Penelitian Ilmiah';
    else if ($isSMK) $pageTitle .= 'Project Akhir';
    else $pageTitle .= 'Penelitian/Project';

    // Live statistics counters
    $totalSma = \App\Models\FinalProjectFormat::whereHas('school', fn($q) => $q->where('type', 'SMA'))->count();
    $totalSmk = \App\Models\FinalProjectFormat::whereHas('school', fn($q) => $q->where('type', 'SMK'))->count();
    $totalAll = $totalSma + $totalSmk;
@endphp

@section('title', $pageTitle . ' - Portal Admin')

@section('content')
<div class="space-y-6">
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-3xl shadow-sm border border-gray-200 px-6 py-5">
        <div class="space-y-1">
            <span class="inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 border border-indigo-100 px-3 py-1 rounded-full text-[10px] font-extrabold uppercase tracking-wider">
                <i class="fas fa-file-pdf"></i> Pengaturan Dokumen
            </span>
            <h1 class="text-lg md:text-xl font-extrabold text-gray-900 tracking-tight">
                Kelola {{ $pageTitle }}
            </h1>
            <p class="text-xs text-gray-500 font-medium">Unggah dan kelola dokumen panduan serta template format penulisan akademik untuk diunduh siswa.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-250 text-emerald-800 px-5 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-sm transition">
            <i class="fas fa-circle-check text-emerald-500 text-lg"></i> 
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-250 text-rose-800 px-5 py-4 rounded-2xl text-sm flex items-center gap-3 shadow-sm transition">
            <i class="fas fa-circle-exclamation text-rose-500 text-lg"></i> 
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Stats Cards Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-1">
                <p class="text-xs text-gray-500 font-extrabold uppercase tracking-wider">Total Berkas Aktif</p>
                <p class="text-3xl font-black text-gray-900">{{ $totalAll }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-xl shadow-inner">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-1">
                <p class="text-xs text-amber-600 font-extrabold uppercase tracking-wider">Panduan Penelitian (SMA)</p>
                <p class="text-3xl font-black text-gray-900">{{ $totalSma }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-600 text-xl shadow-inner">
                <i class="fas fa-book-open"></i>
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 flex items-center justify-between hover:shadow-md transition">
            <div class="space-y-1">
                <p class="text-xs text-emerald-600 font-extrabold uppercase tracking-wider">Panduan Project (SMK)</p>
                <p class="text-3xl font-black text-gray-900">{{ $totalSmk }}</p>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center justify-center text-emerald-600 text-xl shadow-inner">
                <i class="fas fa-cogs"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Upload (Left) --}}
        <div class="bg-white rounded-3xl border border-gray-200 shadow-sm p-6 space-y-5 h-fit">
            <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                <i class="fas fa-upload text-indigo-600"></i> Upload File Panduan Baru
            </h3>
            <form action="{{ route('admin.final-projects.formats.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                @if($isSA)
                    <div>
                        <label for="school_id" class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Sekolah Sasaran</label>
                        <select name="school_id" id="school_id" required class="w-full bg-gray-50 border border-gray-300 rounded-xl px-4 py-2.5 text-sm font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                            <option value="">Pilih Sekolah...</option>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }} ({{ $school->type }})</option>
                            @endforeach
                        </select>
                        @error('school_id')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                @endif

                <div>
                    <label for="title" class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Nama / Judul Berkas</label>
                    <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="Contoh: Format Proposal Rencana Penelitian" class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
                    @error('title')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                    <textarea name="description" id="description" rows="3" placeholder="Tuliskan petunjuk singkat kegunaan naskah atau template..." class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 transition leading-relaxed">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="file_path" class="block text-xs font-black text-gray-800 uppercase tracking-wider mb-1.5">Pilih Berkas Dokumen</label>
                    <input type="file" name="file_path" id="file_path" required class="w-full border border-gray-300 rounded-xl px-3 py-2 text-xs file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-extrabold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-150 transition file:cursor-pointer">
                    <p class="text-[10px] text-gray-500 mt-1.5 leading-relaxed font-bold">Menerima format: PDF, Word, atau ZIP. Maks: 5MB</p>
                    @error('file_path')
                        <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="w-full bg-indigo-650 hover:bg-indigo-750 text-white font-extrabold py-3 rounded-xl text-xs transition-all shadow-md hover:shadow-lg transform active:scale-95 flex items-center justify-center gap-1.5">
                    <i class="fas fa-plus"></i> Upload Panduan
                </button>
            </form>
        </div>

        {{-- Format Files Table (Right) --}}
        <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-200 shadow-sm p-6 space-y-5">
            <h3 class="text-sm font-extrabold text-gray-900 flex items-center gap-2 border-b border-gray-200 pb-3">
                <i class="fas fa-list text-indigo-600"></i> Daftar Berkas Terbitan Sekolah
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-gray-700">
                    <thead class="bg-gray-50 text-gray-500 font-extrabold uppercase tracking-wider text-left border-b border-gray-200 text-xs">
                        <tr>
                            <th class="py-3.5 pl-4">Berkas / Panduan</th>
                            <th class="py-3.5">Sekolah Sasaran</th>
                            <th class="py-3.5">Unduh</th>
                            <th class="py-3.5 pr-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($formats as $f)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="py-4 pl-4 max-w-[240px]">
                                    <div class="flex items-start gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center flex-shrink-0 text-sm border border-rose-100 shadow-sm">
                                            <i class="fas fa-file-pdf"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-extrabold text-gray-900 truncate text-sm" title="{{ $f->title }}">{{ $f->title }}</p>
                                            @if($f->description)
                                                <p class="text-xs text-gray-500 leading-normal line-clamp-2 mt-0.5 font-medium" title="{{ $f->description }}">{{ $f->description }}</p>
                                            @endif
                                            <span class="block text-[10px] text-gray-400 mt-1 font-bold"><i class="fas fa-user-circle mr-0.5"></i> Penerbit: {{ $f->creator->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 font-extrabold text-gray-800">
                                    <div class="space-y-0.5">
                                        <p>{{ $f->school->name }}</p>
                                        <span class="inline-block bg-indigo-50 border border-indigo-200 text-indigo-700 px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider">{{ $f->school->type }}</span>
                                    </div>
                                </td>
                                <td class="py-4">
                                    @if($f->file_path)
                                        <a href="{{ route('public.format.download', $f->id) }}" target="_blank" class="inline-flex items-center gap-1 bg-emerald-50 border border-emerald-250 text-emerald-800 px-3 py-1.5 rounded-lg text-xs font-extrabold hover:bg-emerald-100 transition shadow-sm">
                                            <i class="fas fa-download text-emerald-600"></i> Download
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic font-semibold text-xs">Tanpa File</span>
                                    @endif
                                </td>
                                <td class="py-4 pr-4 text-right">
                                    <form action="{{ route('admin.final-projects.formats.destroy', $f->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berkas format panduan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-extrabold p-2.5 rounded-xl text-xs transition border border-rose-100 shadow-sm transform active:scale-95">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-16 text-center text-gray-500 italic font-bold">
                                    <div class="w-12 h-12 bg-gray-50 border border-gray-200 rounded-full flex items-center justify-center mx-auto mb-3 shadow-inner">
                                        <i class="fas fa-folder-open text-base text-gray-400"></i>
                                    </div>
                                    <p class="text-sm">Belum ada file format panduan yang diupload oleh sekolah.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($formats->hasPages())
                <div class="pt-4 border-t border-gray-200">
                    {{ $formats->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
