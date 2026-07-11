@extends('layouts.guru')

@section('content')
<div class="space-y-6">
    {{-- Header Section --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Perjanjian Kinerja Saya</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola dokumen perjanjian dan sasaran kinerja Anda.</p>
        </div>
        <a href="{{ route('guru.performance_contracts.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md shadow-emerald-500/20 transition-all hover:-translate-y-0.5">
            <i class="fas fa-plus"></i> Buat Kontrak Baru
        </a>
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

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tahun Ajaran</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe Kontrak</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Jabatan</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status Persetujuan</th>
                        <th class="px-5 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contracts as $contract)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <div class="font-semibold text-gray-800">{{ $contract->academicYear->year }}</div>
                            <div class="text-xs text-gray-500">Semester {{ $contract->academicYear->semester }}</div>
                        </td>
                        <td class="px-5 py-4">
                            @if($contract->contract_type == 'pkg_kejuruan')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-200">Form 2A (Kejuruan)</span>
                            @elseif($contract->contract_type == 'pkg_umum')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">Form 2B (Umum)</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200">Form 4 (Jabatan)</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm text-gray-600 font-medium">
                            {{ $contract->position ? $contract->position->position_name : '-' }}
                        </td>
                        <td class="px-5 py-4">
                            @if($contract->status == 'draft')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-gray-100 text-gray-600 border border-gray-200"><i class="fas fa-file-alt text-[10px]"></i> Draft</span>
                            @elseif($contract->status == 'submitted_to_kepsek')
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">1. Diajukan</span>
                                    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-medium bg-white text-gray-500 border border-gray-200 shadow-sm">2. Kepsek</span>
                                    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-medium bg-white text-gray-500 border border-gray-200 shadow-sm">3. Yayasan</span>
                                </div>
                            @elseif($contract->status == 'approved_by_kepsek')
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200"><i class="fas fa-check mr-1"></i> Diajukan</span>
                                    <i class="fas fa-chevron-right text-emerald-300 text-[9px]"></i>
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200">2. Kepsek</span>
                                    <i class="fas fa-chevron-right text-gray-300 text-[9px]"></i>
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-medium bg-white text-gray-500 border border-gray-200 shadow-sm">3. Yayasan</span>
                                </div>
                            @elseif($contract->status == 'approved_by_yayasan')
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200"><i class="fas fa-check mr-1"></i> Diajukan</span>
                                    <i class="fas fa-chevron-right text-emerald-300 text-[9px]"></i>
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200"><i class="fas fa-check mr-1"></i> Kepsek</span>
                                    <i class="fas fa-chevron-right text-emerald-300 text-[9px]"></i>
                                    <span class="inline-flex px-2 py-1 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200"><i class="fas fa-check mr-1"></i> Yayasan</span>
                                </div>
                            @elseif($contract->status == 'rejected')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-rose-50 text-rose-600 border border-rose-200"><i class="fas fa-times-circle text-[10px]"></i> Ditolak</span>
                                <div class="text-[11px] text-rose-500 mt-1.5 flex items-start gap-1">
                                    <i class="fas fa-comment-dots mt-0.5"></i>
                                    <span>{{ $contract->notes }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('guru.performance_contracts.show', $contract->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-600 hover:bg-emerald-50 hover:text-emerald-600 border border-gray-200 hover:border-emerald-200 transition-colors tooltip" title="Lihat Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                
                                @if(in_array($contract->status, ['draft', 'submitted_to_kepsek', 'rejected']))
                                    <a href="{{ route('guru.performance_contracts.edit', $contract->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-600 hover:bg-indigo-50 hover:text-indigo-600 border border-gray-200 hover:border-indigo-200 transition-colors tooltip" title="Edit Kontrak">
                                        <i class="fas fa-pen text-sm"></i>
                                    </a>
                                    <form action="{{ route('guru.performance_contracts.destroy', $contract->id) }}" method="POST" class="m-0 p-0 inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kontrak ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-gray-50 text-gray-600 hover:bg-rose-50 hover:text-rose-600 border border-gray-200 hover:border-rose-200 transition-colors tooltip" title="Hapus Kontrak">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                @endif

                                @if($contract->status == 'approved_by_yayasan')
                                    <a href="{{ route('guru.performance_contracts.print', $contract->id) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-semibold text-xs transition-colors tooltip" title="Cetak Pakta Integritas">
                                        <i class="fas fa-print"></i> Cetak
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                <i class="fas fa-file-signature text-2xl text-gray-300"></i>
                            </div>
                            <h3 class="text-gray-900 font-semibold mb-1">Belum Ada Kontrak</h3>
                            <p class="text-gray-500 text-sm mb-4">Anda belum membuat Perjanjian Kinerja untuk Tahun Ajaran ini.</p>
                            <a href="{{ route('guru.performance_contracts.create') }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-xl text-sm font-semibold transition-all">
                                <i class="fas fa-plus"></i> Buat Sekarang
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
