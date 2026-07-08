@extends('layouts.admin')

@section('title', 'Data Orang Tua/Wali')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Data Orang Tua/Wali</h1>
                    <p class="text-gray-600 mt-1">Manajemen data orang tua & wali siswa</p>
                </div>
            </div>
            <a href="{{ route('admin.parents.create') }}" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-700 text-white rounded-xl font-medium hover:from-orange-700 hover:to-amber-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Orang Tua/Wali
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form action="{{ route('admin.parents.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-users mr-1"></i> Hubungan</label>
                <select name="relation_type" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
                    <option value="">Semua Hubungan</option>
                    <option value="ayah" {{ request('relation_type') == 'ayah' ? 'selected' : '' }}>Ayah</option>
                    <option value="ibu" {{ request('relation_type') == 'ibu' ? 'selected' : '' }}>Ibu</option>
                    <option value="wali" {{ request('relation_type') == 'wali' ? 'selected' : '' }}>Wali</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-search mr-1"></i> Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, Telepon, Email..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-orange-600 to-amber-700 text-white rounded-xl font-medium hover:from-orange-700 hover:to-amber-800 transition-all">
                    Filter
                </button>
                <a href="{{ route('admin.parents.index') }}" class="px-6 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Parents Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Nama</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Hubungan</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Siswa</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Telepon</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Email</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($parents as $index => $parent)
                    <tr class="hover:bg-orange-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-orange-100 text-orange-700 font-bold text-sm">
                                {{ $parents->firstItem() + $index }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-semibold text-gray-900">{{ $parent->full_name }}</div>
                            <div class="text-sm text-gray-500">{{ $parent->occupation ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($parent->relation_type == 'ayah')
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full"><i class="fas fa-user mr-1"></i> Ayah</span>
                            @elseif($parent->relation_type == 'ibu')
                            <span class="px-3 py-1 bg-pink-100 text-pink-800 text-xs font-semibold rounded-full"><i class="fas fa-user mr-1"></i> Ibu</span>
                            @else
                            <span class="px-3 py-1 bg-purple-100 text-purple-800 text-xs font-semibold rounded-full"><i class="fas fa-user mr-1"></i> Wali</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-700">{{ $parent->student->full_name ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $parent->phone ?? '-' }}</td>
                        <td class="px-6 py-4 text-gray-700">{{ $parent->email ?? '-' }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.parents.show', $parent) }}" 
                                    class="flex items-center justify-center w-9 h-9 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg hover:scale-110 transition-transform shadow-md"
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.parents.edit', $parent) }}" 
                                    class="flex items-center justify-center w-9 h-9 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg hover:scale-110 transition-transform shadow-md"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.parents.destroy', $parent) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data orang tua/wali ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                        class="flex items-center justify-center w-9 h-9 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-lg hover:scale-110 transition-transform shadow-md"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-500">
                                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                <p class="text-lg font-medium">Tidak ada data orang tua/wali</p>
                                <p class="text-sm mt-1">Silakan tambahkan data baru</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($parents->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $parents->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
