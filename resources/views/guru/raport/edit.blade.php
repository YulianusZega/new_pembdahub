@extends('layouts.guru')
@section('title', 'Edit Catatan Rapor - ' . $reportCard->student->full_name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-pencil-alt text-rose-500"></i> Edit Catatan Rapor
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $reportCard->student->full_name }} · {{ $reportCard->semester->semester_name ?? '' }}
            </p>
        </div>
        <a href="{{ route('guru.raport.show', $reportCard) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Student Info --}}
        <div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3 border-gray-100">
                    <i class="fas fa-user text-rose-500"></i> Informasi Siswa
                </h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium text-gray-800">{{ $reportCard->student->full_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">NISN</dt>
                        <dd class="font-mono text-gray-800">{{ $reportCard->student->nisn ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kelas</dt>
                        <dd class="font-medium text-gray-800">{{ $reportCard->classroom->class_name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Semester</dt>
                        <dd class="text-gray-800">{{ $reportCard->semester->semester_name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Rata-rata</dt>
                        <dd class="font-bold {{ $reportCard->average_score >= 80 ? 'text-green-600' : ($reportCard->average_score >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ number_format($reportCard->average_score, 1) }}
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Status</dt>
                        <dd>
                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-700">
                                {{ ucfirst($reportCard->status) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>

            <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 mt-4">
                <p class="text-sm font-semibold text-amber-700 mb-2">
                    <i class="fas fa-info-circle mr-1"></i> Catatan Penting
                </p>
                <ul class="text-xs text-amber-600 list-disc list-inside space-y-1">
                    <li>Hanya catatan wali kelas yang dapat diedit</li>
                    <li>Nilai tidak dapat diubah di halaman ini</li>
                    <li>Simpan sebelum melakukan finalize</li>
                </ul>
            </div>
        </div>

        {{-- Right: Edit Form --}}
        <div class="lg:col-span-2">
            <form method="POST" action="{{ route('guru.raport.update', $reportCard) }}">
                @csrf
                @method('PUT')

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 space-y-5">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2 border-b pb-3 border-gray-100">
                        <i class="fas fa-edit text-rose-500"></i> Catatan Wali Kelas
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Catatan / Deskripsi untuk Siswa
                        </label>
                        <textarea name="teacher_notes" rows="6"
                                  class="w-full border-2 border-gray-200 rounded-xl p-4 text-sm focus:ring-2 focus:ring-rose-300 focus:border-rose-400 transition resize-none"
                                  placeholder="Tulis catatan wali kelas tentang siswa ini, misalnya: prestasi, sikap, saran, dll.">{{ old('teacher_notes', $reportCard->teacher_notes) }}</textarea>
                        @error('teacher_notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-gray-400 mt-1">Maksimal 1000 karakter</p>
                    </div>

                    {{-- Read-only notes --}}
                    @if($reportCard->principal_notes)
                        <div>
                            <label class="block text-sm font-semibold text-gray-500 mb-2">
                                <i class="fas fa-lock mr-1 text-gray-400"></i> Catatan Kepala Sekolah (Read-only)
                            </label>
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-600">
                                {{ $reportCard->principal_notes }}
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-3 pt-3 border-t border-gray-100">
                        <a href="{{ route('guru.raport.show', $reportCard) }}" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-gradient-to-r from-rose-500 to-pink-600 hover:from-rose-600 hover:to-pink-700 text-white rounded-xl text-sm font-semibold shadow-md transition">
                            <i class="fas fa-save mr-1"></i> Simpan Catatan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
