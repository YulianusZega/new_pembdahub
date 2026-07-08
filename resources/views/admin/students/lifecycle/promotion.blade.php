@extends('layouts.admin')

@section('title', 'Promosi / Kenaikan Kelas')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg">
                <i class="fas fa-level-up-alt text-white text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Promosi / Kenaikan Kelas</h1>
                <p class="text-gray-500 mt-1">Kelola keputusan kenaikan kelas siswa</p>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                <select name="academic_year_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    @foreach($academicYears as $year)
                        <option value="{{ $year->id }}" {{ $selectedYearId == $year->id ? 'selected' : '' }}>{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kelas</label>
                <select name="classroom_id" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($classrooms as $cls)
                        <option value="{{ $cls->id }}" {{ $selectedClassroomId == $cls->id ? 'selected' : '' }}>{{ $cls->class_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-sm">
                    <i class="fas fa-filter mr-2"></i> Tampilkan
                </button>
            </div>
        </form>
    </div>

    <!-- Students List -->
    @if($selectedClassroomId && $students->count() > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">
            Siswa Kelas {{ $classroom?->class_name }} 
            <span class="text-sm font-normal text-gray-500">({{ $students->count() }} siswa)</span>
        </h2>

        <form action="{{ route('admin.promotions.store') }}" method="POST">
            @csrf
            <input type="hidden" name="classroom_id" value="{{ $selectedClassroomId }}">
            <input type="hidden" name="academic_year_id" value="{{ $selectedYearId }}">

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100">
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-3 font-medium text-gray-600">No</th>
                            <th class="px-4 py-3 font-medium text-gray-600">NIS</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Nama</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Keputusan</th>
                            <th class="px-4 py-3 font-medium text-gray-600">Kelas Tujuan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($students as $idx => $student)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-500">{{ $idx + 1 }}</td>
                            <td class="px-4 py-3 font-mono text-gray-700">{{ $student->nis }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $student->full_name }}</td>
                            <td class="px-4 py-3">
                                <select name="decisions[{{ $student->id }}][decision]" required
                                        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="naik">Naik Kelas</option>
                                    <option value="tinggal">Tinggal Kelas</option>
                                    <option value="lulus">Lulus</option>
                                    <option value="pindah">Pindah</option>
                                    <option value="keluar">Keluar</option>
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <select name="decisions[{{ $student->id }}][to_classroom_id]"
                                        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">-</option>
                                    @foreach($classrooms as $cls)
                                        <option value="{{ $cls->id }}">{{ $cls->class_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition font-medium shadow-sm">
                    <i class="fas fa-check-double mr-2"></i> Proses Promosi
                </button>
            </div>
        </form>
    </div>
    @elseif($selectedClassroomId)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 text-center text-gray-400">
        <i class="fas fa-users text-4xl mb-3"></i>
        <p>Tidak ada siswa aktif di kelas ini untuk tahun ajaran yang dipilih.</p>
    </div>
    @endif
</div>
@endsection
