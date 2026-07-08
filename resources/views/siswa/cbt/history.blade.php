@extends('layouts.siswa')
@section('title', 'Riwayat Ujian CBT')
@section('content')
<div class="space-y-8">
    {{-- Hero Header --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-amber-500 via-orange-500 to-rose-600 rounded-2xl p-8 text-white">
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-12 -ml-12 w-48 h-48 bg-white/10 rounded-full blur-2xl"></div>
        <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-5">
                <a href="{{ route('siswa.cbt.index') }}" class="w-12 h-12 rounded-xl bg-white/15 flex items-center justify-center hover:bg-white/25 transition border border-gray-100">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white tracking-tight">Riwayat Ujian</h1>
                    <p class="text-amber-200 mt-1 text-sm">Semua hasil ujian yang telah dikerjakan</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Results Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Ujian</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Mapel</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nilai</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Predikat</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">B/S/K</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Rank</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($results as $result)
                    @php
                        $passed = ($result->final_score ?? 0) >= ($result->exam->passing_score ?? 0);
                        $pColor = match($result->predicate ?? '') {
                            'A' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                            'B' => 'bg-blue-50 text-blue-700 border-blue-200',
                            'C' => 'bg-amber-50 text-amber-700 border-amber-200',
                            default => 'bg-red-50 text-red-700 border-red-200',
                        };
                    @endphp
                    <tr class="hover:bg-gradient-to-r hover:from-amber-50/30 hover:to-transparent transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $result->exam->exam_title ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ strtoupper($result->exam->exam_type ?? '') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-gray-600">{{ $result->exam->subject->subject_name ?? $result->exam->subject->name ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-lg font-bold {{ $passed ? 'text-emerald-600' : 'text-red-600' }}">{{ number_format($result->final_score, 1) }}</span>
                        </td>
                        <td class="px-5 py-4 text-center"><span class="px-2.5 py-0.5 rounded-lg text-xs font-bold border {{ $pColor }}">{{ $result->predicate ?? '-' }}</span></td>
                        <td class="px-5 py-4 text-center">
                            <span class="text-emerald-600 font-bold">{{ $result->correct_answers }}</span>
                            <span class="text-gray-300">/</span>
                            <span class="text-red-500 font-bold">{{ $result->wrong_answers }}</span>
                            <span class="text-gray-300">/</span>
                            <span class="text-gray-400">{{ $result->unanswered }}</span>
                        </td>
                        <td class="px-5 py-4 text-center font-bold text-gray-700">{{ $result->rank ?? '-' }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold border {{ $passed ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                {{ $passed ? 'Lulus' : 'Tidak Lulus' }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right text-gray-500 text-xs">{{ $result->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center py-16 text-gray-400"><i class="fas fa-inbox text-4xl mb-3 block"></i>Belum ada riwayat ujian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($results->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $results->links() }}</div>
        @endif
    </div>
</div>
@endsection
