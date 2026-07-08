@extends('layouts.' . $role)

@section('title', 'Isi Survey Kepuasan')

@section('content')
@php
    $isGuru = $role === 'guru';
    $accentGradient = $isGuru ? 'linear-gradient(135deg, #059669, #0d9488)' : 'linear-gradient(135deg, #f59e0b, #f97316)';
    $btnGradient = $isGuru ? 'linear-gradient(135deg, #059669, #047857)' : 'linear-gradient(135deg, #f59e0b, #d97706)';
    $ringColor = $isGuru ? 'focus:ring-emerald-400/30 focus:border-emerald-400' : 'focus:ring-amber-400/30 focus:border-amber-400';
    $checkedBg = $isGuru ? 'peer-checked:bg-emerald-600 peer-checked:border-emerald-600 peer-checked:shadow-emerald-100 hover:border-emerald-400 hover:text-emerald-600' : 'peer-checked:bg-amber-500 peer-checked:border-amber-500 peer-checked:shadow-amber-100 hover:border-amber-400 hover:text-amber-600';
    $accentText = $isGuru ? 'text-emerald-700' : 'text-amber-700';
    $accentBg = $isGuru ? 'bg-emerald-50' : 'bg-amber-50';
    $accentBorder = $isGuru ? 'border-emerald-200' : 'border-amber-200';
    $qCount = count($questions);
@endphp

<style>
    .survey-banner { background: {{ $accentGradient }}; }
    .question-card { transition: all 0.2s ease; animation: fadeInUp 0.3s ease both; }
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .question-card:nth-child(1) { animation-delay: 0.05s; }
    .question-card:nth-child(2) { animation-delay: 0.1s; }
    .question-card:nth-child(3) { animation-delay: 0.15s; }
    .question-card:nth-child(4) { animation-delay: 0.2s; }
    .question-card:nth-child(5) { animation-delay: 0.25s; }
    .rating-label { transition: all 0.18s cubic-bezier(0.4, 0, 0.2, 1); }
    .rating-label:hover { transform: scale(1.1); }
    input[type="radio"]:checked + .rating-label { transform: scale(1.05); }
    .form-input-focus:focus { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .teacher-card { transition: all 0.2s ease; }
    .teacher-card:hover { transform: translateY(-1px); }
    .progress-pill { transition: all 0.3s ease; }
</style>

<div class="space-y-6 max-w-6xl mx-auto">
    {{-- BACK HEADER --}}
    <div class="flex items-center justify-between">
        <div>
            <nav class="flex items-center gap-1.5 text-xs text-gray-400 mb-1">
                <a href="{{ route($role . '.surveys.index') }}" class="hover:text-indigo-600 transition font-semibold">Survei</a>
                <i class="fas fa-chevron-right text-[8px]"></i>
                <span class="text-gray-600 font-bold">Isi Kuesioner</span>
            </nav>
            <h1 class="text-xl font-extrabold text-gray-800">Isi Survei Kepuasan</h1>
        </div>
        <a href="{{ route($role . '.surveys.index') }}" class="inline-flex items-center gap-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-700 px-4 py-2.5 rounded-xl font-bold text-sm transition shadow-sm hover:shadow">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Kembali</span>
        </a>
    </div>

    {{-- ERROR ALERT --}}
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 rounded-2xl p-5 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center text-red-600 flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                </div>
                <div>
                    <p class="font-extrabold text-red-800 text-sm mb-2">Mohon lengkapi seluruh pertanyaan wajib:</p>
                    <ul class="list-none space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="flex items-center gap-2 text-xs text-red-700 font-medium">
                                <i class="fas fa-dot-circle text-[8px] text-red-400"></i>{{ $error }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- SURVEY MAIN SHEET --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-lg overflow-hidden">

        {{-- SURVEY BANNER --}}
        <div class="survey-banner p-8 md:p-10 text-white relative overflow-hidden">
            <div class="absolute inset-0 opacity-10" style="background-image: linear-gradient(to right, rgba(255,255,255,0.1) 1px, transparent 1px), linear-gradient(to bottom, rgba(255,255,255,0.1) 1px, transparent 1px); background-size: 24px 24px;"></div>
            <div class="absolute top-0 right-0 w-40 h-40 rounded-full opacity-10" style="background: radial-gradient(circle, white, transparent); transform: translate(25%, -25%);"></div>
            <div class="relative">
                <div class="flex items-start justify-between gap-4">
                    <div class="space-y-2 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 border border-white/25 rounded-full text-[10px] font-extrabold uppercase tracking-wider backdrop-blur-sm">
                                <i class="fas fa-poll-h text-[9px]"></i> Kuesioner Evaluasi
                            </span>
                            <span id="total-badge" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white/15 border border-white/20 rounded-full text-[10px] font-extrabold uppercase tracking-wider">
                                {{ $qCount }} Pertanyaan
                            </span>
                        </div>
                        <h2 class="font-extrabold text-2xl md:text-3xl leading-tight">{{ $survey->title }}</h2>
                        @if($survey->description)
                            <p class="text-white/85 text-sm leading-relaxed max-w-3xl">{{ $survey->description }}</p>
                        @endif
                    </div>
                    <div class="hidden md:flex w-16 h-16 rounded-2xl items-center justify-center border border-white/20 text-white/90 text-3xl flex-shrink-0" style="background: rgba(255,255,255,0.12);">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                </div>

                {{-- Progress Bar --}}
                <div class="mt-6">
                    <div class="flex items-center justify-between text-[11px] text-white/80 font-bold mb-2">
                        <span>Progres Pengisian</span>
                        <span id="progress-text">0 / {{ $qCount }} Dijawab</span>
                    </div>
                    <div class="w-full bg-white/20 h-2 rounded-full overflow-hidden">
                        <div id="progress-bar" class="bg-white h-full rounded-full transition-all duration-500" style="width: 0%;"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM --}}
        <form action="{{ route($role . '.surveys.submit', $survey->id) }}" method="POST" class="p-6 md:p-8 space-y-6" id="survey-form">
            @csrf

            {{-- Teacher Type Selector (Guru only) --}}
            @if($role === 'guru')
            <div class="{{ $accentBg }} rounded-2xl border {{ $accentBorder }} p-6 space-y-4">
                <div>
                    <h3 class="font-extrabold text-gray-800 text-base flex items-center gap-2">
                        <i class="fas fa-chalkboard-teacher {{ $accentText }} text-sm"></i>
                        Tipe Guru <span class="text-red-500 text-sm">*</span>
                    </h3>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Pilih tipe guru Anda untuk menampilkan pertanyaan kuesioner yang relevan.</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <label class="teacher-card flex items-start gap-4 cursor-pointer border-2 border-gray-200 bg-white rounded-2xl p-5 hover:border-emerald-300 hover:bg-emerald-50/30 transition-all duration-200 select-none" id="label-teacher-kejuruan">
                        <input type="radio" name="teacher_type" value="kejuruan" required
                               class="w-5 h-5 mt-0.5 text-emerald-600 focus:ring-emerald-500 border-gray-300 flex-shrink-0"
                               onclick="toggleTeacherQuestions('kejuruan')"
                               {{ old('teacher_type') === 'kejuruan' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-extrabold text-gray-800">Guru Kejuruan (Produktif)</span>
                            <span class="block text-xs text-gray-500 mt-1 leading-relaxed">Mengajar mata pelajaran keahlian produktif/kejuruan SMK (TEFA, Unit Produksi, dll.).</span>
                        </div>
                    </label>
                    <label class="teacher-card flex items-start gap-4 cursor-pointer border-2 border-gray-200 bg-white rounded-2xl p-5 hover:border-emerald-300 hover:bg-emerald-50/30 transition-all duration-200 select-none" id="label-teacher-umum">
                        <input type="radio" name="teacher_type" value="umum" required
                               class="w-5 h-5 mt-0.5 text-emerald-600 focus:ring-emerald-500 border-gray-300 flex-shrink-0"
                               onclick="toggleTeacherQuestions('umum')"
                               {{ old('teacher_type') === 'umum' ? 'checked' : '' }}>
                        <div>
                            <span class="block text-sm font-extrabold text-gray-800">Guru Umum (Non-Kejuruan)</span>
                            <span class="block text-xs text-gray-500 mt-1 leading-relaxed">Mengajar mata pelajaran umum (Normatif/Adaptif) di SMK.</span>
                        </div>
                    </label>
                </div>
            </div>
            @endif

            {{-- Questions --}}
            <div class="space-y-4">
                @foreach($questions as $index => $q)
                    <div class="question-card bg-gray-50/60 border border-gray-100 rounded-2xl p-6 space-y-4 hover:bg-white hover:border-gray-200 hover:shadow-sm transition-all duration-200"
                         data-target-guru="{{ $q->target_guru ?? '' }}"
                         id="q-container-{{ $q->id }}"
                         style="animation-delay: {{ $index * 0.05 }}s">
                        {{-- Question Header --}}
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-xl flex items-center justify-center font-extrabold text-xs flex-shrink-0 text-white shadow-sm" style="background: {{ $accentGradient }};">
                                <span class="question-number">{{ $index + 1 }}</span>
                            </div>
                            <div class="flex-1 pt-0.5">
                                <h4 class="font-extrabold text-gray-800 text-sm leading-relaxed">
                                    {{ $q->question_text }}
                                    @if($q->type === 'scale')
                                        <span class="text-red-500 ml-1">*</span>
                                    @endif
                                </h4>
                                @if($q->type === 'scale')
                                    @php
                                        $typeLabel = ['likert_5' => 'Skala Likert 5', 'likert_4' => 'Skala Likert 4', 'competence_5' => 'Skala Kompetensi', 'yes_no' => 'Ya / Tidak'];
                                        $tLabel = $typeLabel[$q->scale_type ?? 'likert_5'] ?? 'Skala Penilaian';
                                    @endphp
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-teal-50 text-teal-700 border border-teal-200 rounded text-[9px] font-extrabold uppercase">
                                        <i class="fas fa-star text-[7px]"></i>{{ $tLabel }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-blue-50 text-blue-700 border border-blue-200 rounded text-[9px] font-extrabold uppercase">
                                        <i class="fas fa-align-left text-[7px]"></i>Jawaban Terbuka
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Answer Input --}}
                        <div class="pl-12">
                            @if($q->type === 'scale')
                                @php $scaleType = $q->scale_type ?? 'likert_5'; @endphp

                                @if($scaleType === 'yes_no')
                                    <div class="flex items-center gap-3">
                                        <label class="flex items-center gap-3 cursor-pointer border-2 border-gray-200 bg-white rounded-2xl px-6 py-3.5 hover:border-emerald-300 hover:bg-emerald-50/30 transition-all select-none" style="min-width: 120px;">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="1" required
                                                   class="w-5 h-5 text-emerald-600 focus:ring-emerald-500 border-gray-300"
                                                   {{ old('answers.' . $q->id) == '1' ? 'checked' : '' }}
                                                   onchange="updateProgress()">
                                            <span class="text-sm font-extrabold text-gray-700 flex items-center gap-2">
                                                <i class="fas fa-check text-emerald-500 text-xs"></i> Ya
                                            </span>
                                        </label>
                                        <label class="flex items-center gap-3 cursor-pointer border-2 border-gray-200 bg-white rounded-2xl px-6 py-3.5 hover:border-red-300 hover:bg-red-50/30 transition-all select-none" style="min-width: 120px;">
                                            <input type="radio" name="answers[{{ $q->id }}]" value="0" required
                                                   class="w-5 h-5 text-red-600 focus:ring-red-500 border-gray-300"
                                                   {{ old('answers.' . $q->id) == '0' ? 'checked' : '' }}
                                                   onchange="updateProgress()">
                                            <span class="text-sm font-extrabold text-gray-700 flex items-center gap-2">
                                                <i class="fas fa-times text-red-500 text-xs"></i> Tidak
                                            </span>
                                        </label>
                                    </div>
                                @else
                                    @php
                                        $max = $scaleType === 'likert_4' ? 4 : 5;
                                        $leftLabel = $scaleType === 'competence_5' ? '1 = Sangat Kurang' : '1 = Sangat Tidak Setuju';
                                        $rightLabel = $scaleType === 'competence_5' ? "$max = Sangat Menguasai" : "$max = Sangat Setuju";
                                    @endphp
                                    <div class="space-y-3">
                                        <div class="flex items-center gap-3">
                                            @for($rating = 1; $rating <= $max; $rating++)
                                                <div class="relative">
                                                    <input type="radio" id="q-{{ $q->id }}-r-{{ $rating }}"
                                                           name="answers[{{ $q->id }}]" value="{{ $rating }}" required
                                                           class="peer absolute opacity-0 w-0 h-0"
                                                           {{ old('answers.' . $q->id) == $rating ? 'checked' : '' }}
                                                           onchange="updateProgress()">
                                                    <label for="q-{{ $q->id }}-r-{{ $rating }}"
                                                           class="rating-label w-12 h-12 rounded-2xl border-2 border-gray-200 bg-white flex items-center justify-center font-extrabold text-gray-500 text-sm cursor-pointer select-none peer-checked:text-white peer-checked:shadow-lg {{ $checkedBg }}">
                                                        {{ $rating }}
                                                    </label>
                                                </div>
                                            @endfor
                                        </div>
                                        <div class="flex items-center justify-between max-w-[240px] text-[10px] text-gray-400 font-bold px-1">
                                            <span>{{ $leftLabel }}</span>
                                            <span>{{ $rightLabel }}</span>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <textarea name="answers[{{ $q->id }}]" rows="4"
                                          placeholder="Tulis masukan, tanggapan, atau kritik dan saran Anda secara detail di sini..."
                                          class="form-input-focus w-full px-4 py-3.5 border-2 border-gray-200 bg-white rounded-2xl focus:outline-none {{ $ringColor }} transition text-sm leading-relaxed text-gray-700 placeholder-gray-300 font-medium resize-none"
                                          oninput="updateProgress()">{{ old('answers.' . $q->id) }}</textarea>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- SUBMIT ACTION --}}
            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-xs text-gray-400 font-semibold">
                    <i class="fas fa-shield-alt text-gray-300"></i>
                    <span><span class="text-red-400 font-bold">*</span> Wajib diisi · Jawaban Anda dijaga kerahasiaannya</span>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route($role . '.surveys.index') }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl text-sm transition">
                        Batal
                    </a>
                    <button type="submit" id="submit-btn"
                            class="inline-flex items-center gap-2.5 px-8 py-3 text-white font-extrabold rounded-2xl text-sm shadow-lg transition-all duration-200 hover:shadow-xl hover:-translate-y-0.5 active:translate-y-0"
                            style="background: {{ $btnGradient }};">
                        <i class="fas fa-paper-plane text-sm"></i>
                        <span>Kirim Jawaban Survei</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function updateProgress() {
    const form = document.getElementById('survey-form');
    const questionContainers = form.querySelectorAll('.question-card:not([style*="display: none"])');
    let answered = 0;
    let total = 0;

    questionContainers.forEach(container => {
        const radios = container.querySelectorAll('input[type="radio"]:not(:disabled)');
        const textarea = container.querySelector('textarea:not(:disabled)');
        const scaleInputs = container.querySelectorAll('input[type="radio"][name^="answers"]:not(:disabled)');

        if (scaleInputs.length > 0) {
            total++;
            const checked = container.querySelector('input[type="radio"][name^="answers"]:checked');
            if (checked) answered++;
        } else if (textarea) {
            total++;
            if (textarea.value.trim()) answered++;
        }
    });

    const pct = total > 0 ? Math.round((answered / total) * 100) : 0;
    const bar = document.getElementById('progress-bar');
    const txt = document.getElementById('progress-text');
    const badge = document.getElementById('total-badge');
    if (bar) bar.style.width = pct + '%';
    if (txt) txt.textContent = answered + ' / ' + total + ' Dijawab';
    if (badge) badge.innerHTML = total + ' Pertanyaan';
}

@if($role === 'guru')
function toggleTeacherQuestions(type) {
    const questions = document.querySelectorAll('.question-item');
    let visibleIndex = 1;

    const labelKejuruan = document.getElementById('label-teacher-kejuruan');
    const labelUmum = document.getElementById('label-teacher-umum');

    const activeClass = ['border-emerald-500', 'bg-emerald-50/40'];
    const inactiveClass = ['border-gray-200', 'bg-white'];

    if (type === 'kejuruan') {
        labelKejuruan?.classList.add(...activeClass);
        labelKejuruan?.classList.remove(...inactiveClass);
        labelUmum?.classList.remove(...activeClass);
        labelUmum?.classList.add(...inactiveClass);
    } else {
        labelUmum?.classList.add(...activeClass);
        labelUmum?.classList.remove(...inactiveClass);
        labelKejuruan?.classList.remove(...activeClass);
        labelKejuruan?.classList.add(...inactiveClass);
    }

    questions.forEach(q => {
        const target = q.getAttribute('data-target-guru');
        const radios = q.querySelectorAll('input[type="radio"]');
        const textarea = q.querySelector('textarea');

        if (target === '' || target === type) {
            q.style.display = 'block';
            q.classList.remove('hidden');
            radios.forEach(r => { r.disabled = false; r.required = true; });
            if (textarea) textarea.disabled = false;
            const numSpan = q.querySelector('.question-number');
            if (numSpan) numSpan.textContent = visibleIndex++;
        } else {
            q.style.display = 'none';
            q.classList.add('hidden');
            radios.forEach(r => { r.disabled = true; r.required = false; r.checked = false; });
            if (textarea) { textarea.disabled = true; textarea.value = ''; }
        }
    });

    updateProgress();
}

document.addEventListener('DOMContentLoaded', function() {
    const checkedInput = document.querySelector('input[name="teacher_type"]:checked');
    if (checkedInput) {
        toggleTeacherQuestions(checkedInput.value);
    } else {
        document.querySelectorAll('.question-item').forEach(q => {
            const target = q.getAttribute('data-target-guru');
            if (target !== '') {
                q.style.display = 'none';
                q.classList.add('hidden');
                q.querySelectorAll('input[type="radio"]').forEach(r => {
                    r.disabled = true; r.required = false;
                });
            }
        });
    }
    updateProgress();
});
@else
document.addEventListener('DOMContentLoaded', updateProgress);
@endif

// Rename question-card to question-item for toggle compatibility
document.querySelectorAll('.question-card').forEach(el => el.classList.add('question-item'));
</script>
@endsection
