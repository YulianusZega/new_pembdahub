@extends('layouts.admin')

@section('title', $module->exists ? 'Edit Materi' : 'Tambah Materi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.training-modules.index') }}" class="p-2 bg-gray-100 hover:bg-gray-200 rounded-xl transition">
                <i class="fas fa-arrow-left text-gray-600"></i>
            </a>
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-cyan-600 shadow-lg">
                    <i class="fas fa-{{ $module->exists ? 'pen' : 'plus' }} text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $module->exists ? 'Edit Materi' : 'Tambah Materi' }}</h1>
                    <p class="text-gray-600 mt-1">{{ $module->exists ? 'Perbarui informasi materi pelatihan' : 'Buat materi pelatihan baru' }}</p>
                </div>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
        <ul class="text-red-700 text-sm list-disc list-inside">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ $module->exists ? route('admin.training-modules.update', $module) : route('admin.training-modules.store') }}"
          method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if($module->exists) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-alt text-sky-500"></i> Konten Materi
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Judul Materi <span class="text-red-500">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $module->title) }}" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Masukkan judul materi...">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi Singkat</label>
                        <input type="text" name="description" value="{{ old('description', $module->description) }}" maxlength="1000"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Ringkasan singkat materi pelatihan...">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Konten Lengkap</label>
                        <textarea name="content" rows="12"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Isi lengkap materi pelatihan...">{{ old('content', $module->content) }}</textarea>
                    </div>
                </div>

                <!-- Thumbnail Upload -->
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-image text-sky-500"></i> Cover / Thumbnail
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload Gambar Cover</label>
                        @if($module->thumbnail_image)
                        <div class="mb-3">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <img src="{{ $module->thumbnail_url }}" alt="Thumbnail" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-800">Thumbnail saat ini</p>
                                    <p class="text-xs text-gray-500">Upload baru untuk mengganti</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        <input type="file" name="thumbnail_image" accept="image/*"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent file:mr-3 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        <p class="text-xs text-gray-500 mt-1">Format gambar (JPG, PNG, WEBP). Maksimal 2MB.</p>
                    </div>
                </div>

                <!-- PDF Upload -->
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-file-pdf text-sky-500"></i> File PDF
                    </h3>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload PDF</label>
                        @if($module->pdf_file)
                        <div class="mb-3">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <i class="fas fa-file-pdf text-red-500 text-2xl"></i>
                                <div class="flex-1">
                                    <a href="{{ route('admin.training-modules.download', $module) }}" class="text-sm font-medium text-sky-600 hover:text-sky-700">
                                        {{ basename($module->pdf_file) }}
                                    </a>
                                    <p class="text-xs text-gray-500">File saat ini (upload baru untuk mengganti)</p>
                                </div>
                            </div>
                        </div>
                        @endif
                        <input type="file" name="pdf_file" accept=".pdf"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent file:mr-3 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                        <p class="text-xs text-gray-500 mt-1">Format PDF. Maksimal 10MB.</p>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-cog text-sky-500"></i> Pengaturan
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="category" required
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="panduan_umum" {{ old('category', $module->category) == 'panduan_umum' ? 'selected' : '' }}>Panduan Umum</option>
                            <option value="fitur_admin" {{ old('category', $module->category) == 'fitur_admin' ? 'selected' : '' }}>Fitur Admin</option>
                            <option value="fitur_guru" {{ old('category', $module->category) == 'fitur_guru' ? 'selected' : '' }}>Fitur Guru</option>
                            <option value="fitur_siswa" {{ old('category', $module->category) == 'fitur_siswa' ? 'selected' : '' }}>Fitur Siswa</option>
                            <option value="fitur_orangtua" {{ old('category', $module->category) == 'fitur_orangtua' ? 'selected' : '' }}>Fitur Orang Tua</option>
                            <option value="fitur_keuangan" {{ old('category', $module->category) == 'fitur_keuangan' ? 'selected' : '' }}>Fitur Keuangan</option>
                            <option value="fitur_yayasan" {{ old('category', $module->category) == 'fitur_yayasan' ? 'selected' : '' }}>Fitur Yayasan</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Tingkat Kesulitan</label>
                        <select name="difficulty" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent">
                            <option value="Pemula" {{ old('difficulty', $module->difficulty ?? 'Pemula') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                            <option value="Menengah" {{ old('difficulty', $module->difficulty ?? 'Pemula') == 'Menengah' ? 'selected' : '' }}>Menengah</option>
                            <option value="Mahir" {{ old('difficulty', $module->difficulty ?? 'Pemula') == 'Mahir' ? 'selected' : '' }}>Mahir</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Estimasi Waktu Baca (Menit)</label>
                        <input type="number" name="reading_time" value="{{ old('reading_time', $module->reading_time ?? 15) }}" min="1"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="Contoh: 15">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Urutan Tampil</label>
                        <input type="number" name="sort_order" value="{{ old('sort_order', $module->sort_order ?? 0) }}" min="0"
                            class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-sky-500 focus:border-transparent"
                            placeholder="0">
                    </div>

                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="hidden" name="is_published" value="0">
                            <input type="checkbox" name="is_published" value="1"
                                {{ old('is_published', $module->is_published) ? 'checked' : '' }}
                                class="w-5 h-5 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                            <div>
                                <span class="text-sm font-semibold text-gray-700">Publikasikan</span>
                                <p class="text-xs text-gray-500">Materi akan dapat diakses oleh pengguna</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-lg p-6 space-y-5">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-users text-sky-500"></i> Target Role
                    </h3>

                    <div>
                        <button type="button" id="toggleAllRoles"
                            class="mb-3 px-4 py-1.5 bg-sky-50 text-sky-700 rounded-lg text-xs font-semibold hover:bg-sky-100 transition">
                            <i class="fas fa-check-double mr-1"></i> Pilih Semua
                        </button>

                        @php
                            $roles = [
                                'superadmin' => 'Super Admin',
                                'admin_sekolah' => 'Admin Sekolah',
                                'guru' => 'Guru',
                                'siswa' => 'Siswa',
                                'orang_tua' => 'Orang Tua',
                                'bendahara' => 'Bendahara',
                                'ketua_yayasan' => 'Ketua Yayasan',
                            ];
                            $selectedRoles = old('target_roles', $module->target_roles ?? []);
                        @endphp

                        <div class="space-y-3">
                            @foreach($roles as $key => $label)
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="checkbox" name="target_roles[]" value="{{ $key }}"
                                    {{ is_array($selectedRoles) && in_array($key, $selectedRoles) ? 'checked' : '' }}
                                    class="role-checkbox w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                                <span class="text-sm text-gray-700">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 px-6 py-3 bg-gradient-to-r from-sky-500 to-cyan-600 text-white rounded-xl font-medium hover:from-sky-600 hover:to-cyan-700 shadow-lg transition-all text-center">
                        <i class="fas fa-save mr-2"></i> {{ $module->exists ? 'Simpan Perubahan' : 'Simpan Materi' }}
                    </button>
                    <a href="{{ route('admin.training-modules.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl font-medium hover:bg-gray-200 transition text-center">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('toggleAllRoles').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.role-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        this.innerHTML = allChecked
            ? '<i class="fas fa-check-double mr-1"></i> Pilih Semua'
            : '<i class="fas fa-times mr-1"></i> Hapus Semua';
    });
</script>
@endpush
@endsection
