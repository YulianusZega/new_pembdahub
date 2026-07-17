@extends('layouts.admin')
@section('title', 'Pembinaan, Prestasi & Konseling Siswa - PembdaHUB')

@push('styles')
<style>
    /* === HERO GRADIENT HEADER === */
    .counseling-hero {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 40%, #1a1f3a 70%, #0f172a 100%);
        position: relative;
        overflow: hidden;
    }
    .counseling-hero::before {
        content: '';
        position: absolute;
        top: -80px; left: -80px;
        width: 350px; height: 350px;
        background: radial-gradient(circle, rgba(99,102,241,0.18) 0%, transparent 70%);
        pointer-events: none;
    }
    .counseling-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 80px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(244,63,94,0.14) 0%, transparent 70%);
        pointer-events: none;
    }

    /* === ANIMATED STAT CARDS === */
    .stat-card-glow {
        position: relative;
        transition: transform .25s ease, box-shadow .25s ease;
    }
    .stat-card-glow:hover { transform: translateY(-4px); }
    .stat-card-glow.blue:hover  { box-shadow: 0 16px 48px rgba(99,102,241,.22); }
    .stat-card-glow.rose:hover  { box-shadow: 0 16px 48px rgba(244,63,94,.22); }
    .stat-card-glow.amber:hover { box-shadow: 0 16px 48px rgba(245,158,11,.22); }
    .stat-card-glow.green:hover { box-shadow: 0 16px 48px rgba(16,185,129,.22); }

    .stat-icon-ring {
        width: 54px; height: 54px;
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }

    /* === COUNTER ANIMATION === */
    @keyframes countUp {
        from { opacity:0; transform: translateY(8px); }
        to   { opacity:1; transform: translateY(0); }
    }
    .stat-number { animation: countUp .6s ease forwards; }

    /* === LEADERBOARD DARK CARD === */
    .lb-card { background: linear-gradient(145deg, #0f172a, #1e293b); }
    .lb-rank-1 { background: linear-gradient(135deg, #fbbf24, #f59e0b); color: #1a0000; }
    .lb-rank-2 { background: linear-gradient(135deg, #94a3b8, #64748b); color: #fff; }
    .lb-rank-3 { background: linear-gradient(135deg, #cd7c3b, #a0522d); color: #fff; }
    .lb-rank-n { background: rgba(255,255,255,.08); color: #94a3b8; }

    /* === PRIORITY CARD === */
    .priority-card { background: linear-gradient(145deg, #fff, #fff9f9); border-left: 4px solid #f43f5e; }
    .severity-badge { border-radius: 8px; font-size:.65rem; font-weight:700; letter-spacing:.06em; text-transform:uppercase; padding: 3px 10px; }
    .sev-berat   { background:#fee2e2; color:#be123c; }
    .sev-sedang  { background:#fef3c7; color:#92400e; }
    .sev-ringan  { background:#d1fae5; color:#065f46; }
    .sev-kritis  { background:#4c0519; color:#fca5a5; }

    /* === TABLE PREMIUM === */
    .premium-table th {
        font-size:.68rem; font-weight:700; letter-spacing:.08em;
        text-transform:uppercase; padding: 14px 20px; color:#64748b;
        background: #f8fafc; border-bottom: 2px solid #e2e8f0;
    }
    .premium-table td { padding:14px 20px; vertical-align:middle; }
    .premium-table tbody tr {
        border-bottom: 1px solid #f1f5f9;
        transition: background .15s ease;
    }
    .premium-table tbody tr:hover { background: #f8faff; }
    .premium-table tbody tr:last-child { border-bottom: none; }

    /* === RECORD TYPE BADGE === */
    .type-badge-prestasi {
        background: linear-gradient(135deg, #3b82f6, #6366f1);
        color: #fff; border-radius: 10px;
        padding: 4px 12px; font-size:.65rem; font-weight:700;
        letter-spacing:.05em; text-transform: uppercase;
        display: inline-flex; align-items: center; gap: 5px;
    }
    .type-badge-pembinaan {
        background: linear-gradient(135deg, #f43f5e, #e11d48);
        color: #fff; border-radius: 10px;
        padding: 4px 12px; font-size:.65rem; font-weight:700;
        letter-spacing:.05em; text-transform: uppercase;
        display: inline-flex; align-items: center; gap: 5px;
    }

    /* === STATUS PILLS === */
    .status-open       { background:#f1f5f9; color:#475569; }
    .status-in_progress{ background:#dbeafe; color:#1d4ed8; }
    .status-resolved   { background:#d1fae5; color:#065f46; }
    .status-closed     { background:#f3f4f6; color:#6b7280; }

    /* === ACTION BUTTONS === */
    .action-btn {
        display:inline-flex; align-items:center; justify-content:center;
        width:34px; height:34px; border-radius:10px;
        border:1.5px solid #e2e8f0; background:#fff;
        color:#94a3b8; transition:all .18s ease; cursor:pointer;
        font-size:.78rem;
    }
    .action-btn:hover.view  { background:#eff6ff; border-color:#93c5fd; color:#3b82f6; }
    .action-btn:hover.edit  { background:#fffbeb; border-color:#fcd34d; color:#d97706; }
    .action-btn:hover.del   { background:#fff1f2; border-color:#fca5a5; color:#e11d48; }

    /* === FILTER BAR === */
    .filter-select, .filter-input {
        background:#fff; border:1.5px solid #e2e8f0;
        border-radius:12px; padding:10px 16px;
        font-size:.8rem; font-weight:600; color:#374151;
        outline:none; appearance:none; cursor:pointer;
        transition: border-color .2s ease;
        width:100%;
    }
    .filter-select:focus, .filter-input:focus { border-color:#6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.1); }

    /* === SHIMMER === */
    @keyframes shimmer {
        0%   { background-position: -1000px 0; }
        100% { background-position: 1000px 0; }
    }
    .hero-shimmer-line {
        height: 3px; border-radius: 2px;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.2) 50%, transparent 100%);
        background-size: 1000px 100%;
        animation: shimmer 3s infinite;
    }
</style>
@endpush

@section('content')
<div class="space-y-6 pb-12">

    {{-- ===================== HERO HEADER ===================== --}}
    <div class="counseling-hero rounded-3xl p-8 md:p-10 text-white">
        <div class="relative z-10">
            {{-- Title Row --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-white/10 backdrop-blur flex items-center justify-center text-3xl shadow-xl ring-1 ring-white/20">
                        📊
                    </div>
                    <div>
                        <p class="text-indigo-300 text-xs font-bold uppercase tracking-[.15em] mb-1">Sistem Monitoring Siswa</p>
                        <h1 class="text-2xl md:text-3xl font-extrabold leading-tight tracking-tight">Pembinaan, Prestasi &amp; Konseling</h1>
                        <p class="text-slate-400 text-sm mt-1">Rekap holistik perkembangan karakter &amp; pencapaian seluruh siswa SMKS Pembda Nias</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('admin.counseling.create-prestasi') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-900/40 hover:shadow-indigo-600/50 hover:-translate-y-0.5 transition duration-200">
                        <i class="fas fa-trophy"></i> Catat Prestasi
                    </a>
                    <a href="{{ route('admin.counseling.create-pembinaan') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-rose-500 to-pink-600 text-white rounded-xl text-sm font-bold shadow-lg shadow-rose-900/40 hover:shadow-rose-600/50 hover:-translate-y-0.5 transition duration-200">
                        <i class="fas fa-shield-halved"></i> Catat Pembinaan
                    </a>
                </div>
            </div>

            {{-- 3 Module Badges --}}
            <div class="flex flex-wrap gap-3 mt-6">
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl ring-1 ring-white/15 backdrop-blur-sm">
                    <span class="text-base">🏆</span>
                    <div>
                        <p class="text-xs font-bold text-white leading-tight">Prestasi Siswa</p>
                        <p class="text-[10px] text-indigo-300">Akademik · Olahraga · Seni · Lomba</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl ring-1 ring-white/15 backdrop-blur-sm">
                    <span class="text-base">🛡️</span>
                    <div>
                        <p class="text-xs font-bold text-white leading-tight">Pembinaan Karakter</p>
                        <p class="text-[10px] text-rose-300">Kedisiplinan · Perilaku · Absensi</p>
                    </div>
                </div>
                <div class="flex items-center gap-2 px-4 py-2 bg-white/10 rounded-xl ring-1 ring-white/15 backdrop-blur-sm">
                    <span class="text-base">💬</span>
                    <div>
                        <p class="text-xs font-bold text-white leading-tight">Konseling & BK</p>
                        <p class="text-[10px] text-sky-300">Pribadi · Sosial · Karir · Belajar</p>
                    </div>
                </div>
            </div>

            <div class="hero-shimmer-line mt-6"></div>
        </div>
    </div>

    {{-- ===================== STAT CARDS ===================== --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        {{-- Total Prestasi --}}
        <div class="stat-card-glow blue bg-white rounded-2xl p-5 border border-indigo-100 shadow-sm flex items-center gap-4">
            <div class="stat-icon-ring" style="background:linear-gradient(135deg,#6366f1,#3b82f6)">
                <i class="fas fa-trophy text-white"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-indigo-700 stat-number">{{ number_format($stats['total_achievement']) }}</p>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-0.5">Catatan Prestasi</p>
            </div>
        </div>

        {{-- Total Pembinaan --}}
        <div class="stat-card-glow rose bg-white rounded-2xl p-5 border border-rose-100 shadow-sm flex items-center gap-4">
            <div class="stat-icon-ring" style="background:linear-gradient(135deg,#f43f5e,#e11d48)">
                <i class="fas fa-user-shield text-white"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-rose-700 stat-number">{{ number_format($stats['total_cases']) }}</p>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-0.5">Kasus Pembinaan</p>
            </div>
        </div>

        {{-- Bintang Sekolah --}}
        <div class="stat-card-glow amber bg-white rounded-2xl p-5 border border-amber-100 shadow-sm flex items-center gap-4">
            <div class="stat-icon-ring" style="background:linear-gradient(135deg,#f59e0b,#d97706)">
                <i class="fas fa-star text-white"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-amber-700 stat-number">{{ count($stats['star_students']) }}</p>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-0.5">Bintang Sekolah</p>
            </div>
        </div>

        {{-- Kasus Kritis --}}
        <div class="stat-card-glow green bg-white rounded-2xl p-5 border border-red-100 shadow-sm flex items-center gap-4">
            <div class="stat-icon-ring" style="background:linear-gradient(135deg,#ef4444,#dc2626)">
                <i class="fas fa-exclamation-circle text-white"></i>
            </div>
            <div>
                <p class="text-2xl font-extrabold text-red-700 stat-number">{{ count($stats['priority_students']) }}</p>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-0.5">Prioritas Tinggi</p>
            </div>
        </div>
    </div>

    {{-- ===================== LEADERBOARD + PRIORITY ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Bintang Sekolah (Leaderboard) --}}
        <div class="lb-card rounded-3xl p-6 md:p-8 text-white shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.12em] text-indigo-400">🏅 Papan Peringkat</p>
                    <h2 class="text-lg font-extrabold mt-0.5">Bintang Sekolah</h2>
                </div>
                <span class="px-3 py-1 bg-amber-400/20 text-amber-300 text-xs font-bold rounded-full ring-1 ring-amber-400/30">
                    Top {{ count($stats['star_students']) }} Siswa
                </span>
            </div>
            <div class="space-y-3">
                @forelse($stats['star_students'] as $idx => $user)
                    @php $rank = $idx + 1; @endphp
                    <div class="flex items-center gap-4 p-3 rounded-2xl bg-white/5 hover:bg-white/10 transition">
                        {{-- Rank Badge --}}
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-xs font-extrabold flex-shrink-0
                            {{ $rank == 1 ? 'lb-rank-1' : ($rank == 2 ? 'lb-rank-2' : ($rank == 3 ? 'lb-rank-3' : 'lb-rank-n')) }}">
                            {{ $rank <= 3 ? ['🥇','🥈','🥉'][$rank-1] : $rank }}
                        </div>
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-xl overflow-hidden ring-2 ring-white/10 flex-shrink-0">
                            <img src="{{ $user->student->photo_url }}" class="w-full h-full object-cover" alt="{{ $user->student->full_name }}">
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold leading-tight truncate">{{ $user->student->full_name }}</p>
                            <p class="text-xs text-slate-400 mt-0.5">{{ $user->student->currentClassroom->first()?->class_name ?? '-' }}</p>
                        </div>
                        {{-- Points --}}
                        <div class="text-right flex-shrink-0">
                            <p class="text-base font-extrabold text-indigo-300">{{ $user->reputation->total_points }}</p>
                            <p class="text-[10px] text-slate-500 font-semibold uppercase">Poin</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="text-5xl mb-3">🌟</div>
                        <p class="text-slate-500 text-sm">Belum ada data bintang sekolah.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Perhatian Prioritas --}}
        <div class="priority-card rounded-3xl p-6 md:p-8 shadow-xl">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[.12em] text-rose-500">⚠️ Memerlukan Tindakan</p>
                    <h2 class="text-lg font-extrabold text-gray-900 mt-0.5">Perhatian Prioritas</h2>
                </div>
                @if(count($stats['priority_students']) > 0)
                <span class="px-3 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded-full ring-1 ring-rose-200">
                    {{ count($stats['priority_students']) }} Aktif
                </span>
                @endif
            </div>
            <div class="space-y-3">
                @forelse($stats['priority_students'] as $case)
                    <div class="flex items-center gap-3 p-3 bg-white rounded-2xl border border-rose-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-center text-rose-500 flex-shrink-0">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-800 truncate">{{ $case->student->full_name }}</p>
                            <p class="text-xs font-semibold text-rose-600 mt-0.5 uppercase tracking-wide">{{ str_replace('_',' ',$case->category) }}</p>
                        </div>
                        <span class="severity-badge
                            {{ $case->severity == 'berat' ? 'sev-berat' : ($case->severity == 'sedang' ? 'sev-sedang' : ($case->severity == 'kritis' ? 'sev-kritis' : 'sev-ringan')) }}">
                            {{ $case->severity }}
                        </span>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="text-5xl mb-3">✅</div>
                        <p class="text-slate-500 text-sm">Tidak ada kasus kritis aktif.</p>
                        <p class="text-slate-400 text-xs mt-1">Semua siswa dalam kondisi baik!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- ===================== FILTER + TABLE ===================== --}}
    <div class="bg-white rounded-3xl shadow-lg border border-slate-100 overflow-hidden">

        {{-- Filter Bar --}}
        <div class="p-6 md:p-8 bg-gradient-to-r from-slate-50 to-white border-b border-slate-100">
            <form method="GET" id="filterForm">
                <div class="flex flex-col lg:flex-row gap-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 flex-1">

                        {{-- Mode --}}
                        <div class="relative">
                            <select name="report_mode" id="filter-mode"
                                onchange="updateFilters(); document.getElementById('filterForm').submit();"
                                class="filter-select pr-10">
                                <option value="">Semua Laporan</option>
                                <option value="masalah"  {{ request('report_mode') === 'masalah'  ? 'selected' : '' }}>Pembinaan & Kasus</option>
                                <option value="prestasi" {{ request('report_mode') === 'prestasi' ? 'selected' : '' }}>Prestasi & Penghargaan</option>
                            </select>
                            <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                        </div>

                        {{-- Category --}}
                        <div class="relative">
                            <select name="category" id="filter-category" data-selected="{{ request('category') }}"
                                onchange="document.getElementById('filterForm').submit();"
                                class="filter-select pr-10">
                                <option value="">Kategori Fokus</option>
                            </select>
                            <i class="fas fa-sliders absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                        </div>

                        {{-- Level --}}
                        <div class="relative">
                            <select name="{{ request('report_mode') === 'prestasi' ? 'achievement_level' : 'severity' }}"
                                id="filter-level"
                                onchange="document.getElementById('filterForm').submit();"
                                class="filter-select pr-10">
                                <option value="">Status / Level</option>
                            </select>
                            <i class="fas fa-layer-group absolute right-4 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                        </div>

                        {{-- Search --}}
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama siswa..."
                                class="filter-input pl-10">
                            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-slate-800 to-slate-900 text-white rounded-xl text-sm font-bold hover:from-indigo-700 hover:to-indigo-800 transition shadow-md">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                        <a href="{{ route('admin.counseling.index') }}"
                            class="px-4 py-2.5 bg-white border-2 border-slate-100 text-slate-400 rounded-xl hover:bg-slate-50 transition flex items-center justify-center" title="Reset Filter">
                            <i class="fas fa-rotate-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left premium-table">
                <thead>
                    <tr>
                        <th>Siswa &amp; Kelas</th>
                        <th class="text-center">Tipe</th>
                        <th>Judul &amp; Kategori</th>
                        <th class="text-center">Level / Tingkat</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($records as $record)
                    <tr>
                        {{-- Siswa --}}
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl overflow-hidden ring-2 ring-slate-100 flex-shrink-0">
                                    <img src="{{ $record->student->photo_url }}" class="w-full h-full object-cover" alt="{{ $record->student->full_name }}">
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-gray-900 leading-tight">{{ $record->student->full_name }}</p>
                                    <p class="text-xs text-slate-400 font-semibold mt-0.5 uppercase">{{ $record->student->currentClassroom->first()?->class_name ?? '-' }}</p>
                                </div>
                            </div>
                        </td>

                        {{-- Tipe --}}
                        <td class="text-center">
                            @if($record->record_type === 'penghargaan')
                                <span class="type-badge-prestasi"><i class="fas fa-trophy text-[10px]"></i> Prestasi</span>
                            @else
                                <span class="type-badge-pembinaan"><i class="fas fa-user-shield text-[10px]"></i> Pembinaan</span>
                            @endif
                        </td>

                        {{-- Judul --}}
                        <td>
                            <p class="text-sm font-bold text-slate-700 max-w-[220px] truncate" title="{{ $record->title }}">{{ $record->title }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-[11px] font-semibold px-2 py-0.5 rounded-md
                                    {{ $record->record_type == 'penghargaan' ? 'bg-indigo-50 text-indigo-600' : 'bg-rose-50 text-rose-600' }}">
                                    {{ str_replace('_', ' ', $record->category) }}
                                </span>
                                <span class="text-[10px] text-slate-300">•</span>
                                <span class="text-xs text-slate-400 font-medium">{{ $record->incident_date ? $record->incident_date->format('d M Y') : '-' }}</span>
                            </div>
                        </td>

                        {{-- Level --}}
                        <td class="text-center">
                            @if($record->record_type === 'penghargaan' && $record->achievement_level)
                                <span class="inline-block px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-100">
                                    Tingkat {{ ucfirst($record->achievement_level) }}
                                </span>
                            @elseif($record->severity)
                                <span class="severity-badge
                                    {{ $record->severity == 'berat' ? 'sev-berat' : ($record->severity == 'sedang' ? 'sev-sedang' : ($record->severity == 'kritis' ? 'sev-kritis' : 'sev-ringan')) }}">
                                    {{ $record->severity }}
                                </span>
                            @else
                                <span class="text-slate-300 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="text-center">
                            @php
                                $statusMap = [
                                    'open'        => ['label' => 'Baru',    'cls' => 'status-open'],
                                    'in_progress' => ['label' => 'Proses',  'cls' => 'status-in_progress'],
                                    'resolved'    => ['label' => 'Selesai', 'cls' => 'status-resolved'],
                                    'closed'      => ['label' => 'Ditutup', 'cls' => 'status-closed'],
                                ];
                                $st = $statusMap[$record->status] ?? ['label' => $record->status, 'cls' => 'status-open'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold {{ $st['cls'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $record->status === 'in_progress' ? 'bg-blue-500 animate-pulse' : 'bg-current' }}"></span>
                                {{ $st['label'] }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <a href="{{ route('admin.counseling.show', $record) }}" class="action-btn view" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.counseling.edit', $record) }}" class="action-btn edit" title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.counseling.destroy', $record) }}" method="POST"
                                    onsubmit="return confirm('Hapus catatan ini secara permanen?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-btn del" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="text-6xl mb-4">📋</div>
                            <h3 class="text-lg font-bold text-gray-400">Tidak Ada Data</h3>
                            <p class="text-sm text-gray-400 mt-1">Belum ada catatan untuk filter yang dipilih.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-8 py-6 bg-slate-50/70 border-t border-slate-100">
            {{ $records->links() }}
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        updateFilters();
        animateCounters();
    });

    // ── Animasi angka counter ──
    function animateCounters() {
        document.querySelectorAll('.stat-number').forEach(el => {
            const target = parseInt(el.textContent.replace(/,/g, ''));
            if (isNaN(target) || target === 0) return;
            let current = 0;
            const step = Math.ceil(target / 40);
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = current.toLocaleString();
                if (current >= target) clearInterval(timer);
            }, 25);
        });
    }

    // ── Dynamic filter options ──
    const catsMasalah = [
        {v:'perilaku',      t:'Karakter & Perilaku'},
        {v:'kedisiplinan',  t:'Kedisiplinan'},
        {v:'absensi',       t:'Absensi'},
        {v:'akademik',      t:'Akademik'},
        {v:'sosial',        t:'Sosial'},
        {v:'pribadi',       t:'Pribadi'},
    ];
    const catsPrestasi = [
        {v:'akademik',   t:'Akademik & Sains'},
        {v:'olahraga',   t:'Olahraga'},
        {v:'seni',       t:'Seni & Desain'},
        {v:'keagamaan',  t:'Religius'},
        {v:'karir',      t:'Lomba Kejuruan'},
    ];

    function updateFilters() {
        const mode        = document.getElementById('filter-mode').value;
        const catSelect   = document.getElementById('filter-category');
        const levelSelect = document.getElementById('filter-level');
        const selectedCat = catSelect.getAttribute('data-selected');

        catSelect.innerHTML   = '<option value="">Semua Kategori</option>';
        levelSelect.innerHTML = '<option value="">Semua Level</option>';

        let categories = [];
        let levels     = [];

        if (mode === 'prestasi') {
            categories = catsPrestasi;
            levels = [
                {v:'sekolah',       t:'Sekolah'},
                {v:'kabupaten',     t:'Kabupaten'},
                {v:'propinsi',      t:'Propinsi'},
                {v:'nasional',      t:'Nasional'},
                {v:'internasional', t:'Internasional'},
            ];
            levelSelect.name = 'achievement_level';
        } else if (mode === 'masalah') {
            categories = catsMasalah;
            levels = [
                {v:'ringan', t:'Ringan'},
                {v:'sedang', t:'Sedang'},
                {v:'berat',  t:'Berat'},
                {v:'kritis', t:'Kritis'},
            ];
            levelSelect.name = 'severity';
        }

        categories.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.v; opt.text = c.t;
            if (c.v === selectedCat) opt.selected = true;
            catSelect.add(opt);
        });

        levels.forEach(l => {
            const opt = document.createElement('option');
            opt.value = l.v; opt.text = l.t;
            levelSelect.add(opt);
        });

        const urlParams = new URLSearchParams(window.location.search);
        const lName = (mode === 'prestasi') ? 'achievement_level' : 'severity';
        if (urlParams.get(lName)) levelSelect.value = urlParams.get(lName);
    }
</script>
@endpush
