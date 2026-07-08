@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 flex items-center gap-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    Mata Pelajaran
                </h1>
                <p class="text-gray-600 mt-2 ml-1">Kelola mata pelajaran di sekolah</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.subjects.import.form') }}" 
                    class="inline-flex items-center gap-2 bg-white border-2 border-gray-300 hover:border-indigo-500 text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-xl font-medium shadow-sm hover:shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    Import CSV
                </a>
                <a href="{{ route('admin.subjects.import.sample') }}" 
                    class="inline-flex items-center gap-2 bg-white border-2 border-gray-300 hover:border-green-500 text-gray-700 hover:text-green-600 px-4 py-2 rounded-xl font-medium shadow-sm hover:shadow-md transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Sample CSV
                </a>
                <a href="{{ route('admin.subjects.create') }}" 
                    class="inline-flex items-center gap-2 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-xl font-semibold shadow-lg hover:shadow-xl transform hover:scale-105 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah
                </a>
            </div>
        </div>
    </div>

    <!-- Success Alert -->
    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-xl p-4 shadow-sm animate-fade-in">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Filter Card -->
    <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-search mr-1"></i> Cari</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama atau kode..." 
                    class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Sekolah</label>
                @if(auth()->user()->isSuperAdmin())
                    <select name="school_id" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                        <option value="">Semua Sekolah</option>
                        @foreach($schools ?? [] as $sch)
                        <option value="{{ $sch->id }}" {{ request('school_id') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full px-4 py-2 border-2 border-indigo-200 rounded-xl bg-indigo-50 text-gray-800 font-semibold">
                        {{ auth()->user()->school->name }}
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Jurusan</label>
                <select name="major_id" class="w-full px-4 py-2 border-2 border-gray-200 rounded-xl focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition">
                    <option value="">Semua Jurusan</option>
                    @foreach($majors ?? [] as $m)
                    <option value="{{ $m->id }}" {{ request('major_id') == $m->id ? 'selected' : '' }}>{{ $m->major_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2 rounded-xl font-semibold shadow-md hover:shadow-lg transition-all">
                    Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <div class="bg-gradient-to-r from-pink-500 to-purple-600 px-6 py-5">
            <h2 class="text-xl font-bold text-white flex items-center gap-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                </svg>
                Daftar Mata Pelajaran
            </h2>
            <p class="text-pink-100 text-sm mt-1">Total: {{ $subjects->total() }} mata pelajaran</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Sekolah</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($subjects as $s)
                    <tr class="hover:bg-gradient-to-r hover:from-pink-50/50 hover:to-purple-50/50 transition-all duration-200">
                        <td class="px-6 py-5 text-center">
                            <div class="text-sm font-bold text-gray-700">
                                {{ ($subjects->currentPage() - 1) * $subjects->perPage() + $loop->iteration }}
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-gradient-to-br from-pink-400 to-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-md">
                                    {{ strtoupper(substr($s->subject_code, 0, 3)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 text-base">{{ $s->subject_name }}</div>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 border border-green-200">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            KKM: {{ $s->kkm ?? 'Belum diatur' }}
                                        </span>
                                        @if($s->major)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $s->major->major_name }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center gap-2">
                                <div class="flex-shrink-0 w-8 h-8 rounded-lg bg-gradient-to-br from-orange-400 to-orange-600 flex items-center justify-center text-white font-bold text-xs shadow">
                                    {{ $s->school ? strtoupper(substr($s->school->type, 0, 3)) : '?' }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 text-sm">{{ $s->school ? $s->school->name : '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="text-gray-700 text-sm leading-relaxed">
                                {{ $s->description ?: 'Belum ada deskripsi' }}
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.subjects.show', $s) }}" 
                                    class="group relative p-2.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-xl transition-all transform hover:scale-110 shadow-sm hover:shadow-md" 
                                    title="Lihat Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <a href="{{ route('admin.subjects.edit', $s) }}" 
                                    class="group relative p-2.5 bg-amber-100 hover:bg-amber-200 text-amber-700 rounded-xl transition-all transform hover:scale-110 shadow-sm hover:shadow-md" 
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <form action="{{ route('admin.subjects.destroy', $s) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                        class="group relative p-2.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl transition-all transform hover:scale-110 shadow-sm hover:shadow-md" 
                                        title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus {{ $s->subject_name }}?')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <p class="text-gray-500 text-lg font-medium">Tidak ada mata pelajaran</p>
                        <p class="text-gray-400 text-sm mt-1">Coba ubah filter atau tambah mata pelajaran baru</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

    <!-- Pagination -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-t border-gray-200">
        {{ $subjects->appends(request()->query())->links() }}
    </div>
</div>
@endsection