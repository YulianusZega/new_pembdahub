@extends('layouts.admin')
@section('title', 'Riwayat Status - ' . $student->name)
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                    <i class="fas fa-history text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Riwayat Status Siswa</h1>
                    <p class="text-gray-600 mt-1">{{ $student->name }} — {{ $student->nis }}</p>
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.students.lifecycle.transition', $student) }}" class="inline-flex items-center px-5 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-exchange-alt mr-2"></i> Transisi Status
                </a>
                <a href="{{ route('admin.students.show', $student) }}" class="inline-flex items-center px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Current Status -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Saat Ini</h2>
        <div class="flex items-center gap-4">
            <span class="px-4 py-2 rounded-xl text-sm font-semibold @if($student->status === 'aktif') bg-green-100 text-green-800 @elseif($student->status === 'lulus') bg-blue-100 text-blue-800 @elseif($student->status === 'keluar') bg-red-100 text-red-800 @elseif($student->status === 'cuti') bg-yellow-100 text-yellow-800 @else bg-gray-100 text-gray-800 @endif">
                {{ ucfirst($student->status) }}
            </span>
            <span class="text-gray-500">Kelas: {{ $student->classroom?->class_name ?? '-' }}</span>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-6">Timeline Perubahan Status</h2>
        @if($histories->count())
        <div class="relative">
            <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
            @foreach($histories as $history)
            <div class="relative flex items-start gap-6 mb-8">
                <div class="relative z-10 w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 @if($history->new_status === 'aktif') bg-green-500 @elseif($history->new_status === 'lulus') bg-blue-500 @elseif($history->new_status === 'keluar') bg-red-500 @else bg-gray-400 @endif">
                    <i class="fas fa-circle text-white text-xs"></i>
                </div>
                <div class="flex-1 bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 text-xs rounded-lg bg-gray-200 text-gray-700">{{ ucfirst($history->previous_status ?? 'baru') }}</span>
                            <i class="fas fa-arrow-right text-gray-400 text-xs"></i>
                            <span class="px-2 py-1 text-xs rounded-lg font-semibold @if($history->new_status === 'aktif') bg-green-100 text-green-800 @elseif($history->new_status === 'lulus') bg-blue-100 text-blue-800 @elseif($history->new_status === 'keluar') bg-red-100 text-red-800 @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($history->new_status) }}
                            </span>
                        </div>
                        <span class="text-sm text-gray-500">{{ $history->transition_date->format('d M Y') }}</span>
                    </div>
                    @if($history->reason)
                    <p class="text-sm text-gray-600">{{ $history->reason }}</p>
                    @endif
                    @if($history->document_reference)
                    <p class="text-xs text-gray-400 mt-1">Ref: {{ $history->document_reference }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center text-gray-500 py-8">Belum ada riwayat perubahan status.</p>
        @endif
    </div>
</div>
@endsection
