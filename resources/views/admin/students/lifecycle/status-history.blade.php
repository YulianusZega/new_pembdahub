@extends('layouts.admin')

@section('title', 'Riwayat Status - ' . $student->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                <i class="fas fa-history text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Riwayat Status Siswa</h1>
                <p class="text-gray-500 mt-1">{{ $student->full_name }} ({{ $student->nis }})</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Detail Siswa
            </a>
        </div>
    </div>

    <!-- Current Status -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Status Saat Ini</h2>
        <div class="flex items-center gap-4">
            <span class="px-4 py-2 rounded-full text-sm font-semibold @switch($student->status) @case('aktif') bg-green-100 text-green-800 @break @case('lulus') bg-blue-100 text-blue-800 @break @case('pindah') bg-yellow-100 text-yellow-800 @break @case('keluar') bg-red-100 text-red-800 @break @case('cuti') bg-orange-100 text-orange-800 @break @case('calon') bg-gray-100 text-gray-800 @break @default bg-gray-100 text-gray-600 @endswitch">{{ ucfirst($student->status) }}</span>
            <a href="{{ route('admin.students.lifecycle.transition', $student) }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                <i class="fas fa-exchange-alt mr-2"></i> Ubah Status
            </a>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Timeline Perubahan Status</h2>

        @forelse($histories as $history)
        <div class="relative pl-8 pb-8 border-l-2 border-indigo-200 last:border-l-0 last:pb-0">
            <div class="absolute -left-2 top-0 w-4 h-4 rounded-full bg-indigo-500 border-2 border-white shadow"></div>
            <div class="bg-gray-50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full font-medium">{{ ucfirst($history->from_status) }}</span>
                        <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                        <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">{{ ucfirst($history->to_status) }}</span>
                    </div>
                    <span class="text-xs text-gray-500">{{ $history->effective_date?->format('d M Y H:i') ?? $history->created_at?->format('d M Y H:i') }}</span>
                </div>
                @if($history->reason)
                    <p class="text-sm text-gray-700"><strong>Alasan:</strong> {{ $history->reason }}</p>
                @endif
                @if($history->notes)
                    <p class="text-sm text-gray-600 mt-1">{{ $history->notes }}</p>
                @endif
                @if($history->document_number)
                    <p class="text-xs text-gray-500 mt-1"><i class="fas fa-file-alt mr-1"></i>No. Dokumen: {{ $history->document_number }}</p>
                @endif
                @if($history->changedByUser)
                    <p class="text-xs text-gray-400 mt-1">Oleh: {{ $history->changedByUser->name }}</p>
                @endif
            </div>
        </div>
        @empty
        <div class="text-center py-8 text-gray-400">
            <i class="fas fa-inbox text-4xl mb-3"></i>
            <p>Belum ada riwayat perubahan status.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
