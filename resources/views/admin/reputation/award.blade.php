@extends('layouts.admin')
@section('title', 'Berikan Poin Manual - Pembda Elite')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.reputation.logs') }}" class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center text-gray-500 hover:bg-gray-50 transition shadow-sm">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Berikan Poin Manual</h1>
            <p class="text-sm text-gray-500">Apresiasi khusus untuk pencapaian luar biasa pengguna</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="p-8">
            <form action="{{ route('admin.reputation.award') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Target User --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Target Pengguna <span class="text-rose-500">*</span></label>
                        <div class="relative group">
                            <select name="user_id" id="userSelect" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-4 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all appearance-none cursor-pointer">
                                <option value="">Cari nama siswa atau guru...</option>
                            </select>
                            <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <i class="fas fa-search"></i>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 ml-1 italic">Ketik minimal 3 karakter untuk mencari pengguna di sistem.</p>
                    </div>

                    {{-- Point Amount --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Jumlah Poin <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="points" required placeholder="Contoh: 50" 
                                class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-12 py-4 text-xl font-bold text-blue-600 focus:border-blue-500 focus:bg-white outline-none transition-all">
                            <div class="absolute left-5 top-1/2 -translate-y-1/2 text-blue-500">
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-2 ml-1">Gunakan angka negatif (misal: -10) untuk pengurangan poin.</p>
                    </div>

                    {{-- Category --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Kategori <span class="text-rose-500">*</span></label>
                        <select name="category" required class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-4 text-sm font-bold focus:border-blue-500 focus:bg-white outline-none transition-all">
                            <option value="special_achievement">Pencapaian Khusus</option>
                            <option value="character">Karakter & Etika</option>
                            <option value="violation">Hukuman / Pelanggaran</option>
                            <option value="event_participation">Partisipasi Event</option>
                            <option value="other">Lainnya</option>
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-3 pl-1">Keterangan / Alasan <span class="text-rose-500">*</span></label>
                        <textarea name="description" required rows="3" placeholder="Jelaskan mengapa poin ini diberikan..." 
                            class="w-full bg-slate-50 border-2 border-slate-100 rounded-xl px-5 py-4 text-sm font-medium focus:border-blue-500 focus:bg-white outline-none transition-all"></textarea>
                    </div>
                </div>

                <div class="mt-10 flex items-center justify-end gap-3">
                    <a href="{{ route('admin.reputation.logs') }}" class="px-8 py-4 rounded-xl text-sm font-bold text-slate-400 hover:text-slate-600 transition uppercase tracking-widest">Batal</a>
                    <button type="submit" class="px-10 py-4 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white rounded-xl text-sm font-bold shadow-lg shadow-blue-500/30 hover:shadow-blue-500/50 transition transform hover:-translate-y-1 uppercase tracking-widest">
                        Eksekusi Perubahan Poin
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        height: 58px !important;
        background-color: #f8fafc !important;
        border: 2px solid #f1f5f9 !important;
        border-radius: 1rem !important;
        padding: 12px 20px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 56px !important;
        right: 15px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px !important;
        font-weight: 700 !important;
        color: #1e293b !important;
    }
    .select2-container--open .select2-dropdown {
        border-radius: 1rem !important;
        border: 2px solid #3b82f6 !important;
        overflow: hidden;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#userSelect').select2({
            placeholder: 'Ketik nama siswa atau guru...',
            minimumInputLength: 3,
            ajax: {
                url: '{{ route("admin.reputation.search-users") }}',
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term,
                        school_id: '{{ request("school_id") }}'
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                },
                cache: true
            }
        });
    });
</script>
@endpush
