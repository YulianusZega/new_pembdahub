@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-2">
            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Siswa Baru</h1>
                <p class="text-gray-600 mt-1">Lengkapi informasi siswa di bawah ini</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm animate-fade-in">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-1">Terdapat beberapa kesalahan:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.students.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <!-- Data Pribadi -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Data Pribadi</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-school mr-1"></i> Sekolah <span class="text-red-500">*</span>
                    </label>
                    @if(auth()->user()->isSuperAdmin())
                        {{-- SuperAdmin: Bisa pilih sekolah --}}
                        <select name="school_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" required>
                            @foreach($schools as $sch)
                            <option value="{{ $sch->id }}" {{ old('school_id') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                            @endforeach
                        </select>
                    @else
                        {{-- Admin Sekolah: Auto school_id, tidak bisa pilih --}}
                        <input type="hidden" name="school_id" value="{{ auth()->user()->school_id }}">
                        <div class="w-full px-4 py-3 border-2 border-indigo-200 rounded-xl bg-indigo-50 text-gray-800 font-semibold">
                            {{ auth()->user()->school->name }}
                        </div>
                        <p class="text-sm text-gray-500 mt-2">
                            <i class="fas fa-info-circle"></i> Siswa akan otomatis terdaftar di sekolah Anda
                        </p>
                    @endif
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-list-ol mr-1"></i> NISN <span class="text-red-500">*</span></label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Contoh: 0012345678" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-id-badge mr-1"></i> NIS</label>
                    <input type="text" name="nis" value="{{ old('nis') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Nomor Induk Siswa (opsional)">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user mr-1"></i> Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Nama lengkap siswa" required>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-venus-mars mr-1"></i> Jenis Kelamin</label>
                    <select name="gender" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tahun Masuk</label>
                    <input type="number" name="entry_year" value="{{ old('entry_year', date('Y')) }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-map-marker-alt mr-1"></i> Tempat Lahir</label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Kota kelahiran">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-birthday-cake mr-1"></i> Tanggal Lahir</label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-pray mr-1"></i> Agama</label>
                    <select name="religion" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="" disabled {{ old('religion') ? '' : 'selected' }}>-- Pilih Agama --</option>
                        <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen Protestan" {{ old('religion') == 'Kristen Protestan' ? 'selected' : '' }}>Kristen Protestan</option>
                        <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Konghucu" {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                        <option value="Lainnya" {{ old('religion') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-phone mr-1"></i> Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Nomor telepon">
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-map-marker-alt mr-1"></i> Alamat</label>
                    <textarea name="address" rows="3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Alamat lengkap">{{ old('address') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Data Wali -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-green-500 to-green-600 text-white font-bold text-sm">
                    2
                </div>
                <h2 class="text-xl font-bold text-gray-900">Data Wali & Pendidikan Sebelumnya</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Asal Sekolah Sebelumnya</label>
                    <input type="text" name="previous_school" value="{{ old('previous_school') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Nama sekolah sebelumnya">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-users mr-1"></i> Nama Wali</label>
                    <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Nama orang tua/wali">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-phone mr-1"></i> Telepon Wali</label>
                    <input type="text" name="guardian_phone" value="{{ old('guardian_phone') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Nomor telepon wali">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-briefcase mr-1"></i> Pekerjaan Wali</label>
                    <select name="guardian_occupation" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                        <option value="" disabled {{ old('guardian_occupation') ? '' : 'selected' }}>-- Pilih Pekerjaan --</option>
                        <option value="PNS" {{ old('guardian_occupation') == 'PNS' ? 'selected' : '' }}>PNS</option>
                        <option value="TNI/Polri" {{ old('guardian_occupation') == 'TNI/Polri' ? 'selected' : '' }}>TNI/Polri</option>
                        <option value="Guru/Dosen" {{ old('guardian_occupation') == 'Guru/Dosen' ? 'selected' : '' }}>Guru/Dosen</option>
                        <option value="Dokter/Tenaga Medis" {{ old('guardian_occupation') == 'Dokter/Tenaga Medis' ? 'selected' : '' }}>Dokter/Tenaga Medis</option>
                        <option value="Pegawai Swasta" {{ old('guardian_occupation') == 'Pegawai Swasta' ? 'selected' : '' }}>Pegawai Swasta</option>
                        <option value="Wiraswasta" {{ old('guardian_occupation') == 'Wiraswasta' ? 'selected' : '' }}>Wiraswasta</option>
                        <option value="Pedagang" {{ old('guardian_occupation') == 'Pedagang' ? 'selected' : '' }}>Pedagang</option>
                        <option value="Petani" {{ old('guardian_occupation') == 'Petani' ? 'selected' : '' }}>Petani</option>
                        <option value="Nelayan" {{ old('guardian_occupation') == 'Nelayan' ? 'selected' : '' }}>Nelayan</option>
                        <option value="Buruh" {{ old('guardian_occupation') == 'Buruh' ? 'selected' : '' }}>Buruh</option>
                        <option value="Pensiunan" {{ old('guardian_occupation') == 'Pensiunan' ? 'selected' : '' }}>Pensiunan</option>
                        <option value="Ibu Rumah Tangga" {{ old('guardian_occupation') == 'Ibu Rumah Tangga' ? 'selected' : '' }}>Ibu Rumah Tangga</option>
                        <option value="Tidak Bekerja" {{ old('guardian_occupation') == 'Tidak Bekerja' ? 'selected' : '' }}>Tidak Bekerja</option>
                        <option value="Lainnya" {{ old('guardian_occupation') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-map-marker-alt mr-1"></i> Alamat Wali</label>
                    <textarea name="guardian_address" rows="3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Alamat wali">{{ old('guardian_address') }}</textarea>
                </div>
            </div>
        </div>

        <!-- Data Tambahan -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-purple-600 text-white font-bold text-sm">
                    3
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Tambahan</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-id-card mr-1"></i> RFID UID Card</label>
                    <input type="text" name="rfid_uid" value="{{ old('rfid_uid') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Contoh: 04 XX 19 XX">
                    <p class="text-xs text-gray-500 mt-1">Kosongkan jika siswa belum memiliki kartu ID. Jika punya, tap kartu di alat pembaca ke form ini atau masukkan kode UID-nya.</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-palette mr-1"></i> Hobi</label>
                    <input type="text" name="hobby" value="{{ old('hobby') }}" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Hobi siswa">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-hospital mr-1"></i> Riwayat Kesehatan</label>
                    <textarea name="health_history" rows="3" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all" 
                        placeholder="Riwayat kesehatan (alergi, penyakit, dll)">{{ old('health_history') }}</textarea>
                </div>
                
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-camera mr-1"></i> Foto Siswa</label>
                    <div class="mt-2 mb-3">
                        <img id="photo-preview" src="data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><rect fill='%23eeeeee' width='100%25' height='100%25'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23999' font-family='Arial' font-size='16'>No%20Photo</text></svg>" 
                            class="w-32 h-32 object-cover rounded-xl border-2 border-gray-300" alt="Preview">
                    </div>
                    <input type="file" name="photo" id="photo-input" accept="image/*" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 transition-all file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG atau PNG. Maksimal 2MB</p>
                    <div id="photo-error" class="text-red-600 text-sm mt-1"></div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-4 justify-end bg-white rounded-2xl shadow-lg p-6">
            <a href="{{ route('admin.students.index') }}" 
                class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-medium hover:bg-gray-50 transition-all">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batal
                </span>
            </a>
            <button type="submit" 
                class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl font-medium hover:from-indigo-700 hover:to-purple-700 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Siswa
                </span>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var placeholder = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='200' height='200'><rect fill='%23eeeeee' width='100%25' height='100%25'/><text x='50%25' y='50%25' dominant-baseline='middle' text-anchor='middle' fill='%23999' font-family='Arial' font-size='16'>No%20Photo</text></svg>";

        function showError(msg) {
            var el = document.getElementById('photo-error');
            if (el) el.textContent = msg;
        }

        function clearError() {
            var el = document.getElementById('photo-error');
            if (el) el.textContent = '';
        }

        function validateFile(file) {
            if (!file) return true;
            var allowed = ['image/jpeg', 'image/png', 'image/jpg'];
            if (allowed.indexOf(file.type) === -1) return 'Tipe file harus JPG atau PNG.';
            var max = 2048 * 1024; // 2MB
            if (file.size > max) return 'Ukuran file maksimal 2MB.';
            return true;
        }

        function setupPreview(inputId, previewId) {
            var input = document.getElementById(inputId);
            var preview = document.getElementById(previewId);
            if (!input || !preview) return;
            input.addEventListener('change', function() {
                clearError();
                var file = this.files && this.files[0];
                var ok = validateFile(file);
                if (ok !== true) {
                    showError(ok);
                    this.value = '';
                    preview.src = placeholder;
                    return;
                }
                if (file) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.src = placeholder;
                }
            });
        }
        setupPreview('photo-input', 'photo-preview');
    });
</script>

@endsection