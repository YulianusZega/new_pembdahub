@extends('layouts.orangtua')
@section('title', 'Absensi '.$student->full_name.' - Portal Orang Tua')

@section('content')
<div class="space-y-6">
    @include('orangtua.partials.child-header', ['student' => $student, 'classroom' => $classroom, 'active' => 'absensi'])

    {{-- Summary --}}
    <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
        @php
            $items = [
                ['label' => 'Hadir', 'value' => $summary['present'], 'color' => 'green'],
                ['label' => 'Sakit', 'value' => $summary['sick'], 'color' => 'yellow'],
                ['label' => 'Izin', 'value' => $summary['permission'], 'color' => 'blue'],
                ['label' => 'Absen', 'value' => $summary['absent'], 'color' => 'red'],
                ['label' => 'Terlambat', 'value' => $summary['late'], 'color' => 'orange'],
                ['label' => 'Persentase', 'value' => $summary['percentage'].'%', 'color' => 'purple'],
            ];
        @endphp
        @foreach($items as $item)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3 text-center">
                <p class="text-lg font-bold text-{{ $item['color'] }}-600">{{ $item['value'] }}</p>
                <p class="text-xs text-gray-500">{{ $item['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800">📅 Riwayat Kehadiran</h2>
        </div>
        @if($attendances->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-3 text-left font-semibold">Tanggal</th>
                            <th class="px-5 py-3 text-center font-semibold">Status</th>
                            <th class="px-5 py-3 text-left font-semibold">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($attendances as $att)
                            @php
                                $map = [
                                    'hadir' => ['Hadir', 'bg-green-100 text-green-700'],
                                    'sakit' => ['Sakit', 'bg-yellow-100 text-yellow-700'],
                                    'izin' => ['Izin', 'bg-blue-100 text-blue-700'],
                                    'alpha' => ['Absen', 'bg-red-100 text-red-700'],
                                ];
                                $s = $map[$att->status] ?? ['?', 'bg-gray-100 text-gray-700'];
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-5 py-3">{{ $att->attendance_date ? $att->attendance_date->translatedFormat('l, d M Y') : '-' }}</td>
                                <td class="px-5 py-3 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $s[1] }}">{{ $s[0] }}</span>
                                </td>
                                <td class="px-5 py-3 text-gray-500 text-xs">{{ $att->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-10 text-center text-gray-400">
                <i class="fas fa-clipboard text-4xl mb-3"></i>
                <p>Belum ada data kehadiran.</p>
            </div>
        @endif
    </div>
</div>
@endsection
