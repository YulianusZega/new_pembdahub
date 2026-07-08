@extends('layouts.admin')

@section('title', 'Semester')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Semester</h1>
                    <p class="text-gray-600 mt-1">Kelola periode semester akademik</p>
                </div>
            </div>
            <a href="{{ route('admin.semesters.create') }}" 
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-xl font-semibold text-sm hover:from-teal-700 hover:to-cyan-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Tambah Semester
            </a>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm animate-fade-in">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-500 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"/>
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Menu Tabs -->
    <div class="mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-1 inline-flex flex-wrap gap-1">
            <a href="{{ route('admin.academic-years.index') }}" 
               class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.academic-years.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-calendar-alt mr-2"></i> Tahun Ajaran
            </a>
            <a href="{{ route('admin.semesters.index') }}" 
               class="px-5 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ request()->routeIs('admin.semesters.*') ? 'bg-teal-50 text-teal-700 shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-clock mr-2"></i> Semester
            </a>
        </div>
    </div>

    <!-- Search Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" class="flex gap-4">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-search mr-1"></i> Cari Semester
                    </span>
                </label>
                <input type="text" name="q" value="{{ request('q') }}" 
                    placeholder="Cari nama semester, tahun ajaran..." 
                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent transition-all" />
            </div>
            <div class="flex items-end">
                <button type="submit" 
                    class="px-5 py-2.5 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-xl font-semibold text-sm hover:from-teal-700 hover:to-cyan-700 shadow-md hover:shadow-lg transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Semester</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($semesters as $s)
                    <tr class="hover:bg-gradient-to-r hover:from-teal-50 hover:to-cyan-50 transition-all">
                        <td class="px-6 py-4">
                            @if($s->academicYear)
                                @php
                                    $yearColors = [
                                        0 => 'bg-blue-100 text-blue-800 border border-blue-200',
                                        1 => 'bg-amber-100 text-amber-800 border border-amber-200',
                                        2 => 'bg-purple-100 text-purple-800 border border-purple-200',
                                    ];
                                    $colorClass = $yearColors[$s->academicYear->id % 3] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $colorClass }}">
                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $s->academicYear->year }}
                                </span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div>
                                @if(Str::contains(strtolower($s->semester_name), 'ganjil') || $s->semester_number % 2 !== 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                        <span class="w-1.5 h-1.5 bg-indigo-500 rounded-full mr-1.5"></span>
                                        {{ $s->semester_name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                        <span class="w-1.5 h-1.5 bg-teal-500 rounded-full mr-1.5"></span>
                                        {{ $s->semester_name }}
                                    </span>
                                @endif
                                <div class="text-xs text-gray-500 mt-1">Semester {{ $s->semester_number }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-700">
                            <div class="flex items-center gap-1 text-sm">
                                <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span>{{ $s->start_date->format('d M Y') }}</span>
                                <span class="text-gray-400">—</span>
                                <span>{{ $s->end_date->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($s->is_active)
                            <span class="flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold inline-flex w-fit">
                                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                                Aktif
                            </span>
                            @else
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-semibold inline-block">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.semesters.edit', $s) }}" 
                                    class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all" 
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.semesters.destroy', $s) }}" method="POST" 
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus semester ini?');" 
                                    class="inline">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all" 
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-gray-500 text-lg mb-2">Tidak ada data semester</p>
                                <a href="{{ route('admin.semesters.create') }}" 
                                    class="text-teal-600 hover:text-teal-700 font-medium">
                                    Tambah Semester Pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($semesters->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $semesters->links() }}
        </div>
        @endif
    </div>
</div>
@endsection