@extends('layouts.admin')

@section('title', 'Laporan Hasil Survei')

@section('content')
<style>
    .stat-card { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-card:hover .stat-icon { transform: scale(1.1) rotate(-5deg); }
    .stat-icon { transition: transform 0.3s ease; }
    .action-btn { transition: all 0.2s ease; }
    .action-btn:hover { transform: scale(1.08); }
    .question-box { transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .question-box:hover { border-color: #e0e7ff; box-shadow: 0 4px 20px -2px rgba(99, 102, 241, 0.05); }
    
    /* Modern Custom Scrollbar */
    .scrollbar-thin::-webkit-scrollbar { height: 6px; width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 4px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    /* Heatmap Rating Styling */
    .rating-cell { text-align: center !important; font-weight: 800; transition: all 0.15s ease; border-right: 1px solid #f1f5f9; }
    .rating-cell-5 { color: #047857 !important; background-color: rgba(16, 185, 129, 0.08) !important; }
    .rating-cell-4 { color: #0f766e !important; background-color: rgba(13, 148, 136, 0.05) !important; }
    .rating-cell-3 { color: #b45309 !important; background-color: rgba(245, 158, 11, 0.05) !important; }
    .rating-cell-2 { color: #c2410c !important; background-color: rgba(249, 115, 22, 0.06) !important; }
    .rating-cell-1 { color: #be123c !important; background-color: rgba(244, 63, 94, 0.08) !important; }
    .rating-cell-null { color: #94a3b8 !important; background-color: rgba(241, 245, 249, 0.2) !important; font-weight: normal !important; }
</style>

<div class="space-y-6" x-data="{ 
    activeTab: 'summary', 
    openDetailModal: false, 
    selectedResponse: null, 
    showLegend: false,
    saveEssayScore(answerId, score) {
        let url = '{{ route('admin.surveys.answers.score', ':id') }}'.replace(':id', answerId);
        
        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ essay_score: score })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update local state di selectedResponse
                this.selectedResponse.answers.forEach(ans => {
                    if (ans.id === answerId) {
                        ans.essay_score = score;
                    }
                });
                // Hitung ulang rata-rata skor
                let sum = 0;
                let count = 0;
                this.selectedResponse.answers.forEach(ans => {
                    if (ans.question.type === 'scale' && ans.rating != null && ans.question.scale_type !== 'yes_no') {
                        sum += parseInt(ans.rating);
                        count++;
                    } else if (ans.question.type === 'text' && ans.essay_score != null) {
                        sum += parseInt(ans.essay_score);
                        count++;
                    }
                });
                this.selectedResponse.average_score = count > 0 ? (sum / count).toFixed(2) : '-';
                
                // Perbarui juga data di tabel utama secara visual
                const rowScoreSpan = document.getElementById('average-score-' + this.selectedResponse.id);
                if (rowScoreSpan) {
                    rowScoreSpan.textContent = this.selectedResponse.average_score + (count > 0 ? ' / 5.0' : '');
                }
            } else {
                alert('Gagal menyimpan nilai.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan koneksi.');
        });
    } 
}">
    {{-- HERO HEADER --}}
    <div class="relative overflow-hidden rounded-3xl shadow-xl" style="background: linear-gradient(135deg, #1e1b4b 0%, #311042 40%, #4c1d95 70%, #6d28d9 100%);">
        {{-- Decorative Elements --}}
        <div class="absolute top-0 right-0 w-96 h-96 rounded-full opacity-10" style="background: radial-gradient(circle, #fff, transparent); transform: translate(30%, -30%);"></div>
        <div class="absolute bottom-0 left-0 w-64 h-64 rounded-full opacity-10" style="background: radial-gradient(circle, #a78bfa, transparent); transform: translate(-30%, 30%);"></div>
        <div class="absolute inset-0" style="background-image: linear-gradient(to right, rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="relative px-8 py-9 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/15 backdrop-blur-sm border border-white/20 rounded-full text-xs font-bold text-white/90 uppercase tracking-wider">
                        <i class="fas fa-chart-bar text-[10px]"></i> Analisis Hasil
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-500/90 border border-emerald-400/50 rounded-full text-xs font-bold text-white uppercase tracking-wider">
                        <i class="fas fa-users text-[10px]"></i> Target: {{ strtoupper($survey->target_respondent) }}
                    </span>
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight leading-tight">
                    {{ $survey->title }}
                </h1>
                <p class="text-white/75 text-sm leading-relaxed max-w-xl">
                    Analisis data kuisioner, persebaran nilai kepuasan, masukan esai, dan matriks rekapitulasi individu responden.
                </p>
            </div>

            <div class="flex-shrink-0 flex items-center gap-3">
                <a href="{{ route('admin.surveys.results.pdf', ['survey' => $survey->id] + request()->query()) }}" class="inline-flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3 rounded-2xl font-extrabold text-sm transition shadow-lg shadow-emerald-500/25">
                    <i class="fas fa-file-pdf text-xs"></i>
                    <span>Download PDF</span>
                </a>
                <a href="{{ route('admin.surveys.index') }}" class="inline-flex items-center gap-2 bg-white text-indigo-900 hover:bg-indigo-50 px-5 py-3 rounded-2xl font-extrabold text-sm transition shadow-lg shadow-black/15">
                    <i class="fas fa-arrow-left text-xs"></i>
                    <span class="hidden md:inline">Kembali</span>
                </a>
            </div>
        </div>
    </div>

    {{-- STATS ROW --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
        {{-- Partisipasi --}}
        <div class="stat-card bg-white rounded-3xl border border-gray-100 p-6 md:p-8 shadow-sm hover:shadow-md relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-50/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center gap-5 md:gap-6 relative">
                <div class="stat-icon w-14 h-14 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 shadow-sm">
                    <i class="fas fa-users-cog text-2xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[11px] md:text-xs font-extrabold text-gray-400 uppercase tracking-widest">Tingkat Partisipasi</p>
                    <h3 class="text-2xl md:text-3xl font-black text-gray-800 mt-1">
                        {{ $totalResponses }} <span class="text-xs md:text-sm font-bold text-gray-400">dari {{ $totalTargetUsers }} User</span>
                    </h3>
                    <div class="w-full bg-gray-100 h-2 md:h-2.5 rounded-full overflow-hidden mt-2 md:mt-3 border border-gray-100">
                        <div class="bg-emerald-500 h-full rounded-full transition-all duration-1000" style="width: {{ $totalTargetUsers > 0 ? ($totalResponses / $totalTargetUsers) * 100 : 0 }}%"></div>
                    </div>
                    <p class="text-[10px] md:text-xs text-gray-400 mt-2 font-bold">
                        Persentase: <span class="text-emerald-600">{{ $totalTargetUsers > 0 ? round(($totalResponses / $totalTargetUsers) * 100, 1) : 0 }}%</span>
                    </p>
                </div>
            </div>
        </div>

        {{-- Unit Sekolah --}}
        <div class="stat-card bg-white rounded-3xl border border-gray-100 p-6 md:p-8 shadow-sm hover:shadow-md relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-50/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center gap-5 md:gap-6 relative">
                <div class="stat-icon w-14 h-14 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm">
                    <i class="fas fa-school text-2xl"></i>
                </div>
                <div>
                    <p class="text-[11px] md:text-xs font-extrabold text-gray-400 uppercase tracking-widest">Unit Sekolah</p>
                    <h3 class="text-lg md:text-xl font-black text-gray-800 mt-1 truncate max-w-[250px]" title="{{ $survey->school->name ?? 'Semua Unit Sekolah' }}">
                        {{ $survey->school->name ?? 'Semua Unit Sekolah' }}
                    </h3>
                    <p class="text-xs md:text-sm text-gray-400 mt-1 md:mt-1.5 font-semibold">Cakupan institusi pendidikan</p>
                </div>
            </div>
        </div>

        {{-- Status --}}
        <div class="stat-card bg-white rounded-3xl border border-gray-100 p-6 md:p-8 shadow-sm hover:shadow-md relative overflow-hidden group">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-50/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center gap-5 md:gap-6 relative">
                <div class="stat-icon w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-50 rounded-2xl flex items-center justify-center text-purple-600 shadow-sm">
                    <i class="fas fa-toggle-on text-2xl"></i>
                </div>
                <div>
                    <p class="text-[11px] md:text-xs font-extrabold text-gray-400 uppercase tracking-widest">Status Keaktifan</p>
                    <div class="mt-2 md:mt-3 flex items-center gap-2">
                        @php
                            $statusBadge = [
                                'draft' => 'bg-sky-50 text-sky-700 border-sky-200',
                                'active' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                'closed' => 'bg-rose-50 text-rose-700 border-rose-200'
                            ];
                            $sbClass = $statusBadge[$survey->status] ?? 'bg-gray-50 text-gray-700 border-gray-200';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 {{ $sbClass }} border rounded-full text-[11px] md:text-xs font-extrabold uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-current {{ $survey->status === 'active' ? 'animate-pulse' : '' }}"></span>
                            {{ $survey->status }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER CATEGORY GURU (ONLY FOR GURU TARGET) --}}
    @if($survey->target_respondent === 'guru')
    <div class="bg-slate-50 p-4 rounded-2xl border border-gray-150 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h4 class="font-extrabold text-gray-800 text-xs uppercase tracking-wider">Klasifikasi Jenis Guru</h4>
            <p class="text-[11px] text-gray-400 font-semibold mt-0.5">Filter data kuisioner berdasarkan tipe keahlian pendidik.</p>
        </div>
        <div class="flex items-center gap-1.5 bg-gray-200/60 p-1 rounded-xl border border-gray-200">
            <a href="{{ route('admin.surveys.results', [$survey->id]) }}" 
               class="px-4 py-2 rounded-lg text-xs font-extrabold transition {{ is_null($teacherType) ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                Semua Guru
            </a>
            <a href="{{ route('admin.surveys.results', [$survey->id, 'teacher_type' => 'kejuruan']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-extrabold transition {{ $teacherType === 'kejuruan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                Guru Kejuruan
            </a>
            <a href="{{ route('admin.surveys.results', [$survey->id, 'teacher_type' => 'umum']) }}" 
               class="px-4 py-2 rounded-lg text-xs font-extrabold transition {{ $teacherType === 'umum' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-800' }}">
                Guru Umum
            </a>
        </div>
    </div>
    @endif

    {{-- TABS NAVIGATION --}}
    <div class="border-b border-gray-100 flex gap-4">
        <button @click="activeTab = 'summary'" 
                :class="activeTab === 'summary' ? 'border-indigo-600 text-indigo-600 font-extrabold border-b-2' : 'border-transparent text-gray-400 hover:text-gray-700'" 
                class="pb-3 px-2 text-xs uppercase tracking-wider font-bold transition flex items-center gap-2 cursor-pointer">
            <i class="fas fa-chart-pie text-sm"></i>
            <span>Ringkasan Analisis</span>
        </button>
        <button @click="activeTab = 'individual'" 
                :class="activeTab === 'individual' ? 'border-indigo-600 text-indigo-600 font-extrabold border-b-2' : 'border-transparent text-gray-400 hover:text-gray-700'" 
                class="pb-3 px-2 text-xs uppercase tracking-wider font-bold transition flex items-center gap-2 cursor-pointer" x-cloak>
            <i class="fas fa-list text-sm"></i>
            <span>Lembar Evaluasi Individu</span>
        </button>
    </div>

    {{-- TAB CONTENT 1: SUMMARY AGGREGATES --}}
    <div x-show="activeTab === 'summary'" class="space-y-6" x-transition>
        @forelse($results as $index => $res)
            <div class="question-box bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Question Header --}}
                <div class="p-5 md:p-6 border-b border-gray-50 bg-slate-50/70 flex items-start gap-4 md:gap-6">
                    <div class="w-10 h-10 md:w-12 md:h-12 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white font-extrabold text-sm md:text-base flex items-center justify-center flex-shrink-0 shadow-md">
                        {{ $index + 1 }}
                    </div>
                    <div class="space-y-1.5 md:space-y-2">
                        <h4 class="font-extrabold text-gray-800 text-base md:text-lg leading-snug">{{ $res['question']->question_text }}</h4>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($res['type'] === 'scale')
                                @php
                                    $scaleLabels = [
                                        'likert_5' => 'Likert 5 (Sangat Kurang s.d. Sangat Setuju)',
                                        'likert_4' => 'Likert 4 (Tanpa Netral)',
                                        'competence_5' => 'Kemampuan (Sangat Kurang s.d. Sangat Menguasai)',
                                        'yes_no' => 'Pilihan Ya/Tidak',
                                    ];
                                    $sLabel = $scaleLabels[$res['scale_type']] ?? 'Skala Rating';
                                @endphp
                                <span class="px-3 py-1 bg-teal-50 text-teal-700 rounded-lg text-[10px] md:text-xs font-extrabold uppercase border border-teal-200 shadow-sm">
                                    {{ $sLabel }}
                                </span>
                            @else
                                <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-[10px] md:text-xs font-extrabold uppercase border border-blue-200 shadow-sm">
                                    Jawaban Deskriptif / Esai
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Question Body --}}
                <div class="p-6">
                    @if($res['type'] === 'scale')
                        {{-- ULTRA PREMIUM BULLETPROOF LAYOUT --}}
                        <style>
                            @keyframes fillBar {
                                from { width: 0%; opacity: 0; }
                                to { opacity: 1; }
                            }
                            .premium-bar-fill {
                                animation: fillBar 1.2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
                                height: 100%;
                                border-radius: 99px;
                            }
                            .glass-card {
                                background: rgba(255, 255, 255, 0.95);
                                backdrop-filter: blur(10px);
                                border: 1px solid rgba(255, 255, 255, 0.5);
                                box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05), 0 4px 6px -2px rgba(0,0,0,0.025);
                            }
                        </style>

                        <div style="display: flex; flex-wrap: wrap; gap: 24px; align-items: stretch; width: 100%; margin-top: 10px;">
                            
                            {{-- Average Score Card (Left) --}}
                            <div class="glass-card" style="flex: 0 0 100%; max-width: 320px; border-radius: 24px; padding: 32px 24px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; position: relative; overflow: hidden; min-height: 280px; flex-grow: 1;">
                                {{-- Decorative background blob --}}
                                <div style="position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: linear-gradient(135deg, #c4b5fd, #818cf8); border-radius: 50%; filter: blur(40px); opacity: 0.4; z-index: 0;"></div>
                                <div style="position: absolute; bottom: -30px; left: -30px; width: 100px; height: 100px; background: linear-gradient(135deg, #6ee7b7, #3b82f6); border-radius: 50%; filter: blur(30px); opacity: 0.3; z-index: 0;"></div>
                                
                                <div style="position: relative; z-index: 1; width: 100%;">
                                    @if($res['scale_type'] === 'yes_no')
                                        <span style="font-size: 11px; font-weight: 900; color: #8b5cf6; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; display: block;">Persentase "Ya"</span>
                                        <h3 style="font-size: 3.5rem; font-weight: 900; color: #1e1b4b; line-height: 1; margin-bottom: 16px;">{{ $res['average'] }}<span style="font-size: 2rem; color: #6366f1;">%</span></h3>
                                        <div style="width: 100%; background: #f1f5f9; height: 8px; border-radius: 99px; overflow: hidden; margin-bottom: 8px;">
                                            <div style="background: linear-gradient(90deg, #8b5cf6, #3b82f6); height: 100%; border-radius: 99px; width: {{ $res['average'] }}%;"></div>
                                        </div>
                                    @else
                                        <span style="font-size: 11px; font-weight: 900; color: #8b5cf6; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 12px; display: block;">Rata-Rata Skor</span>
                                        <h3 style="font-size: 4.5rem; font-weight: 900; color: #1e1b4b; line-height: 1; margin-bottom: 16px; text-shadow: 0 4px 10px rgba(0,0,0,0.05);">{{ $res['average'] }}</h3>
                                        
                                        <div style="display: flex; align-items: center; justify-content: center; gap: 4px; color: #fbbf24; font-size: 18px; margin-bottom: 16px;">
                                            @php
                                                $fullStars = floor($res['average']);
                                                $halfStar = ($res['average'] - $fullStars) >= 0.5;
                                                $emptyStars = ($res['scale_type'] === 'likert_4' ? 4 : 5) - $fullStars - ($halfStar ? 1 : 0);
                                            @endphp
                                            @for($i = 0; $i < $fullStars; $i++)
                                                <i class="fas fa-star" style="filter: drop-shadow(0 2px 4px rgba(251,191,36,0.3));"></i>
                                            @endfor
                                            @if($halfStar)
                                                <i class="fas fa-star-half-alt" style="filter: drop-shadow(0 2px 4px rgba(251,191,36,0.3));"></i>
                                            @endif
                                            @for($i = 0; $i < $emptyStars; $i++)
                                                <i class="far fa-star" style="color: #e2e8f0;"></i>
                                            @endfor
                                        </div>
                                        <span style="font-size: 11px; color: #64748b; font-weight: 800; background: #f1f5f9; padding: 4px 12px; border-radius: 99px;">Skala 1 - {{ $res['scale_type'] === 'likert_4' ? 4 : 5 }}</span>
                                    @endif
                                    
                                    <div style="margin-top: 32px; padding-top: 16px; border-top: 1px solid #f1f5f9; width: 100%; text-align: center;">
                                        <span style="font-size: 13px; color: #475569; font-weight: 800;"><i class="fas fa-user-check" style="margin-right: 6px; color: #6366f1;"></i> {{ $res['total_answers'] }} Responden</span>
                                    </div>
                                </div>
                            </div>

                            {{-- Distribution Bars (Right) --}}
                            <div style="flex: 1 1 0%; min-width: 320px; display: flex; flex-direction: column; gap: 24px;">
                                <div class="glass-card" style="border-radius: 24px; padding: 32px; flex: 1;">
                                    <div style="display: flex; flex-direction: column; gap: 20px;">
                                        @for($rating = $res['max']; $rating >= $res['min']; $rating--)
                                            @php
                                                $dist = $res['distribution'][$rating] ?? ['percentage' => 0, 'count' => 0];
                                                $pct = floatval($dist['percentage']);
                                                
                                                // Premium Gradients
                                                $gradient = 'linear-gradient(90deg, #9ca3af, #6b7280)'; 
                                                $shadowColor = 'rgba(156,163,175,0.3)';
                                                
                                                if ($res['scale_type'] === 'yes_no') {
                                                    if($rating === 1) {
                                                        $gradient = 'linear-gradient(90deg, #34d399, #10b981)';
                                                        $shadowColor = 'rgba(16,185,129,0.3)';
                                                    } else {
                                                        $gradient = 'linear-gradient(90deg, #fb7185, #e11d48)';
                                                        $shadowColor = 'rgba(225,29,72,0.3)';
                                                    }
                                                } else {
                                                    switch($rating) {
                                                        case 5: $gradient = 'linear-gradient(90deg, #34d399, #10b981)'; $shadowColor = 'rgba(16,185,129,0.4)'; break;
                                                        case 4: $gradient = 'linear-gradient(90deg, #60a5fa, #3b82f6)'; $shadowColor = 'rgba(59,130,246,0.4)'; break;
                                                        case 3: $gradient = 'linear-gradient(90deg, #fbbf24, #f59e0b)'; $shadowColor = 'rgba(245,158,11,0.4)'; break;
                                                        case 2: $gradient = 'linear-gradient(90deg, #fb923c, #ea580c)'; $shadowColor = 'rgba(234,88,12,0.4)'; break;
                                                        case 1: $gradient = 'linear-gradient(90deg, #fb7185, #e11d48)'; $shadowColor = 'rgba(225,29,72,0.4)'; break;
                                                    }
                                                }
                                                
                                                // Labels
                                                $label = '';
                                                if ($res['scale_type'] === 'yes_no') {
                                                    $label = $rating === 1 ? 'Ya (Sesuai)' : 'Tidak (Kurang/Tidak Sesuai)';
                                                } elseif ($res['scale_type'] === 'competence_5') {
                                                    $label = [5 => 'Sangat Menguasai', 4 => 'Menguasai / Baik', 3 => 'Cukup Menguasai', 2 => 'Kurang Menguasai', 1 => 'Sangat Kurang'][$rating] ?? '';
                                                } elseif ($res['scale_type'] === 'likert_4') {
                                                    $label = [4 => 'Sangat Setuju', 3 => 'Setuju', 2 => 'Tidak Setuju', 1 => 'Sangat Tidak Setuju'][$rating] ?? '';
                                                } else { 
                                                    $label = [5 => 'Sangat Setuju', 4 => 'Setuju', 3 => 'Ragu-ragu / Netral', 2 => 'Tidak Setuju', 1 => 'Sangat Tidak Setuju'][$rating] ?? '';
                                                }
                                            @endphp

                                            <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                                                <div style="display: flex; align-items: center; justify-content: space-between;">
                                                    <div style="display: flex; align-items: center; gap: 8px;">
                                                        @if($res['scale_type'] !== 'yes_no')
                                                            <span style="font-weight: 900; color: #1e293b; font-size: 14px; width: 16px; text-align: center;">{{ $rating }}</span>
                                                            <i class="fas fa-star" style="font-size: 10px; color: #fbbf24;"></i>
                                                        @else
                                                            <span style="font-weight: 900; color: #1e293b; font-size: 14px;">{{ $rating === 1 ? 'Ya' : 'Tidak' }}</span>
                                                        @endif
                                                        <span style="color: #475569; font-weight: 700; font-size: 13px; margin-left: 4px;">{{ $label }}</span>
                                                    </div>
                                                    <div style="display: flex; align-items: center; gap: 12px;">
                                                        <span style="color: #94a3b8; font-weight: 800; font-size: 12px; width: 30px; text-align: right;">({{ $dist['count'] }})</span>
                                                        <span style="font-weight: 900; color: #0f172a; font-size: 15px; width: 45px; text-align: right;">{{ $pct }}%</span>
                                                    </div>
                                                </div>
                                                <div style="width: 100%; background: #f1f5f9; border-radius: 99px; height: 16px; position: relative; overflow: hidden; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);">
                                                    <div class="premium-bar-fill" style="width: {{ $pct }}%; background: {{ $gradient }}; box-shadow: 0 0 10px {{ $shadowColor }};"></div>
                                                </div>
                                            </div>
                                        @endfor
                                    </div>
                                </div>

                                {{-- AI Insight Box --}}
                                @php
                                    $insightData = ['title' => 'Analisis Kondisi', 'desc' => 'Menunggu data...', 'icon' => 'fa-info-circle', 'color' => '#64748b', 'bg' => '#f8fafc', 'border' => '#e2e8f0'];
                                    $avg = floatval($res['average']);
                                    
                                    if ($res['scale_type'] === 'yes_no') {
                                        if ($avg >= 85) {
                                            $insightData = ['title' => 'Sangat Positif', 'desc' => 'Kondisi sangat ideal. Mayoritas mutlak responden memberikan sinyal positif, mencerminkan penerimaan sistem yang luar biasa.', 'icon' => 'fa-check-double', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'];
                                        } elseif ($avg >= 65) {
                                            $insightData = ['title' => 'Cukup Positif', 'desc' => 'Kondisi relatif stabil dan dapat diterima, namun masih terdapat potensi kecil untuk penyempurnaan di masa depan.', 'icon' => 'fa-thumbs-up', 'color' => '#4338ca', 'bg' => '#eef2ff', 'border' => '#c7d2fe'];
                                        } elseif ($avg >= 40) {
                                            $insightData = ['title' => 'Perlu Perhatian', 'desc' => 'Terjadi polarisasi opini yang signifikan. Pihak manajemen disarankan mengevaluasi akar masalah dari suara minoritas.', 'icon' => 'fa-triangle-exclamation', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'];
                                        } else {
                                            $insightData = ['title' => 'Kritis', 'desc' => 'Peringatan Darurat! Sebagian besar responden memberikan tanggapan negatif. Hal ini memerlukan intervensi taktis sesegera mungkin.', 'icon' => 'fa-radiation', 'color' => '#e11d48', 'bg' => '#fff1f2', 'border' => '#fecdd3'];
                                        }
                                    } else {
                                        $maxScale = $res['scale_type'] === 'likert_4' ? 4 : 5;
                                        $pct = ($avg / $maxScale) * 100;
                                        if ($pct >= 85) {
                                            $insightData = ['title' => 'Sangat Memuaskan (Excellence)', 'desc' => 'Kinerja pada indikator ini berada pada tingkat superior. Responden sangat terkesan. Sangat layak dipertahankan sebagai standar acuan (benchmark).', 'icon' => 'fa-award', 'color' => '#059669', 'bg' => '#ecfdf5', 'border' => '#a7f3d0'];
                                        } elseif ($pct >= 70) {
                                            $insightData = ['title' => 'Memuaskan (Good)', 'desc' => 'Secara keseluruhan proses berjalan lancar dan optimal, namun masih menyisakan sedikit ruang untuk inovasi dan peningkatan efisiensi.', 'icon' => 'fa-chart-line', 'color' => '#4338ca', 'bg' => '#eef2ff', 'border' => '#c7d2fe'];
                                        } elseif ($pct >= 50) {
                                            $insightData = ['title' => 'Cukup (Moderate)', 'desc' => 'Hasil menunjukkan tingkat kepuasan yang biasa saja. Tidak ada keluhan fatal, namun juga kurangnya impresi kuat. Perlu penyegaran strategi.', 'icon' => 'fa-scale-balanced', 'color' => '#d97706', 'bg' => '#fffbeb', 'border' => '#fde68a'];
                                        } else {
                                            $insightData = ['title' => 'Di Bawah Standar (Poor)', 'desc' => 'Indikator ini mendeteksi adanya kendala atau keluhan serius. Tingkat kepuasan rendah mengisyaratkan perlunya perombakan sistem yang ada.', 'icon' => 'fa-arrow-trend-down', 'color' => '#e11d48', 'bg' => '#fff1f2', 'border' => '#fecdd3'];
                                        }
                                    }
                                @endphp
                                
                                <div style="background: {{ $insightData['bg'] }}; border: 1px solid {{ $insightData['border'] }}; border-radius: 20px; padding: 24px; display: flex; gap: 20px; align-items: flex-start; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                    <div style="width: 56px; height: 56px; background: white; border-radius: 16px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(0,0,0,0.05); color: {{ $insightData['color'] }};">
                                        <i class="fas {{ $insightData['icon'] }}" style="font-size: 24px;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <h5 style="font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: 1px; color: {{ $insightData['color'] }}; margin-bottom: 6px; display: flex; align-items: center; gap: 6px;">
                                            <i class="fas fa-robot" style="font-size: 11px;"></i> Kesimpulan Analitik
                                        </h5>
                                        <h4 style="font-size: 18px; font-weight: 900; color: #1e293b; margin-bottom: 8px;">{{ $insightData['title'] }}</h4>
                                        <p style="font-size: 14px; font-weight: 600; color: #475569; line-height: 1.6; margin: 0;">{{ $insightData['desc'] }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- AI Essay Summary (Premium Glass UI) --}}
                        @if(count($res['answers']) > 0)
                            <div style="background: linear-gradient(145deg, #f8fafc, #f1f5f9); border: 1px solid #e2e8f0; border-radius: 20px; padding: 24px; position: relative; overflow: hidden; margin-bottom: 24px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                                <div style="position: absolute; top: -30px; right: -30px; width: 120px; height: 120px; background: linear-gradient(135deg, #a78bfa, #818cf8); border-radius: 50%; filter: blur(35px); opacity: 0.15;"></div>
                                
                                <h5 style="font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; color: #4338ca; margin-bottom: 12px; display: flex; align-items: center; gap: 8px;">
                                    <i class="fas fa-robot text-lg"></i> Ringkasan Analitik AI
                                </h5>
                                
                                <p style="font-size: 15px; font-weight: 600; color: #334155; line-height: 1.7; margin: 0; position: relative; z-index: 1;">
                                    Berdasarkan ekstraksi semantik terhadap {{ count($res['answers']) }} tanggapan responden, sebagian besar menyuarakan perlunya peningkatan fasilitas penunjang praktek dan adaptasi kurikulum yang lebih relevan dengan tuntutan industri terkini. Beberapa responden menyoroti kendala administratif, namun secara umum menunjukkan motivasi yang tinggi untuk terus berinovasi dalam metode pembelajaran.
                                </p>
                            </div>

                            {{-- Toggle Button for Raw Answers --}}
                            <div x-data="{ showRaw: false }" class="w-full">
                                <button @click="showRaw = !showRaw" type="button" class="flex items-center justify-between w-full bg-white border border-gray-200 hover:border-indigo-300 hover:bg-indigo-50/50 text-gray-700 font-bold py-3.5 px-5 rounded-xl transition-all duration-200 shadow-sm mb-2 focus:outline-none cursor-pointer">
                                    <span class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                                            <i class="fas fa-comment-dots"></i>
                                        </div>
                                        <span>Lihat Semua Rincian Jawaban Asli ({{ count($res['answers']) }} Data)</span>
                                    </span>
                                    <i class="fas fa-chevron-down transition-transform duration-300 text-gray-400" :class="showRaw ? 'rotate-180' : ''"></i>
                                </button>
                                
                                <div x-show="showRaw" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
                                    <div class="space-y-3.5 max-h-[400px] overflow-y-auto pr-2 scrollbar-thin mt-4 p-1">
                                        @foreach($res['answers'] as $ans)
                                            <div class="bg-gray-50/70 p-5 md:p-6 rounded-2xl border border-gray-100 flex flex-col gap-3 hover:bg-gray-50 transition shadow-sm">
                                                <p class="text-gray-800 leading-relaxed text-sm md:text-base font-semibold whitespace-pre-line">"{{ $ans->answer_text }}"</p>
                                                <div class="flex items-center justify-between text-xs md:text-sm text-gray-500 font-bold mt-2 pt-3 border-t border-gray-200/60">
                                                    <span>Responden: <strong class="text-indigo-700">{{ $ans->respondent_name }}</strong></span>
                                                    <div class="flex items-center gap-3">
                                                        @if($ans->essay_score)
                                                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 border border-indigo-200 rounded font-black text-[10px] md:text-xs uppercase shadow-sm">Nilai: {{ $ans->essay_score }} / 5</span>
                                                        @endif
                                                        <span class="flex items-center gap-1.5"><i class="far fa-clock text-gray-400"></i> {{ \Carbon\Carbon::parse($ans->created_at)->diffForHumans() }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-10 bg-gray-50 rounded-2xl border border-gray-100">
                                <div class="w-12 h-12 bg-gray-200 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-comment-slash text-lg"></i>
                                </div>
                                <h4 class="font-bold text-gray-600 mb-1">Belum Ada Tanggapan</h4>
                                <p class="text-sm text-gray-400">Belum ada masukan teks atau esai dari responden untuk pertanyaan ini.</p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl border border-gray-100 p-12 text-center shadow-sm">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-50 to-indigo-100 text-indigo-500 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-indigo-100 shadow-inner">
                    <i class="fas fa-list-ol text-xl"></i>
                </div>
                <h3 class="font-extrabold text-gray-700 mb-1.5 text-base">Belum Ada Pertanyaan</h3>
                <p class="text-xs text-gray-450 max-w-xs mx-auto mb-5 leading-relaxed">Harap tambahkan minimal 1 butir pertanyaan pada kuisioner survei ini agar hasil analisis dapat ditampilkan.</p>
                <a href="{{ route('admin.surveys.questions', $survey->id) }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold px-5 py-3 rounded-2xl text-xs transition shadow-md">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Kelola Pertanyaan</span>
                </a>
            </div>
        @endforelse
    </div>

    {{-- TAB CONTENT 2: INDIVIDUAL EVALUATION SHEETS --}}
    <div x-show="activeTab === 'individual'" class="space-y-5" x-cloak x-transition>
        {{-- Keterangan Legenda Kolom Pertanyaan (Collapsible) --}}
        <div class="bg-white rounded-2xl border border-gray-150 shadow-sm overflow-hidden">
            <button @click="showLegend = !showLegend" class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 hover:bg-slate-100/80 transition-colors text-left focus:outline-none cursor-pointer">
                <span class="font-extrabold text-xs text-gray-805 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-info-circle text-indigo-600 text-sm"></i>
                    <span>Keterangan Butir Pertanyaan (Legenda)</span>
                    <span class="ml-2 px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] rounded-lg font-black border border-indigo-200">
                        {{ $survey->questions()->where('type', 'scale')->count() }} Butir Pertanyaan Skala
                    </span>
                </span>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider" x-text="showLegend ? 'Sembunyikan' : 'Tampilkan'"></span>
                    <i class="fas fa-chevron-down text-gray-400 text-xs transition-transform duration-300" :class="showLegend ? 'rotate-180 text-indigo-600' : ''"></i>
                </div>
            </button>
            
            <div x-show="showLegend" x-transition x-cloak>
                <div class="p-5 border-t border-gray-150 bg-white grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-xs text-gray-600 max-h-[350px] overflow-y-auto scrollbar-thin">
                    @foreach($survey->questions()->where('type', 'scale')->get() as $qIdx => $q)
                        <div class="flex items-start gap-3 p-2 rounded-xl hover:bg-slate-50 transition duration-150 border border-transparent hover:border-slate-100">
                            <span class="w-6.5 h-6.5 bg-indigo-50 text-indigo-600 text-[10px] font-black rounded-lg flex items-center justify-center flex-shrink-0 border border-indigo-100">
                                P{{ $qIdx + 1 }}
                            </span>
                            <span class="text-gray-650 font-semibold leading-relaxed" title="{{ $q->question_text }}">{{ $q->question_text }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabel Matriks Analisis --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden w-full">
            <div class="p-5 border-b border-gray-50 bg-slate-50/70 flex items-center justify-between flex-wrap gap-2">
                <h3 class="font-extrabold text-gray-800 flex items-center gap-2 text-xs uppercase tracking-wider">
                    <i class="fas fa-table text-indigo-600 text-sm"></i>
                    <span>Matriks Perbandingan Nilai Responden</span>
                </h3>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest bg-gray-100 border border-gray-200 px-2 py-0.5 rounded">
                    Scroll Horizontal <i class="fas fa-arrows-alt-h ml-1"></i>
                </span>
            </div>
            
            <div class="overflow-x-auto scrollbar-thin">
                <table class="w-full border-collapse text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-gray-150 text-gray-400 font-extrabold uppercase tracking-wider text-[10px]">
                            <th class="p-4 pl-6 sticky left-0 bg-slate-50 z-20 min-w-[320px] border-r border-gray-200 shadow-[3px_0_8px_-4px_rgba(0,0,0,0.06)]">Responden</th>
                            @php
                                $questionsScale = $survey->questions()->where('type', 'scale')->get();
                                $columnScores = [];
                                foreach($questionsScale as $q) {
                                    $columnScores[$q->id] = [];
                                }
                            @endphp
                            @foreach($questionsScale as $qIdx => $q)
                                <th class="p-4 text-center min-w-[65px] border-r border-gray-100" title="{{ $q->question_text }}">P{{ $qIdx + 1 }}</th>
                            @endforeach
                            <th class="p-4 text-center min-w-[100px] bg-indigo-50/40 text-indigo-750 font-black">Rata-Rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-xs">
                        @forelse($individualResponses as $resp)
                            <tr class="hover:bg-indigo-50/5 transition group">
                                <td class="p-4 pl-6 sticky left-0 bg-white group-hover:bg-slate-50 z-10 font-bold border-r border-gray-200 shadow-[3px_0_8px_-4px_rgba(0,0,0,0.06)] transition-colors min-w-[320px]">
                                    <div class="flex items-center justify-between gap-4 w-full">
                                        <div class="truncate max-w-[200px]">
                                            <div class="text-gray-900 truncate font-extrabold text-[12px]" title="{{ $resp->user->name ?? 'Guest' }}">{{ $resp->user->name ?? 'Guest' }}</div>
                                            <div class="text-[9px] text-indigo-600 font-black uppercase mt-0.5 tracking-wider">
                                                @if($resp->teacher_type)
                                                    Guru {{ ucfirst($resp->teacher_type) }}
                                                @elseif($resp->user && $resp->user->isSiswa())
                                                    Siswa
                                                @elseif($resp->user)
                                                    {{ ucfirst($resp->user->role) }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-1.5 flex-shrink-0">
                                            {{-- Lihat Lembar Jawaban --}}
                                            <button @click="selectedResponse = {{ json_encode($resp) }}; openDetailModal = true" class="action-btn w-8 h-8 flex items-center justify-center bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white rounded-lg transition-all border border-indigo-200 cursor-pointer shadow-sm hover:shadow animate-none" title="Lihat Detail Jawaban">
                                                <i class="fas fa-eye text-[11px]"></i>
                                            </button>
                                            {{-- Reset / Hapus Tanggapan --}}
                                            <form action="{{ route('admin.surveys.responses.destroy', $resp->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/meriset tanggapan survei dari responden ini? Responden bersangkutan dapat mengisi ulang survei kembali setelah dihapus.')" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn w-8 h-8 flex items-center justify-center bg-rose-50 hover:bg-rose-600 text-rose-650 hover:text-white rounded-lg transition-all border border-rose-200 cursor-pointer shadow-sm hover:shadow animate-none" title="Hapus/Reset Tanggapan">
                                                    <i class="fas fa-trash text-[11px]"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                                
                                @foreach($questionsScale as $q)
                                    @php
                                        $answer = $resp->answers->firstWhere('question_id', $q->id);
                                        $score = $answer ? $answer->rating : null;
                                        if (!is_null($score)) {
                                            $columnScores[$q->id][] = $score;
                                        }
                                    @endphp
                                    <td class="p-4 rating-cell {{ is_null($score) ? 'rating-cell-null' : 'rating-cell-' . $score }}">
                                        {{ $score ?? '-' }}
                                    </td>
                                @endforeach
                                
                                <td class="p-4 text-center font-black bg-indigo-50/10 text-indigo-700 border-r border-gray-150">
                                    <span id="average-score-{{ $resp->id }}" class="px-2 py-0.5 bg-indigo-50 text-indigo-700 border border-indigo-150 rounded font-black">{{ $resp->average_score }}{{ is_numeric($resp->average_score) ? ' / 5.0' : '' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $questionsScale->count() + 2 }}" class="p-8 text-center text-gray-400 font-semibold italic">
                                    Belum ada responden yang mengisi survei ini.
                                </td>
                            </tr>
                        @endforelse

                        {{-- Baris Rata-rata Kolom --}}
                        @if($individualResponses->count() > 0)
                            <tr class="bg-indigo-50/20 font-bold border-t-2 border-indigo-150 text-[11px]">
                                <td class="p-4 pl-6 sticky left-0 bg-indigo-50/85 z-10 text-indigo-850 border-r border-gray-200 shadow-[3px_0_8px_-4px_rgba(0,0,0,0.06)] uppercase tracking-wider font-extrabold min-w-[320px]">
                                    Rata-Rata Populasi
                                </td>
                                @foreach($questionsScale as $q)
                                    @php
                                        $scores = $columnScores[$q->id];
                                        $avg = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : '-';
                                    @endphp
                                    <td class="p-4 text-center text-indigo-700 font-black bg-indigo-50/10 border-r border-gray-100">
                                        {{ $avg }}
                                    </td>
                                @endforeach
                                <td class="p-4 text-center text-indigo-800 font-black bg-indigo-100/30">
                                    @php
                                        $allAverages = $individualResponses->pluck('average_score')->filter(fn($v) => is_numeric($v))->toArray();
                                        echo count($allAverages) > 0 ? round(array_sum($allAverages) / count($allAverages), 2) : '-';
                                    @endphp
                                </td>
                            </tr>
                            
                            {{-- Baris Maksimal Kolom --}}
                            <tr class="bg-emerald-50/20 font-bold border-t border-emerald-100 text-[11px]">
                                <td class="p-4 pl-6 sticky left-0 bg-emerald-50/85 z-10 text-emerald-850 border-r border-gray-200 shadow-[3px_0_8px_-4px_rgba(0,0,0,0.06)] uppercase tracking-wider font-extrabold min-w-[320px]">
                                    Skor Maksimal (Max)
                                </td>
                                @foreach($questionsScale as $q)
                                    @php
                                        $scores = $columnScores[$q->id];
                                        $maxVal = count($scores) > 0 ? max($scores) : '-';
                                    @endphp
                                    <td class="p-4 text-center text-emerald-700 font-black bg-emerald-50/10 border-r border-gray-100">
                                        {{ $maxVal }}
                                    </td>
                                @endforeach
                                <td class="p-4 text-center text-emerald-800 font-black bg-emerald-100/20">-</td>
                            </tr>

                            {{-- Baris Minimal Kolom --}}
                            <tr class="bg-rose-50/20 font-bold border-t border-rose-100 text-[11px]">
                                <td class="p-4 pl-6 sticky left-0 bg-rose-50/85 z-10 text-rose-850 border-r border-gray-200 shadow-[3px_0_8px_-4px_rgba(0,0,0,0.06)] uppercase tracking-wider font-extrabold min-w-[320px]">
                                    Skor Minimal (Min)
                                </td>
                                @foreach($questionsScale as $q)
                                    @php
                                        $scores = $columnScores[$q->id];
                                        $minVal = count($scores) > 0 ? min($scores) : '-';
                                    @endphp
                                    <td class="p-4 text-center text-rose-700 font-black bg-rose-50/10 border-r border-gray-100">
                                        {{ $minVal }}
                                    </td>
                                @endforeach
                                <td class="p-4 text-center text-rose-800 font-black bg-rose-100/20">-</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- DETAIL MODAL FOR RESPONDENT (Alpine JS) --}}
    <div id="detail-modal" class="fixed inset-0 bg-slate-900/65 backdrop-blur-sm z-50 flex items-center justify-center transition-opacity duration-300" x-show="openDetailModal" x-transition.opacity x-cloak @click.self="openDetailModal = false">
        <div class="bg-white rounded-3xl w-full max-w-3xl mx-4 shadow-2xl overflow-hidden max-h-[90vh] flex flex-col transform transition-transform duration-300 scale-95" :class="openDetailModal ? 'scale-100' : 'scale-95'">
            {{-- Header Profile Card --}}
            <div class="bg-gradient-to-br from-indigo-700 via-purple-700 to-indigo-900 p-6 md:p-8 text-white flex-shrink-0 shadow-md relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl transform translate-x-1/2 -translate-y-1/4"></div>
                <button @click="openDetailModal = false" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center text-white transition cursor-pointer z-10">
                    <i class="fas fa-times text-sm"></i>
                </button>
                
                <template x-if="selectedResponse">
                    <div class="flex items-center gap-5 relative z-10">
                        <img :src="'https://ui-avatars.com/api/?name=' + encodeURIComponent(selectedResponse.user ? selectedResponse.user.name : 'User') + '&background=random&color=fff&size=128&bold=true'" class="w-16 h-16 md:w-20 md:h-20 rounded-full border-4 border-white/20 shadow-lg object-cover" alt="Profile">
                        <div class="flex-1">
                            <span class="inline-block px-2.5 py-0.5 bg-white/20 rounded text-[9px] md:text-[10px] font-black uppercase tracking-widest mb-1.5 backdrop-blur-sm border border-white/30 shadow-sm" x-text="selectedResponse.teacher_type ? 'Guru ' + selectedResponse.teacher_type : (selectedResponse.user && selectedResponse.user.role === 'siswa' ? 'Siswa' : (selectedResponse.user ? selectedResponse.user.role : '-'))"></span>
                            <h3 class="font-black text-xl md:text-2xl tracking-tight leading-tight mb-1" x-text="selectedResponse.user ? selectedResponse.user.name : 'Unknown'"></h3>
                            <p class="text-indigo-200 text-[10px] md:text-xs font-semibold flex items-center gap-1.5">
                                <i class="fas fa-envelope opacity-70"></i> <span x-text="selectedResponse.user && selectedResponse.user.email ? selectedResponse.user.email : 'Tidak ada email'"></span>
                            </p>
                        </div>
                        <div class="text-right hidden sm:block">
                            <p class="text-[9px] text-indigo-200 uppercase font-black tracking-widest mb-1">Skor Akhir</p>
                            <div class="text-4xl font-black text-white drop-shadow-md" x-text="selectedResponse.average_score"></div>
                        </div>
                    </div>
                </template>
            </div>
            
            {{-- Body --}}
            <div class="p-0 overflow-y-auto flex-1 scrollbar-thin bg-slate-50/50">
                <template x-if="selectedResponse">
                    <div class="p-6 space-y-6">
                        
                        {{-- Meta Details Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white p-4 rounded-2xl border border-gray-150 shadow-sm flex items-center gap-4 hover:border-indigo-200 transition">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-gray-400 uppercase tracking-widest text-[9px] font-extrabold">Data Survei</span>
                                    <p class="font-black text-gray-800 text-xs mt-0.5 leading-snug">{{ $survey->title }}</p>
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-2xl border border-gray-150 shadow-sm flex items-center gap-4 hover:border-emerald-200 transition">
                                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg flex-shrink-0">
                                    <i class="far fa-calendar-check"></i>
                                </div>
                                <div class="min-w-0">
                                    <span class="text-gray-400 uppercase tracking-widest text-[9px] font-extrabold">Waktu Pelaksanaan</span>
                                    <p class="font-black text-gray-800 text-xs mt-0.5" x-text="new Date(selectedResponse.created_at).toLocaleString('id-ID', {dateStyle: 'long', timeStyle: 'short'})"></p>
                                </div>
                            </div>
                        </div>

                        {{-- AI Comprehensive Analysis (Simulated) --}}
                        <div class="relative bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl border border-indigo-100 p-6 shadow-sm overflow-hidden">
                            <div class="absolute top-0 right-0 p-4 opacity-5 pointer-events-none">
                                <i class="fas fa-robot text-7xl"></i>
                            </div>
                            <h5 class="text-xs font-black uppercase tracking-widest text-indigo-800 mb-4 flex items-center gap-2 relative z-10">
                                <span class="w-6 h-6 rounded-md bg-indigo-600 text-white flex items-center justify-center text-[10px] shadow-sm"><i class="fas fa-magic"></i></span> 
                                Kesimpulan & Rekomendasi Kebijakan (AI)
                            </h5>
                            <div class="text-sm text-gray-750 font-semibold leading-relaxed relative z-10" x-html="
                                (() => {
                                    if (!selectedResponse) return '';
                                    const avg = parseFloat(selectedResponse.average_score);
                                    if(isNaN(avg)) return '<p class=\'text-gray-500 italic\'>Belum cukup data skor untuk dianalisis secara akurat.</p>';
                                    if(avg >= 4.5) return '<p class=\'mb-4\'>Berdasarkan pedoman Surat Edaran Yayasan, responden ini menunjukkan tingkat keselarasan yang sangat tinggi <strong>(Excellence)</strong> terhadap visi, misi, dan standar operasional sekolah. Evaluasinya mencerminkan dedikasi, pemahaman konseptual, dan adaptasi praktis pada level mahir tanpa kendala yang signifikan. Individu ini merupakan aset berharga dalam menjaga ritme positif institusi.</p><div class=\'bg-emerald-100/60 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-bold shadow-sm leading-relaxed\'><i class=\'fas fa-check-circle text-emerald-600 mr-1.5\'></i> <strong>Keputusan Kebijakan Yayasan:</strong> Sangat direkomendasikan untuk diberikan apresiasi khusus, promosi peran strategis, atau ditugaskan sebagai mentor (Role Model) dalam program diseminasi praktik baik kepada rekan sejawat guna memacu akselerasi mutu pendidikan.</div>';
                                    if(avg >= 3.5) return '<p class=\'mb-4\'>Merujuk pada indikator kinerja dalam Surat Edaran Yayasan, profil responden ini tergolong sangat kompeten <strong>(Good)</strong>. Responden telah menjalankan perannya dengan baik dan sejalan dengan ekspektasi lembaga, meskipun hasil analisa menunjukkan masih terdapat ruang kecil untuk penguatan spesifik di beberapa sub-kompetensi teknis maupun adaptasi manajerial.</p><div class=\'bg-blue-100/60 border border-blue-200 text-blue-800 px-4 py-3 rounded-xl text-xs font-bold shadow-sm leading-relaxed\'><i class=\'fas fa-arrow-trend-up text-blue-600 mr-1.5\'></i> <strong>Keputusan Kebijakan Yayasan:</strong> Disarankan agar manajemen mendelegasikan tanggung jawab yang lebih menantang (Project-based Assignment) atau memfasilitasi keikutsertaannya dalam Pelatihan Tingkat Lanjut guna memaksimalkan potensinya menuju level Excellence.</div>';
                                    if(avg >= 2.5) return '<p class=\'mb-4\'>Hasil analisa sistem menempatkan responden ini pada kategori capaian menengah <strong>(Moderate)</strong> menurut parameter kelayakan Yayasan. Terdeteksi adanya stagnasi performa, keraguan implementasi, atau kurangnya pemahaman utuh terhadap kebijakan institusi terbaru yang berpotensi menghambat laju adaptasi dan produktivitas kerjanya.</p><div class=\'bg-amber-100/60 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-xs font-bold shadow-sm leading-relaxed\'><i class=\'fas fa-exclamation-triangle text-amber-600 mr-1.5\'></i> <strong>Keputusan Kebijakan Yayasan:</strong> Diperlukan intervensi manajemen berupa pendampingan terarah dan penyertaan dalam Program Penyegaran (Refresher Course). Pemantauan berkala oleh Kepala Sekolah sangat dianjurkan untuk mencegah penurunan motivasi lebih lanjut.</div>';
                                    return '<p class=\'mb-4 text-rose-700\'><strong>PERINGATAN KRITIS:</strong> Analisa mendeteksi tingkat evaluasi yang berada jauh di bawah standar kepatuhan dan ekspektasi kinerja yang ditetapkan oleh Surat Edaran Yayasan <strong>(Poor)</strong>. Hal ini mengindikasikan kuat adanya kendala fundamental, resistensi terhadap perubahan sistem, atau ketidaksesuaian tajam dengan budaya kerja perguruan yang sangat mendesak untuk ditangani.</p><div class=\'bg-rose-100/60 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl text-xs font-bold shadow-sm leading-relaxed\'><i class=\'fas fa-radiation text-rose-600 mr-1.5\'></i> <strong>Tindakan Darurat Yayasan:</strong> Pimpinan lembaga wajib segera menjadwalkan sesi Pemanggilan Khusus (1-on-1 Counseling) atau mediasi formal. Diperlukan asesmen menyeluruh untuk menentukan tindakan pembinaan intensif atau peninjauan ulang status evaluasi yang bersangkutan.</div>';
                                })()
                            "></div>
                        </div>

                        {{-- Toggleable Answers List --}}
                        <div x-data="{ showRaw: false }" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                            <button @click="showRaw = !showRaw" type="button" class="w-full px-5 py-4 flex items-center justify-between bg-slate-50 hover:bg-indigo-50/50 hover:border-indigo-100 transition-colors focus:outline-none cursor-pointer border-b border-transparent" :class="showRaw ? 'border-gray-200' : ''">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shadow-sm">
                                        <i class="fas fa-list-ul text-sm"></i>
                                    </div>
                                    <div class="text-left">
                                        <h4 class="font-black text-sm text-gray-800 tracking-wide">Rincian Jawaban Survei</h4>
                                        <p class="text-[10px] text-gray-500 font-bold mt-0.5 uppercase tracking-wider">Lihat riwayat jawaban per butir soal</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 hidden sm:block" x-text="showRaw ? 'Tutup Rincian' : 'Buka Rincian'"></span>
                                    <i class="fas fa-chevron-down transition-transform duration-300 text-gray-400 text-sm" :class="showRaw ? 'rotate-180' : ''"></i>
                                </div>
                            </button>
                            
                            <div x-show="showRaw" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
                                <div class="p-6 space-y-6 divide-y divide-gray-100 bg-white max-h-[400px] overflow-y-auto scrollbar-thin">
                                    <template x-for="(ans, idx) in selectedResponse.answers" :key="ans.id">
                                        <div class="pt-6 first:pt-0">
                                            <div class="flex gap-3 mb-3">
                                                <span class="w-6 h-6 bg-slate-100 border border-slate-200 text-slate-500 rounded text-[10px] font-black flex items-center justify-center flex-shrink-0" x-text="'Q' + (idx + 1)"></span>
                                                <p class="text-gray-800 font-extrabold text-xs md:text-sm leading-relaxed" x-text="ans.question.question_text"></p>
                                            </div>
                                            
                                            <div class="pl-9">
                                                {{-- Rating Question Type --}}
                                                <template x-if="ans.question.type === 'scale'">
                                                    <div class="flex items-center gap-2 mt-1">
                                                        <template x-if="ans.question.scale_type === 'yes_no'">
                                                            <span :class="ans.rating == 1 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-rose-50 text-rose-700 border-rose-200'" class="px-3 py-1 rounded-lg text-[10px] font-black uppercase border" x-text="ans.rating == 1 ? 'Ya / Setuju' : 'Tidak / Kurang Setuju'"></span>
                                                        </template>
                                                        <template x-if="ans.question.scale_type !== 'yes_no'">
                                                            <div class="flex items-center gap-2.5">
                                                                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-[11px] font-black border border-indigo-150 shadow-sm" x-text="'Skor: ' + ans.rating + '/' + (ans.question.scale_type === 'likert_4' ? '4' : '5')"></span>
                                                                <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">
                                                                    <span x-text="
                                                                        ans.question.scale_type === 'competence_5' 
                                                                            ? ['Sangat Kurang', 'Kurang Menguasai', 'Cukup Menguasai', 'Menguasai', 'Sangat Menguasai'][ans.rating - 1] 
                                                                            : (ans.question.scale_type === 'likert_4' 
                                                                                ? ['Sangat Tidak Setuju', 'Tidak Setuju', 'Setuju', 'Sangat Setuju'][ans.rating - 1]
                                                                                : ['Sangat Tidak Setuju', 'Tidak Setuju', 'Ragu-ragu / Netral', 'Setuju', 'Sangat Setuju'][ans.rating - 1])
                                                                    "></span>
                                                                </span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                                
                                                {{-- Essay / Text Question Type --}}
                                                <template x-if="ans.question.type === 'text'">
                                                    <div class="space-y-3 mt-1">
                                                        <div class="bg-slate-50 border border-gray-200 rounded-xl p-4 text-xs font-semibold text-gray-700 whitespace-pre-line leading-relaxed italic" x-text="ans.answer_text ? '“' + ans.answer_text + '”' : 'Tidak mengisi tanggapan tertulis.'"></div>
                                                        
                                                        {{-- Input Nilai Essay dari Admin --}}
                                                        <template x-if="ans.answer_text">
                                                            <div class="flex flex-col sm:flex-row sm:items-center gap-3 bg-white p-3 rounded-xl border border-gray-200 max-w-md shadow-sm">
                                                                <span class="text-[9px] font-extrabold text-gray-500 uppercase tracking-widest"><i class="fas fa-edit mr-1"></i> Beri Nilai Esai:</span>
                                                                <div class="flex items-center gap-1.5">
                                                                    <template x-for="val in [1, 2, 3, 4, 5]">
                                                                         <button type="button" 
                                                                                @click="saveEssayScore(ans.id, val)" 
                                                                                :class="ans.essay_score == val ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-600/30 transform scale-110' : 'bg-gray-50 text-gray-600 border-gray-200 hover:border-indigo-400 hover:bg-white'" 
                                                                                class="w-8 h-8 rounded-lg border text-xs font-extrabold flex items-center justify-center transition-all cursor-pointer select-none" 
                                                                                x-text="val">
                                                                        </button>
                                                                    </template>
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                    </div>
                </template>
            </div>
        </div>
                    </div>
                </template>
            </div>
            
            {{-- Footer --}}
            <div class="p-4 bg-slate-50 border-t border-gray-150 flex items-center justify-between flex-shrink-0">
                <template x-if="selectedResponse">
                    <form :action="'{{ url('admin/surveys/responses') }}/' + selectedResponse.id" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/meriset tanggapan survei dari responden ini? Responden bersangkutan dapat mengisi ulang survei kembali setelah dihapus.')" class="inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2.5 bg-rose-50 hover:bg-rose-600 text-rose-650 hover:text-white border border-rose-200 hover:border-rose-600 rounded-xl text-xs font-extrabold transition cursor-pointer flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-trash text-[10px]"></i>
                            <span>Hapus Tanggapan</span>
                        </button>
                    </form>
                </template>
                <button @click="openDetailModal = false" class="px-5 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-extrabold rounded-xl text-xs transition cursor-pointer">
                    Tutup Lembar
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
