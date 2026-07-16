@extends(auth()->user()->isKetuaYayasan() ? 'layouts.yayasan' : 'layouts.admin')

@section('title', 'Detail Monitoring Guru')
@section('page_title', 'Detail Laporan Monitoring - ' . $teacher->full_name)

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">{{ $teacher->full_name }}</h2>
            <p class="text-sm text-slate-500 mt-1">Pembimbing PKL</p>
        </div>
        @php
            $indexRoute = auth()->user()->isKetuaYayasan() ? route('yayasan.pkl_monitorings.index') : route('admin.pkl-alumni.monitorings.index');
        @endphp
        <a href="{{ $indexRoute }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Guru
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Daftar Lokasi DUDI -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-building text-indigo-500 mr-2"></i> Lokasi Bimbingan</h3>
                    <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">{{ count($placements) }}</span>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($placements as $group)
                        @php $place = $group->first(); @endphp
                        <div class="p-4 hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-0">
                            <div class="flex justify-between items-start">
                                <h4 class="font-bold text-slate-800 line-clamp-1">{{ $place->dudi->name ?? 'Unknown DUDI' }}</h4>
                            </div>
                            <div class="mt-2 text-xs text-slate-600 space-y-1">
                                <p><i class="fas fa-clock w-4 text-slate-400"></i> Shift: {{ $place->shift ?: '-' }}</p>
                                <p><i class="fas fa-users w-4 text-slate-400"></i> {{ $group->count() }} Siswa</p>
                                <p>
                                    @if($place->is_perangkat_ready)
                                        @if(isset($place->perangkat_file_path))
                                            <a href="{{ Storage::url($place->perangkat_file_path) }}" target="_blank" class="text-emerald-600 hover:text-emerald-800 transition-colors">
                                                <i class="fas fa-check-circle w-4"></i> Perangkat Siap (Lihat)
                                            </a>
                                        @else
                                            <span class="text-emerald-600"><i class="fas fa-check-circle w-4"></i> Perangkat Siap</span>
                                        @endif
                                    @else
                                        <span class="text-amber-600"><i class="fas fa-exclamation-triangle w-4"></i> Perangkat Belum Siap</span>
                                    @endif
                                </p>
                            </div>
                            
                            <div class="mt-3 pt-3 border-t border-slate-100 space-y-2">
                                <p class="text-xs font-semibold text-slate-500 mb-2">Daftar Siswa:</p>
                                @foreach($group as $studentPlacement)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full overflow-hidden border border-slate-200 shrink-0">
                                            <img src="{{ $studentPlacement->student->photo_url ?? asset('images/default-avatar.png') }}" class="w-full h-full object-cover" alt="Foto">
                                        </div>
                                        <div class="text-xs">
                                            <span class="font-semibold text-slate-700 block">{{ $studentPlacement->student->full_name ?? 'Siswa' }}</span>
                                            <span class="text-[10px] text-slate-500">{{ $studentPlacement->student->classroom->class_name ?? '-' }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500 text-sm">
                            Tidak ada data penempatan.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Riwayat Laporan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800"><i class="fas fa-file-invoice text-indigo-500 mr-2"></i> Riwayat Laporan Monitoring</h3>
                    <span class="px-2.5 py-1 bg-indigo-100 text-indigo-700 text-xs font-bold rounded-full">{{ $monitorings->total() }}</span>
                </div>
                
                <div class="divide-y divide-slate-100">
                    @forelse($monitorings as $mon)
                        <div class="p-5 hover:bg-slate-50 transition-colors">
                            <div class="flex flex-col sm:flex-row justify-between gap-4">
                                <div>
                                    <h4 class="font-bold text-slate-800">{{ $mon->monitoring_date->format('l, d F Y') }}</h4>
                                    <p class="text-xs text-indigo-600 font-semibold mt-1"><i class="fas fa-building mr-1"></i> {{ $mon->dudi->name ?? 'Unknown' }} (Shift: {{ $mon->shift ?: '-' }})</p>
                                    <p class="text-sm text-slate-600 mt-3">{{ $mon->notes ?? 'Tidak ada catatan monitoring.' }}</p>
                                </div>
                                <div class="flex gap-3 shrink-0">
                                    @if($mon->photo_path)
                                        <div x-data="{ open: false }">
                                            <img @click="open = true" src="{{ Storage::url($mon->photo_path) }}" class="w-16 h-16 object-cover rounded-xl cursor-pointer border border-slate-200 hover:opacity-80 transition-opacity shadow-sm" title="Lihat Foto Monitoring" alt="Foto">
                                            
                                            <div x-show="open" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/80 backdrop-blur-sm p-4" @click="open = false" @keydown.escape.window="open = false">
                                                <img src="{{ Storage::url($mon->photo_path) }}" class="max-w-full max-h-[90vh] rounded-xl shadow-2xl" @click.stop>
                                                <button @click="open = false" class="absolute top-6 right-6 text-white hover:text-slate-300">
                                                    <i class="fas fa-times text-3xl"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                    @if($mon->assignment_letter_path)
                                        <a href="{{ Storage::url($mon->assignment_letter_path) }}" target="_blank" class="flex flex-col items-center justify-center w-16 h-16 bg-emerald-50 border border-emerald-100 text-emerald-600 hover:bg-emerald-600 hover:text-white rounded-xl transition-all shadow-sm" title="Lihat Surat">
                                            <i class="fas fa-file-pdf text-xl mb-1"></i>
                                            <span class="text-[10px] font-bold uppercase">Surat</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center">
                            <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center text-2xl mx-auto mb-4 border border-slate-200 border-dashed">
                                <i class="fas fa-folder-open"></i>
                            </div>
                            <p class="text-slate-500 font-medium">Guru ini belum mengirimkan laporan monitoring satupun.</p>
                        </div>
                    @endforelse
                </div>

                @if($monitorings->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50">
                        {{ $monitorings->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>
@endsection
