@extends('layouts.admin')

@section('title', 'Input Nilai Tes - ' . $applicant->full_name)

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-2xl p-6 text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-40 h-40 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/4"></div>
        <div class="absolute bottom-0 left-1/3 w-24 h-24 bg-white/5 rounded-full translate-y-1/2"></div>
        <div class="relative flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold flex items-center gap-2">
                    <i class="fas fa-pen-alt"></i> Input Nilai Tes Masuk
                </h1>
                <p class="text-white/70 text-sm mt-1">Input nilai tes untuk pendaftar</p>
            </div>
            <a href="{{ route('admin.psb.applicants.show', $applicant) }}" class="px-5 py-2.5 bg-white/20 hover:bg-white/30 text-white rounded-xl font-semibold transition flex items-center gap-2 text-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Student Info Card --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-gradient-to-br from-teal-400 to-emerald-500 rounded-xl flex items-center justify-center text-white text-xl font-bold">
                {{ strtoupper(substr($applicant->full_name, 0, 2)) }}
            </div>
            <div class="flex-1">
                <h2 class="text-lg font-bold text-gray-800">{{ $applicant->full_name }}</h2>
                <p class="text-xs text-gray-500">{{ $applicant->school->name }} &bull; {{ $applicant->academicYear->year }}</p>
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-semibold">
                        <i class="fas fa-clipboard mr-1"></i>{{ $applicant->registration_number }}
                    </span>
                    <span class="text-[10px] bg-purple-100 text-purple-700 px-2 py-0.5 rounded-full font-semibold">
                        <i class="fas fa-id-badge mr-1"></i>{{ $applicant->nisn }}
                    </span>
                    <span class="text-[10px] bg-{{ $applicant->getStatusBadgeColor() }}-100 text-{{ $applicant->getStatusBadgeColor() }}-700 px-2 py-0.5 rounded-full font-semibold">
                        {{ $applicant->getStatusLabel() }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Input Score Form --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-5 flex items-center gap-2">
            <i class="fas fa-edit text-teal-600"></i> Form Input Nilai
        </h3>

        <form action="{{ route('admin.psb.applicants.save-score', $applicant) }}" method="POST" id="scoreForm">
            @csrf

            {{-- Test Date --}}
            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-600 mb-1">
                    Tanggal Tes <span class="text-red-500">*</span>
                </label>
                <input type="date" name="test_date" required 
                    class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                    value="{{ old('test_date', date('Y-m-d')) }}">
                @error('test_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Score Inputs --}}
            <div class="mb-5">
                <h4 class="text-sm font-bold text-gray-800 mb-3">Nilai Mata Pelajaran</h4>
                
                <div class="space-y-4" id="scoreContainer">
                    {{-- Mathematics --}}
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Matematika</label>
                            <input type="hidden" name="scores[0][subject]" value="Matematika">
                            <input type="number" name="scores[0][score]" min="0" max="100" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="0-100">
                        </div>
                    </div>

                    {{-- Indonesian --}}
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Bahasa Indonesia</label>
                            <input type="hidden" name="scores[1][subject]" value="Bahasa Indonesia">
                            <input type="number" name="scores[1][score]" min="0" max="100" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="0-100">
                        </div>
                    </div>

                    {{-- English --}}
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Bahasa Inggris</label>
                            <input type="hidden" name="scores[2][subject]" value="Bahasa Inggris">
                            <input type="number" name="scores[2][score]" min="0" max="100" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="0-100">
                        </div>
                    </div>

                    {{-- Science --}}
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-medium text-gray-600 mb-1">IPA</label>
                            <input type="hidden" name="scores[3][subject]" value="IPA">
                            <input type="number" name="scores[3][score]" min="0" max="100" step="0.01" required
                                class="w-full px-3 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                                placeholder="0-100">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Total & Average Display --}}
            <div class="bg-teal-50 rounded-xl p-4 mb-5 border border-teal-100">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Total Nilai</p>
                        <p class="text-xl font-bold text-teal-600" id="totalScore">0</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-600 mb-1">Rata-rata</p>
                        <p class="text-xl font-bold text-teal-600" id="averageScore">0</p>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div class="flex items-center gap-3">
                <button type="submit" class="flex-1 px-5 py-3 bg-gradient-to-r from-teal-600 to-emerald-600 text-white rounded-xl font-bold hover:shadow-lg transition-all text-sm">
                    <i class="fas fa-save mr-2"></i>Simpan Nilai
                </button>
                <a href="{{ route('admin.psb.applicants.show', $applicant) }}" class="px-5 py-3 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-all text-sm">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    // Calculate total and average in real-time
    document.addEventListener('DOMContentLoaded', function() {
        const scoreInputs = document.querySelectorAll('input[name*="[score]"]');
        const totalDisplay = document.getElementById('totalScore');
        const averageDisplay = document.getElementById('averageScore');

        function calculateScores() {
            let total = 0;
            let count = 0;

            scoreInputs.forEach(input => {
                const value = parseFloat(input.value) || 0;
                total += value;
                if (input.value) count++;
            });

            const average = count > 0 ? (total / scoreInputs.length).toFixed(2) : 0;

            totalDisplay.textContent = total.toFixed(2);
            averageDisplay.textContent = average;
        }

        scoreInputs.forEach(input => {
            input.addEventListener('input', calculateScores);
        });
    });
</script>
@endsection
