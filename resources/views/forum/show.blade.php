@extends(auth()->user()->layout)

@section('title', $thread->title)

@section('content')
<!-- Dynamic Google Fonts & Phosphor Icons -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;650;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>
<!-- Tailwind CDN without Preflight (To fix missing dark mode colors without breaking global layout) -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    corePlugins: {
      preflight: false,
    }
  }
</script>
<!-- MathJax for math equations -->
<script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<style>
    .forum-hdr { font-family: 'Space Grotesk', sans-serif; }
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    /* Compose bar safe area for iOS */
    .pb-safe { padding-bottom: env(safe-area-inset-bottom); }
</style>

<!-- App Window Wrapper -->
<div class="w-full bg-[#0f0f14] text-[#f8fafc] font-['Inter'] rounded-3xl shadow-2xl border border-white/10 mx-auto flex flex-col pt-4 pb-32 px-4 sm:px-6 relative" style="min-height: 85vh;" x-data="forumChat()">
    
    <!-- Top Nav Bar -->
    <div class="flex items-center justify-between bg-[#16161f]/80 backdrop-blur-xl p-4 rounded-2xl border border-white/5 mb-6 sticky top-4 z-40 shadow-2xl shadow-black/20">
        <div class="flex items-center gap-4">
            <a href="{{ route('forum.index') }}" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-300 hover:text-white transition">
                <i class="ph-bold ph-arrow-left text-xl"></i>
            </a>
            <div>
                <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">{{ $thread->category_label }}</div>
                <h1 class="forum-hdr text-base sm:text-lg font-bold text-white line-clamp-1">{{ $thread->title }}</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            @if(auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru())
                <a href="{{ route('forum.edit', $thread) }}" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center text-amber-400 transition" title="Edit">
                    <i class="ph-bold ph-pencil-simple"></i>
                </a>
                <form action="{{ route('forum.destroy', $thread) }}" method="POST" onsubmit="return confirm('Yakin hapus postingan ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-rose-500/20 hover:text-rose-400 flex items-center justify-center text-slate-400 transition" title="Hapus">
                        <i class="ph-bold ph-trash"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center gap-3">
        <i class="ph-bold ph-check-circle text-xl"></i> <span>{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="mb-6 p-4 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-400 flex items-center gap-3">
        <i class="ph-bold ph-x-circle text-xl"></i> <span>{{ session('error') }}</span>
    </div>
    @endif

    <!-- CHAT AREA -->
    <div class="space-y-6 flex-1 flex flex-col">
        
        <!-- ORIGINAL POST (First Message) -->
        <div class="flex gap-4">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($thread->user->name) }}&size=48&background=random" 
                 class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-white/10 flex-shrink-0">
            <div class="flex-1 min-w-0 space-y-2">
                <!-- Meta -->
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-white text-sm sm:text-base">{{ $thread->user->name }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-slate-300 font-bold uppercase tracking-wider">{{ $thread->user->role }}</span>
                    <span class="text-xs text-slate-500">{{ $thread->created_at->format('H:i • d M Y') }}</span>
                </div>
                
                <!-- Bubble -->
                <div class="bg-gradient-to-br from-[#1c1c28] to-[#16161f] border border-white/10 rounded-2xl rounded-tl-none p-5 sm:p-6 shadow-xl w-full max-w-3xl">
                    <h2 class="forum-hdr text-xl sm:text-2xl font-bold text-white mb-4">{{ $thread->title }}</h2>
                    <div class="prose prose-invert prose-sm sm:prose-base max-w-none text-slate-300">
                        {!! nl2br(e($thread->content)) !!}
                    </div>

                    @if($thread->image_path)
                        <div class="mt-4 rounded-xl overflow-hidden border border-white/10 max-w-lg">
                            <img src="{{ asset('storage/' . $thread->image_path) }}" class="w-full h-auto">
                        </div>
                    @endif

                    @if($thread->attachment_path)
                        <a href="{{ asset('storage/' . $thread->attachment_path) }}" download class="mt-4 flex items-center gap-3 p-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-xl transition max-w-sm">
                            <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                <i class="ph-bold ph-file-arrow-down text-xl"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-slate-200 truncate">{{ $thread->attachment_name ?? 'Download Lampiran' }}</div>
                                <div class="text-[10px] text-slate-500">Klik untuk mengunduh</div>
                            </div>
                        </a>
                    @endif
                    
                    @if($perfCard)
                        <!-- Performance Reference Card omitted for brevity but keeping styling dark -->
                        <div class="mt-4 p-4 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400">
                                <i class="ph-bold ph-medal text-2xl"></i>
                            </div>
                            <div>
                                <div class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">Tautan Prestasi</div>
                                @if($thread->reference_type === \App\Models\Badge::class)
                                    <div class="text-sm font-bold text-slate-200">{{ $perfCard->name }}</div>
                                    <div class="text-xs text-slate-400">{{ $perfCard->description }}</div>
                                @else
                                    <div class="text-sm font-bold text-slate-200">CBT: {{ $perfCard->exam->title ?? 'Ujian' }}</div>
                                    <div class="text-xs text-slate-400">Nilai: {{ $perfCard->final_score }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Thread Reactions & Actions -->
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <!-- Picker Button -->
                    <div class="relative">
                        <button @click="togglePicker('thread')" class="w-8 h-8 rounded-full bg-white/5 hover:bg-white/10 border border-white/10 flex items-center justify-center text-slate-400 hover:text-white transition">
                            <i class="ph-bold ph-smiley-plus"></i>
                        </button>
                        <!-- Picker Dropdown -->
                        <div x-show="pickerOpen === 'thread'" @click.away="pickerOpen = null" class="absolute bottom-full left-0 mb-2 p-2 bg-[#1c1c28] border border-white/10 rounded-xl shadow-2xl flex gap-1 z-50">
                            @foreach(\App\Models\ForumReaction::EMOJIS as $emoji => $name)
                                <button @click="reactThread('{{ $emoji }}')" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-lg transition-transform hover:scale-125">
                                    {{ $emoji }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Existing Reactions -->
                    <div id="thread-reactions" class="flex flex-wrap gap-2">
                        @foreach($threadReactions as $emoji => $count)
                            <button onclick="reactThreadAjax('{{ $emoji }}')" class="flex items-center gap-1.5 px-2 py-1 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-slate-300 transition">
                                <span>{{ $emoji }}</span> <span>{{ $count }}</span>
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- COLLAB PANEL (if active) -->
        @if(in_array($thread->category, ['project_idea', 'committee']) && $thread->status !== 'completed')
            <!-- Kept simple for space, using dark styling -->
            <div class="max-w-3xl ml-14 sm:ml-16 bg-[#16161f] border border-blue-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                <h3 class="forum-hdr text-sm font-bold text-white flex items-center gap-2 mb-3">
                    <i class="ph-bold ph-handshake text-blue-400"></i> Rekrutmen Tim
                </h3>
                @if(auth()->id() !== $thread->user_id && $thread->status === 'seeking_members')
                    @php $hasApplied = $thread->members()->where('user_id', auth()->id())->exists(); @endphp
                    @if(!$hasApplied)
                        <form action="{{ route('forum.join', $thread) }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="notes" placeholder="Pesan singkat (opsional)..." class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3 text-sm text-white focus:border-blue-500 outline-none">
                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-lg transition">Gabung</button>
                        </form>
                    @else
                        <div class="text-sm font-bold text-slate-400">Kamu sudah mendaftar. Menunggu persetujuan.</div>
                    @endif
                @endif
                <!-- Member list skipped for brevity -->
            </div>
        @endif

        <!-- POLL PANEL (if exists) -->
        @if($thread->poll)
            <div class="max-w-3xl ml-14 sm:ml-16 bg-[#16161f] border border-indigo-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                <h3 class="forum-hdr text-base font-bold text-white mb-4">{{ $thread->poll->question }}</h3>
                <div class="space-y-3" id="poll-options-container">
                    @foreach($thread->poll->options as $option)
                        @php 
                            $pct = $option->percentage(); 
                            $hasVoted = $thread->poll->votes()->where('user_id', auth()->id())->where('forum_poll_option_id', $option->id)->exists();
                        @endphp
                        <button onclick="votePoll({{ $option->id }})" class="w-full relative overflow-hidden rounded-xl border {{ $hasVoted ? 'border-indigo-500 bg-indigo-500/10' : 'border-white/10 bg-white/5 hover:bg-white/10' }} p-3 text-left transition group">
                            <!-- Progress Bar -->
                            <div class="absolute top-0 left-0 h-full bg-indigo-500/20 transition-all duration-1000" style="width: {{ $pct }}%" id="poll-bg-{{ $option->id }}"></div>
                            
                            <div class="relative z-10 flex justify-between items-center text-sm font-bold">
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 rounded-full border-2 {{ $hasVoted ? 'border-indigo-400 bg-indigo-400' : 'border-slate-500' }} flex items-center justify-center">
                                        @if($hasVoted)<div class="w-2 h-2 rounded-full bg-[#16161f]"></div>@endif
                                    </div>
                                    <span class="{{ $hasVoted ? 'text-indigo-300' : 'text-slate-300' }}">{{ $option->option_text }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-slate-400">
                                    <span id="poll-pct-{{ $option->id }}">{{ $pct }}%</span>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                <div class="mt-3 text-xs text-slate-500 font-bold text-right" id="poll-total-votes">Total Votes: {{ $thread->poll->totalVotes() }}</div>
            </div>
        @endif

        <!-- REPLIES DIVIDER -->
        @if($thread->replies->count() > 0)
            <div class="flex items-center gap-4 my-4 max-w-3xl ml-14 sm:ml-16">
                <div class="h-px flex-1 bg-white/10"></div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">{{ $thread->replies->count() }} Balasan</span>
                <div class="h-px flex-1 bg-white/10"></div>
            </div>
        @endif

        <!-- REPLIES LIST -->
        <div id="replies" class="space-y-6">
            @foreach($thread->replies as $reply)
                <div class="flex gap-4" id="reply-{{ $reply->id }}">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&size=48&background=random" 
                         class="w-10 h-10 rounded-full border border-white/10 flex-shrink-0">
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <div class="flex items-baseline gap-2">
                            <span class="font-bold text-white text-sm">{{ $reply->user->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-white/10 text-slate-300 font-bold uppercase tracking-wider">{{ $reply->user->role }}</span>
                            <span class="text-xs text-slate-500">{{ $reply->created_at->format('H:i') }}</span>
                            @if($reply->is_accepted)
                                <span class="text-[10px] px-2 py-0.5 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded flex items-center gap-1 font-bold">
                                    <i class="ph-bold ph-star"></i> Jawaban Terbaik
                                </span>
                            @endif
                        </div>

                        <!-- Quote Parent -->
                        @if($reply->parent)
                            <div class="bg-white/5 border-l-2 border-indigo-500 rounded-lg p-2.5 max-w-2xl text-xs text-slate-400 mb-2 cursor-pointer hover:bg-white/10 transition" onclick="document.getElementById('reply-{{ $reply->parent_id }}').scrollIntoView({behavior: 'smooth'})">
                                <div class="font-bold text-indigo-400 mb-1">Membalas {{ $reply->parent->user->name }}</div>
                                <div class="line-clamp-2">{!! strip_tags($reply->parent->content) !!}</div>
                            </div>
                        @endif

                        <!-- Bubble -->
                        <div class="{{ $reply->is_accepted ? 'bg-amber-500/10 border-amber-500/30 ring-1 ring-amber-500/20' : 'bg-white/5 border-white/10' }} border rounded-2xl rounded-tl-none p-4 max-w-2xl text-sm text-slate-200">
                            {!! nl2br(e($reply->content)) !!}
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center gap-2 mt-1">
                            <!-- Reply Picker -->
                            <div class="relative">
                                <button @click="togglePicker('reply-{{ $reply->id }}')" class="w-6 h-6 rounded-full hover:bg-white/10 flex items-center justify-center text-slate-500 hover:text-white transition">
                                    <i class="ph-bold ph-smiley-plus"></i>
                                </button>
                                <div x-show="pickerOpen === 'reply-{{ $reply->id }}'" @click.away="pickerOpen = null" class="absolute bottom-full left-0 mb-2 p-2 bg-[#1c1c28] border border-white/10 rounded-xl shadow-2xl flex gap-1 z-50">
                                    @foreach(\App\Models\ForumReaction::EMOJIS as $emoji => $name)
                                        <button @click="reactReply('{{ $reply->id }}', '{{ $emoji }}')" class="w-8 h-8 rounded-lg hover:bg-white/10 flex items-center justify-center text-lg transition-transform hover:scale-125">
                                            {{ $emoji }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Reactions -->
                            <div id="reply-reactions-{{ $reply->id }}" class="flex flex-wrap gap-1">
                                @foreach($reply->getReactionCounts() as $emoji => $count)
                                    <button onclick="reactReplyAjax({{ $reply->id }}, '{{ $emoji }}')" class="flex items-center gap-1 px-1.5 py-0.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded text-[10px] font-bold text-slate-300 transition">
                                        <span>{{ $emoji }}</span> <span>{{ $count }}</span>
                                    </button>
                                @endforeach
                            </div>

                            <!-- Reply Button -->
                            <button @click="quoteReply({{ $reply->id }}, '{{ addslashes($reply->user->name) }}', '{{ addslashes(Str::limit(strip_tags($reply->content), 100)) }}')" class="text-[10px] font-bold text-slate-500 hover:text-indigo-400 ml-2 transition">
                                BALAS
                            </button>

                            <!-- Accept Answer -->
                            @if(!$reply->is_accepted && !$thread->replies->contains('is_accepted', true) && (auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru()))
                                <form action="{{ route('forum.reply.accept', $reply) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-[10px] font-bold text-slate-500 hover:text-amber-400 ml-2 transition">
                                        TERBAIK
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- STICKY COMPOSE BAR -->
@if(!$thread->is_locked)
<div class="fixed bottom-0 left-0 w-full bg-[#16161f]/90 backdrop-blur-xl border-t border-white/10 pb-safe z-50 transition-all duration-300" id="compose-bar">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-3">
        <!-- Quote Preview Area -->
        <div id="quote-preview" class="hidden mb-2 ml-14 sm:ml-16 mr-14">
            <div class="bg-white/5 border-l-2 border-indigo-500 rounded-lg p-2.5 flex justify-between items-start gap-4">
                <div class="min-w-0">
                    <div class="text-xs font-bold text-indigo-400 mb-0.5" id="quote-user"></div>
                    <div class="text-xs text-slate-400 line-clamp-1" id="quote-text"></div>
                </div>
                <button type="button" onclick="cancelQuote()" class="text-slate-500 hover:text-white p-1">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('forum.reply', $thread) }}" method="POST" class="flex gap-3 items-end">
            @csrf
            <input type="hidden" name="parent_reply_id" id="parent_reply_id">
            
            <button type="button" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-white/5 hover:bg-white/10 flex items-center justify-center text-slate-400 hover:text-white transition flex-shrink-0 mb-1">
                <i class="ph-bold ph-plus text-xl"></i>
            </button>

            <div class="flex-1 bg-black/40 border border-white/10 rounded-2xl overflow-hidden focus-within:border-indigo-500 transition-colors">
                <textarea name="content" rows="1" required placeholder="Ketik pesan..." class="w-full bg-transparent text-white px-4 py-3 outline-none resize-none min-h-[48px] max-h-32 text-sm sm:text-base no-scrollbar" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
            </div>

            <button type="submit" class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 flex items-center justify-center text-white shadow-lg shadow-indigo-500/30 hover:scale-105 transition flex-shrink-0 mb-1">
                <i class="ph-bold ph-paper-plane-right text-xl"></i>
            </button>
        </form>
    </div>
</div>
@else
<div class="fixed bottom-0 left-0 w-full bg-rose-500/10 backdrop-blur-xl border-t border-rose-500/20 pb-safe z-50">
    <div class="max-w-[1200px] mx-auto px-4 py-4 text-center text-rose-400 font-bold text-sm">
        <i class="ph-bold ph-lock-key mr-2"></i> Topik ini telah dikunci.
    </div>
</div>
@endif

<script>
function forumChat() {
    return {
        pickerOpen: null,
        togglePicker(id) {
            this.pickerOpen = this.pickerOpen === id ? null : id;
        },
        reactThread(emoji) {
            reactThreadAjax(emoji);
            this.pickerOpen = null;
        },
        reactReply(replyId, emoji) {
            reactReplyAjax(replyId, emoji);
            this.pickerOpen = null;
        }
    }
}

// Quote functionality
function quoteReply(id, user, text) {
    document.getElementById('parent_reply_id').value = id;
    document.getElementById('quote-user').textContent = 'Membalas ' + user;
    document.getElementById('quote-text').textContent = text;
    document.getElementById('quote-preview').classList.remove('hidden');
    document.querySelector('textarea[name="content"]').focus();
}

function cancelQuote() {
    document.getElementById('parent_reply_id').value = '';
    document.getElementById('quote-preview').classList.add('hidden');
}

// AJAX Reactions
async function reactThreadAjax(emoji) {
    try {
        const res = await fetch("{{ route('forum.react', $thread) }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSR-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emoji: emoji })
        });
        const data = await res.json();
        if (data.success) {
            updateReactionUI('thread-reactions', data.counts, true);
        }
    } catch (e) { console.error(e); }
}

async function reactReplyAjax(replyId, emoji) {
    try {
        const res = await fetch(`/forum/reply/${replyId}/react`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSR-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ emoji: emoji })
        });
        const data = await res.json();
        if (data.success) {
            updateReactionUI(`reply-reactions-${replyId}`, data.counts, false, replyId);
        }
    } catch (e) { console.error(e); }
}

function updateReactionUI(containerId, counts, isThread, replyId = null) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    for (const [em, count] of Object.entries(counts)) {
        if (count > 0) {
            const btn = document.createElement('button');
            const clickFn = isThread ? `reactThreadAjax('${em}')` : `reactReplyAjax(${replyId}, '${em}')`;
            btn.setAttribute('onclick', clickFn);
            btn.className = 'flex items-center gap-1.5 px-2 py-1 bg-white/5 hover:bg-white/10 border border-white/10 rounded-lg text-xs font-bold text-slate-300 transition';
            if(!isThread) btn.className = 'flex items-center gap-1 px-1.5 py-0.5 bg-white/5 hover:bg-white/10 border border-white/10 rounded text-[10px] font-bold text-slate-300 transition';
            btn.innerHTML = `<span>${em}</span> <span>${count}</span>`;
            container.appendChild(btn);
        }
    }
}

// AJAX Poll
async function votePoll(optionId) {
    try {
        const res = await fetch(`/forum/poll/${optionId}/vote`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSR-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await res.json();
        if (data.success) {
            document.getElementById('poll-total-votes').textContent = 'Total Votes: ' + data.total_votes;
            data.options.forEach(opt => {
                document.getElementById(`poll-pct-${opt.id}`).textContent = opt.percentage + '%';
                document.getElementById(`poll-bg-${opt.id}`).style.width = opt.percentage + '%';
                
                // Update styling
                const btn = document.getElementById(`poll-bg-${opt.id}`).parentElement;
                const circle = btn.querySelector('.rounded-full.border-2');
                const text = btn.querySelector('.relative.z-10 span');
                
                if (data.voted && data.voted_option_id === opt.id) {
                    btn.className = 'w-full relative overflow-hidden rounded-xl border border-indigo-500 bg-indigo-500/10 p-3 text-left transition group';
                    circle.className = 'w-4 h-4 rounded-full border-2 border-indigo-400 bg-indigo-400 flex items-center justify-center';
                    circle.innerHTML = '<div class="w-2 h-2 rounded-full bg-[#16161f]"></div>';
                    text.className = 'text-indigo-300';
                } else {
                    btn.className = 'w-full relative overflow-hidden rounded-xl border border-white/10 bg-white/5 hover:bg-white/10 p-3 text-left transition group';
                    circle.className = 'w-4 h-4 rounded-full border-2 border-slate-500 flex items-center justify-center';
                    circle.innerHTML = '';
                    text.className = 'text-slate-300';
                }
            });
        }
    } catch (e) { console.error(e); }
}
</script>
@endsection
