@extends(auth()->user()->isKetuaYayasan() ? 'layouts.yayasan' : 'layouts.admin')

@section('title', 'Form Evaluasi Kinerja')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-2">
                <a href="{{ route((auth()->user()->isKetuaYayasan() && request()->routeIs('yayasan.*') ? 'yayasan.' : 'admin.') . 'performance_evaluations.index') }}" class="hover:text-indigo-600 transition-colors">Evaluasi Kinerja</a>
                <i class="fas fa-chevron-right text-xs"></i>
                <span class="text-gray-900 font-medium">Penilaian</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Form Penilaian Evaluasi</h1>
            <p class="text-gray-600 mt-1">Isi nilai evaluasi (Skala 1-5) untuk setiap butir target yang telah dijanjikan.</p>
        </div>
        <div class="bg-indigo-50 border border-indigo-100 rounded-lg px-4 py-2 text-sm">
            <span class="text-indigo-800 font-medium block text-right">Semester Dinilai:</span>
            <span class="text-indigo-900 font-bold">{{ $semester->name }} ({{ $semester->academicYear->name ?? '' }})</span>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terdapat kesalahan pengisian:</h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informasi Guru -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="h-16 w-16 bg-indigo-100 rounded-full flex items-center justify-center border-4 border-white shadow-sm">
                            <span class="text-indigo-600 font-bold text-xl">{{ substr($contract->employee->full_name, 0, 2) }}</span>
                        </div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900">{{ $contract->employee->full_name }}</h2>
                            <p class="text-sm text-gray-500">{{ $contract->employee->employee_code }}</p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Tipe Kontrak</p>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                @if($contract->contract_type === 'jabatan_tambahan')
                                    Jabatan Tambahan ({{ $contract->position->position_name ?? '-' }})
                                @elseif($contract->contract_type === 'pkg_kejuruan')
                                    Tugas Utama (Kejuruan/Produktif)
                                @else
                                    Tugas Utama (Mapel Umum)
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Sekolah</p>
                            <p class="text-sm font-medium text-gray-900 bg-gray-50 p-2 rounded-lg border border-gray-100">
                                {{ $contract->school->name ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Status Evaluasi Terkini</p>
                            <div>
                                @if($evaluation->status === 'draft')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Draft (Kepsek)</span>
                                @elseif($evaluation->status === 'submitted_to_yayasan')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Menunggu Yayasan</span>
                                @elseif($evaluation->status === 'approved_by_yayasan')
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Final (ACC Yayasan)</span>
                                @else
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Belum Disimpan</span>
                                @endif
                            </div>
                        </div>
                        
                        @if($evaluation->score > 0)
                        <div class="mt-6 pt-6 border-t border-gray-200 text-center">
                            <p class="text-sm text-gray-500 font-medium mb-1">Rata-rata Nilai Evaluasi</p>
                            <div class="text-4xl font-black text-indigo-600">{{ number_format($evaluation->score, 2) }}</div>
                            <div class="flex justify-center mt-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= round($evaluation->score) ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                        </div>

                        @if(is_array($evaluation->evaluation_data) && !empty($evaluation->evaluation_data))
                            @php
                                $score = $evaluation->score;
                                $evalData = $evaluation->evaluation_data;
                                $minScore = !empty($evalData) ? min($evalData) : 0;
                                $maxScore = !empty($evalData) ? max($evalData) : 0;
                                
                                $lowestKeys = array_keys($evalData, $minScore);
                                $highestKeys = array_keys($evalData, $maxScore);
                                
                                $getKeyLabel = function($k) {
                                    if ($k === 'pilar_1') return 'Kompetensi Praktik';
                                    if ($k === 'pilar_2') return 'Kontribusi Program';
                                    if ($k === 'pilar_3') return 'Kolaborasi';
                                    if ($k === 'pilar_4') return 'Budaya 5R / K3';
                                    if (str_starts_with($k, 'target_')) return 'Target ' . substr($k, 7);
                                    return ucwords(str_replace('_', ' ', $k));
                                };
                                
                                $lowestLabel = $getKeyLabel($lowestKeys[0] ?? '');
                                $highestLabel = $getKeyLabel($highestKeys[0] ?? '');

                                if ($score >= 4.0) {
                                    $analisa = "Kinerja sangat konsisten dan unggul. Keunggulan utama pada aspek {$highestLabel} ({$maxScore}/5). Layak dipertahankan sebagai rol model.";
                                } elseif ($score >= 3.5) {
                                    if ($minScore < 3.0) {
                                        $analisa = "Memenuhi syarat rata-rata SK (> 3.5), namun perlu perhatian khusus pada peningkatan aspek {$lowestLabel} ({$minScore}/5).";
                                    } else {
                                        $analisa = "Kinerja stabil dan memenuhi target di seluruh pilar. Paling menonjol pada aspek {$highestLabel} ({$maxScore}/5).";
                                    }
                                } elseif ($score >= 2.5) {
                                    $analisa = "Kinerja dalam tahap cukup. Diperlukan pembinaan dan pendampingan intensif khususnya pada aspek {$lowestLabel} ({$minScore}/5).";
                                } else {
                                    $analisa = "Kinerja berada di bawah target yang disepakati. Evaluasi menyeluruh dan evaluasi pembinaan diperlukan pada aspek {$lowestLabel}.";
                                }
                            @endphp
                            <div class="mt-4 p-3.5 bg-indigo-50/70 border border-indigo-100 rounded-xl text-left text-xs text-slate-700 space-y-2">
                                <div class="font-bold text-indigo-900 flex items-center gap-1.5">
                                    <i class="fas fa-chart-pie text-indigo-600"></i> Analisis Deskriptif Pilar
                                </div>
                                <div class="leading-relaxed text-slate-700">{{ $analisa }}</div>
                            </div>
                        @endif
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-5 shadow-sm">
                <h4 class="font-bold text-blue-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-info-circle"></i> Panduan Skala Nilai
                </h4>
                <ul class="text-sm text-blue-800 space-y-2">
                    <li><span class="font-bold bg-white px-2 py-0.5 rounded text-blue-900 shadow-sm mr-2">5</span> Sangat Baik (Melampaui Target)</li>
                    <li><span class="font-bold bg-white px-2 py-0.5 rounded text-blue-900 shadow-sm mr-2">4</span> Baik (Sesuai Target)</li>
                    <li><span class="font-bold bg-white px-2 py-0.5 rounded text-blue-900 shadow-sm mr-2">3</span> Cukup (Hampir Sesuai Target)</li>
                    <li><span class="font-bold bg-white px-2 py-0.5 rounded text-blue-900 shadow-sm mr-2">2</span> Kurang (Jauh dari Target)</li>
                    <li><span class="font-bold bg-white px-2 py-0.5 rounded text-blue-900 shadow-sm mr-2">1</span> Sangat Kurang (Tidak Dikerjakan)</li>
                </ul>
            </div>
        </div>

        <!-- Form Penilaian -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <form action="{{ route((auth()->user()->isKetuaYayasan() && request()->routeIs('yayasan.*') ? 'yayasan.' : 'admin.') . 'performance_evaluations.store', [$contract->id, $semester->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" id="form_action_input" name="action" value="{{ auth()->user()->isKetuaYayasan() ? 'approve_yayasan' : 'submit_yayasan' }}">
                    
                    @php
                        $isReadOnly = false;
                        $showAccButton = false;
                        if (auth()->user()->isKetuaYayasan()) {
                            // Yayasan TIDAK menilai dari nol. Yayasan hanya meninjau dan menyetujui (ACC) hasil penilaian Kepsek.
                            $isReadOnly = true; 
                            if ($evaluation->status === 'submitted_to_yayasan' && $evaluation->score > 0) {
                                $showAccButton = true;
                            }
                        } else {
                            // Kepala Sekolah / Admin adalah pihak yang berwenang menilai kinerja
                            if (in_array($evaluation->status, ['submitted_to_yayasan', 'approved_by_yayasan'])) {
                                $isReadOnly = true; // Kepsek tidak bisa ubah lagi jika sudah diajukan/di-ACC
                            }
                        }
                    @endphp
                    
                    @if(auth()->user()->isKetuaYayasan())
                        @if($evaluation->status !== 'submitted_to_yayasan' && $evaluation->status !== 'approved_by_yayasan')
                            <div class="p-4 bg-amber-50 border-b border-amber-200 text-amber-800 text-sm flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle text-amber-600 text-lg shrink-0"></i>
                                <div>
                                    <span class="font-bold">Menunggu Penilaian Kepala Sekolah:</span>
                                    <span>Kewenangan menilai kinerja (skor 1-5 & umpan balik) berada pada Kepala Sekolah. Saat ini Kepala Sekolah belum mengajukan penilaian untuk guru ini. Yayasan dapat meninjau dan menyetujui (ACC) setelah hasil evaluasi diajukan.</span>
                                </div>
                            </div>
                        @elseif($evaluation->status === 'submitted_to_yayasan')
                            <div class="p-4 bg-indigo-50 border-b border-indigo-200 text-indigo-900 text-sm flex items-center gap-3">
                                <i class="fas fa-info-circle text-indigo-600 text-lg shrink-0"></i>
                                <div>
                                    <span class="font-bold">Penilaian Diajukan oleh Kepala Sekolah:</span>
                                    <span>Berikut adalah rincian skor dan analisis penilaian dari Kepala Sekolah. Silakan periksa dan klik tombol <b>Setujui & ACC Hasil Penilaian Kepala Sekolah</b> di bawah agar hasil evaluasi resmi diterbitkan ke layar Guru yang bersangkutan.</span>
                                </div>
                            </div>
                        @elseif($evaluation->status === 'approved_by_yayasan')
                            <div class="p-4 bg-emerald-50 border-b border-emerald-200 text-emerald-900 text-sm flex items-center gap-3">
                                <i class="fas fa-check-circle text-emerald-600 text-lg shrink-0"></i>
                                <div>
                                    <span class="font-bold">Sudah Disetujui (Final ACC Yayasan):</span>
                                    <span>Hasil penilaian Kepala Sekolah ini telah disetujui oleh Yayasan dan saat ini sudah dapat dilihat secara transparan di portal Guru yang bersangkutan.</span>
                                </div>
                            </div>
                        @endif
                    @endif

                    <div class="p-0">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700 w-2/3">Indikator & Target yang Dijanjikan</th>
                                    <th class="px-6 py-4 text-center text-sm font-bold text-indigo-700 w-1/3 border-l border-gray-200">Nilai Evaluasi (1-5)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($contract->target_data as $key => $targetValue)
                                    @php
                                        // Bersihkan key agar tampil lebih ramah (ubah underscore jadi spasi, uppercase kata pertama)
                                        $displayKey = ucwords(str_replace('_', ' ', $key));
                                        $currentScore = isset($evaluation->evaluation_data[$key]) ? $evaluation->evaluation_data[$key] : '';
                                    @endphp
                                    <tr class="hover:bg-indigo-50/30 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="text-sm font-bold text-gray-800 mb-1">{{ $displayKey }}</p>
                                            <p class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 leading-relaxed">{{ is_array($targetValue) ? implode(', ', $targetValue) : $targetValue }}</p>
                                        </td>
                                        <td class="px-6 py-4 border-l border-gray-200 bg-gray-50/50">
                                            <div class="flex items-center justify-center">
                                                <input type="number" 
                                                       name="scores[{{ $key }}]" 
                                                       value="{{ old('scores.'.$key, $currentScore) }}" 
                                                       min="1" max="5" 
                                                       required
                                                       {{ $isReadOnly ? 'readonly' : '' }}
                                                       class="w-20 text-center font-bold text-lg border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 {{ $isReadOnly ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : 'text-indigo-700' }}">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="p-6 border-t border-gray-200 bg-gray-50">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Catatan Evaluasi / Umpan Balik (Opsional)</label>
                        <textarea name="notes" rows="3" {{ $isReadOnly ? 'readonly' : '' }} class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm {{ $isReadOnly ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" placeholder="Berikan catatan terhadap pencapaian target ini...">{{ old('notes', $evaluation->notes) }}</textarea>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-between">
                        <a href="{{ route((auth()->user()->isKetuaYayasan() && request()->routeIs('yayasan.*') ? 'yayasan.' : 'admin.') . 'performance_evaluations.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Batal & Kembali
                        </a>
                        <div class="flex gap-3">
                            @if(auth()->user()->isKetuaYayasan())
                                @if($showAccButton)
                                    <button type="submit" onclick="document.getElementById('form_action_input').value='approve_yayasan'" class="px-6 py-2.5 border border-transparent text-sm font-bold rounded-xl shadow-md text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-all flex items-center gap-2">
                                        <i class="fas fa-check-double"></i> Setujui & ACC Hasil Penilaian Kepala Sekolah
                                    </button>
                                @endif
                            @else
                                @if(!$isReadOnly)
                                    <button type="submit" onclick="document.getElementById('form_action_input').value='draft'" class="px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                                        Simpan Draft
                                    </button>
                                    <button type="submit" onclick="document.getElementById('form_action_input').value='submit_yayasan'" class="px-6 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <i class="fas fa-paper-plane mr-1"></i> Ajukan ke Yayasan
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
