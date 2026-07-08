@extends('layouts.admin')
@section('title', 'Log Reputasi - Pembda Elite')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-blue-500"></i> Log Aktivitas Reputasi
            </h1>
            <p class="text-sm text-gray-500">Monitoring histori perolehan poin seluruh pengguna</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.reputation.award.form') }}" class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                <i class="fas fa-plus"></i> Berikan Poin Manual
            </a>
            <a href="{{ route('reputation.leaderboard') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white px-4 py-2 rounded-xl text-sm font-bold shadow-sm transition">
                <i class="fas fa-trophy"></i> Lihat Hall of Fame
            </a>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form action="{{ route('admin.reputation.logs') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5 pl-1">Sekolah</label>
                <select name="school_id" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Sekolah</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5 pl-1">Role</label>
                <select name="role" class="w-full bg-gray-50 border border-gray-100 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Role</option>
                    <option value="siswa" {{ request('role') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                    <option value="guru" {{ request('role') == 'guru' ? 'selected' : '' }}>Guru</option>
                    <option value="superadmin" {{ request('role') == 'superadmin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1.5 pl-1">Cari Nama</label>
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pengguna..." 
                        class="w-full bg-gray-50 border border-gray-100 rounded-xl px-10 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                </div>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-50 text-blue-600 hover:bg-blue-100 px-4 py-2 rounded-xl text-sm font-bold transition">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    {{-- Logs Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengguna</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Poin</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50/50 transition duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gray-100 flex items-center justify-center font-bold text-gray-500 overflow-hidden">
                                        @php $name = $log->user->student ? $log->user->student->full_name : ($log->user->teacher ? $log->user->teacher->full_name : $log->user->name); @endphp
                                        <img src="{{ $log->user->student ? $log->user->student->photo_url : ($log->user->teacher ? $log->user->teacher->photo_url : asset('images/default-student.jpg')) }}" class="w-full h-full object-cover" alt="{{ $name }}">
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-800 leading-tight">{{ $name }}</p>
                                        <div class="flex items-center gap-1.5 mt-0.5">
                                            <span class="text-[9px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded uppercase">{{ $log->user->role }}</span>
                                            <span class="text-[9px] text-gray-400">{{ $log->user->school->name ?? 'Internal' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-block text-sm font-bold {{ $log->points >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    {{ $log->points >= 0 ? '+' : '' }}{{ $log->points }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full uppercase italic tracking-tighter">
                                    {{ str_replace('_', ' ', $log->category) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-600 truncate max-w-xs" title="{{ $log->description }}">
                                    {{ $log->description }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-xs text-gray-500">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</p>
                                <p class="text-[10px] text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.reputation.logs.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Batalkan perolehan poin ini? Skor user akan dikurangi kembali.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-rose-300 hover:text-rose-600 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic">
                                Belum ada data perolehan poin.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
