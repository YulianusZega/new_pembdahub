{{--
    Yayasan Layout — extends unified master layout
    Theme: Violet/Purple
--}}
@extends('layouts.app', [
    'theme'       => 'violet',
    'sidebarId'   => 'yayasan-sidebar',
    'storageKey'  => 'yayasan_sidebar_collapsed',
    'portalName'  => 'Ketua Yayasan',
    'portalSub'   => 'Pembda Hub Oversight',
    'portalIcon'  => 'fas fa-landmark',
])

@section('sidebar-menu')
    @php
        $ac = 'bg-violet-50 text-violet-700 font-semibold active';
        $nc = 'text-gray-600 hover:bg-gray-50';
    @endphp

    <!-- Dashboard -->
    <a href="{{ route('yayasan.dashboard') }}" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('yayasan.dashboard') ? $ac : 'text-gray-700 hover:bg-gray-50' }}">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white shadow"><i class="fas fa-home text-xs"></i></div>
        <span class="text-sm flex-1 font-semibold">Dashboard</span>
    </a>

    <!-- Kalender Pendidikan -->
    <a href="{{ route('yayasan.calendar.index') }}" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('yayasan.calendar.*') ? $ac : 'text-gray-700 hover:bg-gray-50' }}">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white shadow"><i class="fas fa-calendar-alt text-xs"></i></div>
        <span class="text-sm flex-1 font-semibold">Kalender Pendidikan</span>
    </a>

    <!-- Undangan Pelatihan -->
    <a href="{{ route('yayasan.invitations') }}" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('yayasan.invitations') ? $ac : 'text-gray-700 hover:bg-gray-50' }}">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white shadow"><i class="fas fa-envelope-open-text text-xs"></i></div>
        <span class="text-sm flex-1">Undangan Pelatihan</span>
    </a>

    <!-- Finalisasi Perjanjian Kinerja -->
    <a href="{{ route('yayasan.performance_contracts.index') }}" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('yayasan.performance_contracts.*') ? $ac : 'text-gray-700 hover:bg-gray-50' }}">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center text-white shadow"><i class="fas fa-file-signature text-xs"></i></div>
        <span class="text-sm flex-1 font-semibold">Finalisasi Perjanjian Kinerja</span>
    </a>

    <!-- Evaluasi Perjanjian Kinerja -->
    <a href="{{ route('yayasan.performance_evaluations.index') }}" class="menu-item flex items-center gap-3 px-3 py-2.5 rounded-xl {{ request()->routeIs('yayasan.performance_evaluations.*') ? $ac : 'text-gray-700 hover:bg-gray-50' }}">
        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-400 to-blue-600 flex items-center justify-center text-white shadow"><i class="fas fa-star-half-alt text-xs"></i></div>
        <span class="text-sm flex-1 font-semibold">ACC Evaluasi Kinerja</span>
    </a>

    <!-- ════════════════ GROUP: UNIT PENDIDIKAN ════════════════ -->
    <div class="pt-4" data-menu-group="schools">
        <button class="menu-group-toggle open w-full flex items-center justify-between px-3 py-1.5 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-gray-600" onclick="toggleGroup(this)">
            <span class="flex items-center gap-2"><i class="fas fa-school text-[10px]"></i> Unit Pendidikan</span>
            <i class="fas fa-chevron-right text-[9px] chevron"></i>
        </button>
        <div class="menu-group-body mt-1 space-y-0.5" style="max-height:2000px">
            @php
                $schools = \App\Models\School::schoolsOnly()->where('is_active', true)->get();
            @endphp
            @foreach($schools as $school)
                <div class="menu-item flex items-center gap-3 px-3 py-2 rounded-xl text-sm text-gray-600">
                    <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center text-white text-[9px] font-bold">
                        {{ strtoupper(substr($school->type, 0, 3)) }}
                    </div>
                    <span>{{ $school->name }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- ════════════════ GROUP: PEMBDA ELITE ════════════════ -->
    <div class="pt-3" data-menu-group="elite">
        <button class="menu-group-toggle open w-full flex items-center justify-between px-3 py-1.5 text-xs font-bold text-gray-400 uppercase tracking-wider hover:text-gray-600" onclick="toggleGroup(this)">
            <span class="flex items-center gap-2"><i class="fas fa-trophy text-[10px]"></i> Pembda Elite</span>
            <i class="fas fa-chevron-right text-[9px] chevron"></i>
        </button>
        <div class="menu-group-body mt-1 space-y-0.5" style="max-height:2000px">
            <a href="{{ route('training.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->routeIs('training.*') ? $ac : $nc }}">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-sky-400 to-cyan-600 flex items-center justify-center text-white"><i class="fas fa-book-reader text-[10px]"></i></div>
                <span>Pelatihan PembdaHub</span>
            </a>
            <a href="{{ route('forum.index') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->routeIs('forum.*') ? $ac : $nc }}">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white"><i class="fas fa-comments text-[10px]"></i></div>
                <span>Forum & Kolaborasi</span>
            </a>
            <a href="{{ route('reputation.leaderboard') }}" class="menu-item flex items-center gap-3 px-3 py-2 rounded-xl text-sm {{ request()->routeIs('reputation.leaderboard') ? $ac : $nc }}">
                <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-white"><i class="fas fa-ranking-star text-[10px]"></i></div>
                <span>Hall of Fame</span>
            </a>
        </div>
    </div>
@endsection
