{{-- Reusable child header for orang tua pages --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-full flex-shrink-0 overflow-hidden ring-2 ring-teal-100 shadow-sm">
            <img src="{{ $student->photo_url }}" class="w-full h-full object-cover" alt="{{ $student->full_name }}">
        </div>
        <div>
            <h1 class="font-bold text-gray-800">{{ $student->full_name }}</h1>
            <p class="text-xs text-gray-500">{{ $classroom ? $classroom->class_name : '-' }} · {{ $student->school->name ?? '' }}</p>
        </div>
    </div>
    <div class="flex gap-2 flex-wrap">
        <a href="{{ route('orangtua.anak.nilai', $student->id) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $active === 'nilai' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            <i class="fas fa-chart-bar mr-1"></i>Nilai
        </a>
        <a href="{{ route('orangtua.anak.tagihan', $student->id) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $active === 'tagihan' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            <i class="fas fa-file-invoice-dollar mr-1"></i>Tagihan
        </a>
        <a href="{{ route('orangtua.anak.absensi', $student->id) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $active === 'absensi' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            <i class="fas fa-clipboard-check mr-1"></i>Absensi
        </a>
        <a href="{{ route('orangtua.anak.jadwal', $student->id) }}"
           class="px-3 py-1.5 rounded-lg text-xs font-medium transition {{ $active === 'jadwal' ? 'bg-teal-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            <i class="fas fa-calendar-alt mr-1"></i>Jadwal
        </a>
    </div>
</div>
