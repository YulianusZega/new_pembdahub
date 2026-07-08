@extends('layouts.admin')
@section('title', 'Data Alumni')
@section('content')
<div class="space-y-6">
    <div class="mb-8">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-cyan-600 flex items-center justify-center text-white">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Data Alumni</h1>
                <p class="text-gray-600 mt-1">Daftar siswa yang telah lulus</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-3 text-left text-sm">NIS</th>
                    <th class="px-6 py-3 text-left text-sm">Nama</th>
                    <th class="px-6 py-3 text-left text-sm">Kelas Terakhir</th>
                    <th class="px-6 py-3 text-left text-sm">Tgl Kelulusan</th>
                    <th class="px-6 py-3 text-left text-sm">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($alumni as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $student->nis }}</td>
                    <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $student->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600">{{ $student->classroom?->class_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $student->statusHistories->where('new_status', 'lulus')->last()?->transition_date?->format('d M Y') ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('admin.students.lifecycle.history', $student) }}" class="text-indigo-600 hover:text-indigo-800 text-sm"><i class="fas fa-eye"></i></a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data alumni.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="px-6 py-4">{{ $alumni->links() }}</div>
    </div>
</div>
@endsection
