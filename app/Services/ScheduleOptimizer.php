<?php

namespace App\Services;

use App\Models\Booking;
use Carbon\Carbon;

class ScheduleOptimizer
{
    public function isAvailable(Carbon $date, $startTime, $endTime, $locationDetail)
{
    $bookings = Booking::where('date', $date->toDateString())->get();

    foreach ($bookings as $booking) {
        $existingStart = Carbon::parse($booking->start_time);
        $existingEnd = Carbon::parse($booking->end_time);
        $newStart = Carbon::parse($startTime);
        $newEnd = Carbon::parse($endTime);

        $overlap = $newStart < $existingEnd && $existingStart < $newEnd;

        $normalizedExisting = strtolower(trim($booking->location_detail));
$normalizedInput = strtolower(trim($locationDetail));

if ($overlap && levenshtein($normalizedExisting, $normalizedInput) < 10) {
    return false;
}
    }

    return true;
}

    public function canAcceptInDay($date)
    {
        $eventCount = Booking::whereDate('date', $date)
            ->whereIn('status', ['tertunda', 'diterima'])
            ->count();

        return $eventCount < 5;
    }

    public function greedyScheduleForDate($date)
    {
        $bookings = Booking::whereDate('date', $date)
            ->where('status', 'tertunda')
            ->orderBy('end_time')
            ->get();

        $selected = collect();
        $rejected = [];
        $lastEnd = null;

        foreach ($bookings as $booking) {
            if (!$lastEnd || $booking->start_time >= $lastEnd) {
                $selected->push($booking);
                $lastEnd = $booking->end_time;
            } else {
                $rejected[] = $booking;
            }
        }

        return [
            'recommended' => $selected,
            'rejected' => $rejected,
        ];
    }

}
