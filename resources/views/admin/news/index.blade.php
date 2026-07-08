@extends('layouts.admin')

@section('title', 'Kelola Berita')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-pink-600 shadow-lg">
                    <i class="fas fa-newspaper text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Kelola Berita</h1>
                    <p class="text-gray-600 mt-1">Kelola berita & kegiatan yang ditampilkan di homepage</p>
                </div>
            </div>
            <a href="{{ route('admin.news.create') }}"
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-rose-600 to-pink-600 text-white rounded-xl font-medium hover:from-rose-700 hover:to-pink-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-plus"></i> Tambah Berita
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
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Cari</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..."
                    class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-transparent">
            </div>
            <div class="w-40">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Kategori</label>
                <select name="category" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="prestasi" {{ request('category') == 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                    <option value="kegiatan" {{ request('category') == 'kegiatan' ? 'selected' : '' }}>Kegiatan</option>
                    <option value="kerjasama" {{ request('category') == 'kerjasama' ? 'selected' : '' }}>Kerjasama</option>
                    <option value="pengumuman" {{ request('category') == 'pengumuman' ? 'selected' : '' }}>Pengumuman</option>
                </select>
            </div>
            <div class="w-36">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Status</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-rose-500 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2 bg-gray-800 text-white rounded-xl text-sm font-medium hover:bg-gray-900 transition">
                <i class="fas fa-search mr-1"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'category', 'status']))
            <a href="{{ route('admin.news.index') }}" class="px-4 py-2 text-gray-600 border border-gray-200 rounded-xl text-sm hover:bg-gray-50 transition">
                <i class="fas fa-times mr-1"></i> Reset
            </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider w-16">No</th>
                        <th class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider">Berita</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider w-32">Kategori</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider w-32">Status</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider w-36">Tanggal</th>
                        <th class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($news as $item)
                    <tr class="hover:bg-gradient-to-r hover:from-rose-50 hover:to-pink-50 transition-all">
                        <td class="px-6 py-4 text-center text-sm font-semibold text-gray-700">
                            {{ ($news->currentPage() - 1) * $news->perPage() + $loop->iteration }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div class="w-16 h-12 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center"
                                     style="background: linear-gradient(135deg, {{ $item->gradient_from }}, {{ $item->gradient_to }});">
                                    @if($item->image)
                                        <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="{{ $item->icon }} text-white text-lg opacity-50"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900 text-sm">{{ Str::limit($item->title, 50) }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ Str::limit($item->excerpt, 60) }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $item->category == 'prestasi' ? 'bg-amber-100 text-amber-800' : '' }} {{ $item->category == 'kegiatan' ? 'bg-emerald-100 text-emerald-800' : '' }} {{ $item->category == 'kerjasama' ? 'bg-blue-100 text-blue-800' : '' }} {{ $item->category == 'pengumuman' ? 'bg-violet-100 text-violet-800' : '' }}">
                                {{ $item->category_label }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.news.toggle-publish', $item) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium transition {{ $item->is_published ? 'bg-green-100 text-green-800 hover:bg-green-200' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 8 8"><circle cx="4" cy="4" r="3" /></svg>
                                    {{ $item->is_published ? 'Published' : 'Draft' }}
                                </button>
                            </form>
                        </td>
                        <td class="px-6 py-4 text-center text-sm text-gray-600">
                            {{ $item->formatted_date }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('admin.news.edit', $item) }}"
                                    class="p-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-all transform hover:scale-110" title="Edit">
                                    <i class="fas fa-pen text-xs"></i>
                                </a>
                                <form action="{{ route('admin.news.destroy', $item) }}" method="POST" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="p-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-all transform hover:scale-110" title="Hapus"
                                        onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <i class="fas fa-newspaper text-gray-300 text-5xl"></i>
                                <p class="text-gray-500 font-medium">Belum ada berita</p>
                                <a href="{{ route('admin.news.create') }}" class="text-rose-600 hover:text-rose-700 font-medium">
                                    Tambah berita pertama →
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($news->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $news->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
