@extends('layouts.siswa')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Kalender Pendidikan ({{ $school->name }}) 📅</h1>
            <p class="text-sm text-slate-500 mt-1">Tahun Ajaran: {{ $academicYear->year }}</p>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white p-5 shadow-sm rounded-sm border border-slate-200">
        <div class="mb-4 flex flex-wrap gap-4 text-sm">
            <span class="flex items-center"><span class="w-3 h-3 rounded-full mr-2" style="background-color: #8b5cf6;"></span> Monday Inspiration</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span> Libur Yayasan</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-orange-500 mr-2"></span> Kegiatan Yayasan</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span> Libur Sekolah</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span> Kegiatan Sekolah</span>
        </div>
        <!-- Statistik Hari Aktif -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-indigo-50 rounded-lg p-4 border border-indigo-100 flex items-center">
                <div class="w-12 h-12 rounded-full bg-indigo-100 text-indigo-500 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <div class="text-sm text-indigo-600 font-medium">Hari Aktif Smt Ganjil</div>
                    <div class="text-2xl font-bold text-indigo-900">{{ $activeDaysGanjil }} Hari</div>
                </div>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 flex items-center">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-500 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <div class="text-sm text-blue-600 font-medium">Hari Aktif Smt Genap</div>
                    <div class="text-2xl font-bold text-blue-900">{{ $activeDaysGenap }} Hari</div>
                </div>
            </div>
            <div class="bg-emerald-50 rounded-lg p-4 border border-emerald-100 flex items-center">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-500 flex items-center justify-center mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </div>
                <div>
                    <div class="text-sm text-emerald-600 font-medium">Total 1 Tahun Ajaran</div>
                    <div class="text-2xl font-bold text-emerald-900">{{ $activeDaysTotal }} Hari</div>
                </div>
            </div>
        </div>

        <div id="calendar"></div>
    </div>

</div>

<!-- Read-Only Modal -->
<div id="eventModal" class="hidden fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-lg w-full">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-800" id="modalTitle">Detail Jadwal</h3>
            <button class="text-slate-400 hover:text-slate-500" onclick="closeModal()">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                    <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                </svg>
            </button>
        </div>
        <div class="px-6 py-4">
            <div class="mb-4">
                <p class="text-sm font-medium text-slate-500">Judul Kegiatan</p>
                <p class="text-slate-800 font-semibold" id="view_title"></p>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-slate-500">Tanggal Mulai</p>
                    <p class="text-slate-800" id="view_start"></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-slate-500">Tanggal Selesai</p>
                    <p class="text-slate-800" id="view_end"></p>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-sm font-medium text-slate-500">Jenis Kegiatan</p>
                <p class="text-slate-800" id="view_type"></p>
            </div>
            <div class="mb-4">
                <p class="text-sm font-medium text-slate-500">Keterangan Tambahan</p>
                <p class="text-slate-800" id="view_description"></p>
            </div>
            <div class="flex justify-end mt-6">
                <button type="button" class="btn bg-indigo-500 hover:bg-indigo-600 text-white" onclick="closeModal()">Tutup</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<style>
    .fc-event { cursor: pointer; }
    .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 600 !important; }
    
    /* Header background biru, teks putih */
    th.fc-col-header-cell { background-color: #3b82f6 !important; border-color: #2563eb !important; }
    th.fc-col-header-cell .fc-col-header-cell-cushion { color: #ffffff !important; font-weight: 600; padding: 8px 4px; }
    
    /* Hari Aktif (Weekday) BODY hijau muda */
    td.fc-daygrid-day:not(.fc-day-sat):not(.fc-day-sun) { background-color: #f0fdf4 !important; } /* green-50 */
    
    /* Mark Weekends BODY pink, teks tanggal merah */
    td.fc-day-sat, td.fc-day-sun { background-color: #fee2e2 !important; }
    td.fc-day-sat .fc-daygrid-day-number, td.fc-day-sun .fc-daygrid-day-number { color: #dc2626 !important; font-weight: bold; }
    
    /* Multi-month styling */
    .fc-multimonth-title { color: #1e40af !important; font-weight: bold; background-color: #eff6ff; padding: 4px; border-radius: 4px; text-align: center; margin-bottom: 8px; }
    .fc-multimonth-daygrid { border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden; }

    /* Fix FullCalendar width and spacing issues */
    .fc-scrollgrid, .fc-scrollgrid-table, .fc-scrollgrid-sync-table { width: 100% !important; table-layout: fixed !important; }
    .fc-view-harness { width: 100% !important; }

    /* Event text wrapping */
    .fc-event-title { white-space: normal !important; overflow: hidden; }
</style>
@endpush

@push('scripts')
<script>
    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        var calendar = new FullCalendar.Calendar(calendarEl, {
            firstDay: 1, // Start week on Monday
            dayMaxEvents: true, // Allow "more" link
            initialView: 'dayGridMonth',
            validRange: {
                start: '{{ \Carbon\Carbon::parse($academicYear->start_date)->format("Y-m-d") }}',
                end: '{{ \Carbon\Carbon::parse($academicYear->end_date)->addDay()->format("Y-m-d") }}'
            },
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,semester,year,listMonth'
            },
            buttonText: {
                dayGridMonth: 'month',
                listMonth: 'list'
            },
            views: {
                semester: {
                    type: 'multiMonth',
                    duration: { months: 6 },
                    buttonText: 'semester'
                },
                year: {
                    type: 'multiMonth',
                    duration: { months: 12 },
                    buttonText: 'year'
                }
            },
            locale: 'id',
            events: function(fetchInfo, successCallback, failureCallback) {
                fetch(`{{ route('siswa.calendar.index') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
            },
            eventClick: function(info) {
                let event = info.event;
                let props = event.extendedProps;

                document.getElementById('view_title').innerText = event.title;
                document.getElementById('view_start').innerText = event.startStr;
                
                let endDate = event.end ? new Date(event.end) : new Date(event.start);
                if(event.end) {
                    endDate.setDate(endDate.getDate() - 1);
                }
                document.getElementById('view_end').innerText = endDate.toISOString().split('T')[0];
                
                let typeLabel = '';
                if(props.type === 'holiday') typeLabel = 'Libur Khusus';
                else if(props.type === 'national_holiday') typeLabel = 'Libur Nasional';
                else if(props.type === 'yayasan_event') typeLabel = 'Kegiatan Yayasan';
                else if(props.type === 'school_event') typeLabel = 'Kegiatan Sekolah';
                else if(props.type === 'collective_leave') typeLabel = 'Cuti Bersama';
                else if(props.type === 'monday_inspiration') typeLabel = 'Monday Inspiration — Keep Moving Forward';
                else typeLabel = props.type;
                
                if(props.is_holiday) typeLabel += ' (Hari Libur / Tidak Aktif)';

                document.getElementById('view_type').innerText = typeLabel;
                document.getElementById('view_description').innerText = props.description || '-';
                
                document.getElementById('eventModal').classList.remove('hidden');
            }
        });
        calendar.render();
    });
</script>
@endpush
