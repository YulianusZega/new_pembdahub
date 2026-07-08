@extends('layouts.admin')

@section('title', 'Jurusan')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Jurusan</h1>
                    <p class="text-gray-600 mt-1">Kelola jurusan di Unit Sekolah</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.majors.create') }}" 
                    class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl font-medium hover:from-blue-700 hover:to-blue-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Jurusan SMA/SMP
                </a>
                <a href="{{ route('admin.program-keahlians.create') }}" 
                    class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-orange-700 text-white rounded-xl font-medium hover:from-orange-700 hover:to-orange-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Program Keahlian SMK
                </a>
            </div>
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

    <!-- Search & Filter Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <i class="fas fa-school mr-1"></i> Sekolah
                </label>
                @if(auth()->user()->isSuperAdmin())
                    <select name="school_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all">
                        <option value="">-- Semua Sekolah --</option>
                        @foreach(\App\Models\School::schoolsOnly()->get() as $sch)
                        <option value="{{ $sch->id }}" {{ request('school_id') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full px-4 py-3 border-2 border-amber-200 rounded-xl bg-amber-50 text-gray-800 font-semibold">
                        {{ auth()->user()->school->name }}
                    </div>
                @endif
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-gray-700 mb-2">
                    <span class="flex items-center gap-2">
                        <i class="fas fa-search mr-1"></i> Cari Jurusan
                    </span>
                </label>
                <div class="flex gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" 
                        placeholder="Cari nama atau kode jurusan..." 
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent transition-all" />
                    <button type="submit" 
                        class="px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-600 text-white rounded-xl font-medium hover:from-amber-700 hover:to-orange-700 shadow-md hover:shadow-lg transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tabs: SMA/SMP & SMK -->
    <div class="mb-6">
        <div class="flex gap-3 bg-white rounded-xl p-2 shadow-md">
            <button id="tab-sma" type="button" 
                class="tab-jurusan flex items-center gap-2 flex-1 px-6 py-3 rounded-lg font-bold text-white bg-gradient-to-r from-blue-600 to-blue-700 shadow-md transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                Jurusan SMA/SMP
            </button>
            <button id="tab-smk" type="button" 
                class="tab-jurusan flex items-center gap-2 flex-1 px-6 py-3 rounded-lg font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Program & Konsentrasi SMK
            </button>
        </div>
    </div>
    <div id="panel-sma" style="display: block;">
        <!-- Table Jurusan SMA/SMP -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider w-40">Sekolah</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider w-48">Nama Jurusan</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Deskripsi & Informasi</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($majors as $m)
                        <tr class="align-top hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-sm text-gray-900">{{ $m->school->name ?? '-' }}</span>
                                        <span class="text-xs text-gray-500">{{ strtoupper($m->school->type ?? '-') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <span class="text-lg font-bold text-gray-900">{{ $m->major_name }}</span>
                                    <span class="text-xs text-blue-600 font-semibold">Jurusan {{ strtoupper($m->school->type ?? '-') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-2">
                                    @if($m->description)
                                    <p class="text-sm text-gray-700 leading-relaxed">{{ $m->description }}</p>
                                    @else
                                    <p class="text-sm text-gray-400 italic">Belum ada deskripsi</p>
                                    @endif
                                    
                                    <!-- Info tambahan yang relevan -->
                                    <div class="flex flex-wrap gap-2 mt-2">
                                        @if($m->subjects_count ?? 0 > 0)
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            {{ $m->subjects_count }} Mata Pelajaran
                                        </span>
                                        @endif
                                        
                                        @if($m->school->type == 'SMA')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-purple-100 text-purple-700 rounded-lg text-xs font-semibold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Kurikulum {{ $m->school->type }}
                                        </span>
                                        @elseif($m->school->type == 'SMP')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-green-100 text-green-700 rounded-lg text-xs font-semibold">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                            Kurikulum {{ $m->school->type }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.majors.edit', $m) }}" 
                                        class="p-2.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-all shadow-sm hover:shadow-md" 
                                        title="Edit Jurusan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.majors.destroy', $m) }}" method="POST" 
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus jurusan ini?');" 
                                        class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="p-2.5 text-red-600 hover:bg-red-100 rounded-lg transition-all shadow-sm hover:shadow-md" 
                                            title="Hapus Jurusan">
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
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-lg font-semibold mb-2">Belum ada data jurusan</p>
                                    <p class="text-gray-400 text-sm mb-4">Mulai tambahkan jurusan untuk SMA/SMP</p>
                                    <a href="{{ route('admin.majors.create') }}" 
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 shadow-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Tambah Jurusan Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($majors->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gradient-to-r from-blue-50 to-indigo-50">
                {{ $majors->links() }}
            </div>
            @endif
        </div>
    </div>
    <div id="panel-smk" style="display:none">
        <!-- Panel Program & Konsentrasi Keahlian SMK -->
        <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl shadow-lg overflow-hidden p-8">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-orange-700 flex items-center gap-2">
                            Program Keahlian & Konsentrasi Keahlian
                            <span class="text-xs bg-orange-600 text-white px-3 py-1 rounded-full font-semibold shadow-md">SMK</span>
                        </h2>
                        <p class="text-sm text-orange-600 mt-1">SMK Swasta Pembda Nias - Manajemen Program & Konsentrasi</p>
                    </div>
                </div>
                <a href="{{ route('admin.program-keahlians.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-xl font-semibold hover:from-orange-700 hover:to-amber-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tambah Program
                </a>
            </div>

            <!-- Tabel Program Keahlian dan Konsentrasi Keahlian -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Kode</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Program Keahlian</th>
                            <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Konsentrasi Keahlian</th>
                            <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($programKeahlians as $pk)
                        <tr class="align-top hover:bg-gradient-to-r hover:from-orange-50 hover:to-amber-50 transition-all">
                            <td class="px-6 py-5">
                                <div class="flex flex-col items-center gap-2">
                                    <div class="flex items-center justify-center w-16 h-16 rounded-xl bg-gradient-to-br from-orange-500 to-amber-600 shadow-lg">
                                        <span class="text-2xl font-bold text-white">
                                            {{ $pk->kode }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-semibold text-orange-700">KODE</span>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex flex-col gap-2">
                                    <span class="text-lg font-bold text-gray-900">{{ $pk->nama }}</span>
                                    @if($pk->deskripsi)
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ $pk->deskripsi }}</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($pk->konsentrasiKeahlians->count())
                                <div class="space-y-2">
                                    @foreach($pk->konsentrasiKeahlians as $kk)
                                    <div class="flex items-start gap-3 p-3 bg-gradient-to-r from-orange-50 to-amber-50 rounded-lg border border-orange-200 hover:border-orange-300 hover:shadow-md transition-all">
                                        <div class="flex items-center justify-center min-w-[3rem] h-12 rounded-lg bg-gradient-to-br from-orange-400 to-amber-500 shadow">
                                            <span class="text-base font-bold text-white">
                                                {{ $kk->kode }}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <span class="font-bold text-sm text-gray-900 block mb-1">{{ $kk->nama }}</span>
                                            @if($kk->deskripsi)
                                            <p class="text-xs text-gray-600 leading-relaxed">{{ $kk->deskripsi }}</p>
                                            @endif
                                        </div>
                                        <a href="{{ route('admin.konsentrasi-keahlians.edit', $kk) }}" 
                                           class="p-1.5 text-green-600 hover:bg-green-100 rounded-md transition-all" 
                                           title="Edit Konsentrasi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="flex items-center gap-2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                    </svg>
                                    <span class="text-sm italic">Belum ada konsentrasi</span>
                                </div>
                                @endif
                                <div class="mt-3">
                                    <a href="{{ route('admin.konsentrasi-keahlians.create', ['program_keahlian_id' => $pk->id]) }}" 
                                       class="inline-flex items-center gap-1 text-xs font-semibold text-orange-600 hover:text-orange-700 hover:underline transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Tambah Konsentrasi
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.program-keahlians.edit', $pk) }}" 
                                       class="p-2.5 text-blue-600 hover:bg-blue-100 rounded-lg transition-all shadow-sm hover:shadow-md" 
                                       title="Edit Program Keahlian">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.program-keahlians.destroy', $pk) }}" method="POST" 
                                        onsubmit="return confirm('Yakin ingin menghapus program keahlian ini beserta semua konsentrasinya?');" 
                                        class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="p-2.5 text-red-600 hover:bg-red-100 rounded-lg transition-all shadow-sm hover:shadow-md" 
                                            title="Hapus Program">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @if($programKeahlians->isEmpty())
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                        </svg>
                                    </div>
                                    <p class="text-gray-500 text-lg mb-2 font-semibold">Belum ada program keahlian SMK</p>
                                    <p class="text-gray-400 text-sm mb-4">Mulai tambahkan program keahlian untuk SMK Swasta Pembda Nias</p>
                                    <a href="{{ route('admin.program-keahlians.create') }}" 
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-600 text-white rounded-xl font-semibold hover:from-orange-700 hover:to-amber-700 shadow-lg transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Tambah Program Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script>
        // Tab switching logic with improved styling
        document.addEventListener('DOMContentLoaded', function() {
            const tabSMA = document.getElementById('tab-sma');
            const tabSMK = document.getElementById('tab-smk');
            const panelSMA = document.getElementById('panel-sma');
            const panelSMK = document.getElementById('panel-smk');
            
            function activateTabSMA() {
                // Style for active SMA tab
                tabSMA.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                tabSMA.classList.add('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'shadow-md');
                
                // Style for inactive SMK tab
                tabSMK.classList.remove('bg-gradient-to-r', 'from-orange-600', 'to-amber-600', 'text-white', 'shadow-md');
                tabSMK.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                
                // Show/hide panels
                panelSMA.style.display = 'block';
                panelSMK.style.display = 'none';
            }
            
            function activateTabSMK() {
                // Style for inactive SMA tab
                tabSMA.classList.remove('bg-gradient-to-r', 'from-blue-600', 'to-blue-700', 'text-white', 'shadow-md');
                tabSMA.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                
                // Style for active SMK tab
                tabSMK.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                tabSMK.classList.add('bg-gradient-to-r', 'from-orange-600', 'to-amber-600', 'text-white', 'shadow-md');
                
                // Show/hide panels
                panelSMK.style.display = 'block';
                panelSMA.style.display = 'none';
            }
            
            tabSMA.addEventListener('click', activateTabSMA);
            tabSMK.addEventListener('click', activateTabSMK);
        });
    </script>
</div>
@endsection