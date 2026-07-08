@extends('layouts.admin')

@section('title', 'Pengaturan Konversi Predikat Rapor')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-500 to-indigo-600 shadow-lg">
                    <i class="fas fa-graduation-cap text-white text-3xl"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Pengaturan Konversi Predikat Rapor</h1>
                    <p class="text-gray-600 mt-1">Konfigurasi rumus konversi nilai angka ke predikat (A, B, C, D) per tingkat kelas</p>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-xl">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <p class="text-green-700 font-medium">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 rounded-xl">
        <ul class="list-disc list-inside text-red-700">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.settings.report-cards.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-8">
            <!-- Visibilitas Rapor untuk Siswa & Orang Tua -->
            <div class="bg-white rounded-2xl shadow-lg p-6 space-y-4">
                <h2 class="text-xl font-bold text-gray-950 border-b pb-3 flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-indigo-600 rounded-full"></span>
                    Pengaturan Visibilitas Rapor
                </h2>
                <div class="flex items-start gap-3 bg-gray-50 border border-gray-100 rounded-xl p-4">
                    <div class="flex items-center h-5">
                        <input type="checkbox" name="show_report_card" value="1" {{ $showReportCard ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-900">Tampilkan Rapor Digital ke Siswa & Orang Tua</label>
                        <p class="text-xs text-gray-500 mt-1 leading-normal">
                            Jika di-checklist, Siswa dan Orang Tua dapat melihat nilai Akhir, Predikat, Rata-rata Keseluruhan, serta tombol download/cetak Rapor Digital pada portal mereka. Jika tidak di-checklist, komponen rapor tersebut akan disembunyikan dan digantikan dengan Grafik Analisa Progress Akademik.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Jenjang SMP -->
            <div class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
                <h2 class="text-xl font-bold text-gray-950 border-b pb-3 flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-indigo-600 rounded-full"></span>
                    Unit Sekolah Menengah Pertama (SMP)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach(['7' => 'Kelas VII', '8' => 'Kelas VIII', '9' => 'Kelas IX'] as $level => $label)
                        @php
                            $lvlSetting = $settings[$level] ?? ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70];
                        @endphp
                        <div class="border rounded-2xl p-5 bg-gray-50/50 space-y-4 shadow-sm">
                            <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                <i class="fas fa-layer-group text-indigo-500"></i>
                                {{ $label }}
                            </h3>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Konversi</label>
                                <select name="grade[{{ $level }}][mode]" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm transition-all mode-selector" data-level="{{ $level }}">
                                    <option value="kkm_interval" {{ $lvlSetting['mode'] === 'kkm_interval' ? 'selected' : '' }}>Interval KKM (Dinamis)</option>
                                    <option value="static" {{ $lvlSetting['mode'] === 'static' ? 'selected' : '' }}>Batas Nilai Statis</option>
                                </select>
                            </div>

                            <div class="space-y-3 static-fields" id="static-fields-{{ $level }}" style="{{ $lvlSetting['mode'] === 'kkm_interval' ? 'display: none;' : '' }}">
                                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Batas Nilai Predikat</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Min A</label>
                                        <input type="number" name="grade[{{ $level }}][static_a]" value="{{ $lvlSetting['static_a'] }}" min="0" max="100" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 text-sm text-center">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Min B</label>
                                        <input type="number" name="grade[{{ $level }}][static_b]" value="{{ $lvlSetting['static_b'] }}" min="0" max="100" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 text-sm text-center">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Min C</label>
                                        <input type="number" name="grade[{{ $level }}][static_c]" value="{{ $lvlSetting['static_c'] }}" min="0" max="100" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 text-sm text-center">
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 leading-normal">Predikat D otomatis diberikan untuk nilai di bawah batas C.</p>
                            </div>

                            <div class="dynamic-info text-xs text-gray-500 leading-relaxed bg-indigo-50/50 border border-indigo-100 rounded-xl p-3" id="dynamic-info-{{ $level }}" style="{{ $lvlSetting['mode'] === 'static' ? 'display: none;' : '' }}">
                                <i class="fas fa-info-circle text-indigo-500 mr-1"></i>
                                Rentang predikat akan dihitung dinamis dari KKM mata pelajaran:
                                <div class="mt-2 space-y-1 text-[11px] font-medium text-indigo-900 list-disc list-inside">
                                    <div>A: &ge; 100 - Interval</div>
                                    <div>B: &ge; 100 - 2 * Interval</div>
                                    <div>C: &ge; KKM</div>
                                    <div>D: &lt; KKM</div>
                                    <div class="text-[10px] text-indigo-600 mt-1 font-normal">* Interval = (100 - KKM) / 3</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Jenjang SMA/SMK -->
            <div class="bg-white rounded-2xl shadow-lg p-6 space-y-6">
                <h2 class="text-xl font-bold text-gray-950 border-b pb-3 flex items-center gap-2">
                    <span class="w-2.5 h-6 bg-indigo-600 rounded-full"></span>
                    Unit Sekolah Menengah Atas / Kejuruan (SMA/SMK)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach(['10' => 'Kelas X', '11' => 'Kelas XI', '12' => 'Kelas XII'] as $level => $label)
                        @php
                            $lvlSetting = $settings[$level] ?? ['mode' => 'kkm_interval', 'static_a' => 90, 'static_b' => 80, 'static_c' => 70];
                        @endphp
                        <div class="border rounded-2xl p-5 bg-gray-50/50 space-y-4 shadow-sm">
                            <h3 class="font-bold text-gray-900 text-lg flex items-center gap-2">
                                <i class="fas fa-layer-group text-indigo-500"></i>
                                {{ $label }}
                            </h3>
                            
                            <div>
                                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Metode Konversi</label>
                                <select name="grade[{{ $level }}][mode]" class="w-full px-3 py-2 bg-white border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:outline-none text-sm transition-all mode-selector" data-level="{{ $level }}">
                                    <option value="kkm_interval" {{ $lvlSetting['mode'] === 'kkm_interval' ? 'selected' : '' }}>Interval KKM (Dinamis)</option>
                                    <option value="static" {{ $lvlSetting['mode'] === 'static' ? 'selected' : '' }}>Batas Nilai Statis</option>
                                </select>
                            </div>

                            <div class="space-y-3 static-fields" id="static-fields-{{ $level }}" style="{{ $lvlSetting['mode'] === 'kkm_interval' ? 'display: none;' : '' }}">
                                <span class="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Batas Nilai Predikat</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Min A</label>
                                        <input type="number" name="grade[{{ $level }}][static_a]" value="{{ $lvlSetting['static_a'] }}" min="0" max="100" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 text-sm text-center">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Min B</label>
                                        <input type="number" name="grade[{{ $level }}][static_b]" value="{{ $lvlSetting['static_b'] }}" min="0" max="100" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 text-sm text-center">
                                    </div>
                                    <div>
                                        <label class="block text-xs text-gray-500 mb-1">Min C</label>
                                        <input type="number" name="grade[{{ $level }}][static_c]" value="{{ $lvlSetting['static_c'] }}" min="0" max="100" class="w-full px-2 py-1.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-indigo-500 text-sm text-center">
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 leading-normal">Predikat D otomatis diberikan untuk nilai di bawah batas C.</p>
                            </div>

                            <div class="dynamic-info text-xs text-gray-500 leading-relaxed bg-indigo-50/50 border border-indigo-100 rounded-xl p-3" id="dynamic-info-{{ $level }}" style="{{ $lvlSetting['mode'] === 'static' ? 'display: none;' : '' }}">
                                <i class="fas fa-info-circle text-indigo-500 mr-1"></i>
                                Rentang predikat akan dihitung dinamis dari KKM mata pelajaran:
                                <div class="mt-2 space-y-1 text-[11px] font-medium text-indigo-900 list-disc list-inside">
                                    <div>A: &ge; 100 - Interval</div>
                                    <div>B: &ge; 100 - 2 * Interval</div>
                                    <div>C: &ge; KKM</div>
                                    <div>D: &lt; KKM</div>
                                    <div class="text-[10px] text-indigo-600 mt-1 font-normal">* Interval = (100 - KKM) / 3</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 bg-white p-4 rounded-2xl shadow-md">
                <a href="{{ route('admin.settings.index') }}" class="px-5 py-2.5 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 font-medium text-sm transition-all">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-medium text-sm shadow-md transition-all flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Simpan Pengaturan
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectors = document.querySelectorAll('.mode-selector');
        
        selectors.forEach(selector => {
            selector.addEventListener('change', function() {
                const level = this.dataset.level;
                const mode = this.value;
                const staticFields = document.getElementById(`static-fields-${level}`);
                const dynamicInfo = document.getElementById(`dynamic-info-${level}`);
                
                if (mode === 'static') {
                    staticFields.style.display = 'block';
                    dynamicInfo.style.display = 'none';
                } else {
                    staticFields.style.display = 'none';
                    dynamicInfo.style.display = 'block';
                }
            });
        });
    });
</script>
@endsection
