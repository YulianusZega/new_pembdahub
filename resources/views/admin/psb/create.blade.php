@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/3 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-edit"></i> Pendaftaran Siswa Baru
                </h1>
                <p class="text-white/70 text-sm mt-1">Form pendaftaran calon siswa baru</p>
            </div>
            <a href="{{ route('admin.psb.applicants.index') }}" class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl font-semibold transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg">
            <p class="font-semibold mb-2">Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- VISUAL TAHAPAN PENDAFTARAN --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h3 class="text-sm font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-route text-teal-600"></i> Tahapan Pendaftaran PSB
        </h3>

        <div class="relative">
            {{-- Progress Bar Background --}}
            <div class="absolute top-5 left-0 w-full h-1 bg-gray-200 rounded-full"></div>
            
            {{-- Active Progress (Draft = 0%) --}}
            <div class="absolute top-5 left-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full transition-all duration-500" style="width: 0%"></div>

            {{-- Steps --}}
            <div class="relative flex justify-between">
                @php
                    $steps = [
                        ['icon' => '<i class="fas fa-edit mr-1"></i>', 'label' => 'Isi Form', 'desc' => 'Lengkapi Data', 'status' => 'current'],
                        ['icon' => '<i class="fas fa-envelope mr-1"></i>', 'label' => 'Submit', 'desc' => 'Kirim Pendaftaran', 'status' => 'pending'],
                        ['icon' => '<i class="fas fa-credit-card mr-1"></i>', 'label' => 'Bayar', 'desc' => 'Bayar Pendaftaran', 'status' => 'pending'],
                        ['icon' => '<i class="fas fa-file-alt mr-1"></i>', 'label' => 'Dokumen', 'desc' => 'Upload Dokumen', 'status' => 'pending'],
                        ['icon' => '<i class="fas fa-edit mr-1"></i>', 'label' => 'Tes', 'desc' => 'Ikuti Tes Masuk', 'status' => 'pending'],
                        ['icon' => '<i class="fas fa-chart-bar mr-1"></i>', 'label' => 'Penilaian', 'desc' => 'Proses Nilai', 'status' => 'pending'],
                        ['icon' => '<i class="fas fa-party-horn mr-1"></i>', 'label' => 'Hasil', 'desc' => 'Pengumuman', 'status' => 'pending'],
                        ['icon' => '<i class="fas fa-check-circle text-green-500 mr-1"></i>', 'label' => 'Daftar Ulang', 'desc' => 'Bayar Pangkal', 'status' => 'pending'],
                        ['icon' => '<i class="fas fa-user-graduate mr-1"></i>', 'label' => 'Aktif', 'desc' => 'Jadi Siswa', 'status' => 'pending'],
                    ];
                @endphp

                @foreach($steps as $index => $step)
                    <div class="flex flex-col items-center relative group z-10">
                        {{-- Step Circle --}}
                        <div class="relative">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-lg transition-all duration-300 {{ $step['status'] === 'current' ? 'bg-gradient-to-br from-emerald-500 to-teal-500 text-white shadow-lg scale-110 ring-4 ring-emerald-200 animate-pulse' : 'bg-gray-100 text-gray-400' }}">
                                {!! $step['icon'] !!}
                            </div>

                            {{-- Current Indicator --}}
                            @if($step['status'] === 'current')
                                <div class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full animate-ping"></div>
                                <div class="absolute -top-1 -right-1 w-3 h-3 bg-emerald-500 rounded-full"></div>
                            @endif
                        </div>

                        {{-- Label --}}
                        <div class="mt-3 text-center">
                            <p class="text-xs font-semibold {{ $step['status'] === 'current' ? 'text-emerald-600' : 'text-gray-400' }}">
                                {{ $step['label'] }}
                            </p>
                            <p class="text-[10px] text-gray-400 mt-0.5 hidden sm:block">
                                {{ $step['desc'] }}
                            </p>
                        </div>

                        {{-- Tooltip on Hover --}}
                        <div class="absolute -bottom-20 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white text-xs px-3 py-2 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-50">
                            <div class="font-semibold">{{ $step['label'] }}</div>
                            <div class="text-gray-300">{{ $step['desc'] }}</div>
                            <div class="absolute -top-1 left-1/2 transform -translate-x-1/2 w-2 h-2 bg-gray-800 rotate-45"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Info Box --}}
        <div class="mt-8 pt-6 border-t border-gray-100">
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-semibold text-emerald-800">Anda sedang di tahap: <strong>Pengisian Form Pendaftaran</strong></p>
                        <p class="text-xs text-emerald-700 mt-1">
                            Lengkapi semua data dengan benar. Setelah submit, Anda akan mendapat nomor registrasi untuk tracking status pendaftaran.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.psb.applicants.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Hidden Fields --}}
        <input type="hidden" name="academic_year_id" value="{{ $academicYear->id }}">

        {{-- Data Pribadi --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-emerald-100 text-emerald-600 rounded-lg flex items-center justify-center mr-3 text-sm"><i class="fas fa-user mr-1"></i></span>
                Data Pribadi Calon Siswa
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Sekolah Tujuan <span class="text-red-500">*</span>
                    </label>
                    <select name="school_id" id="school_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Pilih Sekolah</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('school_id')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Program & Konsentrasi Keahlian (HANYA SMK) --}}
                <div class="md:col-span-2" id="major-selection-section" style="display:none;">
                    <div class="bg-gradient-to-r from-indigo-50 to-purple-50 rounded-lg p-4 border-2 border-indigo-200">
                        <h4 class="text-md font-bold text-indigo-700 mb-3 flex items-center">
                            <span class="text-xl mr-2"><i class="fas fa-graduation-cap mr-1"></i></span>
                            Pilihan Program & Konsentrasi Keahlian
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Program Keahlian Pilihan 1 <span class="text-red-500">*</span>
                                </label>
                                <select name="program_keahlian_1" id="program_keahlian_1" class="w-full px-4 py-2.5 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <option value="">Pilih Program Keahlian</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Konsentrasi Keahlian Pilihan 1 <span class="text-red-500">*</span>
                                </label>
                                <select name="konsentrasi_keahlian_1" id="konsentrasi_keahlian_1" class="w-full px-4 py-2.5 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <option value="">Pilih Konsentrasi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Program Keahlian Pilihan 2 (Opsional)
                                </label>
                                <select name="program_keahlian_2" id="program_keahlian_2" class="w-full px-4 py-2.5 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <option value="">Pilih Program Keahlian</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Konsentrasi Keahlian Pilihan 2 (Opsional)
                                </label>
                                <select name="konsentrasi_keahlian_2" id="konsentrasi_keahlian_2" class="w-full px-4 py-2.5 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                    <option value="">Pilih Konsentrasi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jalur Pendaftaran <span class="text-red-500">*</span>
                    </label>
                    <select id="admission_path" name="admission_path" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Pilih Jalur</option>
                        <option value="reguler" {{ old('admission_path') == 'reguler' ? 'selected' : '' }}>Reguler</option>
                        <option value="prestasi" {{ old('admission_path') == 'prestasi' ? 'selected' : '' }}>Prestasi</option>
                    </select>
                    @error('admission_path')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        NISN <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="nisn" value="{{ old('nisn') }}" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="10 digit NISN">
                    @error('nisn')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Nama lengkap sesuai akta">
                    @error('full_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Kelamin <span class="text-red-500">*</span>
                    </label>
                    <select name="gender" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Pilih</option>
                        <option value="L" {{ old('gender') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('gender') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('gender')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tempat Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="birth_place" value="{{ old('birth_place') }}" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Kota/Kabupaten">
                    @error('birth_place')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Lahir <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="birth_date" value="{{ old('birth_date') }}" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    @error('birth_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Agama <span class="text-red-500">*</span>
                    </label>
                    <select name="religion" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Pilih Agama</option>
                        <option value="Islam" {{ old('religion') == 'Islam' ? 'selected' : '' }}>Islam</option>
                        <option value="Kristen" {{ old('religion') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                        <option value="Katolik" {{ old('religion') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                        <option value="Hindu" {{ old('religion') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                        <option value="Buddha" {{ old('religion') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                        <option value="Konghucu" {{ old('religion') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
                    </select>
                    @error('religion')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Lengkap <span class="text-red-500">*</span>
                    </label>
                    <textarea name="address" rows="3" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Alamat rumah lengkap">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        No. Telepon/HP
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="08xx-xxxx-xxxx">
                    @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="email@example.com">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Siswa
                    </label>
                    <div class="flex items-start gap-4">
                        <div id="preview-container" class="w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center bg-gray-50 overflow-hidden">
                            <img id="photo-preview" class="hidden w-full h-full object-cover" />
                            <span id="preview-placeholder" class="text-gray-400 text-center text-sm"><i class="fas fa-camera mr-1"></i><br>Preview Foto</span>
                        </div>
                        <div class="flex-1">
                            <input type="file" name="photo" id="photo-input" accept="image/*" capture="environment" 
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-mobile-alt mr-1"></i> Bisa foto langsung dari kamera HP atau upload file.<br>
                                Format: JPG, PNG. Max: 2MB
                            </p>
                        </div>
                    </div>
                    @error('photo')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Data Orang Tua --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mr-3 text-sm"><i class="fas fa-users mr-1"></i></span>
                Data Orang Tua / Wali
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Ayah <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="father_name" value="{{ old('father_name') }}" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Nama lengkap ayah">
                    @error('father_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        No. Telepon Ayah
                    </label>
                    <input type="text" name="father_phone" value="{{ old('father_phone') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="08xx-xxxx-xxxx">
                    @error('father_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pekerjaan Ayah
                    </label>
                    <input type="text" name="father_occupation" value="{{ old('father_occupation') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Pekerjaan ayah">
                    @error('father_occupation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Ibu <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="mother_name" value="{{ old('mother_name') }}" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Nama lengkap ibu">
                    @error('mother_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        No. Telepon Ibu
                    </label>
                    <input type="text" name="mother_phone" value="{{ old('mother_phone') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="08xx-xxxx-xxxx">
                    @error('mother_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Pekerjaan Ibu
                    </label>
                    <input type="text" name="mother_occupation" value="{{ old('mother_occupation') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Pekerjaan ibu">
                    @error('mother_occupation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Penghasilan Orang Tua
                    </label>
                    <select name="parent_income" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="">Pilih Range</option>
                        <option value="< 1 juta" {{ old('parent_income') == '< 1 juta' ? 'selected' : '' }}>< Rp 1.000.000</option>
                        <option value="1-3 juta" {{ old('parent_income') == '1-3 juta' ? 'selected' : '' }}>Rp 1.000.000 - 3.000.000</option>
                        <option value="3-5 juta" {{ old('parent_income') == '3-5 juta' ? 'selected' : '' }}>Rp 3.000.000 - 5.000.000</option>
                        <option value="5-10 juta" {{ old('parent_income') == '5-10 juta' ? 'selected' : '' }}>Rp 5.000.000 - 10.000.000</option>
                        <option value="> 10 juta" {{ old('parent_income') == '> 10 juta' ? 'selected' : '' }}>> Rp 10.000.000</option>
                    </select>
                    @error('parent_income')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Data Sekolah Asal --}}
        <div class="bg-white rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                <span class="w-8 h-8 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mr-3 text-sm"><i class="fas fa-school mr-1"></i></span>
                Data Sekolah Asal
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Sekolah Asal <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="previous_school" value="{{ old('previous_school') }}" required 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Nama sekolah asal">
                    @error('previous_school')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Alamat Sekolah Asal
                    </label>
                    <input type="text" name="previous_school_address" value="{{ old('previous_school_address') }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="Alamat sekolah asal">
                    @error('previous_school_address')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun Lulus
                    </label>
                    <input type="number" name="graduation_year" value="{{ old('graduation_year', date('Y')) }}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                        placeholder="2026" min="2020" max="2030">
                    @error('graduation_year')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Data Prestasi (Khusus Jalur Prestasi) --}}
        <div id="prestasi-section" class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-xl shadow-sm p-6 border-2 border-amber-200" style="display: none;">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center">
                    <span class="w-8 h-8 bg-amber-100 text-amber-600 rounded-lg flex items-center justify-center mr-3 text-sm"><i class="fas fa-trophy mr-1"></i></span>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Data Prestasi</h3>
                        <p class="text-xs text-amber-700 mt-1">Khusus untuk jalur prestasi dengan pembebasan biaya pendaftaran</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg p-4 mb-4 border border-amber-200">
                <h4 class="font-semibold text-sm text-amber-800 mb-2"><i class="fas fa-info-circle text-blue-500 mr-1"></i> Ketentuan Pembebasan Biaya Pendaftaran (Rp 50.000):</h4>
                <ul class="text-xs text-gray-700 space-y-1 ml-4">
                    <li><i class="fas fa-check-circle text-green-500 mr-1"></i> <strong>Juara 1, 2, 3</strong> dari <strong>SMPS Pembda 2</strong> yang mendaftar ke <strong>SMA Swasta Pembda 1</strong> atau <strong>SMK Pembda</strong></li>
                    <li><i class="fas fa-check-circle text-green-500 mr-1"></i> <strong>Juara 1</strong> dari <strong>SMP luar</strong> yang mendaftar ke <strong>SMA/SMK Pembda</strong></li>
                    <li><i class="fas fa-check-circle text-green-500 mr-1"></i> <strong>Juara 1</strong> dari <strong>SD</strong> yang mendaftar ke <strong>SMPS Pembda 2</strong></li>
                    <li class="mt-2 text-amber-700"><i class="fas fa-clipboard mr-1"></i> Wajib melampirkan <strong>raport</strong> atau <strong>piagam kejuaraan</strong> sebagai bukti</li>
                </ul>
            </div>

            <div id="achievement-container" class="space-y-4">
                <div class="achievement-item bg-white rounded-lg p-4 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Prestasi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="achievements[0][name]" value="{{ old('achievements.0.name') }}" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="Juara Olimpiade Matematika">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Jenis Prestasi
                            </label>
                            <select name="achievements[0][type]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Pilih Jenis</option>
                                <option value="academic">Akademik</option>
                                <option value="sports">Olahraga</option>
                                <option value="arts">Seni & Budaya</option>
                                <option value="science">Sains & Teknologi</option>
                                <option value="religion">Keagamaan</option>
                                <option value="other">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tingkat <span class="text-red-500">*</span>
                            </label>
                            <select name="achievements[0][level]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Pilih Tingkat</option>
                                <option value="international">Internasional</option>
                                <option value="national">Nasional</option>
                                <option value="provincial">Provinsi</option>
                                <option value="district">Kabupaten/Kota</option>
                                <option value="school">Sekolah</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Peringkat/Juara <span class="text-red-500">*</span>
                            </label>
                            <select name="achievements[0][rank]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                                <option value="">Pilih Peringkat</option>
                                <option value="1">Juara 1</option>
                                <option value="2">Juara 2</option>
                                <option value="3">Juara 3</option>
                                <option value="harapan_1">Harapan 1</option>
                                <option value="harapan_2">Harapan 2</option>
                                <option value="harapan_3">Harapan 3</option>
                                <option value="partisipan">Partisipan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Penyelenggara
                            </label>
                            <input type="text" name="achievements[0][organizer]" value="{{ old('achievements.0.organizer') }}" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                placeholder="Nama penyelenggara">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Tahun <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="achievements[0][year]" value="{{ old('achievements.0.year', date('Y')) }}" 
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                                min="2015" max="2030">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Upload Bukti (Raport/Piagam) <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="achievements[0][certificate]" accept="image/*,application/pdf"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                            <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Max: 15MB</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-4">
                <button type="button" id="add-achievement" class="px-4 py-2 bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition-all text-sm">
                    <i class="fas fa-plus mr-2"></i>Tambah Prestasi
                </button>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center justify-between bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm text-gray-500">
                <span class="text-red-500">*</span> Field wajib diisi
            </p>
            <div class="flex gap-3">
                <a href="{{ route('admin.psb.applicants.index') }}" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-500 text-white rounded-lg font-semibold hover:shadow-lg transition-all hover:scale-105">
                    <i class="fas fa-save mr-2"></i>Simpan Pendaftaran
                </button>
            </div>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // Photo preview handler
    const photoInput = document.getElementById('photo-input');
    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                    document.getElementById('photo-preview').classList.remove('hidden');
                    document.getElementById('preview-placeholder').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Handle admission path change - show/hide prestasi section
    const admissionPathSelect = document.getElementById('admission_path');
    const prestasiSection = document.getElementById('prestasi-section');
    
    if (admissionPathSelect && prestasiSection) {
        admissionPathSelect.addEventListener('change', function() {
            if (this.value === 'prestasi') {
                prestasiSection.style.display = 'block';
            } else {
                prestasiSection.style.display = 'none';
            }
        });
        
        // Trigger on page load if old value is prestasi
        if (admissionPathSelect.value === 'prestasi') {
            prestasiSection.style.display = 'block';
        }
    }


    // Dynamic school selection - HANYA SMK yang punya pilihan Program/Konsentrasi
    const schoolSelect = document.getElementById('school_id');
    if (schoolSelect) {
        schoolSelect.addEventListener('change', function() {
            const schoolId = this.value;
            const majorSection = document.getElementById('major-selection-section');
            
            if (!schoolId) {
                majorSection.style.display = 'none';
                return;
            }
            
            // School ID 3 adalah SMK
            const isSMK = (schoolId == 3);
            
            if (isSMK) {
                majorSection.style.display = 'block';
                loadProgramKeahlian(schoolId);
            } else {
                // SMP/SMA tidak ada pilihan jurusan
                majorSection.style.display = 'none';
            }
        });
    }

    // Load Program Keahlian for SMK
    function loadProgramKeahlian(schoolId) {
        fetch(`/admin/api/schools/${schoolId}/program-keahlians`)
            .then(response => response.json())
            .then(data => {
                const select1 = document.getElementById('program_keahlian_1');
                const select2 = document.getElementById('program_keahlian_2');
                
                if (select1) select1.innerHTML = '<option value="">Pilih Program Keahlian</option>';
                if (select2) select2.innerHTML = '<option value="">Pilih Program Keahlian</option>';
                
                data.forEach(program => {
                    if (select1) {
                        const option1 = new Option(program.nama, program.id);
                        select1.add(option1);
                    }
                    if (select2) {
                        const option2 = new Option(program.nama, program.id);
                        select2.add(option2);
                    }
                });
            })
            .catch(() => {
                showFlashMessage('Gagal memuat data Program Keahlian.', 'error');
            });
    }

    // Load Konsentrasi when Program Keahlian selected
    const programSelect1 = document.getElementById('program_keahlian_1');
    const programSelect2 = document.getElementById('program_keahlian_2');
    
    if (programSelect1) {
        programSelect1.addEventListener('change', function() {
            loadKonsentrasiKeahlian(this.value, 'konsentrasi_keahlian_1');
        });
    }

    if (programSelect2) {
        programSelect2.addEventListener('change', function() {
            loadKonsentrasiKeahlian(this.value, 'konsentrasi_keahlian_2');
        });
    }

    function loadKonsentrasiKeahlian(programId, targetId) {
        if (!programId) {
            document.getElementById(targetId).innerHTML = '<option value="">Pilih Konsentrasi</option>';
            return;
        }
        
        fetch(`/admin/api/program-keahlians/${programId}/konsentrasi-keahlians`)
            .then(response => response.json())
            .then(data => {
                const select = document.getElementById(targetId);
                select.innerHTML = '<option value="">Pilih Konsentrasi</option>';
                
                data.forEach(konsentrasi => {
                    const option = new Option(konsentrasi.nama, konsentrasi.id);
                    select.add(option);
                });
            })
            .catch(() => {
                showFlashMessage('Gagal memuat data Konsentrasi Keahlian.', 'error');
            });
    }

    // Add more achievement forms
    let achievementCount = 1;
    const addAchievementBtn = document.getElementById('add-achievement');
    if (addAchievementBtn) {
        addAchievementBtn.addEventListener('click', function() {
            const container = document.getElementById('achievement-container');
            const newAchievement = `
        <div class="achievement-item bg-white rounded-lg p-4 border border-gray-200 relative">
            <button type="button" class="remove-achievement absolute top-2 right-2 text-red-500 hover:text-red-700">
                <i class="fas fa-times"></i>
            </button>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Nama Prestasi <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="achievements[${achievementCount}][name]" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Juara Olimpiade Matematika">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Jenis Prestasi
                    </label>
                    <select name="achievements[${achievementCount}][type]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Pilih Jenis</option>
                        <option value="academic">Akademik</option>
                        <option value="sports">Olahraga</option>
                        <option value="arts">Seni & Budaya</option>
                        <option value="science">Sains & Teknologi</option>
                        <option value="religion">Keagamaan</option>
                        <option value="other">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tingkat <span class="text-red-500">*</span>
                    </label>
                    <select name="achievements[${achievementCount}][level]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Pilih Tingkat</option>
                        <option value="international">Internasional</option>
                        <option value="national">Nasional</option>
                        <option value="provincial">Provinsi</option>
                        <option value="district">Kabupaten/Kota</option>
                        <option value="school">Sekolah</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Peringkat/Juara <span class="text-red-500">*</span>
                    </label>
                    <select name="achievements[${achievementCount}][rank]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500">
                        <option value="">Pilih Peringkat</option>
                        <option value="1">Juara 1</option>
                        <option value="2">Juara 2</option>
                        <option value="3">Juara 3</option>
                        <option value="harapan_1">Harapan 1</option>
                        <option value="harapan_2">Harapan 2</option>
                        <option value="harapan_3">Harapan 3</option>
                        <option value="partisipan">Partisipan</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Penyelenggara
                    </label>
                    <input type="text" name="achievements[${achievementCount}][organizer]" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        placeholder="Nama penyelenggara">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Tahun <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="achievements[${achievementCount}][year]" value="${new Date().getFullYear()}" 
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500"
                        min="2015" max="2030">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Upload Bukti (Raport/Piagam) <span class="text-red-500">*</span>
                    </label>
                    <input type="file" name="achievements[${achievementCount}][certificate]" accept="image/*,application/pdf"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-amber-50 file:text-amber-700 hover:file:bg-amber-100">
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, PDF. Max: 15MB</p>
                </div>
            </div>
        </div>
    `;
            
            container.insertAdjacentHTML('beforeend', newAchievement);
            achievementCount++;
        });
    }

    // Remove achievement
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-achievement')) {
            const item = e.target.closest('.achievement-item');
            if (document.querySelectorAll('.achievement-item').length > 1) {
                item.remove();
            } else {
                alert('Minimal harus ada 1 prestasi untuk jalur prestasi');
            }
        }
    });

    // Helper to show flash messages dynamically
    function showFlashMessage(message, type = 'error') {
        const colors = {
            success: { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-700', icon: 'fa-check-circle' },
            error: { bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-700', icon: 'fa-exclamation-circle' },
            warning: { bg: 'bg-yellow-50', border: 'border-yellow-200', text: 'text-yellow-700', icon: 'fa-exclamation-triangle' },
        };
        const c = colors[type] || colors.error;
        const el = document.createElement('div');
        el.className = `mb-4 ${c.bg} border ${c.border} ${c.text} px-4 py-3 rounded-xl flex items-center gap-2 transition-opacity duration-500`;
        el.innerHTML = `<i class="fas ${c.icon}"></i><span>${message}</span>`;
        const main = document.querySelector('#main-content') || document.querySelector('main');
        if (main) main.prepend(el);
        setTimeout(() => { el.style.opacity = '0'; setTimeout(() => el.remove(), 500); }, 5000);
    }

}); // End DOMContentLoaded
</script>
@endsection
