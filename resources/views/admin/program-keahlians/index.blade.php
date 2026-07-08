@extends('layouts.admin')

@section('title', 'Program Keahlian SMK')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-orange-700 mb-4">Program Keahlian SMK</h1>
    <a href="{{ route('admin.program-keahlians.create') }}" class="mb-4 inline-block bg-orange-600 hover:bg-orange-700 text-white px-6 py-2 rounded-xl font-semibold shadow-lg">Tambah Program Keahlian</a>
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-orange-700">Sekolah</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-orange-700">Kode</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-orange-700">Nama Program Keahlian</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-orange-700">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($programKeahlians as $pk)
                <tr>
                    <td class="px-6 py-4">{{ $pk->school->name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $pk->kode }}</td>
                    <td class="px-6 py-4 font-semibold text-orange-900">{{ $pk->nama }}</td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.program-keahlians.edit', $pk) }}" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-all" title="Edit Program Keahlian">
                            Edit
                        </a>
                    </td>
                </tr>
                @endforeach
                @if($programKeahlians->isEmpty())
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-orange-500">Belum ada data program keahlian SMK</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection
