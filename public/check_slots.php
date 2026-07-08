<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$classroom = \App\Models\Classroom::find(338);
$allSchedules = \App\Models\Schedule::where('classroom_id', $classroom->id)
    ->with('timeSlot')
    ->get();

$sMap = [];
foreach ($allSchedules as $s) {
    $timeSlot = $s->timeSlot;
    $timeKey = ($timeSlot->start_time ?? $s->start_time) . '-' . ($timeSlot->end_time ?? $s->end_time);
    $sMap[$s->day_of_week][$timeKey] = $s;
}

$timeSlotIds = $allSchedules->pluck('time_slot_id')->unique()->filter();
$usedSlots = \App\Models\TimeSlot::whereIn('id', $timeSlotIds)->get();
$minOrder = $usedSlots->min('slot_order');
$maxOrder = $usedSlots->max('slot_order');

foreach ($allSchedules as $s) {
    if ($s->timeSlot && $s->duration_slots > 1) {
        $endOrder = $s->timeSlot->slot_order + ($s->duration_slots - 1);
        if ($endOrder > $maxOrder) {
            $maxOrder = $endOrder;
        }
    }
}

$timeSlots = \App\Models\TimeSlot::where('school_id', $classroom->school_id)
    ->whereBetween('slot_order', [$minOrder, $maxOrder])
    ->orderBy('slot_order')
    ->get()
    ->unique(function ($slot) {
        return $slot->start_time . '-' . $slot->end_time;
    });

$timetable = [];
$occupied = [];
$days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
foreach ($timeSlots as $slot) {
    $timeKey = $slot->start_time . '-' . $slot->end_time;
    foreach ($days as $day) {
        if (isset($occupied[$day][$slot->slot_order])) continue;
        $schedule = $sMap[$day][$timeKey] ?? null;
        if ($schedule) {
            $timetable[$slot->slot_order][$day] = $schedule;
            $duration = $schedule->duration_slots ?? 1;
            if ($duration > 1) {
                for ($i = 1; $i < $duration; $i++) {
                    $occupied[$day][$slot->slot_order + $i] = true;
                }
            }
        } else {
            $timetable[$slot->slot_order][$day] = null;
        }
    }
}

// Simulate Blade View Logic
$renderedOccupied = [];
foreach ($timeSlots as $slot) {
    $order = $slot->slot_order;
    echo "<tr> (Order $order)\n";
    $cellCount = 1; // For time column
    
    foreach ($days as $day) {
        if (isset($renderedOccupied[$day][$order])) {
            echo "  $day: Skipped by rowspan\n";
            continue;
        }

        $schedule = $timetable[$order][$day] ?? null;
        $duration = $schedule->duration_slots ?? 1;
        
        if ($duration > 1) {
            for ($i = 1; $i < $duration; $i++) {
                $renderedOccupied[$day][$order + $i] = true;
            }
        }
        
        if ($schedule) {
            echo "  $day: <td> Schedule Dur $duration </td>\n";
        } else {
            echo "  $day: <td> Empty Cell </td>\n";
        }
        $cellCount++;
    }
    echo "  Total Cells Rendered: $cellCount\n";
    echo "</tr>\n";
}
