@extends('layouts.admin')

@section('title', 'Tambah Orang Tua/Wali')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tambah Orang Tua/Wali</h1>
                <p class="text-gray-600 mt-1">Tambahkan data orang tua/wali siswa</p>
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

    <form action="{{ route('admin.parents.store') }}" method="POST">
        @csrf

        <!-- Section 1: Hubungan dengan Siswa -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-bold text-sm">1</div>
                <h2 class="text-xl font-bold text-gray-900">Hubungan dengan Siswa</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-users mr-1"></i> Siswa</label>
                    <select name="student_id" required class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <option value="">-- Pilih Siswa --</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                            {{ $student->full_name }} ({{ $student->nisn }})
                        </option>
                        @endforeach
                    </select>
                    @error('student_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user mr-1"></i> Hubungan</label>
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-blue-50 hover:border-blue-500 transition-colors">
                            <input type="radio" name="relation_type" value="ayah" {{ old('relation_type') == 'ayah' ? 'checked' : '' }} required
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500">
                            <span class="ml-3 font-medium text-gray-900"><i class="fas fa-user mr-1"></i> Ayah</span>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-pink-50 hover:border-pink-500 transition-colors">
                            <input type="radio" name="relation_type" value="ibu" {{ old('relation_type') == 'ibu' ? 'checked' : '' }} required
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500">
                            <span class="ml-3 font-medium text-gray-900"><i class="fas fa-user mr-1"></i> Ibu</span>
                        </label>
                        <label class="flex items-center p-4 border-2 border-gray-300 rounded-xl cursor-pointer hover:bg-purple-50 hover:border-purple-500 transition-colors">
                            <input type="radio" name="relation_type" value="wali" {{ old('relation_type') == 'wali' ? 'checked' : '' }} required
                                class="w-5 h-5 text-orange-600 focus:ring-orange-500">
                            <span class="ml-3 font-medium text-gray-900"><i class="fas fa-user mr-1"></i> Wali</span>
                        </label>
                    </div>
                    @error('relation_type')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Data Orang Tua/Wali -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-bold text-sm">2</div>
                <h2 class="text-xl font-bold text-gray-900">Data Orang Tua/Wali</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user mr-1"></i> Nama Lengkap</label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                        placeholder="Nama lengkap orang tua/wali"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    @error('full_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-phone mr-1"></i> Nomor Telepon</label>
                        <input type="text" name="phone" value="{{ old('phone') }}"
                            placeholder="Contoh: 081234567890"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        @error('phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-envelope mr-1"></i> Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                            placeholder="email@example.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-briefcase mr-1"></i> Pekerjaan</label>
                    <input type="text" name="occupation" value="{{ old('occupation') }}"
                        placeholder="Contoh: Wiraswasta, PNS, Guru"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                    @error('occupation')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-home mr-1"></i> Alamat</label>
                    <textarea name="address" rows="3"
                        placeholder="Alamat lengkap tempat tinggal"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('address') }}</textarea>
                    @error('address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Akun Portal -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-orange-500 to-amber-600 text-white font-bold text-sm">3</div>
                <h2 class="text-xl font-bold text-gray-900">Akun Portal (Opsional)</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="create_account" value="1" id="createAccountCheck" {{ old('create_account') ? 'checked' : '' }}
                            class="w-5 h-5 text-orange-600 border-gray-300 rounded focus:ring-orange-500"
                            onchange="toggleAccountFields()">
                        <span class="text-sm font-medium text-gray-700"><i class="fas fa-lock mr-1"></i> Buatkan akun untuk akses portal</span>
                    </label>
                    <p class="mt-2 text-xs text-gray-500">Dengan akun portal, orang tua dapat melihat perkembangan siswa</p>
                </div>

                <div id="accountFields" class="space-y-5" style="display: {{ old('create_account') ? 'block' : 'none' }}">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-envelope mr-1"></i> Email Akun</label>
                        <input type="email" name="account_email" value="{{ old('account_email') }}"
                            placeholder="email.akun@example.com"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <p class="mt-1 text-xs text-gray-500">Email ini akan digunakan untuk login</p>
                        @error('account_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-lock mr-1"></i> Password</label>
                        <input type="password" name="password"
                            placeholder="Minimal 6 karakter"
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-orange-600 to-amber-700 text-white rounded-xl font-medium hover:from-orange-700 hover:to-amber-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Data
            </button>
            <a href="{{ route('admin.parents.index') }}" 
                class="px-6 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function toggleAccountFields() {
    const checkbox = document.getElementById('createAccountCheck');
    const fields = document.getElementById('accountFields');
    fields.style.display = checkbox.checked ? 'block' : 'none';
}
</script>
@endsection
