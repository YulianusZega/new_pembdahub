@extends(auth()->user()->isYayasan() ? 'layouts.yayasan' : 'layouts.admin')

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
        <form method="GET" action="{{ route((auth()->user()->isYayasan() && request()->routeIs('yayasan.*') ? 'yayasan.' : 'admin.') . 'performance_evaluations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Pelajaran (Target)</label>
                <select name="academic_year_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>
                            {{ $year->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Semester (Penilaian)</label>
                <select name="semester_id" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                    @foreach($semesters as $semester)
                        <option value="{{ $semester->id }}" {{ $selectedSemesterId == $semester->id ? 'selected' : '' }}>
                            {{ $semester->name }} ({{ \Carbon\Carbon::parse($semester->start_date)->format('M Y') }} - {{ \Carbon\Carbon::parse($semester->end_date)->format('M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Cari Guru</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / NIP" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
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
            <table class="w-full whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-200">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Guru</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tipe Kontrak</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Nilai (1-5)</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status Evaluasi</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($contracts as $contract)
                        @php
                            $evaluation = $contract->evaluations->first();
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
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
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900 font-medium">
                                    @if($contract->contract_type === 'jabatan_tambahan')
                                        Jabatan Tambahan ({{ $contract->position->position_name ?? 'Tidak diketahui' }})
                                    @elseif($contract->contract_type === 'pkg_kejuruan')
                                        Tugas Utama (Kejuruan/Produktif)
                                    @else
                                        Tugas Utama (Mapel Umum)
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($evaluation && $evaluation->score > 0)
                                    <div class="inline-flex items-center justify-center px-3 py-1 bg-green-100 text-green-700 font-bold rounded-full">
                                        <i class="fas fa-star text-yellow-400 mr-1 text-xs"></i> {{ number_format($evaluation->score, 2) }}
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
                                <a href="{{ route((auth()->user()->isYayasan() && request()->routeIs('yayasan.*') ? 'yayasan.' : 'admin.') . 'performance_evaluations.evaluate', [$contract->id, $selectedSemesterId]) }}" 
                                   class="inline-flex items-center gap-1 text-white bg-indigo-600 hover:bg-indigo-700 px-3 py-1.5 rounded-lg transition-colors">
                                    <i class="fas fa-edit text-xs"></i> Nilai
                                </a>
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
