@extends('layouts.admin')

@section('title', 'Kompetensi Guru - ' . $teacher->full_name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <a href="{{ $returnUrl ?? route('admin.teachers.index') }}" class="text-purple-600 hover:text-purple-800 mb-2 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>{{ isset($returnUrl) ? 'Kembali ke Daftar Guru' : 'Kembali ke Daftar' }}
                </a>
                <h2 class="text-3xl font-bold text-gray-800">Kompetensi Mengajar</h2>
                <p class="text-gray-600 mt-2">
                    <span class="font-semibold">{{ $teacher->full_name }}</span> 
                    <span class="text-sm">({{ $teacher->teacher_code }})</span>
                </p>
            </div>
            <div class="text-right">
                <div class="bg-gradient-to-r from-purple-500 to-pink-500 text-white px-6 py-3 rounded-xl shadow-lg">
                    <div class="text-sm opacity-90">Total Kompetensi</div>
                    <div class="text-3xl font-bold">{{ $teacher->competentSubjects->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Assigned Subjects -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    Mata Pelajaran yang Dikuasai
                </h3>

                @if($teacher->competentSubjects->isEmpty())
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-book-open text-6xl mb-4"></i>
                        <p class="text-lg">Belum ada kompetensi yang ditambahkan</p>
                        <p class="text-sm mt-2">Pilih mata pelajaran di panel sebelah kanan</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($teacher->competentSubjects as $subject)
                            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-emerald-50 to-teal-50 border-2 border-emerald-200 rounded-xl hover:shadow-md transition">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                        {{ strtoupper(substr($subject->subject_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $subject->subject_name }}</div>
                                        <div class="text-xs text-gray-500">{{ $subject->subject_code }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <button onclick="removeCompetency({{ $teacher->id }}, {{ $subject->id }}, '{{ $subject->subject_name }}')" 
                                            class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Right: Available Subjects -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg p-6 sticky top-4">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-purple-500 mr-2"></i>
                    Tambah Kompetensi
                </h3>

                <form action="{{ route('admin.teachers.competencies.update', $teacher->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @if(isset($returnUrl))
                        <input type="hidden" name="return_url" value="{{ $returnUrl }}">
                    @endif

                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Pilih Mata Pelajaran
                            <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-2 max-h-96 overflow-y-auto border-2 border-gray-200 rounded-xl p-3">
                            @foreach($allSubjects as $subject)
                                <label class="flex items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition">
                                    <input type="checkbox" 
                                           name="subjects[]" 
                                           value="{{ $subject->id }}"
                                           {{ in_array($subject->id, $assignedSubjectIds) ? 'checked' : '' }}
                                           class="w-5 h-5 text-purple-600 rounded focus:ring-2 focus:ring-purple-500">
                                    <span class="ml-3 text-sm text-gray-700">{{ $subject->subject_name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="flex-1 px-4 py-3 bg-gradient-to-r from-purple-600 to-pink-600 text-white font-semibold rounded-xl hover:from-purple-700 hover:to-pink-700 transition-all shadow-lg">
                            <i class="fas fa-save mr-2"></i>Simpan
                        </button>
                    </div>
                </form>

                <!-- Info Box -->
                <div class="mt-6 bg-blue-50 border-2 border-blue-200 rounded-xl p-4">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-blue-500 mt-1 mr-3"></i>
                        <div class="text-sm text-blue-800">
                            <p class="font-semibold mb-1">Catatan:</p>
                            <ul class="list-disc list-inside space-y-1 text-xs">
                                <li>Centang mata pelajaran yang dikuasai guru</li>
                                <li>Guru bisa memiliki beberapa kompetensi</li>
                                <li>Tandai satu sebagai kompetensi utama</li>
                                <li>Hanya guru dengan kompetensi yang muncul saat penjadwalan</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function removeCompetency(teacherId, subjectId, subjectName) {
    if (!confirm(`Hapus kompetensi "${subjectName}" dari guru ini?`)) {
        return;
    }

    fetch(`/admin/teachers/${teacherId}/competencies/${subjectId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network error');
        return response.json();
    })
    .then(data => {
        if (data.message) {
            window.location.reload();
        }
    })
    .catch(error => {
        showFlashMessage('Gagal menghapus kompetensi. Silakan coba lagi.', 'error');
    });
}
</script>
@endsection
