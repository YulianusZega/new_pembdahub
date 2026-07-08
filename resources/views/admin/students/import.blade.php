@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Modern Header -->
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
            </svg>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Import Data Siswa</h1>
            <p class="text-gray-600">Upload file Excel (.xlsx / .xls) untuk import bulk</p>
        </div>
    </div>

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg shadow">
        <div class="flex items-start">
            <svg class="w-6 h-6 text-red-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
            </svg>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $e)
                <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @csrf
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
            <h2 class="text-xl font-bold text-white"><i class="fas fa-chart-bar mr-1"></i> Upload File Excel</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2"><i class="fas fa-file-alt mr-1"></i> Pilih File Excel (.xlsx / .xls)</label>
                <input type="file" name="file" accept=".xlsx, .xls" class="w-full border-2 border-gray-200 p-3 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-6 h-6 text-blue-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"/>
                    </svg>
                    <div>
                        <p class="font-semibold text-blue-800 mb-1">Format Excel</p>
                        <p class="text-sm text-blue-700">Unduh template Excel yang telah disediakan untuk melihat daftar kolom dan contoh pengisian data siswa. Kolom wajib diisi minimal adalah <code class="bg-blue-100 px-2 py-1 rounded">school_id, nisn, full_name, gender, entry_year</code>.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 pb-6 flex gap-3">
            <button type="submit" class="flex-1 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition duration-200 transform hover:scale-105">
                <i class="fas fa-arrow-up mr-1"></i> Import Data
            </button>
            <a href="{{ route('admin.students.index') }}" class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition duration-200">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection