@extends('layouts.guru')
@section('title', 'Detail Rapor - ' . $reportCard->student->full_name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-alt text-rose-500"></i> Detail Rapor
            </h1>
            <p class="text-sm text-gray-500 mt-0.5">
                {{ $reportCard->student->full_name }} · {{ $reportCard->semester->semester_name ?? '' }}
            </p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('guru.raport.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('guru.raport.print', $reportCard) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-xl text-sm font-semibold transition">
                <i class="fas fa-file-pdf"></i> Download PDF
            </a>
            @if($reportCard->isEditable())
                <a href="{{ route('guru.raport.edit', $reportCard) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-xl text-sm font-semibold transition">
                    <i class="fas fa-edit"></i> Edit Catatan
                </a>
            @endif
            @if($reportCard->status === 'draft')
                <form method="POST" action="{{ route('guru.raport.finalize', $reportCard) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-xl text-sm font-semibold transition" onclick="return confirm('Finalize rapor ini?')">
                        <i class="fas fa-check"></i> Finalize
                    </button>
                </form>
            @endif
            @if($reportCard->status === 'finalized')
                <form method="POST" action="{{ route('guru.raport.publish', $reportCard) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-xl text-sm font-semibold transition" onclick="return confirm('Publish rapor ini?')">
                        <i class="fas fa-paper-plane"></i> Publish
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left: Student Info --}}
        <div class="space-y-5">
            {{-- Student Card --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3 border-gray-100">
                    <i class="fas fa-user text-rose-500"></i> Informasi Siswa
                </h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nama</dt>
                        <dd class="font-medium text-gray-800">{{ $reportCard->student->full_name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">NISN</dt>
                        <dd class="font-mono text-gray-800">{{ $reportCard->student->nisn ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Kelas</dt>
                        <dd class="font-medium text-gray-800">{{ $reportCard->classroom->class_name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Semester</dt>
                        <dd class="text-gray-800">{{ $reportCard->semester->semester_name ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Tahun Ajaran</dt>
                        <dd class="text-gray-800">{{ $reportCard->academicYear->year ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Score Summary --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 text-center">
                <p class="text-5xl font-bold {{ $reportCard->average_score >= 80 ? 'text-green-500' : ($reportCard->average_score >= 70 ? 'text-yellow-500' : 'text-red-500') }}">
                    {{ number_format($reportCard->average_score, 1) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Rata-rata</p>
                <div class="flex justify-center gap-4 mt-4">
                    <div>
                        <span class="inline-block px-4 py-2 rounded-full text-lg font-bold {{ $reportCard->predicate == 'A' ? 'bg-green-100 text-green-700' : ($reportCard->predicate == 'B' ? 'bg-blue-100 text-blue-700' : ($reportCard->predicate == 'C' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                            {{ $reportCard->predicate }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Predikat</p>
                    </div>
                    <div>
                        <span class="inline-block px-4 py-2 rounded-full text-lg font-bold bg-indigo-100 text-indigo-700">
                            #{{ $reportCard->rank ?? '-' }}
                        </span>
                        <p class="text-xs text-gray-500 mt-1">Peringkat</p>
                    </div>
                </div>
                <div class="mt-3">
                    @php
                        $statusColors = [
                            'draft' => 'bg-yellow-100 text-yellow-700',
                            'finalized' => 'bg-blue-100 text-blue-700',
                            'published' => 'bg-green-100 text-green-700',
                        ];
                    @endphp
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$reportCard->status] ?? '' }}">
                        {{ ucfirst($reportCard->status) }}
                    </span>
                </div>
            </div>

            {{-- Attendance --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2 border-b pb-3 border-gray-100">
                    <i class="fas fa-calendar-check text-rose-500"></i> Kehadiran
                </h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    <div class="bg-green-50 rounded-lg p-3 text-center">
                        <p class="font-bold text-green-700 text-lg">{{ $reportCard->days_present ?? 0 }}</p>
                        <p class="text-xs text-green-600">Hadir</p>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3 text-center">
                        <p class="font-bold text-yellow-700 text-lg">{{ $reportCard->days_sick ?? 0 }}</p>
                        <p class="text-xs text-yellow-600">Sakit</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-3 text-center">
                        <p class="font-bold text-blue-700 text-lg">{{ $reportCard->days_permission ?? 0 }}</p>
                        <p class="text-xs text-blue-600">Izin</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-3 text-center">
                        <p class="font-bold text-red-700 text-lg">{{ $reportCard->days_absent ?? 0 }}</p>
                        <p class="text-xs text-red-600">Alpha</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Grades & Notes --}}
        <div class="lg:col-span-2 space-y-5">
            {{-- Subject Grades --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-chart-bar text-rose-500"></i> Nilai Per Mata Pelajaran
                    </h3>
                </div>
                @if(count($subjectScores) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold">Mata Pelajaran</th>
                                    <th class="px-3 py-3 text-center font-semibold">KKM</th>
                                    <th class="px-3 py-3 text-center font-semibold">Tugas</th>
                                    <th class="px-3 py-3 text-center font-semibold">UTS</th>
                                    <th class="px-3 py-3 text-center font-semibold">UAS</th>
                                    <th class="px-3 py-3 text-center font-semibold">Sikap</th>
                                    <th class="px-3 py-3 text-center font-semibold">NA</th>
                                    <th class="px-3 py-3 text-center font-semibold">Predikat</th>
                                    <th class="px-3 py-3 text-center font-semibold">Ket</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($subjectScores as $score)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 font-medium text-gray-800">{{ $score['subject'] }}</td>
                                        <td class="px-3 py-3 text-center text-gray-500">{{ $score['kkm'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $score['tugas'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $score['uts'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $score['uas'] }}</td>
                                        <td class="px-3 py-3 text-center">{{ $score['sikap'] }}</td>
                                        <td class="px-3 py-3 text-center font-bold {{ $score['is_passed'] ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $score['final'] }}
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold {{ $score['predicate'] == 'A' ? 'bg-green-100 text-green-700' : ($score['predicate'] == 'B' ? 'bg-blue-100 text-blue-700' : ($score['predicate'] == 'C' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) }}">
                                                {{ $score['predicate'] }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if($score['is_passed'])
                                                <span class="text-green-500"><i class="fas fa-check-circle"></i></span>
                                            @else
                                                <span class="text-red-500"><i class="fas fa-times-circle"></i></span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-8 text-center text-gray-400">
                        <p class="text-sm">Belum ada data nilai.</p>
                    </div>
                @endif
            </div>

            {{-- Notes --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3 border-gray-100">
                    <i class="fas fa-sticky-note text-rose-500"></i> Catatan
                </h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-1">Catatan Wali Kelas</p>
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 min-h-[60px]">
                            {{ $reportCard->teacher_notes ?? 'Belum ada catatan.' }}
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-1">Catatan Kepala Sekolah</p>
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-700 min-h-[60px]">
                            {{ $reportCard->principal_notes ?? 'Belum ada catatan.' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Achievements --}}
            @if($achievements->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2 border-b pb-3 border-gray-100">
                        <i class="fas fa-trophy text-rose-500"></i> Prestasi
                    </h3>
                    <div class="space-y-3">
                        @foreach($achievements as $ach)
                            <div class="flex items-start gap-3 p-3 bg-yellow-50 rounded-lg">
                                <div class="w-8 h-8 bg-yellow-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-medal text-yellow-600 text-sm"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-800 text-sm">{{ $ach->title }}</p>
                                    <p class="text-xs text-gray-500">
                                        {{ ucfirst($ach->level ?? '') }}
                                        @if($ach->achievement_date) · {{ \Carbon\Carbon::parse($ach->achievement_date)->translatedFormat('d M Y') }} @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
