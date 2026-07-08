@extends('layouts.orangtua')
@section('title', 'Nilai '.$student->full_name.' - Portal Orang Tua')

@section('content')
<div class="space-y-6">
    {{-- Child Header --}}
    @include('orangtua.partials.child-header', ['student' => $student, 'classroom' => $classroom, 'active' => 'nilai'])

    {{-- Semester Filter --}}
    <div class="flex justify-end">
        <form method="GET">
            <input type="hidden" name="student" value="{{ $student->id }}">
            <select name="semester_id" onchange="this.form.submit()" class="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-teal-300">
                @foreach($semesters as $sem)
                    <option value="{{ $sem->id }}" {{ $selectedSemesterId == $sem->id ? 'selected' : '' }}>
                        {{ $sem->semester_name ?? 'Semester '.$sem->semester_number }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Analytics Dashboard Section --}}
    @if($subjectGrades->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Radar Chart: Peta Kekuatan Akademik --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-1">
                    <i class="fas fa-chart-pie text-teal-500"></i> Peta Kekuatan Akademik
                </h3>
                <p class="text-xs text-gray-500">Analisis rata-rata nilai kompetensi per mata pelajaran</p>
            </div>
            <div class="relative h-64 mt-4 flex items-center justify-center">
                <canvas id="radarChart"></canvas>
            </div>
        </div>

        {{-- Bar Chart: Capaian Nilai vs KKM --}}
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between lg:col-span-2">
            <div>
                <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-1">
                    <i class="fas fa-chart-bar text-teal-500"></i> Rata-rata Nilai vs KKM
                </h3>
                <p class="text-xs text-gray-500">Membandingkan pencapaian siswa dengan batas ketuntasan minimal (KKM)</p>
            </div>
            <div class="relative h-64 mt-4">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    @if($monthlyGrades->isNotEmpty())
    {{-- Line Chart: Tren Progress Nilai Bulanan --}}
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex flex-col justify-between">
        <div>
            <h3 class="font-bold text-gray-800 flex items-center gap-2 mb-1">
                <i class="fas fa-chart-line text-teal-500"></i> Tren Progress & Kemajuan Belajar
            </h3>
            <p class="text-xs text-gray-500">Perkembangan rata-rata nilai siswa dari bulan ke bulan sepanjang semester</p>
        </div>
        <div class="relative h-56 mt-4">
            <canvas id="lineChart"></canvas>
        </div>
    </div>
    @endif
    @endif

    {{-- Grades Table --}}
    @if($subjectGrades->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">📊 Nilai Per Mata Pelajaran</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold">Mata Pelajaran</th>
                            <th class="px-5 py-3 text-center font-semibold">Nilai</th>
                            <th class="px-5 py-3 text-center font-semibold">Tipe</th>
                            <th class="px-5 py-3 text-center font-semibold">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($subjectGrades as $sg)
                            @php $first = true; @endphp
                            @foreach($sg['grades'] as $grade)
                                <tr class="hover:bg-gray-50 transition">
                                    @if($first)
                                        <td class="px-5 py-3 font-medium text-gray-800" rowspan="{{ $sg['grades']->count() }}">
                                            {{ $sg['subject']->name ?? '-' }}
                                        </td>
                                    @endif
                                    <td class="px-5 py-3 text-center">
                                        <span class="inline-block px-2.5 py-1 rounded-lg font-bold text-sm {{ $grade->score >= 80 ? 'bg-green-100 text-green-700' : ($grade->score >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                            {{ number_format($grade->score, 0) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center text-xs text-gray-500 capitalize">
                                        {{ str_replace('_', ' ', $grade->grade_type ?? '-') }}
                                    </td>
                                    @if($first)
                                        <td class="px-5 py-3 text-center font-bold" rowspan="{{ $sg['grades']->count() }}">
                                            <span class="inline-block px-3 py-1 rounded-lg {{ $sg['average'] >= 80 ? 'bg-green-100 text-green-700' : ($sg['average'] >= 60 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                                {{ $sg['average'] }}
                                            </span>
                                        </td>
                                    @endif
                                    @php $first = false; @endphp
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <i class="fas fa-clipboard text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Belum ada nilai untuk semester ini.</p>
        </div>
    @endif

    {{-- Rapor Digital --}}
    @if($showReportCard && $reportCards->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mt-6">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-file-alt text-teal-500"></i> Rapor Digital
                </h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($reportCards as $rc)
                        <div class="border border-gray-200 rounded-xl p-4 hover:shadow-md hover:border-teal-200 transition-all">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-bold text-gray-800">{{ $rc->semester->semester_name ?? 'Semester' }}</span>
                                <span class="text-xs bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded-full font-medium">
                                    <i class="fas fa-check-circle mr-0.5"></i>Published
                                </span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">
                                {{ $rc->academicYear->year ?? '' }} · {{ $rc->classroom->class_name ?? '' }}
                            </p>
                            <div class="grid grid-cols-3 gap-2 text-center mb-3">
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500">Rata-rata</p>
                                    <p class="font-bold text-teal-600">{{ number_format($rc->average_score, 1) }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500">Peringkat</p>
                                    <p class="font-bold text-blue-600">#{{ $rc->rank }}</p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-2">
                                    <p class="text-xs text-gray-500">Predikat</p>
                                    <p class="font-bold text-emerald-600">{{ $rc->predicate ?? '-' }}</p>
                                </div>
                            </div>
                            <a href="{{ route('orangtua.anak.raport.download', [$student->id, $rc->id]) }}" target="_blank"
                               class="block text-center bg-gradient-to-r from-teal-500 to-emerald-500 hover:from-teal-600 hover:to-emerald-600 text-white text-sm font-medium py-2.5 rounded-xl shadow-sm transition-all">
                                <i class="fas fa-download mr-1"></i> Download Rapor
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Radar Chart
        const ctxRadar = document.getElementById('radarChart').getContext('2d');
        new Chart(ctxRadar, {
            type: 'radar',
            data: {
                labels: @json($chartSubjects),
                datasets: [{
                    label: 'Rata-rata Nilai',
                    data: @json($chartAverages),
                    backgroundColor: 'rgba(20, 184, 166, 0.2)',
                    borderColor: 'rgba(20, 184, 166, 1)',
                    borderWidth: 2,
                    pointBackgroundColor: 'rgba(20, 184, 166, 1)',
                    pointBorderColor: '#fff',
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: 'rgba(20, 184, 166, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    r: {
                        angleLines: { display: true },
                        suggestedMin: 0,
                        suggestedMax: 100,
                        ticks: { stepSize: 20 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });

        // Bar Chart
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: @json($chartSubjects),
                datasets: [
                    {
                        label: 'Nilai Rata-rata',
                        data: @json($chartAverages),
                        backgroundColor: 'rgba(59, 130, 246, 0.85)',
                        borderRadius: 6,
                    },
                    {
                        label: 'KKM',
                        data: @json($chartKkms),
                        backgroundColor: 'rgba(239, 68, 68, 0.4)',
                        borderRadius: 6,
                        type: 'line',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 2,
                        fill: false,
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: { stepSize: 20 }
                    }
                },
                plugins: {
                    legend: { position: 'top' }
                }
            }
        });

        @if($monthlyGrades->isNotEmpty())
        // Line Chart
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: @json($monthlyGrades->pluck('label')),
                datasets: [{
                    label: 'Rata-rata Nilai Bulanan',
                    data: @json($monthlyGrades->pluck('avg')),
                    borderColor: 'rgba(20, 184, 166, 1)',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.3,
                    pointRadius: 5,
                    pointBackgroundColor: 'rgba(20, 184, 166, 1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        suggestedMin: 50,
                        suggestedMax: 100,
                        ticks: { stepSize: 10 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
        @endif
    });
</script>
@endpush
</div>
@endsection
