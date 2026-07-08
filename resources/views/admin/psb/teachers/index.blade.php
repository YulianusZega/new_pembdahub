@extends('layouts.admin')

@section('title', 'Data Guru')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 shadow-lg">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Data Guru</h1>
                    <p class="text-gray-600 mt-1">Manajemen data guru & tenaga pengajar</p>
                </div>
            </div>
            <a href="{{ route('admin.teachers.create') }}" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-teal-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Guru
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
        <form action="{{ route('admin.teachers.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Sekolah</label>
                @if(auth()->user()->isSuperAdmin())
                    <select name="school_id" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                        <option value="">Semua Sekolah</option>
                        @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                @else
                    <div class="w-full px-4 py-2 border-2 border-emerald-200 rounded-xl bg-emerald-50 text-gray-800 font-semibold">
                        {{ auth()->user()->school->name }}
                    </div>
                @endif
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-chart-bar mr-1"></i> Status</label>
                <select name="is_active" class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
                    <option value="">Semua Status</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-search mr-1"></i> Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama, Kode, Telepon..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-teal-800 transition-all">
                    Filter
                </button>
                <a href="{{ route('admin.teachers.index') }}" class="px-6 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Teachers Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm font-semibold">No</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Foto</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Nama Lengkap & Kode</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Sekolah</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Kompetensi Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-sm font-semibold">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($teachers as $index => $teacher)
                    <tr class="hover:bg-gradient-to-r hover:from-emerald-50 hover:to-teal-50 transition-all duration-200">
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-100 to-teal-100 text-emerald-700 font-bold text-sm shadow-sm">
                                {{ $teachers->firstItem() + $index }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($teacher->photo)
                            <img src="{{ asset('storage/' . $teacher->photo) }}" alt="{{ $teacher->full_name }}" 
                                class="w-20 h-20 rounded-2xl object-cover border-3 border-emerald-300 shadow-lg hover:scale-110 transition-transform duration-300 cursor-pointer">
                            @else
                            <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-emerald-400 via-teal-500 to-cyan-500 flex items-center justify-center text-white font-bold text-2xl shadow-lg hover:scale-110 transition-transform duration-300 cursor-pointer">
                                {{ strtoupper(substr($teacher->full_name, 0, 1)) }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <div class="font-bold text-gray-900 text-lg">{{ $teacher->full_name }}</div>
                                <div class="flex items-center gap-2">
                                    <span class="px-3 py-1 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-xs font-bold rounded-lg shadow-sm">
                                        {{ $teacher->teacher_code }}
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        {{ $teacher->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-2xl"><i class="fas fa-school mr-1"></i></span>
                                <span class="font-semibold text-gray-800">{{ $teacher->school->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($teacher->competentSubjects && $teacher->competentSubjects->count() > 0)
                                <div class="space-y-1">
                                    @foreach($teacher->competentSubjects->take(3) as $subject)
                                        <div class="flex items-center gap-2 px-3 py-1.5 bg-white border-l-4 border-purple-500 rounded-r-lg shadow-sm hover:shadow-md transition-shadow">
                                            <span class="text-purple-600 font-bold text-xs"><i class="fas fa-graduation-cap mr-1"></i></span>
                                            <span class="text-sm font-semibold text-gray-800">{{ $subject->subject_name }}</span>
                                            <span class="text-xs text-gray-500">({{ $subject->subject_code }})</span>
                                        </div>
                                    @endforeach
                                    @if($teacher->competentSubjects->count() > 3)
                                        <div class="px-3 py-1 bg-gradient-to-r from-purple-100 to-pink-100 text-purple-700 text-xs font-bold rounded-lg shadow-sm text-center">
                                            +{{ $teacher->competentSubjects->count() - 3 }} mata pelajaran lainnya
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                                    <span class="text-gray-400"><i class="fas fa-exclamation-triangle text-yellow-500 mr-1"></i></span>
                                    <span class="text-gray-500 text-sm italic">Belum ada kompetensi</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($teacher->is_active)
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-100 to-green-200 text-green-800 text-sm font-bold rounded-xl border-2 border-green-300 shadow-sm">
                                <span class="w-3 h-3 bg-green-500 rounded-full animate-pulse shadow-lg"></span>
                                Aktif
                            </span>
                            @else
                            <span class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-200 text-gray-700 text-sm font-bold rounded-xl border-2 border-gray-300 shadow-sm">
                                <span class="w-3 h-3 bg-gray-400 rounded-full"></span>
                                Non-Aktif
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.teachers.show', $teacher) }}" 
                                    class="group flex items-center justify-center w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl hover:scale-110 hover:rotate-3 transition-all duration-300 shadow-lg"
                                    title="Lihat Detail">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.teachers.competencies', $teacher) }}" 
                                    class="group flex items-center justify-center w-10 h-10 bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-xl hover:scale-110 hover:rotate-3 transition-all duration-300 shadow-lg"
                                    title="Kelola Kompetensi">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </a>
                                <a href="{{ route('admin.teachers.edit', $teacher) }}" 
                                    class="group flex items-center justify-center w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 text-white rounded-xl hover:scale-110 hover:rotate-3 transition-all duration-300 shadow-lg"
                                    title="Edit">
                                    <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
                                <form action="{{ route('admin.teachers.destroy', $teacher) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus guru ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                        class="group flex items-center justify-center w-10 h-10 bg-gradient-to-br from-red-500 to-red-600 text-white rounded-xl hover:scale-110 hover:rotate-3 transition-all duration-300 shadow-lg"
                                        title="Hapus">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-16 text-center bg-gradient-to-br from-gray-50 to-gray-100">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-24 h-24 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-full flex items-center justify-center mb-4 animate-pulse">
                                    <svg class="w-12 h-12 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <p class="text-xl font-bold text-gray-700 mb-2"><i class="fas fa-clipboard mr-1"></i> Tidak ada data guru</p>
                                <p class="text-sm text-gray-500 mb-4">Mulai tambahkan guru untuk sekolah Anda</p>
                                <a href="{{ route('admin.teachers.create') }}" 
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-700 text-white rounded-xl font-medium hover:from-emerald-700 hover:to-teal-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Tambah Guru Baru
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($teachers->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $teachers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
