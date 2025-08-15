<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Performer;

class ScheduleOptimizer
{
    /**
     * Mengecek ketersediaan jadwal dengan multi-kriteria (Greedy)
     */
    public function checkScheduleAvailability(
        $date,
        $startTime,
        $endTime,
        $latitude,
        $longitude,
        // $priority,
        // $requiredRoles = [],
        // $performerNeeded = 0
    ) {
        // 1. Batas maksimal acara per hari
        $maxEventsPerDay = 5;
        $eventCount = Schedule::where('date', $date)
            ->whereIn('status', ['tertunda', 'diterima'])
            ->count();

        if ($eventCount >= $maxEventsPerDay) {
            return ['available' => false, 'message' => 'Kuota acara di hari tersebut sudah penuh.'];
        }

        // 2. Cek bentrok waktu
        $conflictingSchedules = Schedule::where('date', $date)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhereRaw('? BETWEEN start_time AND end_time', [$startTime])
                    ->orWhereRaw('? BETWEEN start_time AND end_time', [$endTime]);
            })
            ->get();

        // Fungsi hitung jarak Haversine (km)
        $distance = function($lat1, $lon1, $lat2, $lon2) {
            $earthRadius = 6371;
            $dLat = deg2rad($lat2 - $lat1);
            $dLon = deg2rad($lon2 - $lon1);
            $a = sin($dLat/2) ** 2 +
                cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
                sin($dLon/2) ** 2;
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            return $earthRadius * $c;
        };

        $start = Carbon::createFromFormat('H:i', $startTime);

        foreach ($conflictingSchedules as $schedule) {
            $dist = $distance($latitude, $longitude, $schedule->latitude, $schedule->longitude);
            $travelTimeMinutes = ($dist / 40) * 60 + 15; // kecepatan 40 km/jam + 15 menit buffer macet

            $existingEnd = Carbon::createFromFormat('H:i:s', $schedule->end_time);
            $diffMinutes = $start->diffInMinutes($existingEnd, false);

            if ($diffMinutes < $travelTimeMinutes) {
                return [
                    'available' => false,
                    'message' => 'Waktu tempuh dengan buffer macet tidak cukup antara lokasi acara.'
                ];
            }
        }
        // Kalau semua lolos
        return ['available' => true, 'message' => 'Jadwal tersedia.'];
    }
}
