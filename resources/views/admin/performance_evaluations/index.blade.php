@extends(auth()->user()->isKetuaYayasan() ? 'layouts.yayasan' : 'layouts.admin')

@section('title', 'Evaluasi Perjanjian Kinerja')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Evaluasi Perjanjian Kinerja</h1>
            <p class="text-gray-600 mt-1">Lakukan penilaian akhir semester (Skala 1-5) atas target kinerja guru.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
        <form method="GET" action="{{ route((auth()->user()->isKetuaYayasan() && request()->routeIs('yayasan.*') ? 'yayasan.' : 'admin.') . 'performance_evaluations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-semibold text-gray-900 !text-gray-900 mb-1">Tahun Pelajaran (Target)</label>
                <select name="academic_year_id" onchange="this.form.submit()" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-900 !text-gray-900 bg-white !bg-white font-medium">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }} class="text-gray-900 !text-gray-900 bg-white">
                            {{ $year->year ?? $year->name }}{{ $year->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-900 !text-gray-900 mb-1">Semester (Penilaian)</label>
                <select name="semester_id" onchange="this.form.submit()" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-900 !text-gray-900 bg-white !bg-white font-medium">
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ $selectedSemesterId == $semester->id ? 'selected' : '' }} class="text-gray-900 !text-gray-900 bg-white">
                            {{ $semester->name }} ({{ \Carbon\Carbon::parse($semester->start_date)->format('M Y') }} - {{ \Carbon\Carbon::parse($semester->end_date)->format('M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 !text-gray-900 mb-1">Cari Guru</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIP" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-900 !text-gray-900 bg-white !bg-white font-medium">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 font-medium transition-colors">
                    Terapkan Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/6">Tipe Kontrak</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-2/5">Hasil Penilaian & Analisis Pilar</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/12">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/12">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($contracts as $contract)
                        @php
                            $evaluation = $contract->evaluations->first();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 align-top">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 rounded-full flex items-center justify-center">
                                        <span class="text-indigo-600 font-bold text-sm">{{ substr($contract->employee->full_name, 0, 2) }}</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-semibold text-gray-900">{{ $contract->employee->full_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $contract->employee->employee_code }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 align-top">
                                <span class="text-sm text-gray-900 font-medium block">
                                    @if($contract->contract_type === 'jabatan_tambahan')
                                        Jabatan Tambahan
                                        <span class="block text-xs text-indigo-600 font-semibold">{{ $contract->position->position_name ?? 'Tidak diketahui' }}</span>
                                    @elseif($contract->contract_type === 'pkg_kejuruan')
                                        Tugas Utama (Kejuruan/Produktif)
                                    @else
                                        Tugas Utama (Mapel Umum)
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 align-top">
                                @if($evaluation && $evaluation->score > 0)
                                    <div class="space-y-2.5 max-w-xl">
                                        <!-- Score Header & Category Tag -->
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-600 text-white font-black text-sm rounded-lg shadow-sm">
                                                <i class="fas fa-star text-yellow-300"></i> {{ number_format($evaluation->score, 2) }}
                                            </span>
                                            @php
                                                $score = $evaluation->score;
                                                if ($score >= 4.5) {
                                                    $statusBadge = 'bg-emerald-100 text-emerald-800 border-emerald-200';
                                                    $statusText = 'Sangat Baik (Melampaui Target)';
                                                    $icon = 'fa-check-double text-emerald-600';
                                                } elseif ($score >= 3.5) {
                                                    $statusBadge = 'bg-blue-100 text-blue-800 border-blue-200';
                                                    $statusText = 'Baik (Memenuhi Syarat SK > 3.5)';
                                                    $icon = 'fa-check text-blue-600';
                                                } elseif ($score >= 2.5) {
                                                    $statusBadge = 'bg-amber-100 text-amber-800 border-amber-200';
                                                    $statusText = 'Cukup (Perlu Pembinaan)';
                                                    $icon = 'fa-exclamation-triangle text-amber-600';
                                                } else {
                                                    $statusBadge = 'bg-rose-100 text-rose-800 border-rose-200';
                                                    $statusText = 'Kurang (Di Bawah Kriteria)';
                                                    $icon = 'fa-times-circle text-rose-600';
                                                }
                                            @endphp
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusBadge }}">
                                                <i class="fas {{ $icon }}"></i> {{ $statusText }}
                                            </span>
                                        </div>

                                        <!-- Pillars Breakdown Grid -->
                                        @if(is_array($evaluation->evaluation_data) && !empty($evaluation->evaluation_data))
                                            <div class="grid grid-cols-2 gap-1.5 text-xs">
                                                @foreach($evaluation->evaluation_data as $key => $val)
                                                    @php
                                                        if ($key === 'pilar_1') $label = 'Pilar 1: Kompetensi Praktik';
                                                        elseif ($key === 'pilar_2') $label = 'Pilar 2: Kontribusi Program';
                                                        elseif ($key === 'pilar_3') $label = 'Pilar 3: Kolaborasi';
                                                        elseif ($key === 'pilar_4') $label = 'Pilar 4: Budaya 5R / K3';
                                                        elseif (str_starts_with($key, 'target_')) $label = 'Target Pekerjaan ' . substr($key, 7);
                                                        else $label = ucwords(str_replace('_', ' ', $key));

                                                        $pilarColor = $val >= 4 ? 'bg-emerald-50 text-emerald-700 border-emerald-200/80' : ($val >= 3 ? 'bg-blue-50 text-blue-700 border-blue-200/80' : 'bg-amber-50 text-amber-700 border-amber-200/80');
                                                    @endphp
                                                    <div class="flex items-center justify-between px-2.5 py-1 rounded border {{ $pilarColor }} font-medium">
                                                        <span class="truncate mr-1.5" title="{{ $label }}">{{ $label }}</span>
                                                        <span class="font-bold shrink-0">{{ $val }}/5</span>
                                                    </div>
                                                @endforeach
                                            </div>

                                            <!-- Descriptive Analysis Statement -->
                                            @php
                                                $evalData = $evaluation->evaluation_data;
                                                $minScore = !empty($evalData) ? min($evalData) : 0;
                                                $maxScore = !empty($evalData) ? max($evalData) : 0;
                                                
                                                $lowestKeys = array_keys($evalData, $minScore);
                                                $highestKeys = array_keys($evalData, $maxScore);
                                                
                                                $getKeyLabel = function($k) {
                                                    if ($k === 'pilar_1') return 'Kompetensi Praktik';
                                                    if ($k === 'pilar_2') return 'Kontribusi Program';
                                                    if ($k === 'pilar_3') return 'Kolaborasi';
                                                    if ($k === 'pilar_4') return 'Budaya 5R / K3';
                                                    if (str_starts_with($k, 'target_')) return 'Target ' . substr($k, 7);
                                                    return ucwords(str_replace('_', ' ', $k));
                                                };
                                                
                                                $lowestLabel = $getKeyLabel($lowestKeys[0] ?? '');
                                                $highestLabel = $getKeyLabel($highestKeys[0] ?? '');

                                                if ($score >= 4.0) {
                                                    $analisa = "Kinerja sangat konsisten dan unggul. Keunggulan utama pada aspek {$highestLabel} ({$maxScore}/5). Layak dipertahankan sebagai rol model.";
                                                } elseif ($score >= 3.5) {
                                                    if ($minScore < 3.0) {
                                                        $analisa = "Memenuhi syarat rata-rata SK (> 3.5), namun perlu perhatian khusus pada peningkatan aspek {$lowestLabel} ({$minScore}/5).";
                                                    } else {
                                                        $analisa = "Kinerja stabil dan memenuhi target di seluruh pilar. Paling menonjol pada aspek {$highestLabel} ({$maxScore}/5).";
                                                    }
                                                } elseif ($score >= 2.5) {
                                                    $analisa = "Kinerja dalam tahap cukup. Diperlukan pembinaan dan pendampingan intensif khususnya pada aspek {$lowestLabel} ({$minScore}/5).";
                                                } else {
                                                    $analisa = "Kinerja berada di bawah target yang disepakati. Evaluasi menyeluruh dan evaluasi pembinaan diperlukan pada aspek {$lowestLabel}.";
                                                }
                                            @endphp
                                            <div class="p-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs text-slate-700 leading-relaxed">
                                                <div class="font-bold text-slate-900 mb-0.5"><i class="fas fa-chart-line text-indigo-500 mr-1"></i> Analisis Deskriptif Pilar:</div>
                                                <div>{{ $analisa }}</div>
                                                @if(!empty($evaluation->notes))
                                                    <div class="mt-1.5 pt-1.5 border-t border-slate-200/80 text-slate-600 italic">
                                                        <span class="font-semibold text-slate-700">Catatan Evaluator:</span> "{{ $evaluation->notes }}"
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400 italic text-sm">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if(!$evaluation)
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Belum Dinilai</span>
                                @elseif($evaluation->status === 'draft')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Draft (Kepsek)</span>
                                @elseif($evaluation->status === 'submitted_to_yayasan')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Yayasan</span>
                                @elseif($evaluation->status === 'approved_by_yayasan')
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Final (ACC Yayasan)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                @php
                                    $evalRoute = route((auth()->user()->isKetuaYayasan() && request()->routeIs('yayasan.*') ? 'yayasan.' : 'admin.') . 'performance_evaluations.evaluate', [$contract->id, $selectedSemesterId]);
                                @endphp
                                @if(auth()->user()->isKetuaYayasan())
                                    @if($evaluation && $evaluation->status === 'submitted_to_yayasan')
                                        <a href="{{ $evalRoute }}" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap text-white bg-emerald-600 hover:bg-emerald-700 px-3.5 py-1.5 rounded-lg font-bold shadow-sm transition-colors" style="background-color: #059669 !important; color: #ffffff !important; white-space: nowrap !important;">
                                            <i class="fas fa-check-double text-xs"></i> Tinjau & ACC
                                        </a>
                                    @elseif($evaluation && $evaluation->status === 'approved_by_yayasan')
                                        <a href="{{ $evalRoute }}" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap text-white bg-slate-700 hover:bg-slate-800 px-3.5 py-1.5 rounded-lg font-bold shadow-sm transition-colors" style="background-color: #334155 !important; color: #ffffff !important; white-space: nowrap !important;">
                                            <i class="fas fa-eye text-xs"></i> Lihat Hasil
                                        </a>
                                    @else
                                        <a href="{{ $evalRoute }}" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap text-white bg-amber-500 hover:bg-amber-600 px-3.5 py-1.5 rounded-lg font-bold shadow-sm transition-colors" style="background-color: #f59e0b !important; color: #ffffff !important; white-space: nowrap !important;" title="Kepala Sekolah belum mengajukan penilaian">
                                            <i class="fas fa-clock text-xs"></i> Menunggu Kepsek
                                        </a>
                                    @endif
                                @else
                                    @if($evaluation && in_array($evaluation->status, ['submitted_to_yayasan', 'approved_by_yayasan']))
                                        <a href="{{ $evalRoute }}" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap text-white bg-blue-600 hover:bg-blue-700 px-3.5 py-1.5 rounded-lg font-bold shadow-sm transition-colors" style="background-color: #2563eb !important; color: #ffffff !important; white-space: nowrap !important;">
                                            <i class="fas fa-eye text-xs"></i> Lihat Detail
                                        </a>
                                    @else
                                        <a href="{{ $evalRoute }}" class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap text-white bg-indigo-600 hover:bg-indigo-700 px-3.5 py-1.5 rounded-lg font-bold shadow-sm transition-colors" style="background-color: #4f46e5 !important; color: #ffffff !important; white-space: nowrap !important;">
                                            <i class="fas fa-pen text-xs"></i> Nilai Kinerja
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-file-contract text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-lg font-medium text-gray-900">Tidak ada data</p>
                                    <p class="text-sm">Belum ada Perjanjian Kinerja yang di-ACC Yayasan untuk tahun pelajaran ini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($contracts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
