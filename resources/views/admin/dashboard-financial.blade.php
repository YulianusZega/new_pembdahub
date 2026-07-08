@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Dashboard SuperAdmin</h1>
        <p class="text-gray-600 mt-2">Selamat datang kembali, {{ auth()->user()->name }}! <i class="fas fa-hand-wave mr-1"></i></p>
        @if($currentAcademicYear)
        <p class="text-sm text-gray-500 mt-1">Tahun Ajaran: {{ $currentAcademicYear->year }}</p>
        @endif
    </div>

    <!-- Financial Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm mb-1">Total Siswa</p>
                    <p class="text-3xl font-bold">{{ number_format($totalStudents) }}</p>
                </div>
                <div class="bg-blue-400 bg-opacity-30 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm mb-1">Total Tagihan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalBills, 0, ',', '.') }}</p>
                </div>
                <div class="bg-purple-400 bg-opacity-30 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/>
                        <path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm mb-1">Terbayar</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                </div>
                <div class="bg-green-400 bg-opacity-30 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm mb-1">Tunggakan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($totalOutstanding, 0, ',', '.') }}</p>
                </div>
                <div class="bg-red-400 bg-opacity-30 rounded-xl p-3">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Monthly Collection Chart -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Grafik Pembayaran Bulanan</h3>
            <canvas id="monthlyCollectionChart" height="250"></canvas>
        </div>

        <!-- Overdue Trends Chart -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Trend Tunggakan Lewat Tempo</h3>
            <canvas id="overdueChart" height="250"></canvas>
        </div>
    </div>

    <!-- Second Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Payment Methods Distribution -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Metode Pembayaran</h3>
            <canvas id="paymentMethodsChart" height="250"></canvas>
        </div>

        <!-- Payment by Type -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran per Jenis</h3>
            <canvas id="paymentByTypeChart" height="250"></canvas>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Top Outstanding Students -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Top 10 Tunggakan Terbesar</h3>
            <div class="overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Siswa</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Tunggakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($topOutstanding as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $item->student->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $item->student->nisn }}</p>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-red-600">
                                Rp {{ number_format($item->outstanding, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Pembayaran Terakhir</h3>
            <div class="overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600">Siswa</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentPayments as $payment)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="text-sm font-medium text-gray-900">{{ $payment->student->full_name }}</p>
                                <p class="text-xs text-gray-500">{{ $payment->payment_date->format('d M Y, H:i') }}</p>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold text-green-600">
                                Rp {{ number_format($payment->amount_paid, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-4 py-8 text-center text-gray-500">Tidak ada data</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Collection Chart
const monthlyCtx = document.getElementById('monthlyCollectionChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(collect($monthlyData)->pluck('month')) !!},
        datasets: [{
            label: 'Pembayaran (Rp)',
            data: {!! json_encode(collect($monthlyData)->pluck('amount')) !!},
            borderColor: 'rgb(34, 197, 94)',
            backgroundColor: 'rgba(34, 197, 94, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Overdue Trends Chart
const overdueCtx = document.getElementById('overdueChart').getContext('2d');
new Chart(overdueCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($overdueByMonth)->pluck('month')) !!},
        datasets: [{
            label: 'Tagihan Lewat Tempo',
            data: {!! json_encode(collect($overdueByMonth)->pluck('count')) !!},
            backgroundColor: 'rgba(239, 68, 68, 0.8)',
            borderColor: 'rgb(239, 68, 68)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Payment Methods Chart
const methodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
new Chart(methodsCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($paymentMethods->pluck('payment_method')->map(function($m) {
            $methods = ['cash' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS', 'card' => 'Kartu'];
            return $methods[$m] ?? $m;
        })) !!},
        datasets: [{
            data: {!! json_encode($paymentMethods->pluck('total')) !!},
            backgroundColor: [
                'rgba(59, 130, 246, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(168, 85, 247, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Payment by Type Chart
const typeCtx = document.getElementById('paymentByTypeChart').getContext('2d');
new Chart(typeCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($paymentByType->pluck('paymentType.type_name')) !!},
        datasets: [{
            data: {!! json_encode($paymentByType->pluck('total')) !!},
            backgroundColor: [
                'rgba(239, 68, 68, 0.8)',
                'rgba(251, 191, 36, 0.8)',
                'rgba(34, 197, 94, 0.8)',
                'rgba(59, 130, 246, 0.8)',
                'rgba(168, 85, 247, 0.8)',
                'rgba(236, 72, 153, 0.8)'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endsection
