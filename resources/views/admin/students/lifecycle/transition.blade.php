@extends('layouts.admin')

@section('title', 'Transisi Status - ' . $student->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                <i class="fas fa-exchange-alt text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Ubah Status Siswa</h1>
                <p class="text-gray-500 mt-1">{{ $student->full_name }} ({{ $student->nis }})</p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('admin.students.lifecycle.history', $student) }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Riwayat Status
            </a>
        </div>
    </div>

    <!-- Current Status Info -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-3">Status Saat Ini</h2>
        <span class="px-4 py-2 rounded-full text-sm font-semibold bg-indigo-100 text-indigo-800">{{ ucfirst($student->status) }}</span>
    </div>

    <!-- Transition Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Form Transisi Status</h2>

        @if(count($allowedTransitions) === 0)
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-ban text-4xl mb-3"></i>
                <p>Tidak ada transisi status yang tersedia dari status saat ini.</p>
            </div>
        @else
            <form action="{{ route('admin.students.lifecycle.transition.store', $student) }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="to_status" class="block text-sm font-medium text-gray-700 mb-2">Status Baru <span class="text-red-500">*</span></label>
                    <select name="to_status" id="to_status" required
                            class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        <option value="">-- Pilih Status --</option>
                        @foreach($allowedTransitions as $status)
                            <option value="{{ $status }}" {{ old('to_status') === $status ? 'selected' : '' }}>
                                {{ $statuses[$status] ?? ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                    @error('to_status')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-2">Alasan</label>
                    <input type="text" name="reason" id="reason" value="{{ old('reason') }}" maxlength="255"
                           class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                           placeholder="Alasan perubahan status...">
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">Catatan</label>
                    <textarea name="notes" id="notes" rows="3"
                              class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                              placeholder="Catatan tambahan...">{{ old('notes') }}</textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="document_number" class="block text-sm font-medium text-gray-700 mb-2">No. Dokumen/SK</label>
                        <input type="text" name="document_number" id="document_number" value="{{ old('document_number') }}" maxlength="100"
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                               placeholder="Nomor dokumen...">
                    </div>
                    <div>
                        <label for="effective_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Efektif</label>
                        <input type="date" name="effective_date" id="effective_date" value="{{ old('effective_date', date('Y-m-d')) }}"
                               class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4">
                    <a href="{{ route('admin.students.lifecycle.history', $student) }}"
                       class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition font-medium">
                        Batal
                    </a>
                    <button type="submit"
                            class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-sm">
                        <i class="fas fa-save mr-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
