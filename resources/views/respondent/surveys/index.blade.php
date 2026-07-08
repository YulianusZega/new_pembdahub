@extends('layouts.' . $role)

@section('title', 'Survey Kepuasan & Evaluasi')

@section('content')
@php
    $isGuru = $role === 'guru';
    $gradient = $isGuru
        ? 'linear-gradient(135deg, #065f46 0%, #047857 40%, #059669 70%, #10b981 100%)'
        : 'linear-gradient(135deg, #3b0764 0%, #581c87 40%, #6d28d9 70%, #7c3aed 100%)';
    $accentColor = $isGuru ? '#10b981' : '#8b5cf6';
    $accentDark = $isGuru ? '#065f46' : '#4c1d95';
    $btnBg = $isGuru ? 'linear-gradient(135deg, #059669, #047857)' : 'linear-gradient(135deg, #7c3aed, #6d28d9)';
    $cardAccent = $isGuru ? 'linear-gradient(to right, #059669, #10b981)' : 'linear-gradient(to right, #7c3aed, #a78bfa)';
    $statIconBg = $isGuru ? 'bg-emerald-100 text-emerald-700' : 'bg-violet-100 text-violet-700';
    $progressColor = $isGuru ? 'bg-emerald-500' : 'bg-violet-600';
@endphp

<style>
    .survey-hero { background: {{ $gradient }}; }
    .survey-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .survey-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(0,0,0,0.12); }
    .stat-card { transition: all 0.3s ease; }
    .stat-card:hover .stat-blob { transform: scale(2); }
    .stat-blob { transition: transform 0.5s ease; }
    .progress-bar { transition: width 1.2s cubic-bezier(0.4, 0, 0.2, 1); }
    .tab-btn { transition: all 0.2s ease; }
    .tab-indicator { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .empty-icon { animation: float 3s ease-in-out infinite; }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .survey-badge { backdrop-filter: blur(8px); }
    .completed-badge::before { content: ''; position: absolute; inset: 0; background: linear-gradient(135deg, rgba(16,185,129,0.05), rgba(16,185,129,0)); border-radius: inherit; }
</style>

<div class="space-y-6" x-data="{ activeTab: 'available' }">
    {{-- HERO BANNER --}}
    <div class="survey-hero relative overflow-hidden rounded-3xl shadow-2xl text-white">
        {{-- Decorative --}}
        <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.04) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.04) 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="absolute top-0 right-0 w-80 h-80 rounded-full opacity-15" style="background: radial-gradient(circle, white, transparent); transform: translate(25%, -25%);"></div>
        <div class="absolute bottom-0 left-0 w-56 h-56 rounded-full opacity-10" style="background: radial-gradient(circle, {{ $accentColor }}, transparent); transform: translate(-25%, 25%);"></div>

        <div class="relative px-8 py-10 md:px-10 md:py-12 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-4 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="survey-badge inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/15 border border-white/20 rounded-full text-xs font-extrabold uppercase tracking-wider">
                        <i class="fas fa-bullhorn text-[10px]"></i> Suara Sekolah
                    </span>
                    <span class="survey-badge inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/10 border border-white/15 rounded-full text-xs font-extrabold uppercase tracking-wider text-white/90">
                        <i class="fas fa-check-double text-[10px]"></i> Evaluasi Mutu
                    </span>
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight leading-tight">
                    Survey Kepuasan &<br class="hidden sm:block"> Evaluasi PembdaHUB
                </h1>
                <p class="text-white/80 text-sm leading-relaxed font-medium max-w-xl">
                    Partisipasi Anda berkontribusi langsung pada peningkatan kualitas kurikulum, kenyamanan kerja, serta implementasi Teaching Factory dan Budaya Industri di SMKS Swasta Pembda Nias.
                </p>

                {{-- Quick Stats in Hero --}}
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-2xl px-4 py-2">
                        <i class="fas fa-edit text-white/80 text-sm"></i>
                        <div>
                            <p class="text-xl font-extrabold leading-none">{{ $stats['available'] }}</p>
                            <p class="text-[10px] text-white/70 font-semibold">Survei Tersedia</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 border border-white/20 rounded-2xl px-4 py-2">
                        <i class="fas fa-check-circle text-white/80 text-sm"></i>
                        <div>
                            <p class="text-xl font-extrabold leading-none">{{ $stats['completed'] }}</p>
                            <p class="text-[10px] text-white/70 font-semibold">Telah Selesai</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Icon --}}
            <div class="hidden md:flex w-24 h-24 rounded-3xl items-center justify-center border border-white/15 text-white/90 text-5xl shadow-inner backdrop-blur-sm flex-shrink-0" style="background: rgba(255,255,255,0.08);">
                <i class="fas fa-poll-h"></i>
            </div>
        </div>
    </div>

    {{-- PROGRESS CARD --}}
    @php
        $total = $stats['total'];
        $completed = $stats['completed'];
        $rate = $total > 0 ? round(($completed / $total) * 100) : 0;
    @endphp
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-chart-line text-sm" style="color: {{ $accentColor }};"></i>
                <span class="font-extrabold text-gray-700 text-sm">Tingkat Partisipasi Anda</span>
            </div>
            <span class="text-lg font-black" style="color: {{ $accentColor }};">{{ $rate }}%</span>
        </div>
        <div class="w-full bg-gray-100 h-3 rounded-full overflow-hidden border border-gray-100">
            <div class="{{ $progressColor }} h-full rounded-full progress-bar" style="width: {{ $rate }}%;"></div>
        </div>
        <div class="flex items-center justify-between mt-2 text-[11px] text-gray-400 font-semibold">
            <span>{{ $completed }} dari {{ $total }} survei selesai</span>
            @if($rate === 100)
                <span class="text-emerald-600 font-extrabold">✅ Semua Selesai!</span>
            @endif
        </div>
    </div>

    {{-- ALERTS --}}
    @if(session('success'))
        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm">
            <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600 flex-shrink-0">
                <i class="fas fa-exclamation-circle text-sm"></i>
            </div>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    {{-- TABS --}}
    <div class="space-y-5">
        <div class="flex gap-1 p-1 bg-gray-100 rounded-2xl w-fit">
            <button @click="activeTab = 'available'"
                    :class="activeTab === 'available' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl font-extrabold text-sm">
                <i class="fas fa-edit text-xs"></i>
                <span>Survei Aktif</span>
                <span :class="activeTab === 'available' ? 'text-white' : 'text-gray-400 bg-gray-200'"
                      class="px-2 py-0.5 rounded-full text-[10px] font-extrabold" style="{{ "background: " . ($isGuru ? '#059669' : '#7c3aed') . "; color: white;" }}">{{ $stats['available'] }}</span>
            </button>
            <button @click="activeTab = 'completed'"
                    :class="activeTab === 'completed' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="tab-btn flex items-center gap-2 px-5 py-2.5 rounded-xl font-extrabold text-sm">
                <i class="fas fa-history text-xs"></i>
                <span>Riwayat</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-gray-200 text-gray-500">{{ $stats['completed'] }}</span>
            </button>
        </div>

        {{-- TAB 1: Available Surveys --}}
        <div x-show="activeTab === 'available'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
            @if(count($surveys) > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($surveys as $survey)
                        @php
                            $qCount = $survey->questions_count;
                            $estMinutes = max(1, round($qCount * 0.5));
                        @endphp
                        <div class="survey-card bg-white rounded-3xl border border-gray-100 shadow-sm flex flex-col overflow-hidden group hover:border-gray-200">
                            {{-- Accent Bar --}}
                            <div class="h-1.5 w-full flex-shrink-0" style="background: {{ $cardAccent }};"></div>

                            <div class="p-6 flex flex-col flex-1 gap-4">
                                {{-- Tags --}}
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-100 rounded-xl text-[10px] font-extrabold uppercase">
                                        <i class="fas fa-question-circle text-[9px]"></i>{{ $qCount }} Pertanyaan
                                    </span>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-100 rounded-xl text-[10px] font-extrabold uppercase">
                                        <i class="far fa-clock text-[9px]"></i>±{{ $estMinutes }} Menit
                                    </span>
                                    @if(!$survey->school_id)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-50 text-gray-500 border border-gray-200 rounded-xl text-[10px] font-extrabold uppercase">
                                            <i class="fas fa-globe text-[9px]"></i>Global
                                        </span>
                                    @endif
                                </div>

                                {{-- Title & Desc --}}
                                <div class="flex-1 space-y-2">
                                    <h4 class="font-extrabold text-gray-800 text-base leading-snug group-hover:text-indigo-700 transition-colors">
                                        {{ $survey->title }}
                                    </h4>
                                    <p class="text-xs text-gray-500 leading-relaxed line-clamp-3">
                                        {{ $survey->description ?? 'Harap luangkan waktu sejenak untuk mengisi kuesioner evaluasi tingkat kepuasan sekolah ini.' }}
                                    </p>
                                </div>

                                {{-- Footer --}}
                                <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                    <div class="text-[10px] text-gray-400 font-semibold flex items-center gap-1">
                                        <i class="fas fa-calendar-alt text-[9px]"></i>
                                        {{ \Carbon\Carbon::parse($survey->created_at)->translatedFormat('d F Y') }}
                                    </div>
                                    <a href="{{ route($role . '.surveys.take', $survey->id) }}"
                                       class="inline-flex items-center gap-2 px-4 py-2.5 text-white font-extrabold rounded-xl text-xs shadow-md transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 active:translate-y-0"
                                       style="background: {{ $btnBg }};">
                                        <span>Mulai Isi</span>
                                        <i class="fas fa-arrow-right text-[9px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-16 text-center max-w-lg mx-auto">
                    <div class="empty-icon w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-50 to-teal-50 flex items-center justify-center mx-auto mb-5 border border-emerald-100 shadow-inner">
                        <i class="fas fa-check-double text-3xl text-emerald-400"></i>
                    </div>
                    <h3 class="font-extrabold text-gray-800 text-lg mb-2">Semua Survei Selesai!</h3>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-xs mx-auto mb-4">
                        Terima kasih atas kontribusi Anda! Tidak ada survei kepuasan aktif yang perlu Anda isi saat ini. Masukan Anda sangat berharga.
                    </p>
                    <span class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-full text-xs font-extrabold uppercase">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        100% Kontribusi Terpenuhi
                    </span>
                </div>
            @endif
        </div>

        {{-- TAB 2: Completed Surveys History --}}
        <div x-show="activeTab === 'completed'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
            @if(count($completedSurveys) > 0)
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-history text-indigo-500 text-sm"></i>
                            <h3 class="font-extrabold text-gray-800 text-sm">Riwayat Partisipasi Kuesioner</h3>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg text-[10px] font-extrabold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            {{ count($completedSurveys) }} Terkirim
                        </span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left text-sm">
                            <thead>
                                <tr class="border-b border-gray-50" style="background: linear-gradient(to right, #f8fafc, #f1f5f9);">
                                    <th class="p-4 pl-6 text-[10px] font-extrabold text-gray-400 uppercase tracking-widest min-w-[200px]">Judul Kuesioner</th>
                                    <th class="p-4 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-widest min-w-[80px]">Soal</th>
                                    <th class="p-4 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-widest min-w-[160px]">Tanggal Pengisian</th>
                                    <th class="p-4 text-center text-[10px] font-extrabold text-gray-400 uppercase tracking-widest min-w-[100px]">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($completedSurveys as $cs)
                                    <tr class="hover:bg-gray-50/50 transition">
                                        <td class="p-4 pl-6">
                                            <div class="flex items-start gap-3">
                                                <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 flex-shrink-0 mt-0.5 border border-emerald-100">
                                                    <i class="fas fa-check text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="font-bold text-gray-800 text-sm">{{ $cs->title }}</p>
                                                    <p class="text-[10px] text-gray-400 font-medium mt-0.5">{{ $cs->questions_count }} pertanyaan</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-center font-extrabold text-gray-600">{{ $cs->questions_count }}</td>
                                        <td class="p-4 text-center text-gray-500 font-medium text-xs">
                                            @php
                                                $respDate = $cs->responses->first() ? $cs->responses->first()->created_at : null;
                                            @endphp
                                            {{ $respDate ? \Carbon\Carbon::parse($respDate)->translatedFormat('d F Y, H:i') : '-' }}
                                        </td>
                                        <td class="p-4 text-center">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-xl text-[10px] font-extrabold shadow-sm">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                                Terkirim
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-16 text-center max-w-lg mx-auto">
                    <div class="w-20 h-20 rounded-3xl bg-gray-50 flex items-center justify-center mx-auto mb-5 border border-gray-100 shadow-inner">
                        <i class="fas fa-folder-open text-3xl text-gray-300"></i>
                    </div>
                    <h3 class="font-extrabold text-gray-800 text-base mb-2">Belum Ada Riwayat</h3>
                    <p class="text-xs text-gray-500 leading-relaxed max-w-xs mx-auto">
                        Anda belum pernah mengisi survei kepuasan aktif. Survei yang Anda selesaikan akan tercatat di sini.
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
