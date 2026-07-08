@extends(auth()->user()->layout)

@section('title', 'Wadah Ekspresi & Kolaborasi Siswa')

@section('content')
<!-- Dynamic Google Fonts & Phosphor Icons -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;650;700&display=swap" rel="stylesheet">
<style>
    .forum-hdr {
        font-family: 'Space Grotesk', sans-serif;
    }
</style>

<div class="forum-custom-workspace space-y-8 py-6 w-full max-w-full px-4 sm:px-8 xl:px-12">
    <!-- Header Section (Bright, Vibrant Gradient Banner for Youth) -->
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 rounded-[2.5rem] p-10 lg:p-14 text-white shadow-2xl relative overflow-hidden ring-1 ring-white/20">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/20 rounded-full blur-[80px]"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-white/20 rounded-full blur-[80px]"></div>
        
        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="space-y-5">
                <div class="inline-flex items-center gap-2 bg-white text-indigo-600 shadow-[0_0_20px_rgba(255,255,255,0.4)] px-5 py-2 rounded-full text-xs font-black shadow-md border border-white/50">
                    <i class="ph-bold ph-lightning animate-pulse text-sm"></i>
                    <span class="uppercase tracking-widest">KOMUNITAS PEMBDA HUB</span>
                </div>
                <h1 class="forum-hdr text-4xl lg:text-6xl font-black tracking-tight leading-none text-white drop-shadow-sm">
                    Wadah Ekspresi & Mabar Kolaborasi! 🚀
                </h1>
                <p class="text-base md:text-lg text-white/95 max-w-2xl font-bold leading-relaxed drop-shadow-sm">
                    Tempatnya anak-anak Pembda pamer karya gokil, unjuk bakat musik, info mabar e-sports, kolaborasi proyek seru, dan kumpulin poin reputasi Elite!
                </p>
            </div>
            <div class="flex flex-col sm:flex-row gap-4">
                <a href="{{ route('forum.create') }}" 
                   class="inline-flex items-center justify-center gap-2.5 px-8 py-5 bg-white hover:bg-slate-50 text-indigo-600 hover:text-purple-600 rounded-2xl font-black text-base shadow-[0_10px_25px_rgba(0,0,0,0.15)] hover:shadow-[0_15px_35px_rgba(0,0,0,0.2)] hover:scale-105 transition duration-300">
                    <i class="ph-bold ph-plus-circle text-lg"></i>
                    Ekspresikan Dirimu ⚡
                </a>
            </div>
        </div>
    </div>

    <!-- Main Grid Content (Two Columns - Maximized Width) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Filter and Feed (70%) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Search & Count Header -->
            <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
                <!-- Search Form -->
                <form method="GET" action="{{ route('forum.index') }}" class="w-full sm:max-w-xl relative">
                    @if($category)
                        <input type="hidden" name="category" value="{{ $category }}">
                    @endif
                    <input type="text" name="search" value="{{ $search }}" 
                           class="w-full pl-12 pr-4 py-4 bg-slate-50/50 hover:bg-slate-100 focus:bg-white border-2 border-slate-150 focus:border-indigo-500 rounded-xl text-base font-bold text-slate-700 transition outline-none" 
                           placeholder="Cari karya gokil, obrolan santai, tim mabar...">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">
                        <i class="ph-bold ph-magnifying-glass text-base"></i>
                    </div>
                </form>

                <div class="text-sm text-slate-655 font-black px-2 flex items-center gap-3 self-stretch sm:self-auto justify-between sm:justify-start">
                    <span>Menemukan <strong class="text-indigo-650 font-black text-base">{{ $threads->total() }}</strong> postingan</span>
                    @if($search || $category)
                        <a href="{{ route('forum.index') }}" class="text-rose-600 hover:text-rose-700 flex items-center gap-1.5 font-black bg-rose-50 px-4 py-2 rounded-xl border border-rose-200 text-xs">
                            <i class="ph-bold ph-x-circle"></i> Mulai dari Awal 🔄
                        </a>
                    @endif
                </div>
            </div>

            <!-- Categories Tabs (Sleek Horizontal Capsules) -->
            <div class="bg-white/90 backdrop-blur-lg rounded-3xl border border-slate-200/50 shadow-xl p-6 shadow-sm space-y-4">
                <span class="block text-sm font-black text-slate-500 uppercase tracking-widest px-1">Pilih Saluran Obrolan</span>
                <div class="flex flex-wrap gap-2.5">
                    <a href="{{ route('forum.index', array_filter(['search' => $search])) }}" 
                       class="px-5 py-3 rounded-xl text-sm font-black transition flex items-center gap-2 border-2 {{ !$category ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white border-transparent shadow-md shadow-indigo-600/10' : 'bg-slate-550/5 hover:bg-slate-100 text-slate-700 border-slate-200/80' }}">
                        <i class="ph-bold ph-squares-four text-sm"></i>
                        Semua Kategori
                    </a>
                    
                    @foreach(\App\Models\ForumThread::CATEGORIES as $key => $label)
                        @php
                            $isActive = $category === $key;
                            $badgeCount = $counts[$key] ?? 0;
                            
                            $icon = match($key) {
                                'diskusi' => 'fa-comments',
                                'sharing' => 'fa-file-alt',
                                'info' => 'fa-bullhorn',
                                'performance' => 'fa-trophy',
                                'art_gallery' => 'fa-palette',
                                'talent' => 'fa-music',
                                'gaming' => 'fa-gamepad',
                                'portfolio' => 'fa-certificate',
                                'project_idea' => 'fa-lightbulb',
                                'committee' => 'fa-users',
                                'charity' => 'fa-heart',
                                default => 'fa-tag'
                            };
                            
                            $colorClass = match($key) {
                                'diskusi' => 'hover:border-indigo-400 hover:text-indigo-700',
                                'sharing' => 'hover:border-emerald-400 hover:text-emerald-700',
                                'info' => 'hover:border-amber-400 hover:text-amber-700',
                                'performance' => 'hover:border-purple-400 hover:text-purple-700',
                                'art_gallery' => 'hover:border-pink-400 hover:text-pink-700',
                                'talent' => 'hover:border-violet-400 hover:text-violet-700',
                                'gaming' => 'hover:border-rose-400 hover:text-rose-700',
                                'portfolio' => 'hover:border-cyan-400 hover:text-cyan-700',
                                'project_idea' => 'hover:border-blue-400 hover:text-blue-700',
                                'committee' => 'hover:border-teal-400 hover:text-teal-700',
                                'charity' => 'hover:border-red-400 hover:text-red-700',
                                default => 'hover:border-slate-400'
                            };
                        @endphp
                        <a href="{{ route('forum.index', array_filter(['category' => $key, 'search' => $search])) }}" 
                           class="px-5 py-3 rounded-xl text-sm font-black transition flex items-center gap-2.5 border-2 {{ $isActive ? 'bg-gradient-to-r from-indigo-600 to-purple-600 text-white border-transparent shadow-md shadow-indigo-600/10' : 'bg-slate-550/5 text-slate-700 border-slate-200/80 ' . $colorClass }}">
                            <i class="fa-solid {{ $icon }} text-sm"></i>
                            {{ $label }}
                            @if($badgeCount > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black {{ $isActive ? 'bg-lime-400 text-slate-900 shadow-[0_0_20px_rgba(163,230,53,0.4)]' : 'bg-slate-200 text-slate-700' }}">{{ $badgeCount }}</span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Feed Content -->
            @if(in_array($category, ['performance', 'art_gallery', 'talent', 'gaming']))
                <!-- --- VIEW: SHOWCASE VISUAL GRID (Maximized Font Sizes & Glowing Category Shadows) --- -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    @forelse($threads as $thread)
                        @php
                            $isLiked = $thread->isLikedBy(auth()->user());
                            $author = $thread->user;
                            $schoolName = $author->school->name ?? 'Yayasan Perguruan Pembda';
                            
                            $reputationPoints = $author->reputation->total_points ?? 0;
                            $glowRing = 'border-slate-200';
                            if ($author->isGuru()) {
                                $glowRing = 'ring-4 ring-indigo-500 ring-offset-2';
                            } elseif ($reputationPoints > 200) {
                                $glowRing = 'ring-4 ring-amber-400 ring-offset-2';
                            } elseif ($reputationPoints > 100) {
                                $glowRing = 'ring-4 ring-slate-400 ring-offset-2';
                            }

                            // Dynamic Glow Shadow matching category
                            $cardGlow = match($thread->category) {
                                'performance' => 'shadow-lg shadow-purple-500/5 hover:shadow-purple-500/20 hover:border-purple-300',
                                'art_gallery' => 'shadow-lg shadow-pink-500/5 hover:shadow-pink-500/20 hover:border-pink-300',
                                'talent' => 'shadow-lg shadow-violet-500/5 hover:shadow-violet-500/20 hover:border-violet-300',
                                'gaming' => 'shadow-lg shadow-rose-500/5 hover:shadow-rose-500/20 hover:border-rose-300',
                                default => 'hover:shadow-indigo-500/10 hover:border-indigo-300'
                            };
                        @endphp
                        
                        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-slate-200/60 shadow-lg overflow-hidden transition duration-300 flex flex-col justify-between group {{ $cardGlow }}">
                            <!-- Visual Header -->
                            <div class="h-56 overflow-hidden bg-slate-50/50 relative flex-shrink-0">
                                @if($thread->image_path)
                                    <img src="{{ asset('storage/' . $thread->image_path) }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                @else
                                    <div class="w-full h-full bg-gradient-to-tr from-indigo-500/10 via-purple-500/10 to-pink-500/10 flex flex-col items-center justify-center p-6 text-center border-b border-slate-100">
                                        <div class="w-14 h-14 rounded-full bg-white flex items-center justify-center shadow-sm mb-4">
                                            <i class="ph-bold ph-sparkle text-indigo-550 text-xl animate-bounce"></i>
                                        </div>
                                        <blockquote class="text-sm font-bold text-slate-650 italic line-clamp-3 px-2">
                                            "{{ strip_tags($thread->content) }}"
                                        </blockquote>
                                    </div>
                                @endif
                                <div class="absolute top-4 left-4 bg-slate-900/90 text-white text-[10px] font-black px-3.5 py-1.5 rounded-lg uppercase tracking-wider">
                                    {{ $thread->category_label }}
                                </div>
                            </div>

                            <!-- Showcase Info -->
                            <div class="p-6 flex-1 flex flex-col justify-between space-y-5">
                                <div class="space-y-3">
                                    <a href="{{ route('forum.show', $thread) }}">
                                        <h4 class="forum-hdr font-black text-slate-800 text-xl lg:text-2xl leading-tight group-hover:text-indigo-600 transition line-clamp-2">
                                            {{ $thread->title }}
                                        </h4>
                                    </a>
                                    <p class="text-sm md:text-base font-semibold text-slate-655 line-clamp-2 leading-relaxed">
                                        {{ strip_tags($thread->content) }}
                                    </p>
                                </div>

                                <!-- Connected Reference Badge / Exam score -->
                                @if($thread->reference_type)
                                    <div class="p-3.5 rounded-xl bg-indigo-50/50 border border-indigo-100/60 flex items-center gap-3 text-sm">
                                        <i class="ph-bold ph-medal text-indigo-600 text-lg"></i>
                                        <span class="font-extrabold text-slate-700 truncate">
                                            @if($thread->reference_type === \App\Models\Badge::class)
                                                🎖️ Lencana: {{ \App\Models\Badge::find($thread->reference_id)->name ?? 'Prestasi' }}
                                            @else
                                                💯 Nilai CBT: {{ \App\Models\CbtExamResult::find($thread->reference_id)->final_score ?? 'Lulus' }}
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <!-- User Details & Interaction -->
                                <div class="border-t border-slate-100 pt-5 flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($author->name) }}&size=36&background=random" 
                                             class="w-9 h-9 rounded-full flex-shrink-0 {{ $glowRing }}">
                                        <div class="min-w-0 text-xs">
                                            <span class="font-black text-slate-800 truncate block">{{ $author->name }}</span>
                                            <span class="text-slate-500 font-extrabold block truncate mt-0.5">{{ $schoolName }}</span>
                                        </div>
                                    </div>

                                    <!-- Quick Congratulate / Upvote -->
                                    <div class="flex items-center gap-2.5 flex-wrap notranslate" translate="no">
                                        <!-- Detail Button -->
                                        <a href="{{ route('forum.show', $thread) }}" class="w-10 h-10 rounded-full bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border-2 border-indigo-200 flex items-center justify-center transition shadow-sm" title="Lihat Detail">
                                            <i class="ph-bold ph-eye text-sm"></i>
                                        </a>

                                        <!-- Edit Button (if authorized) -->
                                        @if(auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru())
                                            <a href="{{ route('forum.edit', $thread) }}" class="w-10 h-10 rounded-full bg-amber-50 hover:bg-amber-500 text-amber-800 hover:text-white border-2 border-amber-200 flex items-center justify-center transition shadow-sm" title="Edit Postingan">
                                                <i class="ph-bold ph-pen-to-square text-sm"></i>
                                            </a>
                                            <form action="{{ route('forum.destroy', $thread) }}" method="POST" class="inline" onsubmit="return confirm('Yakin mau hapus postingan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-10 h-10 rounded-full bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border-2 border-rose-200 flex items-center justify-center transition shadow-sm" title="Hapus Postingan">
                                                    <i class="ph-bold ph-trash-can text-sm"></i>
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('forum.show', $thread) }}#replies" class="w-10 h-10 rounded-full bg-slate-105 hover:bg-slate-800 text-slate-700 hover:text-white border-2 border-slate-200 flex items-center justify-center transition relative shadow-sm" title="Komentar">
                                            <i class="ph-bold ph-chat-circle text-sm"></i>
                                            @if($thread->replies->count() > 0)
                                                <span class="absolute -top-1.5 -right-1.5 bg-slate-800 text-white text-[9px] font-black px-2 py-0.5 rounded-full">{{ $thread->replies->count() }}</span>
                                            @endif
                                        </a>

                                        <button onclick="toggleLike(this, '{{ route('forum.like', $thread) }}')" 
                                                class="h-10 px-4 rounded-full flex items-center gap-2 transition text-xs font-black border-2 {{ $isLiked ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-slate-600 border-slate-250 hover:bg-slate-50/50' }}">
                                            <i class="ph-bold ph-thumbs-up text-sm"></i>
                                            <span class="likes-count">{{ $thread->likes->count() }}</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-2 bg-white rounded-3xl border border-slate-100 p-20 text-center shadow-sm">
                            <div class="w-20 h-20 bg-slate-50/50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-150">
                                <i class="ph-bold ph-images text-slate-400 text-3xl"></i>
                            </div>
                            <h4 class="text-xl font-black text-slate-850 mb-2">Panggung Masih Kosong Nih! 🎬</h4>
                            <p class="text-base text-slate-550 max-w-md mx-auto font-medium">Belum ada karya keren atau postingan ekspresi di sini. Yuk, jadi yang pertama memamerkan bakatmu!</p>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- --- VIEW: STANDARD FEED (Glowing Custom Shadows for text categories) --- -->
                <div class="space-y-5">
                    @forelse($threads as $thread)
                        @php
                            $isCollab = in_array($thread->category, ['project_idea', 'committee', 'charity']);
                            $isLiked = $thread->isLikedBy(auth()->user());
                            $schoolName = $thread->user->school->name ?? 'Yayasan Perguruan Pembda';
                            
                            $borderLeftColor = match($thread->category) {
                                'diskusi' => 'border-l-4 border-l-indigo-600',
                                'sharing' => 'border-l-4 border-l-emerald-500',
                                'info' => 'border-l-4 border-l-amber-500',
                                'art_gallery' => 'border-l-4 border-l-pink-500',
                                'talent' => 'border-l-4 border-l-violet-500',
                                'gaming' => 'border-l-4 border-l-rose-500',
                                'portfolio' => 'border-l-4 border-l-cyan-500',
                                'project_idea' => 'border-l-4 border-l-blue-500',
                                'committee' => 'border-l-4 border-l-teal-500',
                                'charity' => 'border-l-4 border-l-rose-500',
                                default => 'border-l-4 border-l-slate-200'
                            };

                            $statusColor = match($thread->status) {
                                'seeking_members' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                'active' => 'bg-blue-50 text-blue-800 border-blue-200',
                                'completed' => 'bg-slate-100 text-slate-700 border-slate-200',
                                default => 'bg-slate-50/50 text-slate-700 border-slate-200'
                            };
                            $statusLabel = match($thread->status) {
                                'seeking_members' => 'Lagi Cari Tim 🤝',
                                'active' => 'Lagi Jalan ⚡',
                                'completed' => 'Misi Selesai 🏆',
                                default => $thread->status
                            };

                            // Elegant glow shadows for standard feed
                            $stdGlow = match($thread->category) {
                                'diskusi' => 'shadow-lg shadow-indigo-500/5 hover:shadow-indigo-500/10 hover:border-indigo-250',
                                'sharing' => 'shadow-lg shadow-emerald-500/5 hover:shadow-emerald-500/10 hover:border-emerald-250',
                                'info' => 'shadow-lg shadow-amber-500/5 hover:shadow-amber-500/10 hover:border-amber-250',
                                'project_idea' => 'shadow-lg shadow-blue-500/5 hover:shadow-blue-500/10 hover:border-blue-250',
                                'committee' => 'shadow-lg shadow-teal-500/5 hover:shadow-teal-500/10 hover:border-teal-250',
                                'charity' => 'shadow-lg shadow-rose-500/5 hover:shadow-rose-500/10 hover:border-rose-250',
                                default => 'hover:border-slate-200 hover:shadow-md'
                            };
                        @endphp
                        
                        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-slate-200/60 shadow-lg {{ $borderLeftColor }} p-6 shadow-sm transition duration-300 flex flex-col md:flex-row gap-6 items-start justify-between {{ $stdGlow }}">
                            <!-- Left Info -->
                            <div class="flex-1 space-y-4 min-w-0 w-full">
                                <div class="flex flex-wrap items-center gap-3 text-[11px] font-black uppercase tracking-wider">
                                    <span class="px-3 py-1 bg-slate-100 text-slate-800 rounded-md">
                                        {{ $thread->category_label }}
                                    </span>
                                    
                                    @if($isCollab)
                                        <span class="px-3 py-1 border-2 {{ $statusColor }} rounded-md">
                                            {{ $statusLabel }}
                                        </span>
                                    @endif

                                    @if($thread->is_pinned)
                                        <span class="px-3 py-1 bg-amber-550/10 text-amber-800 border-2 border-amber-300 rounded-md">
                                            <i class="ph-bold ph-thumbtack mr-1"></i> Tersemat
                                        </span>
                                    @endif

                                    <span class="text-slate-500 font-extrabold tracking-normal normal-case">
                                        {{ $thread->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <div class="space-y-2">
                                    <a href="{{ route('forum.show', $thread) }}" class="group">
                                        <h3 class="forum-hdr text-lg md:text-xl font-black text-slate-850 group-hover:text-indigo-600 transition tracking-tight leading-snug">
                                            {{ $thread->title }}
                                        </h3>
                                    </a>
                                    <p class="text-sm md:text-base font-semibold text-slate-655 line-clamp-2 leading-relaxed">
                                        {{ strip_tags($thread->content) }}
                                    </p>
                                </div>

                                <!-- Progress for charity -->
                                @if($thread->category === 'charity')
                                    @php
                                        $hasTarget = !empty($thread->charity_target_amount);
                                        $pct = 0;
                                        if ($hasTarget && $thread->charity_target_amount > 0) {
                                            $pct = min(100, round(($thread->charity_current_amount / $thread->charity_target_amount) * 100));
                                        }
                                    @endphp
                                    <div class="space-y-2 bg-slate-50/50 p-4 rounded-xl border border-slate-150 max-w-sm">
                                        <div class="flex justify-between items-center text-[10px] font-black text-slate-500 uppercase tracking-wider">
                                            <span>Dana Terkumpul</span>
                                            <span>Goal: Rp {{ number_format($thread->charity_target_amount ?? 0, 0, ',', '.') }}</span>
                                        </div>
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-base font-black text-slate-800">Rp {{ number_format($thread->charity_current_amount, 0, ',', '.') }}</span>
                                            @if($hasTarget)
                                                <span class="text-xs text-indigo-650 font-black">({{ $pct }}%)</span>
                                            @endif
                                        </div>
                                        @if($hasTarget)
                                            <div class="w-full bg-slate-200 h-2 rounded-full overflow-hidden border border-slate-100">
                                                <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <!-- Footer details -->
                                <div class="flex flex-wrap items-center gap-5 text-xs text-slate-500 font-black border-t border-slate-100 pt-4 notranslate" translate="no">
                                    <div class="flex items-center gap-2">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($thread->user->name) }}&size=24&background=random" class="w-6 h-6 rounded-full shadow-sm border border-slate-100">
                                        <span class="text-slate-700 truncate max-w-[150px]">{{ $thread->user->name }}</span>
                                        <span class="text-slate-300 font-black">|</span>
                                        <span class="text-slate-600">{{ $schoolName }}</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span><i class="ph-bold ph-eye mr-1"></i>{{ $thread->views_count }}</span>
                                        <span><i class="ph-bold ph-thumbs-up mr-1"></i>{{ $thread->likes->count() }} Upvote</span>
                                        <span><i class="ph-bold ph-chat-circle mr-1"></i>{{ $thread->replies->count() }} Balasan</span>
                                        @if($isCollab)
                                            <span><i class="ph-bold ph-user-group mr-1"></i>{{ $thread->approvedMembers()->count() }} Anggota Tim</span>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-3 flex-wrap sm:ml-auto">
                                        <!-- Detail Button -->
                                        <a href="{{ route('forum.show', $thread) }}" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-700 hover:text-white border-2 border-indigo-200 rounded-xl transition text-[11px] font-black uppercase flex items-center gap-1.5 shadow-sm" title="Lihat Detail">
                                            <i class="ph-bold ph-eye"></i> Detail
                                        </a>

                                        <!-- Edit Button (if authorized) -->
                                        @if(auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru())
                                            <a href="{{ route('forum.edit', $thread) }}" class="px-4 py-2 bg-amber-50 hover:bg-amber-500 text-amber-800 hover:text-white border-2 border-amber-200 rounded-xl transition text-[11px] font-black uppercase flex items-center gap-1.5 shadow-sm" title="Edit Postingan">
                                                <i class="ph-bold ph-pen-to-square"></i> Edit
                                            </a>
                                            <form action="{{ route('forum.destroy', $thread) }}" method="POST" class="inline" onsubmit="return confirm('Yakin mau hapus postingan ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white border-2 border-rose-250 rounded-xl transition text-[11px] font-black uppercase flex items-center gap-1.5 shadow-sm" title="Hapus Postingan">
                                                    <i class="ph-bold ph-trash-can"></i> Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right Info (Image thumbnail if exists) -->
                            @if($thread->image_path)
                                <div class="w-24 h-24 bg-slate-50/50 border border-slate-150 rounded-2xl overflow-hidden shadow-sm flex-shrink-0 self-center hidden sm:block">
                                    <img src="{{ asset('storage/' . $thread->image_path) }}" class="w-full h-full object-cover">
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="bg-white/80 backdrop-blur-xl rounded-3xl border border-slate-200/60 shadow-lg p-20 text-center shadow-sm">
                            <div class="w-20 h-20 bg-slate-50/50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-150">
                                <i class="ph-bold ph-inbox text-slate-400 text-3xl"></i>
                            </div>
                            <h4 class="text-xl font-black text-slate-850 mb-2">Masih Sunyi Sepi... 🍃</h4>
                            <p class="text-base text-slate-550 font-medium">Belum ada obrolan di kategori ini. Yuk, bikin postingan pertamamu!</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                @if($threads->hasPages())
                    <div class="py-5">
                        {{ $threads->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- Right Column: Sidebar Widgets (30%) -->
        <div class="lg:col-span-4 space-y-6">
            
            <!-- WIDGET 1: USER REPUTATION & PROFILE -->
            @php
                $user = auth()->user();
                $userRep = $user->reputation;
                $currentPoints = $userRep->total_points ?? 0;
                $earnedBadgesCount = $user->badges()->count();
                $schoolName = $user->school->name ?? 'Yayasan Perguruan Pembda';
                
                $nextRankPoints = 100;
                $rankTitle = 'Siswa Perintis 🌟';
                if ($currentPoints >= 500) {
                    $rankTitle = 'Legenda Pembda 👑';
                    $nextRankPoints = 1000;
                } elseif ($currentPoints >= 200) {
                    $rankTitle = 'Kontributor Kece 💎';
                    $nextRankPoints = 500;
                } elseif ($currentPoints >= 100) {
                    $rankTitle = 'Warga Aktif 🚀';
                    $nextRankPoints = 200;
                }
                
                $progressPercent = min(100, round(($currentPoints / $nextRankPoints) * 100));
            @endphp
            <div class="bg-white/90 backdrop-blur-lg rounded-3xl border border-slate-200/50 shadow-xl p-6 shadow-sm space-y-5">
                <div class="flex items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&size=48&background=random" 
                         class="w-14 h-14 rounded-full border border-slate-100 shadow-sm ring-4 ring-indigo-500/10 animate-pulse">
                    <div class="min-w-0">
                        <h4 class="font-black text-slate-800 text-base truncate leading-snug">{{ $user->name }}</h4>
                        <span class="text-xs text-slate-550 font-black truncate block mt-1">{{ $schoolName }}</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 bg-slate-50/50 p-4 rounded-xl border border-slate-150 text-center">
                    <div>
                        <span class="text-[10px] text-slate-500 font-black uppercase tracking-wider block">🔥 Reputasi Poin</span>
                        <span class="text-3xl font-black text-indigo-650 block mt-0.5">{{ $currentPoints }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-500 font-black uppercase tracking-wider block">🎖️ Lencana</span>
                        <span class="text-3xl font-black text-slate-800 block mt-0.5">{{ $earnedBadgesCount }}</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center text-xs font-black">
                        <span class="text-indigo-600">{{ $rankTitle }}</span>
                        <span class="text-slate-550">{{ $currentPoints }}/{{ $nextRankPoints }} Poin</span>
                    </div>
                    <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden border border-slate-100">
                        <div class="bg-indigo-600 h-full rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                    </div>
                </div>
            </div>

            <!-- WIDGET 2: HALL OF FAME / TOP STUDENTS LEADERBOARD -->
            <div class="bg-white/90 backdrop-blur-lg rounded-3xl border border-slate-200/50 shadow-xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h4 class="forum-hdr font-black text-slate-850 text-sm flex items-center gap-1.5">
                        <i class="ph-bold ph-medal text-amber-500 animate-pulse text-base"></i>
                        Papan Reputasi Tergokil (Hall of Fame)
                    </h4>
                    <a href="{{ route('reputation.leaderboard') }}" class="text-xs font-black text-indigo-650 hover:text-indigo-750">Lihat Semua</a>
                </div>

                <div class="space-y-3.5">
                    @forelse($topStudents as $index => $rep)
                        @php
                            $medalColor = match($index) {
                                0 => 'text-amber-400 text-lg',
                                1 => 'text-slate-400 text-base',
                                2 => 'text-amber-600 text-sm',
                                default => 'text-slate-300 text-xs'
                            };
                            
                            $medalIcon = match($index) {
                                0 => 'fa-crown',
                                1 => 'fa-medal',
                                2 => 'fa-medal',
                                default => 'fa-award'
                            };
                            
                            $studentSchool = $rep->user->school->name ?? 'Yayasan Pembda';
                        @endphp
                        <div class="flex items-center justify-between gap-3 p-2 rounded-xl hover:bg-slate-50/50 transition border border-transparent hover:border-slate-100">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-8 h-8 flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $medalIcon }} {{ $medalColor }}"></i>
                                </div>
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($rep->user->name) }}&size=28&background=random" class="w-7 h-7 rounded-full flex-shrink-0 shadow-sm border border-slate-100">
                                <div class="min-w-0">
                                    <span class="text-sm font-black text-slate-800 block truncate leading-none mb-1.5">{{ $rep->user->name }}</span>
                                    <span class="text-[10px] text-slate-550 font-black block truncate">{{ $studentSchool }}</span>
                                </div>
                            </div>
                            <span class="text-xs font-black text-indigo-650 flex-shrink-0">{{ $rep->total_points }} Poin</span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic text-center py-4 font-bold">Papan peringkat belum tersedia.</p>
                    @endforelse
                </div>
            </div>

            <!-- WIDGET 3: ACTIVE COLLABORATIONS -->
            <div class="bg-white/90 backdrop-blur-lg rounded-3xl border border-slate-200/50 shadow-xl p-6 shadow-sm space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <h4 class="forum-hdr font-black text-slate-850 text-sm flex items-center gap-1.5">
                        <i class="ph-bold ph-handshake text-indigo-500 text-base"></i>
                        ⚡ Ajakan Mabar & Proyek Seru
                    </h4>
                </div>

                <div class="space-y-4">
                    @forelse($activeCollabs as $collab)
                        <div class="p-4 rounded-xl bg-slate-50/50 border border-slate-150 hover:border-indigo-200 transition space-y-2.5">
                            <div class="flex justify-between items-start gap-2">
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[9px] font-black uppercase rounded">
                                    {{ $collab->category_label }}
                                </span>
                                <span class="text-[10px] text-slate-500 font-bold">{{ $collab->created_at->diffForHumans() }}</span>
                            </div>
                            <a href="{{ route('forum.show', $collab) }}" class="block">
                                <h5 class="text-sm font-black text-slate-800 hover:text-indigo-600 transition line-clamp-1 leading-snug">
                                    {{ $collab->title }}
                                </h5>
                            </a>
                            <div class="flex justify-between items-center text-[10px] text-slate-500 font-black pt-2.5 border-t border-slate-200/50">
                                <span>Bikinan: <strong class="text-slate-700 font-black">{{ $collab->user->name }}</strong></span>
                                <span class="text-slate-655"><i class="ph-bold ph-users mr-1"></i>{{ $collab->approvedMembers()->count() }} Anggota Tim</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic text-center py-4 font-bold">Belum ada ajakan kolaborasi nih. Bikin proyek pertama kamu yuk!</p>
                    @endforelse
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- AJAX Like Script -->
<script>
async function toggleLike(btn, url) {
    btn.disabled = true;
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSR-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        const result = await response.json();
        if (result.success) {
            const countSpan = btn.querySelector('.likes-count');
            countSpan.textContent = result.likes_count;
            if (result.liked) {
                btn.classList.remove('bg-white', 'text-slate-600', 'border-slate-250');
                btn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            } else {
                btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                btn.classList.add('bg-white', 'text-slate-600', 'border-slate-250');
            }
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        btn.disabled = false;
    }
}
</script>
@endsection
