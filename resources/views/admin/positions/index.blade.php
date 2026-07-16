@extends('layouts.admin')

@section('title', 'Manajemen Jabatan & Tarif')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Manajemen Jabatan & Tarif</h1>
                    <p class="text-gray-600 mt-1">Kelola daftar jabatan dan tunjangan per sekolah</p>
                </div>
            </div>
            <a href="{{ route('admin.master.positions.create', request()->query()) }}" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Jabatan
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-xl p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-red-800 font-medium">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-md p-6 mb-6">
        <form method="GET" action="{{ route('admin.master.positions.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            @if(auth()->user()->isSuperAdmin())
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Sekolah</label>
                <select name="school_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua</option>
                    <option value="global" {{ request('school_id') == 'global' ? 'selected' : '' }}>Global (Semua Sekolah)</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-folder mr-1"></i> Kategori</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    <option value="structural" {{ request('category') == 'structural' ? 'selected' : '' }}>Struktural</option>
                    <option value="functional" {{ request('category') == 'functional' ? 'selected' : '' }}>Fungsional</option>
                    <option value="staff" {{ request('category') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="support" {{ request('category') == 'support' ? 'selected' : '' }}>Support</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-bolt mr-1"></i> Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-search mr-1"></i> Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500" 
                    placeholder="Nama/Kode Jabatan...">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" 
                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition-colors">
                    Filter
                </button>
                <a href="{{ route('admin.master.positions.index') }}" 
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-xl font-medium hover:bg-gray-300 transition-colors">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Positions Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-bold uppercase">No</th>
                        <th class="px-6 py-4 text-left text-sm font-bold uppercase">Jabatan</th>
                        <th class="px-6 py-4 text-left text-sm font-bold uppercase">Kategori</th>
                        <th class="px-6 py-4 text-left text-sm font-bold uppercase">Sekolah</th>
                        @if(auth()->user()->isKetuaYayasan() || auth()->user()->hasRole('bendahara'))
                        <th class="px-6 py-4 text-right text-sm font-bold uppercase">Tunjangan/Bulan</th>
                        @endif
                        <th class="px-6 py-4 text-center text-sm font-bold uppercase">Menjabat</th>
                        <th class="px-6 py-4 text-center text-sm font-bold uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-bold uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($positions as $index => $position)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-700 font-bold">
                                {{ $positions->firstItem() + $index }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">{{ $position->position_name }}</span>
                                <span class="px-2 py-0.5 bg-indigo-100 text-indigo-700 text-xs font-semibold rounded inline-block w-fit mt-1">
                                    {{ $position->position_code }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $categoryColors = [
                                    'structural' => 'bg-purple-100 text-purple-700',
                                    'functional' => 'bg-blue-100 text-blue-700',
                                    'staff' => 'bg-green-100 text-green-700',
                                    'support' => 'bg-orange-100 text-orange-700',
                                ];
                                $categoryNames = [
                                    'structural' => 'Struktural',
                                    'functional' => 'Fungsional',
                                    'staff' => 'Staff',
                                    'support' => 'Support',
                                ];
                            @endphp
                            <span class="px-3 py-1 {{ $categoryColors[$position->position_category] ?? 'bg-gray-100 text-gray-700' }} text-sm font-semibold rounded-lg">
                                {{ $categoryNames[$position->position_category] ?? $position->position_category }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($position->school_id)
                                <span class="px-3 py-1 bg-indigo-100 text-indigo-700 text-sm font-semibold rounded-lg flex items-center gap-1 w-fit">
                                    <i class="fas fa-school mr-1"></i> {{ $position->school->name }}
                                </span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg flex items-center gap-1 w-fit">
                                    <i class="fas fa-globe mr-1"></i> Global
                                </span>
                            @endif
                        </td>
                        @if(auth()->user()->isKetuaYayasan() || auth()->user()->hasRole('bendahara'))
                        <td class="px-6 py-4 text-right">
                            <div class="font-bold text-xl text-green-600">
                                Rp {{ number_format($position->allowance_amount, 0, ',', '.') }}
                            </div>
                        </td>
                        @endif
                        <td class="px-6 py-4 text-center">
                            @if($position->activeEmployees->count() > 0)
                                <div class="group relative inline-block cursor-help">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-sm font-bold rounded-full border border-blue-200">
                                        {{ $position->activeEmployees->count() }} Orang
                                    </span>
                                    <!-- Tooltip with names -->
                                    <div class="opacity-0 w-max invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 absolute z-50 bottom-full left-1/2 -translate-x-1/2 mb-2 p-3 bg-gray-800 text-white text-xs rounded-xl shadow-xl">
                                        <div class="font-bold border-b border-gray-600 pb-1 mb-1">Daftar Penjabat:</div>
                                        <ul class="text-left space-y-1">
                                            @foreach($position->activeEmployees as $emp)
                                                <li>• {{ $emp->full_name ?? '-' }}</li>
                                            @endforeach
                                        </ul>
                                        <!-- Arrow -->
                                        <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-800"></div>
                                    </div>
                                </div>
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-500 text-sm font-medium rounded-full">
                                    0
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($position->is_active)
                                <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-bold rounded-full">
                                    <i class="fas fa-check text-green-500 mr-1"></i> Aktif
                                </span>
                            @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-bold rounded-full">
                                    <i class="fas fa-times text-red-500 mr-1"></i> Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.master.positions.edit', array_merge(['position' => $position->id], request()->query())) }}" 
                                    class="p-2 bg-amber-100 text-amber-700 rounded-lg hover:bg-amber-200 transition-colors" 
                                    title="Edit">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.master.positions.destroy', $position->id) }}" 
                                    method="POST" 
                                    class="inline"
                                    onsubmit="return confirm('Yakin ingin menghapus jabatan {{ $position->position_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    @foreach(request()->only(['school_id', 'category', 'status', 'search', 'page']) as $key => $value)
                                        @if(!is_null($value))
                                            <input type="hidden" name="f_{{ $key }}" value="{{ $value }}">
                                        @endif
                                    @endforeach
                                    <button type="submit" 
                                        class="p-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors" 
                                        title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-4">
                                <div class="w-20 h-20 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
                                    <svg class="w-10 h-10 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xl font-bold text-gray-900 mb-1">Belum Ada Jabatan</p>
                                    <p class="text-gray-600">Silakan tambah jabatan baru dengan klik tombol di atas</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($positions->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $positions->links() }}
        </div>
        @endif
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="text-sm font-semibold opacity-90">Total Jabatan</div>
            <div class="text-4xl font-bold mt-2">{{ $totalPositions }}</div>
        </div>
        <div class="bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="text-sm font-semibold opacity-90">Jabatan Aktif</div>
            <div class="text-4xl font-bold mt-2">{{ $activePositions }}</div>
        </div>
        <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="text-sm font-semibold opacity-90">Rata-rata Tunjangan</div>
            <div class="text-2xl font-bold mt-2">Rp {{ number_format($avgAllowance, 0, ',', '.') }}</div>
        </div>
        <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="text-sm font-semibold opacity-90">Total Budget/Bulan</div>
            <div class="text-2xl font-bold mt-2">Rp {{ number_format($totalBudget, 0, ',', '.') }}</div>
        </div>
    </div>
</div>
@endsection
