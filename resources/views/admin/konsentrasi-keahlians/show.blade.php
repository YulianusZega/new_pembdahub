@extends('layouts.admin')

@section('title', 'Detail Konsentrasi Keahlian')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="relative bg-gradient-to-br from-orange-600 via-orange-700 to-amber-800 rounded-2xl shadow-lg p-6 text-white overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">{{ $konsentrasiKeahlian->nama }}</h1>
                <p class="text-orange-100 mt-1">Detail Konsentrasi Keahlian SMK</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.konsentrasi-keahlians.edit', $konsentrasiKeahlian) }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl font-semibold transition-all">
                    <i class="fas fa-pencil-alt"></i> Edit
                </a>
                <a href="{{ route('admin.konsentrasi-keahlians.index') }}" class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white px-5 py-2.5 rounded-xl font-semibold transition-all">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-bold text-gray-800 mb-5 pb-3 border-b border-gray-100">Informasi Konsentrasi Keahlian</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1">Kode</label>
                <p class="text-gray-800 font-medium">
                    <span class="inline-flex items-center px-3 py-1 bg-orange-100 text-orange-700 rounded-lg text-sm font-bold">{{ $konsentrasiKeahlian->kode }}</span>
                </p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1">Nama Konsentrasi</label>
                <p class="text-gray-800 font-medium">{{ $konsentrasiKeahlian->nama }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1">Program Keahlian</label>
                <p class="text-gray-800 font-medium">{{ $konsentrasiKeahlian->programKeahlian->nama ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1">Sekolah</label>
                <p class="text-gray-800 font-medium">{{ $konsentrasiKeahlian->programKeahlian->school->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-500 mb-1">Status</label>
                @if($konsentrasiKeahlian->is_active)
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-semibold">
                        <i class="fas fa-check-circle text-xs"></i> Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-semibold">
                        <i class="fas fa-times-circle text-xs"></i> Nonaktif
                    </span>
                @endif
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-500 mb-1">Deskripsi</label>
                <p class="text-gray-800">{{ $konsentrasiKeahlian->deskripsi ?: '-' }}</p>
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.konsentrasi-keahlians.edit', $konsentrasiKeahlian) }}" class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors shadow-sm">
            <i class="fas fa-pencil-alt"></i> Edit Konsentrasi
        </a>
        <form action="{{ route('admin.konsentrasi-keahlians.destroy', $konsentrasiKeahlian) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus konsentrasi keahlian ini?')">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors shadow-sm">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </form>
    </div>
</div>
@endsection
