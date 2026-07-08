@extends($layout)

@section('title', 'Pelatihan PembdaHub')

@section('content')
@php
    $categoryGradients = [
        'panduan_umum'    => 'from-blue-400 to-blue-600',
        'fitur_admin'     => 'from-indigo-400 to-indigo-600',
        'fitur_guru'      => 'from-emerald-400 to-emerald-600',
        'fitur_siswa'     => 'from-amber-400 to-amber-600',
        'fitur_orangtua'  => 'from-cyan-400 to-cyan-600',
        'fitur_keuangan'  => 'from-green-400 to-green-600',
        'fitur_yayasan'   => 'from-violet-400 to-violet-600',
    ];
    $categoryIcons = [
        'panduan_umum'    => 'fas fa-book',
        'fitur_admin'     => 'fas fa-user-shield',
        'fitur_guru'      => 'fas fa-chalkboard-teacher',
        'fitur_siswa'     => 'fas fa-user-graduate',
        'fitur_orangtua'  => 'fas fa-people-roof',
        'fitur_keuangan'  => 'fas fa-coins',
        'fitur_yayasan'   => 'fas fa-landmark',
    ];
    $categoryLabels = [
        'panduan_umum'    => 'Panduan Umum',
        'fitur_admin'     => 'Fitur Admin',
        'fitur_guru'      => 'Fitur Guru',
        'fitur_siswa'     => 'Fitur Siswa',
        'fitur_orangtua'  => 'Fitur Orang Tua',
        'fitur_keuangan'  => 'Fitur Keuangan',
        'fitur_yayasan'   => 'Fitur Yayasan',
    ];
@endphp

<!-- ═══════════════════ HEADER ═══════════════════ -->
<div class="relative overflow-hidden bg-gradient-to-br from-sky-500 via-cyan-500 to-teal-500 rounded-2xl mb-6 p-8 text-white">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute -top-10 -right-10 w-60 h-60 bg-white rounded-full"></div>
        <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-white rounded-full"></div>
    </div>
    <div class="relative flex items-center gap-5">
        <div class="w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
            <i class="fas fa-graduation-cap text-3xl"></i>
        </div>
        <div>
            <h1 class="text-2xl font-bold">Pelatihan PembdaHub</h1>
            <p class="text-sky-100 mt-1">Materi panduan dan sosialisasi penggunaan PembdaHub</p>
        </div>
    </div>
</div>

<!-- ═══════════════════ FILTER BAR ═══════════════════ -->
<div class="bg-white rounded-2xl shadow-md p-5 mb-6">
    <form method="GET" action="{{ route('training.index') }}" class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari materi pelatihan..."
                   class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-300 focus:border-sky-400 outline-none transition">
        </div>
        <div class="relative">
            <select name="category" onchange="this.form.submit()"
                    class="appearance-none w-full sm:w-56 pl-4 pr-10 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-300 focus:border-sky-400 outline-none bg-white transition">
                <option value="">Semua Kategori</option>
                <option value="panduan_umum" {{ request('category') == 'panduan_umum' ? 'selected' : '' }}>Panduan Umum</option>
                <option value="fitur_admin" {{ request('category') == 'fitur_admin' ? 'selected' : '' }}>Fitur Admin</option>
                <option value="fitur_guru" {{ request('category') == 'fitur_guru' ? 'selected' : '' }}>Fitur Guru</option>
                <option value="fitur_siswa" {{ request('category') == 'fitur_siswa' ? 'selected' : '' }}>Fitur Siswa</option>
                <option value="fitur_orangtua" {{ request('category') == 'fitur_orangtua' ? 'selected' : '' }}>Fitur Orang Tua</option>
                <option value="fitur_keuangan" {{ request('category') == 'fitur_keuangan' ? 'selected' : '' }}>Fitur Keuangan</option>
                <option value="fitur_yayasan" {{ request('category') == 'fitur_yayasan' ? 'selected' : '' }}>Fitur Yayasan</option>
            </select>
            <i class="fas fa-chevron-down absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
        </div>
        <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 text-white rounded-xl text-sm font-medium hover:shadow-lg transition-all">
            <i class="fas fa-search mr-1.5"></i> Cari
        </button>
    </form>
</div>

<!-- ═══════════════════ MODULE CARDS ═══════════════════ -->
@if($modules->count() > 0)
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($modules as $module)
    @php
        $gradient = $categoryGradients[$module->category] ?? 'from-gray-400 to-gray-600';
        $icon     = $categoryIcons[$module->category] ?? 'fas fa-file-alt';
        $label    = $categoryLabels[$module->category] ?? ucfirst(str_replace('_', ' ', $module->category));
    @endphp
    <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
        <!-- Top gradient bar -->
        <div class="h-1 bg-gradient-to-r {{ $gradient }}"></div>

        <!-- Thumbnail Image -->
        <div class="relative w-full h-40 bg-gray-100 flex items-center justify-center overflow-hidden">
            @if($module->thumbnail_url)
                <img src="{{ $module->thumbnail_url }}" alt="{{ $module->title }}" class="w-full h-full object-cover">
            @else
                <div class="absolute inset-0 bg-gradient-to-br {{ $gradient }} opacity-10"></div>
                <i class="{{ $icon }} text-5xl text-gray-300 opacity-50"></i>
            @endif
            <!-- Category badge -->
            <div class="absolute top-4 right-4">
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gradient-to-r {{ $gradient }} text-white shadow">
                    {{ $label }}
                </span>
            </div>
            <!-- Difficulty Badge Overlay -->
            @php
                $diffBg = match($module->difficulty) {
                    'Pemula' => 'bg-emerald-500',
                    'Menengah' => 'bg-blue-500',
                    'Mahir' => 'bg-red-500',
                    default => 'bg-emerald-500',
                };
            @endphp
            <div class="absolute bottom-3 right-3 {{ $diffBg }} text-white px-2 py-1 rounded-lg text-[10px] font-bold shadow-md">
                {{ $module->difficulty ?? 'Pemula' }}
            </div>
            <!-- Icon Overlay -->
            <div class="absolute -bottom-6 left-6 w-12 h-12 rounded-xl bg-gradient-to-br {{ $gradient }} flex items-center justify-center text-white shadow-lg border-2 border-white">
                <i class="{{ $icon }} text-lg"></i>
            </div>
        </div>

        <div class="p-6 pt-10 relative">
            <!-- Title -->
            <h3 class="font-bold text-lg text-gray-800 mb-2 line-clamp-2">{{ $module->title }}</h3>

            <!-- Description -->
            <p class="text-gray-600 text-sm mb-4 line-clamp-3">{{ Str::limit(strip_tags($module->content), 100) }}</p>

            <!-- Bottom row -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-gray-500 font-medium flex items-center gap-1.5">
                        <i class="far fa-clock"></i> {{ $module->reading_time ?? 15 }} Menit
                    </span>
                    @if($module->pdf_file)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-50 text-red-600 rounded-lg text-[10px] font-semibold">
                        <i class="fas fa-file-pdf text-[10px]"></i> PDF
                    </span>
                    @endif
                </div>
                <a href="{{ route('training.show', $module) }}" class="inline-flex items-center gap-1.5 text-sm font-semibold bg-gradient-to-r {{ $gradient }} bg-clip-text text-transparent hover:opacity-80 transition">
                    Baca <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Pagination -->
@if($modules->hasPages())
<div class="mt-8">
    {{ $modules->withQueryString()->links() }}
</div>
@endif

@else
<!-- ═══════════════════ EMPTY STATE ═══════════════════ -->
<div class="bg-white rounded-2xl shadow-lg p-12 text-center">
    <div class="w-20 h-20 mx-auto bg-gradient-to-br from-sky-100 to-cyan-100 rounded-2xl flex items-center justify-center mb-5">
        <i class="fas fa-graduation-cap text-3xl text-sky-400"></i>
    </div>
    <h3 class="text-lg font-bold text-gray-700 mb-2">Belum ada materi pelatihan untuk Anda</h3>
    <p class="text-gray-500 text-sm">Materi pelatihan akan tersedia segera. Silakan cek kembali nanti.</p>
</div>
@endif
@endsection
