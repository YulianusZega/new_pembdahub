@extends('layouts.admin')
@section('title', 'Perkembangan & Prestasi Siswa - PembdaHUB')

@section('content')
<div class="space-y-8 pb-12">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-slate-900 flex items-center justify-center text-white shadow-lg">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Pusat Perkembangan Siswa</h1>
                <p class="text-slate-500 font-medium">Monitoring prestasi dan pembinaan karakter siswa</p>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.counseling.create-prestasi') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl font-semibold text-sm shadow-lg shadow-blue-200 hover:shadow-blue-400 hover:-translate-y-0.5 transition duration-300">
                <i class="fas fa-trophy mr-2"></i> Input Prestasi
            </a>
            <a href="{{ route('admin.counseling.create-pembinaan') }}" class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-rose-600 to-rose-700 text-white rounded-xl font-semibold text-sm shadow-lg shadow-rose-200 hover:shadow-rose-400 hover:-translate-y-0.5 transition duration-300">
                <i class="fas fa-shield-halved mr-2"></i> Input Pembinaan
            </a>
        </div>
    </div>

    {{-- Highlights Dashboard: "Menghunjuk Siswa" --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Total Stats Card --}}
        <div class="bg-white rounded-2xl p-8 shadow-lg border border-gray-100 flex flex-col justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Ikhtisar Sistem</span>
                <div class="mt-4 flex flex-col gap-4">
                    <div class="flex items-center justify-between p-4 bg-blue-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                <i class="fas fa-trophy text-sm"></i>
                            </div>
                            <span class="text-xs font-semibold text-blue-800 uppercase">Total Prestasi</span>
                        </div>
                        <span class="text-2xl font-bold text-blue-900">{{ number_format($stats['total_achievement']) }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-rose-50 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-rose-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-rose-200">
                                <i class="fas fa-user-shield text-sm"></i>
                            </div>
                            <span class="text-xs font-semibold text-rose-800 uppercase">Total Pembinaan</span>
                        </div>
                        <span class="text-2xl font-bold text-rose-900">{{ number_format($stats['total_cases']) }}</span>
                    </div>
                </div>
            </div>
            <p class="mt-6 text-xs text-gray-400 italic">Data diperbarui secara otomatis berdasarkan log aktivitas.</p>
        </div>

        {{-- Star Students: "Bintang Sekolah" --}}
        <div class="bg-slate-900 rounded-2xl p-8 shadow-lg text-white">
            <div class="flex items-center justify-between mb-6">
                <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Bintang Sekolah</span>
                <i class="fas fa-star text-amber-400"></i>
            </div>
            <div class="space-y-4">
                @forelse($stats['star_students'] as $user)
                    <div class="flex items-center justify-between p-3 bg-white/5 rounded-2xl border border-white/5 hover:bg-white/10 transition cursor-help">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0">
                                <img src="{{ $user->student->photo_url }}" class="w-full h-full object-cover" alt="{{ $user->student->full_name }}">
                            </div>
                            <div>
                                <p class="text-xs font-bold leading-tight">{{ $user->student->full_name }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $user->student->currentClassroom->first()?->class_name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-bold text-blue-400">{{ $user->reputation->total_points }}</p>
                            <p class="text-xs uppercase font-semibold text-slate-500">Pts</p>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic text-center py-8">Belum ada siswa berprestasi.</p>
                @endforelse
            </div>
        </div>

        {{-- Priority Cases: "Perhatian Khusus" --}}
        <div class="bg-white rounded-2xl p-8 shadow-lg border border-rose-100">
            <div class="flex items-center justify-between mb-6">
                <span class="text-xs font-semibold text-rose-400 uppercase tracking-wider">Perhatian Prioritas</span>
                <i class="fas fa-exclamation-triangle text-rose-500"></i>
            </div>
            <div class="space-y-4">
                @forelse($stats['priority_students'] as $case)
                    <div class="flex items-center justify-between p-3 bg-rose-50/50 rounded-2xl border border-rose-100 hover:bg-rose-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-white border border-rose-200 flex items-center justify-center text-rose-600 text-xs">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-800 leading-tight">{{ $case->student->full_name }}</p>
                                <p class="text-xs text-rose-600 font-semibold mt-0.5">{{ strtoupper($case->category) }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-semibold px-2 py-0.5 rounded bg-rose-500 text-white uppercase">{{ $case->severity }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-8">Tidak ada kasus kritis aktif.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Main Filter & Table Section --}}
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
        {{-- Enhanced Filters --}}
        <div class="p-8 border-b border-slate-100 bg-slate-50/50">
            <form method="GET" id="filterForm" class="flex flex-col lg:flex-row gap-4">
                <div class="grid grid-cols-1 md:grid-cols-4 flex-1 gap-4">
                    {{-- Mode Filter --}}
                    <div class="relative group">
                        <select name="report_mode" id="filter-mode" onchange="updateFilters(); document.getElementById('filterForm').submit();" 
                            class="w-full bg-white border-2 border-gray-100 rounded-xl px-5 py-3 text-xs font-semibold text-gray-600 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Semua Laporan</option>
                            <option value="masalah" {{ request('report_mode') === 'masalah' ? 'selected' : '' }}>Pembinaan & Kasus</option>
                            <option value="prestasi" {{ request('report_mode') === 'prestasi' ? 'selected' : '' }}>Prestasi & Penghargaan</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                    </div>

                    {{-- Category Filter --}}
                    <div class="relative group">
                        <select name="category" id="filter-category" data-selected="{{ request('category') }}" onchange="document.getElementById('filterForm').submit();"
                            class="w-full bg-white border-2 border-gray-100 rounded-xl px-5 py-3 text-xs font-semibold text-gray-600 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Kategori Fokus</option>
                        </select>
                        <i class="fas fa-sliders absolute right-5 top-1/2 -translate-y-1/2 text-slate-300 text-[10px] pointer-events-none"></i>
                    </div>

                    {{-- Level Filter --}}
                    <div class="relative group">
                        <select name="{{ request('report_mode') === 'prestasi' ? 'achievement_level' : 'severity' }}" id="filter-level" onchange="document.getElementById('filterForm').submit();"
                            class="w-full bg-white border-2 border-gray-100 rounded-xl px-5 py-3 text-xs font-semibold text-gray-600 focus:border-blue-500 outline-none transition-all appearance-none cursor-pointer">
                            <option value="">Status/Level</option>
                        </select>
                        <i class="fas fa-layer-group absolute right-5 top-1/2 -translate-y-1/2 text-gray-300 text-xs pointer-events-none"></i>
                    </div>

                    {{-- Search --}}
                    <div class="relative group">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama siswa..." 
                            class="w-full bg-white border-2 border-gray-100 rounded-xl px-12 py-3 text-xs font-semibold text-gray-800 placeholder:text-gray-400 focus:border-blue-500 outline-none transition-all">
                        <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white rounded-xl font-semibold text-sm hover:bg-slate-800 transition shadow-lg">
                        Filter
                    </button>
                    <a href="{{ route('admin.counseling.index') }}" class="px-5 py-2.5 bg-white border-2 border-gray-100 text-gray-400 rounded-xl hover:bg-gray-50 transition flex items-center justify-center">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>

        {{-- Premium Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="bg-slate-50/50">
                        <th class="px-8 py-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa & Kelas</th>
                        <th class="px-6 py-5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Tipe/Mode</th>
                        <th class="px-6 py-5 text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul & Kategori</th>
                        <th class="px-6 py-5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Keseriusan/Level</th>
                        <th class="px-6 py-5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-8 py-5 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($records as $record)
                    <tr class="group hover:bg-slate-50/50 transition duration-200">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-4">
                                <div class="w-11 h-11 rounded-xl overflow-hidden flex-shrink-0">
                                    <img src="{{ $record->student->photo_url }}" class="w-full h-full object-cover" alt="{{ $record->student->full_name }}">
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-800 leading-tight">{{ $record->student->full_name }}</p>
                                    <p class="text-xs font-semibold text-gray-400 mt-0.5 uppercase">{{ $record->student->currentClassroom->first()?->class_name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($record->record_type === 'penghargaan')
                                <div class="inline-flex flex-col items-center">
                                    <span class="w-8 h-8 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-1">
                                        <i class="fas fa-trophy text-[10px]"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-blue-500 uppercase tracking-wider">Prestasi</span>
                                </div>
                            @else
                                <div class="inline-flex flex-col items-center">
                                    <span class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center mb-1">
                                        <i class="fas fa-user-shield text-[10px]"></i>
                                    </span>
                                    <span class="text-xs font-semibold text-rose-500 uppercase tracking-wider">Pembinaan</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs font-bold text-slate-700 max-w-[200px] truncate mb-1" title="{{ $record->title }}">{{ $record->title }}</p>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold px-1.5 py-0.5 rounded {{ $record->record_type == 'penghargaan' ? 'bg-blue-50 text-blue-600' : 'bg-rose-50 text-rose-600' }} uppercase">
                                    {{ str_replace('_', ' ', $record->category) }}
                                </span>
                                <span class="text-[9px] text-slate-300">•</span>
                                <span class="text-xs font-semibold text-gray-400">{{ $record->incident_date ? $record->incident_date->format('d M') : '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-center">
                            @if($record->record_type === 'penghargaan' && $record->achievement_level)
                                <span class="inline-block px-3 py-1 rounded-lg bg-blue-50 text-blue-700 text-xs font-semibold uppercase tracking-wider border border-blue-100">
                                    Tingkat {{ $record->achievement_level }}
                                </span>
                            @else
                                <span class="inline-block px-3 py-1 rounded-lg text-xs font-semibold uppercase tracking-wider border {{ $record->severity == 'berat' ? 'bg-rose-50 text-rose-700 border-rose-100' : ($record->severity == 'sedang' ? 'bg-amber-50 text-amber-700 border-amber-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100') }}">
                                    {{ $record->severity }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-5 text-center">
                            @php
                                $statusMap = [
                                    'open' => ['label' => 'Baru', 'color' => 'slate'],
                                    'in_progress' => ['label' => 'Proses', 'color' => 'blue'],
                                    'resolved' => ['label' => 'Selesai', 'color' => 'emerald'],
                                    'closed' => ['label' => 'Ditutup', 'color' => 'gray']
                                ];
                                $st = $statusMap[$record->status] ?? ['label' => $record->status, 'color' => 'slate'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-{{ $st['color'] }}-100 text-{{ $st['color'] }}-700 text-xs font-semibold uppercase">
                                <span class="w-1 h-1 rounded-full bg-{{ $st['color'] }}-500 animate-pulse"></span>
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition duration-300">
                                <a href="{{ route('admin.counseling.show', $record) }}" class="p-2.5 bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-blue-600 hover:border-blue-200 shadow-sm transition">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.counseling.edit', $record) }}" class="p-2.5 bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-amber-600 hover:border-amber-200 shadow-sm transition">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.counseling.destroy', $record) }}" method="POST" onsubmit="return confirm('Hapus Permanen Catatan?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2.5 bg-white border border-slate-100 text-slate-400 rounded-xl hover:text-rose-600 hover:border-rose-200 shadow-sm transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <img src="https://img.icons8.com/bubbles/200/empty-box.png" class="w-32 h-32 mx-auto mb-4 opacity-30 grayscale">
                            <h3 class="text-lg font-bold text-gray-400 uppercase tracking-wider">Data Tidak Ditemukan</h3>
                            <p class="text-gray-400 font-medium">Belum ada catatan perkembangan untuk filter ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-8 py-6 bg-slate-50/50 border-t border-slate-50">
            {{ $records->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateFilters();
    });

    const catsMasalah = [
        {v:'perilaku', t:'Karakter & Perilaku'}, {v:'kedisiplinan', t:'Kedisiplinan'}, 
        {v:'absensi', t:'Absensi'}, {v:'akademik', t:'Akademik'}, 
        {v:'sosial', t:'Sosial'}, {v:'pribadi', t:'Pribadi'}
    ];
    
    const catsPrestasi = [
        {v:'akademik', t:'Akademik & Sains'}, {v:'olahraga', t:'Olahraga'}, 
        {v:'seni', t:'Seni & Desain'}, {v:'keagamaan', t:'Religius'}, {v:'karir', t:'Lomba Kejuruan'}
    ];

    function updateFilters() {
        const mode = document.getElementById('filter-mode').value;
        const catSelect = document.getElementById('filter-category');
        const levelSelect = document.getElementById('filter-level');
        const selectedCat = catSelect.getAttribute('data-selected');

        catSelect.innerHTML = '<option value="">Semua Kategori</option>';
        levelSelect.innerHTML = '<option value="">Semua Level</option>';

        let categories = [];
        let levels = [];

        if (mode === 'prestasi') {
            categories = catsPrestasi;
            levels = [
                {v:'sekolah', t:'Sekolah'}, {v:'kabupaten', t:'Kabupaten'}, 
                {v:'propinsi', t:'Propinsi'}, {v:'nasional', t:'Nasional'}, {v:'internasional', t:'Internasional'}
            ];
            levelSelect.name = 'achievement_level';
        } else if (mode === 'masalah') {
            categories = catsMasalah;
            levels = [
                {v:'ringan', t:'Ringan'}, {v:'sedang', t:'Sedang'}, 
                {v:'berat', t:'Berat'}, {v:'kritis', t:'Kritis'}
            ];
            levelSelect.name = 'severity';
        }

        categories.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.v; opt.text = c.t;
            if(c.v === selectedCat) opt.selected = true;
            catSelect.add(opt);
        });

        levels.forEach(l => {
            const opt = document.createElement('option');
            opt.value = l.v; opt.text = l.t;
            levelSelect.add(opt);
        });
        
        // Match current URL params for level
        const urlParams = new URLSearchParams(window.location.search);
        const lName = (mode === 'prestasi') ? 'achievement_level' : 'severity';
        if(urlParams.get(lName)) levelSelect.value = urlParams.get(lName);
    }
</script>
@endpush
