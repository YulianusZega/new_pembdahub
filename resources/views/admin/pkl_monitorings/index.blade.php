@extends(auth()->user()->isKetuaYayasan() ? 'layouts.yayasan' : 'layouts.admin')

@section('title', 'Laporan Monitoring PKL')
@section('page_title', 'Rekapitulasi Laporan Monitoring PKL Pembimbing')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Performa Guru Pembimbing</h2>
            <p class="text-sm text-slate-500 mt-1">Pantau kinerja Guru Pembimbing dalam melakukan monitoring ke DUDI.</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="p-4 font-semibold text-sm text-slate-600 uppercase tracking-wider">Nama Guru Pembimbing</th>
                        <th class="p-4 font-semibold text-sm text-slate-600 uppercase tracking-wider">Nama & Alamat DUDI</th>
                        <th class="p-4 font-semibold text-sm text-slate-600 uppercase tracking-wider text-center">Total Laporan</th>
                        <th class="p-4 font-semibold text-sm text-slate-600 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($teachers as $t)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                        {{ substr($t->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $t->full_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $t->employee_id ? 'NIP/NUPTK: ' . $t->employee->nik : 'ID Guru: ' . $t->id }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @php
                                    $uniquePlacements = $t->pklPlacements->unique(function($item) {
                                        return $item->dudi_id ? 'dudi_'.$item->dudi_id : 'name_'.$item->company_name;
                                    });
                                @endphp
                                @if($uniquePlacements->isNotEmpty())
                                    <div class="space-y-2">
                                        @foreach($uniquePlacements as $placement)
                                            @php
                                                $dudiName = $placement->dudi->name ?? $placement->company_name ?? 'DUDI Belum Ditentukan';
                                                $dudiAddress = $placement->dudi->address ?? $placement->company_address ?? 'Alamat belum tercatat';
                                            @endphp
                                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-200/80">
                                                <div class="flex items-start gap-2.5">
                                                    <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 mt-0.5">
                                                        <i class="fas fa-building text-xs"></i>
                                                    </div>
                                                    <div>
                                                        <p class="font-bold text-slate-800 text-sm leading-tight">{{ $dudiName }}</p>
                                                        <p class="text-xs text-slate-500 mt-1 flex items-start gap-1">
                                                            <i class="fas fa-map-marker-alt text-slate-400 shrink-0 mt-0.5"></i>
                                                            <span>{{ $dudiAddress }}</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum ada lokasi DUDI</span>
                                @endif
                            </td>
                            <td class="p-4 text-center">
                                <span class="px-3 py-1 {{ $t->pkl_monitorings_count > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} rounded-full text-xs font-bold">
                                    {{ $t->pkl_monitorings_count }} Laporan
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                @php
                                    $showRoute = auth()->user()->isKetuaYayasan() ? route('yayasan.pkl_monitorings.show', $t->id) : route('admin.pkl-alumni.monitorings.show', $t->id);
                                @endphp
                                <a href="{{ $showRoute }}" class="inline-flex items-center justify-center w-8 h-8 bg-white border border-slate-200 text-slate-600 rounded-lg hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-colors" title="Lihat Detail">
                                    <i class="fas fa-search"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-slate-500">
                                Belum ada data guru pembimbing PKL.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($teachers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50">
                {{ $teachers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
