@extends('layouts.alumni')

@section('title', 'Buat Topik Baru')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('alumni.forum.index') }}" class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-gray-500 hover:text-indigo-600 shadow-sm transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Mulai Obrolan Baru</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('alumni.forum.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Kategori -->
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Pilih Kategori Obrolan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($categories as $key => $label)
                        <label class="relative">
                            <input type="radio" name="category" value="{{ $key }}" class="peer sr-only" required>
                            <div class="p-3 bg-white border border-gray-200 rounded-xl cursor-pointer transition-all peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:ring-1 peer-checked:ring-indigo-600 hover:border-gray-300 flex items-center gap-2">
                                <div class="w-4 h-4 rounded-full border border-gray-300 peer-checked:border-[4px] peer-checked:border-indigo-600"></div>
                                <span class="text-sm font-semibold text-gray-700 peer-checked:text-indigo-700">{{ $label }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Judul -->
            <div>
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul Topik <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" required placeholder="Contoh: Ada lowongan desainer grafis nih di kantorku!" class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3">
            </div>

            <!-- Isi Konten -->
            <div>
                <label for="content" class="block text-sm font-bold text-gray-700 mb-2">Isi Pesan <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="6" required placeholder="Tuliskan apa yang ingin Anda bagikan atau diskusikan..." class="w-full border-gray-300 rounded-xl shadow-sm focus:border-indigo-500 focus:ring-indigo-500 px-4 py-3"></textarea>
            </div>

            <!-- Gambar (Opsional) -->
            <div>
                <label for="image" class="block text-sm font-bold text-gray-700 mb-2">Sematkan Gambar <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="file" name="image" id="image" accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
            </div>

            <hr class="border-gray-100">

            <div class="flex justify-end gap-3">
                <a href="{{ route('alumni.forum.index') }}" class="px-6 py-2.5 rounded-xl font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">Batal</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-md hover:shadow-lg transition">Posting Sekarang</button>
            </div>
        </form>
    </div>
</div>
@endsection
