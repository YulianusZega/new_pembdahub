@extends('layouts.admin')

@section('title', 'Edit Semester')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="flex items-center justify-center w-14 h-14 rounded-2xl bg-gradient-to-br from-teal-500 to-cyan-600 shadow-lg">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Semester</h1>
                <p class="text-gray-600 mt-1">Perbarui informasi semester</p>
            </div>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-red-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div class="flex-1">
                <h3 class="text-red-800 font-semibold mb-2">Terdapat kesalahan pada form:</h3>
                <ul class="list-disc list-inside text-red-700 space-y-1">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.semesters.update', $semester) }}" method="POST">
        @csrf @method('PUT')

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-teal-500 to-cyan-600 text-white font-bold text-sm">
                    1
                </div>
                <h2 class="text-xl font-bold text-gray-900">Informasi Semester</h2>
            </div>

            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tahun Ajaran</label>
                    <select name="academic_year_id" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="">-- Pilih Tahun Ajaran --</option>
                        @foreach($academicYears as $ay)
                        <option value="{{ $ay->id }}" {{ old('academic_year_id', $semester->academic_year_id) == $ay->id ? 'selected' : '' }}>{{ $ay->year }}@if($ay->is_active) (Aktif)@endif</option>
                        @endforeach
                    </select>
                    @error('academic_year_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-list-ol mr-1"></i> Semester</label>
                    <select name="semester_number" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                        <option value="1" {{ old('semester_number', $semester->semester_number) == 1 ? 'selected' : '' }}>1 - Ganjil</option>
                        <option value="2" {{ old('semester_number', $semester->semester_number) == 2 ? 'selected' : '' }}>2 - Genap</option>
                    </select>
                    @error('semester_number')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-graduation-cap mr-1"></i> Nama Semester</label>
                    <input type="text" name="semester_name" value="{{ old('semester_name', $semester->semester_name) }}" 
                        placeholder="Contoh: Semester Ganjil"
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent" />
                    @error('semester_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
            <div class="flex items-center gap-3 mb-6 pb-4 border-b border-gray-200">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-teal-500 to-cyan-600 text-white font-bold text-sm">
                    2
                </div>
                <h2 class="text-xl font-bold text-gray-900">Periode & Status</h2>
            </div>

            <div class="space-y-5">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $semester->start_date->format('Y-m-d')) }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent" />
                        @error('start_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-calendar-alt mr-1"></i> Tanggal Selesai</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $semester->end_date->format('Y-m-d')) }}" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-teal-500 focus:border-transparent" />
                        @error('end_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div>
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $semester->is_active) ? 'checked' : '' }}
                            class="w-5 h-5 text-teal-600 border-gray-300 rounded focus:ring-teal-500">
                        <span class="text-sm font-medium text-gray-700"><i class="fas fa-check-circle text-green-500 mr-1"></i> Semester aktif</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" 
                class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-teal-600 to-cyan-700 text-white rounded-xl font-semibold text-sm hover:from-teal-700 hover:to-cyan-800 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Simpan Perubahan
            </button>
            <a href="{{ route('admin.semesters.index') }}" 
                class="px-5 py-2.5 bg-white border-2 border-gray-300 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-50 transition-all">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection