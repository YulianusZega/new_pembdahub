@extends('layouts.admin')

@section('title', 'Import Mata Pelajaran')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-pink-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Import Mata Pelajaran</h1>
            <p class="text-gray-600">Upload file CSV untuk import bulk</p>
        </div>
    </div>

    <form action="{{ route('admin.subjects.import') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @csrf
        <div class="bg-gradient-to-r from-pink-500 to-rose-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-chart-bar mr-1"></i> Upload File CSV</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-file-alt mr-1"></i> Pilih File CSV</label>
                <input type="file" name="csv" accept=".csv" aria-label="File CSV untuk impor" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-pink-500 focus:border-transparent transition">
                @error('csv')<div class="text-red-600 mt-2 text-sm">{{ $message }}</div>@enderror
            </div>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-blue-800 mb-1">Format CSV</p>
                        <p class="text-sm text-blue-700 mb-2">Kolom: <code class="bg-blue-100 px-2 py-1 rounded">school_id, major_id, subject_code, subject_name, description, kkm, is_active</code></p>
                        <a href="{{ route('admin.subjects.import.sample') }}" class="text-pink-600 hover:text-pink-700 font-semibold text-sm"><i class="fas fa-arrow-down mr-1"></i> Download contoh CSV</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-pink-500 to-rose-600 hover:from-pink-600 hover:to-rose-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-arrow-up mr-1"></i> Upload & Import
            </button>
            <a href="{{ route('admin.subjects.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection