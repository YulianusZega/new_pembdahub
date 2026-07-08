@extends('layouts.admin')
@section('title', 'Profil Perkembangan - ' . $student->name)
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center text-white">
                    <i class="fas fa-user-circle text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Profil Perkembangan</h1>
                    <p class="text-gray-600 mt-1">{{ $student->name }} — {{ $student->nis }}</p>
                </div>
            </div>
            <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.students.lifecycle.history', $student) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center"><i class="fas fa-history text-indigo-600"></i></div>
                <div><div class="text-sm font-medium text-gray-900">Riwayat Status</div><div class="text-xs text-gray-500">{{ $statusHistories->count() }} perubahan</div></div>
            </div>
        </a>
        <a href="{{ route('admin.students.development.notes', $student) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center"><i class="fas fa-sticky-note text-teal-600"></i></div>
                <div><div class="text-sm font-medium text-gray-900">Catatan</div><div class="text-xs text-gray-500">{{ $notes->count() }} catatan</div></div>
            </div>
        </a>
        <a href="{{ route('admin.students.development.recommendations', $student) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center"><i class="fas fa-star text-amber-600"></i></div>
                <div><div class="text-sm font-medium text-gray-900">Rekomendasi</div><div class="text-xs text-gray-500">{{ $recommendations->count() }} rekomendasi</div></div>
            </div>
        </a>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center"><i class="fas fa-hands-helping text-pink-600"></i></div>
                <div><div class="text-sm font-medium text-gray-900">Konseling</div><div class="text-xs text-gray-500">{{ $counselingRecords->count() }} catatan BK</div></div>
            </div>
        </div>
    </div>

    <!-- Recent Notes -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Catatan Terbaru</h2>
        @forelse($notes->take(5) as $note)
        <div class="border-l-4 border-teal-400 pl-4 mb-4">
            <div class="flex items-center justify-between">
                <span class="px-2 py-1 text-xs rounded-lg bg-teal-100 text-teal-800">{{ ucfirst($note->category ?? 'umum') }}</span>
                <span class="text-xs text-gray-500">{{ $note->note_date?->format('d M Y') ?? $note->created_at->format('d M Y') }}</span>
            </div>
            <p class="text-sm text-gray-700 mt-2">{{ Str::limit($note->content, 200) }}</p>
        </div>
        @empty
        <p class="text-gray-500 text-center py-4">Belum ada catatan perkembangan.</p>
        @endforelse
    </div>

    <!-- Recent Counseling -->
    @if($counselingRecords->count())
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Konseling Terakhir</h2>
        @foreach($counselingRecords->take(3) as $record)
        <div class="border-l-4 border-pink-400 pl-4 mb-4">
            <div class="flex items-center justify-between">
                <span class="font-medium text-sm text-gray-900">{{ $record->title }}</span>
                <span class="text-xs text-gray-500">{{ $record->counseling_date?->format('d M Y') }}</span>
            </div>
            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($record->description, 150) }}</p>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
