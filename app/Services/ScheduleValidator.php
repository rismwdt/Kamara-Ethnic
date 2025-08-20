<?php

namespace App\Services;

use App\Models\Booking;
use Illuminate\Validation\ValidationException;

class ScheduleValidator
{
    /** ====== Konfigurasi sederhana (ubah sesuai kebijakan) ====== */
    private const MAX_TASKS_PER_DAY          = 2;    // batas maksimal tugas per performer per hari
    private const DEFAULT_SPEED_KMH          = 15;   // asumsi kecepatan rata-rata (km/jam)
    private const PATH_CORRECTION_FACTOR     = 1.35; // koreksi jalur tidak lurus
    private const MIN_STATIC_BUFFER_MIN      = 10;   // buffer default jika koordinat tidak tersedia
    private const MAX_FEASIBLE_DISTANCE_KM   = null; // contoh: 25.0; null = tanpa batas jarak keras

    /**
     * Wrapper: cek ketersediaan dari primitif.
     * Mengunci slot jika pivot.status ∈ {tertunda, dikonfirmasi}.
     */
    public function isAvailable(int $performerId, string $date, string $startTime, string $endTime): bool
    {
        $booking = (object)[
            'id'         => null,
            'date'       => $date,
            'start_time' => $startTime,
            'end_time'   => $endTime,
            'latitude'   => null,
            'longitude'  => null,
        ];
        $performer = (object)['id' => $performerId];

        return $this->isPerformerAvailable($performer, $booking);
    }

    /**
     * Cek ketersediaan (boolean) – dipakai mesin penjadwalan.
     * Memperhitungkan:
     *  - Batas maksimal tugas per hari (hard cap)
     *  - Overlap waktu (ketat; back-to-back diperbolehkan)
     *  - Buffer perjalanan dinamis (berdasar jarak Haversine + estimasi waktu tempuh)
     *  - (Opsional) Batas jarak keras (MAX_FEASIBLE_DISTANCE_KM)
     */
    public function isPerformerAvailable($performer, $booking, $requirement = null): bool
    {
        $excludeId = isset($booking->id) ? $booking->id : null;

        // 0) Batas maksimal tugas per hari
        if (!$this->underDailyCap($performer->id, $booking->date)) {
            return false;
        }

        // 1) Cek bentrok jadwal (overlap) dengan filter status pivot yang mengunci
        $conflict = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id)
                  ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->where(function ($q) use ($booking) {
                // overlap ketat: start < newEnd AND end > newStart
                $q->where('start_time', '<', $booking->end_time)
                  ->where('end_time',   '>', $booking->start_time);
            })
            ->exists();

        if ($conflict) {
            return false;
        }

        // 2) Cek jeda perjalanan + buffer dinamis (dua arah)
        $bookingsToday = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id)
                  ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->get(['id','start_time','end_time','latitude','longitude']);

        $latNew = $booking->latitude ?? null;
        $lonNew = $booking->longitude ?? null;

        foreach ($bookingsToday as $b) {
            $hasCoords = $this->hasCoords($latNew, $lonNew) && $this->hasCoords($b->latitude, $b->longitude);
            $distance  = $hasCoords ? $this->calculateDistanceKm($latNew, $lonNew, $b->latitude, $b->longitude) : 0.0;

            // (Opsional) batas jarak keras
            if ($hasCoords && is_numeric(self::MAX_FEASIBLE_DISTANCE_KM)) {
                if ($distance > (float) self::MAX_FEASIBLE_DISTANCE_KM) {
                    return false;
                }
            }

            $travelTime = $hasCoords
                ? $this->estimateTravelTimeMinutes($distance, self::DEFAULT_SPEED_KMH)
                : 0.0;

            $buffer = $hasCoords
                ? $this->dynamicBufferMinutes($distance)
                : self::MIN_STATIC_BUFFER_MIN;

            $newStart = strtotime($booking->start_time);
            $newEnd   = strtotime($booking->end_time);
            $bStart   = strtotime($b->start_time);
            $bEnd     = strtotime($b->end_time);

            // i) b berakhir sebelum new mulai → butuh jeda sebelum new.start
            if ($bEnd <= $newStart) {
                $gap = (int) round(($newStart - $bEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return false;
                }
            }

            // ii) b mulai setelah new berakhir → butuh jeda setelah new.end
            if ($newEnd <= $bStart) {
                $gap = (int) round(($bStart - $newEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return false;
                }
            }
            // jika bukan i/ii berarti overlap, sudah ditolak pada langkah 1
        }

        return true;
    }

    /**
     * Versi detail (untuk API/UX) – alasan kenapa tidak tersedia.
     */
    public function getAvailabilityDetail($performer, $booking, $requirement = null): array
    {
        $excludeId = isset($booking->id) ? $booking->id : null;

        if (!$this->underDailyCap($performer->id, $booking->date)) {
            return [
                'available' => false,
                'reason'    => 'Melebihi batas maksimal tugas per hari.'
            ];
        }

        $overlap = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id)
                  ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->where(function ($q) use ($booking) {
                $q->where('start_time', '<', $booking->end_time)
                  ->where('end_time',   '>', $booking->start_time);
            })
            ->exists();

        if ($overlap) {
            return [
                'available' => false,
                'reason'    => 'Performer memiliki jadwal yang tumpang tindih pada jam tersebut.'
            ];
        }

        $bookingsToday = Booking::whereHas('performers', function ($q) use ($performer) {
                $q->whereKey($performer->id)
                  ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
            })
            ->when($excludeId, fn($q) => $q->where('id', '!=', $excludeId))
            ->whereDate('date', $booking->date)
            ->get(['id','start_time','end_time','latitude','longitude']);

        $latNew = $booking->latitude ?? null;
        $lonNew = $booking->longitude ?? null;

        foreach ($bookingsToday as $b) {
            $hasCoords = $this->hasCoords($latNew, $lonNew) && $this->hasCoords($b->latitude, $b->longitude);
            $distance  = $hasCoords ? $this->calculateDistanceKm($latNew, $lonNew, $b->latitude, $b->longitude) : 0.0;

            if ($hasCoords && is_numeric(self::MAX_FEASIBLE_DISTANCE_KM) && $distance > (float) self::MAX_FEASIBLE_DISTANCE_KM) {
                return [
                    'available' => false,
                    'reason'    => 'Jarak antar lokasi melebihi batas yang diizinkan (~'.round($distance, 2).' km).'
                ];
            }

            $travelTime = $hasCoords
                ? $this->estimateTravelTimeMinutes($distance, self::DEFAULT_SPEED_KMH)
                : 0.0;

            $buffer     = $hasCoords ? $this->dynamicBufferMinutes($distance) : self::MIN_STATIC_BUFFER_MIN;

            $newStart = strtotime($booking->start_time);
            $newEnd   = strtotime($booking->end_time);
            $bStart   = strtotime($b->start_time);
            $bEnd     = strtotime($b->end_time);

            if ($bEnd <= $newStart) {
                $gap = (int) round(($newStart - $bEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return [
                        'available' => false,
                        'reason'    => "Jeda sebelum acara tidak cukup. Perlu " . round($travelTime) .
                                       " menit perjalanan + buffer {$buffer} menit (jarak ~" . round($distance, 2) .
                                       " km), tersedia {$gap} menit."
                    ];
                }
            }

            if ($newEnd <= $bStart) {
                $gap = (int) round(($bStart - $newEnd) / 60);
                if ($gap < ($travelTime + $buffer)) {
                    return [
                        'available' => false,
                        'reason'    => "Jeda setelah acara tidak cukup. Perlu " . round($travelTime) .
                                       " menit perjalanan + buffer {$buffer} menit (jarak ~" . round($distance, 2) .
                                       " km), tersedia {$gap} menit."
                    ];
                }
            }
        }

        return [
            'available' => true,
            'reason'    => 'Performer tersedia untuk jadwal ini.'
        ];
    }

    /** ======================= util jarak & waktu ======================= */

    private function hasCoords($lat, $lon): bool
    {
        return !is_null($lat) && !is_null($lon);
    }

    private function calculateDistanceKm($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371.0;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return (float) ($earthRadius * $c);
    }

    private function estimateTravelTimeMinutes(float $distanceKm, float $speedKmH = self::DEFAULT_SPEED_KMH): float
    {
        $speedKmH = max(1.0, $speedKmH);
        return (float) (($distanceKm * self::PATH_CORRECTION_FACTOR / $speedKmH) * 60.0);
    }

    private function dynamicBufferMinutes(float $distanceKm): int
    {
        if ($distanceKm <= 1.0) return 10;  // dekat
        if ($distanceKm <= 5.0) return 20;  // sedang
        return 30;                           // jauh
    }

    /** ======================= util kebijakan harian ======================= */

    private function underDailyCap(int $performerId, string $date): bool
    {
        $count = Booking::whereHas('performers', function ($q) use ($performerId) {
                $q->whereKey($performerId)
                  ->whereIn('booking_performers.confirmation_status', ['tertunda','dikonfirmasi']);
            })
            ->whereDate('date', $date)
            ->count();

        return $count < self::MAX_TASKS_PER_DAY;
    }

    /** ======================= validasi waktu ======================= */

    public function ensureValidWindow(string $date, string $start, string $end): void
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw ValidationException::withMessages(['date' => 'Format tanggal harus YYYY-MM-DD']);
        }
        if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $start) || !preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $end)) {
            throw ValidationException::withMessages(['time' => 'Format jam harus HH:MM atau HH:MM:SS']);
        }
        if (strtotime("$date $start") >= strtotime("$date $end")) {
            throw ValidationException::withMessages(['time' => 'Jam mulai harus lebih kecil dari jam selesai']);
        }
    }

    // Debug helper (opsional)
    public function debugDistanceAndTime($lat1, $lon1, $lat2, $lon2, $speed = self::DEFAULT_SPEED_KMH): array
    {
        $distance = $this->calculateDistanceKm($lat1, $lon1, $lat2, $lon2);
        $travelTime = $this->estimateTravelTimeMinutes($distance, $speed);

        return [
            'distance_km'      => round($distance, 2),
            'travel_time_min'  => round($travelTime, 1)
        ];
    }
}
