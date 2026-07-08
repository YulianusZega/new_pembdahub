@extends('layouts.admin')

@section('title', 'Tambah Jabatan Baru')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Jabatan Baru</h1>
                <p class="text-gray-600 mt-1">Isi formulir di bawah untuk menambah jabatan</p>
            </div>
        </div>
        <a href="{{ route('admin.master.positions.index', request()->query()) }}" 
            class="flex items-center gap-2 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-xl p-4 shadow-sm">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-2">Terdapat kesalahan:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.master.positions.store') }}" method="POST" class="space-y-6">
        @csrf

        @foreach(request()->only(['school_id', 'category', 'status', 'search', 'page']) as $key => $value)
            @if(!is_null($value))
                <input type="hidden" name="f_{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <!-- Basic Info -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold">1</span>
                Informasi Dasar
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-edit mr-1"></i> Nama Jabatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="position_name" value="{{ old('position_name') }}" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                        placeholder="Contoh: Kepala Sekolah" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-bookmark mr-1"></i> Kode Jabatan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="position_code" value="{{ old('position_code') }}" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                        placeholder="Contoh: KEPSEK" required>
                    <p class="text-xs text-gray-500 mt-1">Gunakan huruf kapital tanpa spasi</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-folder mr-1"></i> Kategori <span class="text-red-500">*</span>
                    </label>
                    <select name="position_category" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">Pilih Kategori</option>
                        <option value="structural" {{ old('position_category') == 'structural' ? 'selected' : '' }}>Struktural</option>
                        <option value="functional" {{ old('position_category') == 'functional' ? 'selected' : '' }}>Fungsional</option>
                        <option value="staff" {{ old('position_category') == 'staff' ? 'selected' : '' }}>Staff</option>
                        <option value="support" {{ old('position_category') == 'support' ? 'selected' : '' }}>Support</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-file-alt mr-1"></i> Deskripsi
                    </label>
                    <textarea name="description" rows="3" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                        placeholder="Deskripsi tugas dan tanggung jawab...">{{ old('description') }}</textarea>
                </div>
            </div>
        </div>

        <!-- School & Allowance -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold">2</span>
                Sekolah & Tunjangan
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-school mr-1"></i> Sekolah
                    </label>
                    <select name="school_id" 
                        class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Global (Semua Sekolah)</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">
                        Kosongkan untuk membuat jabatan global yang berlaku di semua sekolah
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-coins mr-1"></i> Tunjangan per Bulan <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
                        <input type="number" name="allowance_amount" value="{{ old('allowance_amount', 0) }}" 
                            class="w-full pl-12 pr-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" 
                            placeholder="0" min="0" required>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                <span class="flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-100 text-indigo-700 font-bold">3</span>
                Status
            </h2>
            
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" 
                    {{ old('is_active', 1) ? 'checked' : '' }}
                    class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                <label for="is_active" class="text-gray-700 font-medium cursor-pointer">
                    Aktifkan jabatan ini
                </label>
            </div>
            <p class="text-sm text-gray-500 mt-2 ml-8">
                Jabatan yang tidak aktif tidak akan muncul di pilihan penugasan
            </p>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-semibold hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <i class="fas fa-save mr-1"></i> Simpan Jabatan
            </button>
            <a href="{{ route('admin.master.positions.index', request()->query()) }}" 
                class="px-6 py-3 bg-gray-200 text-gray-700 rounded-xl font-semibold hover:bg-gray-300 transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
