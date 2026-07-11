@extends('layouts.admin')

@section('content')
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
                                <a href="{{ route('admin.performance_contracts.show', $contract->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white border border-indigo-200 hover:border-indigo-600 transition-colors font-semibold text-xs shadow-sm">
                                    <i class="fas fa-search"></i> Periksa
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                <i class="fas fa-folder-open text-2xl text-gray-300"></i>
                            </div>
                            <h3 class="text-gray-900 font-semibold mb-1">Belum Ada Pengajuan</h3>
                            <p class="text-gray-500 text-sm mb-4">Belum ada data pengajuan kontrak kinerja untuk saat ini.</p>
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
