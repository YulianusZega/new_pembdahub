@extends('layouts.admin')

@section('header')
<h2 class="font-semibold text-xl text-slate-800 leading-tight">
    Edit Mitra DUDI
</h2>
@endsection

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-3xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Edit Mitra ✨</h1>
        </div>
        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('admin.pkl-alumni.dudis.index') }}" class="btn border-slate-200 hover:border-slate-300 text-slate-600">
                Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white shadow-lg rounded-sm border border-slate-200 p-6">
        <form method="POST" action="{{ route('admin.pkl-alumni.dudis.update', $dudi) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                
                @if($isSA)
                <div>
                    <label class="block text-sm font-medium mb-1" for="school_id">Sekolah Pemilik (Opsional) <span class="text-rose-500">*</span></label>
                    <select id="school_id" name="school_id" class="form-select w-full">
                        <option value="">-- Tersedia Global untuk Semua Sekolah --</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" {{ old('school_id', $dudi->school_id) == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                        @endforeach
                    </select>
                    <div class="text-xs text-slate-500 mt-1">Kosongkan jika mitra ini milik Yayasan dan bisa dipakai bersama.</div>
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium mb-1" for="name">Nama Perusahaan / Instansi <span class="text-rose-500">*</span></label>
                    <input id="name" name="name" type="text" class="form-input w-full" value="{{ old('name', $dudi->name) }}" required />
                    @error('name') <div class="text-xs mt-1 text-rose-500">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="address">Alamat Lengkap <span class="text-rose-500">*</span></label>
                    <textarea id="address" name="address" rows="3" class="form-input w-full" required>{{ old('address', $dudi->address) }}</textarea>
                    @error('address') <div class="text-xs mt-1 text-rose-500">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1" for="field_of_work">Bidang Kerja / Industri</label>
                    <input id="field_of_work" name="field_of_work" type="text" class="form-input w-full" value="{{ old('field_of_work', $dudi->field_of_work) }}" placeholder="Misal: IT, Otomotif, Perbankan..." />
                    @error('field_of_work') <div class="text-xs mt-1 text-rose-500">{{ $message }}</div> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="mentor_name">Nama Mentor Default</label>
                        <input id="mentor_name" name="mentor_name" type="text" class="form-input w-full" value="{{ old('mentor_name', $dudi->mentor_name) }}" />
                        @error('mentor_name') <div class="text-xs mt-1 text-rose-500">{{ $message }}</div> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="mentor_phone">No. HP/WA Mentor Default</label>
                        <input id="mentor_phone" name="mentor_phone" type="text" class="form-input w-full" value="{{ old('mentor_phone', $dudi->mentor_phone) }}" placeholder="Contoh: 081234567890" />
                        @error('mentor_phone') <div class="text-xs mt-1 text-rose-500">{{ $message }}</div> @enderror
                    </div>
                </div>

            </div>

            <div class="mt-6 border-t border-slate-200 pt-5 flex justify-end">
                <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Perbarui Mitra DUDI</button>
            </div>
        </form>
    </div>

</div>
@endsection
