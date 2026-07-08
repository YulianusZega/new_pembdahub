@extends(auth()->user()->layout)

@section('title', 'Hall of Fame - Pembda Elite')

@section('content')
<div class="hof-universe" id="hofUniverse">
    {{-- ═══════════════════ COMPACT HERO HEADER ═══════════════════ --}}
    <div class="hof-header">
        <div class="hof-header__bg-orbs">
            <div class="hof-orb hof-orb--1"></div>
            <div class="hof-orb hof-orb--2"></div>
            <div class="hof-orb hof-orb--3"></div>
        </div>
        <div class="hof-header__content">
            <div class="hof-badge-live">
                <span class="hof-badge-live__dot"></span>
                <span class="hof-badge-live__text">Live Rankings</span>
            </div>
            <h1 class="hof-header__title">
                <i class="fas fa-trophy hof-header__icon"></i>
                Hall of Fame
            </h1>
            <p class="hof-header__subtitle">Panggung kehormatan bagi siswa & guru PembdaHub terbaik</p>

            @if(auth()->check() && $userRanking)
            <div class="hof-myrank">
                <div class="hof-myrank__item">
                    <span class="hof-myrank__label">Peringkat Anda</span>
                    <span class="hof-myrank__value">#{{ $userRanking }}</span>
                </div>
                <div class="hof-myrank__divider"></div>
                <div class="hof-myrank__item">
                    <span class="hof-myrank__label">Poin Elite</span>
                    <span class="hof-myrank__value hof-myrank__value--emerald">{{ number_format(auth()->user()->reputation->total_points ?? 0) }}</span>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ═══════════════════ HIERARCHY TREE — STUDENTS ═══════════════════ --}}
    <div class="hof-section">
        <h2 class="hof-section__title">
            <i class="fas fa-sitemap"></i>
            Pohon Prestasi — Top Elite Students
        </h2>

        @php
            $s1 = $topStudents->get(0);
            $s2 = $topStudents->get(1);
            $s3 = $topStudents->get(2);
            $s4 = $topStudents->get(3);
            $s5 = $topStudents->get(4);
            $s6 = $topStudents->get(5);
            $s7 = $topStudents->get(6);
        @endphp

        <div class="hof-tree" id="hofTree">
            {{-- SVG Connectors --}}
            <svg class="hof-tree__svg" id="hofTreeSvg" preserveAspectRatio="none">
                <defs>
                    <linearGradient id="lineGradGold" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#f59e0b" stop-opacity="0.9"/>
                        <stop offset="100%" stop-color="#a855f7" stop-opacity="0.4"/>
                    </linearGradient>
                    <linearGradient id="lineGradSilver" x1="0%" y1="0%" x2="0%" y2="100%">
                        <stop offset="0%" stop-color="#94a3b8" stop-opacity="0.7"/>
                        <stop offset="100%" stop-color="#6366f1" stop-opacity="0.3"/>
                    </linearGradient>
                </defs>
            </svg>

            {{-- TIER 1: Champion --}}
            @if($s1)
            <div class="hof-tier hof-tier--1">
                <div class="hof-node hof-node--champion" id="node1" data-delay="0">
                    <div class="hof-node__crown">
                        <i class="fas fa-crown"></i>
                    </div>
                    <div class="hof-node__glow hof-node__glow--gold"></div>
                    <div class="hof-node__photo-ring hof-node__photo-ring--gold">
                        <img src="{{ $s1->user->photo_url }}" alt="{{ $s1->user->name }}" class="hof-node__photo" loading="lazy">
                    </div>
                    <div class="hof-node__info">
                        <span class="hof-node__rank">#1</span>
                        <h3 class="hof-node__name">{{ $s1->user->name }}</h3>
                        <span class="hof-node__class">{{ $s1->user->student->classroom->class_name ?? 'Kelas' }}</span>
                        <div class="hof-node__points">
                            <span class="hof-node__points-value">{{ number_format($s1->total_points) }}</span>
                            <span class="hof-node__points-label">Poin Elite</span>
                        </div>
                        @if($s1->user->badges->count())
                        <div class="hof-node__badges">
                            @foreach($s1->user->badges->take(2) as $badge)
                            <span class="hof-node__badge {{ $badge->color }}">
                                <i class="fas {{ $badge->icon }}"></i> {{ $badge->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    <div class="hof-node__particles" aria-hidden="true">
                        @for($i = 0; $i < 8; $i++)
                        <span class="hof-particle" style="--i:{{ $i }}"></span>
                        @endfor
                    </div>
                </div>
            </div>
            @endif

            {{-- TIER 2: Runner-ups --}}
            <div class="hof-tier hof-tier--2">
                @if($s2)
                <div class="hof-node hof-node--silver" id="node2" data-delay="200">
                    <div class="hof-node__glow hof-node__glow--silver"></div>
                    <div class="hof-node__photo-ring hof-node__photo-ring--silver">
                        <img src="{{ $s2->user->photo_url }}" alt="{{ $s2->user->name }}" class="hof-node__photo" loading="lazy">
                    </div>
                    <div class="hof-node__info">
                        <span class="hof-node__rank">#2</span>
                        <h3 class="hof-node__name">{{ $s2->user->name }}</h3>
                        <span class="hof-node__class">{{ $s2->user->student->classroom->class_name ?? 'Kelas' }}</span>
                        <div class="hof-node__points">
                            <span class="hof-node__points-value">{{ number_format($s2->total_points) }}</span>
                            <span class="hof-node__points-label">Poin</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($s3)
                <div class="hof-node hof-node--bronze" id="node3" data-delay="300">
                    <div class="hof-node__glow hof-node__glow--bronze"></div>
                    <div class="hof-node__photo-ring hof-node__photo-ring--bronze">
                        <img src="{{ $s3->user->photo_url }}" alt="{{ $s3->user->name }}" class="hof-node__photo" loading="lazy">
                    </div>
                    <div class="hof-node__info">
                        <span class="hof-node__rank">#3</span>
                        <h3 class="hof-node__name">{{ $s3->user->name }}</h3>
                        <span class="hof-node__class">{{ $s3->user->student->classroom->class_name ?? 'Kelas' }}</span>
                        <div class="hof-node__points">
                            <span class="hof-node__points-value">{{ number_format($s3->total_points) }}</span>
                            <span class="hof-node__points-label">Poin</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            {{-- TIER 3: Leaves --}}
            <div class="hof-tier hof-tier--3">
                @php $tier3 = [$s4, $s5, $s6, $s7]; @endphp
                @foreach($tier3 as $idx => $leaf)
                    @if($leaf)
                    <div class="hof-node hof-node--leaf" id="node{{ $idx + 4 }}" data-delay="{{ 400 + ($idx * 100) }}">
                        <div class="hof-node__photo-ring hof-node__photo-ring--leaf">
                            <img src="{{ $leaf->user->photo_url }}" alt="{{ $leaf->user->name }}" class="hof-node__photo" loading="lazy">
                        </div>
                        <div class="hof-node__info">
                            <span class="hof-node__rank">#{{ $idx + 4 }}</span>
                            <h3 class="hof-node__name hof-node__name--sm">{{ $leaf->user->name }}</h3>
                            <span class="hof-node__class">{{ $leaf->user->student->classroom->class_name ?? 'Kelas' }}</span>
                            <div class="hof-node__points hof-node__points--sm">
                                <span class="hof-node__points-value">{{ number_format($leaf->total_points) }}</span>
                                <span class="hof-node__points-label">Poin</span>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════ GURU SECTION ═══════════════════ --}}
    <div class="hof-section">
        <h2 class="hof-section__title">
            <i class="fas fa-chalkboard-teacher"></i>
            Inspirational Guru
        </h2>

        <div class="hof-guru-deck">
            @forelse($topTeachers as $index => $rep)
            <div class="hof-guru-card {{ $index === 0 ? 'hof-guru-card--top' : '' }}" style="animation-delay: {{ $index * 150 }}ms">
                @if($index === 0)
                <div class="hof-guru-card__crown">
                    <i class="fas fa-star"></i> Most Inspiring
                </div>
                @endif
                <div class="hof-guru-card__rank">#{{ $index + 1 }}</div>
                <div class="hof-guru-card__photo-wrap">
                    <img src="{{ $rep->user->photo_url }}" alt="{{ $rep->user->name }}" class="hof-guru-card__photo" loading="lazy">
                </div>
                <h4 class="hof-guru-card__name">{{ $rep->user->name }}</h4>
                <div class="hof-guru-card__badges">
                    @foreach($rep->user->badges->take(2) as $badge)
                    <span class="hof-guru-card__badge {{ $badge->color }}">
                        <i class="fas {{ $badge->icon }}"></i> {{ $badge->name }}
                    </span>
                    @endforeach
                </div>
                <div class="hof-guru-card__score">
                    <span class="hof-guru-card__score-val">{{ number_format($rep->total_points) }}</span>
                    <span class="hof-guru-card__score-lbl">Elite Score</span>
                </div>
            </div>
            @empty
            <div class="hof-empty">
                <i class="fas fa-users-slash"></i>
                <p>Belum ada data guru.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════════════ BADGE SHOWCASE ═══════════════════ --}}
    <div class="hof-section hof-section--badges">
        <h2 class="hof-section__title">
            <i class="fas fa-medal"></i>
            Special Elite Badges
        </h2>
        <p class="hof-section__desc">Lencana kehormatan yang bisa didapatkan melalui aksi nyata</p>

        <div class="hof-badges-grid">
            @foreach(\App\Models\Badge::where('is_active', true)->get() as $badge)
            <div class="hof-badge-card">
                <div class="hof-badge-card__icon {{ $badge->color }}">
                    <i class="fas {{ $badge->icon }}"></i>
                </div>
                <h4 class="hof-badge-card__name">{{ $badge->name }}</h4>
                <p class="hof-badge-card__desc">{{ $badge->description }}</p>
                <div class="hof-badge-card__req">
                    <span class="hof-badge-card__req-label">Requirement</span>
                    <span class="hof-badge-card__req-value">{{ number_format($badge->requirement_value) }} Poin</span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ═══════════════════ STYLES ═══════════════════ --}}
<style>
/* ────── BASE RESET ────── */
.hof-universe {
    --gold: #f59e0b;
    --gold-light: #fbbf24;
    --silver: #94a3b8;
    --bronze: #d97706;
    --emerald: #10b981;
    --indigo: #6366f1;
    --slate-900: #0f172a;
    --slate-800: #1e293b;
    padding: 2rem 0;
    min-height: 100vh;
}

/* ────── HEADER ────── */
.hof-header {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, var(--slate-900) 0%, #1e1b4b 50%, var(--slate-800) 100%);
    border-radius: 1.5rem;
    padding: 3rem 2rem;
    text-align: center;
    color: #fff;
    margin-bottom: 3rem;
}

.hof-header__bg-orbs { position: absolute; inset: 0; pointer-events: none; }

.hof-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    opacity: 0.15;
    animation: orbFloat 8s ease-in-out infinite;
}
.hof-orb--1 { width: 300px; height: 300px; background: var(--gold); top: -80px; right: -60px; }
.hof-orb--2 { width: 250px; height: 250px; background: var(--indigo); bottom: -60px; left: -40px; animation-delay: 3s; }
.hof-orb--3 { width: 180px; height: 180px; background: var(--emerald); top: 50%; left: 50%; animation-delay: 5s; }

@keyframes orbFloat {
    0%, 100% { transform: translate(0, 0) scale(1); }
    50% { transform: translate(20px, -20px) scale(1.1); }
}

.hof-header__content { position: relative; z-index: 2; }

.hof-badge-live {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.15);
    padding: 0.35rem 1rem;
    border-radius: 9999px;
    margin-bottom: 1.25rem;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.hof-badge-live__dot {
    width: 8px; height: 8px;
    background: var(--emerald);
    border-radius: 50%;
    animation: pulse 2s infinite;
    box-shadow: 0 0 6px var(--emerald);
}
@keyframes pulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.4); }
}

.hof-header__title {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0 0 0.5rem;
    background: linear-gradient(135deg, #fff, var(--gold-light));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1.2;
}
.hof-header__icon { -webkit-text-fill-color: var(--gold); margin-right: 0.5rem; font-size: 2rem; }

.hof-header__subtitle {
    color: rgba(255,255,255,0.6);
    font-size: 0.95rem;
    margin: 0;
    font-weight: 500;
}

/* My Rank */
.hof-myrank {
    display: inline-flex;
    align-items: center;
    gap: 1.5rem;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: 1rem;
    padding: 1rem 2rem;
    margin-top: 1.5rem;
}
.hof-myrank__label {
    display: block;
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: rgba(255,255,255,0.5);
    font-weight: 700;
    margin-bottom: 0.15rem;
}
.hof-myrank__value { font-size: 1.75rem; font-weight: 800; }
.hof-myrank__value--emerald { color: var(--emerald); }
.hof-myrank__divider { width: 1px; height: 40px; background: rgba(255,255,255,0.15); }

/* ────── SECTION ────── */
.hof-section {
    margin-bottom: 3.5rem;
}
.hof-section__title {
    text-align: center;
    font-size: 1.5rem;
    font-weight: 800;
    color: #1e293b;
    margin-bottom: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
}
.hof-section__title i { color: var(--gold); font-size: 1.2rem; }
.hof-section__desc {
    text-align: center;
    color: #94a3b8;
    font-size: 0.9rem;
    margin-top: -1.5rem;
    margin-bottom: 2rem;
}

/* ────── HIERARCHY TREE ────── */
.hof-tree {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    padding: 1rem 0;
}

.hof-tree__svg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 0;
}

.hof-tree__svg line, .hof-tree__svg path {
    stroke-width: 2;
    stroke-linecap: round;
    fill: none;
}

/* Tiers */
.hof-tier {
    display: flex;
    justify-content: center;
    gap: 2rem;
    position: relative;
    z-index: 1;
    width: 100%;
    flex-wrap: wrap;
}
.hof-tier--1 { margin-bottom: 0.5rem; }
.hof-tier--2 { gap: 4rem; }
.hof-tier--3 { gap: 1.25rem; }

/* ────── NODE CARD ────── */
.hof-node {
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 1.5rem 1.25rem 1.25rem;
    border-radius: 1.25rem;
    background: rgba(255,255,255,0.85);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.6);
    box-shadow: 0 4px 24px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
    transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.4s ease;
    opacity: 0;
    transform: translateY(40px) scale(0.9);
    animation: nodeEnter 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
.hof-node:hover {
    transform: translateY(-6px) scale(1.03);
    box-shadow: 0 20px 50px rgba(0,0,0,0.12);
}

@keyframes nodeEnter {
    to { opacity: 1; transform: translateY(0) scale(1); }
}

/* Champion Node */
.hof-node--champion {
    padding: 2rem 2rem 1.75rem;
    background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(255,251,235,0.9));
    border: 2px solid rgba(245,158,11,0.25);
    box-shadow: 0 8px 40px rgba(245,158,11,0.12), 0 2px 8px rgba(0,0,0,0.04);
    min-width: 220px;
}
.hof-node--champion:hover {
    box-shadow: 0 24px 60px rgba(245,158,11,0.2);
}

/* Silver/Bronze Node */
.hof-node--silver { min-width: 180px; }
.hof-node--bronze { min-width: 180px; }

/* Leaf Node */
.hof-node--leaf {
    padding: 1rem 1rem 0.85rem;
    min-width: 140px;
    max-width: 160px;
}

/* Crown */
.hof-node__crown {
    position: absolute;
    top: -18px;
    left: 50%;
    transform: translateX(-50%);
    color: var(--gold);
    font-size: 1.75rem;
    animation: crownBounce 3s ease-in-out infinite;
    filter: drop-shadow(0 2px 6px rgba(245,158,11,0.4));
}
@keyframes crownBounce {
    0%, 100% { transform: translateX(-50%) translateY(0); }
    50% { transform: translateX(-50%) translateY(-6px); }
}

/* Glow */
.hof-node__glow {
    position: absolute;
    width: 130%;
    height: 130%;
    border-radius: 50%;
    top: -15%;
    left: -15%;
    pointer-events: none;
    opacity: 0.12;
    animation: glowPulse 4s ease-in-out infinite;
}
.hof-node__glow--gold { background: radial-gradient(circle, var(--gold), transparent 70%); }
.hof-node__glow--silver { background: radial-gradient(circle, var(--silver), transparent 70%); opacity: 0.08; }
.hof-node__glow--bronze { background: radial-gradient(circle, var(--bronze), transparent 70%); opacity: 0.1; }

@keyframes glowPulse {
    0%, 100% { transform: scale(1); opacity: 0.12; }
    50% { transform: scale(1.15); opacity: 0.2; }
}

/* Photo Ring */
.hof-node__photo-ring {
    position: relative;
    border-radius: 50%;
    padding: 3px;
    margin-bottom: 0.75rem;
    z-index: 2;
}
.hof-node__photo-ring--gold {
    width: 100px; height: 100px;
    background: linear-gradient(135deg, var(--gold), var(--gold-light), #fff, var(--gold));
    background-size: 300% 300%;
    animation: ringShimmer 4s ease infinite;
    box-shadow: 0 0 20px rgba(245,158,11,0.3);
}
.hof-node__photo-ring--silver {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, #cbd5e1, #e2e8f0, #fff, #94a3b8);
    background-size: 300% 300%;
    animation: ringShimmer 5s ease infinite;
    box-shadow: 0 0 12px rgba(148,163,184,0.3);
}
.hof-node__photo-ring--bronze {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, var(--bronze), #fbbf24, #fff, #b45309);
    background-size: 300% 300%;
    animation: ringShimmer 5s ease infinite;
    box-shadow: 0 0 12px rgba(217,119,6,0.3);
}
.hof-node__photo-ring--leaf {
    width: 56px; height: 56px;
    background: linear-gradient(135deg, var(--indigo), #a78bfa);
    box-shadow: 0 0 8px rgba(99,102,241,0.2);
}

@keyframes ringShimmer {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.hof-node__photo {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

/* Node Info */
.hof-node__info { position: relative; z-index: 2; }

.hof-node__rank {
    display: inline-block;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #fff;
    background: var(--indigo);
    padding: 0.15rem 0.6rem;
    border-radius: 9999px;
    margin-bottom: 0.35rem;
}
.hof-node--champion .hof-node__rank { background: linear-gradient(135deg, var(--gold), var(--bronze)); font-size: 0.75rem; }
.hof-node--silver .hof-node__rank { background: linear-gradient(135deg, #64748b, #94a3b8); }
.hof-node--bronze .hof-node__rank { background: linear-gradient(135deg, #b45309, var(--bronze)); }

.hof-node__name {
    font-size: 0.95rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0.25rem 0 0.1rem;
    line-height: 1.3;
}
.hof-node__name--sm { font-size: 0.8rem; }

.hof-node__class {
    font-size: 0.65rem;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
}

.hof-node__points {
    margin-top: 0.5rem;
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
    border: 1px solid #d1fae5;
    border-radius: 0.75rem;
    padding: 0.35rem 0.75rem;
}
.hof-node__points--sm { padding: 0.25rem 0.5rem; }

.hof-node__points-value {
    display: block;
    font-size: 1.15rem;
    font-weight: 800;
    color: #059669;
}
.hof-node__points--sm .hof-node__points-value { font-size: 0.9rem; }

.hof-node__points-label {
    font-size: 0.55rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6ee7b7;
    font-weight: 700;
}

.hof-node__badges {
    display: flex;
    gap: 0.35rem;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 0.5rem;
}
.hof-node__badge {
    font-size: 0.6rem;
    padding: 0.2rem 0.5rem;
    border-radius: 0.35rem;
    color: #fff;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}
.hof-node__badge i { font-size: 0.55rem; }

/* Particles */
.hof-node__particles {
    position: absolute;
    inset: -20px;
    pointer-events: none;
    z-index: 0;
}
.hof-particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: var(--gold);
    border-radius: 50%;
    top: 50%;
    left: 50%;
    animation: sparkle 3s ease-in-out infinite;
    animation-delay: calc(var(--i) * 0.375s);
    opacity: 0;
}
@keyframes sparkle {
    0% { opacity: 0; transform: translate(0, 0) scale(0); }
    30% { opacity: 1; transform: translate(calc(cos(var(--i) * 45deg) * 60px), calc(sin(var(--i) * 45deg) * 60px)) scale(1); }
    100% { opacity: 0; transform: translate(calc(cos(var(--i) * 45deg) * 90px), calc(sin(var(--i) * 45deg) * 90px)) scale(0); }
}
/* JS-based particle fallback positions */
.hof-particle:nth-child(1) { animation-name: sparkle1; }
.hof-particle:nth-child(2) { animation-name: sparkle2; }
.hof-particle:nth-child(3) { animation-name: sparkle3; }
.hof-particle:nth-child(4) { animation-name: sparkle4; }
.hof-particle:nth-child(5) { animation-name: sparkle5; }
.hof-particle:nth-child(6) { animation-name: sparkle6; }
.hof-particle:nth-child(7) { animation-name: sparkle7; }
.hof-particle:nth-child(8) { animation-name: sparkle8; }

@keyframes sparkle1 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(50px,-30px) scale(1)} 100%{opacity:0;transform:translate(70px,-45px) scale(0)} }
@keyframes sparkle2 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(40px,35px) scale(1)} 100%{opacity:0;transform:translate(60px,50px) scale(0)} }
@keyframes sparkle3 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(-45px,-25px) scale(1)} 100%{opacity:0;transform:translate(-65px,-40px) scale(0)} }
@keyframes sparkle4 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(-35px,40px) scale(1)} 100%{opacity:0;transform:translate(-55px,55px) scale(0)} }
@keyframes sparkle5 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(55px,10px) scale(1)} 100%{opacity:0;transform:translate(75px,15px) scale(0)} }
@keyframes sparkle6 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(-50px,5px) scale(1)} 100%{opacity:0;transform:translate(-70px,8px) scale(0)} }
@keyframes sparkle7 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(15px,-50px) scale(1)} 100%{opacity:0;transform:translate(20px,-70px) scale(0)} }
@keyframes sparkle8 { 0%{opacity:0;transform:translate(0,0) scale(0)} 30%{opacity:1;transform:translate(-10px,55px) scale(1)} 100%{opacity:0;transform:translate(-15px,75px) scale(0)} }

/* ────── GURU DECK ────── */
.hof-guru-deck {
    display: flex;
    gap: 1.25rem;
    justify-content: center;
    flex-wrap: wrap;
    padding: 0 1rem;
}

.hof-guru-card {
    position: relative;
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.6);
    border-radius: 1.25rem;
    padding: 2rem 1.5rem 1.5rem;
    text-align: center;
    min-width: 170px;
    max-width: 200px;
    flex: 1;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    transition: transform 0.4s cubic-bezier(0.16,1,0.3,1), box-shadow 0.4s ease;
    opacity: 0;
    animation: nodeEnter 0.6s cubic-bezier(0.16,1,0.3,1) forwards;
}
.hof-guru-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 16px 40px rgba(0,0,0,0.1);
}

.hof-guru-card--top {
    border: 2px solid rgba(16,185,129,0.25);
    background: linear-gradient(145deg, rgba(255,255,255,0.95), rgba(236,253,245,0.9));
    box-shadow: 0 8px 30px rgba(16,185,129,0.1);
}
.hof-guru-card--top:hover {
    box-shadow: 0 20px 50px rgba(16,185,129,0.15);
}

.hof-guru-card__crown {
    position: absolute;
    top: -12px;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, var(--emerald), #059669);
    color: #fff;
    font-size: 0.6rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    white-space: nowrap;
    box-shadow: 0 2px 10px rgba(16,185,129,0.3);
}

.hof-guru-card__rank {
    font-size: 0.65rem;
    font-weight: 800;
    color: #94a3b8;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}
.hof-guru-card--top .hof-guru-card__rank { color: var(--emerald); }

.hof-guru-card__photo-wrap {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    padding: 3px;
    background: linear-gradient(135deg, var(--emerald), var(--indigo));
    margin: 0 auto 0.75rem;
}
.hof-guru-card__photo {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
}

.hof-guru-card__name {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.5rem;
    line-height: 1.3;
}

.hof-guru-card__badges {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
    justify-content: center;
    margin-bottom: 0.75rem;
}
.hof-guru-card__badge {
    font-size: 0.55rem;
    padding: 0.15rem 0.4rem;
    border-radius: 0.3rem;
    color: #fff;
    font-weight: 700;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.2rem;
}

.hof-guru-card__score {
    background: linear-gradient(135deg, #f0fdf4, #ecfdf5);
    border: 1px solid #d1fae5;
    border-radius: 0.75rem;
    padding: 0.4rem 0.75rem;
}
.hof-guru-card__score-val {
    display: block;
    font-size: 1.1rem;
    font-weight: 800;
    color: #059669;
}
.hof-guru-card__score-lbl {
    font-size: 0.55rem;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: #6ee7b7;
    font-weight: 700;
}

/* Empty state */
.hof-empty {
    text-align: center;
    color: #94a3b8;
    padding: 3rem 0;
    width: 100%;
}
.hof-empty i { font-size: 2rem; margin-bottom: 0.75rem; display: block; }
.hof-empty p { font-size: 0.9rem; font-style: italic; margin: 0; }

/* ────── BADGE SHOWCASE ────── */
.hof-section--badges {
    background: linear-gradient(135deg, #f8fafc, #eef2ff);
    border-radius: 1.5rem;
    padding: 3rem 2rem;
    border: 1px solid #e0e7ff;
}

.hof-badges-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1.25rem;
}

.hof-badge-card {
    background: #fff;
    border-radius: 1.25rem;
    padding: 1.75rem 1.25rem;
    text-align: center;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    transition: transform 0.4s cubic-bezier(0.16,1,0.3,1), box-shadow 0.3s ease;
    border: 1px solid #f1f5f9;
}
.hof-badge-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.08);
}

.hof-badge-card__icon {
    width: 52px;
    height: 52px;
    border-radius: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 1.25rem;
    margin-bottom: 1rem;
    transition: transform 0.5s cubic-bezier(0.16,1,0.3,1);
}
.hof-badge-card:hover .hof-badge-card__icon { transform: rotate(12deg) scale(1.1); }

.hof-badge-card__name {
    font-size: 1rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 0.35rem;
}

.hof-badge-card__desc {
    font-size: 0.75rem;
    color: #94a3b8;
    line-height: 1.5;
    margin: 0 0 1rem;
}

.hof-badge-card__req {
    border-top: 1px solid #f1f5f9;
    padding-top: 0.75rem;
}
.hof-badge-card__req-label {
    display: block;
    font-size: 0.6rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #cbd5e1;
    font-weight: 700;
    margin-bottom: 0.15rem;
}
.hof-badge-card__req-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: #475569;
}

/* ────── FLOATING ANIMATION FOR NODES ────── */
.hof-node--champion { animation: nodeEnter 0.7s cubic-bezier(0.16,1,0.3,1) forwards, nodeFloat 6s ease-in-out 1s infinite; }
.hof-node--silver   { animation: nodeEnter 0.7s cubic-bezier(0.16,1,0.3,1) 0.2s forwards, nodeFloat 7s ease-in-out 1.5s infinite; }
.hof-node--bronze   { animation: nodeEnter 0.7s cubic-bezier(0.16,1,0.3,1) 0.3s forwards, nodeFloat 7s ease-in-out 2s infinite; }
.hof-node--leaf     { animation: nodeEnter 0.7s cubic-bezier(0.16,1,0.3,1) forwards, nodeFloat 8s ease-in-out 2.5s infinite; }

@keyframes nodeFloat {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
}

/* ────── RESPONSIVE ────── */
@media (max-width: 1024px) {
    .hof-tier--2 { gap: 2rem; }
    .hof-tier--3 { gap: 1rem; }
}

@media (max-width: 768px) {
    .hof-header { padding: 2rem 1.25rem; border-radius: 1rem; }
    .hof-header__title { font-size: 1.75rem; }
    .hof-header__icon { font-size: 1.5rem; }
    .hof-myrank { padding: 0.75rem 1.25rem; gap: 1rem; }
    .hof-myrank__value { font-size: 1.35rem; }

    .hof-tree { gap: 1.25rem; }
    .hof-tier { gap: 1rem; }
    .hof-tier--2 { flex-direction: column; align-items: center; gap: 1rem; }
    .hof-tier--3 { 
        display: grid; 
        grid-template-columns: repeat(2, 1fr); 
        gap: 0.75rem; 
        padding: 0 0.5rem;
    }
    .hof-node--leaf { max-width: none; }
    .hof-node--champion { min-width: auto; padding: 1.5rem 1.25rem; }
    .hof-node--silver, .hof-node--bronze { min-width: auto; }

    .hof-guru-deck { flex-direction: column; align-items: center; }
    .hof-guru-card { max-width: 280px; width: 100%; }

    .hof-section__title { font-size: 1.2rem; }
    .hof-badges-grid { grid-template-columns: repeat(2, 1fr); }
}

@media (max-width: 480px) {
    .hof-universe { padding: 1rem 0; }
    .hof-header { padding: 1.5rem 1rem; }
    .hof-header__title { font-size: 1.5rem; }
    .hof-tier--3 { grid-template-columns: 1fr; }
    .hof-badges-grid { grid-template-columns: 1fr; }
    .hof-myrank { flex-direction: column; gap: 0.5rem; }
    .hof-myrank__divider { width: 60px; height: 1px; }
}

/* ────── SVG LINE DRAW ANIMATION ────── */
.hof-connector {
    stroke-dasharray: 200;
    stroke-dashoffset: 200;
    animation: drawLine 1.2s ease-out forwards;
}
.hof-connector--d1 { animation-delay: 0.6s; }
.hof-connector--d2 { animation-delay: 0.8s; }
.hof-connector--d3 { animation-delay: 1.0s; }
.hof-connector--d4 { animation-delay: 1.1s; }
.hof-connector--d5 { animation-delay: 1.2s; }
.hof-connector--d6 { animation-delay: 1.3s; }

@keyframes drawLine {
    to { stroke-dashoffset: 0; }
}
</style>

{{-- ═══════════════════ JAVASCRIPT: SVG CONNECTORS ═══════════════════ --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const svg = document.getElementById('hofTreeSvg');
    if (!svg) return;

    function getNodeCenter(id) {
        const el = document.getElementById(id);
        if (!el) return null;
        const tree = document.getElementById('hofTree');
        const treeRect = tree.getBoundingClientRect();
        const rect = el.getBoundingClientRect();
        return {
            x: rect.left + rect.width / 2 - treeRect.left,
            y: rect.top + rect.height / 2 - treeRect.top,
            top: rect.top - treeRect.top,
            bottom: rect.bottom - treeRect.top
        };
    }

    function drawConnectors() {
        // Clear existing
        svg.querySelectorAll('.hof-connector').forEach(el => el.remove());

        const tree = document.getElementById('hofTree');
        if (!tree) return;
        const treeRect = tree.getBoundingClientRect();
        svg.setAttribute('viewBox', `0 0 ${treeRect.width} ${treeRect.height}`);
        svg.style.width = treeRect.width + 'px';
        svg.style.height = treeRect.height + 'px';

        const n1 = getNodeCenter('node1');
        const n2 = getNodeCenter('node2');
        const n3 = getNodeCenter('node3');

        let delayIdx = 1;

        // Connect #1 -> #2
        if (n1 && n2) {
            drawCurve(svg, n1.x, n1.bottom, n2.x, n2.top, 'url(#lineGradGold)', delayIdx++);
        }
        // Connect #1 -> #3
        if (n1 && n3) {
            drawCurve(svg, n1.x, n1.bottom, n3.x, n3.top, 'url(#lineGradGold)', delayIdx++);
        }

        // Connect #2 -> #4, #5
        if (n2) {
            [4, 5].forEach(function(num) {
                const nx = getNodeCenter('node' + num);
                if (nx) {
                    drawCurve(svg, n2.x, n2.bottom, nx.x, nx.top, 'url(#lineGradSilver)', delayIdx++);
                }
            });
        }

        // Connect #3 -> #6, #7
        if (n3) {
            [6, 7].forEach(function(num) {
                const nx = getNodeCenter('node' + num);
                if (nx) {
                    drawCurve(svg, n3.x, n3.bottom, nx.x, nx.top, 'url(#lineGradSilver)', delayIdx++);
                }
            });
        }
    }

    function drawCurve(svg, x1, y1, x2, y2, stroke, delayIdx) {
        const midY = (y1 + y2) / 2;
        const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        const d = `M ${x1} ${y1} C ${x1} ${midY}, ${x2} ${midY}, ${x2} ${y2}`;
        path.setAttribute('d', d);
        path.setAttribute('stroke', stroke);
        path.setAttribute('fill', 'none');
        path.setAttribute('stroke-width', '2');
        path.setAttribute('stroke-linecap', 'round');
        path.classList.add('hof-connector', 'hof-connector--d' + delayIdx);

        // Calculate path length for dash animation
        svg.appendChild(path);
        const length = path.getTotalLength();
        path.style.strokeDasharray = length;
        path.style.strokeDashoffset = length;
    }

    // Stagger node animations
    document.querySelectorAll('.hof-node').forEach(function(node) {
        const delay = node.getAttribute('data-delay') || 0;
        node.style.animationDelay = delay + 'ms, 1.5s';
    });

    // Draw on load + resize
    setTimeout(drawConnectors, 300);
    window.addEventListener('resize', function() {
        requestAnimationFrame(drawConnectors);
    });
});
</script>
@endsection
