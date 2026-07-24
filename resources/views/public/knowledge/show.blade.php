@extends('layouts.app')
@section('title', $material->title . ' - Pembda Knowledge & Media')

@section('sidebar-menu')
    <div class="space-y-4">
        {{-- Kembali ke Dashboard Utama PembdaHUB --}}
        <div class="pb-2 border-b border-slate-200">
            @auth
                @if(auth()->user()->isGuru())
                    <a href="{{ route('guru.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 bg-teal-50 hover:bg-teal-100 text-teal-800 rounded-xl text-xs font-black transition-all border border-teal-200 shadow-sm">
                        <span class="flex items-center gap-2.5"><i class="fas fa-arrow-left text-teal-600 text-sm"></i> Dashboard Guru</span>
                        <i class="fas fa-home text-teal-600"></i>
                    </a>
                @elseif(auth()->user()->isSiswa())
                    <a href="{{ route('siswa.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 rounded-xl text-xs font-black transition-all border border-indigo-200 shadow-sm">
                        <span class="flex items-center gap-2.5"><i class="fas fa-arrow-left text-indigo-600 text-sm"></i> Dashboard Siswa</span>
                        <i class="fas fa-home text-indigo-600"></i>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center justify-between px-3 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-800 rounded-xl text-xs font-black transition-all border border-indigo-200 shadow-sm">
                        <span class="flex items-center gap-2.5"><i class="fas fa-arrow-left text-indigo-600 text-sm"></i> Admin Dashboard</span>
                        <i class="fas fa-home text-indigo-600"></i>
                    </a>
                @endif
            @else
                <a href="{{ url('/') }}" class="flex items-center justify-between px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 rounded-xl text-xs font-black transition-all border border-slate-200 shadow-sm">
                    <span class="flex items-center gap-2.5"><i class="fas fa-arrow-left text-slate-600 text-sm"></i> Beranda Utama</span>
                    <i class="fas fa-home text-slate-600"></i>
                </a>
            @endauth
        </div>

        {{-- Catalog Navigation Header --}}
        <div class="px-3 py-2 bg-gradient-to-r from-teal-600 to-indigo-600 rounded-xl text-white shadow-sm">
            <h3 class="text-xs font-black uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-cube text-teal-200"></i> Repositori Media
            </h3>
        </div>

        {{-- Menu Links (Dark text for white sidebar) --}}
        <div class="space-y-1">
            <a href="{{ route('knowledge.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:bg-teal-50 hover:text-teal-700 transition-all">
                <span class="flex items-center gap-2.5"><i class="fas fa-globe text-teal-600 text-sm"></i> Semua Koleksi</span>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
            </a>
            <a href="{{ route('knowledge.index', ['category' => 'sekolah']) }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:bg-indigo-50 hover:text-indigo-700 transition-all">
                <span class="flex items-center gap-2.5"><i class="fas fa-book text-indigo-600 text-sm"></i> Materi Sekolah</span>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
            </a>
            <a href="{{ route('knowledge.index', ['category' => 'umum']) }}" class="flex items-center justify-between px-3 py-2.5 rounded-xl text-xs font-bold text-slate-800 hover:bg-emerald-50 hover:text-emerald-700 transition-all">
                <span class="flex items-center gap-2.5"><i class="fas fa-lightbulb text-emerald-600 text-sm"></i> Umum & Hobi</span>
                <i class="fas fa-chevron-right text-[10px] text-slate-400"></i>
            </a>
        </div>

        {{-- Media Types --}}
        <div class="pt-3 border-t border-slate-200 space-y-1">
            <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-wider">Format Media</p>
            <a href="{{ route('knowledge.index', ['type' => 'document']) }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg">
                <i class="fas fa-file-pdf text-rose-500 text-sm"></i> Dokumen PDF
            </a>
            <a href="{{ route('knowledge.index', ['type' => 'video']) }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg">
                <i class="fas fa-play-circle text-rose-600 text-sm"></i> Video Edukasi
            </a>
            <a href="{{ route('knowledge.index', ['type' => 'audio']) }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg">
                <i class="fas fa-headphones text-amber-500 text-sm"></i> Podcast & Audio
            </a>
            <a href="{{ route('knowledge.index', ['type' => 'link']) }}" class="flex items-center gap-2.5 px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-100 hover:text-slate-900 rounded-lg">
                <i class="fas fa-globe text-sky-500 text-sm"></i> Tautan Eksternal
            </a>
        </div>

        {{-- User Role Shortcuts --}}
        @auth
            <div class="pt-3 border-t border-slate-200 space-y-1">
                <p class="px-3 text-[10px] font-black text-slate-400 uppercase tracking-wider">Ruang Kerja</p>
                @if(auth()->user()->isGuru())
                    <a href="{{ route('guru.knowledge.index') }}" class="flex items-center gap-2 px-3 py-2.5 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-xl text-xs font-bold transition-all border border-teal-200">
                        <i class="fas fa-plus-circle"></i> Kelola Karya Guru
                    </a>
                @elseif(auth()->user()->isSuperAdmin() || auth()->user()->isAdminSekolah() || auth()->user()->isKepalaSekolah())
                    <a href="{{ route('admin.knowledge.monitoring') }}" class="flex items-center gap-2 px-3 py-2.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-xs font-bold transition-all border border-indigo-200">
                        <i class="fas fa-chart-line"></i> Pantauan Pimpinan
                    </a>
                @endif
            </div>
        @endauth
    </div>
@endsection

@section('content')
<div class="min-h-screen bg-slate-900 text-slate-100 pb-20" x-data="knowledgeViewer({{ $material->id }}, {{ $isLiked ? 'true' : 'false' }}, {{ $isBookmarked ? 'true' : 'false' }}, {{ $material->likes_count }}, {{ $material->bookmarks_count }}, {{ $material->views_count }}, {{ $material->downloads_count }})">

    {{-- Top Action Toolbar --}}
    <div class="bg-slate-950/90 border-b border-slate-800 sticky top-0 z-30 px-4 py-3 shadow-md backdrop-blur-md">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <a href="{{ route('knowledge.index') }}" class="text-xs font-bold text-slate-300 hover:text-teal-400 flex items-center gap-2 bg-slate-800 hover:bg-slate-700 px-3.5 py-2 rounded-xl transition-all border border-slate-700">
                <i class="fas fa-arrow-left"></i> Kembali ke Katalog
            </a>

            <div class="flex items-center gap-2">
                {{-- Edit & Delete Buttons for Owner / Admin --}}
                @auth
                    @php
                        $isOwner = auth()->user()->teacher && auth()->user()->teacher->id === $material->teacher_id;
                        $isAdminUser = auth()->user()->isSuperAdmin() || auth()->user()->isAdminSekolah() || auth()->user()->isKepalaSekolah();
                        $canEdit = $isOwner || $isAdminUser;
                        $canDelete = $isOwner || $isAdminUser;
                    @endphp

                    @if($canEdit)
                        <a href="{{ route('guru.knowledge.edit', $material->id) }}" class="px-3.5 py-2 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-black shadow-md transition-all flex items-center gap-1.5" title="Edit Materi Ini">
                            <i class="fas fa-edit"></i> Edit Materi
                        </a>
                    @endif

                    @if($canDelete)
                        <form action="{{ $isOwner ? route('guru.knowledge.destroy', $material->id) : route('admin.knowledge.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3.5 py-2 bg-rose-600 hover:bg-rose-500 text-white rounded-xl text-xs font-bold shadow-md transition-all flex items-center gap-1.5" title="Hapus Materi Ini">
                                <i class="fas fa-trash"></i> Hapus Materi
                            </button>
                        </form>
                    @endif
                @endauth

                {{-- Fullscreen Toggle Button --}}
                @if($material->type === 'document' && $material->file_url)
                    <button @click="toggleFullscreenViewer()" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold border border-slate-700 transition-all flex items-center gap-1.5" title="Buka Layar Penuh">
                        <i class="fas fa-expand text-teal-400"></i> Layar Penuh
                    </button>
                @endif

                {{-- Share Button --}}
                <button @click="showShareModal = true" class="px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-xl text-xs font-bold border border-slate-700 transition-all flex items-center gap-1.5">
                    <i class="fas fa-share-alt text-indigo-400"></i> Bagikan
                </button>

                @if($material->allow_download && $material->file_path)
                    <a href="{{ route('knowledge.download', $material->slug) }}" class="px-4 py-2 bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-400 hover:to-emerald-400 text-slate-950 rounded-xl text-xs font-extrabold shadow-md transition-all flex items-center gap-1.5">
                        <i class="fas fa-download"></i> Unduh Dokumen
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6 space-y-6">

        {{-- Header Card (Rich Deep Slate Gradient) --}}
        <div class="bg-gradient-to-br from-slate-950 via-slate-900 to-indigo-950 rounded-3xl p-6 md:p-8 border border-slate-800 shadow-2xl space-y-5 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-[400px] h-[400px] bg-teal-500/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>

            <div class="flex flex-wrap items-center gap-2 relative z-10">
                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider text-white shadow-md
                    {{ $material->category_type === 'sekolah' ? 'bg-indigo-600' : 'bg-emerald-600' }}">
                    {{ $material->category_type }}
                </span>
                @if($material->subject)
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-teal-500/20 text-teal-300 border border-teal-500/30 uppercase tracking-wider">
                        {{ $material->subject->subject_name }}
                    </span>
                @endif
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-800 text-slate-300 border border-slate-700 capitalize flex items-center gap-1.5">
                    @if($material->type === 'video') <i class="fas fa-play text-rose-400"></i>
                    @elseif($material->type === 'audio') <i class="fas fa-headphones text-amber-400"></i>
                    @elseif($material->type === 'link') <i class="fas fa-globe text-sky-400"></i>
                    @else <i class="fas fa-file-alt text-teal-400"></i>
                    @endif
                    Format {{ $material->type }}
                </span>
            </div>

            <h1 class="text-2xl sm:text-4xl font-black text-white leading-tight tracking-tight relative z-10">
                {{ $material->title }}
            </h1>

            {{-- Author Bar & Interactive Buttons --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-4 border-t border-slate-800/80 relative z-10">
                <div class="flex items-center gap-3">
                    <img src="{{ $material->teacher->photo_url ?? asset('images/default-student.jpg') }}" class="w-12 h-12 rounded-full object-cover border-2 border-teal-500 shadow-md">
                    <div>
                        <h4 class="text-sm font-bold text-white">{{ $material->teacher->full_name ?? 'Guru Pembda' }}</h4>
                        <p class="text-xs text-slate-400">Dipublikasikan {{ $material->created_at->translatedFormat('d F Y') }}</p>
                    </div>
                </div>

                {{-- Interactive Like, Bookmark & Share Buttons --}}
                <div class="flex items-center gap-2">
                    {{-- Like Button --}}
                    <button @click="toggleLike()" class="px-4 py-2 rounded-xl text-xs font-bold border transition-all flex items-center gap-2"
                        :class="liked ? 'bg-rose-500/20 border-rose-500/50 text-rose-400' : 'bg-slate-800 border-slate-700 text-slate-300 hover:border-slate-600'">
                        <i class="fas fa-heart text-sm" :class="liked ? 'text-rose-500 animate-pulse' : 'text-slate-400'"></i>
                        <span x-text="likesCount"></span> Suka
                    </button>

                    {{-- Bookmark Button --}}
                    <button @click="toggleBookmark()" class="px-4 py-2 rounded-xl text-xs font-bold border transition-all flex items-center gap-2"
                        :class="bookmarked ? 'bg-indigo-500/20 border-indigo-500/50 text-indigo-400' : 'bg-slate-800 border-slate-700 text-slate-300 hover:border-slate-600'">
                        <i class="fas fa-bookmark text-sm" :class="bookmarked ? 'text-indigo-400' : 'text-slate-400'"></i>
                        <span x-text="bookmarked ? 'Tersimpan' : 'Simpan'"></span>
                    </button>

                    {{-- Share Modal Trigger --}}
                    <button @click="showShareModal = true" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-teal-400 rounded-xl text-xs font-bold border border-slate-700 transition-all flex items-center gap-2">
                        <i class="fas fa-share-alt"></i> Bagikan
                    </button>
                </div>
            </div>
        </div>

        {{-- 📊 INDIKATOR IMPRESI & KETERLIBATAN PENGUNJUNG (SERIUS & LENGKAP) --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            {{-- Views Indicator --}}
            <div class="bg-slate-900/90 p-4 rounded-2xl border border-slate-800 shadow-xl flex items-center gap-3 relative overflow-hidden">
                <div class="w-11 h-11 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-xl font-bold border border-sky-500/30">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Melihat / Pembaca</p>
                    <h4 class="text-xl font-black text-white mt-0.5" x-text="viewsCount"></h4>
                </div>
            </div>

            {{-- Likes Indicator --}}
            <div class="bg-slate-900/90 p-4 rounded-2xl border border-slate-800 shadow-xl flex items-center gap-3 relative overflow-hidden">
                <div class="w-11 h-11 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-xl font-bold border border-rose-500/30">
                    <i class="fas fa-heart"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Suka Karya</p>
                    <h4 class="text-xl font-black text-white mt-0.5" x-text="likesCount"></h4>
                </div>
            </div>

            {{-- Bookmarks Indicator --}}
            <div class="bg-slate-900/90 p-4 rounded-2xl border border-slate-800 shadow-xl flex items-center gap-3 relative overflow-hidden">
                <div class="w-11 h-11 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xl font-bold border border-indigo-500/30">
                    <i class="fas fa-bookmark"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Favorit Disimpan</p>
                    <h4 class="text-xl font-black text-white mt-0.5" x-text="bookmarksCount"></h4>
                </div>
            </div>

            {{-- Downloads Indicator --}}
            <div class="bg-slate-900/90 p-4 rounded-2xl border border-slate-800 shadow-xl flex items-center gap-3 relative overflow-hidden">
                <div class="w-11 h-11 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xl font-bold border border-emerald-500/30">
                    <i class="fas fa-download"></i>
                </div>
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Unduhan File</p>
                    <h4 class="text-xl font-black text-white mt-0.5" x-text="downloadsCount"></h4>
                </div>
            </div>
        </div>

        {{-- MAIN VIEWER SECTION (TINGGI HALAMAN MEMBACA DIPERBESAR & SANGAT LEGAS) --}}
        <div class="bg-slate-950 rounded-3xl border border-slate-800 overflow-hidden shadow-2xl" id="media-viewer-container">
            @if($material->type === 'video')
                {{-- Video Player --}}
                <div class="relative w-full aspect-video bg-black flex items-center justify-center">
                    @if($material->youtube_embed_url)
                        <iframe src="{{ $material->youtube_embed_url }}" class="w-full h-full border-0" allowfullscreen></iframe>
                    @elseif($material->file_url)
                        <video controls class="w-full h-full">
                            <source src="{{ $material->file_url }}" type="video/mp4">
                            Browser Anda tidak mendukung pemutar video.
                        </video>
                    @elseif($material->external_url)
                        <div class="text-center p-8 space-y-4 bg-slate-900 text-white">
                            <i class="fas fa-video text-5xl text-rose-400"></i>
                            <p class="text-sm text-slate-300">Tautan video eksternal:</p>
                            <a href="{{ $material->external_url }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-rose-600 text-white font-bold text-sm rounded-xl hover:bg-rose-500 transition-all">
                                <i class="fas fa-external-link-alt"></i> Tonton di Sumber Eksternal
                            </a>
                        </div>
                    @endif
                </div>

            @elseif($material->type === 'audio')
                {{-- Audio Player --}}
                <div class="p-8 sm:p-12 bg-gradient-to-br from-amber-950 via-slate-900 to-slate-950 text-white text-center space-y-6 border border-amber-500/20">
                    <div class="w-24 h-24 bg-amber-500/20 rounded-full flex items-center justify-center text-amber-400 text-4xl mx-auto border-2 border-amber-500/40 shadow-xl backdrop-blur-md">
                        <i class="fas fa-headphones-alt animate-bounce"></i>
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-xl font-extrabold text-white">{{ $material->title }}</h3>
                        <p class="text-xs text-amber-300/80">Audio Podcast / Materi Suara Guru</p>
                    </div>

                    @if($material->file_url)
                        <div class="max-w-md mx-auto bg-slate-900/80 p-4 rounded-2xl backdrop-blur-md border border-slate-700">
                            <audio controls class="w-full">
                                <source src="{{ $material->file_url }}" type="audio/mpeg">
                                Browser Anda tidak mendukung pemutar audio.
                            </audio>
                        </div>
                    @elseif($material->external_url)
                        <a href="{{ $material->external_url }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-amber-500 text-slate-950 font-extrabold text-sm rounded-xl shadow-lg">
                            <i class="fas fa-external-link-alt"></i> Dengarkan di Audio Link
                        </a>
                    @endif
                </div>

            @elseif($material->type === 'document')
                {{-- Document Viewer (Tinggi Halaman Membaca Sangat Besar 850px / 85vh) --}}
                @if($material->file_url)
                    <div class="w-full bg-slate-900 flex flex-col" style="height: 85vh; min-height: 850px;">
                        {{-- Viewer Toolbar --}}
                        <div class="bg-slate-950 text-slate-200 px-5 py-3 border-b border-slate-800 flex items-center justify-between text-xs">
                            <span class="flex items-center gap-2 font-bold text-teal-400 text-sm">
                                <i class="fas fa-file-pdf"></i> Dokumen Pembelajaran Digital
                            </span>
                            <div class="flex items-center gap-3">
                                <button @click="toggleFullscreenViewer()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg border border-slate-700 transition-all font-bold flex items-center gap-1.5">
                                    <i class="fas fa-expand text-teal-400"></i> Layar Penuh
                                </button>
                                <a href="{{ $material->file_url }}" target="_blank" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 rounded-lg border border-slate-700 transition-all font-bold flex items-center gap-1.5">
                                    <i class="fas fa-external-link-alt"></i> Tab Baru
                                </a>
                                @if($material->allow_download)
                                    <a href="{{ route('knowledge.download', $material->slug) }}" class="px-3.5 py-1.5 bg-teal-500 hover:bg-teal-400 text-slate-950 font-black rounded-lg transition-all flex items-center gap-1.5 shadow-md">
                                        <i class="fas fa-download"></i> Unduh PDF
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Main iFrame Reader --}}
                        <iframe src="{{ $material->file_url }}" class="w-full flex-1 border-0" style="height: 100%; min-height: 800px;" allowfullscreen id="pdf-document-iframe"></iframe>
                    </div>
                @else
                    <div class="p-16 text-center space-y-4">
                        <i class="fas fa-file-pdf text-6xl text-teal-400"></i>
                        <p class="text-sm font-semibold text-slate-300">File dokumen publikasi.</p>
                        @if($material->external_url)
                            <a href="{{ $material->external_url }}" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-teal-500 text-slate-950 font-bold text-sm rounded-xl shadow-md">
                                <i class="fas fa-external-link-alt"></i> Buka Tautan Dokumen
                            </a>
                        @endif
                    </div>
                @endif

            @elseif($material->type === 'link')
                {{-- External Link Card --}}
                <div class="p-16 text-center space-y-6 bg-gradient-to-br from-slate-900 to-indigo-950 text-white">
                    <div class="w-20 h-20 bg-sky-500/20 rounded-2xl flex items-center justify-center text-sky-400 text-4xl mx-auto border border-sky-500/30 shadow-xl">
                        <i class="fas fa-globe"></i>
                    </div>
                    <div class="space-y-2 max-w-lg mx-auto">
                        <h3 class="text-xl font-bold text-white">{{ $material->title }}</h3>
                        <p class="text-xs text-slate-300 truncate">{{ $material->external_url }}</p>
                    </div>
                    @if($material->external_url)
                        <a href="{{ $material->external_url }}" target="_blank" class="inline-flex items-center gap-2 px-8 py-3.5 bg-gradient-to-r from-sky-500 to-teal-400 text-slate-950 font-black text-sm rounded-xl shadow-lg hover:from-sky-400 hover:to-teal-300 transition-all">
                            <span>Buka Tautan Eksternal</span> <i class="fas fa-external-link-alt"></i>
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if($material->description)
            <div class="bg-slate-900/90 rounded-2xl p-6 border border-slate-800 shadow-xl space-y-3">
                <h3 class="text-sm font-bold text-teal-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-align-left"></i> Deskripsi & Penjelasan Lengkap Materi
                </h3>
                <div class="text-sm text-slate-300 leading-relaxed whitespace-pre-line">
                    {{ $material->description }}
                </div>
            </div>
        @endif
    </div>

    {{-- Share Modal --}}
    <div x-show="showShareModal" class="fixed inset-0 z-50 bg-black/70 backdrop-blur-sm flex items-center justify-center p-4" x-cloak x-transition>
        <div @click.away="showShareModal = false" class="bg-slate-800 rounded-3xl border border-slate-700 p-6 max-w-md w-full space-y-6 shadow-2xl relative text-white">
            <button @click="showShareModal = false" class="absolute top-4 right-4 text-slate-400 hover:text-white text-lg">
                <i class="fas fa-times"></i>
            </button>

            <div class="space-y-1">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <i class="fas fa-share-alt text-teal-400"></i> Bagikan Tautan Materi
                </h3>
                <p class="text-xs text-slate-400">Bagikan karya ini kepada siswa, rekan guru, atau grup WhatsApp.</p>
            </div>

            {{-- QR Code Section --}}
            <div class="bg-white p-4 rounded-2xl w-48 h-48 mx-auto flex items-center justify-center shadow-lg">
                <img src="{{ $qrCodeUrl }}" alt="QR Code Link" class="w-full h-full object-contain">
            </div>
            <p class="text-[11px] text-center text-slate-400">Scan QR Code menggunakan smartphone untuk membuka langsung.</p>

            {{-- Share Options --}}
            <div class="space-y-3">
                {{-- Copy Link Button --}}
                <div class="flex items-center gap-2 p-2 bg-slate-900 rounded-xl border border-slate-700">
                    <input type="text" readonly value="{{ $shareUrl }}" class="flex-1 bg-transparent border-0 text-xs text-slate-300 px-2 focus:outline-none">
                    <button @click="copyShareLink('{{ $shareUrl }}')" class="px-4 py-2 bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs rounded-lg transition-all shadow-sm">
                        <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                    </button>
                </div>

                {{-- WhatsApp Share --}}
                <a href="{{ $waShareUrl }}" target="_blank" class="w-full py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl flex items-center justify-center gap-2 transition-all shadow-md">
                    <i class="fab fa-whatsapp text-lg"></i> Bagikan ke WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function knowledgeViewer(id, initialLiked, initialBookmarked, initialLikesCount, initialBookmarksCount, initialViewsCount, initialDownloadsCount) {
        return {
            showShareModal: false,
            copied: false,
            liked: initialLiked,
            bookmarked: initialBookmarked,
            likesCount: initialLikesCount,
            bookmarksCount: initialBookmarksCount,
            viewsCount: initialViewsCount,
            downloadsCount: initialDownloadsCount,

            toggleLike() {
                fetch(`/knowledge/${id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (res.status === 401) {
                        alert('Silakan login terlebih dahulu untuk menyukai materi.');
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success) {
                        this.liked = data.liked;
                        this.likesCount = data.likes_count;
                    }
                })
                .catch(err => console.error(err));
            },

            toggleBookmark() {
                fetch(`/knowledge/${id}/bookmark`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(res => {
                    if (res.status === 401) {
                        alert('Silakan login terlebih dahulu untuk menyimpan favorit.');
                        return;
                    }
                    return res.json();
                })
                .then(data => {
                    if (data && data.success) {
                        this.bookmarked = data.bookmarked;
                        this.bookmarksCount = data.bookmarks_count;
                    }
                })
                .catch(err => console.error(err));
            },

            copyShareLink(url) {
                navigator.clipboard.writeText(url).then(() => {
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2500);
                });
            },

            toggleFullscreenViewer() {
                const el = document.getElementById('pdf-document-iframe') || document.getElementById('media-viewer-container');
                if (el) {
                    if (!document.fullscreenElement) {
                        el.requestFullscreen().catch(err => alert(`Gagal masuk layar penuh: ${err.message}`));
                    } else {
                        document.exitFullscreen();
                    }
                }
            }
        }
    }
</script>
@endsection
