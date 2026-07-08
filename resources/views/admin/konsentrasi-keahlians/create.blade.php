@extends('layouts.admin')

@section('title', 'Tambah Konsentrasi Keahlian SMK')

@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-orange-700 mb-2">Tambah Konsentrasi Keahlian SMK</h1>
        <p class="text-gray-600">Isi data konsentrasi keahlian untuk SMK.</p>
    </div>
    <form action="{{ route('admin.konsentrasi-keahlians.store') }}" method="POST" class="bg-white rounded-2xl shadow-lg p-6">
        @csrf
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Program Keahlian</label>
            <select name="program_keahlian_id" class="w-full border border-gray-300 rounded-xl px-4 py-2">
                <option value="">-- Pilih Program Keahlian --</option>
                @foreach($programKeahlians as $pk)
                <option value="{{ $pk->id }}" {{ $selectedProgramId == $pk->id ? 'selected' : '' }}>{{ $pk->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kode Konsentrasi Keahlian</label>
            <input type="text" name="kode" class="w-full border border-gray-300 rounded-xl px-4 py-2" maxlength="10">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Nama Konsentrasi Keahlian</label>
            <input type="text" name="nama" class="w-full border border-gray-300 rounded-xl px-4 py-2" maxlength="100">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Deskripsi</label>
            <textarea name="deskripsi" rows="3" class="w-full border border-gray-300 rounded-xl px-4 py-2"></textarea>
        </div>
        <div class="mb-4">
            <label class="inline-flex items-center">
                <input type="checkbox" name="is_active" value="1" checked class="mr-2">
                Aktif
            </label>
        </div>
        <div class="flex gap-3 mt-6">
            <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-xl font-semibold shadow-lg">Simpan</button>
            <a href="{{ route('admin.majors.index') }}" class="px-6 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
