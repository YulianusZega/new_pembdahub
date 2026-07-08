@extends('layouts.admin')

@section('title', 'Atur PSB: ' . $school->name)

@section('content')
@php
    $colorClass = match(strtoupper($school->type)) {
        'SMP' => 'from-blue-600 to-cyan-600',
        'SMA' => 'from-purple-600 to-indigo-700',
        'SMK' => 'from-orange-600 to-red-600',
        default => 'from-gray-600 to-gray-700'
    };
    $docs = $school->getAllDocumentTypes();
    $requiredDocs = is_array($school->psb_required_documents) ? $school->psb_required_documents : [];
    $allDocEntries = $school->psb_custom_document_types ?? [];
@endphp

<div class="space-y-5">
    {{-- Top Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.psb.settings.index') }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-xl text-xs font-bold uppercase tracking-widest hover:bg-gray-50 transition flex items-center gap-2">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <span class="px-4 py-2 bg-slate-100 text-slate-500 rounded-xl text-[10px] font-bold uppercase tracking-widest border border-slate-200">
            ID #{{ $school->id }}
        </span>
    </div>

    {{-- Alert --}}
    @if(session('success'))
    <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex items-center gap-3 shadow-sm">
        <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
        <p class="text-sm font-bold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    {{-- School Header (Compact) --}}
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex flex-col sm:flex-row items-center gap-5">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br {{ $colorClass }} text-white flex items-center justify-center text-2xl font-bold shadow-lg flex-shrink-0">
            {{ substr($school->name, 0, 1) }}
        </div>
        <div class="flex-1 text-center sm:text-left">
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-1">
                <h1 class="text-xl font-bold text-gray-900">{{ $school->name }}</h1>
                <span class="w-fit mx-auto sm:mx-0 px-2.5 py-0.5 bg-blue-50 text-blue-600 rounded-full text-[9px] font-bold uppercase tracking-widest border border-blue-100">{{ $school->type }}</span>
            </div>
            <p class="text-xs text-slate-400 font-bold">{{ $school->city }}, {{ $school->province }} · {{ $school->phone ?? '-' }}</p>
        </div>
    </div>

    {{-- ═══════════ MAIN SETTINGS FORM ═══════════ --}}
    <form action="{{ route('admin.psb.settings.update', $school->id) }}" method="POST" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            {{-- Section Header: Status --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center text-xs"><i class="fas fa-power-off"></i></div>
                    <span class="text-sm font-bold text-gray-900">Status & Identitas PSB</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="psb_is_active" value="1" {{ $school->psb_is_active ? 'checked' : '' }} class="sr-only peer">
                    <div class="w-12 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-[18px] after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    <span class="ml-2 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $school->psb_is_active ? 'Aktif' : 'Non-Aktif' }}</span>
                </label>
            </div>

            {{-- Kontak & Sekretariat --}}
            <div class="p-6 space-y-5 border-b border-slate-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Kontak Person (WA)</label>
                        <input type="text" name="psb_contact_person" value="{{ $school->psb_contact_person }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-sm text-gray-800" placeholder="Admin PSB {{ $school->type }}">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp Panitia</label>
                        <input type="text" name="psb_contact_phone" value="{{ $school->psb_contact_phone }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-sm text-gray-800" placeholder="081234567890">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Jam Operasional</label>
                        <textarea name="psb_opening_hours" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-sm text-gray-800" placeholder="Senin - Jumat: 08:00 - 14:00">{{ $school->psb_opening_hours }}</textarea>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Lokasi Sekretariat PSB</label>
                        <textarea name="psb_secretariat" rows="2" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-bold text-sm text-gray-800" placeholder="Gedung A Lt. 1, Ruang Panitia PSB">{{ $school->psb_secretariat }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Proses Seleksi --}}
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs"><i class="fas fa-user-check"></i></div>
                    <span class="text-sm font-bold text-gray-900">Proses Seleksi</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <label class="flex items-center gap-3 px-4 py-3 bg-slate-50 rounded-xl cursor-pointer border border-slate-200 hover:border-emerald-300 transition-all flex-1">
                        <input type="checkbox" name="requires_test" value="1" {{ $school->requires_test ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                        <div>
                            <p class="text-sm font-bold text-slate-700">Wajib Tes Masuk (CBT)</p>
                        </div>
                    </label>
                    <div class="flex-1 space-y-1.5">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tipe / Nama Tes</label>
                        <input type="text" name="test_type" value="{{ $school->test_type }}" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-500 transition-all font-bold text-sm" placeholder="Tes Akademik & Wawancara">
                    </div>
                </div>
            </div>

            {{-- Dokumen Wajib --}}
            <div class="p-6 border-b border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-orange-500 text-white flex items-center justify-center text-xs"><i class="fas fa-file-contract"></i></div>
                        <span class="text-sm font-bold text-gray-900">Dokumen Wajib Upload</span>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400">{{ count($requiredDocs) }} dipilih</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                    @foreach($docs as $key => $label)
                    <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 hover:bg-slate-100 rounded-xl transition-all border border-transparent hover:border-orange-200 group/doc">
                        <label class="flex items-center gap-3 flex-1 cursor-pointer">
                            <input type="checkbox" name="psb_required_documents[]" value="{{ $key }}" {{ in_array($key, $requiredDocs) ? 'checked' : '' }} class="w-4 h-4 text-orange-600 border-gray-300 rounded focus:ring-orange-500 flex-shrink-0">
                            <span class="text-sm font-bold text-slate-700">{{ $label }}</span>
                        </label>
                        <button type="button" onclick="if(confirm('Hapus dokumen: {{ $label }}?')) { document.getElementById('delete-doc-{{ $key }}').submit(); }" 
                                class="w-6 h-6 rounded bg-red-50 text-red-400 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all text-[10px] opacity-0 group-hover/doc:opacity-100 flex-shrink-0">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @endforeach
                </div>
                {{-- Inline Add --}}
                <div class="flex gap-2 mt-3">
                    <input type="text" id="custom_doc_label_input" placeholder="Tambah dokumen baru, contoh: Surat Keterangan Sehat" 
                           class="flex-1 px-4 py-2.5 bg-white border border-dashed border-slate-300 rounded-xl focus:border-orange-500 focus:ring-2 focus:ring-orange-500/20 transition-all font-bold text-sm text-gray-800" maxlength="100">
                    <button type="button" onclick="addCustomDocument()" 
                            class="px-4 py-2.5 bg-orange-500 text-white rounded-xl text-xs font-bold hover:bg-orange-600 transition-all flex items-center gap-2 whitespace-nowrap">
                        <i class="fas fa-plus"></i> Tambah
                    </button>
                </div>
            </div>

            {{-- Penjelasan / Berita PSB --}}
            <div class="p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-indigo-500 text-white flex items-center justify-center text-xs"><i class="fas fa-newspaper"></i></div>
                    <span class="text-sm font-bold text-gray-900">Berita & Penjelasan PSB</span>
                </div>
                <textarea name="psb_description" rows="6" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all font-medium text-sm text-gray-800 leading-relaxed" placeholder="Tuliskan informasi PSB untuk ditampilkan di halaman pendaftaran publik...">{{ $school->psb_description }}</textarea>
                <p class="text-[10px] text-slate-400 font-bold ml-1 mt-1 italic">* Ditampilkan di portal pendaftaran unit sekolah.</p>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="bg-white rounded-2xl p-4 border border-gray-100 shadow-lg flex items-center justify-between sticky bottom-4 z-40">
            <div class="hidden md:flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center"><i class="fas fa-save text-xs"></i></div>
                <div>
                    <p class="text-sm font-bold text-gray-900">Simpan Setelan</p>
                    <p class="text-[10px] font-bold text-slate-400">Semua perubahan di atas</p>
                </div>
            </div>
            <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-xl text-sm font-bold uppercase tracking-widest shadow-lg shadow-blue-200 hover:scale-[1.02] active:scale-95 transition-all flex items-center justify-center gap-2">
                <i class="fas fa-save"></i> Simpan Setelan PSB
            </button>
        </div>
    </form>

    {{-- ═══════════ KOMPONEN BIAYA (CRUD terpisah) ═══════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center text-xs"><i class="fas fa-money-bill-wave"></i></div>
                <div>
                    <span class="text-sm font-bold text-gray-900">Komponen Biaya</span>
                    <p class="text-[10px] font-bold text-slate-400">Biaya pendaftaran & daftar ulang</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modal-add-fee').classList.remove('hidden')" class="px-3 py-2 bg-emerald-600 text-white rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-emerald-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>
        <div class="p-4">
            @forelse($fees as $fee)
            <div class="flex items-center justify-between px-4 py-3 rounded-xl hover:bg-slate-50 transition-all group border-b border-slate-50 last:border-0">
                <div class="flex items-center gap-4 flex-1">
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $fee->fee_name }}</p>
                        <p class="text-[10px] text-slate-400 font-bold">{{ $fee->academicYear->year }} · <span class="uppercase">{{ $fee->fee_type }}</span>{{ $fee->description ? ' · '.$fee->description : '' }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm font-bold text-emerald-600">{{ $fee->formatted_amount }}</span>
                    <form action="{{ route('admin.psb.settings.fees.destroy', $fee->id) }}" method="POST" onsubmit="return confirm('Hapus biaya ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-7 h-7 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all opacity-0 group-hover:opacity-100 text-[10px]">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="py-8 text-center">
                <i class="fas fa-receipt text-2xl text-slate-200 mb-2 block"></i>
                <p class="text-xs font-bold text-slate-400">Belum ada komponen biaya.</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- ═══════════ GELOMBANG PENDAFTARAN (CRUD terpisah) ═══════════ --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center text-xs"><i class="fas fa-water"></i></div>
                <div>
                    <span class="text-sm font-bold text-gray-900">Gelombang Pendaftaran</span>
                    <p class="text-[10px] font-bold text-slate-400">Periode & kuota pendaftaran</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('modal-add-wave').classList.remove('hidden')" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-[10px] font-bold uppercase tracking-widest hover:bg-blue-700 transition flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah
            </button>
        </div>
        <div class="p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @forelse($waves as $wave)
                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 group hover:bg-white hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center font-bold text-blue-600 text-sm shadow-sm border border-slate-100">{{ $wave->wave_number }}</div>
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 leading-tight">{{ $wave->name }}</h4>
                                <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">{{ $wave->academicYear->year }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-1">
                            <form action="{{ route('admin.psb.settings.waves.toggle', $wave->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="px-2 py-1 rounded-full text-[8px] font-bold uppercase tracking-widest {{ $wave->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                    {{ $wave->is_active ? 'Aktif' : 'Off' }}
                                </button>
                            </form>
                            <form action="{{ route('admin.psb.settings.waves.destroy', $wave->id) }}" method="POST" onsubmit="return confirm('Hapus gelombang ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-6 h-6 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all text-[9px]">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="space-y-1.5 text-[10px]">
                        <div class="flex justify-between px-3 py-1.5 bg-white rounded-lg border border-slate-100">
                            <span class="font-bold text-slate-400"><i class="far fa-calendar-alt mr-1"></i>Periode</span>
                            <span class="font-bold text-slate-700">{{ $wave->start_date->format('d M') }} - {{ $wave->end_date->format('d M Y') }}</span>
                        </div>
                        <div class="flex justify-between px-3 py-1.5 bg-white rounded-lg border border-slate-100">
                            <span class="font-bold text-slate-400"><i class="fas fa-users mr-1"></i>Kuota</span>
                            <span class="font-bold text-slate-700">{{ $wave->registered_count }} / {{ $wave->quota ?? '∞' }}</span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-8 text-center">
                    <i class="fas fa-water text-2xl text-slate-200 mb-2 block"></i>
                    <p class="text-xs font-bold text-slate-400">Belum ada gelombang pendaftaran.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- ═══════════ FORMS OUTSIDE MAIN (delete docs, add docs) ═══════════ --}}
@foreach($allDocEntries as $doc)
<form id="delete-doc-{{ $doc['key'] }}" action="{{ route('admin.psb.settings.custom-documents.destroy', $school->id) }}" method="POST" class="hidden">
    @csrf @method('DELETE')
    <input type="hidden" name="document_key" value="{{ $doc['key'] }}">
</form>
@endforeach

<form id="add-custom-doc-form" action="{{ route('admin.psb.settings.custom-documents.store', $school->id) }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="custom_doc_label" id="custom_doc_label_hidden">
</form>

{{-- ═══════════ MODAL: TAMBAH BIAYA ═══════════ --}}
<div id="modal-add-fee" class="hidden fixed inset-0 z-[60] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60" onclick="document.getElementById('modal-add-fee').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-100">
            <form action="{{ route('admin.psb.settings.fees.store', $school->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fas fa-money-bill-transfer"></i></div>
                    <h3 class="text-lg font-bold text-gray-900">Tambah Biaya</h3>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tahun Ajaran</label>
                        <select name="academic_year_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" required>
                            @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>{{ $ay->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tipe</label>
                        <select name="fee_type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" required>
                            <option value="pendaftaran">Pendaftaran</option>
                            <option value="daftar_ulang">Daftar Ulang / Pangkal</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Biaya</label>
                    <input type="text" name="fee_name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" placeholder="Biaya Pendaftaran Gel 1" required>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nominal (Rp)</label>
                    <input type="number" name="amount" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg font-bold text-lg text-emerald-600" placeholder="0" required>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Keterangan (Opsional)</label>
                    <textarea name="description" rows="2" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" placeholder="Catatan..."></textarea>
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-fee').classList.add('hidden')" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold uppercase hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="flex-[2] py-3 bg-emerald-600 text-white rounded-xl text-xs font-bold uppercase hover:bg-emerald-700 shadow-lg shadow-emerald-200 transition flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ═══════════ MODAL: TAMBAH GELOMBANG ═══════════ --}}
<div id="modal-add-wave" class="hidden fixed inset-0 z-[60] overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="fixed inset-0 bg-slate-900/60" onclick="document.getElementById('modal-add-wave').classList.add('hidden')"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md border border-slate-100">
            <form action="{{ route('admin.psb.settings.waves.store', $school->id) }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div class="flex items-center gap-3 mb-1">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-layer-group"></i></div>
                    <h3 class="text-lg font-bold text-gray-900">Tambah Gelombang</h3>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tahun Ajaran</label>
                        <select name="academic_year_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" required>
                            @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ $ay->is_active ? 'selected' : '' }}>{{ $ay->year }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor Gel.</label>
                        <input type="number" name="wave_number" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" value="{{ count($waves) + 1 }}" required>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Gelombang</label>
                    <input type="text" name="name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" placeholder="Gelombang I (Inden)" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Mulai</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" required>
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Selesai</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" required>
                    </div>
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Kuota (Kosongkan = tak terbatas)</label>
                    <input type="number" name="quota" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-xs font-bold" placeholder="100">
                </div>
                <div class="flex gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('modal-add-wave').classList.add('hidden')" class="flex-1 py-3 bg-slate-100 text-slate-600 rounded-xl text-xs font-bold uppercase hover:bg-slate-200 transition">Batal</button>
                    <button type="submit" class="flex-[2] py-3 bg-blue-600 text-white rounded-xl text-xs font-bold uppercase hover:bg-blue-700 shadow-lg shadow-blue-200 transition flex items-center justify-center gap-2">
                        <i class="fas fa-check"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addCustomDocument() {
    const input = document.getElementById('custom_doc_label_input');
    const label = input.value.trim();
    if (!label) { alert('Masukkan nama dokumen.'); input.focus(); return; }
    if (label.length < 3) { alert('Nama dokumen minimal 3 karakter.'); input.focus(); return; }
    document.getElementById('custom_doc_label_hidden').value = label;
    document.getElementById('add-custom-doc-form').submit();
}
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('custom_doc_label_input');
    if (input) {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); addCustomDocument(); }
        });
    }
});
</script>
@endsection
