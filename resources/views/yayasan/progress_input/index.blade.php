@extends('layouts.yayasan')

@section('title', 'Progress Input Data - Ketua Yayasan')

@section('content')
<div class="space-y-6">
    {{-- Header Banner --}}
    <div class="bg-gradient-to-r from-violet-600 via-purple-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 opacity-10 text-9xl">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 relative z-10">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-2xl">📊</span>
                    <span class="bg-white/20 px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider">Monitoring Yayasan</span>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight">Progress Input Data</h1>
                <p class="text-white/80 text-sm mt-1">Rekapitulasi perkembangan penginputan data seluruh unit sekolah TP. {{ $currentYear->year ?? '2026/2027' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                {{-- Form Filter Tahun Pelajaran --}}
                <form action="{{ route('yayasan.progress-input') }}" method="GET" class="flex items-center gap-2 m-0 p-0">
                    <select name="academic_year_id" onchange="this.form.submit()" class="bg-white/15 text-white border border-white/30 rounded-xl px-3 py-2 text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-white/50 backdrop-blur-sm">
                        @foreach($allYears as $year)
                            <option value="{{ $year->id }}" class="text-gray-800" {{ ($currentYear && $currentYear->id == $year->id) ? 'selected' : '' }}>
                                TP. {{ $year->year }} {{ $year->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                {{-- Tombol Export PDF --}}
                <a href="{{ route('yayasan.progress-input.export-pdf', ['academic_year_id' => request('academic_year_id')]) }}" 
                   class="inline-flex items-center gap-2 bg-gradient-to-r from-rose-500 to-red-600 hover:from-rose-600 hover:to-red-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5">
                    <i class="fas fa-file-pdf text-lg"></i>
                    <span>Eksport PDF</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Keterangan Indikator Status --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-sm font-bold text-gray-700">
            <i class="fas fa-info-circle text-violet-600"></i>
            <span>Status Perkembangan:</span>
        </div>
        <div class="flex flex-wrap items-center gap-4 text-xs font-semibold">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Lengkap / Sudah Terinput
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                <span class="w-2 h-2 rounded-full bg-amber-500"></span> Dalam Proses / Belum Maksimal
            </span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                <span class="w-2 h-2 rounded-full bg-rose-500"></span> Belum Terisi (0)
            </span>
        </div>
    </div>

    {{-- Tabel Rekapitulasi Progress Input --}}
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-900 via-gray-800 to-gray-900 text-white text-left text-xs uppercase tracking-wider font-extrabold border-b border-gray-700">
                        <th class="py-4 px-5 w-1/4 border-r border-gray-700">Item</th>
                        <th class="py-4 px-4 w-40 border-r border-gray-700">Unit Sekolah</th>
                        <th class="py-4 px-4 w-48 text-center border-r border-gray-700">Perkembangan</th>
                        <th class="py-4 px-4 w-32 text-center border-r border-gray-700">Satuan</th>
                        <th class="py-4 px-5">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm">
                    @forelse($items as $item)
                        @php
                            $schoolsCount = count($item['schools_data']);
                        @endphp
                        @foreach($item['schools_data'] as $idx => $s)
                            <tr class="hover:bg-violet-50/40 transition-colors {{ $idx === $schoolsCount - 1 ? 'border-b-4 border-gray-200' : '' }}">
                                {{-- Kolom Item (Merged rowspan untuk semua sekolah) --}}
                                @if($idx === 0)
                                    <td rowspan="{{ $schoolsCount }}" class="py-4 px-5 align-top bg-gray-50/60 border-r border-gray-200">
                                        <div class="flex items-start gap-3">
                                            <span class="flex-shrink-0 w-7 h-7 rounded-lg bg-violet-600 text-white font-extrabold text-xs flex items-center justify-center shadow-sm mt-0.5">
                                                {{ $item['number'] }}
                                            </span>
                                            <div>
                                                <h4 class="font-extrabold text-gray-900 text-base leading-snug">{{ $item['title'] }}</h4>
                                                <p class="text-xs text-gray-500 mt-1.5 leading-relaxed">{{ $item['description'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                @endif

                                {{-- Kolom Unit Sekolah --}}
                                <td class="py-4 px-4 align-middle font-bold text-gray-800 border-r border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-violet-500"></div>
                                        <span>{{ $s['school_name'] }}</span>
                                    </div>
                                </td>

                                {{-- Kolom Perkembangan --}}
                                <td class="py-4 px-4 align-middle text-center border-r border-gray-200">
                                    @if($s['status_color'] === 'green')
                                        <span class="inline-block px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold text-xs shadow-sm border border-emerald-300">
                                            <i class="fas fa-check-circle mr-1"></i> {{ $s['perkembangan'] }}
                                        </span>
                                    @elseif($s['status_color'] === 'amber')
                                        <span class="inline-block px-3 py-1 rounded-xl bg-amber-100 text-amber-800 font-extrabold text-xs shadow-sm border border-amber-300">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> {{ $s['perkembangan'] }}
                                        </span>
                                    @else
                                        <span class="inline-block px-3 py-1 rounded-xl bg-rose-100 text-rose-800 font-extrabold text-xs shadow-sm border border-rose-300">
                                            <i class="fas fa-times-circle mr-1"></i> {{ $s['perkembangan'] }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Satuan --}}
                                <td class="py-4 px-4 align-middle text-center font-bold text-gray-600 border-r border-gray-200">
                                    <span class="bg-gray-100 text-gray-700 px-2.5 py-1 rounded-lg text-xs font-semibold border border-gray-200">
                                        {{ $s['satuan'] }}
                                    </span>
                                </td>

                                {{-- Kolom Rekomendasi --}}
                                <td class="py-4 px-5 align-middle text-gray-700 text-xs leading-relaxed">
                                    <div class="flex items-start gap-2">
                                        @if($s['status_color'] === 'green')
                                            <i class="fas fa-check text-emerald-600 mt-0.5 flex-shrink-0"></i>
                                        @elseif($s['status_color'] === 'amber')
                                            <i class="fas fa-lightbulb text-amber-500 mt-0.5 flex-shrink-0"></i>
                                        @else
                                            <i class="fas fa-arrow-right text-rose-500 mt-0.5 flex-shrink-0"></i>
                                        @endif
                                        <span class="font-medium">{{ $s['rekomendasi'] }}</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 text-gray-300 block"></i>
                                <span class="font-semibold">Belum ada data indikator yang tersedia.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
