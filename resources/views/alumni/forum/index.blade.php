@extends('layouts.alumni')

@section('title', 'Forum Alumni')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <div class="bg-gradient-to-br from-indigo-600 to-blue-700 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute right-0 top-0 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3"></div>
        <div class="relative z-10 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold mb-2">Forum Alumni</h1>
                <p class="text-indigo-100 text-sm">Ruang diskusi eksklusif untuk alumni satu unit sekolah Anda.</p>
            </div>
            <a href="{{ route('alumni.forum.create') }}" class="bg-white text-indigo-700 hover:bg-indigo-50 px-5 py-2.5 rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Buat Topik
            </a>
        </div>
    </div>

    <!-- Filter Categories -->
    <div class="flex overflow-x-auto pb-2 gap-2 hide-scrollbar">
        <a href="{{ route('alumni.forum.index') }}" class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition {{ !$category ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Semua Topik
        </a>
        @foreach($categories as $key => $label)
            <a href="{{ route('alumni.forum.index', ['category' => $key]) }}" class="px-4 py-2 rounded-xl text-sm font-semibold whitespace-nowrap transition {{ $category === $key ? 'bg-indigo-600 text-white shadow-md' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Forum List -->
    <div class="space-y-4">
        @forelse($threads as $thread)
            <a href="{{ route('alumni.forum.show', $thread->id) }}" class="block bg-white border border-gray-100 rounded-2xl p-5 shadow-sm hover:shadow-md transition">
                <div class="flex items-start gap-4">
                    <img src="{{ $thread->user->photo_url }}" alt="Avatar" class="w-12 h-12 rounded-full object-cover shrink-0 border border-gray-200">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="bg-indigo-50 text-indigo-600 text-[10px] font-bold px-2 py-0.5 rounded uppercase">{{ $thread->category_label }}</span>
                            <span class="text-xs text-gray-400">&bull; {{ $thread->created_at->diffForHumans() }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 truncate">{{ $thread->title }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($thread->content), 120) }}</p>
                        
                        <div class="flex items-center gap-4 mt-4 text-xs font-medium text-gray-400">
                            <span class="flex items-center gap-1"><i class="fas fa-user text-gray-300"></i> {{ $thread->user->name }}</span>
                            <span class="flex items-center gap-1"><i class="fas fa-eye text-gray-300"></i> {{ $thread->views_count }} dilihat</span>
                            <span class="flex items-center gap-1"><i class="fas fa-comment text-gray-300"></i> {{ $thread->replies->count() }} balasan</span>
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white border border-gray-100 rounded-2xl p-10 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                    <i class="fas fa-folder-open text-2xl"></i>
                </div>
                <h3 class="text-gray-900 font-bold mb-1">Belum ada topik diskusi</h3>
                <p class="text-sm text-gray-500 mb-4">Jadilah yang pertama memulai obrolan di kategori ini.</p>
                <a href="{{ route('alumni.forum.create') }}" class="text-indigo-600 font-semibold text-sm hover:underline">Buat Topik Sekarang</a>
            </div>
        @endforelse
    </div>
    
    {{ $threads->links() }}
</div>
@endsection
