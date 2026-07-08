@extends('layouts.admin')
@section('title', 'Rekomendasi - ' . $student->name)
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-yellow-600 flex items-center justify-center text-white">
                    <i class="fas fa-star text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Rekomendasi Siswa</h1>
                    <p class="text-gray-600 mt-1">{{ $student->name }}</p>
                </div>
            </div>
            <a href="{{ route('admin.students.development.profile', $student) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Profil
            </a>
        </div>
    </div>

    <!-- Add Recommendation -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Rekomendasi</h2>
        <form action="{{ route('admin.students.development.recommendations.store', $student) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tipe</label>
                    <select name="type" required class="w-full rounded-xl border-gray-300 focus:ring-amber-500">
                        <option value="akademik">Akademik</option>
                        <option value="karir">Karir</option>
                        <option value="sosial">Sosial</option>
                        <option value="ekskul">Ekstrakurikuler</option>
                        <option value="umum">Umum</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Prioritas</label>
                    <select name="priority" class="w-full rounded-xl border-gray-300 focus:ring-amber-500">
                        <option value="normal">Normal</option>
                        <option value="tinggi">Tinggi</option>
                        <option value="rendah">Rendah</option>
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Judul</label>
                <input type="text" name="title" required class="w-full rounded-xl border-gray-300 focus:ring-amber-500" placeholder="Judul rekomendasi">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Isi Rekomendasi</label>
                <textarea name="content" rows="3" required class="w-full rounded-xl border-gray-300 focus:ring-amber-500" placeholder="Detail rekomendasi..."></textarea>
            </div>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-amber-500 to-yellow-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-plus mr-2"></i> Tambah Rekomendasi
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-xl"><p class="text-green-700">{{ session('success') }}</p></div>
    @endif

    <!-- Recommendations List -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Semua Rekomendasi</h2>
        @forelse($recommendations as $rec)
        <div class="border rounded-xl p-4 mb-4 last:mb-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs rounded-lg bg-amber-100 text-amber-800">{{ ucfirst($rec->type ?? 'umum') }}</span>
                    @if(($rec->priority ?? 'normal') === 'tinggi')
                    <span class="px-2 py-1 text-xs rounded-lg bg-red-100 text-red-800">Prioritas Tinggi</span>
                    @endif
                    <span class="px-2 py-1 text-xs rounded-lg {{ ($rec->status ?? 'aktif') === 'selesai' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">{{ ucfirst($rec->status ?? 'aktif') }}</span>
                </div>
                <span class="text-xs text-gray-500">{{ $rec->created_at->format('d M Y') }}</span>
            </div>
            <h3 class="font-medium text-gray-900 text-sm">{{ $rec->title }}</h3>
            <p class="text-sm text-gray-700 mt-1">{{ $rec->content }}</p>
            <div class="flex items-center justify-between mt-3">
                <span class="text-xs text-gray-400">Oleh: {{ $rec->recommender->name ?? '-' }}</span>
                @if(($rec->status ?? 'aktif') !== 'selesai')
                <form action="{{ route('admin.students.development.recommendations.update', [$student, $rec]) }}" method="POST" class="inline">
                    @csrf @method('PUT')
                    <input type="hidden" name="status" value="selesai">
                    <button type="submit" class="text-xs text-green-600 hover:text-green-800"><i class="fas fa-check mr-1"></i>Tandai Selesai</button>
                </form>
                @endif
            </div>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">Belum ada rekomendasi.</p>
        @endforelse
    </div>
</div>
@endsection
