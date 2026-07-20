@extends('layouts.yayasan')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 py-8 w-full max-w-9xl mx-auto">

    <!-- Page header -->
    <div class="sm:flex sm:justify-between sm:items-center mb-8">
        <!-- Left: Title -->
        <div class="mb-4 sm:mb-0">
            <h1 class="text-2xl md:text-3xl text-slate-800 font-bold">Kalender Pendidikan (Yayasan) 📅</h1>
            <p class="text-sm text-slate-500 mt-1">Tahun Ajaran: {{ $academicYear->year }}</p>
        </div>

        <div class="grid grid-flow-col sm:auto-cols-max justify-start sm:justify-end gap-2">
            <a href="{{ route('yayasan.calendar.print') }}" target="_blank" class="btn bg-white border-slate-200 hover:border-slate-300 text-slate-500 hover:text-slate-600">
                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 16 16">
                    <path d="M14.3 2.3L11.7.3c-.4-.3-.9-.3-1.4-.3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3.7c0-.5-.2-1-.7-1.4zM11 2v3h3l-3-3zM3 14V2h6v4c0 .6.4 1 1 1h4v7H3z"/>
                </svg>
                <span class="hidden xs:block ml-2">Cetak PDF</span>
            </a>
            <a href="{{ route('yayasan.calendar.monday_inspiration.print') }}" target="_blank" class="btn text-white" style="background-color: #8b5cf6;" onmouseover="this.style.backgroundColor='#7c3aed'" onmouseout="this.style.backgroundColor='#8b5cf6'">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                    <path d="M14.3 2.3L11.7.3c-.4-.3-.9-.3-1.4-.3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3.7c0-.5-.2-1-.7-1.4zM11 2v3h3l-3-3zM3 14V2h6v4c0 .6.4 1 1 1h4v7H3z"/>
                </svg>
                <span class="hidden xs:block ml-2">Surat Edaran MI</span>
            </a>
            <button class="btn bg-indigo-500 hover:bg-indigo-600 text-white" onclick="document.getElementById('eventModal').classList.remove('hidden')">
                <svg class="w-4 h-4 fill-current opacity-50 shrink-0" viewBox="0 0 16 16">
                    <path d="M15 7H9V1c0-.6-.4-1-1-1S7 .4 7 1v6H1c-.6 0-1 .4-1 1s.4 1 1 1h6v6c0 .6.4 1 1 1s1-.4 1-1V9h6c.6 0 1-.4 1-1s-.4-1-1-1z" />
                </svg>
                <span class="hidden xs:block ml-2">Tambah Jadwal</span>
            </button>
        </div>
    </div>

    <!-- Validations -->
    @if (session('success'))
        <div class="mb-4 px-4 py-3 rounded-sm text-sm bg-emerald-100 border border-emerald-200 text-emerald-600">
            {{ session('success') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded-sm text-sm bg-rose-100 border border-rose-200 text-rose-600">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Calendar Container -->
    <div class="bg-white p-5 shadow-sm rounded-sm border border-slate-200">
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

        <div class="mb-4 flex flex-wrap gap-4 text-sm">
            <span class="flex items-center"><span class="w-3 h-3 rounded-full mr-2" style="background-color: #8b5cf6;"></span> Monday Inspiration</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-red-500 mr-2"></span> Libur Yayasan</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-orange-500 mr-2"></span> Kegiatan Yayasan</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-blue-500 mr-2"></span> Libur Sekolah</span>
            <span class="flex items-center"><span class="w-3 h-3 rounded-full bg-green-500 mr-2"></span> Kegiatan Sekolah</span>
        </div>

        <div id="calendar"></div>
    </div>

</div>

<!-- Modal Create/Edit -->
<div id="eventModal" class="hidden fixed inset-0 bg-slate-900 bg-opacity-30 z-50 transition-opacity flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden max-w-lg w-full">
        <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-slate-800" id="modalTitle">Tambah Jadwal Yayasan</h3>
            <button class="text-slate-400 hover:text-slate-500" onclick="closeModal()">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 16 16">
                    <path d="M7.95 6.536l4.242-4.243a1 1 0 111.415 1.414L9.364 7.95l4.243 4.242a1 1 0 11-1.415 1.415L7.95 9.364l-4.243 4.243a1 1 0 01-1.414-1.415L6.536 7.95 2.293 3.707a1 1 0 011.414-1.414L7.95 6.536z" />
                </svg>
            </button>
        </div>
        <div class="px-6 py-4">
            <form id="eventForm" method="POST" action="{{ route('yayasan.calendar.store') }}">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                {{-- Hidden fields untuk menjaga nilai original saat edit event school level --}}
                <input type="hidden" name="original_level" id="originalLevel" value="">
                <input type="hidden" name="original_school_id" id="originalSchoolId" value="">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" for="title">Judul Kegiatan <span class="text-rose-500">*</span></label>
                    <input id="title" name="title" class="form-input w-full" type="text" required />
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium mb-1" for="start_date">Tanggal Mulai <span class="text-rose-500">*</span></label>
                        <input id="start_date" name="start_date" class="form-input w-full" type="date" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1" for="end_date">Tanggal Selesai <span class="text-rose-500">*</span></label>
                        <input id="end_date" name="end_date" class="form-input w-full" type="date" required />
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" for="school_id">Berlaku Untuk (Target)</label>
                    <select id="school_id" name="school_id" class="form-select w-full">
                        <option value="">Semua Unit (Yayasan Global)</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}">{{ $school->name }} ({{ $school->type }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" for="type">Jenis <span class="text-rose-500">*</span></label>
                    <select id="type" name="type" class="form-select w-full" required>
                        <option value="yayasan_event">Kegiatan Yayasan</option>
                        <option value="school_event">Kegiatan Sekolah</option>
                        <option value="holiday">Libur Khusus</option>
                        <option value="national_holiday">Libur Nasional</option>
                        <option value="collective_leave">Cuti Bersama</option>
                        <option value="monday_inspiration">Monday Inspiration</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" name="is_holiday" id="is_holiday" value="1" class="form-checkbox" />
                        <span class="text-sm ml-2 font-bold text-red-600">Tidak Ada KBM / Kegiatan Belajar Mengajar (Libur)</span>
                    </label>
                    <p class="text-xs text-slate-500 mt-1 ml-6">Jika dicentang, tanggal ini tidak akan dihitung sebagai hari aktif belajar untuk absensi.</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" for="description">Keterangan Tambahan</label>
                    <textarea id="description" name="description" class="form-input w-full" rows="3"></textarea>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" class="btn border-slate-200 hover:border-slate-300 text-slate-600 mr-2" onclick="closeModal()">Batal</button>
                    <button type="button" id="btnDelete" class="btn bg-rose-500 hover:bg-rose-600 text-white mr-2 hidden" onclick="deleteEvent()">Hapus</button>
                    <button type="submit" class="btn bg-indigo-500 hover:bg-indigo-600 text-white">Simpan</button>
                </div>
            </form>
            
            <form id="deleteForm" method="POST" action="" class="hidden">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<!-- FullCalendar CSS -->
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
    // Base URL calendar agar benar di localhost maupun production
    const CALENDAR_BASE_URL = "{{ url('yayasan/calendar') }}";

    function closeModal() {
        document.getElementById('eventModal').classList.add('hidden');
        document.getElementById('eventForm').reset();
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('eventForm').action = "{{ route('yayasan.calendar.store') }}";
        document.getElementById('modalTitle').innerText = 'Tambah Jadwal';
        document.getElementById('btnDelete').classList.add('hidden');
    }

    function deleteEvent() {
        if(confirm('Apakah Anda yakin ingin menghapus jadwal ini?')) {
            document.getElementById('deleteForm').submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');
        
        // Ambil posisi terakhir kalender dari sessionStorage
        var savedView = sessionStorage.getItem('calendarView_yayasan') || 'dayGridMonth';
        var savedDate = sessionStorage.getItem('calendarDate_yayasan');
        
        var calendar = new FullCalendar.Calendar(calendarEl, {
            firstDay: 1, // Start week on Monday
            initialView: savedView,
            initialDate: savedDate || undefined, // Gunakan savedDate jika ada
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
                fetch(`{{ route('yayasan.calendar.index') }}?start=${fetchInfo.startStr}&end=${fetchInfo.endStr}`, {
                    headers: {
                        "X-Requested-With": "XMLHttpRequest"
                    }
                })
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
            },
            datesSet: function(info) {
                // Simpan posisi tampilan dan tanggal saat ini setiap kali pengguna melakukan navigasi (Next/Prev/Change View)
                sessionStorage.setItem('calendarView_yayasan', info.view.type);
                sessionStorage.setItem('calendarDate_yayasan', calendar.getDate().toISOString());
            },
            eventClick: function(info) {
                // Populate modal for edit
                let event = info.event;
                let props = event.extendedProps;

                document.getElementById('modalTitle').innerText = 'Edit Jadwal';
                document.getElementById('formMethod').value = 'PUT';
                // Gunakan CALENDAR_BASE_URL agar benar di localhost dan production
                document.getElementById('eventForm').action = `${CALENDAR_BASE_URL}/${event.id}`;

                // Gunakan original_title agar tidak ada prefix [yayasan/smk/smp] pada form edit
                document.getElementById('title').value = props.original_title || event.title;

                // Ambil hanya bagian tanggal (YYYY-MM-DD) tanpa timezone
                document.getElementById('start_date').value = event.startStr.substring(0, 10);

                // FullCalendar exclusive end: kurangi 1 hari. Jika end null, gunakan start.
                if (event.end) {
                    let endDate = new Date(event.end);
                    endDate.setDate(endDate.getDate() - 1);
                    document.getElementById('end_date').value = endDate.toISOString().split('T')[0];
                } else {
                    document.getElementById('end_date').value = event.startStr.substring(0, 10);
                }

                document.getElementById('school_id').value = props.school_id || '';
                document.getElementById('type').value = props.type;
                document.getElementById('is_holiday').checked = props.is_holiday;
                document.getElementById('description').value = props.description || '';

                // Simpan level dan school_id asli agar controller bisa mempertahankan nilai
                document.getElementById('originalLevel').value = props.level || '';
                document.getElementById('originalSchoolId').value = props.school_id || '';

                document.getElementById('btnDelete').classList.remove('hidden');
                // Gunakan CALENDAR_BASE_URL agar benar di localhost dan production
                document.getElementById('deleteForm').action = `${CALENDAR_BASE_URL}/${event.id}`;

                document.getElementById('eventModal').classList.remove('hidden');
            },
            dateClick: function(info) {
                closeModal(); // reset
                document.getElementById('start_date').value = info.dateStr;
                document.getElementById('end_date').value = info.dateStr;
                document.getElementById('eventModal').classList.remove('hidden');
            }
        });
        calendar.render();
        
        // Fix bug: Saat Sidebar di-toggle (disembunyikan/ditampilkan), ukuran container berubah
        // tetapi browser tidak mentrigger window.resize. 
        // ResizeObserver akan mendeteksi perubahan dimensi container dan menyesuaikan kalender.
        if (window.ResizeObserver) {
            const resizeObserver = new ResizeObserver(() => {
                // Gunakan requestAnimationFrame atau timeout kecil untuk mencegah ResizeObserver loop error
                setTimeout(() => {
                    calendar.updateSize();
                }, 50);
            });
            // Observasi container kalender atau parent-nya
            resizeObserver.observe(calendarEl.parentElement);
        } else {
            // Fallback untuk browser lama
            setTimeout(function() {
                calendar.updateSize();
            }, 250);
        }
    });
</script>
@endpush
