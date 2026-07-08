@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-gray-600 to-slate-700 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Detail User</h1>
            <p class="text-gray-600">Informasi lengkap pengguna sistem</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-gray-600 to-slate-700 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="text-2xl font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $user->name }}</h2>
                    <p class="text-gray-200 text-sm">{{ $user->username }}</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-user mr-1"></i> Nama Lengkap</label>
                    <p class="text-gray-800 font-medium">{{ $user->name }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-key mr-1"></i> Username</label>
                    <p class="text-gray-800 font-medium">{{ $user->username }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-envelope mr-1"></i> Email</label>
                    <p class="text-gray-800 font-medium">{{ $user->email }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-theater-masks mr-1"></i> Role</label>
                    <p><span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm font-medium">{{ $user->role }}</span></p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-school mr-1"></i> Sekolah</label>
                    <p class="text-gray-800 font-medium">{{ $user->school->name ?? '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-chart-bar mr-1"></i> Status</label>
                    <p>
                        @if($user->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm font-medium"><i class="fas fa-check-circle text-green-500 mr-1"></i> Aktif</span>
                        @else
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-sm font-medium"><i class="fas fa-times-circle text-red-500 mr-1"></i> Nonaktif</span>
                        @endif
                    </p>
                </div>
                <div class="col-span-2">
                    <label class="block text-sm font-semibold text-gray-500 mb-1"><i class="fas fa-clock mr-1"></i> Terakhir Login</label>
                    <p class="text-gray-800 font-medium">{{ $user->last_login ? $user->last_login->format('d-m-Y H:i') : '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex gap-3">
        <a href="{{ route('admin.users.edit', $user) }}" class="bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
            <i class="fas fa-pencil-alt mr-1"></i> Edit User
        </a>
        <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
            ← Kembali
        </a>
    </div>
</div>
@endsection