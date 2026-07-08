@extends('layouts.admin')

@section('title', 'Dashboard Kepegawaian')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                <i class="fas fa-chart-pie text-2xl text-white"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Dashboard Kepegawaian</h1>
                <p class="text-gray-600 mt-1">Ringkasan data SDM Perguruan PEMBDA Nias</p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5">
        <div class="bg-white rounded-2xl shadow-sm border border-indigo-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Pegawai</p>
                    <p class="text-4xl font-bold text-indigo-600 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-400 to-purple-500 flex items-center justify-center"><i class="fas fa-users text-white text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Guru / Pengajar</p>
                    <p class="text-4xl font-bold text-emerald-600 mt-2">{{ $stats['guru'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center"><i class="fas fa-chalkboard-teacher text-white text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">TU / Staff</p>
                    <p class="text-4xl font-bold text-blue-600 mt-2">{{ $stats['staff'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-400 to-sky-500 flex items-center justify-center"><i class="fas fa-briefcase text-white text-xl"></i></div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-violet-100 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Staf Yayasan</p>
                    <p class="text-4xl font-bold text-violet-600 mt-2">{{ $stats['yayasan'] }}</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center"><i class="fas fa-building text-white text-xl"></i></div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Distribution Charts -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Per School Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-school mr-2"></i>Pegawai Per Unit</h3>
                <div class="space-y-3">
                    @foreach($schools as $school)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $school->isYayasan() ? 'from-violet-400 to-purple-500' : 'from-blue-400 to-indigo-500' }} flex items-center justify-center text-white">
                                <i class="fas {{ $school->isYayasan() ? 'fa-building' : 'fa-school' }} text-sm"></i>
                            </div>
                            <div>
                                <span class="font-semibold text-gray-800">{{ $school->name }}</span>
                                @if($school->isYayasan()) <span class="ml-2 px-2 py-0.5 bg-violet-100 text-violet-700 text-xs font-bold rounded-full">YAYASAN</span> @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-2xl font-bold text-gray-800">{{ $school->employee_count }}</span>
                            <span class="text-xs text-gray-400">orang</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Gender & Status Distribution -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-venus-mars mr-2"></i>Gender</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-blue-500"></span> Laki-laki</span>
                            <span class="font-bold text-gray-800">{{ $genderDist['L'] ?? 0 }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3">
                            @php $totalGender = ($genderDist['L'] ?? 0) + ($genderDist['P'] ?? 0); $pctL = $totalGender > 0 ? round(($genderDist['L'] ?? 0) / $totalGender * 100) : 0; @endphp
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-3 rounded-full transition-all" style="width: {{ $pctL }}%"></div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="flex items-center gap-2"><span class="w-3 h-3 rounded-full bg-pink-500"></span> Perempuan</span>
                            <span class="font-bold text-gray-800">{{ $genderDist['P'] ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                    <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-id-badge mr-2"></i>Status Kepegawaian</h3>
                    <div class="space-y-2">
                        @php $statusLabels = ['tetap_yayasan' => 'Tetap Yayasan', 'honorer' => 'Honorer', 'kontrak' => 'Kontrak', 'pns' => 'PNS']; @endphp
                        @foreach($statusDist as $status => $count)
                        <div class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-50">
                            <span class="text-sm text-gray-700">{{ $statusLabels[$status] ?? ucfirst($status) }}</span>
                            <span class="font-bold text-gray-800">{{ $count }}</span>
                        </div>
                        @endforeach
                        @if($statusDist->isEmpty())
                        <p class="text-sm text-gray-400">Belum ada data</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Education Distribution -->
            @if($educationDist->count())
            <div class="bg-white rounded-2xl shadow-sm border border-blue-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-graduation-cap mr-2"></i>Distribusi Pendidikan</h3>
                <div class="flex flex-wrap gap-3">
                    @php $eduColors = ['S3' => 'violet', 'S2' => 'purple', 'S1' => 'indigo', 'D4' => 'blue', 'D3' => 'sky', 'D2' => 'cyan', 'D1' => 'teal', 'SMA' => 'emerald', 'SMP' => 'green', 'SD' => 'lime']; @endphp
                    @foreach($educationDist as $level => $count)
                    @php $ec = $eduColors[$level] ?? 'gray'; @endphp
                    <div class="flex items-center gap-2 px-4 py-3 bg-{{ $ec }}-50 border border-{{ $ec }}-200 rounded-xl">
                        <span class="text-lg font-bold text-{{ $ec }}-700">{{ $count }}</span>
                        <span class="text-sm text-{{ $ec }}-600 font-medium">{{ $level }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right: Alerts -->
        <div class="space-y-6">
            <!-- On Leave Today -->
            <div class="bg-white rounded-2xl shadow-sm border border-yellow-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-umbrella-beach mr-2 text-yellow-500"></i>Cuti Hari Ini</h3>
                @forelse($onLeaveToday as $leave)
                <div class="flex items-center gap-3 p-3 bg-yellow-50 rounded-xl mb-2">
                    <div class="w-8 h-8 rounded-lg bg-yellow-200 flex items-center justify-center text-yellow-700 font-bold text-sm">
                        {{ strtoupper(substr($leave->employee->full_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 text-sm truncate">{{ $leave->employee->full_name }}</div>
                        <div class="text-xs text-gray-500">{{ $leave->leave_type_label }} · {{ $leave->employee->school->name ?? '' }}</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-sun text-3xl text-yellow-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada pegawai cuti hari ini</p>
                </div>
                @endforelse
            </div>

            <!-- Expiring Contracts -->
            <div class="bg-white rounded-2xl shadow-sm border border-red-100 p-6">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4"><i class="fas fa-exclamation-triangle mr-2 text-red-500"></i>Kontrak Akan Berakhir</h3>
                @forelse($expiringContracts as $contract)
                <div class="flex items-center gap-3 p-3 bg-red-50 rounded-xl mb-2">
                    <div class="w-8 h-8 rounded-lg bg-red-200 flex items-center justify-center text-red-700 font-bold text-sm">
                        {{ strtoupper(substr($contract->employee->full_name, 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 text-sm truncate">{{ $contract->employee->full_name }}</div>
                        <div class="text-xs text-red-600">Berakhir {{ $contract->end_date->format('d M Y') }} ({{ $contract->end_date->diffInDays(now()) }} hari lagi)</div>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="fas fa-shield-check text-3xl text-green-300 mb-2"></i>
                    <p class="text-sm text-gray-500">Tidak ada kontrak yang akan berakhir</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
