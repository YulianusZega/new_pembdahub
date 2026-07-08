@extends('layouts.admin')
@section('title', 'Transisi Status - ' . $student->name)
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white">
                <i class="fas fa-exchange-alt text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Transisi Status Siswa</h1>
                <p class="text-gray-600 mt-1">{{ $student->name }} — Status saat ini: <strong>{{ ucfirst($student->status) }}</strong></p>
            </div>
        </div>
    </div>

    @if(session('error'))
    <div class="p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <p class="text-red-700">{{ session('error') }}</p>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-lg p-6">
        <form action="{{ route('admin.students.lifecycle.transition.store', $student) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status Baru <span class="text-red-500">*</span></label>
                    <select name="new_status" required class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">-- Pilih Status --</option>
                        @foreach($allowedTransitions as $status)
                        <option value="{{ $status }}" {{ old('new_status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                    @error('new_status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Transisi <span class="text-red-500">*</span></label>
                    <input type="date" name="transition_date" value="{{ old('transition_date', date('Y-m-d')) }}" required class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('transition_date')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Alasan / Keterangan <span class="text-red-500">*</span></label>
                    <textarea name="reason" rows="3" required class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">{{ old('reason') }}</textarea>
                    @error('reason')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Referensi Dokumen</label>
                    <input type="text" name="document_reference" value="{{ old('document_reference') }}" placeholder="No. SK / Surat Keterangan" class="w-full rounded-xl border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <a href="{{ route('admin.students.lifecycle.history', $student) }}" class="px-5 py-2 bg-white border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50">Batal</a>
                <button type="submit" class="px-5 py-2 bg-gradient-to-r from-indigo-500 to-purple-600 text-white rounded-xl hover:shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Simpan Transisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
