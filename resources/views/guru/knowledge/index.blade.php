@extends('layouts.guru')
@section('title', 'Pembda Knowledge & Media - Ruang Karya Guru')

@section('content')
<div class="space-y-6">

    {{-- Hero Header & Stat Summary --}}
    <div class="relative overflow-hidden rounded-2xl p-6 md:p-8 bg-gradient-to-br from-teal-900 via-emerald-800 to-indigo-900 text-white shadow-xl">
        <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-emerald-400/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-500/20 backdrop-blur-md rounded-full border border-emerald-400/30 text-emerald-200 text-xs font-semibold">
                    <i class="fas fa-sparkles"></i> Ruang Karya & Knowledge Hub
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Pembda Knowledge & Media</h1>
                <p class="text-emerald-100/80 text-sm max-w-xl">
                    Ruang penyimpanan & publikasi berbagai materi pembelajaran, tutorial, audio, video, dan koleksi karya Anda untuk siswa & pengunjung PembdaHUB.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('guru.knowledge.create') }}" class="inline-flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-bold px-5 py-3 rounded-xl shadow-lg transition-all transform hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle text-lg"></i>
                    <span>Unggah Karya Baru</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Total Points Card --}}
        <div class="bg-gradient-to-br from-amber-500 to-amber-600 rounded-2xl p-4 text-white shadow-md flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-amber-100">Total Poin Karya</p>
                <h3 class="text-2xl font-black mt-1">{{ number_format($totalPoints) }}</h3>
            </div>
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center text-xl text-amber-100 backdrop-blur-md">
                <i class="fas fa-trophy"></i>
            </div>
        </div>

        {{-- Total Uploads --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Materi Terunggah</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalUploads }}</h3>
            </div>
            <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center text-lg">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>

        {{-- Likes --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Disukai (Likes)</p>
                <h3 class="text-2xl font-bold text-rose-600 mt-1">{{ $totalLikes }}</h3>
            </div>
            <div class="w-10 h-10 bg-rose-50 text-rose-600 rounded-xl flex items-center justify-center text-lg">
                <i class="fas fa-heart"></i>
            </div>
        </div>

        {{-- Bookmarks --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Disimpan Favorit</p>
                <h3 class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalBookmarks }}</h3>
            </div>
            <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg">
                <i class="fas fa-bookmark"></i>
            </div>
        </div>

        {{-- Views --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-500">Total Pembaca/Views</p>
                <h3 class="text-2xl font-bold text-sky-600 mt-1">{{ number_format($totalViews) }}</h3>
            </div>
            <div class="w-10 h-10 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-lg">
                <i class="fas fa-eye"></i>
            </div>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Content List Grid --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm space-y-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <i class="fas fa-layer-group text-teal-600"></i> Daftar Koleksi & Materi Anda
            </h2>
            <a href="{{ route('knowledge.index') }}" target="_blank" class="text-xs font-semibold text-teal-600 hover:text-teal-700 flex items-center gap-1">
                <span>Lihat Etalase Publik</span> <i class="fas fa-external-link-alt text-[10px]"></i>
            </a>
        </div>

        @if($materials->isEmpty())
            <div class="text-center py-12 text-slate-400 space-y-3">
                <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-full flex items-center justify-center text-3xl mx-auto">
                    <i class="fas fa-folder-plus"></i>
                </div>
                <p class="text-base font-medium">Belum ada materi atau karya yang Anda unggah.</p>
                <a href="{{ route('guru.knowledge.create') }}" class="inline-flex items-center gap-2 text-sm bg-emerald-600 text-white font-semibold px-4 py-2 rounded-xl hover:bg-emerald-700 transition-all">
                    <i class="fas fa-plus"></i> Unggah Materi Pertama
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($materials as $item)
                    <div class="bg-slate-50/70 rounded-2xl border border-slate-200 overflow-hidden flex flex-col hover:border-emerald-300 hover:shadow-md transition-all">
                        {{-- Header / Thumbnail Badge --}}
                        <div class="relative h-40 bg-slate-800 flex items-center justify-center overflow-hidden">
                            @if($item->thumbnail_url)
                                <img src="{{ $item->thumbnail_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-center text-slate-400 p-4">
                                    @if($item->type === 'video')
                                        <i class="fas fa-file-video text-5xl text-rose-400"></i>
                                    @elseif($item->type === 'audio')
                                        <i class="fas fa-file-audio text-5xl text-amber-400"></i>
                                    @elseif($item->type === 'link')
                                        <i class="fas fa-link text-5xl text-sky-400"></i>
                                    @else
                                        <i class="fas fa-file-pdf text-5xl text-teal-400"></i>
                                    @endif
                                </div>
                            @endif

                            {{-- Type & Category Badges --}}
                            <div class="absolute top-3 left-3 flex items-center gap-2">
                                <span class="px-2.5 py-1 rounded-lg text-xs font-bold text-white shadow-md backdrop-blur-md uppercase tracking-wider
                                    {{ $item->category_type === 'sekolah' ? 'bg-indigo-600/90' : 'bg-emerald-600/90' }}">
                                    {{ $item->category_type }}
                                </span>
                                <span class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-900/80 text-slate-200 shadow-md backdrop-blur-md capitalize flex items-center gap-1">
                                    @if($item->type === 'video') <i class="fas fa-play text-rose-400"></i>
                                    @elseif($item->type === 'audio') <i class="fas fa-headphones text-amber-400"></i>
                                    @elseif($item->type === 'link') <i class="fas fa-globe text-sky-400"></i>
                                    @else <i class="fas fa-file-alt text-teal-400"></i>
                                    @endif
                                    {{ $item->type }}
                                </span>
                            </div>

                            {{-- Visibility Badge --}}
                            <div class="absolute top-3 right-3">
                                @if($item->is_public)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500 text-white shadow" title="Dapat diakses Publik">
                                        <i class="fas fa-globe-asia"></i> Publik
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-600 text-white shadow" title="Private">
                                        <i class="fas fa-lock"></i> Privat
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Body --}}
                        <div class="p-5 flex-1 flex flex-col justify-between space-y-4">
                            <div class="space-y-2">
                                @if($item->subject)
                                    <p class="text-xs font-bold text-teal-600 uppercase tracking-wider">{{ $item->subject->subject_name }}</p>
                                @endif
                                <h3 class="text-base font-bold text-slate-800 line-clamp-2 hover:text-teal-600 transition-colors">
                                    <a href="{{ route('knowledge.show', $item->slug) }}" target="_blank">{{ $item->title }}</a>
                                </h3>
                                <p class="text-xs text-slate-500 line-clamp-2">{{ $item->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>

                            {{-- Stats Footer --}}
                            <div class="pt-3 border-t border-slate-200 flex items-center justify-between text-xs text-slate-500">
                                <div class="flex items-center gap-3">
                                    <span title="Views / Dilihat"><i class="fas fa-eye text-sky-500"></i> {{ $item->views_count }}</span>
                                    <span title="Likes / Disukai"><i class="fas fa-heart text-rose-500"></i> {{ $item->likes_count }}</span>
                                    <span title="Bookmarks / Disimpan"><i class="fas fa-bookmark text-indigo-500"></i> {{ $item->bookmarks_count }}</span>
                                    <span title="Downloads / Diunduh"><i class="fas fa-download text-emerald-500"></i> {{ $item->downloads_count }}</span>
                                </div>
                                <span class="font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md border border-amber-200">
                                    +{{ $item->points }} Poin
                                </span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center justify-between pt-2">
                                <a href="{{ route('knowledge.show', $item->slug) }}" target="_blank" class="text-xs font-semibold text-slate-600 hover:text-teal-600 flex items-center gap-1">
                                    <i class="fas fa-external-link-alt"></i> Pratinjau
                                </a>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('guru.knowledge.edit', $item->id) }}" class="px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-lg border border-amber-200 transition-colors text-xs flex items-center gap-1" title="Edit Materi">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>

                                    <form action="{{ route('guru.knowledge.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold rounded-lg border border-rose-200 transition-colors text-xs flex items-center gap-1" title="Hapus Materi">
                                            <i class="fas fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="pt-4">
                {{ $materials->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
