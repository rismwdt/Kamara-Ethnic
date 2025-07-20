<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class ScheduleOptimizer
{
    public function isAvailable($date, $start, $end, $location)
    {
        return !Booking::where('date', $date)
            ->whereIn('status', ['tertunda', 'diterima'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                      ->orWhereBetween('end_time', [$start, $end])
                      ->orWhere(function ($q2) use ($start, $end) {
                          $q2->where('start_time', '<=', $start)
                             ->where('end_time', '>=', $end);
                      });
            })
            ->exists();
    }

    // public function canAcceptInWeek($date)
    // {
    //     $date = Carbon::parse($date);
    //     $startOfWeek = $date->copy()->startOfWeek(); // default Senin
    //     $endOfWeek = $date->copy()->endOfWeek();

    //     $eventCount = Booking::whereBetween('date', [$startOfWeek, $endOfWeek])
    //         ->whereIn('status', ['tertunda', 'diterima'])
    //         ->count();

    //     return $eventCount < 5; // maksimal 5 event
    // }

    public function canAcceptInDay($date)
    {
        $eventCount = Booking::whereDate('date', $date)
            ->whereIn('status', ['tertunda', 'diterima'])
            ->count();

        return $eventCount < 5;
    }
}
