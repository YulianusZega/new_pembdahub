@extends('layouts.orangtua')
@section('title', 'Jadwal '.$student->full_name.' - Portal Orang Tua')

@section('content')
<div class="space-y-6">
    @include('orangtua.partials.child-header', ['student' => $student, 'classroom' => $classroom, 'active' => 'jadwal'])

    @if(!$classroom)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <i class="fas fa-exclamation-circle text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Belum terdaftar di kelas manapun.</p>
        </div>
    @elseif($schedules->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-10 text-center">
            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Belum ada jadwal untuk kelas {{ $classroom->class_name }}.</p>
        </div>
    @else
        @foreach($days as $day)
            @php $daySchedules = $schedules->get($day, collect()); @endphp
            @if($daySchedules->count() > 0)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 {{ $day === strtolower(now()->format('l')) ? 'bg-teal-50' : 'bg-gray-50' }}">
                        <h2 class="font-bold text-gray-800 flex items-center gap-2">
                            {{ $dayLabels[$day] ?? $day }}
                            @if($day === strtolower(now()->format('l')))
                                <span class="text-xs bg-teal-500 text-white px-2 py-0.5 rounded-full">Hari Ini</span>
                            @endif
                        </h2>
                    </div>
                    <div class="divide-y divide-gray-50">
                        @foreach($daySchedules->sortBy('time_slot_id') as $schedule)
                            <div class="flex items-center gap-4 px-5 py-3 hover:bg-gray-50 transition">
                                <div class="text-center min-w-[80px]">
                                    <p class="text-sm font-bold text-teal-600">{{ $schedule->timeSlot ? $schedule->timeSlot->start_time : ($schedule->start_time ?? '-') }}</p>
                                    <p class="text-xs text-gray-400">{{ $schedule->timeSlot ? $schedule->timeSlot->end_time : ($schedule->end_time ?? '-') }}</p>
                                </div>
                                <div class="flex-1">
                                    <p class="font-semibold text-gray-800">{{ $schedule->subject->name ?? '-' }}</p>
                                    <p class="text-xs text-gray-500">
                                        <i class="fas fa-chalkboard-teacher mr-1"></i>{{ optional($schedule->teacher)->full_name ?? optional($schedule->teacher)->name ?? '-' }}
                                        @if($schedule->room) · <i class="fas fa-door-open mr-1"></i>{{ $schedule->room }} @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    @endif
</div>
@endsection
