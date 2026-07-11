@extends(auth()->user()->layout)

@section('title', $thread->title)

@section('content')
<!-- Dynamic Google Fonts & Phosphor Icons -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;650;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>

<script>
    window.onerror = function(message, source, lineno, colno, error) {
        alert("GLOBAL JS ERROR:\n" + message + "\nIn: " + source + "\nLine: " + lineno + ":" + colno);
        return false;
    };
    window.onunhandledrejection = function(event) {
        alert("UNHANDLED REJECTION:\n" + event.reason);
    };
</script>

<script>
  if (typeof tailwind !== 'undefined') {
    tailwind.config = {
      corePlugins: {
        preflight: false,
      }
    }
  }
</script>
<!-- MathJax for math equations -->
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
<style>
    .forum-hdr { font-family: 'Space Grotesk', sans-serif; }
    /* Hide scrollbar for clean UI */
    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    
    /* Bulletproof Dark Mode Colors */
    .bg-forum-base { background-color: #0f0f14 !important; }
    .bg-forum-panel { background-color: #12121a !important; }
    .bg-forum-card { background-color: #16161f !important; }
    .bg-forum-card-80 { background-color: rgba(22, 22, 31, 0.8) !important; }
    .bg-forum-card-90 { background-color: rgba(22, 22, 31, 0.9) !important; }
    .text-forum-title { color: #f8fafc !important; }
    .text-forum-body { color: #94a3b8 !important; }
    .text-forum-muted { color: #64748b !important; }
    .border-forum { border-color: rgba(255, 255, 255, 0.05) !important; }
    .border-forum-light { border-color: rgba(255, 255, 255, 0.1) !important; }
    .bg-forum-light-5 { background-color: rgba(255, 255, 255, 0.05) !important; }
    .bg-forum-light-10 { background-color: rgba(255, 255, 255, 0.1) !important; }

    /* Compose Emoji Picker Custom CSS */
    .compose-emoji-picker {
        position: absolute !important;
        bottom: 100% !important;
        left: 0 !important;
        margin-bottom: 8px !important;
        padding: 10px !important;
        background-color: #1c1c28 !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 16px !important;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5) !important;
        display: grid !important;
        grid-template-columns: repeat(6, minmax(0, 1fr)) !important;
        gap: 4px !important;
        z-index: 50 !important;
        width: 224px !important;
    }
    .compose-emoji-btn {
        width: 32px !important;
        height: 32px !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 18px !important;
        transition: transform 0.2s !important;
        background: transparent !important;
        border: none !important;
        cursor: pointer !important;
        color: white !important;
    }
    .compose-emoji-btn:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        transform: scale(1.25) !important;
    }
</style>

<!-- App Window Wrapper -->
<div class="w-full bg-forum-base text-forum-title font-['Inter'] rounded-3xl shadow-2xl border border-forum-light mx-auto flex flex-col pt-4 pb-32 px-4 sm:px-6 relative" style="min-height: 85vh;" x-data="forumChat()">
    
    <!-- Top Nav Bar -->
    <div class="flex items-center justify-between bg-forum-card/80 backdrop-blur-xl p-4 rounded-2xl border border-forum mb-6 sticky top-4 z-40 shadow-2xl shadow-black/20">
        <div class="flex items-center gap-4">
            <a href="{{ route('forum.index') }}" class="w-10 h-10 rounded-xl bg-forum-light-5 hover:bg-forum-light-10 flex items-center justify-center text-slate-300 hover:text-white transition">
                <i class="ph-bold ph-arrow-left text-xl"></i>
            </a>
            <div>
                <div class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">{{ $thread->category_label }}</div>
                <h1 class="forum-hdr text-base sm:text-lg font-bold text-white line-clamp-1">{{ $thread->title }}</h1>
            </div>
        </div>
        
        <div class="flex items-center gap-2">
            @if(auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru())
                <a href="{{ route('forum.edit', $thread) }}" class="w-10 h-10 rounded-xl bg-forum-light-5 hover:bg-forum-light-10 flex items-center justify-center text-amber-400 transition" title="Edit">
                    <i class="ph-bold ph-pencil-simple"></i>
                </a>
                <form action="{{ route('forum.destroy', $thread) }}" method="POST" onsubmit="return confirm('Yakin hapus postingan ini?')" class="inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-10 h-10 rounded-xl bg-forum-light-5 hover:bg-rose-500/20 hover:text-rose-400 flex items-center justify-center text-forum-body transition" title="Hapus">
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
                 class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border border-forum-light flex-shrink-0">
            <div class="flex-1 min-w-0 space-y-2">
                <!-- Meta -->
                <div class="flex items-baseline gap-2">
                    <span class="font-bold text-white text-sm sm:text-base">{{ $thread->user->name }}</span>
                    <span class="text-[10px] px-1.5 py-0.5 rounded bg-forum-light-10 text-slate-300 font-bold uppercase tracking-wider">{{ $thread->user->role }}</span>
                    <span class="text-xs text-forum-muted">{{ $thread->created_at->format('H:i • d M Y') }}</span>
                </div>
                
                <!-- Bubble -->
                <div class="bg-gradient-to-br from-[#1c1c28] to-[#16161f] border border-forum-light rounded-2xl rounded-tl-none p-5 sm:p-6 shadow-xl w-full max-w-3xl">
                    <h2 class="forum-hdr text-xl sm:text-2xl font-bold text-white mb-4">{{ $thread->title }}</h2>
                    <div class="prose prose-invert prose-sm sm:prose-base max-w-none text-slate-300">
                        {!! nl2br(e($thread->content)) !!}
                    </div>

                    @if($thread->image_path)
                        <div class="mt-4 rounded-xl overflow-hidden border border-forum-light max-w-lg">
                            <img src="{{ asset('storage/' . $thread->image_path) }}" class="w-full h-auto">
                        </div>
                    @endif

                    @if($thread->attachment_path)
                        <a href="{{ asset('storage/' . $thread->attachment_path) }}" download class="mt-4 flex items-center gap-3 p-3 bg-forum-light-5 hover:bg-forum-light-10 border border-forum-light rounded-xl transition max-w-sm">
                            <div class="w-10 h-10 rounded-lg bg-indigo-500/20 flex items-center justify-center text-indigo-400">
                                <i class="ph-bold ph-file-arrow-down text-xl"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-xs font-bold text-slate-200 truncate">{{ $thread->attachment_name ?? 'Download Lampiran' }}</div>
                                <div class="text-[10px] text-forum-muted">Klik untuk mengunduh</div>
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
                                    <div class="text-xs text-forum-body">{{ $perfCard->description }}</div>
                                @else
                                    <div class="text-sm font-bold text-slate-200">CBT: {{ $perfCard->exam->title ?? 'Ujian' }}</div>
                                    <div class="text-xs text-forum-body">Nilai: {{ $perfCard->final_score }}</div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Thread Reactions & Actions -->
                <div class="flex flex-wrap items-center gap-2 mt-2">
                    <!-- Picker Button -->
                    <div class="flex items-center">
                        <button @click="togglePicker('thread')" class="w-8 h-8 rounded-full bg-forum-light-5 hover:bg-forum-light-10 border border-forum-light flex items-center justify-center text-forum-body hover:text-white transition">
                            <i class="ph-bold ph-smiley"></i>
                        </button>
                        <!-- Picker Dropdown (Inline) -->
                        <div x-show="pickerOpen === 'thread'" class="ml-2 p-1 bg-[#1c1c28] border border-forum-light rounded-xl flex gap-1">
                            @foreach(\App\Models\ForumReaction::EMOJIS as $emoji => $name)
                                <button @click="reactThread('{{ $emoji }}')" class="w-8 h-8 rounded-lg hover:bg-forum-light-10 flex items-center justify-center text-lg transition-transform hover:scale-125">
                                    {{ $emoji }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Existing Reactions -->
                    <div id="thread-reactions" class="flex flex-wrap gap-2">
                        @foreach($threadReactions as $emoji => $count)
                            <button onclick="reactThreadAjax('{{ $emoji }}')" class="flex items-center gap-1.5 px-2 py-1 bg-forum-light-5 hover:bg-forum-light-10 border border-forum-light rounded-lg text-xs font-bold text-slate-300 transition">
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
            <div class="max-w-3xl ml-14 sm:ml-16 bg-forum-card border border-blue-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-blue-500"></div>
                <h3 class="forum-hdr text-sm font-bold text-white flex items-center gap-2 mb-3">
                    <i class="ph-bold ph-handshake text-blue-400"></i> Rekrutmen Tim
                </h3>
                @if(auth()->id() !== $thread->user_id && $thread->status === 'seeking_members')
                    @php $hasApplied = $thread->members()->where('user_id', auth()->id())->exists(); @endphp
                    @if(!$hasApplied)
                        <form action="{{ route('forum.join', $thread) }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="notes" placeholder="Pesan singkat (opsional)..." class="flex-1 bg-forum-light-5 border border-forum-light rounded-lg px-3 text-sm text-white focus:border-blue-500 outline-none">
                            <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-bold rounded-lg transition">Gabung</button>
                        </form>
                    @else
                        <div class="text-sm font-bold text-forum-body">Kamu sudah mendaftar. Menunggu persetujuan.</div>
                    @endif
                @endif
                <!-- Member list skipped for brevity -->
            </div>
        @endif

        <!-- POLL PANEL (if exists) -->
        @if($thread->poll)
            <div class="max-w-3xl ml-14 sm:ml-16 bg-forum-card border border-indigo-500/20 rounded-2xl p-5 shadow-lg relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                <h3 class="forum-hdr text-base font-bold text-white mb-4">{{ $thread->poll->question }}</h3>
                <div class="space-y-3" id="poll-options-container">
                    @foreach($thread->poll->options as $option)
                        @php 
                            $pct = $option->percentage(); 
                            $hasVoted = $thread->poll->votes()->where('user_id', auth()->id())->where('forum_poll_option_id', $option->id)->exists();
                        @endphp
                        <button onclick="votePoll({{ $option->id }})" class="w-full relative overflow-hidden rounded-xl border {{ $hasVoted ? 'border-indigo-500 bg-indigo-500/10' : 'border-forum-light bg-forum-light-5 hover:bg-forum-light-10' }} p-3 text-left transition group">
                            <!-- Progress Bar -->
                            <div class="absolute top-0 left-0 h-full bg-indigo-500/20 transition-all duration-1000" style="width: {{ $pct }}%" id="poll-bg-{{ $option->id }}"></div>
                            
                            <div class="relative z-10 flex justify-between items-center text-sm font-bold">
                                <div class="flex items-center gap-3">
                                    <div class="w-4 h-4 rounded-full border-2 {{ $hasVoted ? 'border-indigo-400 bg-indigo-400' : 'border-slate-500' }} flex items-center justify-center">
                                        @if($hasVoted)<div class="w-2 h-2 rounded-full bg-forum-card"></div>@endif
                                    </div>
                                    <span class="{{ $hasVoted ? 'text-indigo-300' : 'text-slate-300' }}">{{ $option->option_text }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-forum-body">
                                    <span id="poll-pct-{{ $option->id }}">{{ $pct }}%</span>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                <div class="mt-3 text-xs text-forum-muted font-bold text-right" id="poll-total-votes">Total Votes: {{ $thread->poll->totalVotes() }}</div>
            </div>
        @endif

        <!-- REPLIES DIVIDER -->
        @if($thread->replies->count() > 0)
            <div class="flex items-center gap-4 my-4 max-w-3xl ml-14 sm:ml-16">
                <div class="h-px flex-1 bg-forum-light-10"></div>
                <span class="text-xs font-bold text-forum-muted uppercase tracking-wider">{{ $thread->replies->count() }} Balasan</span>
                <div class="h-px flex-1 bg-forum-light-10"></div>
            </div>
        @endif

        <!-- REPLIES LIST -->
        <div id="replies" class="space-y-6">
            @foreach($thread->replies as $reply)
                <div class="flex gap-4" id="reply-{{ $reply->id }}">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&size=48&background=random" 
                         class="w-10 h-10 rounded-full border border-forum-light flex-shrink-0">
                    <div class="flex-1 min-w-0 space-y-1.5">
                        <div class="flex items-baseline gap-2">
                            <span class="font-bold text-white text-sm">{{ $reply->user->name }}</span>
                            <span class="text-[10px] px-1.5 py-0.5 rounded bg-forum-light-10 text-slate-300 font-bold uppercase tracking-wider">{{ $reply->user->role }}</span>
                            <span class="text-xs text-forum-muted">{{ $reply->created_at->format('H:i') }}</span>
                            @if($reply->is_accepted)
                                <span class="text-[10px] px-2 py-0.5 bg-amber-500/20 text-amber-400 border border-amber-500/30 rounded flex items-center gap-1 font-bold">
                                    <i class="ph-bold ph-star"></i> Jawaban Terbaik
                                </span>
                            @endif
                        </div>

                        <!-- Quote Parent -->
                        @if($reply->parent)
                            <div class="bg-forum-light-5 border-l-2 border-indigo-500 rounded-lg p-2.5 max-w-2xl text-xs text-forum-body mb-2 cursor-pointer hover:bg-forum-light-10 transition" onclick="document.getElementById('reply-{{ $reply->parent_id }}').scrollIntoView({behavior: 'smooth'})">
                                <div class="font-bold text-indigo-400 mb-1">Membalas {{ $reply->parent->user->name }}</div>
                                <div class="line-clamp-2">{!! strip_tags($reply->parent->content) !!}</div>
                            </div>
                        @endif

                        <!-- Bubble -->
                        <div class="{{ $reply->is_accepted ? 'bg-amber-500/10 border-amber-500/30 ring-1 ring-amber-500/20' : 'bg-forum-light-5 border-forum-light' }} border rounded-2xl rounded-tl-none p-4 max-w-2xl text-sm text-slate-200">
                            {!! nl2br(e($reply->content)) !!}
                        </div>

                        <!-- Actions -->
                        <div class="flex flex-wrap items-center justify-between gap-2 mt-2 w-full max-w-2xl">
                            <!-- Left: Smiley & Reactions -->
                            <div class="flex items-center gap-2">
                                <!-- Reply Picker -->
                                <div class="flex items-center">
                                    <button @click="togglePicker('reply-{{ $reply->id }}')" class="w-6 h-6 rounded-full hover:bg-forum-light-10 flex items-center justify-center text-forum-muted hover:text-white transition">
                                        <i class="ph-bold ph-smiley"></i>
                                    </button>
                                    <!-- Picker Dropdown (Inline) -->
                                    <div x-show="pickerOpen === 'reply-{{ $reply->id }}'" class="ml-2 p-1 bg-[#1c1c28] border border-forum-light rounded-xl flex gap-1">
                                        @foreach(\App\Models\ForumReaction::EMOJIS as $emoji => $name)
                                            <button @click="reactReply('{{ $reply->id }}', '{{ $emoji }}')" class="w-6 h-6 rounded hover:bg-forum-light-10 flex items-center justify-center text-base transition-transform hover:scale-125">
                                                {{ $emoji }}
                                            </button>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Reactions -->
                                <div id="reply-reactions-{{ $reply->id }}" class="flex flex-wrap gap-1">
                                    @foreach($reply->getReactionCounts() as $emoji => $count)
                                        <button onclick="reactReplyAjax({{ $reply->id }}, '{{ $emoji }}')" class="flex items-center gap-1 px-1.5 py-0.5 bg-forum-light-5 hover:bg-forum-light-10 border border-forum-light rounded text-[10px] font-bold text-slate-300 transition">
                                            <span>{{ $emoji }}</span> <span>{{ $count }}</span>
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Right: Balas & Terbaik -->
                            <div class="flex items-center gap-3 ml-auto">
                                <!-- Reply Button -->
                                <button @click="quoteReply({{ $reply->id }}, '{{ addslashes($reply->user->name) }}', '{{ addslashes(Str::limit(strip_tags($reply->content), 100)) }}')" class="text-[10px] font-bold text-forum-muted hover:text-indigo-400 transition">
                                    BALAS
                                </button>

                                <!-- Accept Answer -->
                                @if(!$reply->is_accepted && !$thread->replies->contains('is_accepted', true) && (auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru()))
                                    <form action="{{ route('forum.reply.accept', $reply) }}" method="POST" class="inline-flex items-center">
                                        @csrf
                                        <button type="submit" class="text-[10px] font-bold text-forum-muted hover:text-amber-400 transition">
                                            TERBAIK
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

<!-- STICKY COMPOSE BAR -->
@if(!$thread->is_locked)
<div class="fixed bottom-0 left-0 w-full bg-forum-card/90 backdrop-blur-xl border-t border-forum-light pb-safe z-50 transition-all duration-300" id="compose-bar">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 py-3">
        <!-- Quote Preview Area -->
        <div id="quote-preview" class="hidden mb-2 ml-14 sm:ml-16 mr-14">
            <div class="bg-forum-light-5 border-l-2 border-indigo-500 rounded-lg p-2.5 flex justify-between items-start gap-4">
                <div class="min-w-0">
                    <div class="text-xs font-bold text-indigo-400 mb-0.5" id="quote-user"></div>
                    <div class="text-xs text-forum-body line-clamp-1" id="quote-text"></div>
                </div>
                <button type="button" onclick="cancelQuote()" class="text-forum-muted hover:text-white p-1">
                    <i class="ph-bold ph-x"></i>
                </button>
            </div>
        </div>

        <form action="{{ route('forum.reply', $thread) }}" method="POST" class="flex gap-3 items-end">
            @csrf
            <input type="hidden" name="parent_reply_id" id="parent_reply_id">
            
            <!-- Emoji Picker for Input (Vanilla JS) -->
            <div class="relative flex-shrink-0 mb-1">
                <button type="button" onclick="toggleComposeEmojiVanilla(event)" class="compose-emoji-trigger w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-forum-light-5 hover:bg-forum-light-10 flex items-center justify-center text-forum-body hover:text-white transition">
                    <i class="ph-bold ph-plus text-xl"></i>
                </button>
                <div id="compose-emoji-picker" class="compose-emoji-picker" style="display: none;">
                    <button type="button" onclick="insertEmojiVanilla('😀')" class="compose-emoji-btn"><span>😀</span></button>
                    <button type="button" onclick="insertEmojiVanilla('😂')" class="compose-emoji-btn"><span>😂</span></button>
                    <button type="button" onclick="insertEmojiVanilla('😍')" class="compose-emoji-btn"><span>😍</span></button>
                    <button type="button" onclick="insertEmojiVanilla('👍')" class="compose-emoji-btn"><span>👍</span></button>
                    <button type="button" onclick="insertEmojiVanilla('🔥')" class="compose-emoji-btn"><span>🔥</span></button>
                    <button type="button" onclick="insertEmojiVanilla('👏')" class="compose-emoji-btn"><span>👏</span></button>
                    <button type="button" onclick="insertEmojiVanilla('❤️')" class="compose-emoji-btn"><span>❤️</span></button>
                    <button type="button" onclick="insertEmojiVanilla('💡')" class="compose-emoji-btn"><span>💡</span></button>
                    <button type="button" onclick="insertEmojiVanilla('🤔')" class="compose-emoji-btn"><span>🤔</span></button>
                    <button type="button" onclick="insertEmojiVanilla('🎉')" class="compose-emoji-btn"><span>🎉</span></button>
                    <button type="button" onclick="insertEmojiVanilla('🙏')" class="compose-emoji-btn"><span>🙏</span></button>
                    <button type="button" onclick="insertEmojiVanilla('✨')" class="compose-emoji-btn"><span>✨</span></button>
                </div>
            </div>

            <div class="flex-1 bg-black/40 border border-forum-light rounded-2xl overflow-hidden focus-within:border-indigo-500 transition-colors">
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
</div>

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

// Vanilla JS Emoji Picker & Insertion
function toggleComposeEmojiVanilla(event) {
    event.stopPropagation();
    const picker = document.getElementById('compose-emoji-picker');
    if (!picker) return;
    if (picker.style.display === 'none') {
        picker.style.display = 'grid';
    } else {
        picker.style.display = 'none';
    }
}

let lastInsertTimeVanilla = 0;
function insertEmojiVanilla(emoji) {
    const now = Date.now();
    if (now - lastInsertTimeVanilla < 150) return;
    lastInsertTimeVanilla = now;

    const textarea = document.querySelector('textarea[name="content"]');
    if (textarea) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + emoji + text.substring(end);
        textarea.focus();
        textarea.style.height = '';
        textarea.style.height = textarea.scrollHeight + 'px';
    }

    const picker = document.getElementById('compose-emoji-picker');
    if (picker) picker.style.display = 'none';
}

// Close picker when clicking anywhere outside
document.addEventListener('click', function(event) {
    const picker = document.getElementById('compose-emoji-picker');
    if (!picker || picker.style.display === 'none') return;
    
    const trigger = event.target.closest('.compose-emoji-trigger');
    const insidePicker = event.target.closest('#compose-emoji-picker');
    if (!trigger && !insidePicker) {
        picker.style.display = 'none';
    }
});

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

function getCsrfToken() {
    const name = "XSRF-TOKEN=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const ca = decodedCookie.split(';');
    for(let i = 0; i < ca.length; i++) {
        let c = ca[i].trim();
        if (c.indexOf(name) === 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '{{ csrf_token() }}';
}
let isReacting = false;

// AJAX Reactions
async function reactThreadAjax(emoji) {
    if (isReacting) return;
    isReacting = true;
    try {
        const res = await fetch("{{ route('forum.react', $thread) }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
            body: JSON.stringify({ emoji: emoji })
        });
        if (!res.ok) {
            const text = await res.text();
            alert("React Thread Server Error (" + res.status + "): " + text.substring(0, 500));
            return;
        }
        const data = await res.json();
        if (data.success) {
            updateReactionUI('thread-reactions', data.counts, true);
        }
    } catch (e) { alert("React Thread JS Error: " + e.message); }
    finally { isReacting = false; }
}

async function reactReplyAjax(replyId, emoji) {
    if (isReacting) return;
    isReacting = true;
    try {
        const res = await fetch(`{{ url('/forum/reply') }}/${replyId}/react`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() },
            body: JSON.stringify({ emoji: emoji })
        });
        if (!res.ok) {
            const text = await res.text();
            alert("React Reply Server Error (" + res.status + "): " + text.substring(0, 500));
            return;
        }
        const data = await res.json();
        if (data.success) {
            updateReactionUI(`reply-reactions-${replyId}`, data.counts, false, replyId);
        }
    } catch (e) { alert("React Reply JS Error: " + e.message); }
    finally { isReacting = false; }
}

function updateReactionUI(containerId, counts, isThread, replyId = null) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    for (const [em, count] of Object.entries(counts)) {
        if (count > 0) {
            const btn = document.createElement('button');
            const clickFn = isThread ? `reactThreadAjax('${em}')` : `reactReplyAjax(${replyId}, '${em}')`;
            btn.setAttribute('onclick', clickFn);
            btn.className = 'flex items-center gap-1.5 px-2 py-1 bg-forum-light-5 hover:bg-forum-light-10 border border-forum-light rounded-lg text-xs font-bold text-slate-300 transition';
            if(!isThread) btn.className = 'flex items-center gap-1 px-1.5 py-0.5 bg-forum-light-5 hover:bg-forum-light-10 border border-forum-light rounded text-[10px] font-bold text-slate-300 transition';
            btn.innerHTML = `<span>${em}</span> <span>${count}</span>`;
            container.appendChild(btn);
        }
    }
}

// AJAX Poll
async function votePoll(optionId) {
    try {
        const res = await fetch(`{{ url('/forum/poll') }}/${optionId}/vote`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-XSRF-TOKEN': getCsrfToken() }
        });
        if (!res.ok) {
            const text = await res.text();
            alert("Poll Server Error (" + res.status + "): " + text.substring(0, 500));
            return;
        }
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
                    circle.innerHTML = '<div class="w-2 h-2 rounded-full bg-forum-card"></div>';
                    text.className = 'text-indigo-300';
                } else {
                    btn.className = 'w-full relative overflow-hidden rounded-xl border border-forum-light bg-forum-light-5 hover:bg-forum-light-10 p-3 text-left transition group';
                    circle.className = 'w-4 h-4 rounded-full border-2 border-slate-500 flex items-center justify-center';
                    circle.innerHTML = '';
                    text.className = 'text-slate-300';
                }
            });
        }
    } catch (e) { alert("Poll JS Error: " + e.message); }
}
</script>
@endsection
