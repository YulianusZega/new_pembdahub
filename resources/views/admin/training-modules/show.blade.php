@extends('layouts.admin')

@section('title', 'Preview Materi: ' . $module->title)

@section('content')
@php
    $categoryGradients = [
        'panduan_umum'    => 'from-blue-500 to-indigo-600',
        'fitur_admin'     => 'from-indigo-500 to-purple-600',
        'fitur_guru'      => 'from-emerald-500 to-teal-600',
        'fitur_siswa'     => 'from-amber-500 to-orange-500',
        'fitur_orangtua'  => 'from-cyan-500 to-blue-600',
        'fitur_keuangan'  => 'from-green-500 to-emerald-600',
        'fitur_yayasan'   => 'from-violet-500 to-fuchsia-600',
    ];
    $categoryIcons = [
        'panduan_umum'    => 'fas fa-book-open',
        'fitur_admin'     => 'fas fa-user-shield',
        'fitur_guru'      => 'fas fa-chalkboard-teacher',
        'fitur_siswa'     => 'fas fa-user-graduate',
        'fitur_orangtua'  => 'fas fa-people-roof',
        'fitur_keuangan'  => 'fas fa-coins',
        'fitur_yayasan'   => 'fas fa-landmark',
    ];

    $gradient = $categoryGradients[$module->category] ?? 'from-gray-500 to-gray-700';
    $icon     = $categoryIcons[$module->category] ?? 'fas fa-file-alt';
@endphp

<style>
    /* Premium Transitions & Wizard styling */
    .step-timeline-item.active .timeline-circle {
        @apply ring-4 ring-indigo-100 bg-indigo-600 text-white scale-110;
        box-shadow: 0 0 15px rgba(79, 70, 229, 0.4);
    }
    .step-timeline-item.completed .timeline-circle {
        @apply bg-emerald-500 text-white;
    }
    .step-card {
        transition: opacity 0.3s ease, transform 0.3s ease;
    }
    .step-card.fade-out {
        opacity: 0;
        transform: translateY(10px);
    }
    .step-card.fade-in {
        opacity: 1;
        transform: translateY(0);
    }
    /* Custom style for parsed markdown in prose */
    .prose h1, .prose h2, .prose h3 {
        color: #1f2937;
        font-weight: 700;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .prose h2 {
        font-size: 1.5rem;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 0.5rem;
    }
    .prose h3 {
        font-size: 1.25rem;
    }
    .prose p {
        margin-bottom: 1rem;
        line-height: 1.7;
    }
    .prose ul, .prose ol {
        margin-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .prose ul {
        list-style-type: disc;
    }
    .prose ol {
        list-style-type: decimal;
    }
    .prose li {
        margin-bottom: 0.5rem;
    }
</style>

<div class="space-y-6">
    <!-- Header with Back navigation -->
    <div class="mb-8 flex items-center justify-between flex-wrap gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.training-modules.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                    <i class="fas fa-eye text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Preview & Preview Pengguna</h1>
                    <p class="text-gray-600 mt-1">Lihat presentasi materi interaktif seperti yang akan dilihat oleh pengguna</p>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.training-modules.edit', $module) }}"
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-600 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition-all">
                <i class="fas fa-pen"></i> Edit Materi
            </a>
            <a href="{{ route('admin.training-modules.index') }}"
                class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-200 transition">
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- ═══════════════════ HEADER SECTION ═══════════════════ -->
    <div class="relative overflow-hidden bg-gradient-to-br {{ $gradient }} rounded-3xl p-8 text-white shadow-xl">
        <div class="absolute inset-0 opacity-15">
            <div class="absolute -top-10 -right-10 w-72 h-72 bg-white rounded-full blur-2xl"></div>
            <div class="absolute -bottom-10 -left-10 w-56 h-56 bg-white rounded-full blur-xl"></div>
        </div>
        <div class="relative flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-start gap-5">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-2xl flex items-center justify-center flex-shrink-0 shadow-inner">
                    <i class="{{ $icon }} text-3xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/20 backdrop-blur-sm text-white">
                            <i class="{{ $icon }} mr-1.5 text-[10px]"></i> {{ $module->category_label }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $module->is_published ? 'bg-green-500/30 text-green-100' : 'bg-gray-500/30 text-gray-200' }} border border-white/10">
                            <i class="fas fa-circle mr-1.5 text-[8px] {{ $module->is_published ? 'text-green-400' : 'text-gray-400' }}"></i> {{ $module->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight mb-2">{{ $module->title }}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-white/80">
                        <span class="flex items-center gap-1.5"><i class="fas fa-calendar-alt"></i> {{ $module->created_at->format('d M Y') }}</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-clock"></i> {{ $module->reading_time ?? 15 }} Menit</span>
                        <span class="flex items-center gap-1.5"><i class="fas fa-layer-group"></i> Tingkat: {{ $module->difficulty ?? 'Pemula' }}</span>
                        @if($module->author)
                        <span class="flex items-center gap-1.5"><i class="fas fa-user-edit"></i> Dibuat oleh: {{ $module->author->name }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════ RAW SOURCE CONTENT (HIDDEN) ═══════════════════ -->
    <div id="raw-markdown-content" style="display:none;">
        {!! Illuminate\Support\Str::markdown($module->content ?? '') !!}
    </div>

    <!-- ═══════════════════ INTERACTIVE WIZARD LAYOUT ═══════════════════ -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-8">
        
        <!-- LEFT SIDEBAR: STICKY NAVIGATION -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Progress Tracker Card -->
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 p-6 sticky top-6">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center justify-between">
                    <span>Daftar Langkah</span>
                    <span id="wizard-progress-percent" class="text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-md text-[10px]">0% Selesai</span>
                </h3>
                
                <!-- Radial / Bar Progress -->
                <div class="w-full bg-gray-100 rounded-full h-2 mb-6 overflow-hidden">
                    <div id="wizard-progress-bar" class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-full rounded-full transition-all duration-500" style="width: 0%"></div>
                </div>

                <!-- Steps list (Timeline) -->
                <div id="wizard-steps-timeline" class="space-y-1 relative">
                    <!-- Will be dynamically generated by Javascript -->
                </div>
            </div>
        </div>

        <!-- RIGHT CONTENT AREA: STEP CONTENT -->
        <div class="lg:col-span-8 space-y-6">
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden flex flex-col min-h-[500px]">
                <!-- Top indicator bar -->
                <div class="px-8 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider" id="active-step-badge">Langkah 1 dari 5</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-indigo-500 animate-pulse"></span>
                        <span class="text-[11px] font-semibold text-indigo-500">Materi Interaktif</span>
                    </div>
                </div>

                <!-- Body prose -->
                <div class="p-8 flex-1">
                    <div id="active-step-card-body" class="prose max-w-none step-card fade-in">
                        <!-- Loaded dynamically via JS -->
                    </div>
                </div>

                <!-- Navigation buttons -->
                <div class="px-8 py-5 bg-gray-50 border-t border-gray-100 flex items-center justify-between rounded-b-3xl">
                    <button id="btn-wizard-prev" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 hover:shadow-sm active:scale-95 transition-all disabled:opacity-50 disabled:pointer-events-none">
                        <i class="fas fa-chevron-left text-xs"></i> Kembali
                    </button>
                    <button id="btn-wizard-next" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gradient-to-r {{ $gradient }} text-white rounded-xl text-sm font-semibold hover:shadow-lg active:scale-95 transition-all">
                        <span>Lanjut</span> <i class="fas fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- ═══════════════════ PDF SECTION (IF AVAILABLE) ═══════════════════ -->
            @if($module->pdf_file)
            <div class="bg-white rounded-3xl shadow-lg border border-gray-100 overflow-hidden transition-all duration-300">
                <!-- PDF Header -->
                <div class="px-8 py-5 border-b border-gray-100 flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                            <i class="fas fa-file-pdf text-rose-500 text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800">Dokumen PDF</h3>
                            <p class="text-xs text-gray-500">Unduh berkas materi panduan lengkap</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.training-modules.download', $module) }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-cyan-500 text-white rounded-xl text-sm font-semibold hover:shadow-lg transition-all">
                        <i class="fas fa-download"></i> Unduh PDF
                    </a>
                </div>
                <!-- PDF Viewer -->
                <div class="p-6 bg-gray-50">
                    <iframe src="{{ $module->pdf_url }}" class="w-full rounded-2xl border border-gray-200 shadow-sm" style="height:600px" title="PDF Viewer"></iframe>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- ═══════════════════ JAVASCRIPT LOGIC ═══════════════════ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rawContent = document.getElementById('raw-markdown-content');
    const timelineContainer = document.getElementById('wizard-steps-timeline');
    const cardBody = document.getElementById('active-step-card-body');
    const badgeStep = document.getElementById('active-step-badge');
    
    const btnPrev = document.getElementById('btn-wizard-prev');
    const btnNext = document.getElementById('btn-wizard-next');
    
    const progressBar = document.getElementById('wizard-progress-bar');
    const progressPercent = document.getElementById('wizard-progress-percent');
    
    if (!rawContent || !timelineContainer || !cardBody) return;

    // Parse steps from rawContent elements
    const steps = [];
    let currentStep = {
        title: 'Pengenalan & Pengantar',
        html: ''
    };

    // Parse HTML child elements to group them by H2 tags
    const children = Array.from(rawContent.children);
    children.forEach(el => {
        if (el.tagName === 'H2') {
            // Push old step if has content
            if (currentStep.html.trim() !== '' || currentStep.title !== 'Pengenalan & Pengantar') {
                steps.push(currentStep);
            }
            // Start new step
            currentStep = {
                title: el.textContent.replace(/^\d+\.\s*/, ''), // strip numberPrefixes
                html: el.outerHTML
            };
        } else {
            // Append elements
            currentStep.html += el.outerHTML;
        }
    });
    // Push final step
    if (currentStep.html.trim() !== '') {
        steps.push(currentStep);
    }

    // Add a final "Checklist & Selesai" Step dynamically
    const moduleId = "{{ $module->id }}";
    
    // Generate Checklist items based on H2s
    const checklistItems = steps.map((s, idx) => {
        return {
            id: `task_${moduleId}_${idx}`,
            label: `Paham mengenai: ${s.title}`
        };
    });
    
    let checklistHtml = `
        <div class="text-center py-6">
            <div class="w-16 h-16 mx-auto rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-3xl mb-4 animate-bounce">
                <i class="fas fa-trophy"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800">Selamat! Anda Telah Membaca Materi</h2>
            <p class="text-gray-500 text-sm mt-1">Konfirmasi pemahaman Anda dengan mencentang evaluasi mandiri di bawah ini:</p>
        </div>
        <div class="mt-6 border border-gray-100 bg-gray-50/70 rounded-2xl p-6 space-y-4">
            <h3 class="text-sm font-bold text-gray-700 flex items-center gap-2 mb-2">
                <i class="fas fa-clipboard-check text-indigo-500"></i> Lembar Verifikasi Mandiri
            </h3>
    `;
    
    checklistItems.forEach(item => {
        checklistHtml += `
            <label class="flex items-start gap-3 p-3 bg-white border border-gray-100 rounded-xl cursor-pointer hover:border-indigo-300 transition-all select-none">
                <input type="checkbox" id="${item.id}" class="mt-1 w-4.5 h-4.5 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer checklist-check-item">
                <span class="text-sm font-medium text-gray-700 leading-tight">${item.label}</span>
            </label>
        `;
    });
    
    checklistHtml += `
        </div>
        <div class="mt-6 text-center">
            <p class="text-xs text-gray-400">Centang checklist di atas untuk menandai pelatihan ini selesai di browser Anda.</p>
        </div>
    `;

    steps.push({
        title: 'Evaluasi & Selesai',
        html: checklistHtml
    });

    let activeStepIdx = 0;

    // Render Timeline steps list on the left
    function renderTimeline() {
        timelineContainer.innerHTML = '';
        steps.forEach((step, idx) => {
            const isCompleted = idx < activeStepIdx;
            const isActive = idx === activeStepIdx;
            
            const timelineItem = document.createElement('button');
            timelineItem.className = `w-full text-left flex items-center gap-3 p-3 rounded-2xl transition-all duration-200 step-timeline-item ${isActive ? 'active bg-indigo-50/50 font-semibold' : 'text-gray-500 hover:bg-gray-50'}`;
            timelineItem.onclick = () => jumpToStep(idx);
            
            let iconCode = `<span class="timeline-circle w-6 h-6 rounded-full bg-gray-100 text-[10px] font-bold flex items-center justify-center text-gray-500 flex-shrink-0 transition-all">${idx + 1}</span>`;
            if (isCompleted) {
                iconCode = `<span class="timeline-circle w-6 h-6 rounded-full bg-emerald-500 text-[10px] text-white flex items-center justify-center flex-shrink-0 transition-all"><i class="fas fa-check"></i></span>`;
            }
            
            timelineItem.innerHTML = `
                ${iconCode}
                <span class="text-xs truncate flex-1 leading-tight">${step.title}</span>
            `;
            timelineContainer.appendChild(timelineItem);
        });
    }

    // Load active step content with animation
    function loadStep(idx) {
        // Fade out
        cardBody.classList.remove('fade-in');
        cardBody.classList.add('fade-out');

        setTimeout(() => {
            activeStepIdx = idx;
            cardBody.innerHTML = steps[idx].html;
            badgeStep.textContent = `Langkah ${idx + 1} dari ${steps.length}`;
            
            // Adjust buttons
            btnPrev.disabled = idx === 0;
            if (idx === steps.length - 1) {
                btnNext.innerHTML = `<span>Selesai</span> <i class="fas fa-check text-xs"></i>`;
            } else {
                btnNext.innerHTML = `<span>Lanjut</span> <i class="fas fa-chevron-right text-xs"></i>`;
            }

            // Calculate progress bar
            const percent = Math.round((idx / (steps.length - 1)) * 100);
            progressBar.style.width = `${percent}%`;
            progressPercent.textContent = `${percent}% Selesai`;

            renderTimeline();
            
            // Bind checklist events if on final step
            if (idx === steps.length - 1) {
                bindChecklistEvents();
            }

            // Fade in
            cardBody.classList.remove('fade-out');
            cardBody.classList.add('fade-in');
        }, 150);
    }

    function jumpToStep(idx) {
        if (idx >= 0 && idx < steps.length) {
            loadStep(idx);
        }
    }

    function bindChecklistEvents() {
        const checkboxes = document.querySelectorAll('.checklist-check-item');
        checkboxes.forEach(chk => {
            // Load state
            const isChecked = localStorage.getItem(chk.id) === 'true';
            chk.checked = isChecked;
            if (isChecked) {
                chk.closest('label').classList.add('border-emerald-500', 'bg-emerald-50/20');
            }

            // Save state on change
            chk.onchange = (e) => {
                const label = chk.closest('label');
                if (e.target.checked) {
                    localStorage.setItem(chk.id, 'true');
                    label.classList.add('border-emerald-500', 'bg-emerald-50/20');
                    // Sparkle confetti effect on check
                    createSparkle(e.target);
                } else {
                    localStorage.setItem(chk.id, 'false');
                    label.classList.remove('border-emerald-500', 'bg-emerald-50/20');
                }
            };
        });
    }

    // Sparkle confetti on check
    function createSparkle(target) {
        const rect = target.getBoundingClientRect();
        for (let i = 0; i < 6; i++) {
            const sparkle = document.createElement('div');
            sparkle.style.position = 'fixed';
            sparkle.style.width = '6px';
            sparkle.style.height = '6px';
            sparkle.style.borderRadius = '50%';
            sparkle.style.backgroundColor = ['#10b981', '#3b82f6', '#f59e0b', '#ec4899'][Math.floor(Math.random() * 4)];
            sparkle.style.left = `${rect.left + rect.width/2}px`;
            sparkle.style.top = `${rect.top + rect.height/2}px`;
            sparkle.style.zIndex = '9999';
            sparkle.style.pointerEvents = 'none';
            document.body.appendChild(sparkle);

            const angle = Math.random() * Math.PI * 2;
            const velocity = 2 + Math.random() * 3;
            let posX = 0;
            let posY = 0;
            let opacity = 1;

            const anim = setInterval(() => {
                posX += Math.cos(angle) * velocity;
                posY += Math.sin(angle) * velocity + 0.2; // simulate gravity
                opacity -= 0.05;
                sparkle.style.transform = `translate(${posX}px, ${posY}px)`;
                sparkle.style.opacity = opacity;

                if (opacity <= 0) {
                    clearInterval(anim);
                    sparkle.remove();
                }
            }, 20);
        }
    }

    btnNext.onclick = () => {
        if (activeStepIdx < steps.length - 1) {
            loadStep(activeStepIdx + 1);
        } else {
            // Trigger finish event
            window.location.href = "{{ route('admin.training-modules.index') }}";
        }
    };

    btnPrev.onclick = () => {
        if (activeStepIdx > 0) {
            loadStep(activeStepIdx - 1);
        }
    };

    // Initial load
    loadStep(0);
});
</script>
@endsection
