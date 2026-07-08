@extends('layouts.siswa')

@section('title', 'Hasil Quiz: ' . $quiz->title)

@push('styles')
<style>
    /* ===== Premium Result Page ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeInScale {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }
    @keyframes drawCircle {
        from { stroke-dashoffset: 314; }
    }
    @keyframes countUp {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }
        50% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.4); }
    }
    @keyframes pulseGlowRed {
        0%, 100% { box-shadow: 0 0 20px rgba(244, 63, 94, 0.2); }
        50% { box-shadow: 0 0 40px rgba(244, 63, 94, 0.4); }
    }
    @keyframes shimmer {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes badgePop {
        0% { transform: scale(0); }
        60% { transform: scale(1.15); }
        100% { transform: scale(1); }
    }

    .result-fadeIn {
        animation: fadeInUp 0.6s ease-out both;
    }
    .result-scale {
        animation: fadeInScale 0.5s ease-out both;
    }
    .result-slide {
        animation: slideInLeft 0.5s ease-out both;
    }

    .score-ring {
        animation: drawCircle 1.5s ease-out both;
        animation-delay: 0.3s;
    }

    .score-glow-pass {
        animation: pulseGlow 2s ease-in-out infinite;
    }
    .score-glow-fail {
        animation: pulseGlowRed 2s ease-in-out infinite;
    }

    .badge-pop {
        animation: badgePop 0.4s ease-out both;
        animation-delay: 1s;
    }

    .stat-card {
        animation: fadeInUp 0.5s ease-out both;
    }
    .stat-card:nth-child(1) { animation-delay: 0.6s; }
    .stat-card:nth-child(2) { animation-delay: 0.75s; }
    .stat-card:nth-child(3) { animation-delay: 0.9s; }

    .review-card {
        animation: fadeInUp 0.4s ease-out both;
    }

    .shimmer-text {
        background: linear-gradient(90deg, currentColor 40%, rgba(255,255,255,0.8) 50%, currentColor 60%);
        background-size: 200% auto;
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: shimmer 3s linear infinite;
    }

    /* SVG Progress Ring */
    .progress-ring-circle-bg {
        stroke: #e5e7eb;
    }
    .progress-ring-circle-pass {
        stroke: url(#gradientPass);
        filter: drop-shadow(0 0 6px rgba(16, 185, 129, 0.4));
    }
    .progress-ring-circle-fail {
        stroke: url(#gradientFail);
        filter: drop-shadow(0 0 6px rgba(244, 63, 94, 0.4));
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-8" x-data="{ shown: true }">

    {{-- ===== SCORE HERO SECTION ===== --}}
    <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden result-fadeIn">
        {{-- Top gradient banner --}}
        <div class="h-2 {{ $attempt->is_passed ? 'bg-gradient-to-r from-emerald-400 via-teal-400 to-emerald-500' : 'bg-gradient-to-r from-rose-400 via-pink-400 to-rose-500' }}"></div>

        <div class="px-6 sm:px-10 py-10">
            {{-- Score Circle --}}
            <div class="flex flex-col items-center">
                <div class="relative {{ $attempt->is_passed ? 'score-glow-pass' : 'score-glow-fail' }} rounded-full">
                    <svg width="180" height="180" viewBox="0 0 120 120" class="transform -rotate-90">
                        <defs>
                            <linearGradient id="gradientPass" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#10b981"/>
                                <stop offset="50%" stop-color="#34d399"/>
                                <stop offset="100%" stop-color="#059669"/>
                            </linearGradient>
                            <linearGradient id="gradientFail" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" stop-color="#f43f5e"/>
                                <stop offset="50%" stop-color="#fb7185"/>
                                <stop offset="100%" stop-color="#e11d48"/>
                            </linearGradient>
                        </defs>
                        <circle cx="60" cy="60" r="50" fill="none" stroke-width="8" class="progress-ring-circle-bg"/>
                        <circle cx="60" cy="60" r="50" fill="none" stroke-width="8"
                                class="score-ring {{ $attempt->is_passed ? 'progress-ring-circle-pass' : 'progress-ring-circle-fail' }}"
                                stroke-linecap="round"
                                stroke-dasharray="314"
                                stroke-dashoffset="{{ 314 - (314 * min($attempt->score, 100) / 100) }}"/>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-extrabold {{ $attempt->is_passed ? 'text-emerald-600' : 'text-rose-600' }}"
                              x-data="{ val: 0 }"
                              x-init="setTimeout(() => {
                                  let target = {{ number_format($attempt->score, 1) }};
                                  let step = target / 40;
                                  let iv = setInterval(() => {
                                      val += step;
                                      if (val >= target) { val = target; clearInterval(iv); }
                                  }, 30);
                              }, 500)"
                              x-text="val.toFixed(1) + '%'">0%</span>
                        <span class="text-xs text-gray-400 mt-0.5">Skor Anda</span>
                    </div>
                </div>

                {{-- Pass/Fail Badge --}}
                <div class="mt-5 badge-pop">
                    @if($attempt->is_passed)
                    <div class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-bold text-lg shadow-lg shadow-emerald-200">
                        <i class="fas fa-trophy"></i> LULUS
                    </div>
                    @else
                    <div class="inline-flex items-center gap-2 px-6 py-2.5 rounded-full bg-gradient-to-r from-rose-500 to-pink-500 text-white font-bold text-lg shadow-lg shadow-rose-200">
                        <i class="fas fa-times-circle"></i> TIDAK LULUS
                    </div>
                    @endif
                </div>
            </div>

            {{-- Stats Row --}}
            @php
                $totalQuestions = $attempt->answers ? $attempt->answers->count() : 0;
                $correctAnswers = $attempt->answers ? $attempt->answers->where('is_correct', true)->count() : 0;
                $wrongAnswers = $attempt->answers ? $attempt->answers->where('is_correct', false)->count() : 0;
                $pendingAnswers = $attempt->answers ? $attempt->answers->whereNull('is_correct')->count() : 0;
            @endphp
            <div class="grid grid-cols-{{ $pendingAnswers > 0 ? '4' : '3' }} gap-4 mt-8">
                <div class="stat-card bg-emerald-50 border border-emerald-100 rounded-2xl p-4 text-center">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-emerald-100 mb-2">
                        <i class="fas fa-check text-emerald-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-emerald-700">{{ $correctAnswers }}</div>
                    <div class="text-xs text-emerald-600 font-medium mt-0.5">Benar</div>
                </div>
                <div class="stat-card bg-rose-50 border border-rose-100 rounded-2xl p-4 text-center">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-rose-100 mb-2">
                        <i class="fas fa-times text-rose-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-rose-700">{{ $wrongAnswers }}</div>
                    <div class="text-xs text-rose-600 font-medium mt-0.5">Salah</div>
                </div>
                @if($pendingAnswers > 0)
                <div class="stat-card bg-amber-50 border border-amber-100 rounded-2xl p-4 text-center">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-amber-100 mb-2">
                        <i class="fas fa-hourglass-half text-amber-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-amber-700">{{ $pendingAnswers }}</div>
                    <div class="text-xs text-amber-600 font-medium mt-0.5">Menunggu Dinilai</div>
                </div>
                @endif
                <div class="stat-card bg-blue-50 border border-blue-100 rounded-2xl p-4 text-center">
                    <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-blue-100 mb-2">
                        <i class="fas fa-list-ol text-blue-600"></i>
                    </div>
                    <div class="text-2xl font-bold text-blue-700">{{ $totalQuestions }}</div>
                    <div class="text-xs text-blue-600 font-medium mt-0.5">Total Soal</div>
                </div>
            </div>

            {{-- Info Row --}}
            <div class="flex flex-wrap items-center justify-center gap-4 sm:gap-6 mt-6 text-sm">
                <div class="flex items-center gap-2 text-gray-500 bg-gray-50 px-4 py-2 rounded-xl">
                    <i class="fas fa-bullseye text-purple-500"></i>
                    <span>Passing: <span class="font-semibold text-gray-700">{{ $quiz->passing_score }}%</span></span>
                </div>
                <div class="flex items-center gap-2 text-gray-500 bg-gray-50 px-4 py-2 rounded-xl">
                    <i class="fas fa-redo text-blue-500"></i>
                    <span>Percobaan ke-<span class="font-semibold text-gray-700">{{ $attempt->id }}</span></span>
                </div>
                <div class="flex items-center gap-2 text-gray-500 bg-gray-50 px-4 py-2 rounded-xl">
                    <i class="fas fa-calendar-check text-teal-500"></i>
                    <span class="font-semibold text-gray-700">{{ $attempt->finished_at ? $attempt->finished_at->format('d M Y H:i') : '-' }}</span>
                </div>
            </div>

            {{-- Retry Button --}}
            @php
                $remaining = $quiz->getRemainingAttempts($student->id);
            @endphp
            @if($remaining === null || $remaining > 0)
            <div class="mt-8 text-center result-fadeIn" style="animation-delay: 1.2s">
                <a href="{{ route('siswa.lms.quizzes.start', $quiz->id) }}"
                   class="inline-flex items-center gap-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-8 py-3.5 rounded-2xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg shadow-purple-200 font-semibold hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fas fa-redo"></i>
                    Coba Lagi
                    @if($remaining !== null)
                    <span class="text-xs bg-white/20 px-2.5 py-1 rounded-lg">{{ $remaining }}x tersisa</span>
                    @endif
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- ===== ANSWER REVIEW SECTION ===== --}}
    @if($quiz->show_result && isset($attempt->answers))
    <div class="space-y-5 result-fadeIn" style="animation-delay: 0.5s">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 flex items-center justify-center shadow-lg">
                <i class="fas fa-clipboard-list text-white text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Review Jawaban</h3>
                <p class="text-xs text-gray-400">Lihat detail jawaban Anda per soal</p>
            </div>
        </div>

        @foreach($attempt->answers as $aIdx => $answer)
        @php
            $answerState = $answer->is_correct === true ? 'correct' : ($answer->is_correct === false ? 'wrong' : 'pending');
        @endphp
        <div class="review-card bg-white rounded-2xl shadow-sm border-2 overflow-hidden
                    {{ $answerState === 'correct' ? 'border-emerald-200' : ($answerState === 'wrong' ? 'border-rose-200' : 'border-amber-200') }}"
             style="animation-delay: {{ 0.7 + ($aIdx * 0.08) }}s">

            {{-- Card accent bar --}}
            <div class="h-1 {{ $answerState === 'correct' ? 'bg-gradient-to-r from-emerald-400 to-teal-400' : ($answerState === 'wrong' ? 'bg-gradient-to-r from-rose-400 to-pink-400' : 'bg-gradient-to-r from-amber-400 to-yellow-400') }}"></div>

            <div class="p-5">
                <div class="flex items-start gap-4">
                    {{-- Status circle --}}
                    <div class="flex-shrink-0">
                        <div class="w-11 h-11 rounded-full flex items-center justify-center shadow-md
                                    {{ $answerState === 'correct'
                                        ? 'bg-gradient-to-br from-emerald-400 to-emerald-600 shadow-emerald-200'
                                        : ($answerState === 'wrong'
                                            ? 'bg-gradient-to-br from-rose-400 to-rose-600 shadow-rose-200'
                                            : 'bg-gradient-to-br from-amber-400 to-amber-500 shadow-amber-200') }}">
                            <i class="fas {{ $answerState === 'correct' ? 'fa-check' : ($answerState === 'wrong' ? 'fa-times' : 'fa-hourglass-half') }} text-white text-sm"></i>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-3">
                            <p class="font-medium text-gray-800 leading-relaxed">{{ $answer->question->question ?? 'Soal tidak tersedia' }}</p>
                            <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold
                                        {{ $answerState === 'correct' ? 'bg-emerald-50 text-emerald-700' : ($answerState === 'wrong' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                                <i class="fas fa-star text-[9px]"></i>
                                {{ $answer->score ?? 0 }}/{{ $answer->question->score ?? 0 }}
                            </span>
                        </div>

                        <div class="mt-3 space-y-2">
                            {{-- Student's answer --}}
                            <div class="flex items-start gap-2 text-sm">
                                <span class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center mt-0.5
                                            {{ $answerState === 'correct' ? 'bg-emerald-100 text-emerald-600' : ($answerState === 'wrong' ? 'bg-rose-100 text-rose-600' : 'bg-amber-100 text-amber-600') }}">
                                    <i class="fas {{ $answerState === 'correct' ? 'fa-check' : ($answerState === 'wrong' ? 'fa-times' : 'fa-hourglass-half') }} text-[10px]"></i>
                                </span>
                                <div>
                                    <span class="text-gray-500 text-xs">Jawaban Anda:</span>
                                    @php
                                        // Resolve display text for index-based answers
                                        $displayAnswer = $answer->answer ?? '-';
                                        if ($answer->question && $answer->question->question_type === 'multiple_choice' && $answer->question->options) {
                                            $opts = $answer->question->options;
                                            $firstOpt = $opts[0] ?? null;
                                            if (!is_array($firstOpt) || !isset($firstOpt['key'])) {
                                                // Non-associative: resolve index to text + label
                                                $ansIdx = (int)$displayAnswer;
                                                $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                                                if (isset($opts[$ansIdx])) {
                                                    $displayAnswer = ($alphabet[$ansIdx] ?? ($ansIdx+1)) . '. ' . $opts[$ansIdx];
                                                }
                                            } else {
                                                // Associative: show key + text
                                                foreach ($opts as $o) {
                                                    if (isset($o['key']) && strtolower($o['key']) === strtolower($displayAnswer)) {
                                                        $displayAnswer = $o['key'] . '. ' . $o['text'];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <p class="font-medium {{ $answerState === 'correct' ? 'text-emerald-700' : ($answerState === 'wrong' ? 'text-rose-700' : 'text-amber-700') }}">{{ $displayAnswer }}</p>
                                </div>
                            </div>

                            {{-- Correct answer (if wrong) --}}
                            @if($answerState === 'wrong' && $answer->question)
                            <div class="flex items-start gap-2 text-sm">
                                <span class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center bg-emerald-100 text-emerald-600 mt-0.5">
                                    <i class="fas fa-check text-[10px]"></i>
                                </span>
                                <div>
                                    <span class="text-gray-500 text-xs">Jawaban Benar:</span>
                                    @php
                                        $correctDisplay = $answer->question->correct_answer;
                                        if ($answer->question->question_type === 'multiple_choice' && $answer->question->options) {
                                            $opts = $answer->question->options;
                                            $firstOpt = $opts[0] ?? null;
                                            if (!is_array($firstOpt) || !isset($firstOpt['key'])) {
                                                $cIdx = (int)$correctDisplay;
                                                $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                                                if (isset($opts[$cIdx])) {
                                                    $correctDisplay = ($alphabet[$cIdx] ?? ($cIdx+1)) . '. ' . $opts[$cIdx];
                                                }
                                            } else {
                                                foreach ($opts as $o) {
                                                    if (isset($o['key']) && strtolower($o['key']) === strtolower($correctDisplay)) {
                                                        $correctDisplay = $o['key'] . '. ' . $o['text'];
                                                        break;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <p class="font-medium text-emerald-700">{{ $correctDisplay }}</p>
                                </div>
                            </div>
                            @elseif($answerState === 'pending' && $answer->question)
                            <div class="flex items-start gap-2 text-sm">
                                <span class="flex-shrink-0 w-5 h-5 rounded flex items-center justify-center bg-amber-100 text-amber-600 mt-0.5">
                                    <i class="fas fa-clock text-[10px]"></i>
                                </span>
                                <div>
                                    <span class="text-amber-600 text-xs font-medium">Menunggu penilaian dari guru</span>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ===== BACK TO COURSE BUTTON ===== --}}
    <div class="result-fadeIn" style="animation-delay: 1s">
        <a href="{{ route('siswa.lms.show', $course->id) }}?tab=quizzes"
           class="inline-flex items-center gap-3 bg-white border-2 border-gray-200 text-gray-700 px-6 py-3.5 rounded-2xl hover:border-purple-300 hover:bg-purple-50 hover:text-purple-700 transition-all font-semibold group shadow-sm hover:shadow-md">
            <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            Kembali ke Course
        </a>
    </div>

</div>
@endsection
