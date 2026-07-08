@extends('layouts.admin')

@section('title', 'Edit Jurusan')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-amber-500 to-orange-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Edit Jurusan</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi jurusan</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-2">Terdapat kesalahan pada form:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.majors.update', $major) }}" method="POST">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Jurusan</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Sekolah</label>
                    <select name="school_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">
                        <option value="">-- Pilih Sekolah --</option>
                        @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ old('school_id', $major->school_id) == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-font mr-1"></i> Kode Jurusan</label>
                    <input type="text" name="major_code" value="{{ old('major_code', $major->major_code) }}" 
                        placeholder="Contoh: IPA, IPS, TKJ"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
                    @error('major_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Nama Jurusan</label>
                    <input type="text" name="major_name" value="{{ old('major_name', $major->major_name) }}" 
                        placeholder="Contoh: Ilmu Pengetahuan Alam"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent" />
                    @error('major_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-edit mr-1"></i> Keterangan</label>
                    <textarea name="description" rows="3"
                        placeholder="Deskripsi jurusan (opsional)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-amber-500 focus:border-transparent">{{ old('description', $major->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $major->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 text-amber-600 border-gray-300 rounded focus:ring-amber-500">
                        <span class="text-sm font-medium text-gray-700"><i class="fas fa-check-circle text-green-500 mr-1"></i> Jurusan aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-amber-600 to-orange-700 text-white rounded-xl font-medium hover:from-amber-700 hover:to-orange-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.majors.index') }}" 
                class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection