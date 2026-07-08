@extends('layouts.admin')
@section('title', 'Edit Lencana - Pembda Elite')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.reputation.badges.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Lencana: {{ $badge->name }}</h1>
            <p class="text-sm text-gray-500">Sesuaikan parameter dan syarat perolehan medali</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <form action="{{ route('admin.reputation.badges.update', $badge->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Nama Lencana <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $badge->name) }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-4 text-lg font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                {{-- Code & requirement --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Kode Unik (Slug) <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $badge->code) }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Milestone Poin <span class="text-rose-500">*</span></label>
                    <input type="number" name="requirement_value" value="{{ old('requirement_value', $badge->requirement_value) }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Tipe Syarat <span class="text-rose-500">*</span></label>
                    <select name="requirement_type" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                        <option value="points" {{ $badge->requirement_type == 'points' ? 'selected' : '' }}>Total Poin</option>
                        <option value="attendance" {{ $badge->requirement_type == 'attendance' ? 'selected' : '' }}>Absensi (Hadir)</option>
                        <option value="quiz" {{ $badge->requirement_type == 'quiz' ? 'selected' : '' }}>LMS Quiz</option>
                        <option value="other" {{ $badge->requirement_type == 'other' ? 'selected' : '' }}>Kategori Lain</option>
                    </select>
                </div>

                {{-- Icon --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Icon (Font Awesome) <span class="text-rose-500">*</span></label>
                    <input type="text" name="icon" value="{{ old('icon', $badge->icon) }}" required 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Tema Warna (Tailwind class) <span class="text-rose-500">*</span></label>
                    <select name="color" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                        <option value="bg-blue-600" {{ $badge->color == 'bg-blue-600' ? 'selected' : '' }}>Biru (Premium)</option>
                        <option value="bg-amber-500" {{ $badge->color == 'bg-amber-500' ? 'selected' : '' }}>Emas (Master)</option>
                        <option value="bg-slate-400" {{ $badge->color == 'bg-slate-400' ? 'selected' : '' }}>Perak (Elite)</option>
                        <option value="bg-emerald-500" {{ $badge->color == 'bg-emerald-500' ? 'selected' : '' }}>Hijau (Growth)</option>
                        <option value="bg-rose-500" {{ $badge->color == 'bg-rose-500' ? 'selected' : '' }}>Merah (Courage)</option>
                        <option value="bg-indigo-600" {{ $badge->color == 'bg-indigo-600' ? 'selected' : '' }}>Ungu (Wisdom)</option>
                        <option value="bg-slate-900" {{ $badge->color == 'bg-slate-900' ? 'selected' : '' }}>Hitam (Legendary)</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Status Lencana</label>
                    <label class="flex items-center gap-3 bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 cursor-pointer hover:bg-white transition-all">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" {{ $badge->is_active ? 'checked' : '' }} class="w-5 h-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-bold text-slate-700">Aktif & Dapat Diperoleh</span>
                    </label>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Deskripsi Pemicu <span class="text-rose-500">*</span></label>
                    <textarea name="description" required rows="2" 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-4 text-sm font-medium focus:border-blue-500 focus:bg-white outline-none transition-all">{{ old('description', $badge->description) }}</textarea>
                </div>
            </div>

            <div class="mt-10 flex items-center justify-end gap-3">
                <a href="{{ route('admin.reputation.badges.index') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-400 hover:text-slate-600 transition uppercase tracking-widest">Batal</a>
                <button type="submit" class="px-12 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/30 transition transform hover:-translate-y-1 uppercase tracking-widest">
                    Update Lencana
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
