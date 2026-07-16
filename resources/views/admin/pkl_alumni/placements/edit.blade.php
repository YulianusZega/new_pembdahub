@extends('layouts.admin')
@section('title', 'Edit Penempatan PKL - Portal Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header Bar --}}
    <div class="flex items-center gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <a href="{{ route('admin.pkl-alumni.placements.index') }}" class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-100 hover:bg-gray-100 flex items-center justify-center text-gray-500 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800">Edit Penempatan PKL</h1>
            <p class="text-xs text-gray-500 mt-0.5">Ubah data penempatan untuk siswa: {{ $placement->student->full_name }}</p>
        </div>
    </div>

    {{-- Error validation alert --}}
    @if($errors->any())
        <div class="bg-rose-50 border border-rose-250 text-rose-800 px-4 py-3 rounded-xl text-xs font-semibold space-y-1">
            <p class="font-bold">Terjadi kesalahan input:</p>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 md:p-6">
        <form action="{{ route('admin.pkl-alumni.placements.update', $placement->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Readonly Student name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-400 uppercase mb-1">Siswa Magang</label>
                    <div class="w-full bg-gray-100 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-500 font-semibold select-none">
                        {{ $placement->student->full_name }} (NISN: {{ $placement->student->nisn }} / {{ $placement->student->school->name ?? '' }})
                    </div>
                </div>

                {{-- Academic Year --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tahun Ajaran</label>
                    <select name="academic_year_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                        @foreach($academicYears as $year)
                            <option value="{{ $year->id }}" {{ old('academic_year_id', $placement->academic_year_id) == $year->id ? 'selected' : '' }}>
                                Tahun Ajaran {{ $year->year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Teacher --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Guru Pembimbing Internal</label>
                    <select name="teacher_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                        <option value="">Pilih Guru...</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ old('teacher_id', $placement->teacher_id) == $t->id ? 'selected' : '' }}>
                                {{ $t->user->name ?? '-' }} (NIP: {{ $t->nip ?? '-' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- DUDI --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Mitra DUDI</label>
                    <select name="dudi_id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                        <option value="">-- Pilih Mitra DUDI --</option>
                        @foreach($dudis as $dudi)
                            <option value="{{ $dudi->id }}" {{ old('dudi_id', $placement->dudi_id) == $dudi->id ? 'selected' : '' }}>
                                {{ $dudi->name }}
                                @if($dudi->school_id) ({{ $dudi->school->name ?? 'SMK' }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Shift --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Periode Shift PKL</label>
                    <select name="shift" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                        <option value="">-- Pilih Shift PKL --</option>
                        <option value="Shift A (Juli - Oktober)" {{ old('shift', $placement->shift) == 'Shift A (Juli - Oktober)' ? 'selected' : '' }}>Shift A (Juli - Oktober)</option>
                        <option value="Shift B (Oktober - Februari)" {{ old('shift', $placement->shift) == 'Shift B (Oktober - Februari)' ? 'selected' : '' }}>Shift B (Oktober - Februari)</option>
                    </select>
                </div>

                {{-- Start Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal Mulai Magang</label>
                    <input type="date" name="start_date" value="{{ old('start_date', $placement->start_date->format('Y-m-d')) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                </div>

                {{-- End Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Tanggal Selesai Magang</label>
                    <input type="date" name="end_date" value="{{ old('end_date', $placement->end_date->format('Y-m-d')) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                </div>

                {{-- Status --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Status Penempatan</label>
                    <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                        <option value="active" {{ old('status', $placement->status) === 'active' ? 'selected' : '' }}>Aktif (Magang sedang berjalan)</option>
                        <option value="completed" {{ old('status', $placement->status) === 'completed' ? 'selected' : '' }}>Selesai (Periode magang telah berakhir)</option>
                        <option value="cancelled" {{ old('status', $placement->status) === 'cancelled' ? 'selected' : '' }}>Batal (Penempatan dibatalkan)</option>
                    </select>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 border-t border-gray-50 pt-4 mt-6">
                <a href="{{ route('admin.pkl-alumni.placements.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-2.5 rounded-xl text-sm transition">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl shadow transition text-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
