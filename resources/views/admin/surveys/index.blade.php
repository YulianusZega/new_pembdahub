@extends('layouts.admin')

@section('title', 'Kelola Survey Kepuasan')

@section('content')
<style>
    .survey-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .survey-card:hover { transform: translateY(-2px); }
    .stat-card { transition: all 0.3s ease; }
    .stat-card:hover .stat-icon { transform: scale(1.1) rotate(-5deg); }
    .stat-icon { transition: transform 0.3s ease; }
    .action-btn { transition: all 0.2s ease; }
    .action-btn:hover { transform: scale(1.1); }
    .badge-pulse { animation: pulse-glow 2s infinite; }
    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 0 0 0 rgba(99,102,241,0); }
        50% { box-shadow: 0 0 0 6px rgba(99,102,241,0.12); }
    }
    .filter-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 1.25em; padding-right: 2.5rem; }
    .table-row { transition: background-color 0.15s ease; }
    .gradient-text { background: linear-gradient(135deg, #6366f1, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
</style>

<div class="space-y-6">
    {{-- HERO HEADER --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl" style="background: linear-gradient(135deg, #312e81 0%, #4c1d95 40%, #6d28d9 70%, #7c3aed 100%);">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #fff, transparent); transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-10" style="background: radial-gradient(circle, #a78bfa, transparent); transform: translate(-30%, 30%);"></div>
        <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="relative px-8 py-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 backdrop-blur-sm border border-white/30 rounded-full text-xs font-bold uppercase tracking-wider" style="color: #ffffff;">
                        <i class="fas fa-poll-h text-[10px]"></i> Modul Evaluasi
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-400/90 border border-amber-300/50 rounded-full text-xs font-bold text-amber-950 uppercase tracking-wider">
                        <i class="fas fa-school text-[10px]"></i> Unit Sekolah
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight leading-tight" style="color: #ffffff;">
                    Kelola Survey Kepuasan
                </h1>
                <p class="text-sm leading-relaxed max-w-xl" style="color: rgba(255, 255, 255, 0.9);">
                    Buat dan kelola kuisioner survei untuk menilai tingkat kepuasan, kemampuan mengajar, dan kompetensi di Perguruan PEMBDA.
                </p>
            </div>

            <div class="flex-shrink-0">
                <a href="{{ route('admin.surveys.create') }}" class="inline-flex items-center gap-2.5 bg-white text-indigo-700 px-6 py-3.5 rounded-2xl font-extrabold text-sm hover:bg-indigo-50 transition-all duration-200 shadow-lg shadow-black/20 hover:shadow-xl hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle text-base"></i>
                    <span>Buat Survei Baru</span>
                </a>
            </div>
        </div>
    </div>

    {{-- STATS ROW --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total --}}
        <div class="stat-card bg-white rounded-2xl border border-gray-100/80 p-5 shadow-sm hover:shadow-lg relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="stat-icon w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-50 flex items-center justify-center text-indigo-600 shadow-sm">
                        <i class="fas fa-layer-group text-base"></i>
                    </div>
                    <span class="text-xs font-bold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-full border border-indigo-100">Total</span>
                </div>
                <p class="text-3xl font-black text-gray-800">{{ $stats['total'] }}</p>
                <p class="text-xs font-semibold text-gray-400 mt-1">Survei terdaftar</p>
            </div>
        </div>

        {{-- Active --}}
        <div class="stat-card bg-white rounded-2xl border border-gray-100/80 p-5 shadow-sm hover:shadow-lg relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="stat-icon w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-50 flex items-center justify-center text-emerald-600 shadow-sm">
                        <i class="fas fa-play-circle text-base"></i>
                    </div>
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-100 flex items-center gap-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block"></span>Aktif
                    </span>
                </div>
                <p class="text-3xl font-black text-emerald-600">{{ $stats['active'] }}</p>
                <p class="text-xs font-semibold text-gray-400 mt-1">Bisa diisi responden</p>
            </div>
        </div>

        {{-- Draft --}}
        <div class="stat-card bg-white rounded-2xl border border-gray-100/80 p-5 shadow-sm hover:shadow-lg relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="stat-icon w-11 h-11 rounded-xl bg-gradient-to-br from-sky-100 to-sky-50 flex items-center justify-center text-sky-600 shadow-sm">
                        <i class="fas fa-file-alt text-base"></i>
                    </div>
                    <span class="text-xs font-bold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-100">Draft</span>
                </div>
                <p class="text-3xl font-black text-sky-600">{{ $stats['draft'] }}</p>
                <p class="text-xs font-semibold text-gray-400 mt-1">Belum dipublikasikan</p>
            </div>
        </div>

        {{-- Closed --}}
        <div class="stat-card bg-white rounded-2xl border border-gray-100/80 p-5 shadow-sm hover:shadow-lg relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-rose-50/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative">
                <div class="flex items-start justify-between mb-4">
                    <div class="stat-icon w-11 h-11 rounded-xl bg-gradient-to-br from-rose-100 to-rose-50 flex items-center justify-center text-rose-600 shadow-sm">
                        <i class="fas fa-lock text-base"></i>
                    </div>
                    <span class="text-xs font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-100">Ditutup</span>
                </div>
                <p class="text-3xl font-black text-rose-600">{{ $stats['closed'] }}</p>
                <p class="text-xs font-semibold text-gray-400 mt-1">Pengisian berakhir</p>
            </div>
        </div>
    </div>

    {{-- FILTER PANEL --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center gap-2">
            <i class="fas fa-sliders-h text-indigo-500 text-sm"></i>
            <span class="font-bold text-gray-700 text-sm">Filter & Pencarian</span>
        </div>
        <div class="p-5">
            <form action="{{ route('admin.surveys.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                @if(auth()->user()->isSuperAdmin())
                <div class="flex-1 min-w-[180px] space-y-1.5">
                    <label for="school_id" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Unit Sekolah</label>
                    <select name="school_id" id="school_id" onchange="this.form.submit()" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition text-sm text-gray-700 font-medium">
                        <option value="">Semua Sekolah</option>
                        @foreach($schools as $sch)
                            <option value="{{ $sch->id }}" {{ request('school_id') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="min-w-[160px] space-y-1.5">
                    <label for="target_respondent" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Target Responden</label>
                    <select name="target_respondent" id="target_respondent" onchange="this.form.submit()" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition text-sm text-gray-700 font-medium">
                        <option value="">Semua Responden</option>
                        <option value="guru" {{ request('target_respondent') === 'guru' ? 'selected' : '' }}>Guru</option>
                        <option value="siswa" {{ request('target_respondent') === 'siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="semua" {{ request('target_respondent') === 'semua' ? 'selected' : '' }}>Semua</option>
                    </select>
                </div>

                <div class="min-w-[140px] space-y-1.5">
                    <label for="status" class="text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Status</label>
                    <select name="status" id="status" onchange="this.form.submit()" class="filter-select w-full px-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition text-sm text-gray-700 font-medium">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                @if(request('school_id') || request('target_respondent') || request('status'))
                    <div class="self-end">
                        <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold rounded-xl text-sm transition">
                            <i class="fas fa-times text-xs"></i>
                            <span>Reset Filter</span>
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- SURVEY TABLE --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-list-alt text-indigo-500 text-sm"></i>
                <span class="font-bold text-gray-700 text-sm">Daftar Survei</span>
                <span class="text-xs font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $surveys->total() }} survei</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border-collapse text-left">
                <thead>
                    <tr class="border-b border-gray-100" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <th class="p-4 pl-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Detail Survei</th>
                        <th class="p-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Unit Sekolah</th>
                        <th class="p-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Target</th>
                        <th class="p-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Status</th>
                        <th class="p-4 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Periode</th>
                        <th class="p-4 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Partisipasi</th>
                        <th class="p-4 pr-6 text-right text-[10px] font-extrabold text-gray-400 uppercase tracking-widest">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($surveys as $survey)
                        @php
                            $statusConfig = [
                                'draft'  => ['bg' => 'bg-sky-50', 'text' => 'text-sky-700', 'border' => 'border-sky-200', 'dot' => 'bg-sky-400', 'label' => 'Draft'],
                                'active' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500', 'label' => 'Aktif'],
                                'closed' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-200', 'dot' => 'bg-rose-400', 'label' => 'Ditutup'],
                            ];
                            $sc = $statusConfig[$survey->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200', 'dot' => 'bg-gray-400', 'label' => $survey->status];

                            $targetConfig = [
                                'guru'  => ['bg' => 'bg-purple-50', 'text' => 'text-purple-700', 'border' => 'border-purple-200', 'icon' => 'fa-chalkboard-teacher'],
                                'siswa' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-200', 'icon' => 'fa-user-graduate'],
                                'semua' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-200', 'icon' => 'fa-users'],
                            ];
                            $tc = $targetConfig[$survey->target_respondent] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'border' => 'border-gray-200', 'icon' => 'fa-users'];
                        @endphp
                        <tr class="table-row hover:bg-indigo-50/20 group">
                            <td class="p-4 pl-6">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center text-indigo-600 shadow-sm mt-0.5" style="background: linear-gradient(135deg, #eef2ff, #e0e7ff);">
                                        <i class="fas fa-poll-h text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-gray-800 leading-snug group-hover:text-indigo-700 transition-colors">{{ $survey->title }}</div>
                                        <div class="text-xs text-gray-400 truncate max-w-sm mt-0.5">{{ $survey->description ?? 'Tidak ada deskripsi.' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($survey->school)
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-school text-gray-400 text-xs"></i>
                                        <span class="font-semibold text-gray-700 text-sm">{{ $survey->school->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs font-bold bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg border border-gray-200">
                                        <i class="fas fa-globe text-[9px]"></i> Semua Unit
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold uppercase {{ $tc['bg'] }} {{ $tc['text'] }} border {{ $tc['border'] }}">
                                    <i class="fas {{ $tc['icon'] }} text-[9px]"></i>
                                    {{ $survey->target_respondent }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-extrabold {{ $sc['bg'] }} {{ $sc['text'] }} border {{ $sc['border'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }} {{ $survey->status === 'active' ? 'animate-pulse' : '' }}"></span>
                                    {{ $sc['label'] }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                        <i class="fas fa-play text-emerald-500 text-[10px] w-3"></i>
                                        <span class="font-medium">{{ $survey->start_date ? \Carbon\Carbon::parse($survey->start_date)->format('d M Y H:i') : 'Tidak diatur' }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5 text-xs text-gray-600">
                                        <i class="fas fa-stop text-rose-500 text-[10px] w-3"></i>
                                        <span class="font-medium">{{ $survey->end_date ? \Carbon\Carbon::parse($survey->end_date)->format('d M Y H:i') : 'Tidak diatur' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="inline-flex flex-col items-center">
                                    <span class="font-extrabold text-gray-800 text-lg leading-none">{{ $survey->responses_count }}</span>
                                    <span class="text-[10px] text-gray-400 font-semibold mt-0.5">Responden</span>
                                </div>
                            </td>
                            <td class="p-4 pr-6">
                                <div class="flex items-center justify-end gap-1.5">
                                    {{-- Manage Questions --}}
                                    <a href="{{ route('admin.surveys.questions', $survey->id) }}"
                                       class="action-btn inline-flex items-center justify-center w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white border border-indigo-100 hover:border-indigo-600 transition shadow-sm"
                                       title="Kelola Pertanyaan">
                                        <i class="fas fa-list-ol text-xs"></i>
                                    </a>
                                    {{-- View Results --}}
                                    <a href="{{ route('admin.surveys.results', $survey->id) }}"
                                       class="action-btn inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white border border-emerald-100 hover:border-emerald-600 transition shadow-sm"
                                       title="Analisis Hasil">
                                        <i class="fas fa-chart-line text-xs"></i>
                                    </a>
                                    {{-- Edit --}}
                                    <a href="{{ route('admin.surveys.edit', $survey->id) }}"
                                       class="action-btn inline-flex items-center justify-center w-8 h-8 rounded-xl bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white border border-amber-100 hover:border-amber-500 transition shadow-sm"
                                       title="Edit">
                                        <i class="fas fa-pen text-xs"></i>
                                    </a>
                                    {{-- Delete --}}
                                    <form action="{{ route('admin.surveys.destroy', $survey->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus survei ini? Semua data respon dan jawaban juga akan dihapus permanen.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="action-btn inline-flex items-center justify-center w-8 h-8 rounded-xl bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white border border-rose-100 hover:border-rose-600 transition shadow-sm"
                                                title="Hapus">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-16 text-center">
                                <div class="flex flex-col items-center gap-4 max-w-sm mx-auto">
                                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-indigo-50 to-purple-50 flex items-center justify-center border border-indigo-100 shadow-inner">
                                        <i class="fas fa-poll-h text-3xl text-indigo-300"></i>
                                    </div>
                                    <div class="space-y-1">
                                        <h4 class="font-extrabold text-gray-700 text-base">Belum Ada Survei</h4>
                                        <p class="text-xs text-gray-400 leading-relaxed">Silakan buat survei kepuasan pertama Anda dengan menekan tombol "Buat Survei Baru" di atas.</p>
                                    </div>
                                    <a href="{{ route('admin.surveys.create') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-2.5 rounded-xl font-bold text-sm transition shadow-md hover:shadow-lg hover:-translate-y-0.5">
                                        <i class="fas fa-plus text-xs"></i>
                                        <span>Buat Survei Pertama</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($surveys->hasPages())
            <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/50">
                {{ $surveys->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
