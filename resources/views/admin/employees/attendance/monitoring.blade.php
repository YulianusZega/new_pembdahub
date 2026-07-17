@extends('layouts.admin')

@section('title', 'Monitoring Absensi - Admin')

@push('styles')
<style>
@keyframes fadeInSlideUp {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fadeInSlideUp 0.3s ease-out forwards;
}
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2m0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-gray-800">Monitoring Absensi</h1>
                    <span id="live_status_badge" style="display: none;" class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200 shadow-sm">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        LIVE
                    </span>
                </div>
                <p class="text-gray-600">Analisis real-time kehadiran pegawai tingkat sekolah</p>
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-3">
            @if($isSuperAdmin)
            <div class="min-w-[200px]">
                <select name="school_id" onchange="this.form.submit()" class="w-full border-gray-200 rounded-xl focus:ring-teal-500 focus:border-teal-500 shadow-sm text-sm">
                    <option value="">Semua Sekolah</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" {{ $schoolId == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 px-3 py-1.5">
                <i class="fas fa-calendar-alt text-gray-400 mr-2 text-sm"></i>
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()" class="border-none focus:ring-0 text-sm p-0 text-gray-700 font-semibold">
            </div>
        </form>
    </div>

    <!-- Cumulative Overview Section -->
    <div class="bg-gradient-to-r from-teal-600 to-emerald-700 rounded-2xl p-8 shadow-xl text-white mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 -m-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-xl">
                <h2 class="text-2xl font-bold mb-2 flex items-center gap-3">
                    <i class="fas fa-chart-pie text-teal-300"></i> Rekap Kehadiran Kumulatif
                </h2>
                <p class="text-teal-50 text-sm leading-relaxed opacity-90">
                    Berdasarkan formula: <span class="font-mono bg-black/20 px-2 py-0.5 rounded">(Hadir / Hari Aktif Sekolah) * 100%</span>. 
                    Dimana <span class="font-bold">Hari Aktif Sekolah</span> adalah hari efektif belajar sejak awal tahun ajaran sampai hari ini.
                </p>
                <div class="mt-4 flex flex-wrap gap-4 text-xs font-bold uppercase tracking-wider">
                    <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg border border-gray-100">
                        <i class="fas fa-calendar-day text-teal-300"></i> Total Hari Aktif Sekolah: {{ $cumulativeStats['z'] }} Hari
                    </span>
                </div>
            </div>
            <div class="flex items-end gap-1">
                <div class="text-right">
                    <p class="text-xs font-bold text-teal-200 uppercase tracking-widest mb-1">Total Persentase</p>
                    <h3 class="text-6xl font-bold leading-none">
                        <span id="live_cumulative_rate" class="transition-all duration-300">{{ $cumulativeStats['z'] > 0 ? round(($cumulativeStats['hadir'] / ($cumulativeStats['z'] * ($classroomStats->sum('employees_count') ?: 1))) * 100, 1) : 0 }}</span>%
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid (Daily Snapshot) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-6">
        <!-- Hadir -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-green-50 rounded-full group-hover:scale-110 transition duration-500 opacity-50"></div>
            <div class="relative">
                <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center text-green-600 mb-4">
                    <i class="fas fa-check-circle"></i>
                </div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Hadir Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_hadir">{{ number_format($dailyStats['hadir']) }}</h3>
                    <span class="text-xs font-bold text-green-600 mb-1.5 transition-all duration-300" id="live_daily_hadir_percentage">{{ $dailyStats['total_daily'] > 0 ? round(($dailyStats['hadir'] / $dailyStats['total_daily']) * 100, 1) : 0 }}%</span>
                </div>
            </div>
        </div>

        <!-- Terlambat -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-purple-50 rounded-full group-hover:scale-110 transition duration-500 opacity-50"></div>
            <div class="relative">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-4">
                    <i class="fas fa-car"></i>
                </div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Dinas Luar Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_dinas_luar">{{ number_format($dailyStats['terlambat']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Izin -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-blue-50 rounded-full group-hover:scale-110 transition duration-500 opacity-50"></div>
            <div class="relative">
                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-4">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Izin Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_izin">{{ number_format($dailyStats['izin']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Sakit -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-amber-50 rounded-full group-hover:scale-110 transition duration-500 opacity-50"></div>
            <div class="relative">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 mb-4">
                    <i class="fas fa-medkit"></i>
                </div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Sakit Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_sakit">{{ number_format($dailyStats['sakit']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Alpha -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-red-50 rounded-full group-hover:scale-110 transition duration-500 opacity-50"></div>
            <div class="relative">
                <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600 mb-4">
                    <i class="fas fa-times-circle"></i>
                </div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Alpa Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_alpha">{{ number_format($dailyStats['alpha']) }}</h3>
                </div>
            </div>
        </div>

        <!-- Total Hari Sekolah (Z) -->
        <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 shadow-lg shadow-indigo-200 border border-indigo-500 hover:scale-[1.02] transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition duration-500"></div>
            <div class="relative text-white">
                <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <p class="text-indigo-100 text-xs font-bold uppercase tracking-wider">Hari Aktif Sekolah</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $cumulativeStats['z'] }} <span class="text-sm font-normal opacity-70">Hari</span></h3>
            </div>
        </div>
    </div>

    <!-- Charts & Classroom Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Presence Trend (Line Chart) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Tren Presence Terakhir</h3>
                    <p class="text-xs text-gray-400">Statistik kehadiran harian (snapshot)</p>
                </div>
                <div class="flex gap-2">
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase">
                        <span class="w-2.5 h-2.5 bg-green-500 rounded-full"></span> Hadir
                    </span>
                    <span class="flex items-center gap-1.5 text-[10px] font-bold text-gray-400 uppercase">
                        <span class="w-2.5 h-2.5 bg-red-400 rounded-full"></span> Absen
                    </span>
                </div>
            </div>
            <div class="p-8">
                <div style="height: 350px;">
                    <canvas id="presenceTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Presence Composition (Pie Chart) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50">
                <h3 class="text-lg font-bold text-gray-800">Komposisi Kehadiran</h3>
                <p class="text-xs text-gray-400">Total partisipasi siswa kumulatif</p>
            </div>
            <div class="p-8">
                @if($cumulativeStats['hadir'] > 0)
                    <div style="height: 300px;" class="mb-6">
                        <canvas id="dailyPieChart"></canvas>
                    </div>
                @else
                    <div class="flex flex-col items-center justify-center py-20 text-gray-400">
                        <i class="fas fa-chart-pie text-5xl mb-4 opacity-20"></i>
                        <p class="text-sm">Mulai kumpulkan data absensi</p>
                    </div>
                @endif
                <div class="space-y-3">
                    <div id="live_chart_data_container" style="display:none;" data-chart-data="{{ json_encode($chartData) }}" data-cum-hadir="{{ $cumulativeStats['hadir'] }}" data-cum-terlambat="{{ $cumulativeStats['terlambat'] }}" data-cum-izin="{{ $cumulativeStats['izin'] }}" data-cum-sakit="{{ $cumulativeStats['sakit'] }}" data-cum-alpha="{{ $cumulativeStats['alpha'] }}"></div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-gray-600 font-medium">
                            <span class="w-3 h-3 bg-emerald-500 rounded-full"></span> Hadir
                        </span>
                        <span class="font-bold text-gray-800 transition-all duration-300" id="live_cum_hadir">{{ number_format($cumulativeStats['hadir']) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-gray-600 font-medium">
                            <span class="w-3 h-3 bg-blue-500 rounded-full"></span> Izin
                        </span>
                        <span class="font-bold text-gray-800 transition-all duration-300" id="live_cum_izin">{{ number_format($cumulativeStats['izin']) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-gray-600 font-medium">
                            <span class="w-3 h-3 bg-purple-500 rounded-full"></span> Terlambat
                        </span>
                        <span class="font-bold text-gray-800 transition-all duration-300" id="live_cum_terlambat">{{ number_format($cumulativeStats['terlambat']) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-gray-600 font-medium">
                            <span class="w-3 h-3 bg-amber-500 rounded-full"></span> Sakit
                        </span>
                        <span class="font-bold text-gray-800 transition-all duration-300" id="live_cum_sakit">{{ number_format($cumulativeStats['sakit']) }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-gray-600 font-medium">
                            <span class="w-3 h-3 bg-red-500 rounded-full"></span> Alpha
                        </span>
                        <span class="font-bold text-gray-800 transition-all duration-300" id="live_cum_alpha">{{ number_format($cumulativeStats['alpha']) }}</span>
                    </div>
                    
                    <div class="pt-2 border-t border-gray-50 flex items-center justify-between text-sm">
                        <span class="text-gray-500 font-bold uppercase text-[10px] tracking-widest">Total Hari Aktif Sekolah</span>
                        <span class="font-bold text-indigo-600">{{ $cumulativeStats['z'] }} Hari</span>
                    </div>
                </div>
            </div>
        </div>
    </div>    <!-- Grid Classroom Rank & Live Feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Classroom Rank Table (2/3 width) -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-8 py-6 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Peringkat Kehadiran Kumulatif Per Unit</h3>
                    <p class="text-sm text-gray-400 mt-1">Peringkat berdasarkan persentase kehadiran terhadap Total Hari Aktif Sekolah</p>
                </div>
                <span class="bg-teal-50 text-teal-700 px-4 py-1.5 rounded-full text-xs font-bold border border-teal-100 uppercase tracking-widest">
                    Urutan Tertinggi
                </span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="bg-gray-50/50">
                            <th class="px-8 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Rank</th>
                            <th class="px-8 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-widest">Nama Unit</th>
                            <th class="px-8 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-widest">Hari Aktif</th>
                            <th class="px-8 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-widest">Total Pegawai</th>
                            <th class="px-8 py-4 text-right text-xs font-bold text-green-600 uppercase tracking-widest">Total Hadir</th>
                            <th class="px-8 py-4 text-right text-xs font-bold text-indigo-500 uppercase tracking-widest">Hadir Hari Ini</th>
                            <th class="px-8 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-widest">Persentase Kumulatif</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50" id="live_classroom_tbody">
                        @forelse($classroomStats as $index => $cls)
                            <tr class="group hover:bg-gray-50 transition duration-150">
                                <td class="px-8 py-5">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-xs {{ $index < 3 ? 'bg-teal-500 text-white shadow-sm' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="font-bold text-gray-800 group-hover:text-teal-600 transition">{{ $cls->school_name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $cls->school->name ?? '-' }}</div>
                                </td>
                                <td class="px-8 py-5 text-right font-bold text-indigo-600">{{ $cls->z_days }}</td>
                                <td class="px-8 py-5 text-right font-semibold text-gray-600">{{ $cls->employees_count }}</td>
                                <td class="px-8 py-5 text-right font-bold text-green-600">{{ number_format($cls->total_hadir) }}</td>
                                <td class="px-8 py-5 text-right">
                                    <span class="px-2 py-1 rounded-md bg-indigo-50 text-indigo-700 font-bold text-xs">{{ $cls->daily_present }}</span>
                                </td>
                                <td class="px-8 py-5">
                                    <div class="flex items-center justify-center gap-3">
                                        <div class="flex-1 max-w-[100px] h-2 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r {{ $cls->presence_rate > 90 ? 'from-green-400 to-emerald-500' : ($cls->presence_rate > 70 ? 'from-blue-400 to-indigo-500' : 'from-orange-400 to-red-500') }}" style="width: {{ $cls->presence_rate }}%"></div>
                                        </div>
                                        <span class="text-sm font-bold text-gray-800 min-w-[45px] text-right">{{ $cls->presence_rate }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-20 text-center text-gray-400">
                                    <i class="fas fa-school text-5xl mb-4 opacity-10"></i>
                                    <p>Belum ada data absensi untuk Unit manapun di tanggal ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Live Activity Feed (1/3 width) -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[550px]">
            <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800">Aktivitas Tap Terbaru</h3>
                    <p class="text-xs text-gray-400">Arus scan RFID & QR Code hari ini</p>
                </div>
                <span class="flex h-3 w-3 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                </span>
            </div>
            <div class="p-6 overflow-y-auto flex-1 space-y-4" id="live_feed_container" style="max-height: 480px;">
                <div class="flex flex-col items-center justify-center h-full text-gray-400 py-20">
                    <i class="fas fa-id-card text-4xl mb-3 opacity-20"></i>
                    <p class="text-xs">Menunggu aktivitas tap...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Prepare data for line chart
        const initialChartData = @json($chartData);
        const dates = Object.keys(initialChartData).sort();
        
        const hadirData = dates.map(d => initialChartData[d].hadir || 0);
        const absenData = dates.map(d => {
            return (initialChartData[d].izin || 0) + (initialChartData[d].sakit || 0) + (initialChartData[d].alpha || 0);
        });

        // Trend Line Chart
        const trendCtx = document.getElementById('presenceTrendChart').getContext('2d');
        window.trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: dates.map(d => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })),
                datasets: [
                    {
                        label: 'Hadir',
                        data: hadirData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    },
                    {
                        label: 'Absen',
                        data: absenData,
                        borderColor: '#f87171',
                        backgroundColor: 'rgba(248, 113, 113, 0.1)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 3,
                        pointRadius: 4,
                        pointBackgroundColor: '#fff',
                        pointBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: 'rgba(0,0,0,0.03)' },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' }, color: '#94a3b8' }
                    }
                }
            }
        });

        const cumHadir = {{ $cumulativeStats['hadir'] }};
        const cumTerlambat = {{ $cumulativeStats['terlambat'] }};
        const cumIzin = {{ $cumulativeStats['izin'] }};
        const cumSakit = {{ $cumulativeStats['sakit'] }};
        const cumAlpha = {{ $cumulativeStats['alpha'] }};

        // Cumulative Distribution Pie Chart
        const pieCtx = document.getElementById('dailyPieChart')?.getContext('2d');
        if (pieCtx) {
            window.pieChart = new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alpha'],
                    datasets: [{
                        data: [cumHadir, cumTerlambat, cumIzin, cumSakit, cumAlpha],
                        backgroundColor: ['#10b981', '#f97316', '#3b82f6', '#f59e0b', '#ef4444'],
                        borderWidth: 0,
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }

        const selectedDate = "{{ $date }}";
        const todayStr = new Date().toLocaleDateString('en-CA', { timeZone: 'Asia/Jakarta' }); // format: YYYY-MM-DD
        
        const badge = document.getElementById('live_status_badge');
        const activeSchoolType = '{{ $schoolId ? strtolower($school?->type) : "" }}';

        // Render functions
        function updateUIElement(id, value, isPercentage = false) {
            const el = document.getElementById(id);
            if (el) {
                const cleanValue = isPercentage ? value + '%' : Number(value).toLocaleString('id-ID');
                if (el.textContent.trim() !== cleanValue) {
                    el.textContent = cleanValue;
                    // Visual flash effect
                    el.classList.add('text-teal-600', 'scale-110');
                    setTimeout(() => {
                        el.classList.remove('text-teal-600', 'scale-110');
                    }, 1000);
                }
            }
        }

        function renderClassrooms(classroomStats) {
            const tbody = document.getElementById('live_classroom_tbody');
            if (!tbody) return;
            
            if (classroomStats.length === 0) {
                tbody.innerHTML = `<tr>
                    <td colspan="7" class="px-8 py-20 text-center text-gray-400">
                        <i class="fas fa-school text-5xl mb-4 opacity-10"></i>
                        <p>Belum ada data absensi untuk Unit manapun di tanggal ini.</p>
                    </td>
                </tr>`;
                return;
            }
            
            let html = '';
            classroomStats.forEach((cls, index) => {
                const rankClass = index < 3 ? 'bg-teal-500 text-white shadow-sm' : 'bg-gray-100 text-gray-600';
                const rateColor = cls.presence_rate > 90 ? 'from-green-400 to-emerald-500' : (cls.presence_rate > 70 ? 'from-blue-400 to-indigo-500' : 'from-orange-400 to-red-500');
                const schoolName = cls.school ? cls.school.name : '-';
                
                html += `<tr class="group hover:bg-gray-50 transition duration-150">
                    <td class="px-8 py-5">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg font-bold text-xs ${rankClass}">
                            ${index + 1}
                        </span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="font-bold text-gray-800 group-hover:text-teal-600 transition">${cls.school_name}</div>
                        <div class="text-[10px] text-gray-400">${schoolName}</div>
                    </td>
                    <td class="px-8 py-5 text-right font-bold text-indigo-600">${cls.z_days}</td>
                    <td class="px-8 py-5 text-right font-semibold text-gray-600">${cls.employees_count}</td>
                    <td class="px-8 py-5 text-right font-bold text-green-600">${Number(cls.total_hadir).toLocaleString('id-ID')}</td>
                    <td class="px-8 py-5 text-right">
                        <span class="px-2 py-1 rounded-md bg-indigo-50 text-indigo-700 font-bold text-xs">${cls.daily_present}</span>
                    </td>
                    <td class="px-8 py-5">
                        <div class="flex items-center justify-center gap-3">
                            <div class="flex-1 max-w-[100px] h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r ${rateColor}" style="width: ${cls.presence_rate}%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-800 min-w-[45px] text-right">${cls.presence_rate}%</span>
                        </div>
                    </td>
                </tr>`;
            });
            tbody.innerHTML = html;
        }

        async function fetchStatsData() {
            try {
                const separator = window.location.href.includes('?') ? '&' : '?';
                const response = await fetch(window.location.href + separator + 'json=1');
                if (!response.ok) return;
                const data = await response.json();

                // 1. Update Daily Stats
                const ds = data.dailyStats;
                const dailyTotal = ds.total_daily || 1;
                const hadirPercentage = dailyTotal > 0 ? Math.round((ds.hadir / dailyTotal) * 100 * 10) / 10 : 0;
                const terlambatPercentage = dailyTotal > 0 ? Math.round((ds.dinas_luar / dailyTotal) * 100 * 10) / 10 : 0;

                updateUIElement('live_daily_hadir', ds.hadir);
                updateUIElement('live_daily_hadir_percentage', hadirPercentage, true);
                updateUIElement('live_daily_dinas_luar', ds.dinas_luar);
                updateUIElement('live_daily_izin', ds.izin);
                updateUIElement('live_daily_sakit', ds.sakit);
                updateUIElement('live_daily_alpha', ds.alpha);

                // 2. Update Cumulative Stats
                const cs = data.cumulativeStats;
                const activeStudentsCount = data.classroomStats.reduce((acc, c) => acc + (c.employees_count || 0), 0) || 1;
                const cumulativeRate = cs.z > 0 ? Math.round((cs.hadir / (cs.z * activeStudentsCount)) * 100 * 10) / 10 : 0;

                updateUIElement('live_cumulative_rate', cumulativeRate, true);
                updateUIElement('live_cum_hadir', cs.hadir);
                updateUIElement('live_cum_izin', cs.izin);
                updateUIElement('live_cum_terlambat', cs.terlambat);
                updateUIElement('live_cum_sakit', cs.sakit);
                updateUIElement('live_cum_alpha', cs.alpha);

                // 3. Update Classroom Table
                renderClassrooms(data.classroomStats);

                // 4. Update Charts
                if (window.trendChart) {
                    const trendDates = Object.keys(data.chartData).sort();
                    const trHadir = trendDates.map(d => data.chartData[d].hadir || 0);
                    const trAbsen = trendDates.map(d => (data.chartData[d].izin || 0) + (data.chartData[d].sakit || 0) + (data.chartData[d].alpha || 0));

                    window.trendChart.data.labels = trendDates.map(d => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
                    window.trendChart.data.datasets[0].data = trHadir;
                    window.trendChart.data.datasets[1].data = trAbsen;
                    window.trendChart.update('none');
                }

                if (window.pieChart) {
                    window.pieChart.data.datasets[0].data = [cs.hadir, cs.terlambat, cs.izin, cs.sakit, cs.alpha];
                    window.pieChart.update('none');
                }

            } catch (error) {
                console.error('Failed to fetch statistics:', error);
            }
        }

        async function fetchLiveFeed() {
            try {
                const response = await fetch('/display/live-data?t=' + Date.now());
                if (!response.ok) return;
                const data = await response.json();
                const feedContainer = document.getElementById('live_feed_container');
                if (!feedContainer) return;

                let feed = data.feed || [];
                
                // Filter by school type if selected
                if (activeSchoolType !== '') {
                    feed = feed.filter(item => item.unit === activeSchoolType);
                }

                if (feed.length === 0) {
                    feedContainer.innerHTML = `<div class="flex flex-col items-center justify-center h-full text-gray-400 py-20">
                        <i class="fas fa-id-card text-4xl mb-3 opacity-20"></i>
                        <p class="text-xs">Belum ada aktivitas tap...</p>
                    </div>`;
                    return;
                }

                let html = '';
                feed.forEach(item => {
                    const badgeClass = item.tipe === 'pulang' ? 'bg-purple-50 text-purple-700 border-orange-100' : (item.tipe === 'terlambat' ? 'bg-yellow-50 text-yellow-700 border-yellow-100' : 'bg-green-50 text-green-700 border-green-100');
                    const viaLabel = item.recorded_via === 'qr_gps' ? 'GPS' : (item.recorded_via === 'qr' ? 'QR Code' : 'RFID');
                    const viaClass = item.recorded_via === 'qr_gps' ? 'bg-purple-50 text-purple-700' : (item.recorded_via === 'qr' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700');
                    
                    html += `<div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100/70 transition duration-150 animate-fade-in border border-gray-100 shadow-sm">
                        <img src="${item.foto}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0" alt="Avatar">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm text-gray-800 truncate">${item.nama}</div>
                            <div class="text-[10px] text-gray-500 flex items-center gap-2">
                                <span class="truncate">${item.info}</span>
                                <span>•</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold ${viaClass}">${viaLabel}</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded-full border text-[10px] font-bold ${badgeClass}">${item.aksi}</span>
                            <div class="text-[10px] text-gray-400 mt-1 font-semibold">${item.waktu}</div>
                        </div>
                    </div>`;
                });
                
                feedContainer.innerHTML = html;

            } catch (error) {
                console.error('Failed to fetch live feed:', error);
            }
        }

        // Initialize Polling
        if (selectedDate === todayStr) {
            badge.style.display = 'inline-flex';
            
            // Initial load
            fetchStatsData();
            fetchLiveFeed();
            
            // Poll Statistics every 5 seconds
            setInterval(fetchStatsData, 5000);
            
            // Poll Live Card Tap stream every 3 seconds
            setInterval(fetchLiveFeed, 3000);
        } else {
            badge.style.display = 'none';
            // Still populate live feed for that date if it's in the past (non-live, just static display)
            fetchLiveFeed();
        }
    });
</script>
@endpush
@endsection

