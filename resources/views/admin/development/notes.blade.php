@extends('layouts.admin')
@section('title', 'Catatan Perkembangan - ' . $student->name)
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center text-white">
                    <i class="fas fa-sticky-note text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Catatan Perkembangan</h1>
                    <p class="text-gray-600 mt-1">{{ $student->name }}</p>
                </div>
            </div>
            <a href="{{ route('admin.students.development.profile', $student) }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                <i class="fas fa-arrow-left mr-2"></i> Profil
            </a>
        </div>
    </div>

    <!-- Add Note Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Tambah Catatan</h2>
        <form action="{{ route('admin.students.development.notes.store', $student) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <select name="category" required class="w-full rounded-xl border-gray-300 focus:ring-teal-500">
                        <option value="akademik">Akademik</option>
                        <option value="perilaku">Perilaku</option>
                        <option value="sosial">Sosial</option>
                        <option value="bakat">Bakat & Minat</option>
                        <option value="kesehatan">Kesehatan</option>
                    </select>
                </div>
                <div>
                    <input type="date" name="note_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl border-gray-300 focus:ring-teal-500">
                </div>
                <div>
                    <select name="semester_id" class="w-full rounded-xl border-gray-300 focus:ring-teal-500">
                        <option value="">-- Semester (opsional) --</option>
                        @foreach($semesters ?? [] as $sem)
                        <option value="{{ $sem->id }}">{{ $sem->semester_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mb-4">
                <textarea name="content" rows="3" required class="w-full rounded-xl border-gray-300 focus:ring-teal-500" placeholder="Tulis catatan perkembangan siswa..."></textarea>
            </div>
            <button type="submit" class="px-5 py-2 bg-gradient-to-r from-teal-500 to-cyan-600 text-white rounded-xl hover:shadow-lg transition">
                <i class="fas fa-plus mr-2"></i> Tambah Catatan
            </button>
        </form>
    </div>

    @if(session('success'))
    <div class="p-4 bg-green-50 border-l-4 border-green-500 rounded-xl"><p class="text-green-700">{{ session('success') }}</p></div>
    @endif

    <!-- Notes List -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Semua Catatan</h2>
        @forelse($notes as $note)
        <div class="border-l-4 border-teal-400 pl-4 mb-6 pb-4 border-b border-gray-100 last:border-b-0">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-1 text-xs rounded-lg bg-teal-100 text-teal-800">{{ ucfirst($note->category ?? 'umum') }}</span>
                    <span class="text-xs text-gray-500">oleh {{ $note->teacher->name ?? $note->user->name ?? 'System' }}</span>
                </div>
                <span class="text-xs text-gray-500">{{ $note->note_date?->format('d M Y') ?? $note->created_at->format('d M Y') }}</span>
            </div>
            <p class="text-sm text-gray-700 whitespace-pre-line">{{ $note->content }}</p>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">Belum ada catatan perkembangan.</p>
        @endforelse
        @if(method_exists($notes, 'links'))
        <div class="mt-4">{{ $notes->links() }}</div>
        @endif
    </div>
</div>
@endsection
