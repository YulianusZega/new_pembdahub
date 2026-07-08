@extends('layouts.admin')
@section('title', 'Edit Lowongan Kerja - Portal Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header Bar --}}
    <div class="flex items-center gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <a href="{{ route('admin.pkl-alumni.jobs.index') }}" class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-100 hover:bg-gray-100 flex items-center justify-center text-gray-500 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800">Edit Lowongan Kerja</h1>
            <p class="text-xs text-gray-500 mt-0.5">Ubah rincian informasi lowongan pekerjaan: {{ $job->title }}</p>
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
        <form action="{{ route('admin.pkl-alumni.jobs.update', $job->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Company Name --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Nama Instansi / Perusahaan</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $job->company_name) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                </div>

                {{-- Job Title --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Posisi / Nama Pekerjaan</label>
                    <input type="text" name="title" value="{{ old('title', $job->title) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                </div>

                {{-- Salary Range --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Kisaran Gaji Bulanan (Opsional)</label>
                    <input type="text" name="salary_range" value="{{ old('salary_range', $job->salary_range) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                </div>

                {{-- Contact Email --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Email Kontak Lamaran (Opsional)</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $job->contact_email) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                </div>

                {{-- Contact Phone --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">No. HP/WA Kontak Lamaran (Opsional)</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $job->contact_phone) }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Status Lowongan</label>
                    <select name="is_active" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                        <option value="1" {{ old('is_active', $job->is_active) ? 'selected' : '' }}>Aktif (Ditayangkan ke siswa/alumni)</option>
                        <option value="0" {{ !old('is_active', $job->is_active) ? 'selected' : '' }}>Nonaktif (Disembunyikan / Ditutup)</option>
                    </select>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Deskripsi Pekerjaan</label>
                    <textarea name="description" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>{{ old('description', $job->description) }}</textarea>
                </div>

                {{-- Requirements --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Persyaratan Pelamar (Kualifikasi)</label>
                    <textarea name="requirements" rows="4" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">{{ old('requirements', $job->requirements) }}</textarea>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 border-t border-gray-50 pt-4 mt-6">
                <a href="{{ route('admin.pkl-alumni.jobs.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-2.5 rounded-xl text-sm transition">
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
