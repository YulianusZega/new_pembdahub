@extends('layouts.admin')

@section('title', 'Kelola Galeri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 shadow-lg">
                    <i class="fas fa-images text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Galeri</h1>
                    <p class="text-gray-600 mt-1">Kelola foto & dokumentasi yang ditampilkan di homepage</p>
                </div>
            </div>
            <a href="{{ route('admin.gallery.create') }}"
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-cyan-600 to-teal-600 text-white rounded-xl font-medium hover:from-cyan-700 hover:to-teal-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i> Tambah Foto
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-500 rounded-lg p-4 shadow-sm">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <!-- Filters -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="w-48">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Kategori</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                    <option value="">Semua</option>
                    @foreach(['upacara','praktikum','olahraga','seni','bengkel','prestasi','komputer','lainnya'] as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-800 text-white rounded-xl text-sm font-medium hover:bg-gray-900 transition">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request('category'))
            <a href="{{ route('admin.gallery.index') }}" class="px-4 py-2 text-gray-600 border border-gray-200 rounded-xl text-sm hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Gallery Grid -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($galleryItems as $item)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden group hover:shadow-xl transition-all">
            <div class="relative h-48 overflow-hidden">
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity">
                    <div class="absolute bottom-3 left-3 right-3 flex justify-between items-end">
                        <span class="text-white text-sm font-semibold">{{ $item->title }}</span>
                    </div>
                </div>
                @if($item->is_featured)
                <div class="absolute top-2 right-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg">
                    <i class="fas fa-star mr-0.5"></i> Featured
                </div>
                @endif
                @if(!$item->is_active)
                <div class="absolute top-2 left-2 bg-gray-800/80 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg">
                    <i class="fas fa-eye-slash mr-0.5"></i> Hidden
                </div>
                @endif
            </div>
            <div class="p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-cyan-50 text-cyan-700">
                        <i class="{{ $item->category_icon }} text-[8px]"></i> {{ $item->category_label }}
                    </span>
                    <span class="text-xs text-gray-400">Order: {{ $item->sort_order }}</span>
                </div>
                <h4 class="font-semibold text-gray-900 text-sm mb-1 truncate">{{ $item->title }}</h4>
                @if($item->caption)
                <p class="text-xs text-gray-500 line-clamp-2">{{ $item->caption }}</p>
                @endif
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.gallery.edit', $item) }}"
                        class="flex-1 text-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold transition">
                        <i class="fas fa-pen mr-1"></i> Edit
                    </a>
                    <form action="{{ route('admin.gallery.destroy', $item) }}" method="POST" class="flex-1">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="w-full px-3 py-1.5 bg-red-50 hover:bg-red-100 text-red-700 rounded-lg text-xs font-semibold transition"
                            onclick="return confirm('Hapus foto ini?')">
                            <i class="fas fa-trash mr-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center">
            <div class="flex flex-col items-center gap-3">
                <i class="fas fa-images text-gray-300 text-5xl"></i>
                <p class="text-gray-500 font-medium">Belum ada foto di galeri</p>
                <a href="{{ route('admin.gallery.create') }}" class="text-cyan-600 hover:text-cyan-700 font-medium">
                    Tambah foto pertama →
                </a>
            </div>
        </div>
        @endforelse
    </div>

    @if($galleryItems->hasPages())
    <div class="mt-6">
        {{ $galleryItems->links() }}
    </div>
    @endif
</div>
@endsection
