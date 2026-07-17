@extends('layouts.admin')

@section('title', 'Monitoring Absensi Pegawai - Admin')

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
            <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                <i class="fas fa-fingerprint text-white text-3xl"></i>
            </div>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-3xl font-bold text-gray-800">Monitoring Absensi Pegawai</h1>
                    <span id="live_status_badge" style="display: none;" class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-full border border-green-200 shadow-sm">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></span>
                        LIVE
                    </span>
                </div>
                <p class="text-gray-600">Analisis real-time kehadiran pegawai tingkat sekolah</p>
            </div>
        </div>

        <form method="GET" class="flex flex-wrap items-center gap-3">
            @if(\)
            <div class="min-w-[200px]">
                <select name="school_id" onchange="this.form.submit()" class="w-full border-gray-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 shadow-sm text-sm">
                    <option value="">Semua Sekolah</option>
                    @foreach(\ as \)
                        <option value="{{ \->id }}" {{ \ == \->id ? 'selected' : '' }}>{{ \->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div class="flex items-center bg-white rounded-xl shadow-sm border border-gray-200 px-3 py-1.5">
                <i class="fas fa-calendar-alt text-gray-400 mr-2 text-sm"></i>
                <input type="date" name="date" value="{{ \ }}" onchange="this.form.submit()" class="border-none focus:ring-0 text-sm p-0 text-gray-700 font-semibold">
            </div>
            <a href="{{ route('admin.employees.attendance.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all">Kembali</a>
        </form>
    </div>

    <!-- Cumulative Overview Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 rounded-2xl p-8 shadow-xl text-white mb-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 -m-12 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="max-w-xl">
                <h2 class="text-2xl font-bold mb-2 flex items-center gap-3">
                    <i class="fas fa-chart-pie text-indigo-300"></i> Rekap Kehadiran Kumulatif (Bulan Ini)
                </h2>
                <p class="text-indigo-50 text-sm leading-relaxed opacity-90">
                    Berdasarkan formula: <span class="font-mono bg-black/20 px-2 py-0.5 rounded">(Hadir / Hari Kerja) * 100%</span>. 
                    Dimana <span class="font-bold">Hari Kerja</span> dihitung sejak tanggal 1 awal bulan sampai hari ini (tanpa Sabtu/Minggu).
                </p>
                <div class="mt-4 flex flex-wrap gap-4 text-xs font-bold uppercase tracking-wider">
                    <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg border border-gray-100">
                        <i class="fas fa-calendar-day text-indigo-300"></i> Total Hari Kerja: {{ \['z'] }} Hari
                    </span>
                    <span class="flex items-center gap-1.5 bg-white/10 px-3 py-1.5 rounded-lg border border-gray-100">
                        <i class="fas fa-users text-indigo-300"></i> Total Pegawai Aktif: {{ \['active_employees'] }} Orang
                    </span>
                </div>
            </div>
            <div class="flex items-end gap-1">
                <div class="text-right">
                    <p class="text-xs font-bold text-indigo-200 uppercase tracking-widest mb-1">Total Persentase</p>
                    <h3 class="text-6xl font-bold leading-none">
                        <span id="live_cumulative_rate" class="transition-all duration-300">{{ \['z'] > 0 ? round((\['hadir'] / (\['z'] * (\['active_employees'] ?: 1))) * 100, 1) : 0 }}</span>%
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
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_hadir">{{ number_format(\['hadir']) }}</h3>
                    <span class="text-xs font-bold text-green-600 mb-1.5 transition-all duration-300" id="live_daily_hadir_percentage">{{ \['total_daily'] > 0 ? round((\['hadir'] / \['total_daily']) * 100, 1) : 0 }}%</span>
                </div>
            </div>
        </div>

        <!-- Dinas Luar -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-purple-50 rounded-full group-hover:scale-110 transition duration-500 opacity-50"></div>
            <div class="relative">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-4">
                    <i class="fas fa-car"></i>
                </div>
                <p class="text-gray-500 text-[11px] font-bold uppercase tracking-wider">Dinas Luar Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_dinas_luar">{{ number_format(\['dinas_luar']) }}</h3>
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
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_izin">{{ number_format(\['izin']) }}</h3>
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
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_sakit">{{ number_format(\['sakit']) }}</h3>
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
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Alpha Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_alpha">{{ number_format(\['alpha']) }}</h3>
                </div>
            </div>
        </div>
        
        <!-- Cuti -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition group overflow-hidden relative">
            <div class="absolute -right-4 -top-4 w-20 h-20 bg-teal-50 rounded-full group-hover:scale-110 transition duration-500 opacity-50"></div>
            <div class="relative">
                <div class="w-10 h-10 bg-teal-100 rounded-xl flex items-center justify-center text-teal-600 mb-4">
                    <i class="fas fa-plane-departure"></i>
                </div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Cuti Hari Ini</p>
                <div class="flex items-end gap-2 mt-1">
                    <h3 class="text-2xl font-bold text-gray-800 transition-all duration-300" id="live_daily_cuti">{{ number_format(\['cuti']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Live Feed Activity -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col h-[600px]">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-gray-800">Live Tap Activity</h3>
                    <p class="text-xs text-gray-500 mt-1">Aktivitas absen masuk/pulang hari ini</p>
                </div>
                <div class="flex gap-1" id="feed_filters">
                    <button class="px-3 py-1 rounded-full text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm" data-filter="">Semua</button>
                    @if(\)
                        <button class="px-3 py-1 rounded-full text-xs font-bold transition-all bg-white text-gray-600 hover:bg-gray-100 border border-gray-200" data-filter="smp">SMP</button>
                        <button class="px-3 py-1 rounded-full text-xs font-bold transition-all bg-white text-gray-600 hover:bg-gray-100 border border-gray-200" data-filter="sma">SMA</button>
                        <button class="px-3 py-1 rounded-full text-xs font-bold transition-all bg-white text-gray-600 hover:bg-gray-100 border border-gray-200" data-filter="smk">SMK</button>
                    @endif
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3" id="live_feed_container">
                <!-- Data will be populated by JS -->
                <div class="flex items-center justify-center h-full text-gray-400">
                    <i class="fas fa-spinner fa-spin mr-2"></i> Memuat live feed...
                </div>
            </div>
        </div>

        <!-- Charts & Tables -->
        <div class="lg:col-span-2 space-y-6 flex flex-col h-[600px]">
            <!-- Unit Status Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex-1 overflow-hidden flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-gray-800">Rekap Unit Sekolah Hari Ini</h3>
                    <a href="{{ route('admin.employees.attendance.rekap') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">Lihat Rekap Lengkap &rarr;</a>
                </div>
                <div class="overflow-y-auto flex-1 pr-2">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600 rounded-l-xl">Unit</th>
                                <th class="px-4 py-3 text-center font-semibold text-gray-600">Total Pegawai</th>
                                <th class="px-4 py-3 text-right font-semibold text-gray-600 rounded-r-xl">Persentase Hadir</th>
                            </tr>
                        </thead>
                        <tbody id="unit_table_body" class="divide-y divide-gray-50">
                            @foreach(\ as \)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-4 py-4">
                                    <div class="font-bold text-gray-800">{{ \['school_name'] }}</div>
                                </td>
                                <td class="px-4 py-4 text-center font-medium text-gray-600">{{ \['employees_count'] }}</td>
                                <td class="px-4 py-4">
                                    <div class="flex items-center justify-end gap-3">
                                        <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                                            @php
                                                \ = \['presence_rate'];
                                                \ = \ >= 90 ? 'from-green-400 to-green-500' : (\ >= 75 ? 'from-yellow-400 to-yellow-500' : 'from-red-400 to-red-500');
                                            @endphp
                                            <div class="h-full bg-gradient-to-r {{ \ }}" style="width: {{ \ }}%"></div>
                                        </div>
                                        <span class="text-sm font-bold text-gray-800 min-w-[45px] text-right">{{ \ }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex-1 flex flex-col min-h-[300px]">
                <h3 class="font-bold text-gray-800 mb-4">Trend Kehadiran Pegawai (14 Hari Terakhir)</h3>
                <div class="relative w-full h-full min-h-[200px] flex-1">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const selectedDate = urlParams.get('date') || new Date().toISOString().split('T')[0];
        
        // Define today in local timezone
        const now = new Date();
        const offset = now.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(now - offset)).toISOString().split('T')[0];
        const todayStr = localISOTime;
        
        const badge = document.getElementById('live_status_badge');
        
        // Chart Data
        const chartRawData = @json(\);
        const dates = Object.keys(chartRawData).sort();
        const hadirData = dates.map(d => chartRawData[d].hadir || 0);
        const absenData = dates.map(d => (chartRawData[d].izin || 0) + (chartRawData[d].sakit || 0) + (chartRawData[d].alpha || 0) + (chartRawData[d].cuti || 0) + (chartRawData[d].dinas_luar || 0));
        
        const labels = dates.map(d => {
            const dt = new Date(d);
            return dt.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });

        // Initialize Trend Chart
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        window.trendChart = new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Hadir',
                        data: hadirData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
                    },
                    {
                        label: 'Tidak Hadir (Sakit/Izin/Alpha/Cuti/DL)',
                        data: absenData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.05)',
                        borderWidth: 2,
                        borderDash: [5, 5],
                        tension: 0.4,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, boxWidth: 6, font: { family: "'Inter', sans-serif", size: 11 } }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' }, border: { display: false } },
                    x: { grid: { display: false }, border: { display: false } }
                }
            }
        });

        // Live Feed Filtering
        let activeSchoolType = '';
        const filterBtns = document.querySelectorAll('#feed_filters button');
        filterBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                filterBtns.forEach(b => {
                    b.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                    b.classList.add('bg-white', 'text-gray-600');
                });
                e.target.classList.remove('bg-white', 'text-gray-600');
                e.target.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                activeSchoolType = e.target.dataset.filter;
                fetchLiveFeed(); // Refetch/render immediately
            });
        });

        function updateUIElement(id, value, isPercentage = false) {
            const el = document.getElementById(id);
            if (el) {
                const currentVal = el.innerText.replace('%', '').replace(/,/g, '');
                if (currentVal !== value.toString()) {
                    el.style.transform = 'scale(1.1)';
                    el.style.color = '#4f46e5';
                    setTimeout(() => {
                        el.innerText = isPercentage ? value + '%' : new Intl.NumberFormat('id-ID').format(value);
                        el.style.transform = 'scale(1)';
                        el.style.color = '';
                    }, 150);
                }
            }
        }

        function renderUnitTable(unitStats) {
            const tbody = document.getElementById('unit_table_body');
            if (!tbody) return;
            
            let html = '';
            unitStats.forEach(unit => {
                const rate = unit.presence_rate;
                const rateColor = rate >= 90 ? 'from-green-400 to-green-500' : (rate >= 75 ? 'from-yellow-400 to-yellow-500' : 'from-red-400 to-red-500');
                
                html += \
                <tr class="hover:bg-gray-50/50 transition">
                    <td class="px-4 py-4">
                        <div class="font-bold text-gray-800">\</div>
                    </td>
                    <td class="px-4 py-4 text-center font-medium text-gray-600">\</td>
                    <td class="px-4 py-4">
                        <div class="flex items-center justify-end gap-3">
                            <div class="w-32 h-2 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r \" style="width: \%"></div>
                            </div>
                            <span class="text-sm font-bold text-gray-800 min-w-[45px] text-right">\%</span>
                        </div>
                    </td>
                </tr>\;
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

                updateUIElement('live_daily_hadir', ds.hadir);
                updateUIElement('live_daily_hadir_percentage', hadirPercentage, true);
                updateUIElement('live_daily_dinas_luar', ds.dinas_luar);
                updateUIElement('live_daily_izin', ds.izin);
                updateUIElement('live_daily_sakit', ds.sakit);
                updateUIElement('live_daily_alpha', ds.alpha);
                updateUIElement('live_daily_cuti', ds.cuti);

                // 2. Update Cumulative Stats
                const cs = data.cumulativeStats;
                const activeEmployeesCount = cs.active_employees || 1;
                const cumulativeRate = cs.z > 0 ? Math.round((cs.hadir / (cs.z * activeEmployeesCount)) * 100 * 10) / 10 : 0;

                updateUIElement('live_cumulative_rate', cumulativeRate, true);

                // 3. Update Unit Table
                renderUnitTable(data.unitStats);

                // 4. Update Charts
                if (window.trendChart) {
                    const trendDates = Object.keys(data.chartData).sort();
                    const trHadir = trendDates.map(d => data.chartData[d].hadir || 0);
                    const trAbsen = trendDates.map(d => (data.chartData[d].izin || 0) + (data.chartData[d].sakit || 0) + (data.chartData[d].alpha || 0) + (data.chartData[d].dinas_luar || 0) + (data.chartData[d].cuti || 0));

                    window.trendChart.data.labels = trendDates.map(d => new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }));
                    window.trendChart.data.datasets[0].data = trHadir;
                    window.trendChart.data.datasets[1].data = trAbsen;
                    window.trendChart.update('none');
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
                
                // FILTER HANYA PEGAWAI
                feed = feed.filter(item => item.kategori === 'pegawai');

                // Filter by school type if selected
                if (activeSchoolType !== '') {
                    feed = feed.filter(item => item.unit === activeSchoolType);
                }

                if (feed.length === 0) {
                    feedContainer.innerHTML = \<div class="flex flex-col items-center justify-center h-full text-gray-400 py-20">
                        <i class="fas fa-id-card text-4xl mb-3 opacity-20"></i>
                        <p class="text-xs">Belum ada aktivitas tap pegawai...</p>
                    </div>\;
                    return;
                }

                let html = '';
                feed.forEach(item => {
                    const badgeClass = item.tipe === 'pulang' ? 'bg-orange-50 text-orange-700 border-orange-100' : 'bg-green-50 text-green-700 border-green-100';
                    const viaLabel = item.recorded_via === 'qr_gps' ? 'GPS' : (item.recorded_via === 'qr' ? 'QR Code' : 'RFID');
                    const viaClass = item.recorded_via === 'qr_gps' ? 'bg-purple-50 text-purple-700' : (item.recorded_via === 'qr' ? 'bg-indigo-50 text-indigo-700' : 'bg-blue-50 text-blue-700');
                    
                    html += \<div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl hover:bg-gray-100/70 transition duration-150 animate-fade-in border border-gray-100 shadow-sm">
                        <img src="\" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm flex-shrink-0" alt="Avatar">
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-sm text-gray-800 truncate">\</div>
                            <div class="text-[10px] text-gray-500 flex items-center gap-2">
                                <span class="truncate font-semibold">\</span>
                                <span>•</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold \">\</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <span class="inline-block px-2 py-0.5 rounded-full border text-[10px] font-bold \">\</span>
                            <div class="text-[10px] text-gray-400 mt-1 font-semibold">\</div>
                        </div>
                    </div>\;
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
