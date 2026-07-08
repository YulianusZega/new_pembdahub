@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-gray-600 to-slate-700 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Tambah User Baru</h1>
            <p class="text-gray-600">Buat akun pengguna sistem</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
        @csrf

        <!-- Section 1: Informasi Akun -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-slate-700 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">1</span>
                    </div>
                    <h2 class="text-xl font-bold text-white"><i class="fas fa-lock mr-1"></i> Informasi Akun</h2>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-user mr-1"></i> Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-key mr-1"></i> Username</label>
                        <input type="text" name="username" value="{{ old('username') }}" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-envelope mr-1"></i> Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-lock mr-1"></i> Password</label>
                        <input type="password" name="password" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-check-circle text-green-500 mr-1"></i> Konfirmasi Password</label>
                        <input type="password" name="password_confirmation" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                    </div>
                </div>
            </div>
        </div>

        <!-- Section 2: Role & Status -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-slate-600 to-gray-700 px-6 py-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">2</span>
                    </div>
                    <h2 class="text-xl font-bold text-white"><i class="fas fa-theater-masks mr-1"></i> Role & Status</h2>
                </div>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-users mr-1"></i> Role Pengguna</label>
                    <select name="role" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                        <option value="">- Pilih Role -</option>
                        @if(auth()->user()->isSuperAdmin())
                        <option value="superadmin" {{ old('role')=='superadmin' ? 'selected' : '' }}>SuperAdmin</option>
                        <option value="admin_sekolah" {{ old('role')=='admin_sekolah' ? 'selected' : '' }}>Admin Sekolah</option>
                        @endif
                        @if(auth()->user()->isSuperAdmin() || auth()->user()->isAdminSekolah() || auth()->user()->isKetuaYayasan())
                        <option value="kepala_sekolah" {{ old('role')=='kepala_sekolah' ? 'selected' : '' }}>Kepala Sekolah</option>
                        @endif
                        <option value="guru" {{ old('role')=='guru' ? 'selected' : '' }}>Guru</option>
                        <option value="pegawai" {{ old('role')=='pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="bendahara" {{ old('role')=='bendahara' ? 'selected' : '' }}>Bendahara</option>
                        <option value="ketua_yayasan" {{ old('role')=='ketua_yayasan' ? 'selected' : '' }}>Ketua Yayasan</option>
                        <option value="siswa" {{ old('role')=='siswa' ? 'selected' : '' }}>Siswa</option>
                        <option value="orang_tua" {{ old('role')=='orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-school mr-1"></i> Sekolah</label>
                    <select name="school_id" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-slate-500 focus:border-transparent transition">
                        <option value="">- Pilih Sekolah -</option>
                        @foreach($schools as $s)
                        <option value="{{ $s->id }}" {{ old('school_id')==$s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked class="w-5 h-5 text-gray-600 border-2 border-gray-300 rounded focus:ring-2 focus:ring-gray-500 mr-3">
                        <span class="text-sm font-semibold text-gray-700"><i class="fas fa-check-circle text-green-500 mr-1"></i> Aktifkan Pengguna</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-gray-600 to-slate-700 hover:from-gray-700 hover:to-slate-800 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-save mr-1"></i> Simpan User
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection