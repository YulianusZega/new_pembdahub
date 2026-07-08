@extends('layouts.admin')
@section('title', 'Tambah Lowongan Kerja - Portal Admin')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    {{-- Header Bar --}}
    <div class="flex items-center gap-3 bg-white rounded-xl shadow-sm border border-gray-100 px-5 py-4">
        <a href="{{ route('admin.pkl-alumni.jobs.index') }}" class="w-8 h-8 rounded-lg bg-gray-50 border border-gray-100 hover:bg-gray-100 flex items-center justify-center text-gray-500 transition">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-lg md:text-xl font-bold text-gray-800">Tambah Lowongan Kerja</h1>
            <p class="text-xs text-gray-500 mt-0.5">Menerbitkan lowongan pekerjaan DUDI mitra di portal siswa/alumni</p>
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
        <form action="{{ route('admin.pkl-alumni.jobs.store') }}" method="POST" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Company Name --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Nama Instansi / Perusahaan</label>
                    <input type="text" name="company_name" value="{{ old('company_name') }}" placeholder="Contoh: CV. Gunungsitoli Tech" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                </div>

                {{-- Job Title --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Posisi / Nama Pekerjaan</label>
                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Contoh: Junior Web Developer" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>
                </div>

                {{-- Salary Range --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Kisaran Gaji Bulanan (Opsional)</label>
                    <input type="text" name="salary_range" value="{{ old('salary_range') }}" placeholder="Contoh: Rp 2.000.000 - Rp 3.000.000" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                </div>

                {{-- Contact Email --}}
                <div>
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Email Kontak Lamaran (Opsional)</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email') }}" placeholder="Contoh: hrd@perusahaan.co.id" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                </div>

                {{-- Contact Phone --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">No. HP/WA Kontak Lamaran (Opsional)</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone') }}" placeholder="Contoh: 081234567890" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Deskripsi Pekerjaan</label>
                    <textarea name="description" rows="4" placeholder="Tuliskan tugas utama, tanggung jawab, dan gambaran umum pekerjaan..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition" required>{{ old('description') }}</textarea>
                </div>

                {{-- Requirements --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-550 mb-1.5">Persyaratan Pelamar (Kualifikasi)</label>
                    <textarea name="requirements" rows="4" placeholder="Tuliskan keahlian khusus, tingkat pendidikan minimal, batas umur, atau kompetensi teknis yang dibutuhkan..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:bg-white transition">{{ old('requirements') }}</textarea>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex justify-end gap-3 border-t border-gray-50 pt-4 mt-6">
                <a href="{{ route('admin.pkl-alumni.jobs.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold px-5 py-2.5 rounded-xl text-sm transition">
                    Batal
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2.5 rounded-xl shadow transition text-sm">
                    Terbitkan Lowongan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
