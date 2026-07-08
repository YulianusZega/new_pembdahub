@extends(auth()->user()->layout)

@section('title', $thread->title)

@section('content')
<!-- Dynamic Google Fonts & Phosphor Icons -->
<script src="https://unpkg.com/@phosphor-icons/web"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;650;700&display=swap" rel="stylesheet">
<style>
    .forum-hdr {
        font-family: 'Space Grotesk', sans-serif;
    }
</style>

<div class="forum-custom-workspace max-w-full w-full px-4 sm:px-8 xl:px-12 py-6 space-y-8">
    <!-- Back & Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('forum.index') }}" class="inline-flex items-center gap-2 text-base font-black text-slate-650 hover:text-indigo-600 transition">
            <i class="ph-bold ph-chevron-left"></i> Kembali ke Beranda Forum 🔙
        </a>

        @if(auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru())
        <div class="flex items-center gap-3 notranslate" translate="no">
            <a href="{{ route('forum.edit', $thread) }}" class="px-5 py-2.5 border-2 border-amber-250 text-amber-750 bg-amber-50 hover:bg-amber-100 rounded-xl text-sm font-bold uppercase tracking-wider transition">
                <i class="ph-bold ph-pen-to-square mr-1"></i> Edit Postingan ✏️
            </a>

            <form action="{{ route('forum.destroy', $thread) }}" method="POST" onsubmit="return confirm('Yakin mau hapus postingan ini? Semua poin reputasi yang kamu dapet dari post ini bakal ditarik balik lho!')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-5 py-2.5 border-2 border-rose-250 text-rose-600 bg-rose-50 hover:bg-rose-100 rounded-xl text-sm font-bold uppercase tracking-wider transition">
                    <i class="ph-bold ph-trash-can mr-1"></i> Hapus Postingan 🗑️
                </button>
            </form>
        </div>
        @endif
    </div>

    <!-- Alerts -->
    @if(session('success'))
    <div class="px-5 py-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center gap-3.5">
        <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0"><i class="ph-bold ph-check text-emerald-600"></i></div>
        <span class="font-bold text-base">{{ session('success') }}</span>
    </div>
    @endif
    @if(session('error'))
    <div class="px-5 py-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center gap-3.5">
        <div class="w-9 h-9 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0"><i class="ph-bold ph-xmark text-red-600"></i></div>
        <span class="font-bold text-base">{{ session('error') }}</span>
    </div>
    @endif

    <!-- Main Thread Card -->
    <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] border border-slate-200/50 shadow-2xl shadow-xl p-10 space-y-8 relative overflow-hidden">
        <!-- Accent indicator -->
        <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

        <!-- Topic Header Info -->
        <div class="flex flex-wrap items-center justify-between gap-6 border-b border-slate-100 pb-6">
            <div class="flex items-center gap-4">
                @php
                    $author = $thread->user;
                    $repPoints = $author->reputation->total_points ?? 0;
                    $glowRing = 'border-slate-200';
                    if ($author->isGuru()) {
                        $glowRing = 'ring-4 ring-indigo-500 ring-offset-2';
                    } elseif ($repPoints > 200) {
                        $glowRing = 'ring-4 ring-amber-400 ring-offset-2';
                    } elseif ($repPoints > 100) {
                        $glowRing = 'ring-4 ring-slate-400 ring-offset-2';
                    }
                @endphp
                <img src="https://ui-avatars.com/api/?name={{ urlencode($author->name) }}&size=48&background=random" 
                     class="w-14 h-14 rounded-full border shadow-sm {{ $glowRing }}">
                <div>
                    <h5 class="font-black text-slate-800 text-base md:text-lg flex items-center gap-2.5">
                        {{ $author->name }}
                        <span class="px-3 py-0.5 bg-slate-105 text-slate-700 rounded-md text-[10px] uppercase font-black tracking-wider border border-slate-200">{{ $author->role }}</span>
                    </h5>
                    <p class="text-sm text-slate-500 font-extrabold mt-1">
                        Diposting {{ $thread->created_at->diffForHumans() }} 
                        @if($author->school)
                            • <span class="text-indigo-650">{{ $author->school->name }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <!-- Header Badges -->
            <div class="flex items-center gap-2.5 text-xs font-black uppercase tracking-wider">
                <span class="px-4 py-2 bg-indigo-50 text-indigo-705 rounded-lg border border-indigo-100">
                    {{ $thread->category_label }}
                </span>
                
                @if(in_array($thread->category, ['project_idea', 'committee', 'charity']))
                @php
                    $statusColor = match($thread->status) {
                        'seeking_members' => 'bg-emerald-50 text-emerald-800 border-emerald-250',
                        'active' => 'bg-blue-50 text-blue-800 border-blue-250',
                        'completed' => 'bg-slate-100 text-slate-700 border-slate-300',
                        default => 'bg-slate-50 text-slate-700 border-slate-200'
                    };
                    $statusLabel = match($thread->status) {
                        'seeking_members' => 'Lagi Cari Tim 🤝',
                        'active' => 'Lagi Jalan ⚡',
                        'completed' => 'Misi Selesai 🏆',
                        default => $thread->status
                    };
                @endphp
                <span class="px-4 py-2 border {{ $statusColor }} rounded-lg">
                    {{ $statusLabel }}
                </span>
                @endif
            </div>
        </div>

        <!-- Thread Body -->
        <div class="space-y-6">
            <h2 class="forum-hdr text-3xl md:text-4xl lg:text-5xl font-black text-slate-850 tracking-tight leading-tight">
                {{ $thread->title }}
            </h2>
            
            <!-- Thread Content (Larger Font Size) -->
            <div class="text-slate-750 leading-relaxed whitespace-pre-line font-medium text-base md:text-lg lg:text-xl">
                {!! e($thread->content) !!}
            </div>

            <!-- Attached Image -->
            @if($thread->image_path)
            <div class="rounded-3xl border border-slate-200 bg-slate-50 overflow-hidden max-h-[600px] shadow-sm">
                <img src="{{ asset('storage/' . $thread->image_path) }}" class="w-full h-full object-contain mx-auto max-h-[600px]">
            </div>
            @endif

            <!-- Attached Document -->
            @if($thread->attachment_path)
            <div class="p-6 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/50 flex items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-2xl">
                        <i class="ph-bold ph-file-lines"></i>
                    </div>
                    <div>
                        <h4 class="text-sm md:text-base font-black text-slate-800">{{ $thread->attachment_name }}</h4>
                        <p class="text-[10px] text-slate-550 font-black uppercase tracking-wider mt-1">File Lampiran</p>
                    </div>
                </div>
                <a href="{{ asset('storage/' . $thread->attachment_path) }}" target="_blank" 
                   class="px-6 py-3 bg-white border border-slate-250 hover:border-indigo-250 hover:text-indigo-700 text-slate-700 rounded-xl text-xs md:text-sm font-black uppercase tracking-wider shadow-sm transition">
                    <i class="ph-bold ph-download mr-1.5"></i> Unduh File 📥
                </a>
            </div>
            @endif

            <!-- Morphic Cards: Achievement Reference -->
            @if($perfCard)
                <div class="p-6 rounded-2xl bg-gradient-to-r from-indigo-50/50 to-white border border-indigo-150 max-w-lg space-y-4 shadow-sm">
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest block"><i class="ph-bold ph-medal mr-1"></i> 🏅 Terverifikasi Prestasi Siswa</span>
                    
                    @if($thread->reference_type === \App\Models\Badge::class)
                        <!-- Badge Display -->
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 {{ $perfCard->color }} rounded-2xl flex items-center justify-center text-white text-3xl shadow-md">
                                <i class="fa-solid {{ str_replace(['fas ', 'fa-solid '], '', $perfCard->icon) }}"></i>
                            </div>
                            <div>
                                <h4 class="forum-hdr font-black text-slate-800 text-lg leading-tight">{{ $perfCard->name }}</h4>
                                <p class="text-sm text-slate-550 mt-1 leading-normal font-bold">🎖️ Lencana Kece: {{ $perfCard->description }}</p>
                            </div>
                        </div>
                    @elseif($thread->reference_type === \App\Models\CbtExamResult::class)
                        <!-- CBT Grade Display -->
                        <div class="space-y-3">
                            <div class="flex justify-between items-start gap-3">
                                <div>
                                    <h4 class="forum-hdr font-black text-slate-800 text-base leading-tight">{{ $perfCard->exam->exam_title }}</h4>
                                    <p class="text-[10px] text-slate-500 font-black mt-1 uppercase tracking-wider">Hasil Ujian CBT</p>
                                </div>
                                <span class="px-3 py-1.5 {{ $perfCard->is_passed ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }} text-[10px] font-black uppercase rounded-md">
                                    {{ $perfCard->is_passed ? 'Lulus KKM' : 'Remedial' }}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-4 bg-white p-4 rounded-xl border border-slate-200 text-center">
                                <div>
                                    <span class="text-[10px] text-slate-550 font-black uppercase tracking-wider block">Skor Ujian</span>
                                    <span class="text-2xl font-black text-indigo-650 block mt-0.5">{{ $perfCard->final_score }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-550 font-black uppercase tracking-wider block">Predikat</span>
                                    <span class="text-2xl font-black text-slate-800 block mt-0.5">{{ $perfCard->predicate }}</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Charity Details Block -->
        @if($thread->category === 'charity')
            @php
                $hasTarget = !empty($thread->charity_target_amount);
                $pct = 0;
                if ($hasTarget && $thread->charity_target_amount > 0) {
                    $pct = min(100, round(($thread->charity_current_amount / $thread->charity_target_amount) * 100));
                }
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 p-8 rounded-2xl bg-amber-50 border border-amber-200">
                <!-- Left stats -->
                <div class="space-y-4">
                    <h4 class="text-sm font-black text-amber-955 uppercase tracking-widest"><i class="ph-bold ph-heart mr-1 text-amber-600"></i> ❤️ Info Donasi & Volunteer</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between text-[11px] font-black text-slate-655 uppercase">
                            <span>Donasi Terkumpul</span>
                            <span>{{ $hasTarget ? "Goal: Rp " . number_format($thread->charity_target_amount, 0, ',', '.') : 'Aksi Sosial' }}</span>
                        </div>
                        <div class="text-4xl font-black text-slate-850">
                            Rp {{ number_format($thread->charity_current_amount, 0, ',', '.') }}
                        </div>
                        @if($hasTarget)
                        <div class="w-full bg-slate-200 h-3 rounded-full overflow-hidden border border-amber-100">
                            <div class="bg-amber-550 h-full rounded-full transition-all duration-300" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="text-right text-xs font-black text-amber-900 font-bold">Progress: {{ $pct }}%</div>
                        @endif
                    </div>
                </div>

                <!-- Right action (Donation Simulation) -->
                @if($thread->status !== 'completed')
                <div class="bg-white p-6 rounded-xl border border-amber-250 shadow-sm space-y-4">
                    <h5 class="text-sm font-black text-slate-700 uppercase tracking-wider">Catat Donasi Baru 💰</h5>
                    <form action="{{ route('forum.donate', $thread) }}" method="POST" class="flex gap-3">
                        @csrf
                        <div class="relative flex-1">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-sm font-black text-slate-500">Rp</span>
                            <input type="number" name="amount" required min="1000" class="w-full pl-10 pr-3 py-3 border-2 border-slate-200 focus:border-indigo-400 rounded-xl text-base outline-none text-slate-850 font-bold" placeholder="Jumlah (cth: 50000)">
                        </div>
                        <button type="submit" class="px-6 py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl text-sm uppercase tracking-wider transition shadow-sm flex-shrink-0">Kirim</button>
                    </form>
                    <p class="text-xs text-slate-550 font-bold leading-normal">Catat donasi di sini buat nambah **+10 Poin Reputasi** kamu secara instan!</p>
                </div>
                @endif
            </div>
        @endif

        <!-- Upvote Thread & Footer views -->
        <div class="flex items-center gap-6 border-t border-slate-105 pt-6">
            <!-- Like Toggle Button (AJAX/Simulated Form) -->
            @php $isLiked = $thread->isLikedBy(auth()->user()); @endphp
            <form id="likeForm" action="{{ route('forum.like', $thread) }}" method="POST">
                @csrf
                <button type="submit" id="likeBtn" 
                        class="inline-flex items-center gap-3 px-6 py-3.5 rounded-xl font-black text-xs md:text-sm uppercase tracking-wider transition shadow-sm border-2 {{ $isLiked ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-slate-655 border-slate-250 hover:bg-slate-50' }}">
                    <i class="ph-bold ph-thumbs-up text-base"></i>
                    <span id="likeBtnText">{{ $isLiked ? 'Udah Kamu Upvote 👍' : 'Beri Upvote 👍' }}</span>
                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-black ml-2 {{ $isLiked ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }}" id="likesCount">{{ $thread->likes->count() }}</span>
                </button>
            </form>

            <div class="flex items-center gap-6 text-xs text-slate-500 font-black ml-auto">
                <span><i class="ph-bold ph-eye mr-1.5 text-slate-400 text-sm"></i> {{ $thread->views_count }} Dilihat</span>
                <span><i class="ph-bold ph-chat-circle mr-1.5 text-slate-400 text-sm"></i> {{ $thread->replies->count() }} Balasan</span>
            </div>
        </div>
    </div>

    <!-- Collaboration Team Recruitment Area -->
    @if(in_array($thread->category, ['project_idea', 'committee', 'charity']))
    <div class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] border border-slate-200/50 shadow-2xl shadow-xl p-10 space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h3 class="forum-hdr text-lg md:text-xl font-black text-slate-800"><i class="ph-bold ph-user-group mr-2 text-indigo-600"></i> 👥 Tim Kolaborasi & Relawan</h3>
            
            <!-- Update Lifecycle status option for Creator -->
            @if(auth()->id() === $thread->user_id)
            <form action="{{ route('forum.status.update', $thread) }}" method="POST" class="flex items-center gap-3">
                @csrf
                <label class="text-sm font-black text-slate-605">Status Proyek:</label>
                <select name="status" onchange="this.form.submit()" class="px-4 py-2 border-2 border-slate-250 rounded-xl text-sm font-black bg-white outline-none cursor-pointer text-slate-700">
                    <option value="seeking_members" {{ $thread->status === 'seeking_members' ? 'selected' : '' }}>Mencari Anggota</option>
                    <option value="active" {{ $thread->status === 'active' ? 'selected' : '' }}>Berjalan / Aktif</option>
                    <option value="completed" {{ $thread->status === 'completed' ? 'selected' : '' }}>Selesai (Bagi Bonus 50 Poin! 🎁)</option>
                </select>
            </form>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left: Current Approved Members list -->
            <div class="space-y-4">
                <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Anggota Tim Aktif ({{ $thread->approvedMembers()->count() }} Orang)</h4>
                
                <div class="space-y-3">
                    <!-- Owner is always leader -->
                    <div class="px-5 py-4 bg-indigo-50/50 border border-indigo-100/50 rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($thread->user->name) }}&size=28&background=random" class="w-7 h-7 rounded-full border shadow-sm">
                            <span class="text-sm font-black text-slate-800">{{ $thread->user->name }}</span>
                        </div>
                        <span class="px-3 py-1 bg-indigo-600 text-white rounded-md text-[9px] uppercase font-black tracking-wider">Leader Proyek 👑</span>
                    </div>

                    @forelse($thread->approvedMembers as $member)
                    <div class="px-5 py-4 bg-slate-50/80 backdrop-blur-md border border-slate-200 rounded-2xl flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&size=28&background=random" class="w-7 h-7 rounded-full border shadow-sm">
                            <span class="text-sm font-black text-slate-800">{{ $member->user->name }}</span>
                        </div>
                        <span class="bg-slate-200 text-slate-700 px-3 py-1 rounded-md text-[9px] uppercase font-black tracking-wider border border-slate-300">{{ $member->user->role }}</span>
                    </div>
                    @empty
                    @endforelse
                </div>
            </div>

            <!-- Right: Application list or Join application form -->
            <div class="space-y-4">
                @if(auth()->id() === $thread->user_id)
                    <!-- Author View: Pending Applications List -->
                    <h4 class="text-[11px] font-black text-slate-500 uppercase tracking-widest">Pendaftar Baru ({{ $thread->members()->where('status', 'pending')->count() }} Menunggu Persetujuan)</h4>
                    
                    <div class="space-y-4 max-h-80 overflow-y-auto pr-2 custom-scrollbar">
                        @forelse($thread->members()->where('status', 'pending')->get() as $app)
                        <div class="p-5 rounded-2xl border-2 border-slate-100 bg-slate-50/50 space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($app->user->name) }}&size=24&background=random" class="w-6 h-6 rounded-full shadow-sm">
                                    <span class="text-sm font-black text-slate-800">{{ $app->user->name }}</span>
                                </div>
                                <span class="text-[9px] bg-slate-200 text-slate-700 px-2.5 py-1 border border-slate-300 rounded-md font-black uppercase tracking-wider">{{ $app->user->role }}</span>
                            </div>
                            @if($app->notes)
                                <p class="text-sm text-slate-750 italic bg-white p-4 rounded-xl border-2 border-slate-100">"{{ $app->notes }}"</p>
                            @endif
                            
                            <div class="flex gap-2.5 justify-end">
                                <form action="{{ route('forum.member.reject', $app) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-white border-2 border-slate-200 text-slate-655 hover:bg-slate-100 text-xs font-black uppercase tracking-wider rounded-xl transition">Tolak</button>
                                </form>
                                <form action="{{ route('forum.member.approve', $app) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white hover:bg-emerald-700 text-xs font-black uppercase tracking-wider rounded-xl transition shadow-sm">Setujui</button>
                                </form>
                            </div>
                        </div>
                        @empty
                        <p class="text-sm text-slate-500 font-bold italic text-center py-8">Belum ada pendaftar baru yang menunggu.</p>
                        @endforelse
                    </div>
                @else
                    <!-- Other User View: Join application form -->
                    @php
                        $myMembership = $thread->members()->where('user_id', auth()->id())->first();
                    @endphp

                    @if($myMembership)
                        <!-- Show my application status -->
                        <div class="p-8 rounded-2xl border text-center space-y-4 {{ $myMembership->status === 'approved' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($myMembership->status === 'rejected' ? 'bg-rose-550/15 border-rose-250 text-rose-800' : 'bg-slate-550/5 border-slate-200 text-slate-800') }}">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center mx-auto bg-white shadow-sm text-lg">
                                <i class="fa-solid {{ $myMembership->status === 'approved' ? 'fa-check text-emerald-600' : ($myMembership->status === 'rejected' ? 'fa-xmark text-rose-600' : 'fa-hourglass-half text-slate-500') }}"></i>
                            </div>
                            <h4 class="text-sm font-black">Status Pendaftaranmu:</h4>
                            <p class="text-base font-black uppercase tracking-wider leading-none">{{ $myMembership->status }}</p>
                            @if($myMembership->status === 'pending')
                                <p class="text-xs text-slate-500 font-bold">Sabar ya, lagi menunggu konfirmasi dari leader proyek.</p>
                            @elseif($myMembership->status === 'approved')
                                <p class="text-xs text-emerald-700 font-bold font-bold">Gokil! Kamu resmi gabung di tim kolaborasi ini! 🎉</p>
                            @endif
                        </div>
                    @else
                        <!-- Form to Join/Volunteer -->
                        @if($thread->status !== 'completed')
                        <div class="bg-slate-50 border border-slate-200 p-8 rounded-2xl space-y-5">
                            <h4 class="text-sm font-black text-slate-700 uppercase tracking-wider">Gabung Tim Sekarang! 🤝</h4>
                            <p class="text-sm text-slate-650 leading-relaxed font-bold">Mau ikutan gabung atau jadi relawan? Kirim pengajuanmu di bawah ini, langsung dapet **+10 Poin Reputasi** lho!</p>
                            
                            <form action="{{ route('forum.join', $thread) }}" method="POST" class="space-y-4">
                                @csrf
                                <textarea name="notes" rows="2" class="w-full px-4 py-3 bg-white border-2 border-slate-200 focus:border-indigo-400 rounded-xl text-sm font-semibold outline-none transition" placeholder="Tulis keahlian atau motivasi kamu (opsional)..."></textarea>
                                <button type="submit" class="w-full py-4 bg-indigo-600 text-white font-black rounded-xl text-sm uppercase tracking-wider hover:bg-indigo-700 hover:shadow-lg transition shadow-md shadow-indigo-600/10">Kirim Lamaran Tim 🚀</button>
                            </form>
                        </div>
                        @else
                        <p class="text-sm text-slate-500 font-bold italic text-center py-8">Misi proyek ini sudah selesai, pendaftaran ditutup ya!</p>
                        @endif
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- Discussion Comments Section -->
    <div id="replies" class="bg-white/90 backdrop-blur-xl rounded-[2.5rem] border border-slate-200/50 shadow-2xl shadow-xl p-10 space-y-8">
        <h3 class="forum-hdr text-xl font-extrabold text-slate-800 border-b border-slate-100 pb-4">💬 Kolom Diskusi & Balasan ({{ $thread->replies->count() }})</h3>

        <!-- Comments List -->
        <div class="space-y-6">
            <!-- First: Render Accepted Reply (Best Answer) -->
            @php $acceptedReply = $thread->replies->where('is_accepted', true)->first(); @endphp
            @if($acceptedReply)
            <div class="p-8 rounded-2xl border-2 border-amber-400 bg-amber-50 relative space-y-4 shadow-sm ring-4 ring-amber-400/5">
                <div class="absolute top-5 right-5 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-[9px] font-black uppercase px-3 py-1.5 rounded-md tracking-widest shadow-sm">
                    <i class="ph-bold ph-crown mr-1"></i> Jawaban Terbaik 👑
                </div>
                <div class="flex items-center gap-3 text-sm text-slate-500">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($acceptedReply->user->name) }}&size=24&background=random" class="w-6 h-6 rounded-full shadow-sm border border-slate-200">
                    <span class="font-extrabold text-slate-800">{{ $acceptedReply->user->name }}</span>
                    <span>{{ $acceptedReply->created_at->diffForHumans() }}</span>
                </div>
                <p class="text-base md:text-lg text-slate-850 leading-relaxed font-bold">{!! e($acceptedReply->content) !!}</p>
            </div>
            @endif

            <!-- Next: All other replies -->
            @forelse($thread->replies as $reply)
                @if($reply->is_accepted) @continue @endif
                <div class="p-6 rounded-2xl border-2 border-slate-100 bg-slate-50/40 flex gap-5">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($reply->user->name) }}&size=36&background=random" class="w-9 h-9 rounded-full flex-shrink-0 border shadow-sm">
                    <div class="flex-1 space-y-3 min-w-0">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-2.5 text-xs font-bold text-slate-550">
                                <span class="font-black text-slate-800 text-sm md:text-base">{{ $reply->user->name }}</span>
                                <span class="bg-slate-150 text-slate-700 px-2.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider border border-slate-250">{{ $reply->user->role }}</span>
                                <span>{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                            
                            <!-- Mark as best answer option for Thread creator or teacher/admin -->
                            @if(!$acceptedReply && (auth()->id() === $thread->user_id || auth()->user()->isSuperAdmin() || auth()->user()->isGuru()))
                            <form action="{{ route('forum.reply.accept', $reply) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-[10px] font-black text-emerald-600 hover:text-emerald-700 transition uppercase tracking-wider bg-emerald-50 px-3.5 py-1.5 rounded-lg border border-emerald-200">
                                    Pilih Sebagai Jawaban Terbaik ⭐️
                                </button>
                            </form>
                            @endif
                        </div>
                        <p class="text-base text-slate-800 leading-relaxed font-bold">{!! e($reply->content) !!}</p>
                    </div>
                </div>
            @empty
                @if(!$acceptedReply)
                <p class="text-sm text-slate-500 font-bold italic text-center py-12">Belum ada komentar nih. Yuk, jadi yang pertama ngasih tanggapan gokil!</p>
                @endif
            @endforelse
        </div>

        <!-- Add Reply Form -->
        @if(!$thread->is_locked)
        <div class="pt-8 border-t border-slate-150 space-y-5">
            <h4 class="text-sm md:text-base font-black text-slate-500 uppercase tracking-widest font-bold">Tulis Balasanmu ✍️</h4>
            
            <form action="{{ route('forum.reply', $thread) }}" method="POST" class="space-y-4" x-data="{ comment: '' }">
                @csrf
                <textarea name="content" x-model="comment" rows="5" required maxlength="5000" class="w-full px-5 py-4 border-2 border-slate-200 focus:border-indigo-400 focus:ring-4 focus:ring-indigo-500/5 rounded-2xl text-base md:text-lg font-semibold outline-none transition resize-y text-slate-800" placeholder="Tuliskan masukan gokil, pendapat santai, rumus pelajaran LaTeX, atau solusi bermanfaat..."></textarea>
                <div class="flex justify-between items-center text-xs font-black text-slate-500">
                    <span class="flex items-center gap-1.5"><i class="ph-bold ph-wand-magic-sparkles text-amber-500"></i> Tanggapan bermanfaat bakal dapet **+5 Poin Reputasi**!</span>
                    <div class="flex items-center gap-5">
                        <span x-text="comment.length + '/5000'"></span>
                        <button type="submit" class="px-8 py-3.5 bg-indigo-600 text-white font-black rounded-xl text-sm uppercase tracking-wider hover:bg-indigo-700 transition shadow-md shadow-indigo-600/10">Kirim Balasan 🚀</button>
                    </div>
                </div>
            </form>
        </div>
        @else
        <div class="p-5 rounded-2xl bg-slate-100 text-center text-sm text-slate-550 font-black">
            <i class="ph-bold ph-lock mr-2.5"></i> Thread ini dikunci oleh admin/penulis. Kolom komentar ditutup dulu ya.
        </div>
        @endif
    </div>
</div>

<!-- MathJax configuration and execution -->
<script>
window.MathJax = {
  tex: {
    inlineMath: [['$', '$'], ['\\(', '\\)']]
  },
  svg: {
    fontCache: 'global'
  }
};
</script>
<script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<!-- AJAX Like Script for show page -->
<script>
document.getElementById('likeForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('likeBtn');
    const btnText = document.getElementById('likeBtnText');
    const countSpan = document.getElementById('likesCount');
    const url = this.getAttribute('action');

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
            countSpan.textContent = result.likes_count;
            if (result.liked) {
                btn.classList.remove('bg-white', 'text-slate-655', 'border-slate-250');
                btn.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                countSpan.classList.remove('bg-slate-200', 'text-slate-700');
                countSpan.classList.add('bg-white/20', 'text-white');
                btnText.textContent = 'Udah Kamu Upvote 👍';
            } else {
                btn.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
                btn.classList.add('bg-white', 'text-slate-655', 'border-slate-250');
                countSpan.classList.remove('bg-white/20', 'text-white');
                countSpan.classList.add('bg-slate-200', 'text-slate-700');
                btnText.textContent = 'Beri Upvote 👍';
            }
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        btn.disabled = false;
    }
});
</script>
@endsection
