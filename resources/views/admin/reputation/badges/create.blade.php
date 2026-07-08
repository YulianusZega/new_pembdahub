@extends('layouts.admin')
@section('title', 'Buat Lencana - Pembda Elite')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.reputation.badges.index') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Buat Lencana Baru</h1>
            <p class="text-sm text-gray-500">Definisikan medali pencapaian baru untuk ekosistem</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <form action="{{ route('admin.reputation.badges.store') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Nama Lencana <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Sang Juara Kelas" 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-4 text-lg font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                {{-- Code & requirement --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Kode Unik (Slug) <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="Contoh: top_scholar" 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Milestone Poin <span class="text-rose-500">*</span></label>
                    <input type="number" name="requirement_value" required placeholder="1000" 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Tipe Syarat <span class="text-rose-500">*</span></label>
                    <select name="requirement_type" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                        <option value="points">Total Poin</option>
                        <option value="attendance">Absensi (Hadir)</option>
                        <option value="quiz">LMS Quiz</option>
                        <option value="other">Kategori Lain</option>
                    </select>
                </div>

                {{-- Icon --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Icon (Font Awesome) <span class="text-rose-500">*</span></label>
                    <input type="text" name="icon" required placeholder="fa-trophy" 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                </div>

                {{-- Color --}}
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Tema Warna (Tailwind class) <span class="text-rose-500">*</span></label>
                    <select name="color" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-3 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                        <option value="bg-blue-600">Biru (Premium)</option>
                        <option value="bg-amber-500">Emas (Master)</option>
                        <option value="bg-slate-400">Perak (Elite)</option>
                        <option value="bg-emerald-500">Hijau (Growth)</option>
                        <option value="bg-rose-500">Merah (Courage)</option>
                        <option value="bg-indigo-600">Ungu (Wisdom)</option>
                        <option value="bg-slate-900">Hitam (Legendary)</option>
                    </select>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Deskripsi Pemicu <span class="text-rose-500">*</span></label>
                    <textarea name="description" required rows="2" placeholder="Jelaskan apa yang harus dilakukan pengguna..." 
                        class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-4 text-sm font-medium focus:border-blue-500 focus:bg-white outline-none transition-all"></textarea>
                </div>
            </div>

            <div class="mt-10 flex items-center justify-end gap-3">
                <a href="{{ route('admin.reputation.badges.index') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-400 hover:text-slate-600 transition uppercase tracking-widest">Batal</a>
                <button type="submit" class="px-12 py-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-emerald-500/30 transition transform hover:-translate-y-1 uppercase tracking-widest">
                    Simpan Lencana
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
