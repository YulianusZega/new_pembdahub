@extends('layouts.siswa')

@section('title', 'Quiz: ' . $quiz->title)

@push('styles')
<style>
    /* ===== Immersive Exam Experience ===== */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(24px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulseGlow {
        0%, 100% { box-shadow: 0 0 8px rgba(239,68,68,0.3); }
        50% { box-shadow: 0 0 24px rgba(239,68,68,0.6); }
    }
    @keyframes checkPop {
        0% { transform: scale(0); }
        60% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(40px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes gentlePulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    .quiz-fadeIn {
        animation: fadeInUp 0.5s ease-out both;
    }
    .quiz-fadeIn-delay-1 { animation-delay: 0.08s; }
    .quiz-fadeIn-delay-2 { animation-delay: 0.16s; }
    .quiz-fadeIn-delay-3 { animation-delay: 0.24s; }

    .timer-pulse {
        animation: pulseGlow 1s ease-in-out infinite;
    }
    .timer-gentle-pulse {
        animation: gentlePulse 1.5s ease-in-out infinite;
    }
    .check-anim {
        animation: checkPop 0.3s ease-out both;
    }
    .nav-slide {
        animation: slideInRight 0.4s ease-out both;
    }

    /* Custom radio/checkbox styling */
    .quiz-option input[type="radio"],
    .quiz-option input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        flex-shrink: 0;
        transition: all 0.2s ease;
        position: relative;
        cursor: pointer;
    }
    .quiz-option input[type="radio"]:checked {
        border-color: #7c3aed;
        background: #7c3aed;
    }
    .quiz-option input[type="radio"]:checked::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 8px;
        height: 8px;
        background: white;
        border-radius: 50%;
        animation: checkPop 0.2s ease-out;
    }
    .quiz-option:hover {
        transform: translateX(4px);
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
    }
    .quiz-option.selected {
        background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 100%);
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124,58,237,0.1);
    }

    /* Glassmorphism footer */
    .glass-footer {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-top: 1px solid rgba(255, 255, 255, 0.3);
    }

    /* Navigator scrollbar */
    .nav-panel::-webkit-scrollbar {
        width: 4px;
    }
    .nav-panel::-webkit-scrollbar-thumb {
        background: rgba(148, 163, 184, 0.4);
        border-radius: 4px;
    }

    /* Question number badge gradient */
    .q-number-badge {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 50%, #5b21b6 100%);
        box-shadow: 0 4px 12px rgba(124, 58, 237, 0.3);
    }

    /* Smooth scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Timer gradient states */
    .timer-safe {
        background: linear-gradient(135deg, #059669, #10b981);
    }
    .timer-warning {
        background: linear-gradient(135deg, #d97706, #f59e0b);
    }
    .timer-danger {
        background: linear-gradient(135deg, #dc2626, #ef4444);
    }
</style>
@endpush

@section('content')
<div x-data="quizApp()" x-init="initQuiz()" class="relative">

    {{-- ===== FLOATING TIMER BAR ===== --}}
    <div class="sticky top-0 z-50 -mx-4 sm:-mx-6 lg:-mx-8 mb-6">
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 shadow-xl">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between py-3">
                    {{-- Left: Course & Quiz info --}}
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="hidden sm:flex items-center justify-center w-10 h-10 rounded-xl bg-white/10 flex-shrink-0">
                            <i class="fas fa-file-alt text-purple-400"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-white font-bold text-sm sm:text-base truncate">{{ $quiz->title }}</h1>
                            <p class="text-slate-400 text-xs truncate">{{ $course->name }} · Percobaan #{{ $attempt->id }}</p>
                        </div>
                    </div>

                    {{-- Center: Progress --}}
                    <div class="hidden md:flex items-center gap-2 px-4">
                        <div class="text-xs text-slate-400">
                            <span class="text-white font-semibold" x-text="answeredCount"></span>
                            <span>dari {{ count($questions) }} dijawab</span>
                        </div>
                        <div class="w-32 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-purple-500 to-emerald-400 rounded-full transition-all duration-500"
                                 :style="'width:' + (answeredCount / {{ count($questions) }} * 100) + '%'"></div>
                        </div>
                    </div>

                    {{-- Right: Timer --}}
                    @if($quiz->time_limit)
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <div :class="timerClass"
                             class="px-4 py-2 rounded-xl flex items-center gap-2 transition-all duration-500"
                             :style="timerSeconds <= 120 ? '' : ''">
                            <i class="fas fa-stopwatch text-white/80 text-sm"></i>
                            <span id="timer" class="text-white font-mono font-bold text-lg tracking-wider"
                                  :class="{ 'timer-gentle-pulse': timerSeconds <= 120 }"
                                  x-text="timerDisplay">{{ $quiz->time_limit }}:00</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Anti-Cheat Info Banner --}}
        <div class="bg-gradient-to-r from-amber-500/10 via-amber-500/5 to-amber-500/10 border-b border-amber-200/30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-center gap-2 py-1.5 text-xs text-amber-700">
                    <i class="fas fa-shield-alt text-amber-500"></i>
                    <span>Aktivitas tab dipantau oleh sistem · Jangan berpindah tab selama ujian berlangsung</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN CONTENT GRID ===== --}}
    <div class="flex gap-6">

        {{-- Questions Column --}}
        <div class="flex-1 min-w-0">
            <form id="quizForm" action="{{ route('siswa.lms.quizzes.submit', $attempt->id) }}" method="POST"
                  class="space-y-5" onsubmit="return confirm('Yakin ingin mengumpulkan jawaban? Anda tidak bisa mengubah jawaban setelah submit.')">
                @csrf

                @foreach($questions as $i => $question)
                @php $existingAnswer = $answerMap[$question->id] ?? null; @endphp
                <div id="question-{{ $i }}"
                     class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden quiz-fadeIn"
                     style="animation-delay: {{ $i * 0.06 }}s"
                     :class="{ 'ring-2 ring-amber-400 border-amber-200': flagged.includes({{ $i }}) }">

                    {{-- Question Header --}}
                    <div class="flex items-center justify-between px-5 pt-5 pb-3">
                        <div class="flex items-center gap-3">
                            <span class="q-number-badge text-white w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 shadow-lg">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-purple-50 text-purple-700 border border-purple-100">
                                    <i class="fas fa-star text-[10px]"></i> {{ $question->score }} poin
                                </span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-slate-50 text-slate-600 border border-slate-100">
                                    @if($question->question_type === 'multiple_choice')
                                        <i class="fas fa-list-ul text-[10px]"></i> Pilihan Ganda
                                    @elseif($question->question_type === 'true_false')
                                        <i class="fas fa-toggle-on text-[10px]"></i> Benar/Salah
                                    @elseif($question->question_type === 'short_answer')
                                        <i class="fas fa-pencil-alt text-[10px]"></i> Jawaban Singkat
                                    @elseif($question->question_type === 'essay')
                                        <i class="fas fa-align-left text-[10px]"></i> Essay
                                    @endif
                                </span>
                            </div>
                        </div>
                        {{-- Flag button --}}
                        <button type="button"
                                @click="toggleFlag({{ $i }})"
                                :class="flagged.includes({{ $i }}) ? 'bg-amber-100 text-amber-600 border-amber-300' : 'bg-gray-50 text-gray-400 border-gray-200 hover:text-amber-500 hover:bg-amber-50'"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium border transition-all duration-200">
                            <i class="fas fa-flag text-[11px]"></i>
                            <span class="hidden sm:inline" x-text="flagged.includes({{ $i }}) ? 'Ditandai' : 'Tandai Ragu'"></span>
                        </button>
                    </div>

                    {{-- Question Text --}}
                    <div class="px-5 pb-4">
                        <p class="text-gray-800 font-medium leading-relaxed">{{ $question->question }}</p>
                        
                        {{-- Media Display --}}
                        @if($question->image_path)
                        <div class="mt-3 max-w-lg rounded-xl overflow-hidden shadow-sm border border-gray-200 bg-white">
                            <img src="{{ asset('storage/' . $question->image_path) }}" class="w-full h-auto object-contain max-h-[350px]" alt="Gambar Pendukung">
                        </div>
                        @endif
                        
                        @if($question->video_url)
                        <div class="mt-3 max-w-lg rounded-xl overflow-hidden shadow-sm border border-gray-200 bg-black">
                            @php
                                $isYoutube = preg_match('/(youtube\.com|youtu\.be)/i', $question->video_url);
                                $embedUrl = '';
                                if ($isYoutube) {
                                    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/i', $question->video_url, $matches)) {
                                        $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                                    } elseif (preg_match('/(?:v=|\/embed\/|&v=)([a-zA-Z0-9_-]+)/i', $question->video_url, $matches)) {
                                        $embedUrl = 'https://www.youtube.com/embed/' . $matches[1];
                                    }
                                }
                            @endphp
                            
                            @if($isYoutube && $embedUrl)
                                <div class="aspect-video w-full">
                                    <iframe class="w-full h-full" src="{{ $embedUrl }}" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                </div>
                            @else
                                <video class="w-full h-auto object-contain max-h-[350px]" controls preload="metadata">
                                    <source src="{{ $question->video_url }}" type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            @endif
                        </div>
                        @endif
                    </div>

                    {{-- Answer Options --}}
                    <div class="px-5 pb-5">
                        @php
                            $options = $quiz->shuffle_questions ? $question->getShuffledOptions($attempt->id) : $question->options;
                        @endphp

                        @if($question->question_type === 'multiple_choice' && $options)
                        <div class="space-y-2">
                            @foreach($options as $idx => $opt)
                            @php
                                $alphabet = ['A', 'B', 'C', 'D', 'E', 'F', 'G'];
                                $isAssoc = is_array($opt) && isset($opt['key']);
                                $optValue = $isAssoc ? $opt['key'] : (string)$idx;
                                $optLabel = $isAssoc ? $opt['key'] : ($alphabet[$idx] ?? $idx + 1);
                                $optText = $isAssoc ? $opt['text'] : $opt;
                                $showLabel = $isAssoc || ($optLabel !== $optText);
                                $isChecked = ($existingAnswer && (string)$existingAnswer->answer === (string)$optValue);
                            @endphp
                            <label class="quiz-option flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all duration-200
                                          {{ $isChecked ? 'selected border-purple-300 bg-purple-50/50' : 'border-transparent hover:border-purple-100' }}"
                                   @click="markAnswered({{ $question->id }})">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $optValue }}"
                                       {{ $isChecked ? 'checked' : '' }}
                                       @change="markAnswered({{ $question->id }})"
                                       class="text-purple-600 focus:ring-purple-500">
                                @if($showLabel)
                                <span class="flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 text-gray-600 text-xs font-bold flex-shrink-0">{{ $optLabel }}</span>
                                @endif
                                <span class="text-sm text-gray-700">{{ $optText }}</span>
                            </label>
                            @endforeach
                        </div>

                        @elseif($question->question_type === 'true_false')
                        <div class="grid grid-cols-2 gap-3">
                            <label class="quiz-option flex items-center justify-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200
                                          {{ ($existingAnswer && $existingAnswer->answer === 'true') ? 'selected border-emerald-300 bg-emerald-50/50' : 'border-gray-200 hover:border-emerald-200' }}"
                                   @click="markAnswered({{ $question->id }})">
                                <input type="radio" name="answers[{{ $question->id }}]" value="true"
                                       {{ ($existingAnswer && $existingAnswer->answer === 'true') ? 'checked' : '' }}
                                       @change="markAnswered({{ $question->id }})"
                                       class="text-purple-600">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                <span class="text-sm font-medium">Benar</span>
                            </label>
                            <label class="quiz-option flex items-center justify-center gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all duration-200
                                          {{ ($existingAnswer && $existingAnswer->answer === 'false') ? 'selected border-rose-300 bg-rose-50/50' : 'border-gray-200 hover:border-rose-200' }}"
                                   @click="markAnswered({{ $question->id }})">
                                <input type="radio" name="answers[{{ $question->id }}]" value="false"
                                       {{ ($existingAnswer && $existingAnswer->answer === 'false') ? 'checked' : '' }}
                                       @change="markAnswered({{ $question->id }})"
                                       class="text-purple-600">
                                <i class="fas fa-times-circle text-rose-500"></i>
                                <span class="text-sm font-medium">Salah</span>
                            </label>
                        </div>

                        @elseif($question->question_type === 'short_answer')
                        <div>
                            <input type="text" name="answers[{{ $question->id }}]"
                                   value="{{ $existingAnswer->answer ?? '' }}"
                                   @input="markAnswered({{ $question->id }})"
                                   class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all math-support"
                                   placeholder="Ketik jawaban singkat di sini...">
                        </div>

                        @elseif($question->question_type === 'essay')
                        <div>
                            <textarea name="answers[{{ $question->id }}]" rows="5"
                                      @input="markAnswered({{ $question->id }})"
                                      class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 text-sm focus:border-purple-400 focus:ring-2 focus:ring-purple-100 transition-all resize-y math-support"
                                      placeholder="Tulis jawaban essay Anda di sini...">{{ $existingAnswer->answer ?? '' }}</textarea>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach

                {{-- Spacer for sticky footer --}}
                <div class="h-24"></div>
            </form>
        </div>

        {{-- ===== QUESTION NAVIGATOR — Desktop Sidebar ===== --}}
        <div class="hidden lg:block w-64 flex-shrink-0">
            <div class="sticky top-36 nav-slide">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-slate-800 to-slate-900 px-4 py-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-white text-sm font-semibold"><i class="fas fa-th mr-1.5"></i> Navigasi Soal</h3>
                            <span class="text-xs text-slate-400"><span class="text-emerald-400 font-semibold" x-text="answeredCount"></span>/{{ count($questions) }}</span>
                        </div>
                    </div>
                    <div class="p-4 nav-panel max-h-[60vh] overflow-y-auto">
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($questions as $i => $question)
                            <button type="button"
                                    @click="scrollToQuestion({{ $i }})"
                                    :class="{
                                        'bg-emerald-500 text-white shadow-emerald-200 shadow-md': answered.includes({{ $question->id }}) && !flagged.includes({{ $i }}),
                                        'bg-amber-400 text-white shadow-amber-200 shadow-md': flagged.includes({{ $i }}),
                                        'bg-gray-100 text-gray-500 hover:bg-gray-200': !answered.includes({{ $question->id }}) && !flagged.includes({{ $i }})
                                    }"
                                    class="w-full aspect-square rounded-lg flex items-center justify-center text-xs font-bold transition-all duration-200 hover:scale-110">
                                {{ $i + 1 }}
                            </button>
                            @endforeach
                        </div>
                        {{-- Legend --}}
                        <div class="mt-4 pt-3 border-t border-gray-100 space-y-1.5">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="w-3 h-3 rounded bg-emerald-500 flex-shrink-0"></span> Dijawab
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="w-3 h-3 rounded bg-amber-400 flex-shrink-0"></span> Ditandai Ragu
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <span class="w-3 h-3 rounded bg-gray-200 flex-shrink-0"></span> Belum Dijawab
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== QUESTION NAVIGATOR — Mobile Bottom Bar ===== --}}
    <div class="lg:hidden fixed bottom-16 left-0 right-0 z-40 bg-white border-t border-gray-200 shadow-lg"
         x-show="showMobileNav" x-transition
         @click.away="showMobileNav = false">
        <div class="px-4 py-3 max-h-48 overflow-y-auto">
            <div class="grid grid-cols-8 gap-2">
                @foreach($questions as $i => $question)
                <button type="button"
                        @click="scrollToQuestion({{ $i }}); showMobileNav = false"
                        :class="{
                            'bg-emerald-500 text-white': answered.includes({{ $question->id }}) && !flagged.includes({{ $i }}),
                            'bg-amber-400 text-white': flagged.includes({{ $i }}),
                            'bg-gray-100 text-gray-500': !answered.includes({{ $question->id }}) && !flagged.includes({{ $i }})
                        }"
                        class="aspect-square rounded-lg flex items-center justify-center text-xs font-bold transition-all">
                    {{ $i + 1 }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ===== STICKY SUBMIT FOOTER ===== --}}
    <div class="fixed bottom-0 left-0 right-0 z-50 glass-footer shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-3">
                {{-- Left: Summary --}}
                <div class="flex items-center gap-4">
                    {{-- Mobile navigator toggle --}}
                    <button type="button" @click="showMobileNav = !showMobileNav"
                            class="lg:hidden flex items-center justify-center w-10 h-10 rounded-xl bg-slate-100 text-slate-600 hover:bg-slate-200 transition">
                        <i class="fas fa-th"></i>
                    </button>
                    <div class="text-sm text-gray-600">
                        <span class="font-semibold text-gray-800" x-text="answeredCount"></span><span class="text-gray-400">/{{ count($questions) }}</span>
                        <span class="text-gray-400 mx-1">soal dijawab</span>
                        <template x-if="flagged.length > 0">
                            <span class="text-amber-600 font-medium">
                                · <span x-text="flagged.length"></span> ditandai
                            </span>
                        </template>
                    </div>
                </div>

                {{-- Right: Submit --}}
                <button type="button"
                        @click="submitQuiz()"
                        class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 text-white px-6 py-2.5 rounded-xl hover:from-purple-700 hover:to-indigo-700 transition-all shadow-lg shadow-purple-200 font-semibold text-sm hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0">
                    <i class="fas fa-paper-plane"></i>
                    <span class="hidden sm:inline">Kumpulkan Jawaban</span>
                    <span class="sm:hidden">Kirim</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function quizApp() {
    return {
        answered: [
            // Pre-populate with already-answered question IDs
            @foreach($questions as $question)
                @if(isset($answerMap[$question->id]))
                    {{ $question->id }},
                @endif
            @endforeach
        ],
        flagged: [],
        showMobileNav: false,
        timerSeconds: {{ $quiz->time_limit ? $quiz->time_limit * 60 : 0 }},
        timerDisplay: '{{ $quiz->time_limit }}:00',
        timerClass: 'timer-safe',

        get answeredCount() {
            return this.answered.length;
        },

        markAnswered(questionId) {
            if (!this.answered.includes(questionId)) {
                this.answered.push(questionId);
            }
        },

        toggleFlag(index) {
            const pos = this.flagged.indexOf(index);
            if (pos === -1) {
                this.flagged.push(index);
            } else {
                this.flagged.splice(pos, 1);
            }
        },

        scrollToQuestion(index) {
            const el = document.getElementById('question-' + index);
            if (el) {
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                el.classList.add('ring-2', 'ring-purple-400');
                setTimeout(() => el.classList.remove('ring-2', 'ring-purple-400'), 1500);
            }
        },

        submitQuiz() {
            document.getElementById('quizForm').requestSubmit();
        },

        initQuiz() {
            // Option selection highlight
            document.querySelectorAll('.quiz-option').forEach(label => {
                const input = label.querySelector('input[type="radio"]');
                if (input) {
                    input.addEventListener('change', () => {
                        const name = input.getAttribute('name');
                        document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                            r.closest('.quiz-option')?.classList.remove('selected', 'border-purple-300', 'bg-purple-50/50', 'border-emerald-300', 'bg-emerald-50/50', 'border-rose-300', 'bg-rose-50/50');
                            r.closest('.quiz-option')?.classList.add('border-transparent');
                        });
                        label.classList.remove('border-transparent');
                        label.classList.add('selected', 'border-purple-300');
                    });
                }
            });

            // Text/textarea input tracking
            document.querySelectorAll('input[type="text"][name^="answers"], textarea[name^="answers"]').forEach(input => {
                if (input.value.trim()) {
                    const match = input.name.match(/answers\[(\d+)\]/);
                    if (match) this.markAnswered(parseInt(match[1]));
                }
            });
        }
    };
}

@if($quiz->time_limit)
// Enhanced Timer
(function() {
    let seconds = {{ $quiz->time_limit * 60 }};
    const timerEl = document.getElementById('timer');
    const form = document.getElementById('quizForm');

    const interval = setInterval(() => {
        seconds--;
        const m = Math.floor(seconds / 60);
        const s = seconds % 60;
        const display = m + ':' + String(s).padStart(2, '0');
        timerEl.textContent = display;

        // Update Alpine timer state
        const appEl = document.querySelector('[x-data]');
        if (appEl && appEl.__x) {
            appEl.__x.$data.timerSeconds = seconds;
            appEl.__x.$data.timerDisplay = display;

            if (seconds > 300) {
                appEl.__x.$data.timerClass = 'timer-safe';
            } else if (seconds > 120) {
                appEl.__x.$data.timerClass = 'timer-warning';
            } else {
                appEl.__x.$data.timerClass = 'timer-danger timer-pulse';
            }
        }

        if (seconds <= 0) {
            clearInterval(interval);
            form.submit();
        }
    }, 1000);
})();
@endif

// Anti-Cheat: Tab/Window Focus Loss Detector
(function() {
    let warningCount = 0;
    const maxWarnings = 3;
    const form = document.getElementById('quizForm');
    let isSubmitting = false;
    let lastWarningTime = 0;

    form.addEventListener('submit', () => {
        isSubmitting = true;
    });

    function triggerWarning() {
        if (isSubmitting) return;

        const now = Date.now();
        if (now - lastWarningTime < 1000) {
            return;
        }
        lastWarningTime = now;

        warningCount++;
        if (warningCount >= maxWarnings) {
            alert("PERINGATAN KRITIS: Anda telah keluar dari halaman ujian sebanyak " + warningCount + " kali. Ujian Anda otomatis dikumpulkan karena indikasi pelanggaran akademik.");
            isSubmitting = true;
            form.submit();
        } else {
            alert("PERINGATAN: Anda terpantau keluar dari halaman kuis (membuka tab/aplikasi lain). Ujian akan otomatis dikumpulkan jika Anda keluar " + (maxWarnings - warningCount) + " kali lagi!");
        }
    }

    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            triggerWarning();
        }
    });

    window.addEventListener('blur', () => {
        triggerWarning();
    });
})();
</script>
@endpush
