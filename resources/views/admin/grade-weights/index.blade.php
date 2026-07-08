@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center gap-4 mb-6">
        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
            <i class="fas fa-balance-scale text-2xl text-white"></i>
        </div>
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Konfigurasi Bobot Nilai</h1>
            <p class="text-gray-600">Atur bobot komponen nilai per unit sekolah. Total harus 100%.</p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-check-circle text-green-500 mr-3"></i>
            <span class="text-green-700 font-medium">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
        <div class="flex items-start">
            <i class="fas fa-exclamation-circle text-red-500 mr-3 mt-0.5"></i>
            <ul class="list-disc list-inside text-red-700">
                @foreach($errors->all() as $err)
                <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Info Box --}}
    <div class="mb-6 bg-blue-50 rounded-xl border border-blue-200 p-5">
        <div class="flex items-start gap-3">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 text-lg"></i>
            <div class="text-sm text-blue-700">
                <p class="font-semibold mb-1">Tentang Bobot Nilai</p>
                <ul class="list-disc list-inside space-y-1">
                    <li><strong>Tugas/Harian</strong> — Nilai tugas, kuis, PR, latihan (termasuk nilai LMS quiz & assignment)</li>
                    <li><strong>PTS (Penilaian Tengah Semester)</strong> — Ujian Tengah Semester / UTS</li>
                    <li><strong>PAS (Penilaian Akhir Semester)</strong> — Ujian Akhir Semester / UAS</li>
                    <li><strong>Sikap</strong> — Penilaian sikap, keaktifan, disiplin</li>
                    <li>Rumus: <code class="bg-blue-100 px-1 rounded">Nilai Akhir = (Tugas × Bobot%) + (PTS × Bobot%) + (PAS × Bobot%) + (Sikap × Bobot%)</code></li>
                </ul>
            </div>
        </div>
    </div>

    {{-- School Weight Cards --}}
    <div class="space-y-6">
        @foreach($schoolWeights as $sw)
        @php $school = $sw['school']; $weights = $sw['weights']; @endphp
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-white">{{ $school->name }}</h2>
                    <p class="text-blue-100 text-sm">{{ $school->type ?? '' }} · {{ $school->city ?? '' }}</p>
                </div>
                <span class="bg-white/20 text-white text-xs px-3 py-1 rounded-full font-medium">
                    {{ $weights->description ?? 'Custom' }}
                </span>
            </div>

            <form action="{{ route('admin.grade-weights.update', $school->id) }}" method="POST" class="p-6" onsubmit="return validateWeights(this)">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    {{-- Tugas --}}
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-pencil-alt text-emerald-500 mr-1"></i> Tugas/Harian
                        </label>
                        <div class="relative">
                            <input type="number" name="tugas_weight" value="{{ $weights->tugas_weight }}" 
                                   min="0" max="100" step="0.01"
                                   class="w-full text-center text-2xl font-bold border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-emerald-300 focus:border-emerald-500 weight-input"
                                   data-school="{{ $school->id }}">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                    </div>

                    {{-- PTS --}}
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-file-alt text-blue-500 mr-1"></i> PTS (UTS)
                        </label>
                        <div class="relative">
                            <input type="number" name="pts_weight" value="{{ $weights->pts_weight }}" 
                                   min="0" max="100" step="0.01"
                                   class="w-full text-center text-2xl font-bold border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-300 focus:border-blue-500 weight-input"
                                   data-school="{{ $school->id }}">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                    </div>

                    {{-- PAS --}}
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-file-signature text-purple-500 mr-1"></i> PAS (UAS)
                        </label>
                        <div class="relative">
                            <input type="number" name="pas_weight" value="{{ $weights->pas_weight }}" 
                                   min="0" max="100" step="0.01"
                                   class="w-full text-center text-2xl font-bold border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-purple-300 focus:border-purple-500 weight-input"
                                   data-school="{{ $school->id }}">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                    </div>

                    {{-- Sikap --}}
                    <div class="text-center">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-star text-amber-500 mr-1"></i> Sikap
                        </label>
                        <div class="relative">
                            <input type="number" name="sikap_weight" value="{{ $weights->sikap_weight }}" 
                                   min="0" max="100" step="0.01"
                                   class="w-full text-center text-2xl font-bold border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-amber-300 focus:border-amber-500 weight-input"
                                   data-school="{{ $school->id }}">
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">%</span>
                        </div>
                    </div>
                </div>

                {{-- Total indicator --}}
                <div class="text-center mb-4">
                    <span class="text-sm text-gray-500">Total:</span>
                    <span class="text-lg font-bold total-display" id="total-{{ $school->id }}" 
                          data-school="{{ $school->id }}">100%</span>
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan (opsional)</label>
                    <input type="text" name="description" value="{{ $weights->description }}" 
                           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-300"
                           placeholder="Mis: Kurikulum Merdeka 2025/2026">
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg transition">
                        <i class="fas fa-save mr-2"></i> Simpan Bobot
                    </button>
                    <a href="{{ route('admin.grade-weights.reset', $school->id) }}" 
                       onclick="return confirm('Reset bobot ke default (20-30-40-10)?')"
                       class="px-6 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-xl font-semibold transition text-center">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </a>
                </div>
            </form>
        </div>
        @endforeach
    </div>
</div>

<script>
    // Real-time total calculation
    document.querySelectorAll('.weight-input').forEach(input => {
        input.addEventListener('input', function() {
            const schoolId = this.dataset.school;
            const form = this.closest('form');
            const inputs = form.querySelectorAll('.weight-input');
            let total = 0;
            inputs.forEach(i => total += parseFloat(i.value) || 0);
            
            const display = document.getElementById('total-' + schoolId);
            display.textContent = total.toFixed(1) + '%';
            display.classList.remove('text-green-600', 'text-red-600');
            display.classList.add(Math.abs(total - 100) < 0.01 ? 'text-green-600' : 'text-red-600');
        });
    });

    // Validate before submit
    function validateWeights(form) {
        const inputs = form.querySelectorAll('.weight-input');
        let total = 0;
        inputs.forEach(i => total += parseFloat(i.value) || 0);
        if (Math.abs(total - 100) > 0.01) {
            alert('Total bobot harus 100%! Saat ini: ' + total.toFixed(1) + '%');
            return false;
        }
        return true;
    }

    // Initialize totals
    document.querySelectorAll('.weight-input').forEach(input => input.dispatchEvent(new Event('input')));
</script>
@endsection
