@extends('layouts.admin')

@section('title', 'Konsentrasi Keahlian SMK')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="relative bg-gradient-to-br from-orange-600 via-orange-700 to-amber-800 rounded-2xl shadow-lg p-6 text-white overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -translate-y-1/2 translate-x-1/2"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 bg-white/10 rounded-full translate-y-1/2 -translate-x-1/2"></div>
        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold">Konsentrasi Keahlian SMK</h1>
                <p class="text-orange-100 mt-1">Kelola data konsentrasi keahlian untuk program SMK</p>
            </div>
            <a href="{{ route('admin.konsentrasi-keahlians.create') }}" class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white px-5 py-2.5 rounded-xl font-semibold transition-all shadow-lg">
                <i class="fas fa-plus"></i> Tambah Konsentrasi
            </a>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('admin.konsentrasi-keahlians.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Program Keahlian</label>
                <select name="program_keahlian_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Program</option>
                    @foreach($programKeahlians as $pk)
                        <option value="{{ $pk->id }}" {{ request('program_keahlian_id') == $pk->id ? 'selected' : '' }}>{{ $pk->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..." class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" />
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('admin.konsentrasi-keahlians.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-sync-alt"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-orange-500 to-amber-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-layer-group text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Konsentrasi</p>
                    <p class="text-xl font-bold text-gray-800">{{ $konsentrasiKeahlians->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-check-circle text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Aktif</p>
                    <p class="text-xl font-bold text-gray-800">{{ $konsentrasiKeahlians->where('is_active', true)->count() }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gradient-to-br from-red-500 to-rose-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-times-circle text-white text-sm"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Nonaktif</p>
                    <p class="text-xl font-bold text-gray-800">{{ $konsentrasiKeahlians->where('is_active', false)->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @if($konsentrasiKeahlians->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b border-gray-200">
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Kode</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Konsentrasi</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Program Keahlian</th>
                            <th class="text-left px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Sekolah</th>
                            <th class="text-center px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="text-center px-6 py-4 text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($konsentrasiKeahlians as $index => $kk)
                            <tr class="hover:bg-orange-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-1 bg-orange-100 text-orange-700 rounded-lg text-xs font-bold">{{ $kk->kode }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-800">{{ $kk->nama }}</p>
                                    @if($kk->deskripsi)
                                        <p class="text-xs text-gray-500 mt-0.5 line-clamp-1">{{ $kk->deskripsi }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $kk->programKeahlian->nama ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $kk->programKeahlian->school->name ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($kk->is_active)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check-circle text-xs"></i> Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-100 text-red-700 rounded-full text-xs font-semibold">
                                            <i class="fas fa-times-circle text-xs"></i> Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.konsentrasi-keahlians.show', $kk) }}" class="w-8 h-8 bg-blue-100 hover:bg-blue-200 text-blue-600 rounded-lg flex items-center justify-center transition-colors" title="Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.konsentrasi-keahlians.edit', $kk) }}" class="w-8 h-8 bg-amber-100 hover:bg-amber-200 text-amber-600 rounded-lg flex items-center justify-center transition-colors" title="Edit">
                                            <i class="fas fa-pencil-alt text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.konsentrasi-keahlians.destroy', $kk) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus konsentrasi keahlian ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition-colors" title="Hapus">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-16 text-gray-400">
                <i class="fas fa-layer-group text-5xl mb-4"></i>
                <p class="text-xl font-bold text-gray-600 mb-2">Tidak ada data konsentrasi keahlian</p>
                <p class="text-sm text-gray-500">Silakan tambah konsentrasi keahlian baru atau ubah filter pencarian.</p>
            </div>
        @endif
    </div>
</div>
@endsection
