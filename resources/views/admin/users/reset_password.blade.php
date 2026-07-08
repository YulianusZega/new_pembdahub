@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-gray-600 to-slate-700 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Reset Password User</h1>
            <p class="text-gray-600">Atur ulang kata sandi pengguna</p>
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

    <form action="{{ route('admin.users.reset-password', $user) }}" method="POST" class="space-y-6">
        @csrf

        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-slate-700 px-6 py-4">
                <h2 class="text-xl font-bold text-white"><i class="fas fa-key mr-1"></i> Password Baru</h2>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-lock mr-1"></i> Password Baru</label>
                    <input type="password" name="password" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-check-circle text-green-500 mr-1"></i> Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-gray-500 focus:border-transparent transition">
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-gray-600 to-slate-700 hover:from-gray-700 hover:to-slate-800 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-lock mr-1"></i> Reset Password
            </button>
            <a href="{{ route('admin.users.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection