@extends('layouts.admin')
@section('title', 'Kelola Lowongan Kerja - Portal Admin')

@section('content')
<div class="space-y-6">
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-briefcase text-indigo-500"></i> Kelola Lowongan Kerja
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">Mengelola informasi lowongan kerja bagi alumni dan siswa tingkat akhir</p>
        </div>
        <div>
            <a href="{{ route('admin.pkl-alumni.jobs.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition">
                <i class="fas fa-plus"></i> Tambah Lowongan
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-255 text-emerald-800 px-4 py-3 rounded-xl text-xs font-semibold">
            {{ session('success') }}
        </div>
    @endif

    {{-- Search Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form action="{{ route('admin.pkl-alumni.jobs.index') }}" method="GET" class="flex gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400 text-sm"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari posisi pekerjaan atau nama perusahaan..." class="w-full bg-gray-50 border border-gray-100 rounded-xl pl-10 pr-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 transition">
            </div>
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-2.5 rounded-xl text-sm transition">
                Cari
            </button>
            @if(request('search'))
                <a href="{{ route('admin.pkl-alumni.jobs.index') }}" class="bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold px-4 py-2.5 rounded-xl text-sm transition flex items-center justify-center">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Job Postings Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                    <tr>
                        <th class="py-3.5 pl-5">Perusahaan & Posisi</th>
                        <th class="py-3.5">Gaji</th>
                        <th class="py-3.5">Kontak Lamaran</th>
                        <th class="py-3.5">Diposting Oleh</th>
                        <th class="py-3.5 text-center">Status</th>
                        <th class="py-3.5 text-center pr-5">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-xs text-gray-700">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-gray-50/50 transition">
                            <td class="py-4 pl-5">
                                <p class="font-extrabold text-gray-850 text-sm leading-snug">{{ $job->title }}</p>
                                <p class="text-[10px] text-gray-400 mt-0.5"><i class="fas fa-building mr-1"></i>{{ $job->company_name }}</p>
                            </td>
                            <td class="py-4 font-semibold text-gray-800">
                                {{ $job->salary_range ?? 'Kompetitif' }}
                            </td>
                            <td class="py-4 space-y-0.5 font-medium text-gray-650">
                                @if($job->contact_email)
                                    <p class="flex items-center gap-1.5"><i class="far fa-envelope text-gray-400"></i>{{ $job->contact_email }}</p>
                                @endif
                                @if($job->contact_phone)
                                    <p class="flex items-center gap-1.5"><i class="fab fa-whatsapp text-emerald-500"></i>{{ $job->contact_phone }}</p>
                                @endif
                            </td>
                            <td class="py-4 font-semibold text-gray-500">
                                {{ $job->creator->name ?? '-' }}
                                <span class="text-[9px] text-gray-400 block font-normal mt-0.5">{{ $job->created_at->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-4 text-center">
                                @if($job->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-250">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-250">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 text-center pr-5">
                                <div class="flex items-center justify-center gap-1.5">
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.pkl-alumni.jobs.edit', $job->id) }}" class="bg-gray-50 hover:bg-amber-50 text-gray-500 hover:text-amber-600 border border-gray-200 hover:border-amber-150 p-2 rounded-xl transition shadow-sm" title="Edit Lowongan">
                                        <i class="fas fa-edit text-[10px]"></i>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.pkl-alumni.jobs.destroy', $job->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus lowongan pekerjaan ini?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-gray-50 hover:bg-rose-50 text-gray-500 hover:text-rose-600 border border-gray-200 hover:border-rose-150 p-2 rounded-xl transition shadow-sm" title="Hapus Lowongan">
                                            <i class="fas fa-trash-alt text-[10px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 italic">
                                <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-briefcase text-lg text-gray-300"></i>
                                </div>
                                Belum ada data lowongan pekerjaan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($jobs->hasPages())
            <div class="px-5 py-4 border-t border-gray-100">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
