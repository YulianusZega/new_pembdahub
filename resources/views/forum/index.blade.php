@extends(auth()->user()->layout)

@section('title', 'Pembda Space')

@section('content')
<!-- Dynamic Google Fonts & Phosphor Icons -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;650;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.3/dist/cdn.min.js" defer></script>

<script>
  if (typeof tailwind !== 'undefined') {
    tailwind.config = {
      corePlugins: {
        preflight: false,
      }
    }
  }
</script>
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
    
    .pixelated-canvas {
        image-rendering: pixelated;
        image-rendering: -moz-crisp-edges;
        image-rendering: crisp-edges;
    }
    
    .pixel-grid {
        background-image: 
            linear-gradient(to right, rgba(148, 163, 184, 0.25) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(148, 163, 184, 0.25) 1px, transparent 1px);
        background-size: calc(100% / 100) calc(100% / 100);
        pointer-events: none;
    }
</style>

<!-- App Window Wrapper (Embedded in Global Layout) -->
<div class="w-full bg-forum-base text-forum-title font-['Inter'] rounded-3xl shadow-2xl overflow-hidden border border-forum-light" style="min-height: 85vh;">
    
    <div class="flex flex-col md:flex-row h-full w-full" x-data="{ mobileSidebarOpen: false }">
        
        <!-- Mobile Header & Toggle (Visible only on mobile inside the window) -->
        <div class="md:hidden flex items-center justify-between bg-forum-card p-4 border-b border-forum">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                    <i class="ph-bold ph-lightning text-white text-xl"></i>
                </div>
                <h1 class="forum-hdr text-xl font-bold text-white tracking-tight">Pembda Space</h1>
            </div>
            <button @click="mobileSidebarOpen = !mobileSidebarOpen" class="p-2 bg-forum-light-5 rounded-lg text-slate-300 hover:text-white transition">
                <i class="ph-bold ph-list text-2xl"></i>
            </button>
        </div>

        <!-- CHANNEL SIDEBAR (Left) -->
        <div :class="mobileSidebarOpen ? 'block' : 'hidden'" class="md:block w-full md:w-64 lg:w-72 bg-forum-panel border-r border-forum flex-shrink-0 flex flex-col transition-all duration-300 relative z-20">
            
            <div class="p-5 h-full flex flex-col gap-6 max-h-[85vh] overflow-y-auto no-scrollbar">
                <!-- Logo (Desktop) -->
                <div class="hidden md:flex items-center gap-3 px-2 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 via-violet-500 to-fuchsia-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <i class="ph-bold ph-lightning text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="forum-hdr text-xl font-bold text-white tracking-tight leading-tight">Pembda Space</h1>
                        <span class="text-[10px] text-forum-body font-semibold uppercase tracking-widest">Space</span>
                    </div>
                </div>

                <nav class="flex-1 space-y-6">
                    <!-- All Channels -->
                    <div class="space-y-1">
                        <a href="{{ route('forum.index', array_filter(['search' => $search])) }}" 
                           class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 {{ !$category ? 'bg-forum-light-10 text-white font-semibold shadow-inner' : 'text-forum-body hover:bg-forum-light-5 hover:text-slate-200' }}">
                            <div class="flex items-center gap-3">
                                <i class="ph-bold ph-compass text-lg {{ !$category ? 'text-indigo-400' : '' }}"></i>
                                <span class="text-sm">Semua Saluran</span>
                            </div>
                        </a>
                    </div>

                    @foreach($channelGroups as $groupName => $channels)
                    <div class="space-y-1.5" x-data="{ expanded: true }">
                        <button @click="expanded = !expanded" class="w-full flex items-center justify-between px-2 py-1 text-xs font-bold text-forum-muted hover:text-slate-300 transition uppercase tracking-wider group">
                            <span>{{ $groupName }}</span>
                            <i class="ph-bold ph-caret-down transition-transform duration-200" :class="expanded ? '' : '-rotate-90'"></i>
                        </button>
                        <div x-show="expanded" class="space-y-0.5">
                            @foreach($channels as $catKey)
                                @php
                                    $catLabel = \App\Models\ForumThread::CATEGORIES[$catKey] ?? $catKey;
                                    $isActive = $category === $catKey;
                                    $count = $counts[$catKey] ?? 0;
                                    preg_match('/^[\p{Emoji_Presentation}\p{Extended_Pictographic}]/u', $catLabel, $matches);
                                    $emoji = $matches[0] ?? '💬';
                                    $cleanLabel = trim(str_replace($emoji, '', $catLabel));
                                @endphp
                                <a href="{{ route('forum.index', array_filter(['category' => $catKey, 'search' => $search])) }}" 
                                   class="flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-200 {{ $isActive ? 'bg-forum-light-10 border-l-2 border-indigo-500 text-white font-semibold' : 'text-forum-body hover:bg-forum-light-5 hover:text-slate-200 border-l-2 border-transparent' }}">
                                    <div class="flex items-center gap-3">
                                        <span>{{ $emoji }}</span>
                                        <span class="text-sm truncate">{{ $cleanLabel }}</span>
                                    </div>
                                    @if($count > 0)
                                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $isActive ? 'bg-indigo-500/20 text-indigo-300' : 'bg-forum-light-5 text-forum-muted' }}">{{ $count }}</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </nav>

                <!-- Stats footer -->
                <div class="mt-auto pt-4 border-t border-forum pb-2">
                    <div class="flex justify-between items-center px-2 text-xs font-medium text-forum-muted">
                        <div class="flex items-center gap-1.5" title="Online dalam 15 menit terakhir">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span>{{ $onlineCount }} Online</span>
                        </div>
                        <div>{{ $totalThreads }} Topik</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MAIN FEED (Center) -->
        <div class="flex-1 flex flex-col min-w-0 bg-forum-base p-4 sm:p-6 lg:p-8 max-h-[85vh] overflow-y-auto">
            <!-- Header & Search -->
            <div class="bg-forum-card/90 backdrop-blur-xl border border-forum rounded-2xl p-4 mb-6 sticky top-0 z-30 shadow-xl shadow-black/20 flex flex-col sm:flex-row gap-4 items-center justify-between">
                <form method="GET" action="{{ route('forum.index') }}" class="w-full sm:max-w-md relative">
                    @if($category) <input type="hidden" name="category" value="{{ $category }}"> @endif
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-forum-body">
                        <i class="ph-bold ph-magnifying-glass"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" 
                           class="w-full pl-11 pr-4 py-2.5 bg-black/20 hover:bg-black/40 focus:bg-black/60 border border-forum-light focus:border-indigo-500 rounded-xl text-sm text-white placeholder-slate-500 transition outline-none shadow-inner" 
                           placeholder="Cari obrolan, proyek, atau karya...">
                </form>
                
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    @if($search || $category)
                        <a href="{{ route('forum.index') }}" class="px-4 py-2 bg-rose-500/10 text-rose-400 hover:bg-rose-500/20 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                            <i class="ph-bold ph-x-circle"></i> Reset Filter
                        </a>
                    @endif
                    <a href="{{ route('forum.create') }}" class="flex px-5 py-2.5 bg-gradient-to-r from-indigo-500 to-fuchsia-500 hover:from-indigo-400 hover:to-fuchsia-400 text-white rounded-xl text-sm font-bold shadow-lg shadow-indigo-500/25 hover:shadow-indigo-500/40 transition-all items-center gap-2 whitespace-nowrap">
                        <i class="ph-bold ph-plus"></i> Buat Post
                    </a>
                </div>
            </div>

            <!-- Pembda Colabs (Puzzle) Widget -->
            <div class="mb-6 bg-forum-card/90 border border-forum-light rounded-2xl overflow-hidden shadow-xl" x-data="pembdaColabs()">
                <div class="p-4 border-b border-forum-light flex justify-between items-center bg-black/40 cursor-pointer hover:bg-black/60 transition" @click="toggleCollapse()">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-blue-500 flex items-center justify-center shadow-lg shadow-blue-500/20">
                            <i class="ph-bold ph-puzzle-piece text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="forum-hdr text-lg font-bold text-white tracking-tight leading-tight">Pembda COLABS</h2>
                            <span class="text-xs text-forum-body hidden sm:inline">Susun puzzle bersama angkatan. 1 Keping / Hari.</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button @click.stop="showGuide = true" class="text-xs font-bold text-blue-400 bg-blue-500/10 px-3 py-1.5 rounded-lg hover:bg-blue-500/20 transition">
                            <i class="ph-bold ph-book-open mr-1"></i> Panduan
                        </button>
                        <button class="text-forum-muted hover:text-white transition p-1.5 bg-forum-light-5 rounded-lg">
                            <i class="ph-bold ph-caret-down transition-transform duration-300" :class="isCollapsed ? '' : 'rotate-180'"></i>
                        </button>
                    </div>
                </div>
                
                <div x-show="!isCollapsed" x-transition.opacity.duration.300ms class="p-4">
                    <template x-if="puzzle">
                        <div class="flex flex-col md:flex-row gap-4">
                            <!-- Left: Inventory (Available Pieces) -->
                            <div class="w-full md:w-1/3 bg-black/40 rounded-xl border border-forum-light p-3">
                                <h3 class="text-xs font-bold text-forum-muted uppercase tracking-wider mb-3">Kepingan Tersedia</h3>
                                
                                <div x-show="hasPlacedToday" class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-lg text-sm text-blue-300 mb-3 text-center">
                                    <i class="ph-bold ph-check-circle text-2xl mb-1 block"></i>
                                    Kamu sudah menaruh kepingan hari ini! Kembali besok.
                                </div>
                                
                                <div class="flex flex-wrap gap-1 max-h-[300px] overflow-y-auto no-scrollbar justify-center" :class="hasPlacedToday ? 'opacity-50 pointer-events-none' : ''">
                                    <template x-for="idx in inventory" :key="'inv-'+idx">
                                        <button @click="selectPiece(idx)" 
                                                class="w-10 h-10 sm:w-12 sm:h-12 border-2 transition-all duration-200"
                                                :class="selectedPiece === idx ? 'border-white scale-110 z-10 shadow-[0_0_10px_rgba(255,255,255,0.5)]' : 'border-forum-light hover:border-blue-400 hover:scale-105'"
                                                :style="`background-image: url(${puzzle.image_url}); background-size: ${puzzle.grid_x * 100}% ${puzzle.grid_y * 100}%; background-position: ${getBgPos(idx)};`">
                                        </button>
                                    </template>
                                    <div x-show="inventory.length === 0" class="text-sm text-emerald-400 font-bold p-4 text-center w-full">
                                        Puzzle Selesai! 🎉
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Right: Board -->
                            <div class="w-full md:w-2/3 bg-black/60 rounded-xl border border-forum-light p-3 flex flex-col items-center justify-center relative overflow-hidden">
                                <h3 class="text-xs font-bold text-forum-muted uppercase tracking-wider mb-3 w-full text-left" x-text="`${puzzle.title} (${puzzle.progress.percentage}%)`"></h3>
                                
                                <div class="w-full max-w-[500px] aspect-[2/1] relative shadow-2xl bg-forum-card border border-forum-light/50"
                                     :style="`display: grid; grid-template-columns: repeat(${puzzle.grid_x}, 1fr); grid-template-rows: repeat(${puzzle.grid_y}, 1fr);`">
                                    
                                    <template x-for="(piece, i) in board" :key="'board-'+i">
                                        <div class="w-full h-full border-[0.5px] border-white/10 relative group cursor-pointer bg-black/40"
                                             @click="placeAt(i)">
                                             
                                            <!-- Empty Slot -->
                                            <div x-show="!piece" class="absolute inset-0 hover:bg-white/20 transition flex items-center justify-center">
                                                <i x-show="selectedPiece !== null" class="ph-bold ph-plus text-white/50"></i>
                                            </div>
                                            
                                            <!-- Placed Piece -->
                                            <div x-show="piece" class="absolute inset-0"
                                                 :style="`background-image: url(${puzzle.image_url}); background-size: ${puzzle.grid_x * 100}% ${puzzle.grid_y * 100}%; background-position: ${getBgPos(i)};`">
                                            </div>
                                            
                                            <!-- Tooltip -->
                                            <div x-show="piece" class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 bg-black/90 text-white text-[10px] px-2 py-1 rounded pointer-events-none opacity-0 group-hover:opacity-100 transition whitespace-nowrap z-50">
                                                Oleh <span class="font-bold text-blue-400" x-text="piece ? piece.placed_by : ''"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!puzzle">
                        <div class="text-center p-8 text-forum-muted">Sedang memuat puzzle...</div>
                    </template>
                </div>

                <!-- Guide Modal -->
                <div x-show="showGuide" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
                    <div @click.away="showGuide = false" class="bg-forum-card border border-forum-light rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                        <div class="p-5 border-b border-forum-light flex justify-between items-center bg-black/40">
                            <h3 class="forum-hdr text-lg font-bold text-white"><i class="ph-bold ph-puzzle-piece text-blue-400 mr-2"></i> Cara Main Pembda Colabs</h3>
                            <button @click="showGuide = false" class="text-forum-muted hover:text-white"><i class="ph-bold ph-x text-xl"></i></button>
                        </div>
                        <div class="p-5 space-y-4 text-sm text-forum-body">
                            <p><strong class="text-white">1. Pilih Kepingan:</strong> Di sebelah kiri, pilih satu kepingan puzzle yang tersedia.</p>
                            <p><strong class="text-white">2. Letakkan dengan Benar:</strong> Klik salah satu kotak kosong di papan kanan. Jika posisinya salah, kepingan akan ditolak.</p>
                            <p><strong class="text-white">3. Satu Hari, Satu Keping:</strong> Setiap siswa hanya memiliki hak meletakkan 1 kepingan puzzle per hari (berhasil ataupun tidak). Gunakan dengan bijak!</p>
                            <p><strong class="text-blue-400">4. +10 Poin Reputasi:</strong> Jika kepingan diletakkan dengan benar, kamu mendapat tambahan poin!</p>
                            <div class="mt-6 pt-4 border-t border-forum-light text-center">
                                <button @click="showGuide = false" class="px-6 py-2 bg-gradient-to-r from-indigo-500 to-blue-500 hover:from-indigo-400 hover:to-blue-400 text-white font-bold rounded-xl transition w-full shadow-lg shadow-blue-500/25">Mengerti, Ayo Main!</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feed List -->
            <div class="space-y-4 pb-10">
                @forelse($threads as $thread)
                    @php
                        $author = $thread->user;
                        $isLiked = $thread->isLikedBy(auth()->user());
                        $catLabel = $thread->category_label;
                        
                        $catColor = match($thread->category) {
                            'diskusi' => 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30',
                            'sharing' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                            'info' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                            'performance' => 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                            'art_gallery' => 'bg-pink-500/20 text-pink-300 border-pink-500/30',
                            'talent' => 'bg-violet-500/20 text-violet-300 border-violet-500/30',
                            'gaming' => 'bg-rose-500/20 text-rose-300 border-rose-500/30',
                            'tanya_jawab' => 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30',
                            'trending' => 'bg-orange-500/20 text-orange-300 border-orange-500/30',
                            'project_idea' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                            'committee' => 'bg-teal-500/20 text-teal-300 border-teal-500/30',
                            'charity' => 'bg-red-500/20 text-red-300 border-red-500/30',
                            default => 'bg-slate-500/20 text-slate-300 border-slate-500/30'
                        };
                    @endphp
                    
                    <div class="bg-white/[0.03] border border-white/[0.05] hover:border-forum-light rounded-2xl p-5 transition-all duration-300 group {{ $thread->is_pinned ? 'ring-1 ring-amber-500/30 shadow-[0_0_15px_rgba(245,158,11,0.1)]' : '' }}">
                        <a href="{{ route('forum.show', $thread) }}" class="block">
                            <!-- Author & Meta -->
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($author->name) }}&size=40&background=random" 
                                         class="w-10 h-10 rounded-full border border-forum-light">
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-white text-sm">{{ $author->name }}</span>
                                            <span class="text-[10px] font-bold px-1.5 py-0.5 bg-forum-light-10 text-slate-300 rounded uppercase tracking-wider">{{ $author->role }}</span>
                                            <span class="text-xs text-forum-muted">&bull; {{ $thread->created_at->diffForHumans() }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] px-2 py-0.5 rounded-md border font-semibold tracking-wider {{ $catColor }}">
                                                {{ $catLabel }}
                                            </span>
                                            @if($thread->is_pinned)
                                                <span class="text-[10px] px-2 py-0.5 bg-amber-500/10 text-amber-400 border border-amber-500/30 rounded-md font-semibold flex items-center gap-1">
                                                    <i class="ph-bold ph-push-pin"></i> Tersemat
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="pl-13 space-y-3">
                                <h3 class="forum-hdr text-lg md:text-xl font-bold text-forum-title group-hover:text-indigo-400 transition-colors leading-snug">
                                    {{ $thread->title }}
                                </h3>
                                <p class="text-sm text-forum-body line-clamp-2 leading-relaxed">
                                    {{ Str::limit(strip_tags($thread->content), 200) }}
                                </p>

                                @if($thread->image_path)
                                    <div class="mt-3 rounded-xl overflow-hidden border border-forum max-w-sm max-h-48">
                                        <img src="{{ asset('storage/' . $thread->image_path) }}" class="w-full h-full object-cover">
                                    </div>
                                @endif

                                @if($thread->poll)
                                    <div class="mt-3 p-3 bg-forum-light-5 border border-forum-light rounded-xl max-w-sm flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-500/20 flex items-center justify-center"><i class="ph-bold ph-chart-bar text-indigo-400"></i></div>
                                        <div>
                                            <div class="text-xs text-forum-body font-semibold">Polling Interaktif</div>
                                            <div class="text-sm text-white font-medium line-clamp-1">{{ $thread->poll->question }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </a>

                        <!-- Action Bar -->
                        <div class="pl-13 mt-4 flex items-center flex-wrap gap-2 text-xs font-semibold text-forum-body">
                            <!-- Upvote/Like -->
                            <button onclick="toggleLike(this, '{{ route('forum.like', $thread) }}')" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border transition-colors {{ $isLiked ? 'bg-indigo-500/20 border-indigo-500/40 text-indigo-400' : 'bg-transparent border-forum-light hover:bg-forum-light-5' }}">
                                <i class="ph-bold ph-thumbs-up"></i> <span class="likes-count">{{ $thread->likes->count() }}</span>
                            </button>
                            
                            <!-- Replies count -->
                            <a href="{{ route('forum.show', $thread) }}#replies" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-forum-light bg-transparent hover:bg-forum-light-5 transition-colors">
                                <i class="ph-bold ph-chat-circle"></i> {{ $thread->replies->count() }}
                            </a>

                            <!-- Views -->
                            <div class="flex items-center gap-1.5 px-3 py-1.5">
                                <i class="ph-bold ph-eye"></i> {{ $thread->views_count }}
                            </div>

                            @if(in_array($thread->category, ['project_idea', 'committee', 'charity']))
                                <div class="flex items-center gap-1.5 px-3 py-1.5 ml-auto bg-blue-500/10 text-blue-400 rounded-lg">
                                    <i class="ph-bold ph-users"></i> {{ $thread->approvedMembers()->count() }} Tim
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="bg-forum-light-5 border border-forum-light rounded-3xl p-16 text-center mt-10">
                        <div class="w-16 h-16 bg-forum-light-5 rounded-full flex items-center justify-center mx-auto mb-4 border border-forum-light">
                            <i class="ph-bold ph-ghost text-forum-muted text-3xl"></i>
                        </div>
                        <h4 class="forum-hdr text-xl font-bold text-slate-300 mb-2">Masih Sepi Nih</h4>
                        <p class="text-sm text-forum-muted">Belum ada obrolan di saluran ini. Jadilah yang pertama!</p>
                    </div>
                @endforelse

                <!-- Pagination -->
                @if($threads->hasPages())
                    <div class="pt-6 pb-10 flex justify-center">
                        {{ $threads->links('pagination::tailwind') }}
                    </div>
                @endif
            </div>
        </div>

        <!-- SIDEBAR WIDGETS (Right) -->
        <div class="hidden xl:flex flex-col w-72 flex-shrink-0 bg-forum-panel border-l border-forum p-5 gap-5 max-h-[85vh] overflow-y-auto no-scrollbar">
            <!-- User Profile Card -->
            @php
                $user = auth()->user();
                $rep = $user->reputation;
                $pts = $rep->total_points ?? 0;
                $school = $user->school->name ?? 'Pembda';
                
                $next = 100; $rank = 'Perintis'; $color = 'text-indigo-400';
                if ($pts >= 500) { $rank = 'Legenda 👑'; $next = 1000; $color = 'text-amber-400'; }
                elseif ($pts >= 200) { $rank = 'Kontributor 💎'; $next = 500; $color = 'text-cyan-400'; }
                elseif ($pts >= 100) { $rank = 'Warga Aktif 🚀'; $next = 200; $color = 'text-purple-400'; }
                $pct = min(100, round(($pts / $next) * 100));
            @endphp
            <div class="bg-forum-card border border-forum rounded-2xl p-4 shadow-xl relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-fuchsia-500"></div>
                <div class="flex items-center gap-3 mb-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=48&background=random" 
                         class="w-10 h-10 rounded-xl shadow-lg border border-forum-light">
                    <div class="min-w-0">
                        <div class="font-bold text-forum-title truncate text-sm">{{ $user->name }}</div>
                        <div class="text-[10px] text-forum-body uppercase tracking-widest truncate">{{ $school }}</div>
                    </div>
                </div>
                
                <div class="flex justify-between items-end mb-2 text-xs font-bold">
                    <span class="{{ $color }}">{{ $rank }}</span>
                    <span class="text-forum-body">{{ $pts }} / {{ $next }} Pts</span>
                </div>
                <div class="w-full bg-black/40 h-1.5 rounded-full overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-fuchsia-500 h-full rounded-full transition-all" style="width: {{ $pct }}%"></div>
                </div>
            </div>

            <!-- Leaderboard Widget -->
            <div class="bg-forum-card border border-forum rounded-2xl p-4 shadow-xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="forum-hdr text-sm font-bold text-white flex items-center gap-2">
                        <i class="ph-bold ph-trophy text-amber-500"></i> Leaderboard
                    </h3>
                    <a href="{{ route('reputation.leaderboard') }}" class="text-[10px] text-indigo-400 hover:text-indigo-300 uppercase tracking-wider font-bold">Semua</a>
                </div>
                <div class="space-y-3">
                    @foreach($topStudents as $i => $s)
                        <div class="flex items-center gap-2.5">
                            <div class="w-5 h-5 rounded-full bg-forum-light-5 flex items-center justify-center text-[10px] font-bold text-forum-muted">{{ $i+1 }}</div>
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($s->user->name) }}&size=30&background=random" class="w-6 h-6 rounded-full">
                            <div class="min-w-0 flex-1">
                                <div class="text-[11px] font-bold text-slate-200 truncate">{{ $s->user->name }}</div>
                            </div>
                            <div class="text-[11px] font-bold text-indigo-400">{{ $s->total_points }}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Collab Widget -->
            <div class="bg-forum-card border border-forum rounded-2xl p-4 shadow-xl">
                <h3 class="forum-hdr text-sm font-bold text-white flex items-center gap-2 mb-4">
                    <i class="ph-bold ph-handshake text-blue-400"></i> Cari Tim
                </h3>
                <div class="space-y-2.5">
                    @forelse($activeCollabs as $c)
                        <a href="{{ route('forum.show', $c) }}" class="block p-3 bg-forum-light-5 hover:bg-forum-light-10 rounded-xl border border-transparent hover:border-forum-light transition">
                            <div class="text-[9px] text-blue-400 font-bold uppercase mb-1">{{ $c->category_label }}</div>
                            <div class="text-xs font-bold text-slate-200 line-clamp-2 mb-1.5">{{ $c->title }}</div>
                            <div class="flex justify-between items-center text-[10px] text-forum-body">
                                <span class="truncate pr-2">{{ $c->user->name }}</span>
                                <span class="flex-shrink-0">{{ $c->approvedMembers()->count() }} Tim</span>
                            </div>
                        </a>
                    @empty
                        <div class="text-[11px] text-forum-muted italic text-center py-2">Belum ada kolaborasi aktif.</div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

<!-- AJAX Like Script -->
<script>
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

async function toggleLike(btn, url) {
    btn.disabled = true;
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-XSRF-TOKEN': getCsrfToken()
            }
        });
        if (!response.ok) {
            const text = await response.text();
            alert("Like Server Error (" + response.status + "): " + text.substring(0, 500));
            return;
        }
        const result = await response.json();
        if (result.success) {
            const countSpan = btn.querySelector('.likes-count');
            countSpan.textContent = result.likes_count;
            if (result.liked) {
                btn.classList.remove('bg-transparent', 'border-forum-light', 'hover:bg-forum-light-5');
                btn.classList.add('bg-indigo-500/20', 'border-indigo-500/40', 'text-indigo-400');
            } else {
                btn.classList.remove('bg-indigo-500/20', 'border-indigo-500/40', 'text-indigo-400');
                btn.classList.add('bg-transparent', 'border-forum-light', 'hover:bg-forum-light-5');
            }
        }
    } catch (error) {
        alert('Like JS Catch Error: ' + error.message);
    } finally {
        btn.disabled = false;
    }
}

function pembdaColabs() {
    return {
        puzzle: null,
        pieces: [],
        hasPlacedToday: false,
        showGuide: false,
        isCollapsed: false,
        selectedPiece: null, 
        inventory: [], 
        board: [],
        
        async init() {
            const savedState = localStorage.getItem('pembdaColabsCollapsed');
            if (savedState !== null) this.isCollapsed = savedState === 'true';
            
            await this.fetchState();
            setInterval(() => {
                if(!this.isCollapsed) this.fetchState();
            }, 5000);
        },
        
        async fetchState() {
            try {
                const res = await fetch('{{ route("forum.puzzle.state") }}');
                const data = await res.json();
                if(data.success) {
                    this.puzzle = data.puzzle;
                    this.hasPlacedToday = data.has_placed_today;
                    this.pieces = data.pieces;
                    this.rebuildBoard();
                }
            } catch(e) {}
        },
        
        rebuildBoard() {
            if(!this.puzzle) return;
            const total = this.puzzle.grid_x * this.puzzle.grid_y;
            let newBoard = new Array(total).fill(null);
            let placedIndices = new Set();
            
            this.pieces.forEach(p => {
                if(p.is_placed) {
                    newBoard[p.index] = p;
                    placedIndices.add(p.index);
                }
            });
            this.board = newBoard;
            
            let newInv = [];
            for(let i=0; i<total; i++) {
                if(!placedIndices.has(i)) newInv.push(i);
            }
            if(this.inventory.length !== newInv.length) {
                this.inventory = newInv.sort(() => Math.random() - 0.5);
            }
        },
        
        toggleCollapse() {
            this.isCollapsed = !this.isCollapsed;
            localStorage.setItem('pembdaColabsCollapsed', this.isCollapsed);
            if(!this.isCollapsed) this.fetchState();
        },
        
        getBgPos(index) {
            if(!this.puzzle) return '0 0';
            const col = index % this.puzzle.grid_x;
            const row = Math.floor(index / this.puzzle.grid_x);
            const x = this.puzzle.grid_x > 1 ? (col / (this.puzzle.grid_x - 1)) * 100 : 0;
            const y = this.puzzle.grid_y > 1 ? (row / (this.puzzle.grid_y - 1)) * 100 : 0;
            return `${x}% ${y}%`;
        },
        
        selectPiece(index) {
            if(this.hasPlacedToday) {
                alert("Anda sudah meletakkan kepingan hari ini! Tunggu besok.");
                return;
            }
            this.selectedPiece = index;
        },
        
        async placeAt(targetIndex) {
            if(this.selectedPiece === null) return;
            
            const pieceIdx = this.selectedPiece;
            this.selectedPiece = null; 
            
            try {
                const res = await fetch('{{ route("forum.puzzle.place") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-XSRF-TOKEN': getCsrfToken()
                    },
                    body: JSON.stringify({
                        puzzle_id: this.puzzle.id,
                        piece_index: pieceIdx,
                        target_index: targetIndex
                    })
                });
                
                const data = await res.json();
                if(data.success) {
                    alert(data.message);
                    this.hasPlacedToday = true;
                    this.fetchState();
                } else {
                    alert(data.message);
                }
            } catch(e) {
                alert("Error koneksi saat meletakkan puzzle.");
            }
        }
    }
}
</script>
@endsection
