@extends('layouts.admin')

@section('content')
@php
    $routePrefix = ($isYayasanView ?? (auth()->user()->isSuperAdmin() || auth()->user()->isYayasan() || request()->routeIs('yayasan.*'))) ? 'yayasan.' : 'admin.';
@endphp
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">{{ $viewTitle }}</h2>
            <p class="text-sm text-gray-500 mt-1">Tahun Ajaran Aktif: <span class="font-semibold text-indigo-600">{{ $currentYear->year ?? '-' }}</span></p>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <div class="bg-emerald-100 p-2 rounded-lg"><i class="fas fa-check-circle text-emerald-600"></i></div>
            <p class="font-medium text-sm">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl flex items-center gap-3">
            <div class="bg-rose-100 p-2 rounded-lg"><i class="fas fa-exclamation-circle text-rose-600"></i></div>
            <p class="font-medium text-sm">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Navigation Tabs --}}
    <div class="bg-white rounded-2xl p-2 sm:p-2.5 shadow-sm border border-gray-100 flex items-center gap-2 overflow-x-auto">
        {{-- Semua --}}
        <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'all']) }}" 
           class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shrink-0 {{ ($tab ?? 'all') === 'all' ? 'bg-gradient-to-r from-gray-800 to-gray-900 text-white shadow-md shadow-gray-900/20' : 'bg-gray-50/80 hover:bg-gray-100 text-gray-600 hover:text-gray-900 border border-gray-200/60' }}">
            <i class="fas fa-layer-group text-xs {{ ($tab ?? 'all') === 'all' ? 'text-gray-300' : 'text-gray-400' }}"></i>
            <span>Semua</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($tab ?? 'all') === 'all' ? 'bg-white/20 text-white' : 'bg-gray-200/80 text-gray-700' }}">
                {{ $statusCounts['all'] ?? 0 }}
            </span>
        </a>

        {{-- Setuju Yayasan --}}
        <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'approved_by_yayasan']) }}" 
           class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shrink-0 {{ ($tab ?? '') === 'approved_by_yayasan' ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md shadow-emerald-500/20' : 'bg-gray-50/80 hover:bg-emerald-50 text-gray-600 hover:text-emerald-700 border border-gray-200/60 hover:border-emerald-200' }}">
            <i class="fas fa-check-double text-xs {{ ($tab ?? '') === 'approved_by_yayasan' ? 'text-emerald-200' : 'text-emerald-500' }}"></i>
            <span>Setuju Yayasan</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($tab ?? '') === 'approved_by_yayasan' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }}">
                {{ $statusCounts['approved_by_yayasan'] ?? 0 }}
            </span>
        </a>

        {{-- Setuju Kepala Sekolah --}}
        <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'approved_by_kepsek']) }}" 
           class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shrink-0 {{ ($tab ?? '') === 'approved_by_kepsek' ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-500/20' : 'bg-gray-50/80 hover:bg-blue-50 text-gray-600 hover:text-blue-700 border border-gray-200/60 hover:border-blue-200' }}">
            <i class="fas fa-check-circle text-xs {{ ($tab ?? '') === 'approved_by_kepsek' ? 'text-blue-200' : 'text-blue-500' }}"></i>
            <span>Setuju Kepala Sekolah</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($tab ?? '') === 'approved_by_kepsek' ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-800' }}">
                {{ $statusCounts['approved_by_kepsek'] ?? 0 }}
            </span>
        </a>

        {{-- Di Ajukan --}}
        <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'submitted_to_kepsek']) }}" 
           class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shrink-0 {{ ($tab ?? '') === 'submitted_to_kepsek' ? 'bg-gradient-to-r from-amber-500 to-orange-500 text-white shadow-md shadow-amber-500/20' : 'bg-gray-50/80 hover:bg-amber-50 text-gray-600 hover:text-amber-700 border border-gray-200/60 hover:border-amber-200' }}">
            <i class="fas fa-paper-plane text-xs {{ ($tab ?? '') === 'submitted_to_kepsek' ? 'text-amber-200' : 'text-amber-500' }}"></i>
            <span>Di Ajukan</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($tab ?? '') === 'submitted_to_kepsek' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-800' }}">
                {{ $statusCounts['submitted_to_kepsek'] ?? 0 }}
            </span>
        </a>

        {{-- Di Tolak --}}
        <a href="{{ route($routePrefix . 'performance_contracts.index', ['tab' => 'rejected']) }}" 
           class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold transition-all shrink-0 {{ ($tab ?? '') === 'rejected' ? 'bg-gradient-to-r from-rose-600 to-red-600 text-white shadow-md shadow-rose-500/20' : 'bg-gray-50/80 hover:bg-rose-50 text-gray-600 hover:text-rose-700 border border-gray-200/60 hover:border-rose-200' }}">
            <i class="fas fa-times-circle text-xs {{ ($tab ?? '') === 'rejected' ? 'text-rose-200' : 'text-rose-500' }}"></i>
            <span>Di Tolak</span>
            <span class="px-2 py-0.5 rounded-full text-[11px] font-black {{ ($tab ?? '') === 'rejected' ? 'bg-white/20 text-white' : 'bg-rose-100 text-rose-800' }}">
                {{ $statusCounts['rejected'] ?? 0 }}
            </span>
        </a>
    </div>

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Guru</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Unit Sekolah</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe Kontrak</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status Persetujuan</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-800">{{ $contract->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $contract->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-bold text-gray-900">{{ $contract->employee->full_name ?? '-' }}</div>
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600 font-medium">
                            {{ $contract->school->name ?? 'SMK' }}
                        </td>
                        <td class="px-5 py-4">
                            @if($contract->contract_type == 'pkg_kejuruan')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-200">Form 2A (Kejuruan)</span>
                            @elseif($contract->contract_type == 'pkg_umum')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Form 2B (Umum)</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-amber-50 text-amber-700 border border-amber-200">Form 4 (Jabatan: {{ $contract->position->position_name ?? '' }})</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($contract->status == 'submitted_to_kepsek')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200"><i class="fas fa-clock text-[10px]"></i> Menunggu Kepsek</span>
                            @elseif($contract->status == 'approved_by_kepsek')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200"><i class="fas fa-clock text-[10px]"></i> Menunggu Yayasan</span>
                            @elseif($contract->status == 'approved_by_yayasan')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200"><i class="fas fa-check-circle text-[10px]"></i> ACC Yayasan</span>
                            @elseif($contract->status == 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-50 text-rose-700 border border-rose-200"><i class="fas fa-times-circle text-[10px]"></i> Ditolak</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200">{{ $contract->status }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route($routePrefix . 'performance_contracts.show', $contract->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 hover:border-indigo-600 transition-colors font-semibold text-xs shadow-sm">
                                    <i class="fas fa-search"></i> Periksa
                                </a>
                                <form action="{{ route($routePrefix . 'performance_contracts.destroy', $contract->id) }}" method="POST" class="m-0 p-0 inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak kinerja ini secara permanen?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white border border-rose-200 hover:border-rose-600 transition-colors font-semibold text-xs shadow-sm">
                                        <i class="fas fa-trash-alt"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-16 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gray-50 border border-gray-100 mb-4">
                                @if(($tab ?? 'all') === 'approved_by_yayasan')
                                    <i class="fas fa-check-double text-2xl text-emerald-400"></i>
                                @elseif(($tab ?? 'all') === 'approved_by_kepsek')
                                    <i class="fas fa-check-circle text-2xl text-blue-400"></i>
                                @elseif(($tab ?? 'all') === 'submitted_to_kepsek')
                                    <i class="fas fa-paper-plane text-2xl text-amber-400"></i>
                                @elseif(($tab ?? 'all') === 'rejected')
                                    <i class="fas fa-times-circle text-2xl text-rose-400"></i>
                                @else
                                    <i class="fas fa-folder-open text-2xl text-gray-300"></i>
                                @endif
                            </div>
                            <h3 class="text-gray-900 font-bold mb-1">
                                @if(($tab ?? 'all') === 'approved_by_yayasan')
                                    Belum Ada Kontrak Disetujui Yayasan
                                @elseif(($tab ?? 'all') === 'approved_by_kepsek')
                                    Belum Ada Kontrak Disetujui Kepala Sekolah
                                @elseif(($tab ?? 'all') === 'submitted_to_kepsek')
                                    Belum Ada Kontrak yang Diajukan
                                @elseif(($tab ?? 'all') === 'rejected')
                                    Belum Ada Kontrak Ditolak
                                @else
                                    Belum Ada Pengajuan
                                @endif
                            </h3>
                            <p class="text-gray-500 text-sm mb-4 max-w-md mx-auto">
                                @if(($tab ?? 'all') === 'approved_by_yayasan')
                                    Saat ini belum ada dokumen perjanjian kinerja yang telah mendapat persetujuan akhir dari Ketua Yayasan.
                                @elseif(($tab ?? 'all') === 'approved_by_kepsek')
                                    Saat ini belum ada dokumen perjanjian kinerja yang berstatus disetujui oleh Kepala Sekolah dan menunggu verifikasi Yayasan.
                                @elseif(($tab ?? 'all') === 'submitted_to_kepsek')
                                    Saat ini belum ada pengajuan baru dari guru yang menunggu pemeriksaan Kepala Sekolah.
                                @elseif(($tab ?? 'all') === 'rejected')
                                    Saat ini tidak ada pengajuan perjanjian kinerja yang dikembalikan atau ditolak.
                                @else
                                    Belum ada data pengajuan perjanjian kinerja untuk saat ini.
                                @endif
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($contracts->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $contracts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
