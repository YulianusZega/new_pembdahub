@extends('layouts.admin')

@section('content')
@php
    $routePrefix = ($isYayasanView ?? (auth()->user()->isSuperAdmin() || auth()->user()->isYayasan() || request()->routeIs('yayasan.*'))) ? 'yayasan.' : 'admin.';
@endphp
<div class="space-y-8 pb-12">
    {{-- Header Banner Section --}}
    <div class="bg-gradient-to-r from-indigo-900 via-purple-900 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-950/20 border border-indigo-700/40 relative overflow-hidden">
        <div class="absolute -right-12 -top-12 w-64 h-64 bg-purple-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-12 -bottom-12 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/10 backdrop-blur-md border border-white/20 text-indigo-200 text-xs sm:text-sm font-bold uppercase tracking-wider">
                    <i class="fas fa-layer-group text-amber-400"></i>
                    <span>Modul Manajemen Kinerja</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                    <i class="fas fa-file-signature text-amber-400"></i>
                    <span>{{ $viewTitle }}</span>
                </h2>
                <p class="text-indigo-100/90 font-medium text-sm sm:text-base max-w-2xl leading-relaxed">
                    Kelola, periksa, dan evaluasi seluruh pengajuan dokumen perjanjian kinerja secara efektif sesuai tahapan proses yang berlangsung.
                </p>
            </div>
            <div class="flex items-center shrink-0">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 px-5 py-3.5 rounded-2xl flex items-center gap-3.5 shadow-inner">
                    <div class="w-11 h-11 rounded-xl bg-amber-400/20 border border-amber-400/40 flex items-center justify-center text-amber-300 text-lg font-bold">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-indigo-200 uppercase tracking-wider">Tahun Pelajaran</div>
                        <div class="text-lg font-black text-white tracking-wide">{{ $currentYear->year ?? '-' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-emerald-50 border-2 border-emerald-300 text-emerald-900 px-5 py-4 rounded-2xl flex items-center gap-4 shadow-sm">
            <div class="bg-emerald-600 text-white w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 shadow-md shadow-emerald-600/30">
                <i class="fas fa-check"></i>
            </div>
            <p class="font-bold text-sm sm:text-base">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-2 border-rose-300 text-rose-900 px-5 py-4 rounded-2xl flex items-center gap-4 shadow-sm">
            <div class="bg-rose-600 text-white w-10 h-10 rounded-xl flex items-center justify-center text-lg shrink-0 shadow-md shadow-rose-600/30">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <p class="font-bold text-sm sm:text-base">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Navigation Tabs --}}
    <div class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h3 class="text-base sm:text-lg font-black text-gray-800 flex items-center gap-2.5">
                <i class="fas fa-filter text-indigo-600"></i>
                <span>Filter Tahapan Proses</span>
            </h3>
            <span class="text-xs sm:text-sm font-semibold text-gray-500">Klik tab di bawah untuk memilah data</span>
        </div>
        
        <div class="grid grid-cols-2 sm:flex sm:flex-wrap sm:items-center gap-3">
            {{-- Semua --}}
            <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'all']) }}" 
               class="inline-flex items-center justify-between gap-3 px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-sm shrink-0 {{ ($tab ?? 'all') === 'all' ? 'bg-gradient-to-r from-slate-800 to-gray-900 text-white shadow-lg shadow-gray-900/30 ring-2 ring-slate-400 border border-slate-700' : 'bg-slate-100/90 hover:bg-slate-200 text-slate-800 border-2 border-slate-300' }}">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-layer-group text-base sm:text-lg {{ ($tab ?? 'all') === 'all' ? 'text-amber-400' : 'text-slate-600' }}"></i>
                    <span>Semua Data</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-black {{ ($tab ?? 'all') === 'all' ? 'bg-white/25 text-white' : 'bg-white text-gray-900 shadow-sm border border-gray-200/60' }}">
                    {{ $statusCounts['all'] ?? 0 }}
                </span>
            </a>

            {{-- Setuju Yayasan --}}
            <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'approved_by_yayasan']) }}" 
               class="inline-flex items-center justify-between gap-3 px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-sm shrink-0 {{ ($tab ?? '') === 'approved_by_yayasan' ? 'bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-lg shadow-emerald-600/30 ring-2 ring-emerald-300 border border-emerald-500' : 'bg-emerald-50 hover:bg-emerald-100/90 text-emerald-900 border-2 border-emerald-300/80 hover:border-emerald-400' }}">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-check-double text-base sm:text-lg {{ ($tab ?? '') === 'approved_by_yayasan' ? 'text-white' : 'text-emerald-600' }}"></i>
                    <span>Setuju Yayasan</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-black {{ ($tab ?? '') === 'approved_by_yayasan' ? 'bg-white/25 text-white' : 'bg-white text-emerald-900 shadow-sm border border-emerald-200' }}">
                    {{ $statusCounts['approved_by_yayasan'] ?? 0 }}
                </span>
            </a>

            {{-- Setuju Kepala Sekolah --}}
            <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'approved_by_kepsek']) }}" 
               class="inline-flex items-center justify-between gap-3 px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-sm shrink-0 {{ ($tab ?? '') === 'approved_by_kepsek' ? 'bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-lg shadow-blue-600/30 ring-2 ring-blue-300 border border-blue-500' : 'bg-blue-50 hover:bg-blue-100/90 text-blue-900 border-2 border-blue-300/80 hover:border-blue-400' }}">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-check-circle text-base sm:text-lg {{ ($tab ?? '') === 'approved_by_kepsek' ? 'text-white' : 'text-blue-600' }}"></i>
                    <span>Setuju Kepala Sekolah</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-black {{ ($tab ?? '') === 'approved_by_kepsek' ? 'bg-white/25 text-white' : 'bg-white text-blue-900 shadow-sm border border-blue-200' }}">
                    {{ $statusCounts['approved_by_kepsek'] ?? 0 }}
                </span>
            </a>

            {{-- Di Ajukan --}}
            <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'submitted_to_kepsek']) }}" 
               class="inline-flex items-center justify-between gap-3 px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-sm shrink-0 {{ ($tab ?? '') === 'submitted_to_kepsek' ? 'bg-gradient-to-r from-amber-500 to-orange-600 text-white shadow-lg shadow-amber-500/30 ring-2 ring-amber-300 border border-amber-400' : 'bg-amber-50 hover:bg-amber-100/90 text-amber-900 border-2 border-amber-300/80 hover:border-amber-400' }}">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-paper-plane text-base sm:text-lg {{ ($tab ?? '') === 'submitted_to_kepsek' ? 'text-white' : 'text-amber-600' }}"></i>
                    <span>Di Ajukan</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-black {{ ($tab ?? '') === 'submitted_to_kepsek' ? 'bg-white/25 text-white' : 'bg-white text-amber-900 shadow-sm border border-amber-200' }}">
                    {{ $statusCounts['submitted_to_kepsek'] ?? 0 }}
                </span>
            </a>

            {{-- Di Tolak --}}
            <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'rejected']) }}" 
               class="inline-flex items-center justify-between gap-3 px-5 py-3.5 rounded-2xl text-sm sm:text-base font-black transition-all shadow-sm shrink-0 {{ ($tab ?? '') === 'rejected' ? 'bg-gradient-to-r from-rose-600 to-red-700 text-white shadow-md shadow-rose-600/30 ring-2 ring-rose-300 border border-rose-500' : 'bg-rose-50 hover:bg-rose-100/90 text-rose-900 border-2 border-rose-300/80 hover:border-rose-400' }}">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-times-circle text-base sm:text-lg {{ ($tab ?? '') === 'rejected' ? 'text-white' : 'text-rose-600' }}"></i>
                    <span>Di Tolak</span>
                </div>
                <span class="px-3 py-1 rounded-full text-xs sm:text-sm font-black {{ ($tab ?? '') === 'rejected' ? 'bg-white/25 text-white' : 'bg-white text-rose-900 shadow-sm border border-rose-200' }}">
                    {{ $statusCounts['rejected'] ?? 0 }}
                </span>
            </a>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-3xl shadow-xl border border-indigo-100 overflow-hidden">
        <div class="h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-slate-100 via-indigo-50/40 to-slate-100 border-b-2 border-gray-200">
                        <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Nama Guru</th>
                        <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Unit Sekolah</th>
                        <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Tipe Kontrak</th>
                        <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider">Status Persetujuan</th>
                        <th class="px-6 py-5 text-sm font-black text-gray-800 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-indigo-50/50 transition-all duration-200 group">
                        <td class="px-6 py-5">
                            <div class="font-extrabold text-base text-gray-900">{{ $contract->created_at->format('d M Y') }}</div>
                            <div class="text-sm font-bold text-indigo-600 mt-0.5"><i class="far fa-clock mr-1"></i>{{ $contract->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-6 py-5">
                            <div class="font-black text-base sm:text-lg text-gray-900 group-hover:text-indigo-900 transition-colors">{{ $contract->employee->full_name ?? '-' }}</div>
                            @if($contract->employee && $contract->employee->nip)
                                <div class="text-xs font-bold text-gray-500 mt-0.5">NIP: {{ $contract->employee->nip }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-100 text-slate-800 font-extrabold text-sm border border-slate-300/80 shadow-sm">
                                <i class="fas fa-school text-indigo-600"></i>
                                {{ $contract->school->name ?? 'SMK' }}
                            </span>
                        </td>
                        <td class="px-6 py-5">
                            @if($contract->contract_type == 'pkg_kejuruan')
                                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-black bg-blue-100 text-blue-900 border-2 border-blue-300 shadow-sm">
                                    <i class="fas fa-tools text-blue-600"></i> Form 2A (Kejuruan)
                                </span>
                            @elseif($contract->contract_type == 'pkg_umum')
                                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-black bg-indigo-100 text-indigo-900 border-2 border-indigo-300 shadow-sm">
                                    <i class="fas fa-book-open text-indigo-600"></i> Form 2B (Umum)
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-300 shadow-sm">
                                    <i class="fas fa-briefcase text-amber-600"></i> Form 4 ({{ $contract->position->position_name ?? 'Jabatan' }})
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            @if($contract->status == 'submitted_to_kepsek')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-amber-100 text-amber-900 border-2 border-amber-400 shadow-sm">
                                    <i class="fas fa-clock text-amber-600 animate-pulse"></i> Menunggu Kepsek
                                </span>
                            @elseif($contract->status == 'approved_by_kepsek')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-blue-100 text-blue-900 border-2 border-blue-400 shadow-sm">
                                    <i class="fas fa-spinner text-blue-600 animate-spin"></i> Menunggu Yayasan
                                </span>
                            @elseif($contract->status == 'approved_by_yayasan')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-emerald-100 text-emerald-900 border-2 border-emerald-400 shadow-sm">
                                    <i class="fas fa-check-circle text-emerald-600"></i> Setuju Yayasan
                                </span>
                            @elseif($contract->status == 'rejected')
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-black bg-rose-100 text-rose-900 border-2 border-rose-400 shadow-sm">
                                    <i class="fas fa-times-circle text-rose-600"></i> Di Tolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-sm font-bold bg-gray-100 text-gray-700 border border-gray-300">
                                    {{ $contract->status }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-end gap-2.5">
                                <a href="{{ route($routePrefix . 'performance_contracts.show', $contract->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition-all font-bold text-sm shadow-md shadow-indigo-600/20 hover:shadow-lg hover:-translate-y-0.5">
                                    <i class="fas fa-search"></i> Periksa
                                </a>
                                <form action="{{ route($routePrefix . 'performance_contracts.destroy', $contract->id) }}" method="POST" class="m-0 p-0 inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak kinerja ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white border-2 border-rose-200 hover:border-rose-600 transition-all font-bold text-sm shadow-sm hover:shadow-md">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-slate-100 border-2 border-slate-200 mb-5 shadow-inner">
                                @if(($tab ?? 'all') === 'approved_by_yayasan')
                                    <i class="fas fa-check-double text-3xl text-emerald-500"></i>
                                @elseif(($tab ?? 'all') === 'approved_by_kepsek')
                                    <i class="fas fa-check-circle text-3xl text-blue-500"></i>
                                @elseif(($tab ?? 'all') === 'submitted_to_kepsek')
                                    <i class="fas fa-paper-plane text-3xl text-amber-500"></i>
                                @elseif(($tab ?? 'all') === 'rejected')
                                    <i class="fas fa-times-circle text-3xl text-rose-500"></i>
                                @else
                                    <i class="fas fa-folder-open text-3xl text-slate-400"></i>
                                @endif
                            </div>
                            <h3 class="text-gray-900 font-black text-xl mb-2">
                                @if(($tab ?? 'all') === 'approved_by_yayasan')
                                    Belum Ada Kontrak Disetujui Yayasan
                                @elseif(($tab ?? 'all') === 'approved_by_kepsek')
                                    Belum Ada Kontrak Disetujui Kepala Sekolah
                                @elseif(($tab ?? 'all') === 'submitted_to_kepsek')
                                    Belum Ada Kontrak yang Diajukan
                                @elseif(($tab ?? 'all') === 'rejected')
                                    Belum Ada Kontrak Ditolak
                                @else
                                    Belum Ada Pengajuan Data
                                @endif
                            </h3>
                            <p class="text-gray-600 font-medium text-base mb-6 max-w-lg mx-auto leading-relaxed">
                                @if(($tab ?? 'all') === 'approved_by_yayasan')
                                    Saat ini belum ada dokumen perjanjian kinerja yang telah mendapat persetujuan akhir dari Ketua Yayasan.
                                @elseif(($tab ?? 'all') === 'approved_by_kepsek')
                                    Saat ini belum ada dokumen perjanjian kinerja yang berstatus disetujui oleh Kepala Sekolah dan menunggu verifikasi Yayasan.
                                @elseif(($tab ?? 'all') === 'submitted_to_kepsek')
                                    Saat ini belum ada pengajuan baru dari guru yang menunggu pemeriksaan Kepala Sekolah.
                                @elseif(($tab ?? 'all') === 'rejected')
                                    Saat ini tidak ada pengajuan perjanjian kinerja yang dikembalikan atau ditolak.
                                @else
                                    Belum ada data pengajuan perjanjian kinerja yang masuk dalam sistem untuk saat ini.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($contracts->hasPages())
        <div class="px-6 py-5 border-t-2 border-gray-100 bg-gradient-to-r from-gray-50 via-slate-50 to-gray-50 font-bold text-base">
            {{ $contracts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
