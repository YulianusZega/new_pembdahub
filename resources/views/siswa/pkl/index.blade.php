@extends('layouts.siswa')
@section('title', 'Praktik Kerja Lapangan (PKL) - Portal Siswa')

@section('content')
<div class="space-y-6">
    {{-- Header Bar --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-briefcase text-amber-500"></i> Monitoring Praktik Kerja Lapangan (PKL)
            </h1>
            <p class="text-xs text-gray-500 mt-0.5">
                SMKS Swasta Pembda Nias — Manajemen Logbook & Evaluasi Industri
            </p>
        </div>
        <div>
            <span class="inline-flex items-center gap-1.5 bg-amber-50 text-amber-700 px-3 py-1.5 rounded-lg text-xs font-semibold">
                <i class="far fa-calendar text-xs"></i> TA {{ date('Y') }}
            </span>
        </div>
    </div>

    @if(!$placement)
        {{-- No Active Placement state --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center max-w-xl mx-auto my-10">
            <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i class="fas fa-briefcase text-4xl"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-2">Penempatan PKL Belum Aktif</h3>
            <p class="text-sm text-gray-500 mb-6 leading-relaxed">
                Anda belum terdaftar dalam penempatan Praktik Kerja Lapangan (PKL) yang aktif di sistem. Silakan berkoordinasi dengan Panitia PKL atau Admin Sekolah untuk pengaturan penempatan industri Anda.
            </p>
            <a href="{{ route('siswa.dashboard') }}" class="inline-flex items-center gap-2 bg-amber-500 text-white font-bold px-5 py-2.5 rounded-xl shadow-md hover:bg-amber-600 transition text-sm">
                <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
            </a>
        </div>
    @else
        {{-- Active Placement view --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left column: Placement & Evaluation --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Placement Info --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-bold text-gray-850 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-building text-amber-500"></i> Informasi Penempatan
                    </h3>
                    <div class="space-y-4">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Instansi / Perusahaan DUDI</span>
                            <p class="text-sm font-bold text-gray-850 leading-snug">{{ $placement->company_name }}</p>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed"><i class="fas fa-map-marker-alt text-rose-500 mr-1"></i>{{ $placement->company_address }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4 border-t border-gray-50 pt-3">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pembimbing Lapangan</span>
                                <p class="text-xs font-bold text-gray-800">{{ $placement->mentor_name }}</p>
                                @if($placement->mentor_phone)
                                    <p class="text-xs text-gray-500 mt-0.5"><i class="fab fa-whatsapp text-emerald-500 mr-1"></i>{{ $placement->mentor_phone }}</p>
                                @endif
                            </div>
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Guru Pembimbing</span>
                                <p class="text-xs font-bold text-gray-800">{{ $placement->teacher->user->name ?? 'Belum ditentukan' }}</p>
                                @if($placement->teacher && $placement->teacher->user->phone)
                                    <p class="text-xs text-gray-500 mt-0.5"><i class="fas fa-phone text-blue-500 mr-1"></i>{{ $placement->teacher->user->phone }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="border-t border-gray-50 pt-3">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Durasi Magang</span>
                            <p class="text-xs font-semibold text-gray-700 flex items-center gap-1.5 mt-0.5">
                                <i class="far fa-calendar-alt text-gray-400"></i>
                                {{ \Carbon\Carbon::parse($placement->start_date)->translatedFormat('d M Y') }} — {{ \Carbon\Carbon::parse($placement->end_date)->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Grades Card (If graded) --}}
                @if($placement->grade)
                    <div class="bg-gradient-to-br from-slate-900 to-slate-800 text-white rounded-2xl shadow-lg border border-slate-700 p-5 relative overflow-hidden">
                        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-500/10 rounded-full"></div>
                        <h3 class="text-sm font-bold border-b border-white/10 pb-3 mb-4 flex items-center justify-between">
                            <span class="flex items-center gap-2"><i class="fas fa-star text-amber-400"></i> Nilai Akhir DUDI</span>
                            <span class="bg-emerald-500/20 text-emerald-400 text-xs font-bold px-2 py-0.5 rounded-full border border-emerald-500/30">SELESAI</span>
                        </h3>
                        
                        <div class="flex items-center justify-between mb-5">
                            <div class="text-4xl font-extrabold text-white">{{ number_format($placement->grade->score_average, 1) }}</div>
                            <div class="text-right">
                                <p class="text-[10px] text-slate-400 uppercase tracking-wider">Diserahkan tanggal</p>
                                <p class="text-xs font-semibold text-slate-200">{{ \Carbon\Carbon::parse($placement->grade->submitted_at)->translatedFormat('d M Y') }}</p>
                            </div>
                        </div>

                        <div class="space-y-2 text-xs">
                            <div class="flex items-center justify-between p-2 rounded bg-white/5">
                                <span class="text-slate-300">Kedisiplinan</span>
                                <span class="font-bold text-emerald-400">{{ $placement->grade->score_discipline }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded bg-white/5">
                                <span class="text-slate-300">Kerjasama Tim</span>
                                <span class="font-bold text-emerald-400">{{ $placement->grade->score_teamwork }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded bg-white/5">
                                <span class="text-slate-300">Kemampuan Teknis</span>
                                <span class="font-bold text-emerald-400">{{ $placement->grade->score_technical }}</span>
                            </div>
                            <div class="flex items-center justify-between p-2 rounded bg-white/5">
                                <span class="text-slate-300">Keselamatan Kerja</span>
                                <span class="font-bold text-emerald-400">{{ $placement->grade->score_safety }}</span>
                            </div>
                        </div>

                        @if($placement->grade->notes)
                            <div class="mt-4 p-3 bg-white/5 rounded-xl border border-white/5 text-xs">
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Catatan Evaluasi Mentor:</p>
                                <p class="text-slate-200 italic">"{{ $placement->grade->notes }}"</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right Column: Form + History --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Form Logbook --}}
                @if(!$placement->grade)
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                        <h3 class="text-sm font-bold text-gray-850 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                            <i class="fas fa-edit text-amber-500"></i> Isi Logbook Harian PKL
                        </h3>
                        
                        @if(session('success'))
                            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl text-xs font-semibold mb-4">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl text-xs font-semibold mb-4">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('siswa.pkl.log.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal Kegiatan</label>
                                    <input type="date" name="log_date" value="{{ date('Y-m-d') }}" max="{{ date('Y-m-d') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition" required>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Foto Bukti Kegiatan (Maks 5MB)</label>
                                    <input type="file" name="photo" accept="image/*" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Deskripsi Aktivitas & Hasil Pekerjaan</label>
                                <textarea name="activity" rows="4" placeholder="Tuliskan detail pekerjaan, alat/bahan yang digunakan, dan hasil yang dicapai hari ini (Minimal 10 karakter)..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:bg-white transition" required></textarea>
                            </div>

                            {{-- GPS Geolocation info --}}
                            <div class="bg-amber-50/50 border border-amber-100 rounded-xl px-4 py-3">
                                <div class="flex items-center gap-2 text-xs font-medium text-amber-800" id="gps-status">
                                    <i class="fas fa-spinner animate-spin text-amber-500"></i> Mengambil koordinat GPS Anda...
                                </div>
                                <input type="hidden" name="latitude" id="latitude">
                                <input type="hidden" name="longitude" id="longitude">
                            </div>

                            <div class="flex justify-end">
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-6 py-2.5 rounded-xl shadow transition text-sm flex items-center gap-2">
                                    <i class="fas fa-paper-plane"></i> Kirim Logbook
                                </button>
                            </div>
                        </form>
                    </div>
                @endif

                {{-- History Logbook --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-sm font-bold text-gray-850 border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                        <i class="fas fa-history text-indigo-500"></i> Riwayat Logbook PKL
                    </h3>

                    <div class="space-y-4">
                        @forelse($placement->logs as $log)
                            <div class="border border-gray-100 rounded-xl p-4 hover:bg-gray-50/30 transition">
                                <div class="flex flex-col md:flex-row md:items-start justify-between gap-3 mb-3">
                                    <div>
                                        <p class="text-xs font-bold text-gray-700">
                                            {{ \Carbon\Carbon::parse($log->log_date)->translatedFormat('l, d M Y') }}
                                        </p>
                                        @if($log->latitude && $log->longitude)
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ $log->latitude }},{{ $log->longitude }}" target="_blank" class="inline-flex items-center text-[10px] text-blue-600 hover:underline mt-1">
                                                <i class="fas fa-map-marked-alt mr-1"></i> Lokasi GPS ({{ number_format($log->latitude, 6) }}, {{ number_format($log->longitude, 6) }})
                                            </a>
                                        @else
                                            <span class="text-[10px] text-gray-400 italic mt-1 block">GPS tidak terekam</span>
                                        @endif
                                    </div>
                                    <div>
                                        @php
                                            $statusClass = match($log->status) {
                                                'approved' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'rejected' => 'bg-rose-50 text-rose-700 border-rose-200',
                                                default => 'bg-amber-50 text-amber-700 border-amber-200'
                                            };
                                            $statusText = match($log->status) {
                                                'approved' => 'Disetujui Mentor',
                                                'rejected' => 'Perlu Revisi',
                                                default => 'Menunggu Persetujuan'
                                            };
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $statusClass }}">
                                            <i class="fas {{ $log->status === 'approved' ? 'fa-check' : ($log->status === 'rejected' ? 'fa-times' : 'fa-clock') }}"></i>
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                    <div class="md:col-span-3">
                                        <p class="text-xs text-gray-600 whitespace-pre-line">{{ $log->activity }}</p>
                                    </div>
                                    @if($log->photo)
                                        <div class="md:col-span-1">
                                            <a href="{{ asset('storage/' . $log->photo) }}" target="_blank" class="block rounded-lg overflow-hidden border border-gray-200 hover:opacity-90 transition max-h-[100px] shadow-sm">
                                                <img src="{{ asset('storage/' . $log->photo) }}" class="w-full h-[100px] object-cover" alt="Bukti Foto">
                                            </a>
                                        </div>
                                    @endif
                                </div>

                                @if($log->status === 'rejected' && $log->mentor_notes)
                                    <div class="mt-3 p-3 bg-rose-50/50 border border-rose-100 rounded-xl text-xs text-rose-800">
                                        <span class="font-bold flex items-center gap-1 mb-1"><i class="fas fa-exclamation-circle"></i> Catatan Mentor:</span>
                                        <p class="italic">"{{ $log->mentor_notes }}"</p>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-center py-10 text-xs text-gray-400 italic">Belum ada riwayat logbook kegiatan magang.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if($placement && !$placement->grade)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const gpsStatus = document.getElementById('gps-status');
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                latInput.value = position.coords.latitude;
                lngInput.value = position.coords.longitude;
                gpsStatus.innerHTML = '<span class="text-emerald-700 flex items-center gap-1.5"><i class="fas fa-check-circle"></i> Koordinat GPS berhasil dikunci (' + position.coords.latitude.toFixed(6) + ', ' + position.coords.longitude.toFixed(6) + ')</span>';
            }, function(error) {
                console.error('GPS error:', error);
                gpsStatus.innerHTML = '<span class="text-amber-700 flex items-center gap-1.5"><i class="fas fa-exclamation-triangle"></i> Lokasi GPS gagal dimuat. Logbook tetap bisa dikirim tanpa lokasi GPS.</span>';
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            });
        } else {
            gpsStatus.innerHTML = '<span class="text-rose-700 flex items-center gap-1.5"><i class="fas fa-times-circle"></i> Browser Anda tidak mendukung sensor GPS.</span>';
        }
    });
</script>
@endif
@endsection
