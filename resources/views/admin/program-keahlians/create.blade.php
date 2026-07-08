@extends('layouts.admin')

@section('title', 'Tambah Program Keahlian SMK')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Program Keahlian SMK</h1>
                <p class="text-gray-600 mt-1">Isi data program keahlian untuk SMK</p>
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

    <form action="{{ route('admin.program-keahlians.store') }}" method="POST">
        @csrf

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Program Keahlian</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Sekolah SMK</label>
                    <select name="school_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">-- Pilih Sekolah SMK --</option>
                        @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ old('school_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                    @error('school_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-font mr-1"></i> Kode Program Keahlian</label>
                    <input type="text" name="kode" value="{{ old('kode') }}" 
                        placeholder="Contoh: TIK, AKL, OTKP"
                        maxlength="10"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" />
                    @error('kode')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Nama Program Keahlian</label>
                    <input type="text" name="nama" value="{{ old('nama') }}" 
                        placeholder="Contoh: Teknologi Informasi dan Komunikasi"
                        maxlength="100"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent" />
                    @error('nama')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-gray-500">Contoh lain: Bisnis dan Manajemen, Akuntansi dan Keuangan</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-edit mr-1"></i> Deskripsi</label>
                    <textarea name="deskripsi" rows="3"
                        placeholder="Deskripsi program keahlian (opsional)"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', 1) ? 'checked' : '' }}
                            class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500">
                        <span class="text-sm font-medium text-gray-700"><i class="fas fa-check-circle text-green-500 mr-1"></i> Program Keahlian aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Info Box -->
        <div class="bg-blue-50 border-l-4 border-blue-500 rounded-xl p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                <div class="text-sm text-blue-700">
                    <p class="font-semibold mb-1"><i class="fas fa-lightbulb text-yellow-500 mr-1"></i> Informasi</p>
                    <p>Setelah menambah Program Keahlian, Anda dapat menambahkan <strong>Konsentrasi Keahlian</strong> di halaman detail atau dari tabel di halaman Jurusan.</p>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-700 text-white rounded-xl font-medium hover:from-orange-700 hover:to-amber-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Program Keahlian
            </button>
            <a href="{{ route('admin.majors.index') }}" 
                class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
