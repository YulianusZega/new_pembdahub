@extends('layouts.admin')
@section('title', 'Manajemen Lencana - Pembda Elite')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-medal text-amber-500"></i> Koleksi Lencana (Badges)
            </h1>
            <p class="text-sm text-gray-500">Atur medali dan syarat pencapaian bagi pengguna</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reputation.badges.create') }}" class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                <i class="fas fa-plus"></i> Buat Lencana Baru
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($badges as $badge)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-xl hover:border-blue-100 transition duration-300">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div class="w-16 h-16 {{ $badge->color }} text-white rounded-2xl flex items-center justify-center text-3xl shadow-lg group-hover:scale-110 transition-transform">
                            <i class="fas {{ $badge->icon }}"></i>
                        </div>
                        <div class="flex flex-col items-end gap-2">
                            <span class="text-[10px] font-bold {{ $badge->is_active ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }} border px-2 py-0.5 rounded-full uppercase tracking-widest">
                                {{ $badge->is_active ? 'Aktif' : 'Non-Aktif' }}
                            </span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-tighter">Code: {{ $badge->code }}</span>
                        </div>
                    </div>

                    <h3 class="text-xl font-bold text-slate-800 mb-2">{{ $badge->name }}</h3>
                    <p class="text-xs text-slate-500 leading-relaxed mb-6">{{ $badge->description }}</p>

                    <div class="bg-slate-50 rounded-2xl p-4 mb-6">
                        <div class="flex items-center justify-between mb-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <span>Syarat Perolehan</span>
                            <span class="text-blue-600">{{ $badge->requirement_type }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-slate-800">
                            <i class="fas fa-star text-blue-500"></i>
                            <span class="text-lg font-bold">{{ number_format($badge->requirement_value) }}</span>
                            <span class="text-sm font-bold text-slate-400">Poin Milestone</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-50">
                        <div class="text-xs font-bold text-slate-400">
                            <i class="fas fa-users mr-1"></i> {{ $badge->users_count }} Pemilik
                        </div>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.reputation.badges.edit', $badge->id) }}" class="p-2 text-slate-400 hover:text-blue-600 transition">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.reputation.badges.destroy', $badge->id) }}" method="POST" onsubmit="return confirm('Hapus lencana ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        {{-- Add Card --}}
        <a href="{{ route('admin.reputation.badges.create') }}" class="group bg-white border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center p-8 hover:bg-white hover:border-emerald-300 transition duration-300 min-h-[350px]">
            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-slate-300 group-hover:bg-emerald-50 group-hover:text-emerald-500 transition-colors mb-4">
                <i class="fas fa-plus text-2xl"></i>
            </div>
            <p class="font-bold text-slate-400 group-hover:text-emerald-600 uppercase tracking-widest text-xs">Tambah Lencana</p>
        </a>
    </div>
</div>
@endsection
