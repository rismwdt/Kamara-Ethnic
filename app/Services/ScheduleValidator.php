<?php

namespace App\Services;

use App\Models\Booking;

class ScheduleValidator
{
    /**
     * Cek ketersediaan (boolean) – dipakai mesin penjadwalan.
     */
    public function isPerformerAvailable($performer, $booking, $requirement = null): bool
    {
        // 0) Hindari membandingkan dengan booking yang sama (saat edit)
        $excludeId = isset($booking->id) ? $booking->id : null;

        // 1) Cek bentrok jadwal (overlap) – rumus KETAT (back-to-back boleh)
        $conflict = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->where(function ($q) use ($booking) {
                $q->where('start_time', '<',  $booking->end_time)
                  ->where('end_time',   '>',  $booking->start_time);
            })
            ->exists();

        if ($conflict) {
            return false;
        }

        // 2) Cek jeda perjalanan + buffer dinamis (dua arah)
        $bookingsToday = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->get(['id','start_time','end_time','latitude','longitude']);

        $latNew = $booking->latitude ?? null;
        $lonNew = $booking->longitude ?? null;

        $MIN_STATIC_BUFFER = 10; // menit, jika tidak ada koordinat

        foreach ($bookingsToday as $b) {
            $hasCoords = !is_null($latNew) && !is_null($lonNew) && !is_null($b->latitude) && !is_null($b->longitude);
            $distance  = $hasCoords
                ? $this->calculateDistanceKm($latNew, $lonNew, $b->latitude, $b->longitude)
                : 0.0;

            $travelTime = $this->estimateTravelTimeMinutes($distance, 15);
            $buffer     = $hasCoords ? $this->dynamicBufferMinutes($distance) : $MIN_STATIC_BUFFER;

            $newStart = strtotime($booking->start_time);
            $newEnd   = strtotime($booking->end_time);
            $bStart   = strtotime($b->start_time);
            $bEnd     = strtotime($b->end_time);

            // i) b berakhir sebelum new mulai → perlu jeda sebelum new.start
            if ($bEnd <= $newStart) {
                $gap = (int) round(($newStart - $bEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return false;
                }
            }

            // ii) b mulai setelah new berakhir → perlu jeda setelah new.end
            if ($newEnd <= $bStart) {
                $gap = (int) round(($bStart - $newEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return false;
                }
            }
            // Jika tidak termasuk i/ii, berarti overlap dan sudah ditangani di langkah 1.
        }

        // 3) Lolos semua cek
        return true;
    }

    /**
     * Versi detail untuk API – mengembalikan array berisi available + reason.
     */
    public function getAvailabilityDetail($performer, $booking, $requirement = null): array
    {
        // Ulangi cek agar bisa beri alasan yang spesifik.
        $excludeId = isset($booking->id) ? $booking->id : null;

        $overlap = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->where(function ($q) use ($booking) {
                $q->where('start_time', '<',  $booking->end_time)
                  ->where('end_time',   '>',  $booking->start_time);
            })
            ->exists();

        if ($overlap) {
            return [
                'available' => false,
                'reason'    => 'Performer sudah punya jadwal yang tumpang tindih pada jam tersebut.'
            ];
        }

        $bookingsToday = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->get(['id','start_time','end_time','latitude','longitude']);

        $latNew = $booking->latitude ?? null;
        $lonNew = $booking->longitude ?? null;
        $MIN_STATIC_BUFFER = 10;

        foreach ($bookingsToday as $b) {
            $hasCoords = !is_null($latNew) && !is_null($lonNew) && !is_null($b->latitude) && !is_null($b->longitude);
            $distance  = $hasCoords
                ? $this->calculateDistanceKm($latNew, $lonNew, $b->latitude, $b->longitude)
                : 0.0;

            $travelTime = $this->estimateTravelTimeMinutes($distance, 15);
            $buffer     = $hasCoords ? $this->dynamicBufferMinutes($distance) : $MIN_STATIC_BUFFER;

            $newStart = strtotime($booking->start_time);
            $newEnd   = strtotime($booking->end_time);
            $bStart   = strtotime($b->start_time);
            $bEnd     = strtotime($b->end_time);

            if ($bEnd <= $newStart) {
                $gap = (int) round(($newStart - $bEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return [
                        'available' => false,
                        'reason'    => "Jeda sebelum acara tidak cukup. Perlu " . round($travelTime) . " menit perjalanan + buffer {$buffer} menit (jarak ~" . round($distance, 2) . " km), tersedia {$gap} menit."
                    ];
                }
            }

            if ($newEnd <= $bStart) {
                $gap = (int) round(($bStart - $newEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return [
                        'available' => false,
                        'reason'    => "Jeda setelah acara tidak cukup. Perlu " . round($travelTime) . " menit perjalanan + buffer {$buffer} menit (jarak ~" . round($distance, 2) . " km), tersedia {$gap} menit."
                    ];
                }
            }
        }

        return [
            'available' => true,
            'reason'    => 'Performer tersedia untuk jadwal ini.'
        ];
    }

    private function calculateDistanceKm($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    private function estimateTravelTimeMinutes($distanceKm, $speedKmH = 25)
    {
        // Faktor koreksi jalur tidak lurus
        $correctionFactor = 1.35;
        return ($distanceKm * $correctionFactor / $speedKmH) * 60;
    }

    private function dynamicBufferMinutes($distanceKm)
    {
        if ($distanceKm <= 1) return 10;  // dekat, buffer kecil
        if ($distanceKm <= 5) return 20;  // sedang
        return 30;                        // jauh
    }

    public function debugDistanceAndTime($lat1, $lon1, $lat2, $lon2, $speed = 15)
    {
        $distance = $this->calculateDistanceKm($lat1, $lon1, $lat2, $lon2);
        $travelTime = $this->estimateTravelTimeMinutes($distance, $speed);

        return [
            'distance_km'      => round($distance, 2),
            'travel_time_min'  => round($travelTime, 1)
        ];
    }
}
