@extends('layouts.admin')

@section('title', 'Tahun Ajaran')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-green-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Tahun Ajaran</h1>
                    <p class="text-gray-600 mt-1">Kelola tahun ajaran untuk semua sekolah</p>
                </div>
            </div>
            <a href="{{ route('admin.academic-years.create') }}" 
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-700 text-white rounded-xl font-semibold text-sm hover:from-emerald-700 hover:to-green-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Tahun Ajaran
            </a>
        </div>
    </div>

    <!-- Menu Tabs -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 inline-flex flex-wrap gap-1">
            <a href="{{ route('admin.academic-years.index') }}" 
               class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.academic-years.*') ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran
            </a>
            <a href="{{ route('admin.semesters.index') }}" 
               class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.semesters.*') ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-clock mr-2"></i> Semester
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" class="flex items-center gap-4">
            <div class="flex-1">
                <input type="text" name="q" value="{{ request('q') }}" 
                    placeholder="Cari tahun ajaran..."
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
            </div>
            <button type="submit" 
                class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-green-700 text-white rounded-xl font-semibold text-sm hover:from-emerald-700 hover:to-green-800 shadow-md transition-all">
                Cari
            </button>
            @if(request('q'))
            <a href="{{ route('admin.academic-years.index') }}" 
                class="px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition-all">
                Reset
            </a>
            @endif
        </form>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-green-800 font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <span class="text-red-800 font-medium">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tahun</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-50">
                    @forelse($academicYears as $ay)
                    <tr class="hover:bg-gray-50 transition-colors" data-year-id="{{ $ay->id }}">
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 font-semibold text-sm">
                                {{ ($academicYears->currentPage() - 1) * $academicYears->perPage() + $loop->iteration }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $yearColors = [
                                    0 => 'bg-blue-50 text-blue-700 border border-blue-200',
                                    1 => 'bg-amber-50 text-amber-700 border border-amber-200',
                                    2 => 'bg-purple-50 text-purple-700 border border-purple-200',
                                ];
                                $colorClass = $yearColors[$ay->id % 3] ?? 'bg-gray-50 text-gray-700 border border-gray-200';
                            @endphp
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg font-semibold {{ $colorClass }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $ay->year }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $ay->start_date->format('d M Y') }} — {{ $ay->end_date->format('d M Y') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center status-cell">
                            @if($ay->is_active)
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold">
                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                Tidak Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" class="toggle-active-btn inline-flex items-center justify-center w-9 h-9 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 hover:scale-110 transition-all duration-200"
                                    data-year-id="{{ $ay->id }}" data-active="{{ $ay->is_active ? 1 : 0 }}"
                                    title="{{ $ay->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </button>
                                <a href="{{ route('admin.academic-years.edit', $ay) }}"
                                    class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 hover:scale-110 transition-all duration-200"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.academic-years.destroy', $ay) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 hover:scale-110 transition-all duration-200"
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus tahun ajaran {{ $ay->year }}?')">
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <p class="text-lg font-medium mb-1">Tidak ada data tahun ajaran</p>
                            <p class="text-sm">Silakan tambah tahun ajaran baru</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($academicYears->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $academicYears->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.toggle-active-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                var yearId = btn.getAttribute('data-year-id');
                var currentlyActive = btn.getAttribute('data-active') === '1';
                var setActive = currentlyActive ? 0 : 1;

                 var baseUrl = window.location.pathname.replace(/\/$/, '');
                 fetch(baseUrl + "/" + yearId + '/toggle-active', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        set_active: setActive
                    })
                }).then(function(res) {
                    if (!res.ok) {
                        return res.json().then(function(err) {
                            throw new Error(err.error || 'Terjadi kesalahan saat mengubah status tahun ajaran.');
                        }).catch(function() {
                            throw new Error('Terjadi kesalahan saat mengubah status tahun ajaran.');
                        });
                    }
                    return res.json();
                }).then(function(data) {
                    window.location.reload();
                }).catch(function(err) {
                    showFlashMessage(err.message || 'Terjadi kesalahan saat mengubah status tahun ajaran.', 'error');
                });
            });
        });
    });
</script>
@endpush

@endsection