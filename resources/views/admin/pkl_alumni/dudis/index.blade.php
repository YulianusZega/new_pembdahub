@extends('layouts.admin')

@section('header')
<h2 class="font-semibold text-xl text-slate-800 leading-tight">
    Master Mitra DUDI
</h2>
@endsection

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">

        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Daftar Mitra DUDI ✨</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data mitra perusahaan untuk tempat PKL siswa.</p>
        </div>

        <!-- Right: Actions -->
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('admin.pkl-alumni.dudis.create') }}" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span class="hidden xs:block ml-2">Tambah Mitra</span>
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-sm font-semibold mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-sm font-semibold mb-6 flex items-center gap-2">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Search Form -->
    <div class="bg-white p-4 border border-slate-200 rounded-sm shadow-sm mb-6">
        <form method="GET" action="{{ route('admin.pkl-alumni.dudis.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="sr-only">Cari Mitra</label>
                <input id="search" name="search" type="text" value="{{ request('search') }}" class="form-input w-full" placeholder="Cari nama perusahaan atau mentor..." />
            </div>
            
            @if($isSA)
            <div class="w-full sm:w-1/4">
                <label for="school_id" class="sr-only">Sekolah</label>
                <select id="school_id" name="school_id" class="form-select w-full">
                    <option value="">Semua Sekolah / Global</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>
                            {{ $school->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            @endif

            <button type="submit" class="btn bg-white border-slate-200 hover:border-slate-300 text-indigo-500">Cari</button>
            @if(request()->anyFilled(['search', 'school_id']))
                <a href="{{ route('admin.pkl-alumni.dudis.index') }}" class="btn text-slate-600 hover:text-slate-800">Reset</a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white shadow-lg rounded-sm border border-slate-200">
        <header class="px-5 py-4 border-b border-slate-100">
            <h2 class="font-semibold text-slate-800">Mitra DUDI <span class="text-slate-400 font-medium">{{ $dudis->total() }}</span></h2>
        </header>
        <div class="p-3">
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead class="text-xs font-semibold uppercase text-slate-500 bg-slate-50 border-t border-b border-slate-200">
                        <tr>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Nama Mitra</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Bidang Kerja</div></th>
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Mentor Default</div></th>
                            @if($isSA)
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-left">Pemilik Data</div></th>
                            @endif
                            <th class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap"><div class="font-semibold text-center">Aksi</div></th>
                        </tr>
                    </thead>
                    <tbody class="text-sm divide-y divide-slate-200">
                        @forelse($dudis as $dudi)
                            <tr>
                                <td class="px-2 first:pl-5 last:pr-5 py-3">
                                    <div class="font-medium text-slate-800">{{ $dudi->name }}</div>
                                    <div class="text-xs text-slate-500 truncate max-w-xs" title="{{ $dudi->address }}">{{ Str::limit($dudi->address, 50) }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-slate-800">{{ $dudi->field_of_work ?? '-' }}</div>
                                </td>
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="text-slate-800">{{ $dudi->mentor_name ?? '-' }}</div>
                                    @if($dudi->mentor_phone)
                                        <div class="text-xs text-slate-500">{{ $dudi->mentor_phone }}</div>
                                    @endif
                                </td>
                                @if($isSA)
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    @if($dudi->school)
                                        <span class="inline-flex font-medium bg-indigo-100 text-indigo-600 rounded-full text-center px-2.5 py-0.5 text-xs">{{ $dudi->school->name }}</span>
                                    @else
                                        <span class="inline-flex font-medium bg-emerald-100 text-emerald-600 rounded-full text-center px-2.5 py-0.5 text-xs">Global Yayasan</span>
                                    @endif
                                </td>
                                @endif
                                <td class="px-2 first:pl-5 last:pr-5 py-3 whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="{{ route('admin.pkl-alumni.dudis.edit', $dudi) }}" class="text-slate-400 hover:text-slate-500 rounded-full p-1" title="Edit">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                                <path d="M11.7.3c-.4-.4-1-.4-1.4 0l-10 10c-.2.2-.3.4-.3.7v4c0 .6.4 1 1 1h4c.3 0 .5-.1.7-.3l10-10c.4-.4.4-1 0-1.4l-4-4zM4.6 14H2v-2.6l6-6L10.6 8l-6 6zM12 6.6L9.4 4 11 2.4 13.6 5 12 6.6z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.pkl-alumni.dudis.destroy', $dudi) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data DUDI ini? Data Penempatan PKL yang menggunakan DUDI ini tidak akan dihapus, hanya memutuskan relasi.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:text-rose-600 rounded-full p-1" title="Hapus">
                                                <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                                                    <path d="M5 7h2v6H5V7zm4 0h2v6H9V7zm3-6v2h4v2h-1v10c0 .6-.4 1-1 1H2c-.6 0-1-.4-1-1V5H0V3h4V1c0-.6.4-1 1-1h6c.6 0 1 .4 1 1zM6 2v1h4V2H6zm7 3H3v9h10V5z" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $isSA ? 5 : 4 }}" class="px-2 first:pl-5 last:pr-5 py-8 text-center text-slate-500">
                                    Belum ada data Mitra DUDI.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        {{ $dudis->links() }}
    </div>

</div>
@endsection
