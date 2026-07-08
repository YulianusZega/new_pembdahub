@extends('layouts.admin')

@section('title', $item->exists ? 'Edit Foto Galeri' : 'Tambah Foto Galeri')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.gallery.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-cyan-500 to-teal-600 shadow-lg">
                    <i class="fas fa-{{ $item->exists ? 'pen' : 'plus' }} text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $item->exists ? 'Edit Foto' : 'Tambah Foto' }}</h1>
                    <p class="text-gray-600 mt-1">{{ $item->exists ? 'Perbarui informasi foto galeri' : 'Tambah foto baru ke galeri homepage' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
        <ul class="text-red-700 text-sm list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ $item->exists ? route('admin.gallery.update', $item) : route('admin.gallery.store') }}"
          method="POST" enctype="multipart/form-data">
        @csrf
        @if($item->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-image text-cyan-500"></i> Detail Foto
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $item->title) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Judul foto galeri...">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Caption / Deskripsi</label>
                        <textarea name="caption" rows="3" maxlength="500"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent"
                            placeholder="Deskripsi singkat foto...">{{ old('caption', $item->caption) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Gambar {{ $item->exists ? '' : '*' }}</label>
                        @if($item->exists && $item->image)
                        <div class="mb-3">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-48 h-32 object-cover rounded-xl border">
                            <p class="text-xs text-gray-500 mt-1">Gambar saat ini</p>
                        </div>
                        @endif
                        <input type="file" name="image" accept="image/*" {{ $item->exists ? '' : 'required' }}
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent file:mr-3 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-cyan-50 file:text-cyan-700 hover:file:bg-cyan-100">
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, WebP. Maks 3MB.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cog text-cyan-500"></i> Pengaturan
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                            @foreach(['upacara' => 'Upacara Bendera', 'praktikum' => 'Praktikum Lab', 'olahraga' => 'Olahraga', 'seni' => 'Pentas Seni', 'bengkel' => 'Praktik Bengkel', 'prestasi' => 'Prestasi', 'komputer' => 'Lab Komputer', 'lainnya' => 'Lainnya'] as $val => $label)
                            <option value="{{ $val }}" {{ old('category', $item->category) == $val ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Urutan (Sort Order)</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order ?? 0) }}" min="0"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-cyan-500 focus:border-transparent">
                        <p class="text-xs text-gray-500 mt-1">Semakin kecil, semakin di atas</p>
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_featured" value="0">
                            <input type="checkbox" name="is_featured" value="1"
                                {{ old('is_featured', $item->is_featured) ? 'checked' : '' }}
                                class="w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-700">Featured</span>
                                <p class="text-xs text-gray-500">Tampil di slot besar (utama)</p>
                            </div>
                        </label>
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $item->exists ? $item->is_active : true) ? 'checked' : '' }}
                                class="w-5 h-5 text-cyan-600 border-gray-300 rounded focus:ring-cyan-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-700">Aktif</span>
                                <p class="text-xs text-gray-500">Tampilkan di homepage</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-cyan-600 to-teal-600 text-white rounded-xl font-medium hover:from-cyan-700 hover:to-teal-700 shadow-lg transition-all text-center">
                        <i class="fas fa-save mr-2"></i> {{ $item->exists ? 'Simpan' : 'Tambah Foto' }}
                    </button>
                    <a href="{{ route('admin.gallery.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
