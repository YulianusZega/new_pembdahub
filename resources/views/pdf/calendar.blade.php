<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kalender Pendidikan</title>
    <style>
        @page {
            margin: 10mm 10mm 10mm 10mm;
            size: A4 landscape;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 5px;
        }
        .header h1 {
            margin: 0 0 2px 0;
            font-size: 16px;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 0;
            font-size: 13px;
            color: #555;
        }
        
        .main-table {
            width: 100%;
            border-collapse: collapse;
        }
        .main-table td {
            vertical-align: top;
        }
        
        /* Grid Kalender Kiri */
        .calendar-section {
            width: 75%;
            padding-right: 10px;
        }
        .months-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
        }
        .months-grid td {
            width: 25%;
        }
        
        /* Individual Month Table */
        .month-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
            text-align: center;
        }
        .month-title {
            background-color: #3b82f6;
            color: white;
            font-weight: bold;
            padding: 3px;
            font-size: 11px;
            text-transform: uppercase;
        }
        .month-table th {
            background-color: #f3f4f6;
            font-size: 9px;
            padding: 2px;
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #eee;
        }
        .month-table th.sun {
            color: #dc2626;
        }
        .month-table td {
            padding: 2px;
            font-size: 9px;
            border: 1px solid #eee;
            height: 14px;
        }
        .month-table td.sun {
            color: #dc2626;
            background-color: #fee2e2;
        }
        .month-table td.empty {
            background-color: #f9fafb;
        }
        
        /* Event List Kanan */
        .events-section {
            width: 25%;
            border-left: 1px solid #ccc;
            padding-left: 10px;
        }
        .events-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 3px;
        }
        .event-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .event-item {
            margin-bottom: 4px;
            font-size: 8px;
            line-height: 1.2;
            page-break-inside: avoid;
        }
        .color-box {
            display: inline-block;
            width: 8px;
            height: 8px;
            margin-right: 3px;
            vertical-align: middle;
        }
        .event-date {
            font-weight: bold;
            color: #444;
        }
        
        /* Legend */
        .legend {
            margin-top: 10px;
            font-size: 8px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 10px;
        }
    </style>
</head>
<body>

@php
    $months = [];
    $start = \Carbon\Carbon::parse($academicYear->start_date)->startOfMonth();
    $end = \Carbon\Carbon::parse($academicYear->end_date)->endOfMonth();
    
    while ($start->lte($end)) {
        $months[] = $start->copy();
        $start->addMonth();
    }
    // Limit to 12 months
    $months = array_slice($months, 0, 12);
    
    // Map events to dates
    $eventDates = [];
    foreach($events as $e) {
        $d = \Carbon\Carbon::parse($e->start_date);
        $d_end = \Carbon\Carbon::parse($e->end_date);
        while ($d->lte($d_end)) {
            $dateStr = $d->format('Y-m-d');
            if(!isset($eventDates[$dateStr])) {
                $eventDates[$dateStr] = [];
            }
            $eventDates[$dateStr][] = $e;
            $d->addDay();
        }
    }
    
    function getEventColor($event) {
        if ($event->level === 'yayasan') {
            return $event->is_holiday ? '#e3342f' : '#f6993f';
        }
        return $event->is_holiday ? '#6574cd' : '#38c172';
    }
    
    $indoMonths = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
@endphp

<div class="header">
    <h1>Kalender Pendidikan Yayasan Perguruan Pembda Nias</h1>
    <h2>Tahun Ajaran {{ $academicYear->year }}</h2>
</div>

<table class="main-table">
    <tr>
        <td class="calendar-section">
            <table class="months-grid">
                @foreach(array_chunk($months, 4) as $row)
                <tr>
                    @foreach($row as $month)
                    <td>
                        <table class="month-table">
                            <tr>
                                <td colspan="7" class="month-title">{{ $indoMonths[$month->month] }} {{ $month->year }}</td>
                            </tr>
                            <tr>
                                <th class="sun">Min</th>
                                <th>Sen</th>
                                <th>Sel</th>
                                <th>Rab</th>
                                <th>Kam</th>
                                <th>Jum</th>
                                <th>Sab</th>
                            </tr>
                            @php
                                $firstDay = $month->copy()->startOfMonth();
                                $daysInMonth = $month->daysInMonth;
                                $startPadding = $firstDay->dayOfWeek; 
                                // In carbon, 0 is Sunday, which matches our table (Min, Sen, Sel...)
                                
                                $currentDay = 1;
                                $weeks = ceil(($daysInMonth + $startPadding) / 7);
                            @endphp
                            
                            @for($w = 0; $w < $weeks; $w++)
                            <tr>
                                @for($d = 0; $d < 7; $d++)
                                    @if($w == 0 && $d < $startPadding)
                                        <td class="empty"></td>
                                    @elseif($currentDay > $daysInMonth)
                                        <td class="empty"></td>
                                    @else
                                        @php
                                            $dateStr = $month->copy()->addDays($currentDay - 1)->format('Y-m-d');
                                            $hasEvent = isset($eventDates[$dateStr]);
                                            $bgColor = '';
                                            $textColor = '';
                                            if ($hasEvent) {
                                                // ambil event pertama di tanggal ini untuk warna background
                                                $firstEvt = $eventDates[$dateStr][0];
                                                $bgColor = getEventColor($firstEvt);
                                                $textColor = '#fff';
                                            }
                                        @endphp
                                        <td class="{{ $d == 0 ? 'sun' : '' }}" 
                                            @if($bgColor) style="background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: bold;" @endif>
                                            {{ $currentDay }}
                                        </td>
                                        @php $currentDay++; @endphp
                                    @endif
                                @endfor
                            </tr>
                            @endfor
                        </table>
                    </td>
                    @endforeach
                    
                    @if(count($row) < 4)
                        @for($i = count($row); $i < 4; $i++)
                            <td></td>
                        @endfor
                    @endif
                </tr>
                @endforeach
            </table>
            
            <div class="legend">
                <strong>Keterangan Warna:</strong><br>
                <div class="legend-item"><span class="color-box" style="background-color: #e3342f;"></span> Libur Yayasan</div>
                <div class="legend-item"><span class="color-box" style="background-color: #f6993f;"></span> Kegiatan Yayasan</div>
                <div class="legend-item"><span class="color-box" style="background-color: #6574cd;"></span> Libur Sekolah</div>
                <div class="legend-item"><span class="color-box" style="background-color: #38c172;"></span> Kegiatan Sekolah</div>
            </div>
        </td>
        
        <td class="events-section">
            <div class="events-title">Daftar Kegiatan & Libur</div>
            <ul class="event-list">
                @foreach($events as $event)
                    @php
                        $startDate = \Carbon\Carbon::parse($event->start_date)->format('d/m/Y');
                        $endDate = \Carbon\Carbon::parse($event->end_date)->format('d/m/Y');
                        $dateDisplay = $startDate === $endDate ? $startDate : $startDate . ' - ' . $endDate;
                        $color = getEventColor($event);
                    @endphp
                    <li class="event-item">
                        <span class="color-box" style="background-color: {{ $color }};"></span>
                        <span class="event-date">{{ $dateDisplay }}</span><br>
                        {{ $event->title }}
                        @if($event->level === 'school' && $event->school)
                            <br><span style="font-size: 7px; color: #666;">({{ $event->school->type }})</span>
                        @endif
                    </li>
                @endforeach
                
                @if($events->isEmpty())
                    <li class="event-item" style="color: #999;">Belum ada jadwal tersimpan.</li>
                @endif
            </ul>
        </td>
    </tr>
</table>

</body>
</html>
